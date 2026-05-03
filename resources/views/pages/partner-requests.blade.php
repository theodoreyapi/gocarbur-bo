@extends('layouts.master', ['title' => 'Demandes partenaires', 'subTitle' => 'Demandes partenaires'])

@push('csss')
    <style>
        :root {
            --primary: #FF6B35;
            --primary-light: #FFF0EB;
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

        body {
            background: var(--bg);
        }

        .page-content {
            padding: 24px 32px;
        }

        .page-hdr {
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

        /* KPIs */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .kpi-mini {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, border-color .15s;
        }

        .kpi-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .09);
        }

        .kpi-mini.kpi-active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 107, 53, .15);
        }

        .kpi-mini-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .kpi-mini-val {
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .kpi-mini-lbl {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Alert */
        .alert-warn {
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            color: #92400E;
            padding: 12px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            margin-bottom: 18px;
        }

        /* Filters row */
        .filter-row {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
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

        /* Card + Tabs */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .tab-nav {
            display: flex;
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
        }

        .tab-btn {
            padding: 14px 16px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 2.5px solid transparent;
            margin-bottom: -1px;
            transition: color .15s, border-color .15s;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--text);
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .nav-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        /* Request card */
        .req-wrap {
            display: flex;
            flex-direction: column;
        }

        .req-card {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }

        .req-card:last-child {
            border-bottom: none;
        }

        .req-card:hover {
            background: #FAFBFF;
        }

        .req-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .req-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .req-meta {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 7px;
            margin: 8px 0 10px;
        }

        .req-meta-item {
            font-size: 13px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .req-meta-item i {
            color: var(--text-muted);
            width: 14px;
            text-align: center;
            flex-shrink: 0;
            font-size: 12px;
        }

        .req-message {
            background: var(--bg);
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 13px;
            color: var(--text-muted);
            font-style: italic;
            margin-bottom: 12px;
            border-left: 3px solid var(--border);
        }

        .req-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Badges */
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

        .badge-station {
            background: var(--primary-light);
            color: var(--primary);
        }

        .badge-garage {
            background: #EDE9FE;
            color: var(--purple);
        }

        .badge-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-contacted {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .badge-approved {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-rejected {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-premium {
            background: #EDE9FE;
            color: var(--purple);
        }

        .badge-pro {
            background: #DBEAFE;
            color: var(--info);
        }

        .badge-free {
            background: #F1F5F9;
            color: #64748B;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .btn-success {
            background: #10B981;
            color: #fff;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #DC2626;
        }

        .btn-secondary {
            background: #F1F5F9;
            color: var(--text);
        }

        .btn-secondary:hover {
            background: #E2E8F0;
        }

        .btn-info {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .btn-info:hover {
            background: #BFDBFE;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 7px;
        }

        /* Table */
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

        /* Pagination */
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
            transition: all .12s;
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

        /* Modal */
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
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .modal-footer {
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #FAFBFC;
        }

        /* Form */
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
            transition: border-color .15s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* Callout */
        .callout-success {
            background: #D1FAE5;
            color: #065F46;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        /* Empty */
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 32px;
            opacity: .3;
            display: block;
            margin-bottom: 10px;
        }

        /* Toast */
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
            animation: slideIn .25s ease;
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

        .toast.info .toast-dot {
            background: #3B82F6;
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
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

        @keyframes slideIn {
            from {
                transform: translateX(50px);
                opacity: 0
            }

            to {
                transform: none;
                opacity: 1
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const CSRF = document.querySelector('meta[name=csrf-token]')?.content;
        let currentId = null;

        // ─── Navigation par tab / statut ──────────────────────────────────────────────
        function switchTab(s) {
            const u = new URL(window.location.href);
            u.searchParams.set('status', s);
            u.searchParams.delete('page');
            window.location.href = u;
        }

        function applyFilter(key, val) {
            const u = new URL(window.location.href);
            val ? u.searchParams.set(key, val) : u.searchParams.delete(key);
            u.searchParams.delete('page');
            window.location.href = u;
        }

        // ─── Approve ─────────────────────────────────────────────────────────────────
        function approveRequest(id) {
            currentId = id;
            document.getElementById('approveEmail').value = '';
            document.getElementById('approveNotes').value = '';
            openModal('modalApprove');
        }

        function confirmApprove() {
            const plan = document.getElementById('approvePlan').value;
            const email = document.getElementById('approveEmail').value.trim();
            const notes = document.getElementById('approveNotes').value.trim();
            if (!email) {
                showToast('Email requis', 'error');
                return;
            }

            const btn = document.getElementById('btnApprove');
            setLoading(btn, true, 'Traitement...');

            postAction(`/admin/partner-requests/${currentId}/approve`, {
                plan,
                email,
                admin_notes: notes
            }, d => {
                setLoading(btn, false, '<i class="fa-solid fa-check"></i> Confirmer l\'approbation');
                showToast(d.message, 'success');
                closeModal('modalApprove');
                removeCard(currentId);
                updateKpi('pending', -1);
                updateKpi('approved', 1);
            }, () => setLoading(btn, false, '<i class="fa-solid fa-check"></i> Confirmer l\'approbation'));
        }

        // ─── Contact ──────────────────────────────────────────────────────────────────
        function contactRequest(id) {
            postAction(`/admin/partner-requests/${id}/contact`, {}, d => {
                showToast(d.message, 'info');
                removeCard(id);
                updateKpi('pending', -1);
                updateKpi('contacted', 1);
            });
        }

        // ─── Reject ───────────────────────────────────────────────────────────────────
        function rejectRequest(id) {
            currentId = id;
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectMessage').value = '';
            openModal('modalReject');
        }

        function confirmReject() {
            const reason = document.getElementById('rejectReason').value;
            const message = document.getElementById('rejectMessage').value.trim();
            if (!reason) {
                showToast('Raison requise', 'error');
                return;
            }

            const btn = document.getElementById('btnReject');
            setLoading(btn, true, 'Traitement...');

            postAction(`/admin/partner-requests/${currentId}/reject`, {
                reason,
                message
            }, d => {
                setLoading(btn, false, '<i class="fa-solid fa-times"></i> Rejeter la demande');
                showToast(d.message, 'warning');
                closeModal('modalReject');
                removeCard(currentId);
                updateKpi('pending', -1);
                updateKpi('rejected', 1);
            }, () => setLoading(btn, false, '<i class="fa-solid fa-times"></i> Rejeter la demande'));
        }

        // ─── Delete ───────────────────────────────────────────────────────────────────
        function deleteRequest(id) {
            if (!confirm('Supprimer définitivement cette demande ?')) return;
            fetch(`/admin/partner-requests/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(d => {
                showToast(d.message, d.success ? 'success' : 'error');
                if (d.success) removeCard(id);
            });
        }

        // ─── Map ──────────────────────────────────────────────────────────────────────
        function viewOnMap(lat, lng, name) {
            if (!lat || !lng) {
                showToast('Coordonnées non disponibles', 'warning');
                return;
            }
            window.open(`https://www.google.com/maps?q=${lat},${lng}&z=16`, '_blank');
        }

        // ─── DOM helpers ─────────────────────────────────────────────────────────────
        function removeCard(id) {
            const el = document.querySelector(`[data-req-id="${id}"]`);
            if (el) {
                el.style.opacity = '0';
                el.style.transition = 'opacity .3s';
                setTimeout(() => el.remove(), 300);
            }
        }

        function updateKpi(key, delta) {
            const el = document.getElementById('kpi-' + key);
            if (el) el.textContent = Math.max(0, parseInt(el.textContent) + delta);
            const badge = document.getElementById('badge-' + key);
            if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent) + delta);
            const alert = document.getElementById('alert-count');
            if (alert && key === 'pending') alert.textContent = Math.max(0, parseInt(alert.textContent) + delta);
        }

        // ─── Modal ────────────────────────────────────────────────────────────────────
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.onclick = e => {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal(btn.dataset.modalClose);
                };
            });
            document.querySelectorAll('.modal-overlay').forEach(o => {
                o.addEventListener('click', e => {
                    if (e.target === o) closeModal(o.id);
                });
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        });

        // ─── Utilities ────────────────────────────────────────────────────────────────
        function postAction(url, body, onSuccess, onError) {
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
                    else {
                        showToast(d.message || 'Erreur', 'error');
                        onError && onError();
                    }
                })
                .catch(() => {
                    showToast('Erreur réseau', 'error');
                    onError && onError();
                });
        }

        function setLoading(btn, loading, label) {
            btn.disabled = loading;
            btn.innerHTML = loading ? '<i class="fa-solid fa-spinner fa-spin"></i> ' + label : label;
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
            }, 3500);
        }
    </script>
@endpush

@section('content')
    <main class="page-content">

        {{-- ── Header ── --}}
        <div class="page-hdr">
            <h1>Demandes partenaires</h1>
            <p>Gérez les inscriptions de stations et garages depuis le site web.</p>
        </div>

        {{-- ── KPIs cliquables ── --}}
        <div class="kpi-row">
            @foreach ([['pending', 'En attente', '#FEF3C7', 'var(--warning)', 'fa-clock', 'warning'], ['contacted', 'Contactées', '#DBEAFE', 'var(--info)', 'fa-phone', 'info'], ['approved', 'Approuvées', '#D1FAE5', 'var(--success)', 'fa-check', 'success'], ['rejected', 'Rejetées', '#FEE2E2', 'var(--danger)', 'fa-times', 'danger']] as [$key, $label, $bg, $color, $icon, $badgeColor])
                <div class="kpi-mini {{ $status === $key ? 'kpi-active' : '' }}" onclick="switchTab('{{ $key }}')">
                    <div class="kpi-mini-icon" style="background:{{ $bg }}">
                        <i class="fa-solid {{ $icon }}" style="color:{{ $color }}"></i>
                    </div>
                    <div>
                        <div class="kpi-mini-val" id="kpi-{{ $key }}">{{ (int) ($kpis->$key ?? 0) }}</div>
                        <div class="kpi-mini-lbl">{{ $label }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Alerte si demandes en attente ── --}}
        @if (($kpis->pending ?? 0) > 0)
            <div class="alert-warn">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <strong><span id="alert-count">{{ (int) $kpis->pending }}</span> demande(s)</strong>
                    attendent votre traitement. Répondez dans les 48h pour maintenir la qualité de service.
                </span>
            </div>
        @endif

        {{-- ── Filtres rapides ── --}}
        <div class="filter-row">
            <select class="form-select" onchange="applyFilter('type', this.value)">
                <option value="">Tous les types</option>
                <option value="station" {{ $type === 'station' ? 'selected' : '' }}>Stations</option>
                <option value="garage" {{ $type === 'garage' ? 'selected' : '' }}>Garages</option>
            </select>
            <select class="form-select" onchange="applyFilter('city', this.value)">
                <option value="">Toutes les villes</option>
                @foreach ($cities as $c)
                    <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        {{-- ── Card principale ── --}}
        <div class="card">

            {{-- Tab nav --}}
            <div class="tab-nav">
                @foreach ([['pending', 'En attente', 'warning', true], ['contacted', 'Contactées', 'info', true], ['approved', 'Approuvées', null, false], ['rejected', 'Rejetées', null, false]] as [$key, $label, $badgeColor, $showBadge])
                    <button class="tab-btn {{ $status === $key ? 'active' : '' }}"
                        onclick="switchTab('{{ $key }}')">
                        {{ $label }}
                        @if ($showBadge && ($kpis->$key ?? 0) > 0)
                            <span class="nav-count" id="badge-{{ $key }}"
                                style="background:var(--{{ $badgeColor }})">{{ (int) $kpis->$key }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- ── PENDING / CONTACTED — Cards détaillées ── --}}
            @if (in_array($status, ['pending', 'contacted']))
                @if ($requests->isEmpty())
                    <div class="empty-state">
                        <i class="fa-solid fa-inbox"></i>
                        Aucune demande {{ $status === 'pending' ? 'en attente' : 'contactée' }}
                    </div>
                @else
                    <div class="req-wrap">
                        @foreach ($requests as $req)
                            <div class="req-card" data-req-id="{{ $req->id_demande }}">
                                <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">

                                    {{-- Icône --}}
                                    <div class="req-icon"
                                        style="background:{{ $req->type === 'station' ? 'var(--primary-light)' : '#EDE9FE' }}">
                                        <i class="fa-solid {{ $req->type === 'station' ? 'fa-gas-pump' : 'fa-wrench' }}"
                                            style="color:{{ $req->type === 'station' ? 'var(--primary)' : 'var(--purple)' }}"></i>
                                    </div>

                                    <div style="flex:1;min-width:220px">

                                        {{-- Titre + badges ── --}}
                                        <div
                                            style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px">
                                            <span class="req-name">{{ $req->business_name }}</span>
                                            <span
                                                class="badge {{ $req->type === 'station' ? 'badge-station' : 'badge-garage' }}">
                                                <i class="fa-solid {{ $req->type === 'station' ? 'fa-gas-pump' : 'fa-wrench' }}"
                                                    style="font-size:9px"></i>
                                                {{ ucfirst($req->type) }}
                                            </span>
                                            <span class="badge badge-{{ $req->status }}">
                                                <i class="fa-solid {{ $req->status === 'pending' ? 'fa-clock' : 'fa-phone' }}"
                                                    style="font-size:9px"></i>
                                                {{ $req->status === 'pending' ? 'En attente' : 'Contacté' }}
                                            </span>
                                            <span style="font-size:12px;color:var(--text-muted);margin-left:auto">
                                                {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}
                                            </span>
                                        </div>

                                        {{-- Infos contact ── --}}
                                        <div class="req-meta">
                                            <div class="req-meta-item">
                                                <i class="fa-solid fa-user"></i>
                                                <strong>{{ $req->contact_name }}</strong>
                                            </div>
                                            <div class="req-meta-item">
                                                <i class="fa-solid fa-phone"></i>
                                                <a href="tel:{{ $req->contact_phone }}"
                                                    style="color:inherit;text-decoration:none">{{ $req->contact_phone }}</a>
                                            </div>
                                            @if ($req->contact_email)
                                                <div class="req-meta-item">
                                                    <i class="fa-solid fa-envelope"></i>
                                                    <a href="mailto:{{ $req->contact_email }}"
                                                        style="color:inherit;text-decoration:none">{{ $req->contact_email }}</a>
                                                </div>
                                            @endif
                                            <div class="req-meta-item">
                                                <i class="fa-solid fa-location-dot"></i>
                                                {{ $req->address }}, {{ $req->city }}
                                            </div>
                                        </div>

                                        {{-- Message ── --}}
                                        @if ($req->message)
                                            <div class="req-message">
                                                <i class="fa-solid fa-comment"
                                                    style="margin-right:6px"></i>"{{ $req->message }}"
                                            </div>
                                        @endif

                                        {{-- Note admin si contacté ── --}}
                                        @if ($req->status === 'contacted' && $req->admin_notes)
                                            <div
                                                style="background:#DBEAFE;padding:8px 12px;border-radius:8px;font-size:12.5px;color:#1D4ED8;margin-bottom:10px">
                                                <i class="fa-solid fa-note-sticky"
                                                    style="margin-right:5px"></i>{{ $req->admin_notes }}
                                            </div>
                                        @endif

                                        {{-- Boutons ── --}}
                                        <div class="req-actions">
                                            <button class="btn btn-success btn-sm"
                                                onclick="approveRequest({{ $req->id_demande }})">
                                                <i class="fa-solid fa-check"></i> Approuver
                                            </button>
                                            @if ($req->status === 'pending')
                                                <button class="btn btn-info btn-sm"
                                                    onclick="contactRequest({{ $req->id_demande }})">
                                                    <i class="fa-solid fa-phone"></i> Marquer contacté
                                                </button>
                                            @endif
                                            @if ($req->latitude && $req->longitude)
                                                <button class="btn btn-secondary btn-sm"
                                                    onclick="viewOnMap({{ $req->latitude }}, {{ $req->longitude }}, '{{ addslashes($req->business_name) }}')">
                                                    <i class="fa-solid fa-map-location-dot"></i> Carte
                                                </button>
                                            @endif
                                            <button class="btn btn-danger btn-sm"
                                                onclick="rejectRequest({{ $req->id_demande }})">
                                                <i class="fa-solid fa-times"></i> Rejeter
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ── APPROVED / REJECTED — Table ── --}}
            @else
                @if ($requests->isEmpty())
                    <div class="empty-state">
                        <i class="fa-solid fa-folder-open"></i>
                        Aucune demande {{ $status === 'approved' ? 'approuvée' : 'rejetée' }}
                    </div>
                @else
                    <div class="tbl-wrap">
                        <table class="data-tbl">
                            <thead>
                                <tr>
                                    <th>Établissement</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Ville</th>
                                    @if ($status === 'approved')
                                        <th>Plan</th>
                                        <th>Approuvé le</th>
                                    @else
                                        <th>Raison</th>
                                        <th>Rejeté le</th>
                                    @endif
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    <tr data-req-id="{{ $req->id_demande }}">
                                        <td class="fw-600">{{ $req->business_name }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $req->type === 'station' ? 'badge-station' : 'badge-garage' }}">
                                                {{ ucfirst($req->type) }}
                                            </span>
                                        </td>
                                        <td style="color:var(--text-muted)">
                                            <div>{{ $req->contact_name }}</div>
                                            <div style="font-size:12px">{{ $req->contact_phone }}</div>
                                        </td>
                                        <td>{{ $req->city }}</td>

                                        @if ($status === 'approved')
                                            <td>
                                                @php
                                                    $tbl = $req->type === 'station' ? 'stations' : 'garages';
                                                    $plan =
                                                        \Illuminate\Support\Facades\DB::table($tbl)
                                                            ->where('name', $req->business_name)
                                                            ->value('subscription_type') ?? '—';
                                                @endphp
                                                @if ($plan === 'premium')
                                                    <span class="badge badge-premium">Premium</span>
                                                @elseif($plan === 'pro')
                                                    <span class="badge badge-pro">Pro</span>
                                                @else
                                                    <span class="badge badge-free">{{ $plan }}</span>
                                                @endif
                                            </td>
                                            <td style="color:var(--text-muted);font-size:13px">
                                                {{ $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->locale('fr')->isoFormat('D MMM YYYY') : '—' }}
                                            </td>
                                        @else
                                            <td style="color:var(--danger);font-size:12.5px;max-width:220px">
                                                {{ $req->admin_notes ? \Illuminate\Support\Str::limit($req->admin_notes, 80) : '—' }}
                                            </td>
                                            <td style="color:var(--text-muted);font-size:13px">
                                                {{ $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->locale('fr')->isoFormat('D MMM YYYY') : '—' }}
                                            </td>
                                        @endif

                                        <td>
                                            <button class="btn btn-secondary btn-sm"
                                                onclick="deleteRequest({{ $req->id_demande }})" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            {{-- Pagination --}}
            @if ($requests->hasPages())
                <div class="pagi-bar">
                    <span class="pagi-info">
                        {{ $requests->firstItem() }}–{{ $requests->lastItem() }} sur {{ $requests->total() }} demandes
                    </span>
                    <div class="pagi">
                        @if ($requests->onFirstPage())
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                    style="font-size:11px"></i></span>
                        @else
                            <a class="pagi-btn" href="{{ $requests->previousPageUrl() }}"><i
                                    class="fa-solid fa-chevron-left" style="font-size:11px"></i></a>
                        @endif
                        @foreach ($requests->getUrlRange(max(1, $requests->currentPage() - 2), min($requests->lastPage(), $requests->currentPage() + 2)) as $page => $url)
                            @if ($page == $requests->currentPage())
                                <span class="pagi-btn active">{{ $page }}</span>
                            @else
                                <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($requests->hasMorePages())
                            <a class="pagi-btn" href="{{ $requests->nextPageUrl() }}"><i
                                    class="fa-solid fa-chevron-right" style="font-size:11px"></i></a>
                        @else
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                    style="font-size:11px"></i></span>
                        @endif
                    </div>
                </div>
            @endif

        </div>{{-- /card --}}

    </main>

    {{-- ══ Modal Approuver ══ --}}
    <div class="modal-overlay" id="modalApprove">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-check" style="color:var(--success)"></i> Approuver la demande</h5>
                <button class="modal-close" data-modal-close="modalApprove">✕</button>
            </div>
            <div class="modal-body">
                <div class="callout-success">
                    <i class="fa-solid fa-circle-info" style="margin-top:1px;flex-shrink:0"></i>
                    <span>Cette action créera le compte professionnel et activera l'établissement sur la plateforme.</span>
                </div>
                <div>
                    <label class="form-label">Plan à attribuer *</label>
                    <select id="approvePlan" class="form-select">
                        <option value="free">Gratuit — accès de base</option>
                        <option value="pro">Pro — 12 500 FCFA/mois</option>
                        <option value="premium">Premium — 32 500 FCFA/mois</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Email du compte pro *</label>
                    <input type="email" id="approveEmail" class="form-control" placeholder="contact@etablissement.ci">
                    <p style="font-size:11.5px;color:var(--text-muted);margin:5px 0 0">Les identifiants seront envoyés à
                        cet email.</p>
                </div>
                <div>
                    <label class="form-label">Note interne (optionnelle)</label>
                    <textarea id="approveNotes" class="form-control" rows="2" placeholder="Visible uniquement par les admins..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalApprove">Annuler</button>
                <button id="btnApprove" class="btn btn-success" onclick="confirmApprove()">
                    <i class="fa-solid fa-check"></i> Confirmer l'approbation
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Modal Rejeter ══ --}}
    <div class="modal-overlay" id="modalReject">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-times" style="color:var(--danger)"></i> Rejeter la demande</h5>
                <button class="modal-close" data-modal-close="modalReject">✕</button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="form-label">Raison du rejet *</label>
                    <select id="rejectReason" class="form-select">
                        <option value="">— Sélectionner une raison —</option>
                        <option value="Informations incomplètes">Informations incomplètes</option>
                        <option value="Zone géographique non couverte">Zone géographique non couverte</option>
                        <option value="Établissement non conforme">Établissement non conforme</option>
                        <option value="Doublon détecté">Doublon détecté</option>
                        <option value="Coordonnées invalides">Coordonnées invalides</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Message au demandeur (optionnel)</label>
                    <textarea id="rejectMessage" class="form-control" rows="3"
                        placeholder="Ce message sera inclus dans l'email de notification..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalReject">Annuler</button>
                <button id="btnReject" class="btn btn-danger" onclick="confirmReject()">
                    <i class="fa-solid fa-times"></i> Rejeter la demande
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
