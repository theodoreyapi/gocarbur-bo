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
use Illuminate\Support\Facades\Hash;

// ═══════════════════════════════════════════════════════════════
// ProAuthController
// ═══════════════════════════════════════════════════════════════

class ProAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'entity_type' => 'required|in:station,garage',
        ]);

        $model = $data['entity_type'] === 'station' ? StationOwner::class : GarageOwner::class;
        $owner = $model::where('email', $data['email'])->first();

        if (!$owner || !Hash::check($data['password'], $owner->password)) {
            return response()->json(['success' => false, 'message' => 'Email ou mot de passe incorrect.'], 401);
        }

        if (!$owner->is_active) {
            return response()->json(['success' => false, 'message' => 'Compte suspendu. Contactez le support.'], 403);
        }

        if ($owner->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Compte en attente de validation.'], 403);
        }

        $owner->update(['last_login_at' => now()]);
        $owner->tokens()->where('name', 'pro-mobile')->delete();
        $token = $owner->createToken('pro-mobile')->plainTextToken;

        return response()->json([
            'success'     => true,
            'token'       => $token,
            'token_type'  => 'Bearer',
            'entity_type' => $data['entity_type'],
            'owner'       => $owner->only(['id','name','email','company_name','status']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnexion réussie.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => 'required|email', 'entity_type' => 'required|in:station,garage']);
        // Implémenter envoi email de réinitialisation
        return response()->json(['success' => true, 'message' => 'Email de réinitialisation envoyé si le compte existe.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'entity_type'           => 'required|in:station,garage',
        ]);
        // Implémenter reset avec token
        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.']);
    }

    public function profile(Request $request): JsonResponse
    {
        $owner = $request->user()->load(['stations' => fn($q) => $q->withCount(['reviews','views'])]);
        return response()->json(['success' => true, 'data' => $owner]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $owner = $request->user();
        $data  = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'phone'        => 'sometimes|string|max:20',
            'company_name' => 'sometimes|string|max:150',
        ]);
        $owner->update($data);
        return response()->json(['success' => true, 'message' => 'Profil mis à jour.', 'data' => $owner->fresh()]);
    }

    public function updateLogo(Request $request): JsonResponse
    {
        $request->validate(['logo' => 'required|image|max:2048']);
        $path = $request->file('logo')->store('pro-logos', 'public');
        // Mettre à jour logo sur les stations/garages de cet owner
        return response()->json(['success' => true, 'logo_url' => \Storage::url($path)]);
    }
}

// ═══════════════════════════════════════════════════════════════
// ProPromotionController
// ═══════════════════════════════════════════════════════════════

class ProPromotionController extends Controller
{
    private function storePromotion(Request $request, string $type, int $entityId, string $modelClass): JsonResponse
    {
        $data = $request->validate([
            'title'                  => 'required|string|max:150',
            'description'            => 'nullable|string|max:1000',
            'type'                   => 'required|in:discount,offre_speciale,service_gratuit,cadeau,autre',
            'discount_percent'       => 'nullable|numeric|min:1|max:100',
            'discount_amount'        => 'nullable|numeric|min:1',
            'starts_at'              => 'required|date|after_or_equal:today',
            'ends_at'                => 'required|date|after:starts_at',
            'send_push_notification' => 'boolean',
            'notification_radius_km' => 'nullable|integer|min:1|max:50',
        ]);

        $data['promotable_type'] = $modelClass;
        $data['promotable_id']   = $entityId;
        $data['is_active']       = true;

        $promotion = Promotion::create($data);

        // Envoyer notification push si demandé (plan Premium)
        if ($data['send_push_notification'] ?? false) {
            $this->sendPromoNotification($promotion);
        }

        return response()->json(['success' => true, 'message' => 'Promotion créée.', 'data' => $promotion], 201);
    }

    private function sendPromoNotification(Promotion $promotion): void
    {
        $entity   = $promotion->promotable;
        $firebase = app(FirebaseService::class);

        $tokens = \App\Models\User::where('is_active', true)
            ->whereNotNull('fcm_token')
            ->where('city', 'like', "%{$entity->city}%")
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($tokens)) {
            $firebase->sendMulticast($tokens, [
                'title' => "Promotion : {$promotion->title}",
                'body'  => "{$entity->name} — {$promotion->description}",
            ], [
                'type'         => 'promotion',
                'promotion_id' => (string) $promotion->id,
            ]);
        }
    }

    // ── Station ────────────────────────────────────────────────

    public function indexStation(Request $request, int $stationId): JsonResponse
    {
        $station    = $request->user()->stations()->findOrFail($stationId);
        $promotions = $station->promotions()->orderByDesc('starts_at')->paginate(10);
        return response()->json(['success' => true, 'data' => $promotions]);
    }

    public function storeStation(Request $request, int $stationId): JsonResponse
    {
        $station = $request->user()->stations()->findOrFail($stationId);
        return $this->storePromotion($request, 'station', $station->id, \App\Models\Station::class);
    }

    public function updateStation(Request $request, int $stationId, int $id): JsonResponse
    {
        $station   = $request->user()->stations()->findOrFail($stationId);
        $promotion = Promotion::where('promotable_type', \App\Models\Station::class)
            ->where('promotable_id', $station->id)->findOrFail($id);

        $data = $request->validate([
            'title'       => 'sometimes|string|max:150',
            'description' => 'nullable|string|max:1000',
            'starts_at'   => 'sometimes|date',
            'ends_at'     => 'sometimes|date|after:starts_at',
        ]);

        $promotion->update($data);
        return response()->json(['success' => true, 'message' => 'Promotion mise à jour.', 'data' => $promotion->fresh()]);
    }

    public function toggle(Request $request, int $entityId, int $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => !$promotion->is_active]);
        $state = $promotion->is_active ? 'activée' : 'désactivée';
        return response()->json(['success' => true, 'message' => "Promotion {$state}."]);
    }

    public function destroyStation(Request $request, int $stationId, int $id): JsonResponse
    {
        $station = $request->user()->stations()->findOrFail($stationId);
        Promotion::where('promotable_type', \App\Models\Station::class)
            ->where('promotable_id', $station->id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Promotion supprimée.']);
    }

    // ── Garage ─────────────────────────────────────────────────

    public function indexGarage(Request $request, int $garageId): JsonResponse
    {
        $garage     = $request->user()->garages()->findOrFail($garageId);
        $promotions = $garage->promotions()->orderByDesc('starts_at')->paginate(10);
        return response()->json(['success' => true, 'data' => $promotions]);
    }

    public function storeGarage(Request $request, int $garageId): JsonResponse
    {
        $garage = $request->user()->garages()->findOrFail($garageId);
        return $this->storePromotion($request, 'garage', $garage->id, \App\Models\Garage::class);
    }

    public function updateGarage(Request $request, int $garageId, int $id): JsonResponse
    {
        $garage    = $request->user()->garages()->findOrFail($garageId);
        $promotion = Promotion::where('promotable_type', \App\Models\Garage::class)
            ->where('promotable_id', $garage->id)->findOrFail($id);
        $promotion->update($request->only(['title','description','starts_at','ends_at']));
        return response()->json(['success' => true, 'message' => 'Promotion mise à jour.', 'data' => $promotion->fresh()]);
    }

    public function destroyGarage(Request $request, int $garageId, int $id): JsonResponse
    {
        $garage = $request->user()->garages()->findOrFail($garageId);
        Promotion::where('promotable_type', \App\Models\Garage::class)
            ->where('promotable_id', $garage->id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Promotion supprimée.']);
    }
}
