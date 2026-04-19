<?php

namespace App\Http\Controllers\Api\Pub;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Toutes les promos actives autour de moi
    // GET /promotions?lat=&lng=&radius=5&limit=20
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
            'limit'  => 'nullable|integer|min:1|max:100',
            'page'   => 'nullable|integer|min:1',
        ]);

        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 5);
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));
        $today  = now()->toDateString();

        $query = DB::table('promotions')
            ->where('is_active', true)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        // Filtrage géographique : on restreint aux promotables dans le rayon
        if ($lat && $lng) {
            $h = $this->haversine($lat, $lng);

            $nearbyStationIds = DB::table('stations')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->selectRaw("id_station, ($h) AS distance")
                ->havingRaw('distance <= ?', [$radius])
                ->pluck('id_station');

            $nearbyGarageIds = DB::table('garages')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->selectRaw("id_garage, ($h) AS distance")
                ->havingRaw('distance <= ?', [$radius])
                ->pluck('id_garage');

            $query->where(function ($q) use ($nearbyStationIds, $nearbyGarageIds) {
                $q->where(function ($q2) use ($nearbyStationIds) {
                    $q2->where('promotable_type', 'App\Models\Station')
                       ->whereIn('promotable_id', $nearbyStationIds);
                })->orWhere(function ($q2) use ($nearbyGarageIds) {
                    $q2->where('promotable_type', 'App\Models\Garage')
                       ->whereIn('promotable_id', $nearbyGarageIds);
                });
            });
        }

        $total  = (clone $query)->count();
        $promos = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        // Enrichir chaque promo avec les infos du promotable (station ou garage)
        $promos = $promos->map(function ($promo) {
            $promo = (array) $promo;

            if ($promo['promotable_type'] === 'App\Models\Station') {
                $promo['promotable'] = DB::table('stations')
                    ->where('id_station', $promo['promotable_id'])
                    ->first(['id_station', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url']);
            } elseif ($promo['promotable_type'] === 'App\Models\Garage') {
                $promo['promotable'] = DB::table('garages')
                    ->where('id_garage', $promo['promotable_id'])
                    ->first(['id_garage', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url']);
            } else {
                $promo['promotable'] = null;
            }

            return $promo;
        });

        return response()->json([
            'success' => true,
            'data'    => $promos,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail d'une promotion
    // GET /promotions/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $promo = DB::table('promotions')
            ->where('id_promotion', $id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Promotion introuvable.'], 404);
        }

        $promo = (array) $promo;

        if ($promo['promotable_type'] === 'App\Models\Station') {
            $promo['promotable'] = DB::table('stations')
                ->where('id_station', $promo['promotable_id'])
                ->first(['id_station', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url', 'phone', 'whatsapp']);
        } elseif ($promo['promotable_type'] === 'App\Models\Garage') {
            $promo['promotable'] = DB::table('garages')
                ->where('id_garage', $promo['promotable_id'])
                ->first(['id_garage', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url', 'phone', 'whatsapp']);
        } else {
            $promo['promotable'] = null;
        }

        return response()->json(['success' => true, 'data' => $promo]);
    }

    // ─────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────
    private function haversine(float $lat, float $lng): string
    {
        return "(6371 * ACOS(LEAST(1,
            COS(RADIANS($lat)) * COS(RADIANS(latitude)) *
            COS(RADIANS(longitude) - RADIANS($lng)) +
            SIN(RADIANS($lat)) * SIN(RADIANS(latitude))
        )))";
    }
}
