<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private function getVehicle(Request $request, int $vehicleId): Vehicle
    {
        return $request->user()->vehicles()->findOrFail($vehicleId);
    }

    /**
     * GET /vehicles/{vehicleId}/documents
     */
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle   = $this->getVehicle($request, $vehicleId);
        $documents = $vehicle->documents()
            ->orderBy('expiry_date')
            ->get();

        return response()->json(['success' => true, 'data' => $documents]);
    }

    /**
     * POST /vehicles/{vehicleId}/documents
     */
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = $this->getVehicle($request, $vehicleId);

        $data = $request->validate([
            'type'        => 'required|in:permis_conduire,assurance,carte_grise,visite_technique,vignette,autre',
            'number'      => 'nullable|string|max:100',
            'issue_date'  => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:500',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $path            = $request->file('file')->store("documents/{$vehicle->id}", 'public');
            $data['file_url']  = Storage::url($path);
            $data['file_path'] = $path;
        }

        $data['vehicle_id'] = $vehicle->id;
        $document = Document::create($data);

        // Créer un rappel automatique si expiry_date présente
        if ($document->expiry_date) {
            $vehicle->reminders()->create([
                'user_id'           => $request->user()->id,
                'document_id'       => $document->id,
                'type'              => 'expiration_' . $document->type,
                'title'             => "Expiration {$document->type}",
                'remind_at'         => $document->expiry_date->subDays(30),
                'remind_before_days'=> 30,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Document ajouté.', 'data' => $document], 201);
    }

    /**
     * GET /vehicles/{vehicleId}/documents/{id}
     */
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle  = $this->getVehicle($request, $vehicleId);
        $document = $vehicle->documents()->findOrFail($id);

        return response()->json(['success' => true, 'data' => $document]);
    }

    /**
     * PUT /vehicles/{vehicleId}/documents/{id}
     */
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle  = $this->getVehicle($request, $vehicleId);
        $document = $vehicle->documents()->findOrFail($id);

        $data = $request->validate([
            'number'      => 'nullable|string|max:100',
            'issue_date'  => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes'       => 'nullable|string|max:500',
        ]);

        $document->update($data);

        return response()->json(['success' => true, 'message' => 'Document mis à jour.', 'data' => $document->fresh()]);
    }

    /**
     * DELETE /vehicles/{vehicleId}/documents/{id}
     */
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle  = $this->getVehicle($request, $vehicleId);
        $document = $vehicle->documents()->findOrFail($id);

        // Supprimer le fichier physique
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document supprimé.']);
    }

    /**
     * POST /vehicles/{vehicleId}/documents/{id}/upload
     */
    public function uploadFile(Request $request, int $vehicleId, int $id): JsonResponse
    {
        $vehicle  = $this->getVehicle($request, $vehicleId);
        $document = $vehicle->documents()->findOrFail($id);

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Supprimer l'ancien fichier
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $path = $request->file('file')->store("documents/{$vehicle->id}", 'public');
        $document->update([
            'file_url'  => Storage::url($path),
            'file_path' => $path,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Fichier uploadé.',
            'file_url' => $document->file_url,
        ]);
    }
}
