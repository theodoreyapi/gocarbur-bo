<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\StationOwner;
use App\Models\GarageOwner;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// ═══════════════════════════════════════════════════════════════
// ProAuthController
// ═══════════════════════════════════════════════════════════════

class ProAuthController extends Controller
{
    // ─────────────────────────────────────────────
    // Déterminer la table et le guard selon l'owner connecté
    // ─────────────────────────────────────────────
    private function resolveOwner(Request $request): array
    {
        $user = $request->user('station_owner')
             ?? $request->user('garage_owner');

        if (!$user) abort(401);

        $isStation = $request->user('station_owner') !== null;

        return [
            'user'      => $user,
            'table'     => $isStation ? 'station_owners'  : 'garage_owners',
            'pk'        => $isStation ? 'id_station_owner' : 'id_gara_owner',
            'guard'     => $isStation ? 'station_owner'    : 'garage_owner',
            'type'      => $isStation ? 'station'          : 'garage',
            'link_table'=> $isStation ? 'station_owner_station' : 'garage_owner_garage',
            'link_fk'   => $isStation ? 'station_owner_id' : 'garage_owner_id',
            'entity_fk' => $isStation ? 'station_id'       : 'garage_id',
        ];
    }

    // ─────────────────────────────────────────────
    // LOGIN
    // POST /pro/auth/login
    // Body: email, password, type (station|garage)
    // ─────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'type'     => 'required|in:station,garage',
        ]);

        $table = $request->type === 'station' ? 'station_owners' : 'garage_owners';
        $pk    = $request->type === 'station' ? 'id_station_owner' : 'id_gara_owner';

        $owner = DB::table($table)
            ->where('email', $request->email)
            ->whereNull('deleted_at')
            ->first();

        if (!$owner || !Hash::check($request->password, $owner->password)) {
            return response()->json(['success' => false, 'message' => 'Identifiants incorrects.'], 401);
        }

        if ($owner->status !== 'approved') {
            $msgs = [
                'pending'   => 'Votre compte est en attente de validation.',
                'suspended' => 'Votre compte a été suspendu.',
                'rejected'  => 'Votre demande a été rejetée.',
            ];
            return response()->json(['success' => false, 'message' => $msgs[$owner->status] ?? 'Accès refusé.'], 403);
        }

        if (!$owner->is_active) {
            return response()->json(['success' => false, 'message' => 'Compte désactivé.'], 403);
        }

        DB::table($table)->where($pk, $owner->$pk)->update(['last_login_at' => now()]);

        // Token Sanctum avec guard spécifique
        // Note : nécessite que StationOwner/GarageOwner utilisent HasApiTokens
        $guardModel = $request->type === 'station'
            ? StationOwner::find($owner->$pk)
            : GarageOwner::find($owner->$pk);

        $token = $guardModel->createToken('pro_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data'    => [
                'owner' => $this->formatOwner($owner, $request->type),
                'token' => $token,
                'type'  => $request->type,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // FORGOT PASSWORD
    // POST /pro/auth/forgot-password
    // ─────────────────────────────────────────────
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'type'  => 'required|in:station,garage',
        ]);

        $table = $request->type === 'station' ? 'station_owners' : 'garage_owners';

        $owner = DB::table($table)->where('email', $request->email)->whereNull('deleted_at')->first();

        if (!$owner) {
            // Sécurité : même réponse si inexistant
            return response()->json(['success' => true, 'message' => 'Si ce compte existe, un email a été envoyé.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // TODO : Mail::to($owner->email)->send(new ProPasswordResetMail($token, $request->type))
        \Illuminate\Support\Facades\Log::info("Reset token pro [{$token}] pour {$request->email}");

        return response()->json(['success' => true, 'message' => 'Si ce compte existe, un email a été envoyé.']);
    }

    // ─────────────────────────────────────────────
    // RESET PASSWORD
    // POST /pro/auth/reset-password
    // ─────────────────────────────────────────────
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
            'type'                  => 'required|in:station,garage',
        ]);

        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['success' => false, 'message' => 'Token invalide ou expiré.'], 422);
        }

        // Expiration 60 minutes
        if (now()->diffInMinutes($reset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['success' => false, 'message' => 'Token expiré.'], 422);
        }

        $table = $request->type === 'station' ? 'station_owners' : 'garage_owners';
        $pk    = $request->type === 'station' ? 'id_station_owner' : 'id_gara_owner';

        DB::table($table)
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password), 'updated_at' => now()]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.']);
    }

    // ─────────────────────────────────────────────
    // LOGOUT
    // POST /pro/auth/logout
    // ─────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Déconnecté.']);
    }

    // ─────────────────────────────────────────────
    // PROFILE
    // GET /pro/subscription/profile
    // ─────────────────────────────────────────────
    public function profile(Request $request): JsonResponse
    {
        $ctx   = $this->resolveOwner($request);
        $owner = DB::table($ctx['table'])->where($ctx['pk'], $ctx['user']->{$ctx['pk']})->first();

        // Entités liées (stations ou garages)
        $entityIds = DB::table($ctx['link_table'])
            ->where($ctx['link_fk'], $ctx['user']->{$ctx['pk']})
            ->pluck($ctx['entity_fk']);

        $entityTable = $ctx['type'] . 's';
        $entityPk    = 'id_' . $ctx['type'];

        $entities = DB::table($entityTable)
            ->whereIn($entityPk, $entityIds)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get(['id_' . $ctx['type'], 'name', 'city', 'subscription_type', 'is_verified']);

        // Abonnement actif
        $subscription = DB::table('subscriptions')
            ->where('subscribable_type', $ctx['type'] === 'station' ? 'App\Models\StationOwner' : 'App\Models\GarageOwner')
            ->where('subscribable_id', $ctx['user']->{$ctx['pk']})
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => array_merge($this->formatOwner($owner, $ctx['type']), [
                'entities'     => $entities,
                'subscription' => $subscription,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE PROFILE
    // PUT /pro/subscription/profile
    // ─────────────────────────────────────────────
    public function updateProfile(Request $request): JsonResponse
    {
        $ctx = $this->resolveOwner($request);

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'phone'        => 'sometimes|nullable|string|max:20',
            'company_name' => 'sometimes|nullable|string|max:150',
            'rccm'         => 'sometimes|nullable|string|max:50',
            'password'     => 'sometimes|string|min:6|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        DB::table($ctx['table'])
            ->where($ctx['pk'], $ctx['user']->{$ctx['pk']})
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table($ctx['table'])->where($ctx['pk'], $ctx['user']->{$ctx['pk']})->first();

        return response()->json(['success' => true, 'message' => 'Profil mis à jour.', 'data' => $this->formatOwner($updated, $ctx['type'])]);
    }

    // ─────────────────────────────────────────────
    // UPDATE LOGO
    // POST /pro/subscription/profile/logo
    // ─────────────────────────────────────────────
    public function updateLogo(Request $request): JsonResponse
    {
        $request->validate(['logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048']);

        $ctx = $this->resolveOwner($request);

        // Supprimer l'ancien logo de l'entité principale
        $entityTable  = $ctx['type'] . 's';
        $entityPk     = 'id_' . $ctx['type'];
        $entityIds    = DB::table($ctx['link_table'])
            ->where($ctx['link_fk'], $ctx['user']->{$ctx['pk']})
            ->pluck($ctx['entity_fk']);

        $path     = $request->file('logo')->store("logos/{$ctx['type']}s", 'public');
        $logoUrl  = '/storage/' . $path;

        // Appliquer le logo à toutes les entités de ce pro
        DB::table($entityTable)
            ->whereIn($entityPk, $entityIds)
            ->update(['logo_url' => $logoUrl, 'updated_at' => now()]);

        return response()->json(['success' => true, 'data' => ['logo_url' => $logoUrl]]);
    }

    // ─────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────
    private function formatOwner(object $owner, string $type): array
    {
        $pk = $type === 'station' ? 'id_station_owner' : 'id_gara_owner';
        return [
            'id'           => $owner->$pk,
            'name'         => $owner->name,
            'email'        => $owner->email,
            'phone'        => $owner->phone,
            'company_name' => $owner->company_name,
            'rccm'         => $owner->rccm,
            'status'       => $owner->status,
            'is_active'    => (bool) $owner->is_active,
            'last_login_at'=> $owner->last_login_at,
            'type'         => $type,
        ];
    }
}
