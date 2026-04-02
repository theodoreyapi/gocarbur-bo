<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    /** GET /admin/articles */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::withTrashed()
            ->when($request->search, fn($q) =>
                $q->where('title','like',"%{$request->search}%")
            )
            ->when($request->category,   fn($q) => $q->where('category', $request->category))
            ->when($request->published !== null, fn($q) => $q->where('is_published', $request->boolean('published')))
            ->when($request->sponsored !== null, fn($q) => $q->where('is_sponsored', $request->boolean('sponsored')))
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json(['success' => true, 'data' => $articles]);
    }

    /** POST /admin/articles */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'              => 'required|string|max:200',
            'excerpt'            => 'nullable|string|max:500',
            'content'            => 'required|string',
            'category'           => 'required|in:entretien_auto,economie_carburant,conduite_securite,documents_admin,astuces_mecaniques,videos_conseils,actualites,legislation',
            'is_sponsored'       => 'boolean',
            'sponsor_name'       => 'nullable|string|max:100',
            'sponsor_logo_url'   => 'nullable|url',
            'sponsor_url'        => 'nullable|url',
            'read_time_minutes'  => 'nullable|integer|min:1',
            'tags'               => 'nullable|array',
            'is_published'       => 'boolean',
        ]);

        $data['admin_id']  = $request->user()->id;
        $data['slug']      = Str::slug($data['title']) . '-' . Str::random(6);
        $data['published_at'] = $data['is_published'] ? now() : null;

        $article = Article::create($data);

        return response()->json(['success' => true, 'message' => 'Article créé.', 'data' => $article], 201);
    }

    /** GET /admin/articles/{id} */
    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Article::withTrashed()->findOrFail($id)]);
    }

    /** PUT /admin/articles/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $article = Article::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'title'             => 'sometimes|string|max:200',
            'excerpt'           => 'nullable|string|max:500',
            'content'           => 'sometimes|string',
            'category'          => 'sometimes|string',
            'is_sponsored'      => 'boolean',
            'sponsor_name'      => 'nullable|string|max:100',
            'sponsor_logo_url'  => 'nullable|url',
            'sponsor_url'       => 'nullable|url',
            'read_time_minutes' => 'nullable|integer|min:1',
            'tags'              => 'nullable|array',
        ]);

        $article->update($data);

        return response()->json(['success' => true, 'message' => 'Article mis à jour.', 'data' => $article->fresh()]);
    }

    /** PATCH /admin/articles/{id}/publish */
    public function publish(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->update(['is_published' => true, 'published_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Article publié.']);
    }

    /** PATCH /admin/articles/{id}/unpublish */
    public function unpublish(int $id): JsonResponse
    {
        Article::findOrFail($id)->update(['is_published' => false]);
        return response()->json(['success' => true, 'message' => 'Article dépublié.']);
    }

    /** POST /admin/articles/{id}/cover */
    public function uploadCover(Request $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $request->validate(['cover' => 'required|image|max:3072']);

        if ($article->cover_image_url) {
            $old = str_replace(Storage::url(''), '', $article->cover_image_url);
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('cover')->store("articles/{$id}", 'public');
        $article->update(['cover_image_url' => Storage::url($path)]);

        return response()->json(['success' => true, 'cover_image_url' => $article->cover_image_url]);
    }

    /** DELETE /admin/articles/{id} */
    public function destroy(int $id): JsonResponse
    {
        Article::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Article supprimé.']);
    }
}
