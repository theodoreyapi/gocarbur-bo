<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelLogController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Historique des pleins
    // GET /connecte/vehicles/{vehicleId}/fuel-logs
    // ─────────────────────────────────────────────
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $limit = $request->input('limit', 20);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->orderByDesc('filled_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE — Enregistrer un plein
    // POST /connecte/vehicles/{vehicleId}/fuel-logs
    // ─────────────────────────────────────────────
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $validated = $request->validate([
            'fuel_type'       => 'required|in:essence,gasoil,sans_plomb,super',
            'liters'          => 'required|numeric|min:0.1',
            'price_per_liter' => 'required|numeric|min:0',
            'total_cost'      => 'required|numeric|min:0',
            'mileage'         => 'nullable|integer|min:0',
            'full_tank'       => 'sometimes|boolean',
            'notes'           => 'nullable|string|max:300',
            'station_id'      => 'nullable|integer|exists:stations,id_station',
            'filled_at'       => 'required|date',
        ]);

        $id = DB::table('fuel_logs')->insertGetId(array_merge($validated, [
            'vehicle_id' => $vehicleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        // Mettre à jour le kilométrage du véhicule si fourni
        if (!empty($validated['mileage'])) {
            $vehicle = DB::table('vehicles')->where('id_vehicule', $vehicleId)->first(['mileage']);
            if (!$vehicle->mileage || $validated['mileage'] > $vehicle->mileage) {
                DB::table('vehicles')->where('id_vehicule', $vehicleId)
                    ->update(['mileage' => $validated['mileage'], 'updated_at' => now()]);
            }
        }

        $log = DB::table('fuel_logs')->where('id_fuel_log', $id)->first();

        return response()->json(['success' => true, 'message' => 'Plein enregistré.', 'data' => $log], 201);
    }

    // ─────────────────────────────────────────────
    // STATS — Statistiques mensuelles (Premium)
    // GET /connecte/vehicles/{vehicleId}/fuel-logs/stats
    // ─────────────────────────────────────────────
    public function stats(Request $request, int $vehicleId): JsonResponse
    {
        $user = $request->user();

        // Vérification abonnement premium
        if ($user->subscription_type !== 'premium') {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonctionnalité est réservée aux membres Premium.',
                'upgrade' => true,
            ], 403);
        }

        if (!$this->vehicleBelongsToUser($user->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        // Stats par mois (12 derniers mois)
        $monthly = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->where('filled_at', '>=', now()->subMonths(12))
            ->selectRaw("
                DATE_FORMAT(filled_at, '%Y-%m') AS month,
                COUNT(*)                         AS fill_count,
                SUM(liters)                      AS total_liters,
                SUM(total_cost)                  AS total_cost,
                AVG(price_per_liter)             AS avg_price_per_liter
            ")
            ->groupByRaw("DATE_FORMAT(filled_at, '%Y-%m')")
            ->orderByRaw("month DESC")
            ->get();

        // Totaux globaux
        $totals = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->selectRaw("
                COUNT(*)        AS total_fills,
                SUM(liters)     AS total_liters,
                SUM(total_cost) AS total_cost,
                AVG(price_per_liter) AS avg_price
            ")
            ->first();

        // Consommation moyenne (L/100km) si kilométrage disponible
        $firstLog = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->whereNotNull('mileage')
            ->orderBy('filled_at')
            ->first(['mileage', 'filled_at']);

        $lastLog = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->whereNotNull('mileage')
            ->orderByDesc('filled_at')
            ->first(['mileage', 'liters']);

        $avgConsumption = null;
        if ($firstLog && $lastLog && $lastLog->mileage > $firstLog->mileage) {
            $kmDriven       = $lastLog->mileage - $firstLog->mileage;
            $totalLiters    = DB::table('fuel_logs')->where('vehicle_id', $vehicleId)->sum('liters');
            $avgConsumption = round(($totalLiters / $kmDriven) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'monthly'         => $monthly,
                'totals'          => $totals,
                'avg_consumption' => $avgConsumption ? "{$avgConsumption} L/100km" : null,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // GET /connecte/vehicles/{vehicleId}/fuel-logs/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $log = DB::table('fuel_logs')
            ->where('id_fuel_log', $id)
            ->where('vehicle_id', $vehicleId)
            ->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Enregistrement introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $log]);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // PUT /connecte/vehicles/{vehicleId}/fuel-logs/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $log = DB::table('fuel_logs')->where('id_fuel_log', $id)->where('vehicle_id', $vehicleId)->first();
        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Enregistrement introuvable.'], 404);
        }

        $validated = $request->validate([
            'fuel_type'       => 'sometimes|in:essence,gasoil,sans_plomb,super',
            'liters'          => 'sometimes|numeric|min:0.1',
            'price_per_liter' => 'sometimes|numeric|min:0',
            'total_cost'      => 'sometimes|numeric|min:0',
            'mileage'         => 'sometimes|nullable|integer|min:0',
            'full_tank'       => 'sometimes|boolean',
            'notes'           => 'sometimes|nullable|string|max:300',
            'filled_at'       => 'sometimes|date',
        ]);

        DB::table('fuel_logs')
            ->where('id_fuel_log', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('fuel_logs')->where('id_fuel_log', $id)->first();

        return response()->json(['success' => true, 'message' => 'Plein mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // DELETE /connecte/vehicles/{vehicleId}/fuel-logs/{id}
    // ─────────────────────────────────────────────
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $exists = DB::table('fuel_logs')
            ->where('id_fuel_log', $id)->where('vehicle_id', $vehicleId)->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Enregistrement introuvable.'], 404);
        }

        DB::table('fuel_logs')->where('id_fuel_log', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Enregistrement supprimé.']);
    }

    private function vehicleBelongsToUser(int $userId, int $vehicleId): bool
    {
        return DB::table('vehicles')
            ->where('id_vehicule', $vehicleId)->where('user_id', $userId)->whereNull('deleted_at')->exists();
    }
}
