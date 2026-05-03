<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConseilsController extends Controller
{
    private const CATEGORIES = [
        'entretien_auto'     => 'Entretien auto',
        'economie_carburant' => 'Économie carburant',
        'conduite_securite'  => 'Conduite & sécurité',
        'documents_admin'    => 'Documents admin',
        'astuces_mecaniques' => 'Astuces mécaniques',
        'videos_conseils'    => 'Vidéos conseils',
        'actualites'         => 'Actualités',
        'legislation'        => 'Législation',
    ];

    // ─────────────────────────────────────────────
    // INDEX
    // GET /admin/articles?tab=all&category=&search=&page=
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'all');
        $category = $request->input('category', '');
        $search   = $request->input('search', '');
        $limit    = 20;

        // ── Compteurs par onglet ───────────────────
        $counts = DB::table('articles')
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*)                               as tout,
                SUM(is_published = 1)                  as published,
                SUM(is_published = 0)                  as draft,
                SUM(is_sponsored = 1 AND is_published = 1) as sponsored
            ")
            ->first();

        // ── Requête principale ─────────────────────
        $query = DB::table('articles')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        // Filtre onglet
        match ($tab) {
            'published' => $query->where('is_published', true),
            'draft'     => $query->where('is_published', false),
            'sponsored' => $query->where('is_sponsored', true)->where('is_published', true),
            default     => null,
        };

        if ($category) $query->where('category', $category);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $total    = $query->count();
        $articles = $query->select([
            'id_article', 'title', 'slug', 'category', 'excerpt',
            'is_published', 'is_sponsored', 'sponsor_name',
            'views_count', 'read_time_minutes', 'tags',
            'published_at', 'created_at',
        ])->paginate($limit)->withQueryString();

        // Caster les tags JSON pour chaque article
        $articles->getCollection()->transform(function ($a) {
            $a->tags = $a->tags ? json_decode($a->tags, true) : [];
            return $a;
        });

        return view('pages.articles', compact(
            'articles', 'counts', 'tab', 'category', 'search', 'total'
        ) + ['categories' => self::CATEGORIES]);
    }

    // ─────────────────────────────────────────────
    // SHOW — JSON pour modal de prévisualisation
    // GET /admin/articles/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $article = DB::table('articles')
            ->where('id_article', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        $data = (array) $article;
        $data['tags'] = $data['tags'] ? json_decode($data['tags'], true) : [];

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ─────────────────────────────────────────────
    // STORE — Créer un article
    // POST /admin/articles
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'excerpt'           => 'nullable|string|max:500',
            'content'           => 'required|string|min:10',
            'read_time_minutes' => 'nullable|integer|min:1|max:120',
            'is_sponsored'      => 'sometimes|boolean',
            'sponsor_name'      => 'required_if:is_sponsored,true|nullable|string|max:100',
            'sponsor_url'       => 'nullable|url',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|max:50',
            'publish'           => 'sometimes|boolean',
            'cover_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $slug = $this->uniqueSlug($validated['title']);
        $isPublished = !empty($validated['publish']);

        $coverUrl = null;
        if ($request->hasFile('cover_image')) {
            $path     = $request->file('cover_image')->store('articles/covers', 'public');
            $coverUrl = '/storage/' . $path;
        }

        $id = DB::table('articles')->insertGetId([
            'title'             => $validated['title'],
            'slug'              => $slug,
            'excerpt'           => $validated['excerpt'] ?? null,
            'content'           => $validated['content'],
            'cover_image_url'   => $coverUrl,
            'category'          => $validated['category'],
            'is_sponsored'      => !empty($validated['is_sponsored']),
            'sponsor_name'      => $validated['sponsor_name'] ?? null,
            'sponsor_url'       => $validated['sponsor_url'] ?? null,
            'is_published'      => $isPublished,
            'published_at'      => $isPublished ? now() : null,
            'views_count'       => 0,
            'read_time_minutes' => $validated['read_time_minutes'] ?? $this->estimateReadTime($validated['content']),
            'tags'              => !empty($validated['tags']) ? json_encode($validated['tags']) : null,
            'admin_id'          => auth()->id(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $article = DB::table('articles')->where('id_article', $id)->first();

        return response()->json([
            'success' => true,
            'message' => $isPublished ? 'Article publié avec succès.' : 'Article sauvegardé en brouillon.',
            'data'    => $article,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // UPDATE — Modifier un article
    // PUT /admin/articles/{id}
    // ─────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $article = DB::table('articles')->where('id_article', $id)->whereNull('deleted_at')->first();
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        $validated = $request->validate([
            'title'             => 'sometimes|string|max:255',
            'category'          => 'sometimes|in:' . implode(',', array_keys(self::CATEGORIES)),
            'excerpt'           => 'sometimes|nullable|string|max:500',
            'content'           => 'sometimes|string|min:10',
            'read_time_minutes' => 'sometimes|nullable|integer|min:1|max:120',
            'is_sponsored'      => 'sometimes|boolean',
            'sponsor_name'      => 'nullable|string|max:100',
            'sponsor_url'       => 'nullable|url',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|max:50',
        ]);

        // Recalculer le slug si le titre change
        if (isset($validated['title']) && $validated['title'] !== $article->title) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $id);
        }

        // Recalculer le temps de lecture si le contenu change
        if (isset($validated['content']) && !isset($validated['read_time_minutes'])) {
            $validated['read_time_minutes'] = $this->estimateReadTime($validated['content']);
        }

        if (isset($validated['tags'])) {
            $validated['tags'] = json_encode($validated['tags']);
        }

        if (isset($validated['is_sponsored'])) {
            $validated['is_sponsored'] = (bool) $validated['is_sponsored'];
        }

        DB::table('articles')
            ->where('id_article', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $updated = DB::table('articles')->where('id_article', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Article mis à jour.',
            'data'    => $updated,
        ]);
    }

    // ─────────────────────────────────────────────
    // PUBLISH — Publier / dépublier
    // POST /admin/articles/{id}/publish
    // ─────────────────────────────────────────────
    public function publish(int $id): JsonResponse
    {
        $article = DB::table('articles')->where('id_article', $id)->whereNull('deleted_at')->first();
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        $newStatus = !$article->is_published;

        DB::table('articles')->where('id_article', $id)->update([
            'is_published' => $newStatus,
            'published_at' => $newStatus ? now() : null,
            'updated_at'   => now(),
        ]);

        return response()->json([
            'success'      => true,
            'message'      => $newStatus ? 'Article publié.' : 'Article dépublié.',
            'is_published' => $newStatus,
        ]);
    }

    // ─────────────────────────────────────────────
    // DESTROY — Soft delete
    // DELETE /admin/articles/{id}
    // ─────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        $exists = DB::table('articles')->where('id_article', $id)->whereNull('deleted_at')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        DB::table('articles')->where('id_article', $id)->update([
            'deleted_at'   => now(),
            'is_published' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Article supprimé.']);
    }

    // ─────────────────────────────────────────────
    // UPLOAD COVER — Uploader une image de couverture
    // POST /admin/articles/{id}/cover
    // ─────────────────────────────────────────────
    public function uploadCover(Request $request, int $id): JsonResponse
    {
        $request->validate(['cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072']);

        $article = DB::table('articles')->where('id_article', $id)->whereNull('deleted_at')->first();
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article introuvable.'], 404);
        }

        // Supprimer l'ancienne image
        if ($article->cover_image_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $article->cover_image_url));
        }

        $path     = $request->file('cover')->store("articles/covers", 'public');
        $coverUrl = '/storage/' . $path;

        DB::table('articles')->where('id_article', $id)->update([
            'cover_image_url' => $coverUrl,
            'updated_at'      => now(),
        ]);

        return response()->json(['success' => true, 'data' => ['cover_image_url' => $coverUrl]]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug  = Str::slug($title);
        $base  = $slug;
        $i     = 1;

        while (true) {
            $query = DB::table('articles')->where('slug', $slug)->whereNull('deleted_at');
            if ($excludeId) $query->where('id_article', '!=', $excludeId);
            if (!$query->exists()) break;
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function estimateReadTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200)); // 200 mots/minute
    }
}
