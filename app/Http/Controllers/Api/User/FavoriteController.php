<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Garage;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    private function resolveModel(string $type): string
    {
        return match($type) {
            'station' => Station::class,
            'garage'  => Garage::class,
            default   => abort(422, 'Type invalide. Utilisez station ou garage.'),
        };
    }

    /**
     * GET /favorites
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()->favorites()
            ->with('favoriteable')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('favoriteable_type')
            ->map(fn($group) => $group->pluck('favoriteable'));

        return response()->json(['success' => true, 'data' => $favorites]);
    }

    /**
     * POST /favorites
     * Body: { type: "station", id: 5 }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:station,garage',
            'id'   => 'required|integer',
        ]);

        $modelClass = $this->resolveModel($data['type']);
        $model      = $modelClass::findOrFail($data['id']);

        // Vérifier doublon
        $exists = $request->user()->favorites()
            ->where('favoriteable_type', $modelClass)
            ->where('favoriteable_id', $data['id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Déjà dans vos favoris.'], 409);
        }

        $favorite = $request->user()->favorites()->create([
            'favoriteable_type' => $modelClass,
            'favoriteable_id'   => $data['id'],
        ]);

        return response()->json(['success' => true, 'message' => 'Ajouté aux favoris.', 'data' => $favorite], 201);
    }

    /**
     * DELETE /favorites/{type}/{id}
     */
    public function destroy(Request $request, string $type, int $id): JsonResponse
    {
        $modelClass = $this->resolveModel($type);

        $deleted = $request->user()->favorites()
            ->where('favoriteable_type', $modelClass)
            ->where('favoriteable_id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Favori introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Retiré des favoris.']);
    }

    /**
     * GET /favorites/check/{type}/{id}
     */
    public function check(Request $request, string $type, int $id): JsonResponse
    {
        $modelClass = $this->resolveModel($type);

        $is_favorite = $request->user()->favorites()
            ->where('favoriteable_type', $modelClass)
            ->where('favoriteable_id', $id)
            ->exists();

        return response()->json(['success' => true, 'is_favorite' => $is_favorite]);
    }
}
