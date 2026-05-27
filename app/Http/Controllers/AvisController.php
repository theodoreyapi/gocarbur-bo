<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvisController extends Controller
{
    /**
     * Display moderation dashboard.
     */
    public function index(Request $request)
    {
        // KPIs
        $stats = [
            'pending'      => Review::pending()->count(),
            'approved'     => Review::approved()->count(),
            'deleted'      => Review::onlyTrashed()->count(),
            'avg_rating'   => round(Review::approved()->avg('rating') ?? 0, 1),
        ];

        // ── Onglet "En attente" ──────────────────────
        $pendingReviews = Review::with(['user', 'reviewable'])
            ->pending()
            ->orderByDesc('created_at')
            ->get();

        // ── Onglet "Approuvés" ───────────────────────
        $approvedQuery = Review::with(['user', 'reviewable'])
            ->approved();

        if ($request->filled('search_approved')) {
            $search = $request->search_approved;
            $approvedQuery->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHasMorph('reviewable', '*', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('rating_approved') && $request->rating_approved !== 'all') {
            $approvedQuery->where('rating', $request->rating_approved);
        }

        if ($request->filled('type_approved') && $request->type_approved !== 'all') {
            $morph = $request->type_approved === 'stations'
                ? 'App\\Models\\Station'
                : 'App\\Models\\Garage';
            $approvedQuery->where('reviewable_type', $morph);
        }

        $approvedReviews = $approvedQuery->orderByDesc('approved_at')->paginate(20, ['*'], 'page_approved');

        // ── Onglet "Tous" ────────────────────────────
        $allQuery = Review::with(['user', 'reviewable']);

        if ($request->filled('search_all')) {
            $search = $request->search_all;
            $allQuery->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status_all') && $request->status_all !== 'all') {
            match ($request->status_all) {
                'pending'  => $allQuery->pending(),
                'approved' => $allQuery->approved(),
                'deleted'  => $allQuery->onlyTrashed(),
                default    => null,
            };
        }

        if ($request->filled('rating_all') && $request->rating_all !== 'all') {
            $allQuery->where('rating', $request->rating_all);
        }

        $allReviews = $allQuery->orderByDesc('created_at')->paginate(20, ['*'], 'page_all');

        return view('pages.reviews', compact('stats', 'pendingReviews', 'approvedReviews', 'allReviews'));
    }

    /**
     * Approve a single review.
     */
    public function approve(string $id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return redirect()->route('reviews.index')
            ->with('toast_success', 'Avis approuvé et publié.');
    }

    /**
     * Approve all pending reviews at once.
     */
    public function approveAll()
    {
        $count = Review::pending()->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return redirect()->route('reviews.index')
            ->with('toast_success', "{$count} avis approuvés.");
    }

    /**
     * Soft-delete a review.
     */
    public function destroy(string $id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        $review->delete(); // soft delete

        return redirect()->route('reviews.index')
            ->with('toast_error', 'Avis supprimé.');
    }
}
