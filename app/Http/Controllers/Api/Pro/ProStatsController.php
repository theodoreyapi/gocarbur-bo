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
    // ─────────────────────────────────────────────
    // OVERVIEW — Vue d'ensemble (vues, clics, appels ce mois)
    // GET /pro/subscription/stats/overview
    // ─────────────────────────────────────────────
    public function overview(Request $request): JsonResponse
    {
        $user      = $request->user();
        $isStation = isset($user->id_station_owner);
        $monthStart = now()->startOfMonth()->toDateTimeString();

        if ($isStation) {
            $entityIds = DB::table('station_owner_station')
                ->where('station_owner_id', $user->id_station_owner)
                ->pluck('station_id');

            $stats = DB::table('station_views')
                ->whereIn('station_id', $entityIds)
                ->where('viewed_at', '>=', $monthStart)
                ->selectRaw("
                    COUNT(*) as total_views,
                    SUM(action = 'call') as total_calls,
                    SUM(action = 'whatsapp') as total_whatsapp,
                    SUM(action = 'itinerary') as total_itinerary
                ")
                ->first();

            $totalPromos = DB::table('promotions')
                ->where('promotable_type', 'App\Models\Station')
                ->whereIn('promotable_id', $entityIds)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->count();

            $avgRating = null; // Les stations n'ont pas de note globale dans ce schéma

        } else {
            $entityIds = DB::table('garage_owner_garage')
                ->where('garage_owner_id', $user->id_gara_owner)
                ->pluck('garage_id');

            $stats = DB::table('garage_views')
                ->whereIn('garage_id', $entityIds)
                ->where('viewed_at', '>=', $monthStart)
                ->selectRaw("
                    COUNT(*) as total_views,
                    SUM(action = 'call') as total_calls,
                    SUM(action = 'whatsapp') as total_whatsapp,
                    SUM(action = 'itinerary') as total_itinerary
                ")
                ->first();

            $totalPromos = DB::table('promotions')
                ->where('promotable_type', 'App\Models\Garage')
                ->whereIn('promotable_id', $entityIds)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->count();

            $ratingRow = DB::table('reviews')
                ->where('reviewable_type', 'App\Models\Garage')
                ->whereIn('reviewable_id', $entityIds)
                ->where('is_approved', true)
                ->whereNull('deleted_at')
                ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
                ->first();

            $avgRating = ['avg' => round($ratingRow->avg ?? 0, 2), 'total' => $ratingRow->total ?? 0];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'period'         => now()->format('Y-m'),
                'total_views'    => (int) ($stats->total_views ?? 0),
                'total_calls'    => (int) ($stats->total_calls ?? 0),
                'total_whatsapp' => (int) ($stats->total_whatsapp ?? 0),
                'total_itinerary' => (int) ($stats->total_itinerary ?? 0),
                'active_promos'  => $totalPromos,
                'avg_rating'     => $avgRating,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // STATION — Stats d'une station spécifique
    // GET /pro/subscription/stats/stations/{id}
    // ─────────────────────────────────────────────
    public function station(Request $request, int $id): JsonResponse
    {
        $ownerId = $request->user()->id_station_owner;

        if (!DB::table('station_owner_station')->where('station_owner_id', $ownerId)->where('station_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Station introuvable.'], 404);
        }

        $monthStart = now()->startOfMonth()->toDateTimeString();

        // Vues par action ce mois
        $byAction = DB::table('station_views')
            ->where('station_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->selectRaw("action, COUNT(*) as count")
            ->groupBy('action')
            ->get()
            ->keyBy('action');

        // Vues par jour (30 derniers jours)
        $byDay = DB::table('station_views')
            ->where('station_id', $id)
            ->where('viewed_at', '>=', now()->subDays(30)->toDateTimeString())
            ->selectRaw("DATE(viewed_at) as day, COUNT(*) as count")
            ->groupByRaw("DATE(viewed_at)")
            ->orderBy('day')
            ->get();

        // Prix actuels
        $prices = DB::table('fuel_prices')
            ->where('station_id', $id)
            ->orderBy('fuel_type')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'by_action' => $byAction,
                'by_day'    => $byDay,
                'prices'    => $prices,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GARAGE — Stats d'un garage spécifique
    // GET /pro/subscription/stats/garages/{id}
    // ─────────────────────────────────────────────
    public function garage(Request $request, int $id): JsonResponse
    {
        $ownerId = $request->user()->id_gara_owner;

        if (!DB::table('garage_owner_garage')->where('garage_owner_id', $ownerId)->where('garage_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Garage introuvable.'], 404);
        }

        $monthStart = now()->startOfMonth()->toDateTimeString();

        $byAction = DB::table('garage_views')
            ->where('garage_id', $id)
            ->where('viewed_at', '>=', $monthStart)
            ->selectRaw("action, COUNT(*) as count")
            ->groupBy('action')
            ->get()
            ->keyBy('action');

        $byDay = DB::table('garage_views')
            ->where('garage_id', $id)
            ->where('viewed_at', '>=', now()->subDays(30)->toDateTimeString())
            ->selectRaw("DATE(viewed_at) as day, COUNT(*) as count")
            ->groupByRaw("DATE(viewed_at)")
            ->orderBy('day')
            ->get();

        $ratingRow = DB::table('reviews')
            ->where('reviewable_type', 'App\Models\Garage')
            ->where('reviewable_id', $id)
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'by_action'  => $byAction,
                'by_day'     => $byDay,
                'avg_rating' => round($ratingRow->avg ?? 0, 2),
                'total_reviews' => $ratingRow->total ?? 0,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // ADVANCED — Stats avancées (vues par jour/semaine/mois)
    // GET /pro/subscription/stats/advanced?period=week
    // ─────────────────────────────────────────────
    public function advanced(Request $request): JsonResponse
    {
        $request->validate(['period' => 'nullable|in:week,month,year']);

        $user      = $request->user();
        $isStation = isset($user->id_station_owner);
        $period    = $request->input('period', 'month');

        $since = match ($period) {
            'week'  => now()->subWeek(),
            'year'  => now()->subYear(),
            default => now()->subMonth(),
        };

        $groupFormat = match ($period) {
            'year'  => '%Y-%m',
            default => '%Y-%m-%d',
        };

        if ($isStation) {
            $entityIds  = DB::table('station_owner_station')->where('station_owner_id', $user->id_station_owner)->pluck('station_id');
            $viewTable  = 'station_views';
            $entityFk   = 'station_id';
        } else {
            $entityIds  = DB::table('garage_owner_garage')->where('garage_owner_id', $user->id_gara_owner)->pluck('garage_id');
            $viewTable  = 'garage_views';
            $entityFk   = 'garage_id';
        }

        $data = DB::table($viewTable)
            ->whereIn($entityFk, $entityIds)
            ->where('viewed_at', '>=', $since)
            ->selectRaw("DATE_FORMAT(viewed_at, '{$groupFormat}') as period, action, COUNT(*) as count")
            ->groupByRaw("DATE_FORMAT(viewed_at, '{$groupFormat}'), action")
            ->orderBy('period')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ─────────────────────────────────────────────
    // REVIEWS — Avis reçus
    // GET /pro/subscription/stats/reviews?approved=1
    // ─────────────────────────────────────────────
    public function reviews(Request $request): JsonResponse
    {
        $user      = $request->user();
        $isStation = isset($user->id_station_owner);
        $limit     = $request->input('limit', 20);
        $page      = max(1, (int) $request->input('page', 1));

        if ($isStation) {
            $entityIds       = DB::table('station_owner_station')->where('station_owner_id', $user->id_station_owner)->pluck('station_id');
            $reviewableType  = 'App\Models\Station';
        } else {
            $entityIds       = DB::table('garage_owner_garage')->where('garage_owner_id', $user->id_gara_owner)->pluck('garage_id');
            $reviewableType  = 'App\Models\Garage';
        }

        $query = DB::table('reviews')
            ->where('reviewable_type', $reviewableType)
            ->whereIn('reviewable_id', $entityIds)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        if ($request->has('approved')) {
            $query->where('is_approved', $request->boolean('approved'));
        }

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }
}
