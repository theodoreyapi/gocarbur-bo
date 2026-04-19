<?php

namespace App\Http\Controllers\Api\Pub;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — Bannières actives filtrées
    // GET /banners?position=home_top&city=Abidjan
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'position' => 'nullable|string|in:home_top,home_middle,map_bottom,stations_list,garages_list,articles_list,splash',
            'city'     => 'nullable|string|max:100',
        ]);

        $today = now()->toDateString();
        $user  = $request->user(); // null si non connecté

        $query = DB::table('banners')
            ->where('is_active', true)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->whereNull('deleted_at')
            ->orderBy('starts_at');

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Ciblage par type d'abonnement
        $subscriptionType = $user->subscription_type ?? 'free';

        $query->where(function ($q) use ($subscriptionType) {
            $q->where('target_type', 'all')
              ->orWhere('target_type', $subscriptionType === 'premium' ? 'premium_users' : 'free_users');
        });

        // Ciblage par ville
        if ($request->filled('city')) {
            $city = $request->city;
            $query->where(function ($q) use ($city) {
                $q->where('target_type', '!=', 'city')
                  ->orWhere('target_city', $city);
            });
        }

        $banners = $query->get();

        return response()->json(['success' => true, 'data' => $banners]);
    }

    // ─────────────────────────────────────────────
    // IMPRESSION — Enregistrer une impression
    // POST /banners/{id}/impression
    // ─────────────────────────────────────────────
    public function impression(int $id): JsonResponse
    {
        $exists = DB::table('banners')->where('id_banner', $id)->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Bannière introuvable.'], 404);
        }

        DB::table('banners')->where('id_banner', $id)->increment('impressions_count');

        return response()->json(['success' => true, 'message' => 'Impression enregistrée.']);
    }

    // ─────────────────────────────────────────────
    // CLICK — Enregistrer un clic
    // POST /banners/{id}/click
    // ─────────────────────────────────────────────
    public function click(int $id): JsonResponse
    {
        $banner = DB::table('banners')->where('id_banner', $id)->first(['id_banner', 'action_url']);

        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Bannière introuvable.'], 404);
        }

        DB::table('banners')->where('id_banner', $id)->increment('clicks_count');

        return response()->json([
            'success'    => true,
            'message'    => 'Clic enregistré.',
            'action_url' => $banner->action_url,
        ]);
    }
}
