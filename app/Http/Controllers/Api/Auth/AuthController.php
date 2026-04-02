<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    /**
     * POST /auth/request-otp
     * Envoie un code OTP au numéro de téléphone fourni
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:20',
        ]);

        $phone = preg_replace('/\s+/', '', $request->phone);

        // Vérifier le rate limit : max 3 OTP par heure par numéro
        $recentCount = OtpCode::where('phone', $phone)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Trop de demandes. Réessayez dans 1 heure.',
            ], 429);
        }

        // Invalider les anciens OTP
        OtpCode::where('phone', $phone)->where('is_used', false)->delete();

        // Générer et envoyer
        $code = $this->otpService->generate($phone);

        return response()->json([
            'success' => true,
            'message' => 'Code OTP envoyé par SMS.',
            'expires_in' => 300, // 5 minutes
        ]);
    }

    /**
     * POST /auth/verify-otp
     * Vérifie le code OTP et retourne un token Sanctum
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string|size:6',
        ]);

        $phone = preg_replace('/\s+/', '', $request->phone);

        $otp = OtpCode::where('phone', $phone)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.',
            ], 422);
        }

        // Marquer l'OTP comme utilisé
        $otp->update(['is_used' => true]);

        // Créer ou retrouver l'utilisateur
        $user = DB::transaction(function () use ($phone) {
            return User::firstOrCreate(
                ['phone' => $phone],
                ['name' => 'Utilisateur', 'is_active' => true]
            );
        });

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été suspendu. Contactez le support.',
            ], 403);
        }

        // Mettre à jour last_login_at
        $user->update(['last_login_at' => now()]);

        // Révoquer les anciens tokens de ce device si un nom est fourni
        $deviceName = $request->input('device_name', 'mobile');
        $user->tokens()->where('name', $deviceName)->delete();

        // Créer le token Sanctum
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success'    => true,
            'message'    => 'Connexion réussie.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'is_new_user'=> $user->wasRecentlyCreated,
            'user'       => [
                'id'                => $user->id,
                'name'              => $user->name,
                'phone'             => $user->phone,
                'city'              => $user->city,
                'avatar_url'        => $user->avatar_url,
                'subscription_type' => $user->subscription_type,
                'is_premium'        => $user->isPremium(),
            ],
        ]);
    }

    /**
     * POST /auth/refresh-token
     * Rotation du token courant
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user        = $request->user();
        $currentToken= $user->currentAccessToken();
        $deviceName  = $currentToken->name;

        // Révoquer l'ancien
        $currentToken->delete();

        // Émettre un nouveau
        $newToken = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success'    => true,
            'token'      => $newToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * POST /auth/logout
     * Révoke le token courant
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ]);
    }

    /**
     * POST /auth/logout-all
     * Révoke tous les tokens (tous les appareils)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté de tous les appareils.',
        ]);
    }
}
