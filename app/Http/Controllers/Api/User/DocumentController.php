<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Lister les documents d'un véhicule
    // GET /connecte/vehicles/{vehicleId}/documents
    // ─────────────────────────────────────────────
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $documents = DB::table('documents')
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->orderBy('expiry_date')
            ->get();

        return response()->json(['success' => true, 'data' => $documents]);
    }

    // ─────────────────────────────────────────────
    // STORE — Ajouter un document
    // POST /connecte/vehicles/{vehicleId}/documents
    // ─────────────────────────────────────────────
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $validated = $request->validate([
            'type'        => 'required|in:permis_conduire,assurance,carte_grise,visite_technique,vignette,autre',
            'number'      => 'nullable|string|max:100',
            'issue_date'  => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'notes'       => 'nullable|string|max:500',
            'file'        => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $fileUrl  = null;
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("documents/{$vehicleId}", 'public');
            $fileUrl  = '/storage/' . $filePath;
        }

        // Calculer le statut initial
        $status = $this->computeStatus($validated['expiry_date'] ?? null);

        $id = DB::table('documents')->insertGetId([
            'vehicle_id'  => $vehicleId,
            'type'        => $validated['type'],
            'number'      => $validated['number'] ?? null,
            'issue_date'  => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'file_url'    => $fileUrl,
            'file_path'   => $filePath,
            'status'      => $status,
            'notes'       => $validated['notes'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $document = DB::table('documents')->where('id_doc', $id)->first();

        return response()->json(['success' => true, 'message' => 'Document ajouté.', 'data' => $document], 201);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'un document
    // GET /connecte/vehicles/{vehicleId}/documents/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $document = DB::table('documents')
            ->where('id_doc', $id)
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document introuvable.'], 404);
        }

        return response()->json(['success' => true, 'data' => $document]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier un document
    // PUT /connecte/vehicles/{vehicleId}/documents/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $document = DB::table('documents')
            ->where('id_doc', $id)
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document introuvable.'], 404);
        }

        $validated = $request->validate([
            'number'      => 'sometimes|nullable|string|max:100',
            'issue_date'  => 'sometimes|nullable|date',
            'expiry_date' => 'sometimes|nullable|date',
            'notes'       => 'sometimes|nullable|string|max:500',
        ]);

        $expiryDate = $validated['expiry_date'] ?? $document->expiry_date;
        $validated['status'] = $this->computeStatus($expiryDate);

        DB::table('documents')
            ->where('id_doc', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('documents')->where('id_doc', $id)->first();

        return response()->json(['success' => true, 'message' => 'Document mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Supprimer un document
    // DELETE /connecte/vehicles/{vehicleId}/documents/{id}
    // ─────────────────────────────────────────────
    public function destroy(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $document = DB::table('documents')
            ->where('id_doc', $id)
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document introuvable.'], 404);
        }

        DB::table('documents')->where('id_doc', $id)->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Document supprimé.']);
    }

    // ─────────────────────────────────────────────
    // UPLOAD FILE — Upload ou remplacement de la photo
    // POST /connecte/vehicles/{vehicleId}/documents/{id}/upload
    // ─────────────────────────────────────────────
    public function uploadFile(Request $request, int $vehicleId, int $id): JsonResponse
    {
        if (!$this->vehicleBelongsToUser($request->user()->id_user_carbu, $vehicleId)) {
            return response()->json(['success' => false, 'message' => 'Véhicule introuvable.'], 404);
        }

        $request->validate(['file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120']);

        $document = DB::table('documents')
            ->where('id_doc', $id)
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document introuvable.'], 404);
        }

        // Supprimer l'ancien fichier
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $filePath = $request->file('file')->store("documents/{$vehicleId}", 'public');
        $fileUrl  = '/storage/' . $filePath;

        DB::table('documents')->where('id_doc', $id)->update([
            'file_url'   => $fileUrl,
            'file_path'  => $filePath,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'data' => ['file_url' => $fileUrl]]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function vehicleBelongsToUser(int $userId, int $vehicleId): bool
    {
        return DB::table('vehicles')
            ->where('id_vehicule', $vehicleId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function computeStatus(?string $expiryDate): string
    {
        if (!$expiryDate) return 'valid';
        $expiry = \Carbon\Carbon::parse($expiryDate);
        if ($expiry->isPast())               return 'expired';
        if ($expiry->diffInDays(now()) <= 30) return 'expiring_soon';
        return 'valid';
    }
}
