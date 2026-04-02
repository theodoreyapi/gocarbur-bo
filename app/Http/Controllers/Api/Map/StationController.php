<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    /**
     * GET /stations
     * Filtres: lat, lng, radius, fuel_type, city, verified, sort, subscription_type
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'       => 'nullable|numeric|between:-90,90',
            'lng'       => 'nullable|numeric|between:-180,180',
            'radius'    => 'nullable|integer|min:1|max:50',
            'fuel_type' => 'nullable|in:essence,gasoil,sans_plomb,super,gpl',
            'city'      => 'nullable|string|max:100',
            'verified'  => 'nullable|boolean',
            'sort'      => 'nullable|in:distance,price_asc,price_desc,name',
        ]);

        $query = Station::active()
            ->with(['fuelPrices', 'services:id,station_id,service'])
            ->withCount('reviews');

        // Filtre géographique
        if ($request->lat && $request->lng) {
            $query->nearby($request->lat, $request->lng, $request->input('radius', 10));
        }

        // Filtres
        if ($request->city)     $query->where('city', 'like', "%{$request->city}%");
        if ($request->verified) $query->verified();

        // Filtre par disponibilité du carburant
        if ($request->fuel_type) {
            $query->whereHas('fuelPrices', fn($q) =>
                $q->where('fuel_type', $request->fuel_type)->where('is_available', true)
            );
        }

        // Tri
        $sort = $request->input('sort', 'distance');
        if ($sort === 'price_asc' && $request->fuel_type) {
            $query->join('fuel_prices', 'stations.id', '=', 'fuel_prices.station_id')
                  ->where('fuel_prices.fuel_type', $request->fuel_type)
                  ->orderBy('fuel_prices.price');
        } elseif ($sort === 'name') {
            $query->orderBy('name');
        }

        // Priorité aux abonnés pro/premium sur la carte
        $query->orderByRaw("FIELD(subscription_type, 'premium', 'pro', 'free')");

        $stations = $query->paginate($request->input('per_page', 20));

        return response()->json(['success' => true, 'data' => $stations]);
    }

    /**
     * GET /stations/nearby
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:50',
        ]);

        $stations = Station::active()
            ->nearby($request->lat, $request->lng, $request->input('radius', 5))
            ->with(['fuelPrices', 'services:id,station_id,service'])
            ->limit(30)
            ->get();

        return response()->json(['success' => true, 'data' => $stations]);
    }

    /**
     * GET /stations/cheapest
     */
    public function cheapest(Request $request): JsonResponse
    {
        $request->validate([
            'fuel_type' => 'required|in:essence,gasoil,sans_plomb,super,gpl',
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
            'radius'    => 'nullable|integer|min:1|max:50',
        ]);

        $query = Station::active()
            ->join('fuel_prices', 'stations.id', '=', 'fuel_prices.station_id')
            ->where('fuel_prices.fuel_type', $request->fuel_type)
            ->where('fuel_prices.is_available', true)
            ->select('stations.*', 'fuel_prices.price', 'fuel_prices.updated_at_price');

        if ($request->lat && $request->lng) {
            $lat = $request->lat;
            $lng = $request->lng;
            $query->selectRaw("
                stations.*,
                fuel_prices.price,
                (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                COS(RADIANS(longitude) - RADIANS(?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<=', $request->input('radius', 10));
        }

        $stations = $query->orderBy('fuel_prices.price')->limit(20)->get();

        return response()->json([
            'success'   => true,
            'fuel_type' => $request->fuel_type,
            'data'      => $stations,
        ]);
    }

    /**
     * GET /stations/verified
     */
    public function verified(Request $request): JsonResponse
    {
        $stations = Station::active()->verified()
            ->with('fuelPrices')
            ->when($request->city, fn($q) => $q->where('city', $request->city))
            ->orderByRaw("FIELD(subscription_type, 'premium', 'pro', 'free')")
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $stations]);
    }

    /**
     * POST /stations/register
     * Inscription partenaire depuis le site web
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'business_name'  => 'required|string|max:150',
            'contact_name'   => 'required|string|max:100',
            'contact_phone'  => 'required|string|max:20',
            'contact_email'  => 'nullable|email',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'message'        => 'nullable|string|max:1000',
        ]);

        $data['type'] = 'station';
        \App\Models\PartnerRequest::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Votre demande a été reçue. Notre équipe vous contactera sous 48h.',
        ], 201);
    }

    /**
     * GET /stations/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $station = Station::active()
            ->with([
                'fuelPrices',
                'services:id,station_id,service',
                'promotions' => fn($q) => $q->active(),
                'reviews'    => fn($q) => $q->with('user:id,name,avatar_url')->latest()->limit(5),
            ])
            ->withCount('reviews')
            ->findOrFail($id);

        // Enregistrer la vue
        StationView::create([
            'station_id' => $station->id,
            'user_id'    => $request->user()?->id,
            'ip_address' => $request->ip(),
            'action'     => 'view_profile',
            'viewed_at'  => now(),
        ]);

        $station->increment('views_count');

        return response()->json(['success' => true, 'data' => $station]);
    }

    /**
     * GET /stations/{id}/prices
     */
    public function prices(int $id): JsonResponse
    {
        $station = Station::active()->findOrFail($id);
        $prices  = $station->fuelPrices()->orderBy('fuel_type')->get();

        return response()->json(['success' => true, 'data' => $prices]);
    }

    /**
     * GET /stations/{id}/promotions
     */
    public function promotions(int $id): JsonResponse
    {
        $station    = Station::active()->findOrFail($id);
        $promotions = $station->promotions()->active()->get();

        return response()->json(['success' => true, 'data' => $promotions]);
    }

    /**
     * GET /stations/{id}/reviews
     */
    public function reviews(Request $request, int $id): JsonResponse
    {
        $station = Station::active()->findOrFail($id);
        $reviews = $station->reviews()
            ->with('user:id,name,avatar_url')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    /**
     * GET /stations/{id}/services
     */
    public function services(int $id): JsonResponse
    {
        $station  = Station::active()->findOrFail($id);
        $services = $station->services()->pluck('service');

        return response()->json(['success' => true, 'data' => $services]);
    }
}
