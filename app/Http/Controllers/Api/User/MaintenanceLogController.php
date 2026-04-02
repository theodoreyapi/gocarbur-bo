<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    private function getVehicle(Request $request, int $vehicleId): Vehicle
    {
        return $request->user()->vehicles()->findOrFail($vehicleId);
    }

    /**
     * GET /vehicles/{vehicleId}/maintenance
     */
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $logs = $vehicle->maintenanceLogs()
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderByDesc('done_at')
            ->paginate($request->input('per_page', 15));

        return response()->json(['success' => true, 'data' => $logs]);
    }

    /**
     * POST /vehicles/{vehicleId}/maintenance
     */
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $data = $request->validate([
            'type'                  => 'required|in:vidange,pneus,batterie,freins,filtres,climatisation,courroie,amortisseurs,reparation,revision,autre',
            'title'                 => 'required|string|max:150',
            'notes'                 => 'nullable|string|max:1000',
            'cost'                  => 'nullable|numeric|min:0',
            'done_at'               => 'required|date',
            'mileage_at_service'    => 'nullable|integer|min:0',
            'next_service_mileage'  => 'nullable|integer|min:0',
            'next_service_date'     => 'nullable|date|after:done_at',
            'garage_name'           => 'nullable|string|max:150',
        ]);

        $data['vehicle_id'] = $vehicle->id;
        $log = $vehicle->maintenanceLogs()->create($data);

        // Mettre à jour le kilométrage du véhicule si plus élevé
        if (!empty($data['mileage_at_service']) && $data['mileage_at_service'] > $vehicle->mileage) {
            $vehicle->update(['mileage' => $data['mileage_at_service']]);
        }

        // Créer rappel prochain entretien
        if (!empty($data['next_service_date'])) {
            $vehicle->reminders()->create([
                'user_id'    => $request->user()->id,
                'type'       => 'entretien',
                'title'      => "Prochain entretien : {$data['type']}",
                'remind_at'  => \Carbon\Carbon::parse($data['next_service_date'])->subDays(7),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Entrée ajoutée.', 'data' => $log], 201);
    }

    /**
     * GET /vehicles/{vehicleId}/maintenance/{id}
     */
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->maintenanceLogs()->findOrFail($id);

        return response()->json(['success' => true, 'data' => $log]);
    }

    /**
     * PUT /vehicles/{vehicleId}/maintenance/{id}
     */
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->maintenanceLogs()->findOrFail($id);

        $data = $request->validate([
            'type'                 => 'sometimes|in:vidange,pneus,batterie,freins,filtres,climatisation,courroie,amortisseurs,reparation,revision,autre',
            'title'                => 'sometimes|string|max:150',
            'notes'                => 'nullable|string|max:1000',
            'cost'                 => 'nullable|numeric|min:0',
            'done_at'              => 'sometimes|date',
            'mileage_at_service'   => 'nullable|integer|min:0',
            'next_service_mileage' => 'nullable|integer|min:0',
            'next_service_date'    => 'nullable|date',
            'garage_name'          => 'nullable|string|max:150',
        ]);

        $log->update($data);

        return response()->json(['success' => true, 'message' => 'Entrée mise à jour.', 'data' => $log->fresh()]);
    }

    /**
     * DELETE /vehicles/{vehicleId}/maintenance/{id}
     */
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);
        $log     = $vehicle->maintenanceLogs()->findOrFail($id);
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Entrée supprimée.']);
    }
}
