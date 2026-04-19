<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\Review;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    private const TYPE_MAP = [
        'station' => 'App\Models\Station',
        'garage'  => 'App\Models\Garage',
    ];

    // POST /connecte/reviews  {type: "station", id: 5, rating: 4, comment: "..."}
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'    => 'required|in:station,garage',
            'id'      => 'required|integer|min:1',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId         = $request->user()->id_user_carbu;
        $reviewableType = self::TYPE_MAP[$validated['type']];
        $reviewableId   = $validated['id'];

        // Vérifier que la ressource existe
        $table = $validated['type'] . 's';
        $pk    = 'id_' . ($validated['type'] === 'station' ? 'station' : 'garage');
        if (!DB::table($table)->where($pk, $reviewableId)->where('is_active', true)->exists()) {
            return response()->json(['success' => false, 'message' => ucfirst($validated['type']) . ' introuvable.'], 404);
        }

        // Un avis unique par utilisateur + ressource (géré par la contrainte unique DB)
        $exists = DB::table('reviews')
            ->where('user_id', $userId)
            ->where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $reviewableId)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Vous avez déjà déposé un avis.'], 409);
        }

        $id = DB::table('reviews')->insertGetId([
            'user_id'         => $userId,
            'reviewable_type' => $reviewableType,
            'reviewable_id'   => $reviewableId,
            'rating'          => $validated['rating'],
            'comment'         => $validated['comment'] ?? null,
            'is_approved'     => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Recalculer la note du garage si c'en est un
        if ($validated['type'] === 'garage') {
            $this->recalcGarageRating($reviewableId);
        }

        $review = DB::table('reviews')->where('id_review', $id)->first();

        return response()->json(['success' => true, 'message' => 'Avis déposé. En attente de modération.', 'data' => $review], 201);
    }

    // PUT /connecte/reviews/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $review = DB::table('reviews')
            ->where('id_review', $id)->where('user_id', $userId)->whereNull('deleted_at')->first();

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Avis introuvable.'], 404);
        }

        $validated = $request->validate([
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:1000',
        ]);

        DB::table('reviews')
            ->where('id_review', $id)
            ->update(array_merge($validated, [
                'is_approved' => false, // repasse en modération
                'updated_at'  => now(),
            ]));

        if ($review->reviewable_type === 'App\Models\Garage') {
            $this->recalcGarageRating($review->reviewable_id);
        }

        $updated = DB::table('reviews')->where('id_review', $id)->first();

        return response()->json(['success' => true, 'message' => 'Avis mis à jour.', 'data' => $updated]);
    }

    // DELETE /connecte/reviews/{id}
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;

        $review = DB::table('reviews')
            ->where('id_review', $id)->where('user_id', $userId)->whereNull('deleted_at')->first();

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Avis introuvable.'], 404);
        }

        DB::table('reviews')->where('id_review', $id)->update(['deleted_at' => now()]);

        if ($review->reviewable_type === 'App\Models\Garage') {
            $this->recalcGarageRating($review->reviewable_id);
        }

        return response()->json(['success' => true, 'message' => 'Avis supprimé.']);
    }

    // GET /connecte/reviews/my
    public function myReviews(Request $request): JsonResponse
    {
        $userId = $request->user()->id_user_carbu;
        $limit  = $request->input('limit', 20);
        $page   = max(1, (int) $request->input('page', 1));

        $query = DB::table('reviews')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');

        $total = $query->count();
        $items = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => ['total' => $total, 'page' => $page, 'limit' => $limit],
        ]);
    }

    private function recalcGarageRating(int $garageId): void
    {
        $stats = DB::table('reviews')
            ->where('reviewable_type', 'App\Models\Garage')
            ->where('reviewable_id', $garageId)
            ->where('is_approved', true)
            ->whereNull('deleted_at')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        DB::table('garages')->where('id_garage', $garageId)->update([
            'rating'       => round($stats->avg ?? 0, 2),
            'rating_count' => $stats->total ?? 0,
            'updated_at'   => now(),
        ]);
    }
}
