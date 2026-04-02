<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\PartnerRequest;
use App\Models\Review;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// ═══════════════════════════════════════════════════════════════
// AdminGarageController
// ═══════════════════════════════════════════════════════════════

class AdminGarageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $garages = Garage::withTrashed()
            ->with(['services'])
            ->when($request->search, fn($q) =>
                $q->where('name','like',"%{$request->search}%")
                  ->orWhere('city','like',"%{$request->search}%")
            )
            ->when($request->type,         fn($q) => $q->where('type', $request->type))
            ->when($request->verified !== null, fn($q) => $q->where('is_verified', $request->boolean('verified')))
            ->when($request->subscription, fn($q) => $q->where('subscription_type', $request->subscription))
            ->withCount('reviews')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 25));

        return response()->json(['success' => true, 'data' => $garages]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'type'        => 'required|in:garage_general,centre_vidange,lavage_auto,pneus,batterie,climatisation,electricite_auto,depannage,carrosserie,vitrage',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'phone'       => 'nullable|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        $garage = Garage::create($data);
        return response()->json(['success' => true, 'message' => 'Garage créé.', 'data' => $garage], 201);
    }

    public function show(int $id): JsonResponse
    {
        $garage = Garage::withTrashed()->with(['services','promotions','reviews'])->withCount(['reviews','views'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $garage]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $garage = Garage::withTrashed()->findOrFail($id);
        $data   = $request->validate([
            'name'      => 'sometimes|string|max:150',
            'type'      => 'sometimes|string',
            'address'   => 'sometimes|string|max:255',
            'city'      => 'sometimes|string|max:100',
            'latitude'  => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'phone'     => 'nullable|string|max:20',
        ]);
        $garage->update($data);
        return response()->json(['success' => true, 'message' => 'Garage mis à jour.', 'data' => $garage->fresh()]);
    }

    public function verify(int $id): JsonResponse
    {
        Garage::findOrFail($id)->update(['is_verified' => true]);
        return response()->json(['success' => true, 'message' => 'Badge vérifié attribué.']);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $garage = Garage::withTrashed()->findOrFail($id);
        $garage->update(['is_active' => !$garage->is_active]);
        $action = $garage->is_active ? 'activé' : 'désactivé';
        return response()->json(['success' => true, 'message' => "Garage {$action}."]);
    }

    public function destroy(int $id): JsonResponse
    {
        Garage::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Garage supprimé.']);
    }
}

// ═══════════════════════════════════════════════════════════════
// AdminPartnerRequestController
// ═══════════════════════════════════════════════════════════════

class AdminPartnerRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = PartnerRequest::with('admin:id,name')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type,   fn($q) => $q->where('type', $request->type))
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => PartnerRequest::with('admin')->findOrFail($id)]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $partnerRequest = PartnerRequest::findOrFail($id);
        $data = $request->validate(['subscription_type' => 'required|in:free,pro,premium']);

        // Créer la station ou le garage
        if ($partnerRequest->type === 'station') {
            $entity = \App\Models\Station::create([
                'name'      => $partnerRequest->business_name,
                'address'   => $partnerRequest->address,
                'city'      => $partnerRequest->city,
                'phone'     => $partnerRequest->contact_phone,
                'latitude'  => $partnerRequest->latitude ?? 5.354,
                'longitude' => $partnerRequest->longitude ?? -4.003,
                'subscription_type' => $data['subscription_type'],
                'is_active' => true,
            ]);
        } else {
            $entity = \App\Models\Garage::create([
                'name'      => $partnerRequest->business_name,
                'type'      => 'garage_general',
                'address'   => $partnerRequest->address,
                'city'      => $partnerRequest->city,
                'phone'     => $partnerRequest->contact_phone,
                'latitude'  => $partnerRequest->latitude ?? 5.354,
                'longitude' => $partnerRequest->longitude ?? -4.003,
                'subscription_type' => $data['subscription_type'],
                'is_active' => true,
            ]);
        }

        $partnerRequest->update([
            'status'       => 'approved',
            'admin_id'     => $request->user()->id,
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Partenaire approuvé et créé.',
            'entity'  => $entity,
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $partnerRequest = PartnerRequest::findOrFail($id);
        $partnerRequest->update([
            'status'       => 'rejected',
            'admin_id'     => $request->user()->id,
            'admin_notes'  => $request->input('reason'),
            'processed_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Demande rejetée.']);
    }

    public function contacted(Request $request, int $id): JsonResponse
    {
        PartnerRequest::findOrFail($id)->update([
            'status'   => 'contacted',
            'admin_id' => $request->user()->id,
        ]);
        return response()->json(['success' => true, 'message' => 'Marqué comme contacté.']);
    }
}

// ═══════════════════════════════════════════════════════════════
// AdminSubscriptionController
// ═══════════════════════════════════════════════════════════════

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscriptions = Subscription::with('subscribable')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->plan,   fn($q) => $q->where('plan', $request->plan))
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['success' => true, 'data' => $subscriptions]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Subscription::with('subscribable')->findOrFail($id)]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $sub = Subscription::findOrFail($id);
        $sub->update([
            'status'               => 'cancelled',
            'cancellation_reason'  => $request->input('reason', 'Annulé par admin'),
            'cancelled_at'         => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Abonnement annulé.']);
    }

    public function extend(Request $request, int $id): JsonResponse
    {
        $data = $request->validate(['months' => 'required|integer|min:1|max:24']);
        $sub  = Subscription::findOrFail($id);

        $newExpiry = ($sub->expires_at->isFuture() ? $sub->expires_at : now())->addMonths($data['months']);
        $sub->update(['expires_at' => $newExpiry, 'status' => 'active']);

        return response()->json(['success' => true, 'message' => "Prolongé jusqu'au {$newExpiry->format('d/m/Y')}.", 'expires_at' => $newExpiry]);
    }

    public function revenueStats(): JsonResponse
    {
        $stats = Subscription::where('status', 'active')
            ->selectRaw('plan, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('plan')
            ->orderByDesc('total')
            ->get();

        $totalRevenue = \App\Models\PaymentTransaction::where('status', 'success')->sum('amount');

        return response()->json(['success' => true, 'data' => ['by_plan' => $stats, 'total_revenue' => $totalRevenue]]);
    }
}

// ═══════════════════════════════════════════════════════════════
// AdminReviewController
// ═══════════════════════════════════════════════════════════════

class AdminReviewController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        $reviews = Review::with(['user:id,name,phone', 'reviewable:id,name'])
            ->where('is_approved', false)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with(['user:id,name', 'reviewable:id,name'])
            ->when($request->approved !== null, fn($q) => $q->where('is_approved', $request->boolean('approved')))
            ->when($request->rating, fn($q) => $q->where('rating', $request->rating))
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Review::with(['user','reviewable'])->findOrFail($id)]);
    }

    public function approve(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true, 'approved_at' => now()]);

        // Recalculer la note du garage si applicable
        if ($review->reviewable_type === \App\Models\Garage::class) {
            $avg   = Review::where('reviewable_type', \App\Models\Garage::class)
                ->where('reviewable_id', $review->reviewable_id)->where('is_approved', true)->avg('rating');
            $count = Review::where('reviewable_type', \App\Models\Garage::class)
                ->where('reviewable_id', $review->reviewable_id)->where('is_approved', true)->count();
            \App\Models\Garage::where('id', $review->reviewable_id)->update(['rating' => round($avg, 2), 'rating_count' => $count]);
        }

        return response()->json(['success' => true, 'message' => 'Avis approuvé.']);
    }

    public function destroy(int $id): JsonResponse
    {
        Review::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Avis supprimé.']);
    }
}
