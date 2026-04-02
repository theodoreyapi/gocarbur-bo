<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\GarageView;
use App\Models\Review;
use App\Models\StationView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProStatsController extends Controller
{
    /** GET /pro/stats/overview */
    public function overview(Request $request): JsonResponse
    {
        $owner    = $request->user();
        $stations = $owner->stations()->pluck('id');
        $garages  = method_exists($owner, 'garages') ? $owner->garages()->pluck('id') : collect();

        $month = now()->startOfMonth();

        $stationViews = StationView::whereIn('station_id', $stations)
            ->where('viewed_at', '>=', $month)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action');

        $garageViews = GarageView::whereIn('garage_id', $garages)
            ->where('viewed_at', '>=', $month)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action');

        return response()->json([
            'success' => true,
            'data'    => [
                'period'   => now()->format('F Y'),
                'stations' => [
                    'count'        => $stations->count(),
                    'views'        => $stationViews->get('view_profile', 0),
                    'map_views'    => $stationViews->get('view_map', 0),
                    'calls'        => $stationViews->get('call', 0),
                    'whatsapp'     => $stationViews->get('whatsapp', 0),
                    'itineraries'  => $stationViews->get('itinerary', 0),
                ],
                'garages'  => [
                    'count'        => $garages->count(),
                    'views'        => $garageViews->get('view_profile', 0),
                    'calls'        => $garageViews->get('call', 0),
                    'whatsapp'     => $garageViews->get('whatsapp', 0),
                    'itineraries'  => $garageViews->get('itinerary', 0),
                ],
            ],
        ]);
    }

    /** GET /pro/stats/stations/{id} */
    public function station(Request $request, int $id): JsonResponse
    {
        $station = $request->user()->stations()->findOrFail($id);

        $daily = StationView::where('station_id', $station->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(viewed_at) as date, action, COUNT(*) as count')
            ->groupByRaw('DATE(viewed_at), action')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return response()->json([
            'success' => true,
            'data'    => [
                'station'    => $station->only(['id','name','views_count','is_verified','subscription_type']),
                'last_30_days' => $daily,
            ],
        ]);
    }

    /** GET /pro/stats/garages/{id} */
    public function garage(Request $request, int $id): JsonResponse
    {
        $garage = $request->user()->garages()->findOrFail($id);

        $daily = GarageView::where('garage_id', $garage->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(viewed_at) as date, action, COUNT(*) as count')
            ->groupByRaw('DATE(viewed_at), action')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return response()->json([
            'success' => true,
            'data'    => [
                'garage'      => $garage->only(['id','name','views_count','rating','rating_count','subscription_type']),
                'last_30_days'=> $daily,
            ],
        ]);
    }

    /** GET /pro/stats/advanced (Premium uniquement) */
    public function advanced(Request $request): JsonResponse
    {
        $owner    = $request->user();
        $stations = $owner->stations()->pluck('id');

        $weekly = StationView::whereIn('station_id', $stations)
            ->where('viewed_at', '>=', now()->subWeeks(12))
            ->selectRaw('YEARWEEK(viewed_at) as week, COUNT(*) as views, SUM(action="call") as calls')
            ->groupByRaw('YEARWEEK(viewed_at)')
            ->orderBy('week')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ['weekly_stats' => $weekly],
        ]);
    }

    /** GET /pro/stats/reviews */
    public function reviews(Request $request): JsonResponse
    {
        $owner    = $request->user();
        $stations = $owner->stations()->pluck('id');

        $reviews = Review::where('reviewable_type', \App\Models\Station::class)
            ->whereIn('reviewable_id', $stations)
            ->with(['user:id,name,avatar_url', 'reviewable:id,name'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $avg = Review::where('reviewable_type', \App\Models\Station::class)
            ->whereIn('reviewable_id', $stations)
            ->where('is_approved', true)
            ->avg('rating');

        return response()->json([
            'success' => true,
            'data'    => ['average_rating' => round($avg, 2), 'reviews' => $reviews],
        ]);
    }
}
