<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStationController extends Controller
{
    /** GET /admin/stations */
    public function index(Request $request): JsonResponse
    {
        $stations = Station::withTrashed()
            ->with(['fuelPrices', 'services'])
            ->when($request->search, fn($q) =>
                $q->where('name','like',"%{$request->search}%")
                  ->orWhere('city','like',"%{$request->search}%")
            )
            ->when($request->city,             fn($q) => $q->where('city', $request->city))
            ->when($request->verified !== null, fn($q) => $q->where('is_verified', $request->boolean('verified')))
            ->when($request->subscription,     fn($q) => $q->where('subscription_type', $request->subscription))
            ->withCount('reviews')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 25));

        return response()->json(['success' => true, 'data' => $stations]);
    }

    /** POST /admin/stations */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'brand'       => 'nullable|string|max:60',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'opens_at'    => 'nullable|date_format:H:i',
            'closes_at'   => 'nullable|date_format:H:i',
            'is_open_24h' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $station = Station::create($data);

        return response()->json(['success' => true, 'message' => 'Station créée.', 'data' => $station], 201);
    }

    /** GET /admin/stations/{id} */
    public function show(int $id): JsonResponse
    {
        $station = Station::withTrashed()
            ->with(['fuelPrices','services','promotions','reviews','priceHistory'])
            ->withCount(['reviews','views','fuelLogs'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $station]);
    }

    /** PUT /admin/stations/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $station = Station::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:150',
            'brand'       => 'nullable|string|max:60',
            'address'     => 'sometimes|string|max:255',
            'city'        => 'sometimes|string|max:100',
            'latitude'    => 'sometimes|numeric',
            'longitude'   => 'sometimes|numeric',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'opens_at'    => 'nullable|date_format:H:i',
            'closes_at'   => 'nullable|date_format:H:i',
            'is_open_24h' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $station->update($data);

        return response()->json(['success' => true, 'message' => 'Station mise à jour.', 'data' => $station->fresh()]);
    }

    /** PATCH /admin/stations/{id}/verify */
    public function verify(int $id): JsonResponse
    {
        Station::findOrFail($id)->update(['is_verified' => true]);
        return response()->json(['success' => true, 'message' => 'Badge vérifié attribué.']);
    }

    /** PATCH /admin/stations/{id}/unverify */
    public function unverify(int $id): JsonResponse
    {
        Station::findOrFail($id)->update(['is_verified' => false]);
        return response()->json(['success' => true, 'message' => 'Badge vérifié retiré.']);
    }

    /** PATCH /admin/stations/{id}/toggle-active */
    public function toggleActive(int $id): JsonResponse
    {
        $station = Station::withTrashed()->findOrFail($id);
        $station->update(['is_active' => !$station->is_active]);
        $action = $station->is_active ? 'activée' : 'désactivée';

        return response()->json(['success' => true, 'message' => "Station {$action}."]);
    }

    /** PUT /admin/stations/{id}/prices */
    public function updatePrices(Request $request, int $id): JsonResponse
    {
        $station = Station::findOrFail($id);

        $data = $request->validate([
            'prices'                => 'required|array',
            'prices.*.fuel_type'    => 'required|in:essence,gasoil,sans_plomb,super,gpl',
            'prices.*.price'        => 'required|numeric|min:1',
            'prices.*.is_available' => 'boolean',
        ]);

        DB::transaction(function () use ($station, $data) {
            foreach ($data['prices'] as $item) {
                $station->fuelPrices()->updateOrCreate(
                    ['fuel_type' => $item['fuel_type']],
                    ['price' => $item['price'], 'is_available' => $item['is_available'] ?? true, 'updated_at_price' => now()]
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Prix mis à jour par admin.', 'data' => $station->fuelPrices()->get()]);
    }

    /** DELETE /admin/stations/{id} */
    public function destroy(int $id): JsonResponse
    {
        Station::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Station supprimée.']);
    }
}
