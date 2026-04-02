<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * GET /articles
     */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::published()
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->sponsored, fn($q) => $q->sponsored())
            ->when($request->search, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('title', 'like', "%{$request->search}%")
                      ->orWhere('excerpt', 'like', "%{$request->search}%")
                )
            )
            ->select(['id','title','slug','excerpt','cover_image_url','category',
                      'is_sponsored','sponsor_name','sponsor_logo_url',
                      'published_at','views_count','read_time_minutes'])
            ->orderByDesc('published_at')
            ->paginate($request->input('per_page', 15));

        return response()->json(['success' => true, 'data' => $articles]);
    }

    /**
     * GET /articles/featured
     */
    public function featured(): JsonResponse
    {
        $articles = Article::published()
            ->where(fn($q) => $q->sponsored()->orWhere('views_count', '>', 100))
            ->select(['id','title','slug','excerpt','cover_image_url','category',
                      'is_sponsored','sponsor_name','sponsor_logo_url',
                      'published_at','read_time_minutes'])
            ->orderByDesc('is_sponsored')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $articles]);
    }

    /**
     * GET /articles/trending
     */
    public function trending(): JsonResponse
    {
        $articles = Article::published()
            ->select(['id','title','slug','cover_image_url','category','views_count','published_at'])
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $articles]);
    }

    /**
     * GET /articles/categories
     */
    public function categories(): JsonResponse
    {
        $categories = [
            ['key' => 'entretien_auto',       'label' => 'Entretien automobile'],
            ['key' => 'economie_carburant',   'label' => 'Économie carburant'],
            ['key' => 'conduite_securite',    'label' => 'Conduite & sécurité'],
            ['key' => 'documents_admin',      'label' => 'Documents administratifs'],
            ['key' => 'astuces_mecaniques',   'label' => 'Astuces mécaniques'],
            ['key' => 'videos_conseils',      'label' => 'Vidéos conseils'],
            ['key' => 'actualites',           'label' => 'Actualités'],
            ['key' => 'legislation',          'label' => 'Législation'],
        ];

        // Compter les articles par catégorie
        $counts = Article::published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        foreach ($categories as &$cat) {
            $cat['count'] = $counts[$cat['key']] ?? 0;
        }

        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * GET /articles/{idOrSlug}
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        $article = Article::published()
            ->where(fn($q) =>
                $q->where('id', $idOrSlug)->orWhere('slug', $idOrSlug)
            )
            ->firstOrFail();

        $article->increment('views_count');

        return response()->json(['success' => true, 'data' => $article]);
    }

    /**
     * GET /articles/{id}/related
     */
    public function related(int $id): JsonResponse
    {
        $article = Article::published()->findOrFail($id);

        $related = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->select(['id','title','slug','cover_image_url','published_at','read_time_minutes'])
            ->limit(5)
            ->get();

        return response()->json(['success' => true, 'data' => $related]);
    }
}
