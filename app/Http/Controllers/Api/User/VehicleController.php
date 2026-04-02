<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * GET /vehicles
     */
    public function index(Request $request): JsonResponse
    {
        $vehicles = $request->user()
            ->vehicles()
            ->withCount(['documents', 'maintenanceLogs', 'fuelLogs'])
            ->orderByDesc('is_primary')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $vehicles]);
    }

    /**
     * POST /vehicles
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier limite gratuit : 1 véhicule
        if (!$user->isPremium() && $user->vehicles()->count() >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Passez en Premium pour ajouter plusieurs véhicules.',
                'upgrade_required' => true,
            ], 403);
        }

        $data = $request->validate([
            'brand'        => 'required|string|max:60',
            'model'        => 'required|string|max:60',
            'year'         => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'plate_number' => 'nullable|string|max:20',
            'fuel_type'    => 'required|in:essence,gasoil,hybride,electrique',
            'color'        => 'nullable|string|max:40',
            'mileage'      => 'nullable|integer|min:0',
        ]);

        $data['user_id']   = $user->id;
        $data['is_primary']= $user->vehicles()->count() === 0; // Premier = principal

        $vehicle = Vehicle::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Véhicule ajouté.',
            'data'    => $vehicle,
        ], 201);
    }

    /**
     * GET /vehicles/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $vehicle = $request->user()->vehicles()
            ->with(['documents', 'maintenanceLogs' => fn($q) => $q->latest('done_at')->limit(5)])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $vehicle]);
    }

    /**
     * PUT /vehicles/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $vehicle = $request->user()->vehicles()->findOrFail($id);

        $data = $request->validate([
            'brand'        => 'sometimes|string|max:60',
            'model'        => 'sometimes|string|max:60',
            'year'         => 'sometimes|integer|min:1950|max:' . (date('Y') + 1),
            'plate_number' => 'nullable|string|max:20',
            'fuel_type'    => 'sometimes|in:essence,gasoil,hybride,electrique',
            'color'        => 'nullable|string|max:40',
            'mileage'      => 'nullable|integer|min:0',
        ]);

        $vehicle->update($data);

        return response()->json(['success' => true, 'message' => 'Véhicule mis à jour.', 'data' => $vehicle->fresh()]);
    }

    /**
     * DELETE /vehicles/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $vehicle = $request->user()->vehicles()->findOrFail($id);
        $vehicle->delete();

        return response()->json(['success' => true, 'message' => 'Véhicule supprimé.']);
    }

    /**
     * PATCH /vehicles/{id}/set-primary
     */
    public function setPrimary(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $vehicle = $user->vehicles()->findOrFail($id);

        DB::transaction(function () use ($user, $vehicle) {
            $user->vehicles()->update(['is_primary' => false]);
            $vehicle->update(['is_primary' => true]);
        });

        return response()->json(['success' => true, 'message' => 'Véhicule principal défini.']);
    }
}
