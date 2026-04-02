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
    private function getVehicle(Request $request, int $vehicleId): Vehicle
    {
        return $request->user()->vehicles()->findOrFail($vehicleId);
    }

    /**
     * GET /vehicles/{vehicleId}/fuel-logs
     */
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $logs = $vehicle->fuelLogs()
            ->with('station:id,name,brand,logo_url')
            ->when($request->fuel_type, fn($q) => $q->where('fuel_type', $request->fuel_type))
            ->when($request->from, fn($q) => $q->where('filled_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->where('filled_at', '<=', $request->to))
            ->orderByDesc('filled_at')
            ->paginate($request->input('per_page', 15));

        return response()->json(['success' => true, 'data' => $logs]);
    }

    /**
     * POST /vehicles/{vehicleId}/fuel-logs
     */
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $data = $request->validate([
            'fuel_type'       => 'required|in:essence,gasoil,sans_plomb,super',
            'liters'          => 'required|numeric|min:0.5|max:200',
            'price_per_liter' => 'required|numeric|min:1',
            'station_id'      => 'nullable|exists:stations,id',
            'mileage'         => 'nullable|integer|min:0',
            'full_tank'       => 'boolean',
            'notes'           => 'nullable|string|max:300',
            'filled_at'       => 'nullable|date',
        ]);

        $data['total_cost'] = round($data['liters'] * $data['price_per_liter'], 2);
        $data['vehicle_id'] = $vehicle->id;
        $data['filled_at']  = $data['filled_at'] ?? now();

        $log = FuelLog::create($data);

        // Mettre à jour kilométrage véhicule
        if (!empty($data['mileage']) && $data['mileage'] > $vehicle->mileage) {
            $vehicle->update(['mileage' => $data['mileage']]);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Plein enregistré.',
            'data'       => $log->load('station:id,name,brand'),
            'total_cost' => $data['total_cost'],
        ], 201);
    }

    /**
     * GET /vehicles/{vehicleId}/fuel-logs/stats  (Premium uniquement)
     */
    public function stats(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $year  = $request->input('year', now()->year);
        $month = $request->input('month'); // optionnel

        // Stats mensuelles sur 12 mois
        $monthly = $vehicle->fuelLogs()
            ->selectRaw('
                YEAR(filled_at)  AS year,
                MONTH(filled_at) AS month,
                COUNT(*)         AS fills_count,
                SUM(liters)      AS total_liters,
                SUM(total_cost)  AS total_cost,
                AVG(price_per_liter) AS avg_price
            ')
            ->whereYear('filled_at', $year)
            ->when($month, fn($q) => $q->whereMonth('filled_at', $month))
            ->groupByRaw('YEAR(filled_at), MONTH(filled_at)')
            ->orderByRaw('YEAR(filled_at), MONTH(filled_at)')
            ->get();

        // Totaux globaux
        $totals = $vehicle->fuelLogs()
            ->selectRaw('
                COUNT(*)     AS total_fills,
                SUM(liters)  AS total_liters,
                SUM(total_cost) AS total_spent,
                AVG(price_per_liter) AS avg_price_per_liter
            ')
            ->whereYear('filled_at', $year)
            ->first();

        // Consommation moyenne (L/100km) — si données kilométrage disponibles
        $consumption = null;
        $logsWithMileage = $vehicle->fuelLogs()
            ->whereNotNull('mileage')
            ->orderBy('mileage')
            ->get(['mileage', 'liters']);

        if ($logsWithMileage->count() >= 2) {
            $totalKm     = $logsWithMileage->last()->mileage - $logsWithMileage->first()->mileage;
            $totalLiters = $logsWithMileage->skip(1)->sum('liters'); // Exclure le premier plein
            if ($totalKm > 0) {
                $consumption = round(($totalLiters / $totalKm) * 100, 2);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'year'              => $year,
                'monthly'           => $monthly,
                'totals'            => $totals,
                'avg_consumption'   => $consumption ? "{$consumption} L/100km" : null,
            ],
        ]);
    }

    /**
     * GET /vehicles/{vehicleId}/fuel-logs/{id}
     */
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->fuelLogs()->with('station:id,name,brand,address')->findOrFail($id);

        return response()->json(['success' => true, 'data' => $log]);
    }

    /**
     * PUT /vehicles/{vehicleId}/fuel-logs/{id}
     */
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->fuelLogs()->findOrFail($id);

        $data = $request->validate([
            'fuel_type'       => 'sometimes|in:essence,gasoil,sans_plomb,super',
            'liters'          => 'sometimes|numeric|min:0.5|max:200',
            'price_per_liter' => 'sometimes|numeric|min:1',
            'station_id'      => 'nullable|exists:stations,id',
            'mileage'         => 'nullable|integer|min:0',
            'full_tank'       => 'boolean',
            'notes'           => 'nullable|string|max:300',
            'filled_at'       => 'nullable|date',
        ]);

        if (isset($data['liters']) || isset($data['price_per_liter'])) {
            $liters  = $data['liters'] ?? $log->liters;
            $price   = $data['price_per_liter'] ?? $log->price_per_liter;
            $data['total_cost'] = round($liters * $price, 2);
        }

        $log->update($data);

        return response()->json(['success' => true, 'message' => 'Plein mis à jour.', 'data' => $log->fresh()]);
    }

    /**
     * DELETE /vehicles/{vehicleId}/fuel-logs/{id}
     */
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->fuelLogs()->findOrFail($id);
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Enregistrement supprimé.']);
    }
}
