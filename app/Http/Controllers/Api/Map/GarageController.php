<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\GarageService;
use App\Models\GarageView;
use App\Models\Promotion;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GarageController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /garages?lat=&lng=&radius=&type=&city=&limit=20
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
            'type'   => 'nullable|string|max:50',
            'city'   => 'nullable|string|max:100',
            'limit'  => 'nullable|integer|min:1|max:100',
            'page'   => 'nullable|integer|min:1',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 10);
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('garages')
            ->where('is_active', true)
            ->whereNull('deleted_at');

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($lat && $lng) {
            $h = $this->haversine($lat, $lng);
            $query->selectRaw("*, ($h) AS distance")
                  ->havingRaw('distance <= ?', [$radius])
                  ->orderBy('distance');
        } else {
            $query->select('*')->orderByDesc('rating');
        }

        $total = (clone $query)->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // NEARBY
    // GET /garages/nearby?lat=&lng=&radius=5
    // ─────────────────────────────────────────────
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'required|numeric|between:-90,90',
            'lng'    => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:50',
            'limit'  => 'nullable|integer|min:1|max:50',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 5);
        $limit  = $request->input('limit', 10);
        $h      = $this->haversine($lat, $lng);

        $garages = DB::table('garages')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->selectRaw("*, ($h) AS distance")
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $garages,
            'meta'    => ['lat' => $lat, 'lng' => $lng, 'radius' => $radius, 'unit' => 'km'],
        ]);
    }

    // ─────────────────────────────────────────────
    // BY TYPE
    // GET /garages/by-type?type=depannage
    // ─────────────────────────────────────────────
    public function byType(Request $request): JsonResponse
    {
        $request->validate([
            'type'  => 'required|string|max:50',
            'limit' => 'nullable|integer|min:1|max:100',
            'page'  => 'nullable|integer|min:1',
        ]);

        $limit = $request->input('limit', 20);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('garages')
            ->where('is_active', true)
            ->where('type', $request->type)
            ->whereNull('deleted_at')
            ->orderByDesc('rating');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // EMERGENCY — Dépanneurs disponibles (urgence)
    // GET /garages/emergency?lat=&lng=&radius=20
    // ─────────────────────────────────────────────
    public function emergency(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 20);

        $query = DB::table('garages')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('type', 'depannage')->orWhere('is_open_24h', true);
            });

        if ($lat && $lng) {
            $h = $this->haversine($lat, $lng);
            $query->selectRaw("*, ($h) AS distance")
                  ->havingRaw('distance <= ?', [$radius])
                  ->orderBy('distance');
        } else {
            $query->select('*')->orderByDesc('rating');
        }

        return response()->json([
            'success' => true,
            'data'    => $query->limit(20)->get(),
        ]);
    }

    // ─────────────────────────────────────────────
    // REGISTER — Inscription partenaire garage
    // POST /garages/register
    // ─────────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'business_name'  => 'required|string|max:150',
            'contact_name'   => 'required|string|max:100',
            'contact_phone'  => 'required|string|max:20',
            'contact_email'  => 'nullable|email',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'message'        => 'nullable|string|max:1000',
        ]);

        $id = DB::table('partner_requests')->insertGetId(array_merge($validated, [
            'type'       => 'garage',
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Demande partenaire envoyée. Nous vous contacterons sous 48h.',
            'data'    => ['id' => $id],
        ], 201);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'un garage
    // GET /garages/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $garage = DB::table('garages')
            ->where('id_garage', $id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$garage) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        DB::table('garages')->where('id_garage', $id)->increment('views_count');

        DB::table('garage_views')->insert([
            'garage_id'  => $id,
            'user_id'    => $request->user()?->id_user_carbu,
            'ip_address' => $request->ip(),
            'action'     => 'view_profile',
            'viewed_at'  => now(),
        ]);

        $services = DB::table('garage_services')->where('garage_id', $id)->get();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $garage, ['services' => $services]),
        ]);
    }

    // ─────────────────────────────────────────────
    // SERVICES
    // GET /garages/{id}/services
    // ─────────────────────────────────────────────
    public function services(int $id): JsonResponse
    {
        if (!$this->garageExists($id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $services = DB::table('garage_services')->where('garage_id', $id)->get();

        return response()->json(['success' => true, 'data' => $services]);
    }

    // ─────────────────────────────────────────────
    // PROMOTIONS
    // GET /garages/{id}/promotions
    // ─────────────────────────────────────────────
    public function promotions(int $id): JsonResponse
    {
        if (!$this->garageExists($id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $today = now()->toDateString();

        $promotions = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Garage')
            ->where('promotable_id', $id)
            ->where('is_active', true)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->whereNull('deleted_at')
            ->orderBy('starts_at')
            ->get();

        return response()->json(['success' => true, 'data' => $promotions]);
    }

    // ─────────────────────────────────────────────
    // REVIEWS
    // GET /garages/{id}/reviews
    // ─────────────────────────────────────────────
    public function reviews(Request $request, int $id): JsonResponse
    {
        if (!$this->garageExists($id)) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $limit = $request->input('limit', 10);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('reviews')
            ->where('reviewable_type', 'garage')
            ->where('reviewable_id', $id)
            ->where('is_approved', true)
            ->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function garageExists(int $id): bool
    {
        return DB::table('garages')
            ->where('id_garage', $id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function haversine(float $lat, float $lng, string $prefix = ''): string
    {
        $latCol = $prefix ? "{$prefix}.latitude"  : 'latitude';
        $lngCol = $prefix ? "{$prefix}.longitude" : 'longitude';

        return "(6371 * ACOS(LEAST(1,
            COS(RADIANS($lat)) * COS(RADIANS($latCol)) *
            COS(RADIANS($lngCol) - RADIANS($lng)) +
            SIN(RADIANS($lat)) * SIN(RADIANS($latCol))
        )))";
    }
}
