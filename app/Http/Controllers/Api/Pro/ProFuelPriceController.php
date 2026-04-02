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
    private function myStation(Request $request, int $id): Station
    {
        return $request->user()->stations()->findOrFail($id);
    }

    /** GET /pro/stations/{stationId}/prices */
    public function index(Request $request, int $stationId): JsonResponse
    {
        $station = $this->myStation($request, $stationId);
        $prices  = $station->fuelPrices()->get();

        return response()->json(['success' => true, 'data' => $prices]);
    }

    /** PUT /pro/stations/{stationId}/prices/{fuelType} */
    public function update(Request $request, int $stationId, string $fuelType): JsonResponse
    {
        $station = $this->myStation($request, $stationId);

        $data = $request->validate([
            'price'        => 'required|numeric|min:1|max:99999',
            'is_available' => 'boolean',
        ]);

        DB::transaction(function () use ($station, $fuelType, $data, $request) {
            $existing = $station->fuelPrices()->where('fuel_type', $fuelType)->first();
            $oldPrice  = $existing?->price;

            $price = $station->fuelPrices()->updateOrCreate(
                ['fuel_type' => $fuelType],
                [
                    'price'            => $data['price'],
                    'is_available'     => $data['is_available'] ?? true,
                    'updated_at_price' => now(),
                ]
            );

            // Historiser la modification
            if ($oldPrice && $oldPrice != $data['price']) {
                FuelPriceHistory::create([
                    'station_id'      => $station->id,
                    'fuel_type'       => $fuelType,
                    'old_price'       => $oldPrice,
                    'new_price'       => $data['price'],
                    'changed_by_type' => get_class($request->user()),
                    'changed_by_id'   => $request->user()->id,
                    'changed_at'      => now(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Prix mis à jour.']);
    }

    /** PUT /pro/stations/{stationId}/prices (tous en une fois) */
    public function updateAll(Request $request, int $stationId): JsonResponse
    {
        $station = $this->myStation($request, $stationId);

        $data = $request->validate([
            'prices'               => 'required|array',
            'prices.*.fuel_type'   => 'required|in:essence,gasoil,sans_plomb,super,gpl',
            'prices.*.price'       => 'required|numeric|min:1',
            'prices.*.is_available'=> 'boolean',
        ]);

        DB::transaction(function () use ($station, $data, $request) {
            foreach ($data['prices'] as $item) {
                $existing = $station->fuelPrices()->where('fuel_type', $item['fuel_type'])->first();
                $oldPrice  = $existing?->price;

                $station->fuelPrices()->updateOrCreate(
                    ['fuel_type'       => $item['fuel_type']],
                    ['price'           => $item['price'],
                     'is_available'    => $item['is_available'] ?? true,
                     'updated_at_price'=> now()]
                );

                if ($oldPrice && $oldPrice != $item['price']) {
                    FuelPriceHistory::create([
                        'station_id'      => $station->id,
                        'fuel_type'       => $item['fuel_type'],
                        'old_price'       => $oldPrice,
                        'new_price'       => $item['price'],
                        'changed_by_type' => get_class($request->user()),
                        'changed_by_id'   => $request->user()->id,
                        'changed_at'      => now(),
                    ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Tous les prix mis à jour.', 'data' => $station->fuelPrices()->get()]);
    }

    /** GET /pro/stations/{stationId}/prices/history */
    public function history(Request $request, int $stationId): JsonResponse
    {
        $station = $this->myStation($request, $stationId);
        $history = FuelPriceHistory::where('station_id', $station->id)
            ->orderByDesc('changed_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $history]);
    }
}
