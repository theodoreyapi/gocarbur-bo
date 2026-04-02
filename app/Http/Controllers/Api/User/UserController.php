<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * GET /user/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'vehicles:id,user_id,brand,model,year,plate_number,is_primary',
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                       => $user->id,
                'name'                     => $user->name,
                'phone'                    => $user->phone,
                'city'                     => $user->city,
                'avatar_url'               => $user->avatar_url,
                'subscription_type'        => $user->subscription_type,
                'subscription_expires_at'  => $user->subscription_expires_at,
                'is_premium'               => $user->isPremium(),
                'vehicles_count'           => $user->vehicles->count(),
                'primary_vehicle'          => $user->vehicles->where('is_primary', true)->first(),
                'created_at'               => $user->created_at,
            ],
        ]);
    }

    /**
     * PUT /user/profile
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
        ]);

        $request->user()->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour.',
            'data'    => $request->user()->fresh(),
        ]);
    }

    /**
     * POST /user/profile/avatar
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        // Supprimer l'ancien avatar
        if ($user->avatar_url) {
            $oldPath = str_replace(Storage::url(''), '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('avatar')->store("avatars/{$user->id}", 'public');
        $user->update(['avatar_url' => Storage::url($path)]);

        return response()->json([
            'success'    => true,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    /**
     * POST /user/fcm-token
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /user/account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Révoquer tous les tokens
        $user->tokens()->delete();

        // Soft delete
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre compte a été supprimé.',
        ]);
    }

    /**
     * GET /user/subscription
     */
    public function subscription(Request $request): JsonResponse
    {
        $user         = $request->user();
        $subscription = $user->activeSubscription();

        return response()->json([
            'success' => true,
            'data'    => [
                'type'       => $user->subscription_type,
                'is_premium' => $user->isPremium(),
                'expires_at' => $user->subscription_expires_at,
                'active_sub' => $subscription,
            ],
        ]);
    }
}
