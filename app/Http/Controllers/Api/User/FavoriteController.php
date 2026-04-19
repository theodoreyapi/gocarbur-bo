<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Garage;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    private const TYPE_MAP = [
        'station' => 'App\Models\Station',
        'garage'  => 'App\Models\Garage',
    ];

    // GET /connecte/favorites
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $favorites = DB::table('favorites')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        // Enrichir chaque favori avec les infos du lieu
        $enriched = $favorites->map(function ($fav) {
            $fav = (array) $fav;

            if ($fav['favoriteable_type'] === 'App\Models\Station') {
                $fav['place'] = DB::table('stations')
                    ->where('id_station', $fav['favoriteable_id'])
                    ->first(['id_station', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url', 'is_verified']);
            } elseif ($fav['favoriteable_type'] === 'App\Models\Garage') {
                $fav['place'] = DB::table('garages')
                    ->where('id_garage', $fav['favoriteable_id'])
                    ->first(['id_garage', 'name', 'address', 'city', 'latitude', 'longitude', 'logo_url', 'rating']);
            } else {
                $fav['place'] = null;
            }

            return $fav;
        });

        return response()->json(['success' => true, 'data' => $enriched]);
    }

    // POST /connecte/favorites  {type: "station", id: 5}
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:station,garage',
            'id'   => 'required|integer|min:1',
        ]);

        $userId        = $request->user()->id_user_carbu;
        $favoriteType  = self::TYPE_MAP[$validated['type']];
        $favoriteId    = $validated['id'];

        // Vérifier que la ressource existe
        $table = $validated['type'] . 's';
        $pk    = 'id_' . ($validated['type'] === 'station' ? 'station' : 'garage');
        $exists = DB::table($table)->where($pk, $favoriteId)->where('is_active', true)->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => ucfirst($validated['type']) . ' introuvable.'], 404);
        }

        // Vérifier qu'il n'est pas déjà en favori
        $alreadyFav = DB::table('favorites')
            ->where('user_id', $userId)
            ->where('favoriteable_type', $favoriteType)
            ->where('favoriteable_id', $favoriteId)
            ->exists();

        if ($alreadyFav) {
            return response()->json(['success' => false, 'message' => 'Déjà en favoris.'], 409);
        }

        $id = DB::table('favorites')->insertGetId([
            'user_id'           => $userId,
            'favoriteable_type' => $favoriteType,
            'favoriteable_id'   => $favoriteId,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Ajouté aux favoris.', 'data' => ['id' => $id]], 201);
    }

    // DELETE /connecte/favorites/{type}/{id}
    public function destroy(Request $request, string $type, int $id): JsonResponse
    {
        if (!array_key_exists($type, self::TYPE_MAP)) {
            return response()->json(['success' => false, 'message' => 'Type invalide.'], 422);
        }

        $deleted = DB::table('favorites')
            ->where('user_id', $request->user()->id_user_carbu)
            ->where('favoriteable_type', self::TYPE_MAP[$type])
            ->where('favoriteable_id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Favori introuvable.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Retiré des favoris.']);
    }

    // GET /connecte/favorites/check/{type}/{id}
    public function check(Request $request, string $type, int $id): JsonResponse
    {
        if (!array_key_exists($type, self::TYPE_MAP)) {
            return response()->json(['success' => false, 'message' => 'Type invalide.'], 422);
        }

        $isFav = DB::table('favorites')
            ->where('user_id', $request->user()->id_user_carbu)
            ->where('favoriteable_type', self::TYPE_MAP[$type])
            ->where('favoriteable_id', $id)
            ->exists();

        return response()->json(['success' => true, 'data' => ['is_favorite' => $isFav]]);
    }
}
