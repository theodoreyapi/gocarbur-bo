<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Mes véhicules
    // GET /connecte/vehicles
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $vehicles = DB::table('vehicles')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderByDesc('is_primary')
            ->orderBy('brand')
            ->get();

        return response()->json(['success' => true, 'data' => $vehicles]);
    }

    // ─────────────────────────────────────────────
    // STORE — Ajouter un véhicule
    // POST /connecte/vehicles
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $validated = $request->validate([
            'brand'        => 'required|string|max:100',
            'model'        => 'required|string|max:100',
            'year'         => 'required|integer|min:1970|max:' . (date('Y') + 1),
            'plate_number' => 'nullable|string|max:20',
            'fuel_type'    => 'required|in:essence,gasoil,hybride,electrique',
            'mileage'      => 'nullable|integer|min:0',
            'color'        => 'nullable|string|max:50',
        ]);

        // Si c'est le premier véhicule, il devient principal
        $isPrimary = !DB::table('vehicles')->where('user_id', $userId)->whereNull('deleted_at')->exists();

        $id = DB::table('vehicles')->insertGetId(array_merge($validated, [
            'user_id'    => $userId,
            'is_primary' => $isPrimary,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $vehicle = DB::table('vehicles')->where('id_vehicule', $id)->first();

        return response()->json(['success' => true, 'message' => 'Véhicule ajouté.', 'data' => $vehicle], 201);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'un véhicule
    // GET /connecte/vehicles/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $vehicle = $this->findUserVehicle($request->user()->id_user_carbu, $id);

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        // Dernier plein + dernier entretien
        $lastFuel = DB::table('fuel_logs')
            ->where('vehicle_id', $id)
            ->orderByDesc('filled_at')
            ->first(['id_fuel_log', 'fuel_type', 'liters', 'total_cost', 'mileage', 'filled_at']);

        $lastMaintenance = DB::table('maintenance_logs')
            ->where('vehicle_id', $id)
            ->whereNull('deleted_at')
            ->orderByDesc('done_at')
            ->first(['id_maint_log', 'type', 'title', 'cost', 'done_at', 'next_service_date']);

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $vehicle, [
                'last_fuel'        => $lastFuel,
                'last_maintenance' => $lastMaintenance,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier un véhicule
    // PUT /connecte/vehicles/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserVehicle($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $validated = $request->validate([
            'brand'        => 'sometimes|string|max:100',
            'model'        => 'sometimes|string|max:100',
            'year'         => 'sometimes|integer|min:1970|max:' . (date('Y') + 1),
            'plate_number' => 'sometimes|nullable|string|max:20',
            'fuel_type'    => 'sometimes|in:essence,gasoil,hybride,electrique',
            'mileage'      => 'sometimes|nullable|integer|min:0',
            'color'        => 'sometimes|nullable|string|max:50',
        ]);

        DB::table('vehicles')
            ->where('id_vehicule', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $vehicle = DB::table('vehicles')->where('id_vehicule', $id)->first();

        return response()->json(['success' => true, 'message' => 'Véhicule mis à jour.', 'data' => $vehicle]);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Supprimer un véhicule
    // DELETE /connecte/vehicles/{id}
    // ─────────────────────────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserVehicle($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        DB::table('vehicles')
            ->where('id_vehicule', $id)
            ->update(['deleted_at' => now()]);

        // Si c'était le principal, promouvoir le suivant
        $wasPrimary = DB::table('vehicles')->where('id_vehicule', $id)->value('is_primary');
        if ($wasPrimary) {
            $next = DB::table('vehicles')
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->where('id_vehicule', '!=', $id)
                ->first('id_vehicule');

            if ($next) {
                DB::table('vehicles')->where('id_vehicule', $next->id_vehicule)->update(['is_primary' => true]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Véhicule supprimé.']);
    }

    // ─────────────────────────────────────────────
    // SET PRIMARY — Véhicule principal
    // PATCH /connecte/vehicles/{id}/set-primary
    // ─────────────────────────────────────────────
    public function setPrimary(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        if (!$this->findUserVehicle($userId, $id)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        // Retirer le statut principal de tous
        DB::table('vehicles')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->update(['is_primary' => false]);

        // Définir le nouveau principal
        DB::table('vehicles')
            ->where('id_vehicule', $id)
            ->update(['is_primary' => true, 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Véhicule principal défini.']);
    }

    // ─────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────
    private function findUserVehicle(int $userId, int $vehicleId): ?object
    {
        return DB::table('vehicles')
            ->where('id_vehicule', $vehicleId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();
    }
}
