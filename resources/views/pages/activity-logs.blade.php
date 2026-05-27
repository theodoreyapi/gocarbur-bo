@extends('layouts.master', ['title' => "Journaux d'activité", 'subTitle' => "Journaux d'activité"])

@push('scripts')
    <script>
        /* ── Flash ───────────────────────────────────── */
        @if (session('toast_info'))
            showToast(@json(session('toast_info')), 'info');
        @endif

        /* ── Modal détail ────────────────────────────── */
        function showLogDetail(id) {
            fetch(`/activity-logs/${id}`)
                .then(r => r.json())
                .then(log => {
                    document.getElementById('dTimestamp').textContent = log.occurred_at;
                    document.getElementById('dLevel').innerHTML =
                        `<span class="badge ${log.level_badge}">${log.level_label}</span>`;
                    document.getElementById('dCategory').innerHTML =
                        `<span class="badge badge-gray" style="font-size:10px">${log.action_group}</span>`;
                    document.getElementById('dAction').textContent = log.action;
                    document.getElementById('dAdmin').textContent = log.causer_name + (log.causer_id ?
                        ` (ID: ${log.causer_id})` : '');
                    document.getElementById('dIp').textContent = log.ip_address;
                    document.getElementById('dAgent').textContent = log.user_agent;
                    document.getElementById('dDescription').textContent = log.description ?? '—';

                    const jsonData = {};
                    if (log.old_values) jsonData.old_values = log.old_values;
                    if (log.new_values) jsonData.new_values = log.new_values;
                    document.getElementById('dJson').textContent = Object.keys(jsonData).length ?
                        JSON.stringify(jsonData, null, 2) :
                        '{ }';

                    openModal('modalLogDetail');
                });
        }
    </script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-header"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Journaux d'activité</h1>
                <p>Toutes les actions effectuées par les administrateurs et le système.</p>
            </div>
            <a href="{{ route('activity-logs.export', request()->all()) }}" class="btn btn-secondary">
                <i class="fa-solid fa-download"></i> Exporter les logs
            </a>
        </div>

        {{-- ── KPIs ─────────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:20px">
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Actions aujourd'hui</div>
                <div class="fw-700" style="font-size:22px">{{ number_format($stats['today'], 0, ',', ' ') }}</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Cette semaine</div>
                <div class="fw-700" style="font-size:22px">{{ number_format($stats['week'], 0, ',', ' ') }}</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Ce mois</div>
                <div class="fw-700" style="font-size:22px">{{ number_format($stats['month'], 0, ',', ' ') }}</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Admins actifs</div>
                <div class="fw-700" style="font-size:22px">{{ $stats['active_admins'] }}</div>
            </div>
        </div>

        <div class="card">

            {{-- ── Filtres ─────────────────────────────── --}}
            <form method="GET" action="{{ route('activity-logs.index') }}" class="filter-bar">

                <div style="position:relative;flex:1;min-width:200px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" name="search" placeholder="Description, action, IP..."
                        value="{{ request('search') }}"
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>

                <select name="action_group" class="form-select" style="width:180px" onchange="this.form.submit()">
                    <option value="all" {{ request('action_group', 'all') === 'all' ? 'selected' : '' }}>Toutes les
                        actions</option>
                    @foreach (\App\Models\ActivityLog::actionGroups() as $val => $label)
                        <option value="{{ $val }}" {{ request('action_group') === $val ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <select name="causer_id" class="form-select" style="width:160px" onchange="this.form.submit()">
                    <option value="all" {{ request('causer_id', 'all') === 'all' ? 'selected' : '' }}>Tous les admins
                    </option>
                    @foreach ($admins as $admin)
                        <option value="{{ $admin['id'] }}" {{ request('causer_id') == $admin['id'] ? 'selected' : '' }}>
                            {{ $admin['name'] }}
                        </option>
                    @endforeach
                    <option value="0" {{ request('causer_id') === '0' ? 'selected' : '' }}>Système (auto)</option>
                </select>

                <input type="date" name="date" class="form-control" style="width:155px" value="{{ request('date') }}"
                    onchange="this.form.submit()">

                <select name="level" class="form-select" style="width:140px" onchange="this.form.submit()">
                    <option value="all" {{ request('level', 'all') === 'all' ? 'selected' : '' }}>Tous niveaux</option>
                    <option value="success" {{ request('level') === 'success' ? 'selected' : '' }}>Succès</option>
                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Avertissement</option>
                    <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Erreur</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            {{-- ── Tableau ──────────────────────────────── --}}
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Horodatage</th>
                            <th>Niveau</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Admin</th>
                            <th>IP</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $isSystem = !$log->causer_id;
                                $avatarColors = [
                                    '#3B82F6,#1D4ED8',
                                    '#EF4444,#B91C1C',
                                    '#10B981,#059669',
                                    '#F59E0B,#D97706',
                                    '#8B5CF6,#6D28D9',
                                ];
                                $color = $avatarColors[$log->id_log % count($avatarColors)];
                            @endphp
                            <tr>
                                {{-- Horodatage --}}
                                <td style="font-size:12px;white-space:nowrap">
                                    <div>{{ $log->occurred_at->format('d M Y') }}</div>
                                    <div style="color:var(--text-muted)">{{ $log->occurred_at->format('H:i:s') }}</div>
                                </td>

                                {{-- Niveau --}}
                                <td>
                                    <span class="badge {{ $log->level_badge }}">{{ $log->level_label }}</span>
                                </td>

                                {{-- Catégorie d'action --}}
                                <td>
                                    <span class="badge badge-gray" style="font-size:10px">{{ $log->action_group }}</span>
                                </td>

                                {{-- Description --}}
                                <td style="font-size:13px;max-width:280px">
                                    {{ Str::limit($log->description ?? $log->action, 80) }}
                                </td>

                                {{-- Admin / Causer --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px">
                                        @if ($isSystem)
                                            <div
                                                style="width:26px;height:26px;background:#F1F5F9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px">
                                                🤖
                                            </div>
                                            <span style="font-size:12px;color:var(--text-muted)">Système</span>
                                        @else
                                            <div
                                                style="width:26px;height:26px;background:linear-gradient(135deg,{{ $color }});border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0">
                                                {{ $log->causer_initials }}
                                            </div>
                                            <span style="font-size:12px">{{ $log->causer_name }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- IP --}}
                                <td style="font-size:11px;color:var(--text-muted)">
                                    {{ $log->ip_address ?? '—' }}
                                </td>

                                {{-- Détail --}}
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="showLogDetail({{ $log->id_log }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:var(--text-muted);padding:32px">
                                    Aucun journal trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ───────────────────────────── --}}
            <div
                style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:10px">
                <span style="font-size:13px;color:var(--text-muted)">
                    {{ $logs->total() }} entrées
                    @if (request('date'))
                        le {{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}
                    @endif
                </span>
                {{ $logs->appends(request()->all())->links('vendor.pagination.simple') }}
            </div>
        </div>

    </main>

    {{-- ── Modal Détail log ─────────────────────────── --}}
    <div class="modal-overlay" id="modalLogDetail">
        <div class="modal-box" style="max-width:520px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-list-check" style="color:var(--info)"></i> Détail de l'entrée</h5>
                <button class="modal-close" data-modal-close="modalLogDetail">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:14px">

                    {{-- Méta --}}
                    <div style="background:var(--bg);border-radius:var(--radius-sm);padding:14px">
                        <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;font-size:13px">
                            <span style="color:var(--text-muted)">Horodatage</span>
                            <span class="fw-600" id="dTimestamp">—</span>

                            <span style="color:var(--text-muted)">Niveau</span>
                            <span id="dLevel">—</span>

                            <span style="color:var(--text-muted)">Catégorie</span>
                            <span id="dCategory">—</span>

                            <span style="color:var(--text-muted)">Action brute</span>
                            <span class="fw-600" id="dAction" style="font-size:12px;font-family:monospace">—</span>

                            <span style="color:var(--text-muted)">Admin</span>
                            <span class="fw-600" id="dAdmin">—</span>

                            <span style="color:var(--text-muted)">Adresse IP</span>
                            <span class="fw-600" id="dIp">—</span>

                            <span style="color:var(--text-muted)">User-Agent</span>
                            <span style="font-size:11px;color:var(--text-muted)" id="dAgent">—</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <div class="fw-600" style="font-size:13px;margin-bottom:6px">Description</div>
                        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;font-size:13px"
                            id="dDescription">—</div>
                    </div>

                    {{-- JSON --}}
                    <div>
                        <div class="fw-600" style="font-size:13px;margin-bottom:6px">Données associées (JSON)</div>
                        <pre id="dJson"
                            style="background:#1E293B;color:#94A3B8;padding:12px;border-radius:var(--radius-sm);font-size:11px;overflow-x:auto;margin:0;white-space:pre-wrap">{ }</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalLogDetail">Fermer</button>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection
