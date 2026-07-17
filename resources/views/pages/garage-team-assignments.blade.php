@extends('layouts.master', ['title' => 'Affectations équipe garages', 'subTitle' => 'Affectations équipe garages'])

@push('csss')
    <style>
        :root {
            --primary: #8B5CF6;
            --primary-light: #EDE9FE;
            --info: #3B82F6;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --purple: #8B5CF6;
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

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-role-owner {
            background: #EDE9FE;
            color: var(--purple);
        }

        .badge-role-manager {
            background: #DBEAFE;
            color: var(--info);
        }

        .badge-role-employee {
            background: #F1F5F9;
            color: #64748B;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            color: var(--primary);
            font-size: 12px;
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
            max-width: 480px;
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

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13.5px;
            outline: none;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary);
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
        const ROLE_LABELS = {
            owner: 'Propriétaire',
            manager: 'Manager',
            employee: 'Employé'
        };
        let searchTimer;
        let editingId = null;

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const r = document.getElementById('filterRole').value;
            const st = document.getElementById('filterGarage').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            r ? url.searchParams.set('role', r) : url.searchParams.delete('role');
            st ? url.searchParams.set('garage_id', st) : url.searchParams.delete('garage_id');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 380);
        });

        function openAddAssignment() {
            editingId = null;
            document.getElementById('formModalTitle').textContent = 'Ajouter une affectation';
            document.getElementById('fOwner').value = '';
            document.getElementById('fGarage').value = '';
            document.getElementById('fRole').value = 'manager';
            openModal('modalFormAssignment');
        }

        function editAssignment(id) {
            fetch(`/admin/garage-team-assignments/${id}`, {
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
                    document.getElementById('formModalTitle').textContent = 'Modifier l\'affectation';
                    document.getElementById('fOwner').value = d.data.owner_id;
                    document.getElementById('fGarage').value = d.data.garage_id;
                    document.getElementById('fRole').value = d.data.role;
                    openModal('modalFormAssignment');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function saveAssignment() {
            const data = {
                owner_id: document.getElementById('fOwner').value,
                garage_id: document.getElementById('fGarage').value,
                role: document.getElementById('fRole').value,
            };

            if (!data.owner_id || !data.garage_id) {
                showToast('Propriétaire et garage requis', 'error');
                return;
            }

            if (editingId) {
                fetch(`/admin/garage-team-assignments/${editingId}`, {
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
                        closeModal('modalFormAssignment');
                        setTimeout(() => location.reload(), 800);
                    })
                    .catch(() => showToast('Erreur réseau', 'error'));
            } else {
                postAction('/admin/garage-team-assignments', data, d => {
                    showToast(d.message, 'success');
                    closeModal('modalFormAssignment');
                    setTimeout(() => location.reload(), 800);
                });
            }
        }

        function deleteAssignment(id) {
            confirmAction('Supprimer cette affectation ?', () => {
                fetch(`/admin/garage-team-assignments/${id}`, {
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
                            const row = document.querySelector(`tr[data-assignment-id="${id}"]`);
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
                <h1>Affectations équipe garages</h1>
                <p>Gérez qui (propriétaire, manager, employé) est rattaché à chaque garage.</p>
            </div>
            <button class="btn btn-primary" onclick="openAddAssignment()">
                <i class="fa-solid fa-plus"></i> Ajouter une affectation
            </button>
        </div>

        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:var(--primary-light)"><i class="fa-solid fa-users"
                        style="color:var(--primary)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['total'] }}</div>
                    <div class="kpi-mini-lbl">Total affectations</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#EDE9FE"><i class="fa-solid fa-crown"
                        style="color:var(--purple)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['owners'] }}</div>
                    <div class="kpi-mini-lbl">Propriétaires</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#DBEAFE"><i class="fa-solid fa-user-tie"
                        style="color:var(--info)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['managers'] }}</div>
                    <div class="kpi-mini-lbl">Managers</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#F1F5F9"><i class="fa-solid fa-user"
                        style="color:var(--text-muted)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['employees'] }}</div>
                    <div class="kpi-mini-lbl">Employés</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Nom, email, garage..."
                        value="{{ $search }}">
                </div>
                <select id="filterRole" class="form-select" style="width:150px" onchange="applyFilters()">
                    <option value="">Tous les rôles</option>
                    <option value="owner" {{ $role === 'owner' ? 'selected' : '' }}>Propriétaire</option>
                    <option value="manager" {{ $role === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ $role === 'employee' ? 'selected' : '' }}>Employé</option>
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
                            <th>Propriétaire</th>
                            <th>Garage</th>
                            <th>Rôle</th>
                            <th>Rattaché depuis</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $a)
                            <tr data-assignment-id="{{ $a->id_gara_owner_gara }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="avatar-circle">{{ strtoupper(substr($a->owner->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $a->owner->name ?? '—' }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">
                                                {{ $a->owner->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600">{{ $a->garage->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--text-muted)">{{ $a->garage->city ?? '' }}</div>
                                </td>
                                <td><span
                                        class="badge badge-role-{{ $a->role }}">{{ ['owner' => 'Propriétaire', 'manager' => 'Manager', 'employee' => 'Employé'][$a->role] }}</span>
                                </td>
                                <td style="font-size:12.5px;color:var(--text-muted)">{{ $a->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px">
                                        <button class="btn btn-sm btn-secondary"
                                            onclick="editAssignment({{ $a->id_gara_owner_gara }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="deleteAssignment({{ $a->id_gara_owner_gara }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-users"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucune affectation trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagi-bar">
                <span class="pagi-info">
                    Affichage {{ $assignments->firstItem() ?? 0 }}–{{ $assignments->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} affectations
                </span>
                <div class="pagi">
                    @if ($assignments->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $assignments->previousPageUrl() }}"><i
                                class="fa-solid fa-chevron-left" style="font-size:11px"></i></a>
                    @endif

                    @foreach ($assignments->getUrlRange(max(1, $assignments->currentPage() - 2), min($assignments->lastPage(), $assignments->currentPage() + 2)) as $page => $url)
                        @if ($page == $assignments->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($assignments->hasMorePages())
                        <a class="pagi-btn" href="{{ $assignments->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- ══ Modal — Ajouter / Modifier affectation ══ --}}
    <div class="modal-overlay" id="modalFormAssignment">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-users" style="color:var(--primary)"></i> <span id="formModalTitle">Ajouter une
                        affectation</span></h5>
                <button class="modal-close" data-modal-close="modalFormAssignment">✕</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom:14px">
                    <label class="form-label">Propriétaire *</label>
                    <select id="fOwner" class="form-select" style="width:100%">
                        <option value="">— Choisir —</option>
                        @foreach ($allOwners as $o)
                            <option value="{{ $o->id_gara_owner }}">{{ $o->name }} ({{ $o->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:14px">
                    <label class="form-label">Garage *</label>
                    <select id="fGarage" class="form-select" style="width:100%">
                        <option value="">— Choisir —</option>
                        @foreach ($allGarages as $g)
                            <option value="{{ $g->id_garage }}">{{ $g->name }} — {{ $g->city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Rôle *</label>
                    <select id="fRole" class="form-select" style="width:100%">
                        <option value="owner">Propriétaire</option>
                        <option value="manager">Manager</option>
                        <option value="employee">Employé</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalFormAssignment">Annuler</button>
                <button class="btn btn-primary" onclick="saveAssignment()"><i class="fa-solid fa-check"></i>
                    Enregistrer</button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
