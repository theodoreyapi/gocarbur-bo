<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    // Colonnes sûres à retourner (on exclut admin_id par sécurité)
    private const COLUMNS = [
        'id_article', 'title', 'slug', 'excerpt', 'content',
        'cover_image_url', 'category', 'is_sponsored', 'sponsor_name',
        'sponsor_logo_url', 'sponsor_url', 'published_at',
        'views_count', 'read_time_minutes', 'tags',
    ];

    // ─────────────────────────────────────────────
    // INDEX
    // GET /articles?category=entretien_auto&sponsored=1&limit=10&page=1
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category'  => 'nullable|string|in:entretien_auto,economie_carburant,conduite_securite,documents_admin,astuces_mecaniques,videos_conseils,actualites,legislation',
            'sponsored' => 'nullable|boolean',
            'limit'     => 'nullable|integer|min:1|max:100',
            'page'      => 'nullable|integer|min:1',
        ]);

        $limit = $request->input('limit', 10);
        $page  = max(1, (int) $request->input('page', 1));

        $query = DB::table('articles')
            ->select(self::COLUMNS)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('sponsored')) {
            $query->where('is_sponsored', true);
        }

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get()
            ->map(fn ($a) => $this->castArticle($a));

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    // ─────────────────────────────────────────────
    // FEATURED — Articles à la une / sponsorisés
    // GET /articles/featured
    // ─────────────────────────────────────────────
    public function featured(): JsonResponse
    {
        $articles = DB::table('articles')
            ->select(self::COLUMNS)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('is_sponsored', true))
            ->orderByDesc('published_at')
            ->limit(10)
            ->get()
            ->map(fn ($a) => $this->castArticle($a));

        return response()->json(['success' => true, 'data' => $articles]);
    }

    // ─────────────────────────────────────────────
    // TRENDING — Articles les plus consultés
    // GET /articles/trending
    // ─────────────────────────────────────────────
    public function trending(): JsonResponse
    {
        $articles = DB::table('articles')
            ->select(self::COLUMNS)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get()
            ->map(fn ($a) => $this->castArticle($a));

        return response()->json(['success' => true, 'data' => $articles]);
    }

    // ─────────────────────────────────────────────
    // CATEGORIES — Liste des catégories avec compteur
    // GET /articles/categories
    // ─────────────────────────────────────────────
    public function categories(): JsonResponse
    {
        $categories = DB::table('articles')
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        return response()->json(['success' => true, 'data' => $categories]);
    }

    // ─────────────────────────────────────────────
    // SHOW — Détail (par id ou slug)
    // GET /articles/{idOrSlug}
    // ─────────────────────────────────────────────
    public function show(string $idOrSlug): JsonResponse
    {
        $query = DB::table('articles')
            ->select(self::COLUMNS)
            ->where('is_published', true)
            ->whereNull('deleted_at');

        $article = is_numeric($idOrSlug)
            ? $query->where('id_article', (int) $idOrSlug)->first()
            : $query->where('slug', $idOrSlug)->first();

        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        DB::table('articles')->where('id_article', $article->id_article)->increment('views_count');

        return response()->json([
            'success' => true,
            'data'    => $this->castArticle($article),
        ]);
    }

    // ─────────────────────────────────────────────
    // RELATED — Articles similaires (même catégorie)
    // GET /articles/{id}/related
    // ─────────────────────────────────────────────
    public function related(int $id): JsonResponse
    {
        $article = DB::table('articles')
            ->where('id_article', $id)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->first(['id_article', 'category']);

        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        $related = DB::table('articles')
            ->select(self::COLUMNS)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->where('category', $article->category)
            ->where('id_article', '!=', $id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn ($a) => $this->castArticle($a));

        return response()->json(['success' => true, 'data' => $related]);
    }

    // ─────────────────────────────────────────────
    // HELPER — Caster les champs JSON et dates
    // ─────────────────────────────────────────────
    private function castArticle(object $article): array
    {
        $data = (array) $article;
        $data['tags']         = $data['tags'] ? json_decode($data['tags'], true) : [];
        $data['is_sponsored'] = (bool) $data['is_sponsored'];
        return $data;
    }
}
