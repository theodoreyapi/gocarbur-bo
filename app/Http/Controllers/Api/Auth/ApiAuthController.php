<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserCarbur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    // ─────────────────────────────────────────────
    // REGISTER
    // ─────────────────────────────────────────────

    /**
     * Créer un nouveau compte utilisateur.
     *
     * POST /api/auth/register
     *
     * Body JSON :
     *  - name        (required)
     *  - phone       (required, unique)
     *  - password    (required, min:6, confirmed)
     *  - email       (optional)
     *  - city        (optional)
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'phone'                 => 'required|string|max:20|unique:users_carbur,phone',
            'email'                 => 'nullable|email|unique:users_carbur,email',
            'password'              => 'required|string|min:6|confirmed',
            'city'                  => 'nullable|string|max:100',
        ]);

        $user = UserCarbur::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'city'     => $validated['city'] ?? null,
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès.',
            'data'    => [
                'user'  => $this->formatUser($user),
                'token' => $token,
            ],
        ], 201);
    }

    // ─────────────────────────────────────────────
    // LOGIN (email/phone + password)
    // ─────────────────────────────────────────────

    /**
     * Connexion classique par email ou téléphone + mot de passe.
     *
     * POST /api/auth/login
     *
     * Body JSON :
     *  - login    (required) → email ou numéro de téléphone
     *  - password (required)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');

        // Chercher par email ou par téléphone
        $user = UserCarbur::where('email', $login)
            ->orWhere('phone', $login)
            ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte est désactivé.',
            ], 403);
        }

        // Révoquer les anciens tokens si vous préférez la politique « un seul token »
        // $user->tokens()->delete();

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data'    => [
                'user'  => $this->formatUser($user),
                'token' => $token,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // REQUEST OTP
    // ─────────────────────────────────────────────

    /**
     * Envoyer un code OTP par email ou WhatsApp.
     *
     * POST /api/auth/request-otp
     *
     * Body JSON :
     *  - channel  (required) → "email" | "whatsapp"
     *  - email    (required si channel = email)
     *  - phone    (required si channel = whatsapp)
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|in:email,whatsapp',
            'email'   => 'required_if:channel,email|nullable|email',
            'phone'   => 'required_if:channel,whatsapp|nullable|string|max:20',
        ]);

        $channel = $request->input('channel');
        $otp     = $this->generateOtp();

        if ($channel === 'email') {
            $identifier = $request->input('email');

            // Stocker l'OTP en cache pendant 10 minutes (clé unique par email)
            Cache::put("otp:email:{$identifier}", $otp, now()->addMinutes(10));

            // TODO : envoyer l'email avec $otp (Mail::to($identifier)->send(...))
            Log::info("OTP email [{$otp}] envoyé à {$identifier}");

        } else {
            $identifier = $request->input('phone');

            // Stocker l'OTP en cache pendant 10 minutes (clé unique par téléphone)
            Cache::put("otp:phone:{$identifier}", $otp, now()->addMinutes(10));

            // TODO : envoyer le WhatsApp avec $otp (via Twilio, Meta API, etc.)
            Log::info("OTP whatsapp [{$otp}] envoyé à {$identifier}");
        }

        return response()->json([
            'success' => true,
            'message' => "Code OTP envoyé via {$channel}.",
            // NE PAS retourner $otp en production !
        ]);
    }

    // ─────────────────────────────────────────────
    // VERIFY OTP
    // ─────────────────────────────────────────────

    /**
     * Vérifier le code OTP et retourner un token Sanctum.
     *
     * POST /api/auth/verify-otp
     *
     * Body JSON :
     *  - channel     (required) → "email" | "whatsapp"
     *  - email       (required si channel = email)
     *  - phone       (required si channel = whatsapp)
     *  - otp         (required)
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|in:email,whatsapp',
            'email'   => 'required_if:channel,email|nullable|email',
            'phone'   => 'required_if:channel,whatsapp|nullable|string|max:20',
            'otp'     => 'required|string|size:6',
        ]);

        $channel = $request->input('channel');
        $otp     = $request->input('otp');

        if ($channel === 'email') {
            $identifier = $request->input('email');
            $cacheKey   = "otp:email:{$identifier}";
            $user       = UserCarbur::where('email', $identifier)->first();
        } else {
            $identifier = $request->input('phone');
            $cacheKey   = "otp:phone:{$identifier}";
            $user       = UserCarbur::where('phone', $identifier)->first();
        }

        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.',
            ], 422);
        }

        // OTP valide → le supprimer du cache
        Cache::forget($cacheKey);

        // Si l'utilisateur n'existe pas encore, le créer (inscription via OTP)
        if (!$user) {
            $user = UserCarbur::create([
                'name'  => $channel === 'email' ? explode('@', $identifier)[0] : $identifier,
                'email' => $channel === 'email' ? $identifier : null,
                'phone' => $channel === 'whatsapp' ? $identifier : 'N/A',
                'password' => Hash::make(str()->random(32)),
                'is_active' => true,
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte est désactivé.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP vérifié. Connexion réussie.',
            'data'    => [
                'user'  => $this->formatUser($user),
                'token' => $token,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // REFRESH TOKEN
    // ─────────────────────────────────────────────

    /**
     * Rotation du token : révoque le token actuel et en émet un nouveau.
     *
     * POST /api/auth/refresh-token
     * Header : Authorization: Bearer {token}
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();

        // Révoquer le token courant
        $request->user()->currentAccessToken()->delete();

        // Émettre un nouveau token
        $newToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renouvelé.',
            'data'    => [
                'token' => $newToken,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // LOGOUT (appareil courant)
    // ─────────────────────────────────────────────

    /**
     * Déconnexion : révoque uniquement le token courant.
     *
     * POST /api/auth/logout
     * Header : Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté avec succès.',
        ]);
    }

    // ─────────────────────────────────────────────
    // LOGOUT ALL (tous les appareils)
    // ─────────────────────────────────────────────

    /**
     * Déconnexion de tous les appareils : révoque tous les tokens de l'utilisateur.
     *
     * POST /api/auth/logout-all
     * Header : Authorization: Bearer {token}
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté de tous les appareils.',
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────────

    /**
     * Générer un OTP numérique à 6 chiffres.
     */
    private function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Formater les données utilisateur renvoyées dans les réponses.
     */
    private function formatUser(UserCarbur $user): array
    {
        return [
            'id'                     => $user->id_user_carbu,
            'name'                   => $user->name,
            'email'                  => $user->email,
            'phone'                  => $user->phone,
            'city'                   => $user->city,
            'avatar_url'             => $user->avatar_url,
            'subscription_type'      => $user->subscription_type,
            'subscription_expires_at'=> $user->subscription_expires_at,
            'is_active'              => $user->is_active,
            'last_login_at'          => $user->last_login_at,
        ];
    }
}
