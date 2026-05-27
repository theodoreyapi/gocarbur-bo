@extends('layouts.master', ['title' => 'Abonnements', 'subTitle' => 'Abonnements'])

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    /* ── Flash toasts ────────────────────────────── */
    @if(session('toast_success'))
        showToast(@json(session('toast_success')), 'success');
    @endif
    @if(session('toast_warning'))
        showToast(@json(session('toast_warning')), 'warning');
    @endif

    /* ── Graphe revenus ─────────────────────────── */
    new Chart(document.getElementById('subRevenueChart'), {
        type: 'bar',
        data: {
            labels: @json(array_column($chartData, 'label')),
            datasets: [
                {
                    label: 'User Premium',
                    data: @json(array_column($chartData, 'users')),
                    backgroundColor: '#FF6B35',
                    borderRadius: 4
                },
                {
                    label: 'Station Pro/Premium',
                    data: @json(array_column($chartData, 'stations')),
                    backgroundColor: '#3B82F6',
                    borderRadius: 4
                },
                {
                    label: 'Garage Pro/Premium',
                    data: @json(array_column($chartData, 'garages')),
                    backgroundColor: '#8B5CF6',
                    borderRadius: 4
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, boxWidth: 10 }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { color: '#F1F5F9' },
                    ticks: { callback: v => (v / 1000000).toFixed(1) + 'M' }
                }
            },
        },
    });

    /* ── Cancel ──────────────────────────────────── */
    function confirmCancel(id) {
        confirmAction('Annuler cet abonnement ?', () => {
            document.getElementById(`cancelForm_${id}`).submit();
        });
    }
</script>
@endpush

@section('content')
<main class="page-content">

    <div class="page-header">
        <h1>Abonnements</h1>
        <p>Gestion des abonnements actifs et des revenus.</p>
    </div>

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF3C7">
                <i class="fa-solid fa-sack-dollar" style="color:var(--warning)"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['revenueThisMonth'], 0, ',', ' ') }}</div>
            <div class="stat-label">Revenus ce mois (FCFA)</div>
            <div class="stat-change {{ $stats['revenueGrowth'] >= 0 ? 'up' : 'down' }}">
                <i class="fa-solid fa-arrow-{{ $stats['revenueGrowth'] >= 0 ? 'up' : 'down' }}"></i>
                {{ $stats['revenueGrowth'] >= 0 ? '+' : '' }}{{ $stats['revenueGrowth'] }}%
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#D1FAE5">
                <i class="fa-solid fa-crown" style="color:var(--success)"></i>
            </div>
            <div class="stat-value">{{ $stats['activeCount'] }}</div>
            <div class="stat-label">Abonnements actifs</div>
            <div class="stat-change up">
                <i class="fa-solid fa-arrow-up"></i> +{{ $stats['newThisMonth'] }} ce mois
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEE2E2">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--danger)"></i>
            </div>
            <div class="stat-value">{{ $stats['expiringThisMonth'] }}</div>
            <div class="stat-label">Expirent ce mois</div>
            <div class="stat-change down">
                <i class="fa-solid fa-exclamation"></i> À renouveler
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#DBEAFE">
                <i class="fa-solid fa-chart-line" style="color:var(--info)"></i>
            </div>
            <div class="stat-value">{{ $stats['renewalRate'] }}%</div>
            <div class="stat-label">Taux de renouvellement</div>
            <div class="stat-change up">
                <i class="fa-solid fa-arrow-up"></i>
                {{ $stats['renewalRate'] >= 80 ? 'Excellent' : 'À améliorer' }}
            </div>
        </div>
    </div>

    {{-- ── Graphe + répartition ────────────────────── --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px">

        {{-- Chart.js --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-chart-bar" style="color:var(--primary)"></i>
                    Revenus par plan — 6 derniers mois
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="subRevenueChart"></canvas></div>
            </div>
        </div>

        {{-- Répartition par plan --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-list" style="color:var(--info)"></i> Revenus par plan
                </div>
            </div>
            <div class="card-body">
                @php
                    $planGroups = [
                        ['plans' => ['user_premium'],                    'color' => 'var(--primary)', 'label' => 'User Premium'],
                        ['plans' => ['station_pro'],                     'color' => 'var(--info)',    'label' => 'Station Pro'],
                        ['plans' => ['station_premium'],                 'color' => 'var(--purple)',  'label' => 'Station Premium'],
                        ['plans' => ['garage_pro', 'garage_premium'],   'color' => 'var(--success)', 'label' => 'Garage Pro/Premium'],
                    ];
                @endphp
                <div style="display:flex;flex-direction:column;gap:14px">
                    @foreach($planGroups as $group)
                        @php
                            $groupTotal = collect($group['plans'])->sum(fn($p) => $planRevenues[$p] ?? 0);
                            $pct = $totalRevenue > 0 ? round(($groupTotal / $totalRevenue) * 100) : 0;
                        @endphp
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px">
                                <span>
                                    <i class="fa-solid fa-circle" style="color:{{ $group['color'] }};font-size:10px"></i>
                                    {{ $group['label'] }}
                                </span>
                                <strong>{{ number_format($groupTotal, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $group['color'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tableau abonnements ──────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Abonnements actifs</div>
            <div style="display:flex;gap:10px">
                <form method="GET" action="{{ route('subscriptions.index') }}" style="display:flex;gap:10px">
                    <select name="plan_filter" class="form-select" style="width:160px" onchange="this.form.submit()">
                        <option value="all" {{ request('plan_filter', 'all') === 'all' ? 'selected' : '' }}>Tous les plans</option>
                        @foreach(\App\Models\Subscription::planLabels() as $val => $label)
                            <option value="{{ $val }}" {{ request('plan_filter') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <select name="status_filter" class="form-select" style="width:140px" onchange="this.form.submit()">
                        <option value="all" {{ request('status_filter', 'all') === 'all' ? 'selected' : '' }}>Tous statuts</option>
                        <option value="active"    {{ request('status_filter') === 'active'    ? 'selected' : '' }}>Actif</option>
                        <option value="expired"   {{ request('status_filter') === 'expired'   ? 'selected' : '' }}>Expiré</option>
                        <option value="cancelled" {{ request('status_filter') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        <option value="pending"   {{ request('status_filter') === 'pending'   ? 'selected' : '' }}>En attente</option>
                    </select>
                </form>
                <a href="{{ route('subscriptions.index', array_merge(request()->all(), ['export' => 1])) }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-download"></i> Export
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Abonné</th>
                        <th>Type</th>
                        <th>Plan</th>
                        <th>Méthode</th>
                        <th>Montant</th>
                        <th>Début</th>
                        <th>Expiration</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        @php
                            $avatarColors = ['#3B82F6,#1D4ED8','#EF4444,#B91C1C','#10B981,#059669','#F59E0B,#D97706','#8B5CF6,#6D28D9'];
                            $color = $avatarColors[$sub->id_subcrip % count($avatarColors)];
                            $typeIcon  = match($sub->subscriber_type) {
                                'user'    => ['fa-user',    'badge-primary', 'Utilisateur'],
                                'station' => ['fa-gas-pump','badge-info',    'Station'],
                                'garage'  => ['fa-wrench',  'badge-purple',  'Garage'],
                                default   => ['fa-circle',  'badge-gray',    '—'],
                            };
                        @endphp
                        <tr>
                            {{-- Abonné --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="width:34px;height:34px;background:linear-gradient(135deg,{{ $color }});border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                                        {{ $sub->subscriber_initials }}
                                    </div>
                                    <span class="fw-600">{{ $sub->subscriber_name }}</span>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="badge {{ $typeIcon[1] }}">
                                    <i class="fa-solid {{ $typeIcon[0] }}" style="font-size:9px"></i>
                                    {{ $typeIcon[2] }}
                                </span>
                            </td>

                            {{-- Plan --}}
                            <td>
                                <span class="badge {{ $sub->plan_badge }}">{{ $sub->plan_label }}</span>
                            </td>

                            {{-- Méthode paiement --}}
                            <td>
                                <span style="display:flex;align-items:center;gap:5px;font-size:12px">
                                    <i class="fa-solid fa-circle" style="color:{{ $sub->payment_method_color }};font-size:8px"></i>
                                    {{ $sub->payment_method_label }}
                                </span>
                            </td>

                            {{-- Montant --}}
                            <td class="fw-600">{{ number_format($sub->amount, 0, ',', ' ') }} FCFA</td>

                            {{-- Dates --}}
                            <td style="font-size:12px">{{ $sub->starts_at->format('d M Y') }}</td>
                            <td style="font-size:12px">{{ $sub->expires_at->format('d M Y') }}</td>

                            {{-- Statut --}}
                            <td>
                                <span class="badge {{ $sub->status_badge }}">
                                    <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                    {{ $sub->status_label }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- Prolonger --}}
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault();document.getElementById('extendForm_{{ $sub->id_subcrip }}').submit()">
                                            <i class="fa-solid fa-plus"></i> Prolonger
                                        </a>
                                        <form id="extendForm_{{ $sub->id_subcrip }}" method="POST"
                                            action="{{ route('subscriptions.extend', $sub->id_subcrip) }}" style="display:none">
                                            @csrf
                                        </form>

                                        {{-- Annuler --}}
                                        @if($sub->status === 'active')
                                            <a class="dropdown-item text-danger" href="#"
                                                onclick="event.preventDefault();confirmCancel({{ $sub->id_subcrip }})">
                                                <i class="fa-solid fa-ban"></i> Annuler
                                            </a>
                                            <form id="cancelForm_{{ $sub->id_subcrip }}" method="POST"
                                                action="{{ route('subscriptions.cancel', $sub->id_subcrip) }}" style="display:none">
                                                @csrf
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;color:var(--text-muted);padding:32px">
                                Aucun abonnement trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
            <span style="font-size:13px;color:var(--text-muted)">
                {{ $subscriptions->total() }} abonnements
            </span>
            {{ $subscriptions->appends(request()->all())->links('vendor.pagination.simple') }}
        </div>
    </div>

</main>

<div class="toast-container"></div>
@endsection
