<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use App\Models\FuelPriceHistory;
use App\Models\Station;
use App\Models\StationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProStationController extends Controller
{
    private function myStation(Request $request, int $id): Station
    {
        $owner = $request->user(); // ProOwner
        return $owner->stations()->findOrFail($id);
    }

    /** GET /pro/stations */
    public function index(Request $request): JsonResponse
    {
        $stations = $request->user()->stations()
            ->with(['fuelPrices', 'services'])
            ->withCount(['reviews', 'views'])
            ->get();

        return response()->json(['success' => true, 'data' => $stations]);
    }

    /** GET /pro/stations/{id} */
    public function show(Request $request, int $id): JsonResponse
    {
        $station = $this->myStation($request, $id)
            ->load(['fuelPrices', 'services', 'promotions']);

        return response()->json(['success' => true, 'data' => $station]);
    }

    /** PUT /pro/stations/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $station = $this->myStation($request, $id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:150',
            'address'     => 'sometimes|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'latitude'    => 'sometimes|numeric',
            'longitude'   => 'sometimes|numeric',
        ]);

        $station->update($data);

        return response()->json(['success' => true, 'message' => 'Station mise à jour.', 'data' => $station->fresh()]);
    }

    /** POST /pro/stations/{id}/photos */
    public function uploadPhotos(Request $request, int $id): JsonResponse
    {
        $station = $this->myStation($request, $id);

        $request->validate(['photos' => 'required|array|max:6', 'photos.*' => 'image|max:3072']);

        if ($station->subscription_type === 'free') {
            return response()->json(['success' => false, 'message' => 'Galerie photos disponible en plan Pro.'], 403);
        }

        $photos = $station->photos ?? [];

        foreach ($request->file('photos') as $file) {
            $path     = $file->store("stations/{$station->id}/photos", 'public');
            $photos[] = Storage::url($path);
        }

        $station->update(['photos' => $photos]);

        return response()->json(['success' => true, 'photos' => $photos]);
    }

    /** DELETE /pro/stations/{id}/photos/{photoIndex} */
    public function deletePhoto(Request $request, int $id, int $photoIndex): JsonResponse
    {
        $station = $this->myStation($request, $id);
        $photos  = $station->photos ?? [];

        if (!isset($photos[$photoIndex])) {
            return response()->json(['success' => false, 'message' => 'Photo introuvable.'], 404);
        }

        $path = str_replace(Storage::url(''), '', $photos[$photoIndex]);
        Storage::disk('public')->delete($path);
        array_splice($photos, $photoIndex, 1);
        $station->update(['photos' => array_values($photos)]);

        return response()->json(['success' => true, 'message' => 'Photo supprimée.']);
    }

    /** PUT /pro/stations/{id}/services */
    public function updateServices(Request $request, int $id): JsonResponse
    {
        $station = $this->myStation($request, $id);

        $request->validate([
            'services'   => 'required|array',
            'services.*' => 'string|in:lavage_auto,gonflage_pneus,boutique,restaurant,toilettes,wifi,atm,parking,gonflage_gratuit,huile_moteur,reparation_rapide',
        ]);

        DB::transaction(function () use ($station, $request) {
            $station->services()->delete();
            foreach ($request->services as $service) {
                $station->services()->create(['service' => $service]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Services mis à jour.', 'data' => $station->services()->pluck('service')]);
    }

    /** PUT /pro/stations/{id}/hours */
    public function updateHours(Request $request, int $id): JsonResponse
    {
        $station = $this->myStation($request, $id);

        $data = $request->validate([
            'opens_at'    => 'nullable|date_format:H:i',
            'closes_at'   => 'nullable|date_format:H:i',
            'is_open_24h' => 'boolean',
        ]);

        $station->update($data);

        return response()->json(['success' => true, 'message' => 'Horaires mis à jour.']);
    }
}
