<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationService;
use App\Models\StationView;
use App\Models\Promotion;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // GET /stations?lat=&lng=&radius=5&city=&verified=1&sort=distance&limit=20
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'      => 'nullable|numeric|between:-90,90',
            'lng'      => 'nullable|numeric|between:-180,180',
            'radius'   => 'nullable|numeric|min:0.1|max:100',
            'city'     => 'nullable|string|max:100',
            'verified' => 'nullable|boolean',
            'sort'     => 'nullable|in:distance,name,views',
            'limit'    => 'nullable|integer|min:1|max:100',
            'page'     => 'nullable|integer|min:1',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 10);
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('stations')
            ->where('is_active', true)
            ->whereNull('deleted_at');

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        if ($lat && $lng) {
            $h = $this->haversine($lat, $lng);
            $query->selectRaw("*, ($h) AS distance")
                  ->havingRaw('distance <= ?', [$radius])
                  ->orderBy('distance');
        } else {
            $query->select('*');
            match ($request->input('sort', 'name')) {
                'views' => $query->orderByDesc('views_count'),
                default => $query->orderBy('name'),
            };
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
    // GET /stations/nearby?lat=&lng=&radius=5
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

        $stations = DB::table('stations')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->selectRaw("*, ($h) AS distance")
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $stations,
            'meta'    => ['lat' => $lat, 'lng' => $lng, 'radius' => $radius, 'unit' => 'km'],
        ]);
    }

    // ─────────────────────────────────────────────
    // CHEAPEST
    // GET /stations/cheapest?fuel_type=essence&lat=&lng=&radius=10
    // ─────────────────────────────────────────────
    public function cheapest(Request $request): JsonResponse
    {
        $request->validate([
            'fuel_type' => 'nullable|string|max:50',
            'lat'       => 'nullable|numeric|between:-90,90',
            'lng'       => 'nullable|numeric|between:-180,180',
            'radius'    => 'nullable|numeric|min:0.1|max:100',
            'limit'     => 'nullable|integer|min:1|max:50',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 10);
        $limit  = $request->input('limit', 10);

        $query = DB::table('stations')
            ->join('fuel_prices', 'fuel_prices.station_id', '=', 'stations.id_station')
            ->where('stations.is_active', true)
            ->whereNull('stations.deleted_at')
            ->select(
                'stations.*',
                'fuel_prices.fuel_type',
                'fuel_prices.price',
                'fuel_prices.updated_at as price_updated_at'
            )
            ->orderBy('fuel_prices.price');

        if ($request->filled('fuel_type')) {
            $query->where('fuel_prices.fuel_type', $request->fuel_type);
        }

        if ($lat && $lng) {
            $h = $this->haversine($lat, $lng, 'stations');
            $query->selectRaw("stations.*, fuel_prices.fuel_type, fuel_prices.price, ($h) AS distance")
                  ->havingRaw('distance <= ?', [$radius]);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->limit($limit)->get(),
        ]);
    }

    // ─────────────────────────────────────────────
    // VERIFIED
    // GET /stations/verified
    // ─────────────────────────────────────────────
    public function verified(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('stations')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereNull('deleted_at')
            ->orderBy('name');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // REGISTER — Inscription partenaire station
    // POST /stations/register
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
            'type'       => 'station',
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
    // SHOW — Détail d'une station
    // GET /stations/{id}
    // ─────────────────────────────────────────────
    public function show(Request $request, int $id): JsonResponse
    {
        $station = DB::table('stations')
            ->where('id_station', $id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        DB::table('stations')->where('id_station', $id)->increment('views_count');

        DB::table('station_views')->insert([
            'station_id' => $id,
            'user_id'    => $request->user()?->id_user_carbu,
            'ip_address' => $request->ip(),
            'action'     => 'view_profile',
            'viewed_at'  => now(),
        ]);

        $services    = DB::table('station_services')->where('station_id', $id)->get();
        $fuelPrices  = DB::table('fuel_prices')->where('station_id', $id)->orderBy('fuel_type')->get();

        return response()->json([
            'success' => true,
            'data'    => array_merge((array) $station, [
                'services'    => $services,
                'fuel_prices' => $fuelPrices,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // PRICES
    // GET /stations/{id}/prices
    // ─────────────────────────────────────────────
    public function prices(int $id): JsonResponse
    {
        $station = DB::table('stations')
            ->where('id_station', $id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $prices = DB::table('fuel_prices')->where('station_id', $id)->orderBy('fuel_type')->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'station_id'   => $id,
                'station_name' => $station->name,
                'prices'       => $prices,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // PROMOTIONS
    // GET /stations/{id}/promotions
    // ─────────────────────────────────────────────
    public function promotions(int $id): JsonResponse
    {
        if (!$this->stationExists($id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $today = now()->toDateString();

        $promotions = DB::table('promotions')
            ->where('promotable_type', 'App\Models\Station')
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
    // GET /stations/{id}/reviews
    // ─────────────────────────────────────────────
    public function reviews(Request $request, int $id): JsonResponse
    {
        if (!$this->stationExists($id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $limit = $request->input('limit', 10);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('reviews')
            ->where('reviewable_type', 'station')
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
    // SERVICES
    // GET /stations/{id}/services
    // ─────────────────────────────────────────────
    public function services(int $id): JsonResponse
    {
        if (!$this->stationExists($id)) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $services = DB::table('station_services')->where('station_id', $id)->get();

        return response()->json(['success' => true, 'data' => $services]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function stationExists(int $id): bool
    {
        return DB::table('stations')
            ->where('id_station', $id)
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
