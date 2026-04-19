<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProGarageController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Mes garages
    // GET /pro/subscription/garages
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id_gara_owner;

        $garageIds = DB::table('garage_owner_garage')
            ->where('garage_owner_id', $ownerId)
            ->pluck('garage_id');

        $garages = DB::table('garages')
            ->whereIn('id_garage', $garageIds)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $garages]);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'un garage
    // GET /pro/subscription/garages/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $garage = $this->findOwnerGarage($request->user()->id_gara_owner, $id);

        if (!$garage) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $services   = DB::table('garage_services')->where('garage_id', $id)->get();
        $promotions = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Garage')
            ->where('promotable_id', $id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        // Stats mois en cours
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $viewsMonth = DB::table('garage_views')
            ->where('garage_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->count();

        $callsMonth = DB::table('garage_views')
            ->where('garage_id', $id)
            ->where('action', 'call')
            ->where('viewed_at', '>=', $monthStart)
            ->count();

        // Note moyenne (avis approuvés)
        $ratingStats = DB::table('reviews')
            ->where('reviewable_type', 'App\Models\Garage')
            ->where('reviewable_id', $id)
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $garage, [
                'services'    => $services,
                'promotions'  => $promotions,
                'stats_month' => ['views' => $viewsMonth, 'calls' => $callsMonth],
                'rating_live' => [
                    'avg'   => round($ratingStats->avg ?? 0, 2),
                    'total' => $ratingStats->total ?? 0,
                ],
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier les infos
    // PUT /pro/subscription/garages/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerGarage($request->user()->id_gara_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:150',
            'type'        => 'sometimes|in:garage_general,centre_vidange,lavage_auto,pneus,batterie,climatisation,electricite_auto,depannage,carrosserie,vitrage',
            'address'     => 'sometimes|string|max:255',
            'city'        => 'sometimes|string|max:100',
            'phone'       => 'sometimes|nullable|string|max:20',
            'whatsapp'    => 'sometimes|nullable|string|max:20',
            'description' => 'sometimes|nullable|string|max:2000',
        ]);

        DB::table('garages')
            ->where('id_garage', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('garages')->where('id_garage', $id)->first();

        return response()->json(['success' => true, 'message' => 'Garage mis à jour.', 'data' => $updated]);
    }

    // ─────────────────────────────────────────────
    // UPLOAD PHOTOS
    // POST /pro/subscription/garages/{id}/photos
    // ─────────────────────────────────────────────
    public function uploadPhotos(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerGarage($request->user()->id_gara_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $request->validate([
            'photos'   => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $garage       = DB::table('garages')->where('id_garage', $id)->first(['photos']);
        $existingUrls = $garage->photos ? json_decode($garage->photos, true) : [];

        $newUrls = [];
        foreach ($request->file('photos') as $file) {
            $path      = $file->store("garages/{$id}/photos", 'public');
            $newUrls[] = '/storage/' . $path;
        }

        $allUrls = array_merge($existingUrls, $newUrls);

        DB::table('garages')
            ->where('id_garage', $id)
            ->update(['photos' => json_encode($allUrls), 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => count($newUrls) . ' photo(s) ajoutée(s).',
            'data'    => ['photos' => $allUrls],
        ]);
    }

    // ─────────────────────────────────────────────
    // DELETE PHOTO
    // DELETE /pro/subscription/garages/{id}/photos/{photoIndex}
    // ─────────────────────────────────────────────
    public function deletePhoto(Request $request, int $id, int $photoIndex): JsonResponse
    {
        if (!$this->findOwnerGarage($request->user()->id_gara_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $garage = DB::table('garages')->where('id_garage', $id)->first(['photos']);
        $photos = $garage->photos ? json_decode($garage->photos, true) : [];

        if (!isset($photos[$photoIndex])) {
            return response()->json(['success' => false, 'message' => 'Photo introuvable.'], 404);
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $photos[$photoIndex]));
        array_splice($photos, $photoIndex, 1);

        DB::table('garages')
            ->where('id_garage', $id)
            ->update(['photos' => json_encode(array_values($photos)), 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Photo supprimée.', 'data' => ['photos' => $photos]]);
    }

    // ─────────────────────────────────────────────
    // UPDATE SERVICES
    // PUT /pro/subscription/garages/{id}/services
    // Body: { services: [{ service: "vidange", price_range: "5000 - 15000 FCFA" }, ...] }
    // ─────────────────────────────────────────────
    public function updateServices(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerGarage($request->user()->id_gara_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $validated = $request->validate([
            'services'              => 'required|array',
            'services.*.service'    => 'required|string|in:vidange,freins,pneus,batterie,climatisation,electricite,carrosserie,vitrage,courroie_distribution,amortisseurs,echappement,revision_complete,diagnostic_electronique,depannage_route,remorquage,lavage_interieur,lavage_exterieur,polissage',
            'services.*.price_range'=> 'nullable|string|max:100',
        ]);

        DB::table('garage_services')->where('garage_id', $id)->delete();

        $rows = array_map(fn ($svc) => [
            'garage_id'   => $id,
            'service'     => $svc['service'],
            'price_range' => $svc['price_range'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ], $validated['services']);

        DB::table('garage_services')->insert($rows);

        $services = DB::table('garage_services')->where('garage_id', $id)->get();

        return response()->json(['success' => true, 'message' => 'Services mis à jour.', 'data' => $services]);
    }

    // ─────────────────────────────────────────────
    // UPDATE HOURS
    // PUT /pro/subscription/garages/{id}/hours
    // ─────────────────────────────────────────────
    public function updateHours(Request $request, int $id): JsonResponse
    {
        if (!$this->findOwnerGarage($request->user()->id_gara_owner, $id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
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

        DB::table('garages')
            ->where('id_garage', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json(['success' => true, 'message' => 'Horaires mis à jour.']);
    }

    // ─────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────
    private function findOwnerGarage(int $ownerId, int $garageId): ?object
    {
        $linked = DB::table('garage_owner_garage')
            ->where('garage_owner_id', $ownerId)
            ->where('garage_id', $garageId)
            ->exists();

        if (!$linked) return null;

        return DB::table('garages')
            ->where('id_garage', $garageId)
            ->whereNull('deleted_at')
            ->first();
    }
}
