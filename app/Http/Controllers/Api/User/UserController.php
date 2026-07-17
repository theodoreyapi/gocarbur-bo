<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // ─────────────────────────────────────────────
    // PROFILE — Profil complet
    // GET /connecte/user/profile
    // ─────────────────────────────────────────────
    public function profile($id): JsonResponse
    {
        $user = DB::table('users_carbur')
            ->where('id_user_carbu', $id)
            ->whereNull('deleted_at')
            ->first([
                'id_user_carbu',
                'name',
                'email',
                'phone',
                'city',
                'avatar_url',
                'subscription_type',
                'subscription_expires_at',
                'is_active',
                'last_login_at',
                'created_at',
            ]);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        // Statistiques rapides
        $vehicleCount  = DB::table('vehicles')->where('user_id', $id)->whereNull('deleted_at')->count();
        $reminderCount = DB::table('reminders')->where('user_id', $id)->where('is_dismissed', false)->count();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $user, [
                'vehicle_count'  => $vehicleCount,
                'reminder_count' => $reminderCount,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier le profil
    // PUT /connecte/user/profile
    // ─────────────────────────────────────────────
    public function update(Request $request): JsonResponse
    {
        $userId = $request->idUser;

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:100',
            'city'  => 'sometimes|nullable|string|max:100',
            'email' => 'sometimes|nullable|email|unique:users_carbur,email,' . $userId . ',id_user_carbu',
        ]);

        DB::table('users_carbur')
            ->where('id_user_carbu', $userId)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $user = DB::table('users_carbur')
            ->where('id_user_carbu', $userId)
            ->first(['id_user_carbu', 'name', 'email', 'phone', 'city', 'avatar_url', 'subscription_type']);

        return response()->json(['success' => true, 'message' => 'Profil mis à jour.', 'data' => $user]);
    }

    // ─────────────────────────────────────────────
    // UPDATE AVATAR
    // POST /connecte/user/profile/avatar
    // ─────────────────────────────────────────────
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $userId = $request->idUser;

        // Supprimer l'ancien avatar si existant
        $current = DB::table('users_carbur')->where('id_user_carbu', $userId)->value('avatar_url');
        if ($current) {

            $oldPath = public_path(parse_url($current, PHP_URL_PATH));

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $timestamp = Carbon::now()->format('Ymd_His');

        $file = $request->file('avatar');
        $name = 'goutilisateur_' . $timestamp . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assurances'), $name);

        $avatarUrl = url('gocarbu/public/goutilisateur/' . $name);

        DB::table('users_carbur')
            ->where('id_user_carbu', $userId)
            ->update(['avatar_url' => $avatarUrl, 'updated_at' => now()]);

        return response()->json(['success' => true, 'data' => ['avatar_url' => $avatarUrl]]);
    }

    // ─────────────────────────────────────────────
    // FCM TOKEN — Enregistrer le token Firebase
    // POST /connecte/user/fcm-token
    // ─────────────────────────────────────────────
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string|max:255']);

        DB::table('users_carbur')
            ->where('id_user_carbu', $request->idUser)
            ->update(['fcm_token' => $request->fcm_token, 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Token FCM enregistré.']);
    }

    // ─────────────────────────────────────────────
    // DELETE ACCOUNT — Supprimer son compte
    // DELETE /connecte/user/account
    // ─────────────────────────────────────────────
    public function deleteAccount($id): JsonResponse
    {
        $userId = $id;

        // Révoquer tous les tokens Sanctum
        // DB::table('personal_access_tokens')
        //     ->where('tokenable_type', 'App\Models\UserCarbur')
        //     ->where('tokenable_id', $userId)
        //     ->delete();

        // Soft delete du compte
        DB::table('users_carbur')
            ->where('id_user_carbu', $userId)
            ->update(['deleted_at' => now(), 'is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Compte supprimé.']);
    }

    // ─────────────────────────────────────────────
    // SUBSCRIPTION — Abonnement actuel
    // GET /connecte/user/subscription
    // ─────────────────────────────────────────────
    public function subscription($id): JsonResponse
    {
        $userId = $id;

        $subscription = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\UserCarbur')
            ->where('subscribable_id', $userId)
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $subscription,
        ]);
    }
}
