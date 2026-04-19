<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now        = now();
        $monthStart = $now->copy()->startOfMonth();
        $lastMonth  = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // ── KPIs ──────────────────────────────────────────────────────

        // Utilisateurs actifs
        $totalUsers     = DB::table('users_carbur')->where('is_active', true)->whereNull('deleted_at')->count();
        $usersLastMonth = DB::table('users_carbur')->where('is_active', true)->whereNull('deleted_at')
            ->where('created_at', '<', $monthStart)->count();
        $usersGrowth    = $usersLastMonth > 0 ? round((($totalUsers - $usersLastMonth) / $usersLastMonth) * 100, 1) : 0;
        $usersThisMonth = DB::table('users_carbur')->where('created_at', '>=', $monthStart)->count();

        // Abonnés premium
        $premiumUsers    = DB::table('users_carbur')->where('subscription_type', 'premium')->where('is_active', true)->whereNull('deleted_at')->count();
        $premiumLast     = DB::table('users_carbur')->where('subscription_type', 'premium')
            ->where('created_at', '<', $monthStart)->count();
        $premiumGrowth   = $premiumLast > 0 ? round((($premiumUsers - $premiumLast) / $premiumLast) * 100, 1) : 0;

        // Stations actives
        $totalStations     = DB::table('stations')->where('is_active', true)->whereNull('deleted_at')->count();
        $stationsThisMonth = DB::table('stations')->where('is_active', true)->whereNull('deleted_at')
            ->where('created_at', '>=', $monthStart)->count();

        // Garages actifs
        $totalGarages     = DB::table('garages')->where('is_active', true)->whereNull('deleted_at')->count();
        $garagesThisMonth = DB::table('garages')->where('is_active', true)->whereNull('deleted_at')
            ->where('created_at', '>=', $monthStart)->count();

        // Revenus ce mois (transactions success)
        $revenueThisMonth = DB::table('payment_transactions')
            ->where('status', 'success')
            ->where('paid_at', '>=', $monthStart)
            ->sum('amount');

        $revenueLastMonth = DB::table('payment_transactions')
            ->where('status', 'success')
            ->whereBetween('paid_at', [$lastMonth, $lastMonthEnd])
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // Demandes partenaires en attente
        $pendingRequests  = DB::table('partner_requests')->where('status', 'pending')->count();
        $pendingYesterday = DB::table('partner_requests')->where('status', 'pending')
            ->where('created_at', '<', $now->copy()->subDay())->count();
        $pendingDiff      = $pendingRequests - $pendingYesterday;

        // ── Charts ────────────────────────────────────────────────────

        // Revenus 6 derniers mois
        $revenueMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = $now->copy()->subMonths($i)->startOfMonth();
            $end   = $now->copy()->subMonths($i)->endOfMonth();

            $rev = DB::table('payment_transactions')
                ->where('status', 'success')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');

            // Séparation revenus totaux vs abonnements seuls
            $subs = DB::table('payment_transactions')
                ->join('subscriptions', 'subscriptions.id_subcrip', '=', 'payment_transactions.subscription_id')
                ->where('payment_transactions.status', 'success')
                ->whereBetween('payment_transactions.paid_at', [$start, $end])
                ->sum('payment_transactions.amount');

            $revenueMonths[] = [
                'label'  => $start->locale('fr')->isoFormat('MMM'),
                'total'  => (float) $rev,
                'subs'   => (float) $subs,
            ];
        }

        // Répartition plans abonnements actifs
        $planBreakdown = DB::table('subscriptions')
            ->where('status', 'active')
            ->where('expires_at', '>=', $now->toDateString())
            ->select('plan', DB::raw('COUNT(*) as count'))
            ->groupBy('plan')
            ->orderByDesc('count')
            ->get();

        $totalActiveSubs = $planBreakdown->sum('count') ?: 1;
        $planBreakdown = $planBreakdown->map(fn ($r) => [
            'plan'    => $r->plan,
            'count'   => $r->count,
            'percent' => round(($r->count / $totalActiveSubs) * 100),
        ]);

        // Nouveaux users par jour (30 derniers jours)
        $growthByDay = DB::table('users_carbur')
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->whereNull('deleted_at')
            ->selectRaw("DATE(created_at) as day, COUNT(*) as count")
            ->groupByRaw("DATE(created_at)")
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Remplir les jours manquants avec 0
        $growthDays = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $growthDays[] = [
                'label' => $now->copy()->subDays($i)->format('d/m'),
                'count' => $growthByDay->get($d)?->count ?? 0,
            ];
        }

        // Activité par ville (utilisateurs)
        $cityActivity = DB::table('users_carbur')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('city')
            ->select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $totalUsersWithCity = $cityActivity->sum('count') ?: 1;
        $cityActivity = $cityActivity->map(fn ($r) => [
            'city'    => $r->city,
            'count'   => $r->count,
            'percent' => round(($r->count / $totalUsersWithCity) * 100),
        ]);

        // ── Tables ────────────────────────────────────────────────────

        // Derniers inscrits
        $recentUsers = DB::table('users_carbur')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id_user_carbu', 'name', 'phone', 'city', 'subscription_type', 'created_at']);

        // Demandes partenaires en attente
        $pendingPartners = DB::table('partner_requests')
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Activité récente (dernières transactions + inscriptions + publications)
        $recentActivity = $this->buildActivityFeed();

        return view('index', compact(
            // KPIs
            'totalUsers', 'usersGrowth', 'usersThisMonth',
            'premiumUsers', 'premiumGrowth',
            'totalStations', 'stationsThisMonth',
            'totalGarages', 'garagesThisMonth',
            'revenueThisMonth', 'revenueGrowth',
            'pendingRequests', 'pendingDiff',
            // Charts
            'revenueMonths', 'planBreakdown', 'growthDays', 'cityActivity',
            // Tables
            'recentUsers', 'pendingPartners', 'recentActivity'
        ));
    }

    // ─────────────────────────────────────────────
    // Construire le feed d'activité récente
    // ─────────────────────────────────────────────
    private function buildActivityFeed(): Collection
    {
        $activities = collect();

        // Nouveaux utilisateurs (5 derniers)
        DB::table('users_carbur')->whereNull('deleted_at')
            ->orderByDesc('created_at')->limit(5)
            ->get(['name', 'phone', 'created_at'])
            ->each(fn ($u) => $activities->push([
                'type'    => 'user_register',
                'icon'    => 'fa-user-plus',
                'color'   => '#D1FAE5',
                'icolor'  => '#10B981',
                'label'   => "Nouvel utilisateur inscrit",
                'detail'  => "{$u->name} ({$u->phone})",
                'time'    => $u->created_at,
            ]));

        // Paiements réussis (5 derniers)
        DB::table('payment_transactions')
            ->where('status', 'success')
            ->orderByDesc('paid_at')->limit(5)
            ->get(['amount', 'payment_method', 'payer_id', 'payer_type', 'paid_at'])
            ->each(function ($t) use (&$activities) {
                $payerName = $this->resolvePayerName($t->payer_type, $t->payer_id);
                $amount    = number_format($t->amount, 0, ',', ' ');
                $method    = str_replace('_', ' ', $t->payment_method);
                $activities->push([
                    'type'   => 'payment',
                    'icon'   => 'fa-crown',
                    'color'  => '#FEF3C7',
                    'icolor' => '#F59E0B',
                    'label'  => 'Paiement reçu',
                    'detail' => "{$payerName} — {$amount} FCFA via {$method}",
                    'time'   => $t->paid_at,
                ]);
            });

        // Prix carburant mis à jour (5 derniers)
        DB::table('fuel_price_history')
            ->join('stations', 'stations.id_station', '=', 'fuel_price_history.station_id')
            ->orderByDesc('fuel_price_history.changed_at')->limit(5)
            ->get(['stations.name as station_name', 'fuel_price_history.fuel_type', 'fuel_price_history.new_price', 'fuel_price_history.changed_at'])
            ->each(fn ($r) => $activities->push([
                'type'   => 'price_update',
                'icon'   => 'fa-gas-pump',
                'color'  => '#DBEAFE',
                'icolor' => '#3B82F6',
                'label'  => 'Prix carburant mis à jour',
                'detail' => "{$r->station_name} — " . ucfirst($r->fuel_type) . " : {$r->new_price} FCFA/L",
                'time'   => $r->changed_at,
            ]));

        // Stations vérifiées (5 dernières)
        DB::table('stations')
            ->where('is_verified', true)
            ->whereNull('deleted_at')
            ->orderByDesc('updated_at')->limit(5)
            ->get(['name', 'updated_at'])
            ->each(fn ($s) => $activities->push([
                'type'   => 'verified',
                'icon'   => 'fa-shield-check',
                'color'  => '#FEE2E2',
                'icolor' => '#EF4444',
                'label'  => 'Station vérifiée',
                'detail' => "{$s->name} — Badge vérifié attribué",
                'time'   => $s->updated_at,
            ]));

        // Articles publiés (5 derniers)
        DB::table('articles')
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->orderByDesc('published_at')->limit(5)
            ->get(['title', 'published_at'])
            ->each(fn ($a) => $activities->push([
                'type'   => 'article',
                'icon'   => 'fa-newspaper',
                'color'  => '#EDE9FE',
                'icolor' => '#8B5CF6',
                'label'  => 'Article publié',
                'detail' => "\"{$a->title}\"",
                'time'   => $a->published_at,
            ]));

        return $activities->sortByDesc('time')->take(10)->values();
    }

    private function resolvePayerName(string $type, int $id): string
    {
        return match (true) {
            str_contains($type, 'UserCarbur')    => DB::table('users_carbur')->where('id_user_carbu', $id)->value('name') ?? 'Utilisateur',
            str_contains($type, 'StationOwner')  => DB::table('station_owners')->where('id_station_owner', $id)->value('name') ?? 'Station Owner',
            str_contains($type, 'GarageOwner')   => DB::table('garage_owners')->where('id_gara_owner', $id)->value('name') ?? 'Garage Owner',
            default => 'Inconnu',
        };
    }
}
