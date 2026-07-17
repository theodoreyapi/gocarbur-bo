<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionUserController extends Controller
{
    // GET /connecte/subscription/plans
    public function plans(): JsonResponse
    {
        // Les plans sont définis en config ou en BDD (app_settings)
        $plans = [
            [
                'plan'          => 'user_free',
                'label'         => 'Gratuit',
                'price'         => 0,
                'currency'      => 'XOF',
                'billing_cycle' => null,
                'features'      => ['Carte des stations', 'Recherche de garages', '1 véhicule'],
            ],
            [
                'plan'          => 'user_premium',
                'label'         => 'Premium',
                'price'         => 2000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout gratuit +', 'Véhicules illimités', 'Statistiques carburant', 'Rappels avancés', 'Sans publicités'],
            ],
        ];

        return response()->json(['success' => true, 'data' => $plans]);
    }

    // POST /connecte/subscription/initiate
    public function initiate(Request $request): JsonResponse
    {
        $userId = $request->idUser;

        $validated = $request->validate([
            'plan'           => 'required|in:user_premium',
            'billing_cycle'  => 'required|in:mensuel,trimestriel,annuel',
            'payment_method' => 'required|in:orange_money,mtn_money,moov_money,wave',
            'starts' => 'required',
            'expires' => 'required',
        ]);

        $amounts = ['mensuel' => 2000, 'trimestriel' => 5000, 'annuel' => 20000];
        $amount  = $amounts[$validated['billing_cycle']];

        $reference = 'SUB-' . strtoupper(uniqid());

        try {

            // Créer un abonnement en attente
            $subscription = DB::table('subscriptions')->create([
                'subscribable_type'     => 'App\Models\UserCarbur',
                'subscribable_id'       => $userId,
                'amount'                => $amount,
                'currency'              => 'XOF',
                'payment_method'        => $validated['payment_method'],
                'plan'                  => $validated['plan'],
                'billing_cycle'         => $validated['billing_cycle'],
                'starts_at'             => $validated['starts'],
                'expires_at'            => $validated['expires'],
                'status'                => 'pending',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // Créer la transaction en attente
            $transaction = DB::table('payment_transactions')->create([
                'reference'         => $reference,
                'payer_type'        => 'App\Models\UserCarbur',
                'payer_id'          => $userId,
                'amount'            => $amount,
                'subscription_id'   => $subscription->id_subcrip,
                'currency'          => 'XOF',
                'payment_method'    => $validated['payment_method'],
                'status'            => 'pending',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $payload = [
                'amount' => (string) $amount,
                'currency' => 'XOF',
                'success_url' => 'https://pay.gocarbu.com/payment/wave/success/' . $subscription->id_subcrip,
                'error_url'   => 'https://pay.gocarbu.com/payment/wave/error/' . $subscription->id_subcrip,
                'client_reference' => (string) $userId,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer wave_ci_prod_tIc5B0OlAxjucp29W83a2YLvua7Z7FOTmAFYtQlONucpqcNHU0TklALECuBP-nf5HL8HkGgopw0UzPFz2aXld43qhMcAwXINng',
                'Content-Type'  => 'application/json',
            ])->post('https://api.wave.com/v1/checkout/sessions', $payload);

            if (!$response->successful()) {
                Log::error('Wave error', $response->json());

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur Wave',
                    'details' => $response->json(),
                ], 500);
            }

            $data = $response->json();

            $subscription->update([
                'payment_transaction_id' => $data['id'],
            ]);
            $transaction->update([
                'operator_transaction_id' => $data['id'],
            ]);

            return response()->json([
                'success' => true,
                'subscription_url' => $data['wave_launch_url'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Wave Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
            ], 500);
        }

        // TODO : appeler l'API de paiement (CinetPay, Orange Money, etc.)
        // $paymentResponse = PaymentService::initiate($reference, $amount, $validated);
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Paiement initié.',
        //     'data'    => [
        //         'reference'      => $reference,
        //         'amount'         => $amount,
        //         'currency'       => 'XOF',
        //         'payment_method' => $validated['payment_method'],
        //         // 'payment_url' => $paymentResponse->url, // lien redirect opérateur
        //     ],
        // ]);
    }

    // GET /connecte/subscription/status/{reference}
    public function status(Request $request, string $reference): JsonResponse
    {
        $userId = $request->idUser;

        $transaction = DB::table('payment_transactions')
            ->where('reference', $reference)
            ->where('payer_type', 'App\Models\UserCarbur')
            ->where('payer_id', $userId)
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $transaction]);
    }

    // POST /connecte/subscription/cancel
    public function cancel(Request $request): JsonResponse
    {
        $userId = $request->idUser;

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $subscription = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\UserCarbur')
            ->where('subscribable_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Aucun abonnement actif.'], 404);
        }

        DB::table('subscriptions')
            ->where('id_subcrip', $subscription->id_subcrip)
            ->update([
                'status'               => 'cancelled',
                'cancellation_reason'  => $validated['reason'] ?? null,
                'cancelled_at'         => now(),
                'updated_at'           => now(),
            ]);

        // Rétrograder le type d'abonnement de l'utilisateur
        DB::table('users_carbur')
            ->where('id_user_carbu', $userId)
            ->update([
                'subscription_type'       => 'free',
                'subscription_expires_at' => null,
                'updated_at'              => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Abonnement annulé.']);
    }

    // GET /connecte/subscription/history
    public function history(Request $request): JsonResponse
    {
        $userId = $request->idUser;
        $limit  = $request->input('limit', 10);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('payment_transactions')
            ->where('payer_type', 'App\Models\UserCarbur')
            ->where('payer_id', $userId)
            ->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }
}
