<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceLogController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Historique d'entretien
    // GET /connecte/vehicles/{vehicleId}/maintenance
    // ─────────────────────────────────────────────
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $limit = $request->input('limit', 20);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('maintenance_logs')
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->orderByDesc('done_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE — Ajouter une entrée d'entretien
    // POST /connecte/vehicles/{vehicleId}/maintenance
    // ─────────────────────────────────────────────
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $validated = $request->validate([
            'type'                 => 'required|in:vidange,pneus,batterie,freins,filtres,climatisation,courroie,amortisseurs,reparation,revision,autre',
            'title'                => 'required|string|max:150',
            'notes'                => 'nullable|string|max:500',
            'cost'                 => 'nullable|numeric|min:0',
            'done_at'              => 'required|date',
            'mileage_at_service'   => 'nullable|integer|min:0',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date'    => 'nullable|date|after:done_at',
            'garage_name'          => 'nullable|string|max:150',
        ]);

        $id = DB::table('maintenance_logs')->insertGetId(array_merge($validated, [
            'vehicle_id' => $vehicleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        // Mettre à jour le kilométrage du véhicule si fourni et supérieur
        if (!empty($validated['mileage_at_service'])) {
            $vehicle = DB::table('vehicles')->where('id_vehicule', $vehicleId)->first(['mileage']);
            if (!$vehicle->mileage || $validated['mileage_at_service'] > $vehicle->mileage) {
                DB::table('vehicles')->where('id_vehicule', $vehicleId)
                    ->update(['mileage' => $validated['mileage_at_service'], 'updated_at' => now()]);
            }
        }

        $log = DB::table('maintenance_logs')->where('id_maint_log', $id)->first();

        return response()->json(['success' => true, 'message' => 'Entretien enregistré.', 'data' => $log], 201);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'une entrée
    // GET /connecte/vehicles/{vehicleId}/maintenance/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $log = DB::table('maintenance_logs')
            ->where('id_maint_log', $id)
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Entrée introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $log]);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // PUT /connecte/vehicles/{vehicleId}/maintenance/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $log = DB::table('maintenance_logs')
            ->where('id_maint_log', $id)->where('vehicle_id', $vehicleId)->whereNull('deleted_at')->first();

        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Entrée introuvable.'], 404);
        }

        $validated = $request->validate([
            'type'                 => 'sometimes|in:vidange,pneus,batterie,freins,filtres,climatisation,courroie,amortisseurs,reparation,revision,autre',
            'title'                => 'sometimes|string|max:150',
            'notes'                => 'sometimes|nullable|string|max:500',
            'cost'                 => 'sometimes|nullable|numeric|min:0',
            'done_at'              => 'sometimes|date',
            'mileage_at_service'   => 'sometimes|nullable|integer|min:0',
            'next_service_mileage' => 'sometimes|nullable|integer|min:0',
            'next_service_date'    => 'sometimes|nullable|date',
            'garage_name'          => 'sometimes|nullable|string|max:150',
        ]);

        DB::table('maintenance_logs')
            ->where('id_maint_log', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('maintenance_logs')->where('id_maint_log', $id)->first();

        return response()->json(['success' => true, 'message' => 'Entretien mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // DELETE /connecte/vehicles/{vehicleId}/maintenance/{id}
    // ─────────────────────────────────────────────
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $exists = DB::table('maintenance_logs')
            ->where('id_maint_log', $id)->where('vehicle_id', $vehicleId)->whereNull('deleted_at')->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Entrée introuvable.'], 404);
        }

        DB::table('maintenance_logs')->where('id_maint_log', $id)->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Entretien supprimé.']);
    }

    private function vehicleBelongsToUser(int $userId, int $vehicleId): bool
    {
        return DB::table('vehicles')
            ->where('id_vehicule', $vehicleId)->where('user_id', $userId)->whereNull('deleted_at')->exists();
    }
}
