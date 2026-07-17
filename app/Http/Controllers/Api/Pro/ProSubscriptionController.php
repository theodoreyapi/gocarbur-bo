<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProSubscriptionController extends Controller
{
    // GET /pro/subscription/subscription/plans
    public function plans(): JsonResponse
    {
        $plans = [
            // ── Stations ──────────────────────────────
            [
                'plan'     => 'station_free',
                'label'    => 'Station Gratuit',
                'target'   => 'station',
                'price'    => 0,
                'currency' => 'XOF',
                'features' => ['Fiche station basique', 'Visible sur la carte', 'Prix carburant public'],
            ],
            [
                'plan'          => 'station_pro',
                'label'         => 'Station Pro',
                'target'        => 'station',
                'price'         => 5000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout gratuit +', 'Mise à jour des prix en temps réel', 'Promotions (jusqu\'à 3)', 'Statistiques de base', 'Badge Pro'],
            ],
            [
                'plan'          => 'station_premium',
                'label'         => 'Station Premium',
                'target'        => 'station',
                'price'         => 10000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout Pro +', 'Promotions illimitées', 'Statistiques avancées', 'Badge Premium', 'Push notifications clients', 'Support prioritaire'],
            ],
            // ── Garages ───────────────────────────────
            [
                'plan'     => 'garage_free',
                'label'    => 'Garage Gratuit',
                'target'   => 'garage',
                'price'    => 0,
                'currency' => 'XOF',
                'features' => ['Fiche garage basique', 'Visible sur la carte'],
            ],
            [
                'plan'          => 'garage_pro',
                'label'         => 'Garage Pro',
                'target'        => 'garage',
                'price'         => 5000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout gratuit +', 'Promotions (jusqu\'à 3)', 'Statistiques de base', 'Badge Pro'],
            ],
            [
                'plan'          => 'garage_premium',
                'label'         => 'Garage Premium',
                'target'        => 'garage',
                'price'         => 10000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout Pro +', 'Promotions illimitées', 'Statistiques avancées', 'Badge Premium', 'Push notifications', 'Support prioritaire'],
            ],
        ];

        return response()->json(['success' => true, 'data' => $plans]);
    }

    // GET /pro/subscription/subscription/current
    public function current(Request $request): JsonResponse
    {
        $user      = $request;
        $isStation = isset($user->id_station_owner);

        $type     = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $ownerId  = $isStation ? $user->id_station_owner  : $user->id_gara_owner;

        $sub = DB::table('subscriptions')
            ->where('subscribable_type', $type)
            ->where('subscribable_id', $ownerId)
            ->where('status', 'active')
            ->orderByDesc('expires_at')
            ->first();

        return response()->json(['success' => true, 'data' => $sub]);
    }

    // POST /pro/subscription/subscription/initiate
    public function initiate(Request $request): JsonResponse
    {
        $user      = $request;
        $isStation = isset($user->id_station_owner);

        $allowedPlans = $isStation
            ? ['station_pro', 'station_premium']
            : ['garage_pro', 'garage_premium'];

        $request->validate([
            'plan'           => 'required|in:' . implode(',', $allowedPlans),
            'billing_cycle'  => 'required|in:mensuel,trimestriel,annuel',
            'payment_method' => 'required|in:orange_money,mtn_money,moov_money,cinetpay,wave,especes',
            'starts' => 'required',
            'expires' => 'required',
        ]);

        // Grille tarifaire
        $priceGrid = [
            'station_pro'     => ['mensuel' => 5000, 'trimestriel' => 12000, 'annuel' => 57000],
            'station_premium' => ['mensuel' => 10000, 'trimestriel' => 27000, 'annuel' => 117000],
            'garage_pro'      => ['mensuel' => 5000, 'trimestriel' => 12000, 'annuel' => 57000],
            'garage_premium'  => ['mensuel' => 10000, 'trimestriel' => 27000, 'annuel' => 117000],
        ];

        $amount    = $priceGrid[$request->plan][$request->billing_cycle];
        $reference = 'PRO-' . strtoupper(uniqid());

        $payerType = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $payerId   = $isStation ? $user->id_station_owner  : $user->id_gara_owner;

        try {

            // Créer un abonnement en attente
            $subscription = DB::table('subscriptions')->create([
                'subscribable_type'     => $payerType,
                'subscribable_id'       => $payerId,
                'amount'                => $amount,
                'currency'              => 'XOF',
                'payment_method'        => $request->payment_method,
                'plan'                  => $request->plan,
                'billing_cycle'         => $request->billing_cycle,
                'starts_at'             => $request->starts,
                'expires_at'            => $request->expires,
                'status'                => 'pending',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // Créer la transaction en attente
            $transaction = DB::table('payment_transactions')->create([
                'reference'         => $reference,
                'payer_type'        => $payerType,
                'payer_id'          => $payerId,
                'amount'            => $amount,
                'subscription_id'   => $subscription->id_subcrip,
                'currency'          => 'XOF',
                'payment_method'    => $request->payment_method,
                'status'            => 'pending',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $payload = [
                'amount' => (string) $amount,
                'currency' => 'XOF',
                'success_url' => 'https://pay.gocarbu.com/pro/wave/success/' . $subscription->id_subcrip,
                'error_url'   => 'https://pay.gocarbu.com/pro/wave/error/' . $subscription->id_subcrip,
                'client_reference' => (string) $payerId,
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
        // TODO : intégrer CinetPay / Orange Money API

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Paiement initié.',
        //     'data'    => [
        //         'reference'      => $reference,
        //         'amount'         => $amount,
        //         'currency'       => 'XOF',
        //         'payment_method' => $request->payment_method,
        //     ],
        // ]);
    }

    // GET /pro/subscription/subscription/status/{reference}
    public function status(Request $request, string $reference): JsonResponse
    {
        $user      = $request;
        $isStation = isset($user->id_station_owner);
        $payerType = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $payerId   = $isStation ? $user->id_station_owner  : $user->id_gara_owner;

        $transaction = DB::table('payment_transactions')
            ->where('reference', $reference)
            ->where('payer_type', $payerType)
            ->where('payer_id', $payerId)
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $transaction]);
    }

    // GET /pro/subscription/subscription/history
    public function history(Request $request): JsonResponse
    {
        $user      = $request;
        $isStation = isset($user->id_station_owner);
        $payerType = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $payerId   = $isStation ? $user->id_station_owner  : $user->id_gara_owner;
        $limit     = $request->input('limit', 10);
        $page      = max(1, (int) $request->input('page', 1));

        $query = DB::table('payment_transactions')
            ->where('payer_type', $payerType)
            ->where('payer_id', $payerId)
            ->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // POST /pro/subscription/subscription/cancel
    public function cancel(Request $request): JsonResponse
    {
        $user      = $request;
        $isStation = isset($user->id_station_owner);
        $subType   = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $ownerId   = $isStation ? $user->id_station_owner  : $user->id_gara_owner;

        $request->validate(['reason' => 'nullable|string|max:500']);

        $sub = DB::table('subscriptions')
            ->where('subscribable_type', $subType)
            ->where('subscribable_id', $ownerId)
            ->where('status', 'active')
            ->first();

        if (!$sub) {
            return response()->json(['success' => false, 'message' => 'Aucun abonnement actif.'], 404);
        }

        DB::table('subscriptions')
            ->where('id_subcrip', $sub->id_subcrip)
            ->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_at'        => now(),
                'updated_at'          => now(),
            ]);

        // Rétrograder le plan de chaque entité liée
        if ($isStation) {
            $stationIds = DB::table('station_owner_station')->where('station_owner_id', $ownerId)->pluck('station_id');
            DB::table('stations')->whereIn('id_station', $stationIds)->update(['subscription_type' => 'free', 'updated_at' => now()]);
        } else {
            $garageIds = DB::table('garage_owner_garage')->where('garage_owner_id', $ownerId)->pluck('garage_id');
            DB::table('garages')->whereIn('id_garage', $garageIds)->update(['subscription_type' => 'free', 'updated_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Abonnement annulé.']);
    }
}
