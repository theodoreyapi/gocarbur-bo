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
    private function resolveModel(string $type)
    {
        return match($type) {
            'station' => [Station::class, new Station()],
            'garage'  => [Garage::class,  new Garage()],
            default   => abort(422, 'Type invalide.'),
        };
    }

    /**
     * POST /reviews
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'    => 'required|in:station,garage',
            'id'      => 'required|integer',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        [$modelClass] = $this->resolveModel($data['type']);
        $entity = $modelClass::findOrFail($data['id']);

        // Un utilisateur = un avis par établissement
        $exists = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', $modelClass)
            ->where('reviewable_id', $data['id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Vous avez déjà laissé un avis.'], 409);
        }

        $review = Review::create([
            'user_id'         => $request->user()->id,
            'reviewable_type' => $modelClass,
            'reviewable_id'   => $data['id'],
            'rating'          => $data['rating'],
            'comment'         => $data['comment'] ?? null,
            'is_approved'     => false, // Modération admin
        ]);

        // Recalculer la note moyenne du garage
        if ($data['type'] === 'garage') {
            $avg = Review::where('reviewable_type', $modelClass)
                ->where('reviewable_id', $data['id'])
                ->where('is_approved', true)
                ->avg('rating');
            $count = Review::where('reviewable_type', $modelClass)
                ->where('reviewable_id', $data['id'])
                ->where('is_approved', true)
                ->count();
            $entity->update(['rating' => round($avg, 2), 'rating_count' => $count]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avis soumis. Il sera visible après modération.',
            'data'    => $review,
        ], 201);
    }

    /**
     * PUT /reviews/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Repasser en modération
        $data['is_approved'] = false;
        $review->update($data);

        return response()->json(['success' => true, 'message' => 'Avis mis à jour.', 'data' => $review->fresh()]);
    }

    /**
     * DELETE /reviews/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);
        $review->delete();

        return response()->json(['success' => true, 'message' => 'Avis supprimé.']);
    }

    /**
     * GET /reviews/my
     */
    public function myReviews(Request $request): JsonResponse
    {
        $reviews = Review::where('user_id', $request->user()->id)
            ->with('reviewable:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $reviews]);
    }
}
