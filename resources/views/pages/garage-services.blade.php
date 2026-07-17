@extends('layouts.master', ['title' => 'Services garages', 'subTitle' => 'Services garages'])

@push('csss')
    <style>
        :root {
            --primary: #8B5CF6;
            --primary-light: #EDE9FE;
            --info: #3B82F6;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --border: #EAECF0;
            --text: #0F172A;
            --text-muted: #64748B;
            --bg: #F7F8FB;
            --card: #FFFFFF;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .04);
        }

        .page-content {
            padding: 24px 32px;
        }

        .page-hdr {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
        }

        .page-hdr h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
            margin: 0 0 4px;
            letter-spacing: -.4px;
        }

        .page-hdr p {
            color: var(--text-muted);
            font-size: 13.5px;
            margin: 0;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .kpi-mini {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 18px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeUp .4s ease both;
        }

        .kpi-mini-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .kpi-mini-val {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .kpi-mini-lbl {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeUp .4s ease .1s both;
        }

        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: #FAFBFC;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-wrap i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
            pointer-events: none;
        }

        .search-inp {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13px;
            outline: none;
            background: #fff;
            box-sizing: border-box;
        }

        .search-inp:focus {
            border-color: var(--primary);
        }

        .form-select {
            padding: 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13px;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            outline: none;
        }

        .form-select:focus {
            border-color: var(--primary);
        }

        .tbl-wrap {
            overflow-x: auto;
        }

        .data-tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .data-tbl th {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            background: #FAFBFC;
            white-space: nowrap;
        }

        .data-tbl td {
            padding: 12px 14px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        .data-tbl tr:last-child td {
            border-bottom: none;
        }

        .data-tbl tbody tr:hover td {
            background: #F8FAFF;
        }

        .svc-chip {
            background: var(--primary-light);
            color: var(--primary);
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .garage-icon {
            width: 34px;
            height: 34px;
            background: #FFF0EB;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: #7C3AED;
        }

        .btn-secondary {
            background: #F1F5F9;
            color: var(--text);
        }

        .btn-secondary:hover {
            background: #E2E8F0;
        }

        .btn-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .btn-danger:hover {
            background: #FECACA;
        }

        .btn-sm {
            padding: 5px 11px;
            font-size: 12px;
            border-radius: 7px;
        }

        .pagi-bar {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagi-info {
            font-size: 13px;
            color: var(--text-muted);
        }

        .pagi {
            display: flex;
            gap: 4px;
        }

        .pagi-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            text-decoration: none;
        }

        .pagi-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .pagi-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .pagi-btn.disabled {
            opacity: .4;
            pointer-events: none;
        }

        .modal-overlay {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(3px);
            z-index: 9000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: opacity .2s ease, visibility .2s ease;
        }

        .modal-overlay.open {
            visibility: visible;
            opacity: 1;
            pointer-events: auto;
        }

        .modal-overlay.open .modal-box {
            animation: slideUp .25s ease both;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            position: relative;
            z-index: 9001;
        }

        .modal-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
        }

        .modal-hdr h5 {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--text-muted);
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #F1F5F9;
        }

        .modal-body {
            padding: 22px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #FAFBFC;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text);
            display: block;
            margin-bottom: 5px;
        }

        .form-control,
        .form-select.full {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13.5px;
            outline: none;
            box-sizing: border-box;
        }

        .fw-600 {
            font-weight: 600;
        }

        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: 10px;
            background: #1e293b;
            color: #fff;
            font-size: 13.5px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
            min-width: 240px;
        }

        .toast-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .toast.success .toast-dot {
            background: #10B981;
        }

        .toast.error .toast-dot {
            background: #EF4444;
        }

        .toast.warning .toast-dot {
            background: #F59E0B;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const CSRF = document.querySelector('meta[name=csrf-token]')?.content;
        let searchTimer;
        let editingId = null;

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const sv = document.getElementById('filterService').value;
            const st = document.getElementById('filterGarage').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            sv ? url.searchParams.set('service', sv) : url.searchParams.delete('service');
            st ? url.searchParams.set('garage_id', st) : url.searchParams.delete('garage_id');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 380);
        });

        function openAddService() {
            editingId = null;
            document.getElementById('formModalTitle').textContent = 'Ajouter un service';
            document.getElementById('fGarage').value = '';
            document.getElementById('fService').value = 'vidange';
            document.getElementById('fPriceRange').value = '';
            openModal('modalFormService');
        }

        function editService(id) {
            fetch(`/admin/garage-services/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (!d.success) {
                        showToast('Erreur chargement', 'error');
                        return;
                    }
                    editingId = id;
                    document.getElementById('formModalTitle').textContent = 'Modifier le service';
                    document.getElementById('fGarage').value = d.data.garage_id;
                    document.getElementById('fService').value = d.data.service;
                    document.getElementById('fPriceRange').value = d.data.price_range || '';
                    openModal('modalFormService');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function saveService() {
            const data = {
                garage_id: document.getElementById('fGarage').value,
                service: document.getElementById('fService').value,
                price_range: document.getElementById('fPriceRange').value.trim() || null,
            };

            if (!data.garage_id) {
                showToast('Garage requis', 'error');
                return;
            }

            if (editingId) {
                fetch(`/admin/garage-services/${editingId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (!d.success) {
                            showToast(d.message || 'Erreur', 'error');
                            return;
                        }
                        showToast(d.message, 'success');
                        closeModal('modalFormService');
                        setTimeout(() => location.reload(), 800);
                    })
                    .catch(() => showToast('Erreur réseau', 'error'));
            } else {
                postAction('/admin/garage-services', data, d => {
                    showToast(d.message, 'success');
                    closeModal('modalFormService');
                    setTimeout(() => location.reload(), 800);
                });
            }
        }

        function deleteService(id) {
            confirmAction('Supprimer ce service ?', () => {
                fetch(`/admin/garage-services/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        showToast(d.message, d.success ? 'success' : 'error');
                        if (d.success) {
                            const row = document.querySelector(`tr[data-service-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transition = 'opacity .3s';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
            });
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('open');
            document.body.style.overflow = '';
        }

        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal(this.dataset.modalClose);
                };
            });
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(this.id);
                });
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        });

        function postAction(url, body, onSuccess) {
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) onSuccess(d);
                    else showToast(d.message || 'Erreur', 'error');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function confirmAction(msg, cb) {
            if (window.confirm(msg)) cb();
        }

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
    </script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-hdr">
            <div>
                <h1>Services garages</h1>
                <p>Gérez individuellement chaque service rattaché à un garage.</p>
            </div>
            <button class="btn btn-primary" onclick="openAddService()">
                <i class="fa-solid fa-plus"></i> Ajouter un service
            </button>
        </div>

        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:var(--primary-light)"><i class="fa-solid fa-list-check"
                        style="color:var(--primary)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['total'] }}</div>
                    <div class="kpi-mini-lbl">Total entrées</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#DBEAFE"><i class="fa-solid fa-wrench"
                        style="color:var(--info)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['garages_with_services'] }}</div>
                    <div class="kpi-mini-lbl">Garages équipés</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#D1FAE5"><i class="fa-solid fa-star"
                        style="color:var(--success)"></i></div>
                <div>
                    <div class="kpi-mini-val" style="font-size:14px">
                        {{ $kpis['most_common'] === '—' ? '—' : \Illuminate\Support\Str::headline($kpis['most_common']) }}
                    </div>
                    <div class="kpi-mini-lbl">Service le + courant</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEF3C7"><i class="fa-solid fa-shapes"
                        style="color:var(--warning)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['distinct_types'] }}/11</div>
                    <div class="kpi-mini-lbl">Types utilisés</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Nom du garage..."
                        value="{{ $search }}">
                </div>
                <select id="filterService" class="form-select" style="width:180px" onchange="applyFilters()">
                    <option value="">Tous les services</option>
                    @foreach (\App\Http\Controllers\GarageServicesController::SERVICES as $svc)
                        <option value="{{ $svc }}" {{ $service === $svc ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::headline($svc) }}</option>
                    @endforeach
                </select>
                <select id="filterGarage" class="form-select" style="width:200px" onchange="applyFilters()">
                    <option value="">Tous les garages</option>
                    @foreach ($allGarages as $g)
                        <option value="{{ $g->id_garage }}"
                            {{ (string) $garageId === (string) $g->id_garage ? 'selected' : '' }}>{{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="tbl-wrap">
                <table class="data-tbl">
                    <thead>
                        <tr>
                            <th>Garage</th>
                            <th>Service</th>
                            <th>Ajouté le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $svc)
                            <tr data-service-id="{{ $svc->id_gara_service }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="garage-icon">
                                            <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:13px"></i>
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $svc->garage->name ?? '—' }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">
                                                {{ $svc->garage->city ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="svc-chip"><i class="fa-solid fa-check" style="font-size:9px"></i>
                                        {{ \Illuminate\Support\Str::headline($svc->service) }}</span>
                                    @if ($svc->price_range)
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                            {{ $svc->price_range }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12.5px;color:var(--text-muted)">
                                    {{ $svc->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display:flex;gap:6px">
                                        <button class="btn btn-sm btn-secondary"
                                            onclick="editService({{ $svc->id_gara_service }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="deleteService({{ $svc->id_gara_service }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-list-check"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucun service trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagi-bar">
                <span class="pagi-info">
                    Affichage {{ $services->firstItem() ?? 0 }}–{{ $services->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} entrées
                </span>
                <div class="pagi">
                    @if ($services->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $services->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></a>
                    @endif

                    @foreach ($services->getUrlRange(max(1, $services->currentPage() - 2), min($services->lastPage(), $services->currentPage() + 2)) as $page => $url)
                        @if ($page == $services->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($services->hasMorePages())
                        <a class="pagi-btn" href="{{ $services->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- ══ Modal — Ajouter / Modifier service ══ --}}
    <div class="modal-overlay" id="modalFormService">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-list-check" style="color:var(--primary)"></i> <span id="formModalTitle">Ajouter
                        un service</span></h5>
                <button class="modal-close" data-modal-close="modalFormService">✕</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom:14px">
                    <label class="form-label">Garage *</label>
                    <select id="fGarage" class="form-select full">
                        <option value="">— Choisir —</option>
                        @foreach ($allGarages as $g)
                            <option value="{{ $g->id_garage }}">{{ $g->name }} — {{ $g->city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Service *</label>
                    <select id="fService" class="form-select full">
                        @foreach (\App\Http\Controllers\GarageServicesController::SERVICES as $svc)
                            <option value="{{ $svc }}">{{ \Illuminate\Support\Str::headline($svc) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-top:14px">
                    <label class="form-label">Fourchette de prix</label>
                    <input type="text" id="fPriceRange" class="form-control" placeholder="Ex: 15 000 – 25 000 FCFA">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalFormService">Annuler</button>
                <button class="btn btn-primary" onclick="saveService()"><i class="fa-solid fa-check"></i>
                    Enregistrer</button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
