<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbonnementsController extends Controller
{
    /**
     * Display subscriptions dashboard.
     */
    public function index(Request $request)
    {
        /* ── KPIs ─────────────────────────────────── */
        $currentMonthStart = now()->startOfMonth();
        $lastMonthStart    = now()->subMonth()->startOfMonth();
        $lastMonthEnd      = now()->subMonth()->endOfMonth();

        $revenueThisMonth = Subscription::where('status', 'active')
            ->where('paid_at', '>=', $currentMonthStart)
            ->sum('amount');

        $revenueLastMonth = Subscription::where('status', 'active')
            ->whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : 0;

        $activeCount       = Subscription::active()->count();
        $newThisMonth      = Subscription::active()->where('starts_at', '>=', $currentMonthStart)->count();
        $expiringThisMonth = Subscription::expiringSoon(30)->count();

        $renewalRate = $activeCount > 0
            ? round((Subscription::active()->whereNotNull('payment_reference')->count() / $activeCount) * 100)
            : 0;

        $stats = compact(
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowth',
            'activeCount', 'newThisMonth', 'expiringThisMonth', 'renewalRate'
        );

        /* ── Graphe 6 mois (par plan group) ──────── */
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();

            $base = Subscription::whereBetween('paid_at', [$start, $end]);
            $chartData[] = [
                'label'    => $month->translatedFormat('M'),
                'users'    => (clone $base)->byPlanGroup('users')->sum('amount'),
                'stations' => (clone $base)->byPlanGroup('stations')->sum('amount'),
                'garages'  => (clone $base)->byPlanGroup('garages')->sum('amount'),
            ];
        }

        /* ── Répartition revenus par plan (ce mois) ─ */
        $planRevenues = Subscription::active()
            ->where('paid_at', '>=', $currentMonthStart)
            ->select('plan', DB::raw('SUM(amount) as total'))
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        $totalRevenue = array_sum($planRevenues) ?: 1; // avoid div/0

        /* ── Table abonnements (filtrée + paginée) ── */
        $query = Subscription::with('subscribable')->latest('starts_at');

        if ($request->filled('plan_filter') && $request->plan_filter !== 'all') {
            $query->where('plan', $request->plan_filter);
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'all') {
            $query->where('status', $request->status_filter);
        }

        $subscriptions = $query->paginate(20);

        return view('pages.subscriptions', compact(
            'stats', 'chartData', 'planRevenues', 'totalRevenue', 'subscriptions'
        ));
    }

    /**
     * Extend a subscription by one billing cycle.
     */
    public function extend(string $id)
    {
        $sub = Subscription::findOrFail($id);

        $months = match ($sub->billing_cycle) {
            'trimestriel' => 3,
            'annuel'      => 12,
            default       => 1,
        };

        $base = $sub->expires_at->isFuture() ? $sub->expires_at : now();
        $sub->update([
            'expires_at' => $base->addMonths($months),
            'status'     => 'active',
        ]);

        return redirect()->route('subscriptions.index')
            ->with('toast_success', "Abonnement prolongé de {$months} mois.");
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Request $request, string $id)
    {
        $sub = Subscription::findOrFail($id);
        $sub->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at'        => now(),
        ]);

        return redirect()->route('subscriptions.index')
            ->with('toast_warning', 'Abonnement annulé.');
    }

    /* ── Resource stubs (non utilisés pour l'instant) ── */
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
