<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use App\Models\FuelPriceHistory;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProFuelPriceController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Prix actuels d'une station
    // GET /pro/subscription/stations/{stationId}/prices
    // ─────────────────────────────────────────────
    public function index(Request $request, int $stationId): JsonResponse
    {
        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $prices = DB::table('fuel_prices')
            ->where('station_id', $stationId)
            ->orderBy('fuel_type')
            ->get();

        return response()->json(['success' => true, 'data' => $prices]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Mettre à jour un prix (plan Pro/Premium)
    // PUT /pro/subscription/stations/{stationId}/prices/{fuelType}
    // Body: { price: 750.00, is_available: true }
    // ─────────────────────────────────────────────
    public function update(Request $request, int $stationId, string $fuelType): JsonResponse
    {
        $this->requireProPlan($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $request->validate([
            'price'        => 'required|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $validFuelTypes = ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl'];
        if (!in_array($fuelType, $validFuelTypes)) {
            return response()->json(['success' => false, 'message' => 'Type de carburant invalide.'], 422);
        }

        // Récupérer l'ancien prix pour l'historique
        $existing = DB::table('fuel_prices')
            ->where('station_id', $stationId)
            ->where('fuel_type', $fuelType)
            ->first();

        $newPrice = (float) $request->price;

        // Upsert du prix
        DB::table('fuel_prices')->updateOrInsert(
            ['station_id' => $stationId, 'fuel_type' => $fuelType],
            [
                'price'            => $newPrice,
                'is_available'     => $request->input('is_available', true),
                'updated_at_price' => now(),
                'updated_at'       => now(),
                'created_at'       => now(),
            ]
        );

        // Logger dans l'historique si le prix a changé
        if ($existing && (float) $existing->price !== $newPrice) {
            DB::table('fuel_price_history')->insert([
                'station_id'      => $stationId,
                'fuel_type'       => $fuelType,
                'old_price'       => $existing->price,
                'new_price'       => $newPrice,
                'changed_by_type' => 'station_owner',
                'changed_by_id'   => $request->user()->id_station_owner,
                'changed_at'      => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        $updated = DB::table('fuel_prices')
            ->where('station_id', $stationId)
            ->where('fuel_type', $fuelType)
            ->first();

        return response()->json(['success' => true, 'message' => 'Prix mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // UPDATE ALL — Tous les prix en une fois
    // PUT /pro/subscription/stations/{stationId}/prices
    // Body: { prices: [{ fuel_type: "essence", price: 750, is_available: true }, ...] }
    // ─────────────────────────────────────────────
    public function updateAll(Request $request, int $stationId): JsonResponse
    {
        $this->requireProPlan($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $request->validate([
            'prices'               => 'required|array|min:1',
            'prices.*.fuel_type'   => 'required|in:essence,gasoil,sans_plomb,super,gpl',
            'prices.*.price'       => 'required|numeric|min:0',
            'prices.*.is_available'=> 'sometimes|boolean',
        ]);

        $ownerId    = $request->user()->id_station_owner;
        $historyRows = [];

        foreach ($request->prices as $item) {
            $fuelType = $item['fuel_type'];
            $newPrice = (float) $item['price'];

            $existing = DB::table('fuel_prices')
                ->where('station_id', $stationId)
                ->where('fuel_type', $fuelType)
                ->first();

            DB::table('fuel_prices')->updateOrInsert(
                ['station_id' => $stationId, 'fuel_type' => $fuelType],
                [
                    'price'            => $newPrice,
                    'is_available'     => $item['is_available'] ?? true,
                    'updated_at_price' => now(),
                    'updated_at'       => now(),
                    'created_at'       => now(),
                ]
            );

            if ($existing && (float) $existing->price !== $newPrice) {
                $historyRows[] = [
                    'station_id'      => $stationId,
                    'fuel_type'       => $fuelType,
                    'old_price'       => $existing->price,
                    'new_price'       => $newPrice,
                    'changed_by_type' => 'station_owner',
                    'changed_by_id'   => $ownerId,
                    'changed_at'      => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        if (!empty($historyRows)) {
            DB::table('fuel_price_history')->insert($historyRows);
        }

        $prices = DB::table('fuel_prices')
            ->where('station_id', $stationId)
            ->orderBy('fuel_type')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tous les prix mis à jour.',
            'data'    => $prices,
        ]);
    }

    // ─────────────────────────────────────────────
    // HISTORY — Historique des modifications de prix
    // GET /pro/subscription/stations/{stationId}/prices/history
    // ─────────────────────────────────────────────
    public function history(Request $request, int $stationId): JsonResponse
    {
        $this->requireProPlan($request);

        if (!$this->ownsStation($request->user()->id_station_owner, $stationId)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $limit = $request->input('limit', 20);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('fuel_price_history')
            ->where('station_id', $stationId)
            ->orderByDesc('changed_at');

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function ownsStation(int $ownerId, int $stationId): bool
    {
        return DB::table('station_owner_station')
            ->where('station_owner_id', $ownerId)
            ->where('station_id', $stationId)
            ->exists();
    }

    private function requireProPlan(Request $request): void
    {
        $ownerId = $request->user()->id_station_owner;

        $sub = DB::table('subscriptions')
            ->where('subscribable_type', 'App\Models\StationOwner')
            ->where('subscribable_id', $ownerId)
            ->where('status', 'active')
            ->whereIn('plan', ['station_pro', 'station_premium'])
            ->where('expires_at', '>=', now()->toDateString())
            ->first();

        if (!$sub) {
            abort(response()->json([
                'success' => false,
                'message' => 'Fonctionnalité réservée aux plans Pro et Premium.',
                'upgrade' => true,
            ], 403));
        }
    }
}
