<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

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
                'price'         => 15000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout gratuit +', 'Mise à jour des prix en temps réel', 'Promotions (jusqu\'à 3)', 'Statistiques de base', 'Badge Pro'],
            ],
            [
                'plan'          => 'station_premium',
                'label'         => 'Station Premium',
                'target'        => 'station',
                'price'         => 35000,
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
                'price'         => 12000,
                'currency'      => 'XOF',
                'billing_cycle' => 'mensuel',
                'features'      => ['Tout gratuit +', 'Promotions (jusqu\'à 3)', 'Statistiques de base', 'Badge Pro'],
            ],
            [
                'plan'          => 'garage_premium',
                'label'         => 'Garage Premium',
                'target'        => 'garage',
                'price'         => 28000,
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
        $user      = $request->user();
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
        $user      = $request->user();
        $isStation = isset($user->id_station_owner);

        $allowedPlans = $isStation
            ? ['station_pro', 'station_premium']
            : ['garage_pro', 'garage_premium'];

        $request->validate([
            'plan'           => 'required|in:' . implode(',', $allowedPlans),
            'billing_cycle'  => 'required|in:mensuel,trimestriel,annuel',
            'payment_method' => 'required|in:orange_money,mtn_money,moov_money,cinetpay,wave,especes',
            'phone'          => 'required|string|max:20',
        ]);

        // Grille tarifaire
        $priceGrid = [
            'station_pro'     => ['mensuel' => 15000, 'trimestriel' => 42000, 'annuel' => 160000],
            'station_premium' => ['mensuel' => 35000, 'trimestriel' => 98000, 'annuel' => 375000],
            'garage_pro'      => ['mensuel' => 12000, 'trimestriel' => 34000, 'annuel' => 130000],
            'garage_premium'  => ['mensuel' => 28000, 'trimestriel' => 78000, 'annuel' => 300000],
        ];

        $amount    = $priceGrid[$request->plan][$request->billing_cycle];
        $reference = 'PRO-' . strtoupper(uniqid());

        $payerType = $isStation ? 'App\Models\StationOwner' : 'App\Models\GarageOwner';
        $payerId   = $isStation ? $user->id_station_owner  : $user->id_gara_owner;

        DB::table('payment_transactions')->insertGetId([
            'reference'      => $reference,
            'payer_type'     => $payerType,
            'payer_id'       => $payerId,
            'amount'         => $amount,
            'currency'       => 'XOF',
            'payment_method' => $request->payment_method,
            'phone_payer'    => $request->phone,
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // TODO : intégrer CinetPay / Orange Money API

        return response()->json([
            'success' => true,
            'message' => 'Paiement initié.',
            'data'    => [
                'reference'      => $reference,
                'amount'         => $amount,
                'currency'       => 'XOF',
                'payment_method' => $request->payment_method,
            ],
        ]);
    }

    // GET /pro/subscription/subscription/status/{reference}
    public function status(Request $request, string $reference): JsonResponse
    {
        $user      = $request->user();
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
        $user      = $request->user();
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
        $user      = $request->user();
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
