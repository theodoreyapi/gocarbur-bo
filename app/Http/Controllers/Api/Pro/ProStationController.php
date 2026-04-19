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
    // ─────────────────────────────────────────────
    // INDEX — Mes stations
    // GET /pro/subscription/stations
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id_station_owner;

        $stationIds = DB::table('station_owner_station')
            ->where('station_owner_id', $ownerId)
            ->pluck('station_id');

        $stations = DB::table('stations')
            ->whereIn('id_station', $stationIds)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $stations]);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'une station
    // GET /pro/subscription/stations/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $station = $this->findOwnerStation($request->user()->id_station_owner, $id);

        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $services   = DB::table('station_services')->where('station_id', $id)->get();
        $prices     = DB::table('fuel_prices')->where('station_id', $id)->orderBy('fuel_type')->get();
        $promotions = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Station')
            ->where('promotable_id', $id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        // Stats rapides du mois en cours
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $viewsMonth = DB::table('station_views')
            ->where('station_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->count();

        $callsMonth = DB::table('station_views')
            ->where('station_id', $id)
            ->where('action', 'call')
            ->where('viewed_at', '>=', $monthStart)
            ->count();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $station, [
                'services'    => $services,
                'prices'      => $prices,
                'promotions'  => $promotions,
                'stats_month' => ['views' => $viewsMonth, 'calls' => $callsMonth],
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier les infos
    // PUT /pro/subscription/stations/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $ownerId = $request->user()->id_station_owner;

        if (!$this->findOwnerStation($ownerId, $id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:150',
            'brand'       => 'sometimes|nullable|string|max:100',
            'address'     => 'sometimes|string|max:255',
            'city'        => 'sometimes|string|max:100',
            'phone'       => 'sometimes|nullable|string|max:20',
            'whatsapp'    => 'sometimes|nullable|string|max:20',
            'description' => 'sometimes|nullable|string|max:2000',
        ]);

        DB::table('stations')
            ->where('id_station', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('stations')->where('id_station', $id)->first();

        return response()->json(['success' => true, 'message' => 'Station mise à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // UPLOAD PHOTOS — Galerie photos
    // POST /pro/subscription/stations/{id}/photos
    // ─────────────────────────────────────────────
    public function uploadPhotos(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerStation($request->user()->id_station_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $request->validate([
            'photos'   => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $station      = DB::table('stations')->where('id_station', $id)->first(['photos']);
        $existingUrls = $station->photos ? json_decode($station->photos, true) : [];

        $newUrls = [];
        foreach ($request->file('photos') as $file) {
            $path      = $file->store("stations/{$id}/photos", 'public');
            $newUrls[] = '/storage/' . $path;
        }

        $allUrls = array_merge($existingUrls, $newUrls);

        DB::table('stations')
            ->where('id_station', $id)
            ->update(['photos' => json_encode($allUrls), 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => count($newUrls) . ' photo(s) ajoutée(s).',
            'data'    => ['photos' => $allUrls],
        ]);
    }

    // ─────────────────────────────────────────────
    // DELETE PHOTO — Supprimer une photo
    // DELETE /pro/subscription/stations/{id}/photos/{photoIndex}
    // ─────────────────────────────────────────────
    public function deletePhoto(Request $request, int $id, int $photoIndex): JsonResponse
    {
        if (!$this->findOwnerStation($request->user()->id_station_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $station = DB::table('stations')->where('id_station', $id)->first(['photos']);
        $photos  = $station->photos ? json_decode($station->photos, true) : [];

        if (!isset($photos[$photoIndex])) {
            return response()->json(['success' => false, 'message' => 'Photo introuvable.'], 404);
        }

        // Supprimer le fichier
        $filePath = str_replace('/storage/', '', $photos[$photoIndex]);
        Storage::disk('public')->delete($filePath);

        array_splice($photos, $photoIndex, 1);

        DB::table('stations')
            ->where('id_station', $id)
            ->update(['photos' => json_encode(array_values($photos)), 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Photo supprimée.', 'data' => ['photos' => $photos]]);
    }

    // ─────────────────────────────────────────────
    // UPDATE SERVICES — Modifier les services
    // PUT /pro/subscription/stations/{id}/services
    // Body: { services: ["lavage_auto", "boutique", ...] }
    // ─────────────────────────────────────────────
    public function updateServices(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerStation($request->user()->id_station_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $validated = $request->validate([
            'services'   => 'required|array',
            'services.*' => 'string|in:lavage_auto,gonflage_pneus,boutique,restaurant,toilettes,wifi,atm,parking,gonflage_gratuit,huile_moteur,reparation_rapide',
        ]);

        // Remplacer tous les services existants
        DB::table('station_services')->where('station_id', $id)->delete();

        $rows = array_map(fn ($svc) => [
            'station_id'  => $id,
            'service'     => $svc,
            'created_at'  => now(),
            'updated_at'  => now(),
        ], array_unique($validated['services']));

        DB::table('station_services')->insert($rows);

        $services = DB::table('station_services')->where('station_id', $id)->get();

        return response()->json(['success' => true, 'message' => 'Services mis à jour.', 'data' => $services]);
    }

    // ─────────────────────────────────────────────
    // UPDATE HOURS — Modifier les horaires
    // PUT /pro/subscription/stations/{id}/hours
    // Body: { opens_at: "07:00", closes_at: "22:00", is_open_24h: false }
    // ─────────────────────────────────────────────
    public function updateHours(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerStation($request->user()->id_station_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $validated = $request->validate([
            'opens_at'    => 'sometimes|nullable|date_format:H:i',
            'closes_at'   => 'sometimes|nullable|date_format:H:i',
            'is_open_24h' => 'sometimes|boolean',
        ]);

        if (!empty($validated['is_open_24h'])) {
            $validated['opens_at']  = null;
            $validated['closes_at'] = null;
        }

        DB::table('stations')
            ->where('id_station', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json(['success' => true, 'message' => 'Horaires mis à jour.']);
    }

    // ─────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────
    private function findOwnerStation(int $ownerId, int $stationId): ?object
    {
        $linked = DB::table('station_owner_station')
            ->where('station_owner_id', $ownerId)
            ->where('station_id', $stationId)
            ->exists();

        if (!$linked) return null;

        return DB::table('stations')
            ->where('id_station', $stationId)
            ->whereNull('deleted_at')
            ->first();
    }
}
