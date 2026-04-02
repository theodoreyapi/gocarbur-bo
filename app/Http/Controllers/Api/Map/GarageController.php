<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\GarageView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GarageController extends Controller
{
    /**
     * GET /garages
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'nullable|numeric',
            'lng'    => 'nullable|numeric',
            'radius' => 'nullable|integer|min:1|max:50',
            'type'   => 'nullable|string',
            'city'   => 'nullable|string',
        ]);

        $query = Garage::active()
            ->with('services:id,garage_id,service,price_range')
            ->withCount('reviews');

        if ($request->lat && $request->lng) {
            $query->nearby($request->lat, $request->lng, $request->input('radius', 10));
        }

        if ($request->type) $query->where('type', $request->type);
        if ($request->city) $query->where('city', 'like', "%{$request->city}%");
        if ($request->verified) $query->verified();

        // Filtre par service spécifique
        if ($request->service) {
            $query->whereHas('services', fn($q) => $q->where('service', $request->service));
        }

        $query->orderByRaw("FIELD(subscription_type, 'premium', 'pro', 'free')")
              ->orderByDesc('rating');

        $garages = $query->paginate($request->input('per_page', 20));

        return response()->json(['success' => true, 'data' => $garages]);
    }

    /**
     * GET /garages/nearby
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:50',
        ]);

        $garages = Garage::active()
            ->nearby($request->lat, $request->lng, $request->input('radius', 5))
            ->with('services:id,garage_id,service')
            ->limit(30)
            ->get();

        return response()->json(['success' => true, 'data' => $garages]);
    }

    /**
     * GET /garages/by-type?type=depannage
     */
    public function byType(Request $request): JsonResponse
    {
        $request->validate(['type' => 'required|string']);

        $garages = Garage::active()
            ->where('type', $request->type)
            ->with('services:id,garage_id,service')
            ->when($request->lat && $request->lng, fn($q) =>
                $q->nearby($request->lat, $request->lng, $request->input('radius', 20))
            )
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $garages]);
    }

    /**
     * GET /garages/emergency
     * Dépanneurs ouverts en ce moment
     */
    public function emergency(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $now     = now()->format('H:i:s');
        $garages = Garage::active()
            ->where('type', 'depannage')
            ->where(fn($q) =>
                $q->where('is_open_24h', true)
                  ->orWhere(fn($q2) =>
                      $q2->where('opens_at', '<=', $now)->where('closes_at', '>=', $now)
                  )
            )
            ->nearby($request->lat, $request->lng, 30)
            ->with('services:id,garage_id,service')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $garages]);
    }

    /**
     * POST /garages/register
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:150',
            'contact_name'  => 'required|string|max:100',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'message'       => 'nullable|string|max:1000',
        ]);

        $data['type'] = 'garage';
        \App\Models\PartnerRequest::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Votre demande a été reçue. Notre équipe vous contactera sous 48h.',
        ], 201);
    }

    /**
     * GET /garages/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $garage = Garage::active()
            ->with([
                'services:id,garage_id,service,price_range',
                'promotions' => fn($q) => $q->active(),
                'reviews'    => fn($q) => $q->with('user:id,name,avatar_url')->latest()->limit(5),
            ])
            ->withCount('reviews')
            ->findOrFail($id);

        GarageView::create([
            'garage_id'  => $garage->id,
            'user_id'    => $request->user()?->id,
            'ip_address' => $request->ip(),
            'action'     => 'view_profile',
            'viewed_at'  => now(),
        ]);

        $garage->increment('views_count');

        return response()->json(['success' => true, 'data' => $garage]);
    }

    /**
     * GET /garages/{id}/services
     */
    public function services(int $id): JsonResponse
    {
        $garage   = Garage::active()->findOrFail($id);
        $services = $garage->services()->get();

        return response()->json(['success' => true, 'data' => $services]);
    }

    /**
     * GET /garages/{id}/promotions
     */
    public function promotions(int $id): JsonResponse
    {
        $garage     = Garage::active()->findOrFail($id);
        $promotions = $garage->promotions()->active()->get();

        return response()->json(['success' => true, 'data' => $promotions]);
    }

    /**
     * GET /garages/{id}/reviews
     */
    public function reviews(Request $request, int $id): JsonResponse
    {
        $garage  = Garage::active()->findOrFail($id);
        $reviews = $garage->reviews()
            ->with('user:id,name,avatar_url')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $reviews]);
    }
}
