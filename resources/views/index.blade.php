@extends('layouts.master', ['title' => 'Tableau de bord', 'subTitle' => 'Tableau de bord'])

@push('scripts')
    <script>
        // ─── Data passée depuis le contrôleur ────────────────────────────────────────
        const revenueData = @json($revenueMonths);
        const planData = @json($planBreakdown);
        const growthData = @json($growthDays);
        const cityData = @json($cityActivity);

        // ─── Palette couleurs plans ──────────────────────────────────────────────────
        const PLAN_COLORS = {
            'user_premium': '#FF6B35',
            'user_free': '#94A3B8',
            'station_premium': '#3B82F6',
            'station_pro': '#60A5FA',
            'station_free': '#BFDBFE',
            'garage_premium': '#8B5CF6',
            'garage_pro': '#A78BFA',
            'garage_free': '#DDD6FE',
        };
        const PLAN_LABELS = {
            'user_premium': 'User Premium',
            'user_free': 'User Gratuit',
            'station_premium': 'Station Premium',
            'station_pro': 'Station Pro',
            'station_free': 'Station Gratuit',
            'garage_premium': 'Garage Premium',
            'garage_pro': 'Garage Pro',
            'garage_free': 'Garage Gratuit',
        };

        // ─── Chart — Revenus mensuels ────────────────────────────────────────────────
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueData.map(r => r.label),
                datasets: [{
                        label: 'Revenus totaux',
                        data: revenueData.map(r => r.total),
                        backgroundColor: 'rgba(255,107,53,.85)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Abonnements',
                        data: revenueData.map(r => r.subs),
                        backgroundColor: 'rgba(59,130,246,.85)',
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            callback: v => v >= 1000000 ?
                                (v / 1000000).toFixed(1) + 'M' : v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v,
                            font: {
                                size: 12
                            },
                        },
                    },
                },
            },
        });

        // ─── Chart — Répartition plans ──────────────────────────────────────────────
        const planLabels = planData.map(p => PLAN_LABELS[p.plan] || p.plan);
        const planCounts = planData.map(p => p.count);
        const planColors = planData.map(p => PLAN_COLORS[p.plan] || '#CBD5E1');

        new Chart(document.getElementById('plansChart'), {
            type: 'doughnut',
            data: {
                labels: planLabels,
                datasets: [{
                    data: planCounts,
                    backgroundColor: planColors,
                    borderWidth: 0
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
            },
        });

        // ─── Chart — Nouveaux users / jour ──────────────────────────────────────────
        new Chart(document.getElementById('growthChart'), {
            type: 'line',
            data: {
                labels: growthData.map(d => d.label),
                datasets: [{
                    label: 'Nouveaux users',
                    data: growthData.map(d => d.count),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 8,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            stepSize: 1
                        }
                    },
                },
            },
        });

        // ─── Actions partenaires ─────────────────────────────────────────────────────
        function approveRequest(id, btn) {
            fetch(`/admin/partner-requests/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        btn.closest('.partner-item').style.opacity = '0';
                        btn.closest('.partner-item').style.transition = 'opacity .3s';
                        setTimeout(() => btn.closest('.partner-item').remove(), 300);
                        showToast('Demande approuvée', 'success');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function rejectRequest(id, btn) {
            fetch(`/admin/partner-requests/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        btn.closest('.partner-item').style.opacity = '0';
                        btn.closest('.partner-item').style.transition = 'opacity .3s';
                        setTimeout(() => btn.closest('.partner-item').remove(), 300);
                        showToast('Demande rejetée', 'error');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Toast ───────────────────────────────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const c = document.getElementById('toast-container');
            const t = document.createElement('div');
            t.className = `toast ${type}`;
            t.innerHTML = `<span class="toast-dot"></span>${msg}`;
            c.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transition = 'opacity .3s';
                setTimeout(() => t.remove(), 300);
            }, 3000);
        }

        // ─── Export ──────────────────────────────────────────────────────────────────
        function exportReport() {
            showToast('Export en cours...', 'info');
            window.location.href = '/admin/dashboard/export';
        }

        function changePeriod(v) {
            window.location.href = `/admin/dashboard?period=${v}`;
        }
    </script>
@endpush

@push('csss')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <!-- Content -->
    <main class="page-content">

        <!-- Page Header -->
        <div class="page-header"
            style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Tableau de bord</h1>
                <p>Bienvenue, Super Admin · Voici un aperçu de votre plateforme · Dernière mise à jour :
                    {{ now()->format('d M Y à H:i') }}</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <select class="form-select" style="width:auto;padding:8px 12px" onchange="changePeriod(this.value)">
                    <option value="7">7 derniers jours</option>
                    <option value="30" selected>30 derniers jours</option>
                    <option value="90">3 derniers mois</option>
                    <option value="365">Cette année</option>
                </select>
                <button class="btn btn-primary" onclick="exportReport()"><i class="fa-solid fa-download"></i>
                    Exporter</button>
            </div>
        </div>

        <!-- ── KPI Cards ── -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:24px">

            <div class="stat-card">
                <div class="stat-icon" style="background:#FFF0EB"><i class="fa-solid fa-users"
                        style="color:var(--primary)"></i></div>
                <div class="stat-value" id="kpi-users">{{ number_format($totalUsers, 0, ',', ' ') }}</div>
                <div class="stat-label">Utilisateurs actifs</div>
                @if ($usersGrowth > 0)
                    <span class="kpi-change up"><i class="fa-solid fa-arrow-up"></i> +{{ $usersGrowth }}% ce mois</span>
                @elseif($usersGrowth < 0)
                    <span class="kpi-change down"><i class="fa-solid fa-arrow-down"></i> {{ $usersGrowth }}% ce mois</span>
                @else
                    <span class="kpi-change neutral">+{{ $usersThisMonth }} nouveaux</span>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#D1FAE5"><i class="fa-solid fa-crown"
                        style="color:var(--success)"></i></div>
                <div class="stat-value" id="kpi-premium">{{ number_format($premiumUsers, 0, ',', ' ') }}</div>
                <div class="stat-label">Abonnés Premium</div>
                @if ($premiumGrowth > 0)
                    <span class="kpi-change up"><i class="fa-solid fa-arrow-up"></i> +{{ $premiumGrowth }}% ce mois</span>
                @else
                    <span class="kpi-change neutral">{{ $premiumGrowth }}% ce mois</span>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#DBEAFE"><i class="fa-solid fa-gas-pump"
                        style="color:var(--info)"></i></div>
                <div class="stat-value" id="kpi-stations">{{ $totalStations }}</div>
                <div class="stat-label">Stations actives</div>
                <span class="kpi-change {{ $stationsThisMonth > 0 ? 'up' : 'neutral' }}">
                    @if ($stationsThisMonth > 0)
                        <i class="fa-solid fa-arrow-up"></i> +{{ $stationsThisMonth }} ce mois
                    @else
                        Aucune nouvelle
                    @endif
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#EDE9FE"><i class="fa-solid fa-wrench"
                        style="color:var(--purple)"></i></div>
                <div class="stat-value" id="kpi-garages">{{ $totalGarages }}</div>
                <div class="stat-label">Garages actifs</div>
                <span class="kpi-change {{ $garagesThisMonth > 0 ? 'up' : 'neutral' }}">
                    @if ($garagesThisMonth > 0)
                        <i class="fa-solid fa-arrow-up"></i> +{{ $garagesThisMonth }} ce mois
                    @else
                        Aucun nouveau
                    @endif
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF3C7"><i class="fa-solid fa-sack-dollar"
                        style="color:var(--warning)"></i></div>
                <div class="stat-value" id="kpi-revenue">{{ number_format($revenueThisMonth, 0, ',', ' ') }}</div>
                <div class="stat-label">Revenus FCFA ce mois</div>
                @if ($revenueGrowth > 0)
                    <span class="kpi-change up"><i class="fa-solid fa-arrow-up"></i> +{{ $revenueGrowth }}% vs mois
                        dernier</span>
                @elseif($revenueGrowth < 0)
                    <span class="kpi-change down"><i class="fa-solid fa-arrow-down"></i> {{ $revenueGrowth }}% vs mois
                        dernier</span>
                @else
                    <span class="kpi-change neutral">Stable</span>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#FEE2E2"><i class="fa-solid fa-clock"
                        style="color:var(--danger)"></i></div>
                <div class="stat-value" id="kpi-pending">{{ $pendingRequests }}</div>
                <div class="stat-label">Demandes en attente</div>
                @if ($pendingDiff < 0)
                    <span class="kpi-change up"><i class="fa-solid fa-arrow-down"></i> {{ abs($pendingDiff) }} traitées
                        hier</span>
                @elseif($pendingDiff > 0)
                    <span class="kpi-change down"><i class="fa-solid fa-arrow-up"></i> +{{ $pendingDiff }} depuis
                        hier</span>
                @else
                    <span class="kpi-change neutral">Stable</span>
                @endif
            </div>

        </div>

        <!-- ── Charts Row ── -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">

            <!-- Revenus mensuels -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-chart-line" style="color:var(--primary)"></i>
                        Revenus mensuels (FCFA)</div>
                    <div style="display:flex;gap:8px">
                        <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-muted)"><span
                                style="width:10px;height:10px;background:var(--primary);border-radius:2px;display:inline-block"></span>Revenus</span>
                        <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-muted)"><span
                                style="width:10px;height:10px;background:var(--info);border-radius:2px;display:inline-block"></span>Abonnements</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="revenueChart"></canvas></div>
                </div>
            </div>

            <!-- Répartition abonnements -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--purple)"></i>
                        Répartition plans</div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:200px"><canvas id="plansChart"></canvas></div>
                    <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
                        @foreach ($planBreakdown as $plan)
                            <div class="plan-legend-item">
                                <span>
                                    <span class="plan-dot"
                                        style="background:{{ ['user_premium' => '#FF6B35', 'station_pro' => '#3B82F6', 'station_premium' => '#2563EB', 'garage_pro' => '#8B5CF6', 'garage_premium' => '#6D28D9'][$plan['plan']] ?? '#CBD5E1' }}"></span>
                                    {{ ['user_premium' => 'User Premium', 'user_free' => 'User Gratuit', 'station_pro' => 'Station Pro', 'station_premium' => 'Station Premium', 'garage_pro' => 'Garage Pro', 'garage_premium' => 'Garage Premium'][$plan['plan']] ?? $plan['plan'] }}
                                </span>
                                <strong>{{ $plan['count'] }} ({{ $plan['percent'] }}%)</strong>
                            </div>
                        @endforeach
                        @if ($planBreakdown->isEmpty())
                            <p style="font-size:13px;color:var(--text-muted);text-align:center;margin:0">Aucun abonnement
                                actif</p>
                        @endif
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                            <span style="display:flex;align-items:center;gap:6px"><span
                                    style="width:10px;height:10px;background:#FF6B35;border-radius:50%;display:inline-block"></span>User
                                Premium</span>
                            <strong>241 (69%)</strong>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                            <span style="display:flex;align-items:center;gap:6px"><span
                                    style="width:10px;height:10px;background:#3B82F6;border-radius:50%;display:inline-block"></span>Station
                                Pro</span>
                            <strong>68 (20%)</strong>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                            <span style="display:flex;align-items:center;gap:6px"><span
                                    style="width:10px;height:10px;background:#8B5CF6;border-radius:50%;display:inline-block"></span>Garage
                                Pro</span>
                            <strong>38 (11%)</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Croissance utilisateurs ── -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-user-plus" style="color:var(--success)"></i>
                        Nouveaux utilisateurs / jour (30j)</div>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="growthChart"></canvas></div>
                </div>
            </div>

            <!-- Activité par ville -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-map-location-dot" style="color:var(--info)"></i>
                        Activité par ville</div>
                </div>
                <div class="card-body">
                    @php
                        $barColors = [
                            'var(--primary)',
                            'var(--info)',
                            'var(--success)',
                            'var(--warning)',
                            'var(--purple)',
                        ];
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:14px">
                        @forelse($cityActivity as $i => $city)
                            <div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>{{ $city['city'] }}</span>
                                    <strong>{{ $city['percent'] }}%</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar"
                                        style="width:{{ $city['percent'] }}%;background:{{ $barColors[$i] ?? '#CBD5E1' }}">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:var(--text-muted);text-align:center;margin:0">Aucune donnée de
                                ville</p>
                        @endforelse
                        {{-- <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                <span>Abidjan</span><strong>68%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:68%;background:var(--primary)"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                <span>Bouaké</span><strong>12%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:12%;background:var(--info)"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                <span>Daloa</span><strong>7%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:7%;background:var(--success)"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                <span>Yamoussoukro</span><strong>5%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:5%;background:var(--warning)"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                <span>Autres</span><strong>8%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:8%;background:var(--purple)"></div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Derniers utilisateurs + Demandes ── -->
        <div style="display:grid;grid-template-columns:3fr 2fr;gap:20px;margin-bottom:24px">

            <!-- Derniers inscrits -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-users" style="color:var(--primary)"></i>
                        Dernières inscriptions</div>
                    <a href="{{ url('admin.users.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Téléphone</th>
                                <th>Ville</th>
                                <th>Plan</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody id="recent-users">
                            @forelse($recentUsers as $user)
                                @php
                                    $initials = collect(explode(' ', $user->name))
                                        ->map(fn($w) => strtoupper($w[0] ?? ''))
                                        ->take(2)
                                        ->implode('');
                                    $avatarBgs = [
                                        'linear-gradient(135deg,#FF6B35,#FF9B6B)',
                                        'linear-gradient(135deg,#3B82F6,#1D4ED8)',
                                        'linear-gradient(135deg,#10B981,#059669)',
                                        'linear-gradient(135deg,#8B5CF6,#6D28D9)',
                                        'linear-gradient(135deg,#F59E0B,#D97706)',
                                    ];
                                    $bg = $avatarBgs[$loop->index % count($avatarBgs)];
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar" style="background:{{ $bg }}">
                                                {{ $initials }}</div>
                                            <span class="fw-600">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td style="color:var(--text-muted)">{{ $user->phone }}</td>
                                    <td>{{ $user->city ?? '—' }}</td>
                                    <td>
                                        @if ($user->subscription_type === 'premium')
                                            <span class="badge badge-premium"><i class="fa-solid fa-crown"
                                                    style="font-size:9px;margin-right:3px"></i>Premium</span>
                                        @else
                                            <span class="badge badge-free">Gratuit</span>
                                        @endif
                                    </td>
                                    <td style="color:var(--text-muted)">
                                        {{ \Carbon\Carbon::parse($user->created_at)->locale('fr')->isoFormat('D MMM YYYY') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px">
                                        Aucun utilisateur inscrit récemment
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Demandes partenaires -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-handshake" style="color:var(--warning)"></i>
                        Demandes en attente
                        @if ($pendingRequests > 0)
                            <span
                                style="background:var(--danger);color:#fff;font-size:10px;padding:2px 7px;border-radius:20px;margin-left:4px">{{ $pendingRequests }}</span>
                        @endif
                    </div>
                    <a href="{{ url('admin.partner-requests.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
                </div>
                <div class="card-body" style="padding:0">
                    <div style="display:flex;flex-direction:column">
                        {{-- <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                            <div
                                style="width:38px;height:38px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i>
                            </div>
                            <div style="flex:1">
                                <div class="fw-600" style="font-size:13.5px">Total Marcory</div>
                                <div style="font-size:12px;color:var(--text-muted)">Station • Abidjan</div>
                            </div>
                            <div style="display:flex;gap:5px">
                                <button class="btn btn-sm btn-success"
                                    onclick="showToast('Demande approuvée','success')"><i
                                        class="fa-solid fa-check"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="showToast('Demande rejetée','error')"><i
                                        class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                            <div
                                style="width:38px;height:38px;background:#DBEAFE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-wrench" style="color:var(--info)"></i>
                            </div>
                            <div style="flex:1">
                                <div class="fw-600" style="font-size:13.5px">Garage Auto Plus</div>
                                <div style="font-size:12px;color:var(--text-muted)">Garage • Cocody</div>
                            </div>
                            <div style="display:flex;gap:5px">
                                <button class="btn btn-sm btn-success"
                                    onclick="showToast('Demande approuvée','success')"><i
                                        class="fa-solid fa-check"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="showToast('Demande rejetée','error')"><i
                                        class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                            <div
                                style="width:38px;height:38px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i>
                            </div>
                            <div style="flex:1">
                                <div class="fw-600" style="font-size:13.5px">Petro Ivoire Yop.</div>
                                <div style="font-size:12px;color:var(--text-muted)">Station • Yopougon</div>
                            </div>
                            <div style="display:flex;gap:5px">
                                <button class="btn btn-sm btn-success"
                                    onclick="showToast('Demande approuvée','success')"><i
                                        class="fa-solid fa-check"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="showToast('Demande rejetée','error')"><i
                                        class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                        <div style="padding:14px 20px;display:flex;align-items:center;gap:12px">
                            <div
                                style="width:38px;height:38px;background:#EDE9FE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-wrench" style="color:var(--purple)"></i>
                            </div>
                            <div style="flex:1">
                                <div class="fw-600" style="font-size:13.5px">Centre Vidange Exp.</div>
                                <div style="font-size:12px;color:var(--text-muted)">Garage • Plateau</div>
                            </div>
                            <div style="display:flex;gap:5px">
                                <button class="btn btn-sm btn-success"
                                    onclick="showToast('Demande approuvée','success')"><i
                                        class="fa-solid fa-check"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="showToast('Demande rejetée','error')"><i
                                        class="fa-solid fa-times"></i></button>
                            </div>
                        </div> --}}
                        @forelse($pendingPartners as $partner)
                            <div class="partner-item" id="partner-{{ $partner->id_demande }}">
                                <div class="partner-ico"
                                    style="background:{{ $partner->type === 'station' ? '#FFF0EB' : '#EDE9FE' }}">
                                    <i class="fa-solid {{ $partner->type === 'station' ? 'fa-gas-pump' : 'fa-wrench' }}"
                                        style="color:{{ $partner->type === 'station' ? 'var(--primary)' : 'var(--purple)' }}"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="fw-600"
                                        style="font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                        {{ $partner->business_name }}
                                    </div>
                                    <div style="font-size:12px;color:var(--text-muted)">
                                        <span
                                            class="badge {{ $partner->type === 'station' ? 'badge-station' : 'badge-garage' }}"
                                            style="font-size:10px;padding:1px 6px">
                                            {{ ucfirst($partner->type) }}
                                        </span>
                                        · {{ $partner->city }}
                                    </div>
                                </div>
                                <div style="display:flex;gap:5px;flex-shrink:0">
                                    <button class="btn-success-sm"
                                        onclick="approveRequest({{ $partner->id_demande }}, this)" title="Approuver">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button class="btn-danger-sm"
                                        onclick="rejectRequest({{ $partner->id_demande }}, this)" title="Rejeter">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div style="padding:40px 20px;text-align:center;color:var(--text-muted)">
                                <i class="fa-solid fa-check-circle"
                                    style="font-size:28px;color:#10B981;margin-bottom:8px;display:block"></i>
                                Aucune demande en attente
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Activité récente ── -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--info)"></i>
                    Activité récente</div>
                <a href="{{ url('admin.activity-logs.index') }}" class="btn btn-sm btn-secondary">Journal complet</a>
            </div>
            <div class="card-body" style="padding:0">
                {{-- <div id="activity-feed" style="display:flex;flex-direction:column">
                    <div
                        style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                        <div
                            style="width:36px;height:36px;background:#D1FAE5;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-user-plus" style="color:var(--success)"></i>
                        </div>
                        <div style="flex:1"><strong>Nouvel utilisateur inscrit</strong> — Kouassi Aya (+225 07 12
                            34 56)</div>
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 5 min</span>
                    </div>
                    <div
                        style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                        <div
                            style="width:36px;height:36px;background:#FEF3C7;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-crown" style="color:var(--warning)"></i>
                        </div>
                        <div style="flex:1"><strong>Abonnement Premium activé</strong> — N'Guessan Ahou — 1 500
                            FCFA via Orange Money</div>
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 12
                            min</span>
                    </div>
                    <div
                        style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                        <div
                            style="width:36px;height:36px;background:#DBEAFE;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-gas-pump" style="color:var(--info)"></i>
                        </div>
                        <div style="flex:1"><strong>Prix carburant mis à jour</strong> — Total Énergies Cocody —
                            Essence : 695 FCFA/L</div>
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 28
                            min</span>
                    </div>
                    <div
                        style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                        <div
                            style="width:36px;height:36px;background:#FEE2E2;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-shield-check" style="color:var(--danger)"></i>
                        </div>
                        <div style="flex:1"><strong>Station vérifiée</strong> — Shell Plateau — Badge vérifié
                            attribué par Super Admin</div>
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 45
                            min</span>
                    </div>
                    <div style="padding:14px 20px;display:flex;align-items:center;gap:14px">
                        <div
                            style="width:36px;height:36px;background:#EDE9FE;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-newspaper" style="color:var(--purple)"></i>
                        </div>
                        <div style="flex:1"><strong>Article publié</strong> — "5 signes que votre voiture a besoin
                            d'une vidange" — Par Admin</div>
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 1h</span>
                    </div>
                </div> --}}
                @forelse($recentActivity as $item)
                    <div class="activity-item" style="display:flex;flex-direction:column">
                        <div class="activity-icon" style="background:{{ $item['color'] }}">
                            <i class="fa-solid {{ $item['icon'] }}" style="color:{{ $item['icolor'] }}"></i>
                        </div>
                        <div class="activity-label">
                            <strong>{{ $item['label'] }}</strong>
                            <span> — {{ $item['detail'] }}</span>
                        </div>
                        <div class="activity-time"
                            title="{{ \Carbon\Carbon::parse($item['time'])->format('d/m/Y H:i') }}">
                            {{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div style="padding:40px 20px;text-align:center;color:var(--text-muted)">
                        Aucune activité récente
                    </div>
                @endforelse
            </div>
        </div>

    </main>
@endsection
