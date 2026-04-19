@extends('layouts.master', ['title' => 'Utilisateurs', 'subTitle' => 'Utilisateurs'])

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

        /* ── Page header ── */
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

        /* ── KPI mini ── */
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
            transition: transform .15s, box-shadow .15s;
            animation: fadeUp .4s ease both;
        }

        .kpi-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .09);
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

        /* ── Card ── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeUp .4s ease .1s both;
        }

        /* ── Filter bar ── */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: #FAFBFC;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
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

        .search-input {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13px;
            outline: none;
            background: #fff;
            transition: border-color .15s;
        }

        .search-input:focus {
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

        /* ── Table ── */
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
            padding: 13px 14px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        .data-tbl tr:last-child td {
            border-bottom: none;
        }

        .data-tbl tbody tr {
            transition: background .12s;
        }

        .data-tbl tbody tr:hover td {
            background: #F8FAFF;
        }

        /* ── User avatar ── */
        .u-avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }

        /* ── Badges ── */
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

        .badge-premium {
            background: #FFF0EB;
            color: var(--primary);
        }

        .badge-free {
            background: #F1F5F9;
            color: #64748B;
        }

        .badge-active {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-suspended {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* ── Dropdown ── */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            min-width: 170px;
            z-index: 100;
            padding: 6px 0;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-menu,
        .dropdown.open .dropdown-menu {
            display: block;
            animation: dropIn .15s ease;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 13px;
            color: var(--text);
            cursor: pointer;
            text-decoration: none;
            transition: background .1s;
        }

        .dropdown-item:hover {
            background: #F1F5F9;
        }

        .dropdown-item.text-danger {
            color: var(--danger);
        }

        .dropdown-item.text-danger:hover {
            background: #FEE2E2;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        /* ── Buttons ── */
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
            background: #e5561e;
        }

        .btn-secondary {
            background: #F1F5F9;
            color: var(--text);
        }

        .btn-secondary:hover {
            background: #E2E8F0;
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

        .btn-sm {
            padding: 5px 11px;
            font-size: 12px;
            border-radius: 7px;
        }

        .w-100 {
            width: 100%;
            justify-content: center;
        }

        /* ── Pagination ── */
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

        /* ── Modal ── */
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
            max-width: 520px;
            overflow: hidden;
            position: relative;
            z-index: 9001;
        }

        .modal-box.lg {
            max-width: 640px;
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
        }

        .modal-footer {
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #FAFBFC;
        }

        /* ── Form elements ── */
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

        /* ── Info grid in modal ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .info-cell {
            background: var(--bg);
            border-radius: 9px;
            padding: 12px 14px;
        }

        .info-cell-lbl {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        .info-cell-val {
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
        }

        /* ── Toast ── */
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

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes dropIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(50px);
                opacity: 0;
            }

            to {
                transform: none;
                opacity: 1;
            }
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const CSRF = document.querySelector('meta[name=csrf-token]')?.content;

        // ─── Recherche & filtres (debounce) ─────────────────────────────────────────
        let searchTimer;

        function filterUsers(val) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => applyFilters(), 350);
        }

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const p = document.getElementById('filterPlan').value;
            const st = document.getElementById('filterStatus').value;
            const c = document.getElementById('filterCity').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            p ? url.searchParams.set('plan', p) : url.searchParams.delete('plan');
            st ? url.searchParams.set('status', st) : url.searchParams.delete('status');
            c ? url.searchParams.set('city', c) : url.searchParams.delete('city');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        // ─── Select all ─────────────────────────────────────────────────────────────
        function selectAll(cb) {
            document.querySelectorAll('#usersTable tbody input[type=checkbox]').forEach(c => c.checked = cb.checked);
        }

        // ─── View user (modal) ──────────────────────────────────────────────────────
        function viewUser(id) {
            fetch(`/admin/users/${id}`, {
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
                    const u = d.data;
                    const initials = u.name.split(' ').map(w => w[0]?.toUpperCase() || '').slice(0, 2).join('');
                    document.getElementById('viewAvatar').textContent = initials;
                    document.getElementById('viewName').textContent = u.name;
                    document.getElementById('viewSub').textContent = (u.phone || '—') + ' · ' + (u.city || '—');
                    document.getElementById('viewPlanBadge').innerHTML = u.subscription_type === 'premium' ?
                        `<span class="badge badge-premium"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span>` :
                        `<span class="badge badge-free">Gratuit</span>`;
                    document.getElementById('viewStatusBadge').innerHTML = u.is_active ?
                        `<span class="badge badge-active"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span>` :
                        `<span class="badge badge-suspended"><i class="fa-solid fa-circle" style="font-size:7px"></i> Suspendu</span>`;
                    document.getElementById('viewVehicles').textContent = u.vehicle_count;
                    document.getElementById('viewDocuments').textContent = u.document_count;
                    document.getElementById('viewExpiry').textContent = u.subscription?.expires_at ?
                        new Date(u.subscription.expires_at).toLocaleDateString('fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) :
                        'Aucun';
                    document.getElementById('viewCreated').textContent = new Date(u.created_at).toLocaleDateString(
                        'fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                    // Boutons d'action selon statut
                    document.getElementById('viewBtnSuspend').style.display = u.is_active ? 'flex' : 'none';
                    document.getElementById('viewBtnReact').style.display = !u.is_active ? 'flex' : 'none';
                    document.getElementById('viewBtnSuspend').onclick = () => suspendUser(u.id_user_carbu);
                    document.getElementById('viewBtnReact').onclick = () => reactivateUser(u.id_user_carbu);
                    document.getElementById('viewBtnPremium').onclick = () => grantPremium(u.id_user_carbu);
                    openModal('modalViewUser');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Actions ────────────────────────────────────────────────────────────────
        function grantPremium(id) {
            postAction(`/admin/users/${id}/grant-premium`, {}, d => {
                showToast(d.message, 'success');
                closeModal('modalViewUser');
                setTimeout(() => location.reload(), 800);
            });
        }

        function suspendUser(id) {
            confirmAction('Suspendre cet utilisateur ? Il sera déconnecté immédiatement.', () => {
                postAction(`/admin/users/${id}/suspend`, {}, d => {
                    showToast(d.message, 'warning');
                    closeModal('modalViewUser');
                    updateRowStatus(id, false);
                });
            });
        }

        function reactivateUser(id) {
            postAction(`/admin/users/${id}/reactivate`, {}, d => {
                showToast(d.message, 'success');
                closeModal('modalViewUser');
                updateRowStatus(id, true);
            });
        }

        function deleteUser(id) {
            confirmAction('Supprimer définitivement cet utilisateur ? Cette action est irréversible.', () => {
                fetch(`/admin/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                    })
                    .then(r => r.json())
                    .then(d => {
                        showToast(d.message, d.success ? 'success' : 'error');
                        if (d.success) {
                            const row = document.querySelector(`tr[data-user-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transition = 'opacity .3s';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
            });
        }

        function saveUser() {
            const name = document.getElementById('addName').value.trim();
            const phone = document.getElementById('addPhone').value.trim();
            const email = document.getElementById('addEmail').value.trim();
            const city = document.getElementById('addCity').value;
            const plan = document.getElementById('addPlan').value;
            const pass = document.getElementById('addPass').value.trim();

            if (!name || !phone || !pass) {
                showToast('Nom, téléphone et mot de passe requis', 'error');
                return;
            }

            postAction('/admin/users', {
                name,
                phone,
                email,
                city,
                subscription_type: plan,
                password: pass
            }, d => {
                showToast(d.message, 'success');
                closeModal('modalAddUser');
                setTimeout(() => location.reload(), 800);
            });
        }

        function exportUsers() {
            showToast('Export CSV en cours...', 'info');
            window.location.href = '/admin/users/export' + window.location.search;
        }

        // ─── Mise à jour DOM row ─────────────────────────────────────────────────────
        function updateRowStatus(id, isActive) {
            const row = document.querySelector(`tr[data-user-id="${id}"]`);
            if (!row) return;
            const badge = row.querySelector('.status-badge');
            if (badge) {
                badge.className = 'badge ' + (isActive ? 'badge-active' : 'badge-suspended');
                badge.innerHTML =
                    `<i class="fa-solid fa-circle" style="font-size:7px"></i> ${isActive ? 'Actif' : 'Suspendu'}`;
            }
        }

        // ─── Helpers ────────────────────────────────────────────────────────────────
        function postAction(url, body, onSuccess) {
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body),
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) onSuccess(d);
                    else showToast(d.message || 'Erreur', 'error');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
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

        // ── Gestion des modals — approche directe sans conflit ──────────────────────

        // Clic sur le fond de l'overlay (hors .modal-box)
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // Boutons d'ouverture : on surcharge via onclick pour bypasser le master
        document.querySelectorAll('[data-modal-open]').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                openModal(this.dataset.modalOpen);
            };
        });

        // Boutons de fermeture : même chose
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal(this.dataset.modalClose);
            };
        });

        // Touche Échap
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
            }
        });

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

        // ─── Dropdown toggle ────────────────────────────────────────────────────────
        document.addEventListener('click', e => {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
            const btn = e.target.closest('.dropdown > button');
            if (btn) {
                btn.parentElement.classList.toggle('open');
                e.stopPropagation();
            }
        });
    </script>
@endpush

@section('content')
    <main class="page-content">

        {{-- ── Page Header ── --}}
        <div class="page-hdr">
            <div>
                <h1>Utilisateurs</h1>
                <p>Gérez tous les comptes utilisateurs de la plateforme.</p>
            </div>
            <div style="display:flex;gap:10px">
                <button class="btn btn-secondary" onclick="exportUsers()">
                    <i class="fa-solid fa-download"></i> Export CSV
                </button>
                <button class="btn btn-primary" data-modal-open="modalAddUser">
                    <i class="fa-solid fa-user-plus"></i> Ajouter
                </button>
            </div>
        </div>

        {{-- ── KPI mini ── --}}
        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:var(--primary-light)">
                    <i class="fa-solid fa-users" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['total'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Total</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#D1FAE5">
                    <i class="fa-solid fa-crown" style="color:var(--success)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['premium'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Premium</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#DBEAFE">
                    <i class="fa-solid fa-user-check" style="color:var(--info)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['active'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Actifs</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEE2E2">
                    <i class="fa-solid fa-user-slash" style="color:var(--danger)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['suspended'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Suspendus</div>
                </div>
            </div>
        </div>

        {{-- ── Main card ── --}}
        <div class="card">

            {{-- Filtres --}}
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Nom, téléphone, email..."
                        value="{{ $search }}" oninput="filterUsers(this.value)">
                </div>
                <select id="filterPlan" class="form-select" style="width:150px" onchange="applyFilters()">
                    <option value="">Tous les plans</option>
                    <option value="free" {{ $plan === 'free' ? 'selected' : '' }}>Gratuit</option>
                    <option value="premium" {{ $plan === 'premium' ? 'selected' : '' }}>Premium</option>
                </select>
                <select id="filterStatus" class="form-select" style="width:150px" onchange="applyFilters()">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                </select>
                <select id="filterCity" class="form-select" style="width:160px" onchange="applyFilters()">
                    <option value="">Toutes les villes</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Table --}}
            <div class="tbl-wrap">
                <table class="data-tbl" id="usersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" onchange="selectAll(this)"></th>
                            <th>Utilisateur</th>
                            <th>Téléphone</th>
                            <th>Ville</th>
                            <th>Véhicules</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $avatarGradients = [
                                'linear-gradient(135deg,#FF6B35,#FF9B6B)',
                                'linear-gradient(135deg,#3B82F6,#1D4ED8)',
                                'linear-gradient(135deg,#10B981,#059669)',
                                'linear-gradient(135deg,#8B5CF6,#6D28D9)',
                                'linear-gradient(135deg,#F59E0B,#D97706)',
                                'linear-gradient(135deg,#EF4444,#B91C1C)',
                            ];
                        @endphp
                        @forelse($users as $i => $user)
                            @php
                                $initials = collect(explode(' ', $user->name))
                                    ->map(fn($w) => strtoupper($w[0] ?? ''))
                                    ->take(2)
                                    ->implode('');
                                $bg = $avatarGradients[$i % count($avatarGradients)];
                                $vehicles = $vehicleCounts[$user->id_user_carbu] ?? 0;
                            @endphp
                            <tr data-user-id="{{ $user->id_user_carbu }}">
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="u-avatar" style="background:{{ $bg }}">{{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $user->name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">#{{ $user->id_user_carbu }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color:var(--text-muted)">{{ $user->phone }}</td>
                                <td>{{ $user->city ?? '—' }}</td>
                                <td style="font-weight:600;text-align:center">{{ $vehicles }}</td>
                                <td>
                                    @if ($user->subscription_type === 'premium')
                                        <span class="badge badge-premium">
                                            <i class="fa-solid fa-crown" style="font-size:9px"></i> Premium
                                        </span>
                                    @else
                                        <span class="badge badge-free">Gratuit</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge status-badge {{ $user->is_active ? 'badge-active' : 'badge-suspended' }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                        {{ $user->is_active ? 'Actif' : 'Suspendu' }}
                                    </span>
                                </td>
                                <td style="color:var(--text-muted);font-size:12.5px">
                                    {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '—' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" onclick="viewUser({{ $user->id_user_carbu }})">
                                                <i class="fa-solid fa-eye"></i> Voir
                                            </a>
                                            @if ($user->subscription_type !== 'premium')
                                                <a class="dropdown-item"
                                                    onclick="grantPremium({{ $user->id_user_carbu }})">
                                                    <i class="fa-solid fa-crown"></i> Accorder Premium
                                                </a>
                                            @endif
                                            @if ($user->is_active)
                                                <a class="dropdown-item"
                                                    onclick="suspendUser({{ $user->id_user_carbu }})">
                                                    <i class="fa-solid fa-ban"></i> Suspendre
                                                </a>
                                            @else
                                                <a class="dropdown-item"
                                                    onclick="reactivateUser({{ $user->id_user_carbu }})">
                                                    <i class="fa-solid fa-rotate-left"></i> Réactiver
                                                </a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger"
                                                onclick="deleteUser({{ $user->id_user_carbu }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-users-slash"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucun utilisateur trouvé
                                    @if ($search || $plan || $status || $city)
                                        — <a href="{{ route('admin.users.index') }}"
                                            style="color:var(--primary)">réinitialiser les filtres</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagi-bar">
                <span class="pagi-info">
                    Affichage {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} utilisateurs
                </span>
                <div class="pagi">
                    {{-- Précédent --}}
                    @if ($users->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $users->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></a>
                    @endif

                    {{-- Pages --}}
                    @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                        @if ($page == $users->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($users->lastPage() > $users->currentPage() + 2)
                        <span class="pagi-btn disabled">…</span>
                        <a class="pagi-btn" href="{{ $users->url($users->lastPage()) }}">{{ $users->lastPage() }}</a>
                    @endif

                    {{-- Suivant --}}
                    @if ($users->hasMorePages())
                        <a class="pagi-btn" href="{{ $users->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>

    </main>

    {{-- ══ Modal — Ajouter utilisateur ══ --}}
    <div class="modal-overlay" id="modalAddUser">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-user-plus" style="color:var(--primary)"></i> Ajouter un utilisateur</h5>
                <button class="modal-close" data-modal-close="modalAddUser">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label class="form-label">Nom complet *</label>
                        <input type="text" id="addName" class="form-control" placeholder="Ex: Kouassi Aya">
                    </div>
                    <div>
                        <label class="form-label">Téléphone *</label>
                        <input type="text" id="addPhone" class="form-control" placeholder="+225 07 ...">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" id="addEmail" class="form-control" placeholder="email@exemple.com">
                    </div>
                    <div>
                        <label class="form-label">Ville</label>
                        <select id="addCity" class="form-select" style="width:100%">
                            <option value="">— Choisir —</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="Abidjan">Abidjan</option>
                            <option value="Bouaké">Bouaké</option>
                            <option value="Daloa">Daloa</option>
                            <option value="Yamoussoukro">Yamoussoukro</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Plan</label>
                        <select id="addPlan" class="form-select" style="width:100%">
                            <option value="free">Gratuit</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" id="addPass" class="form-control" placeholder="Min. 6 caractères">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalAddUser">Annuler</button>
                <button class="btn btn-primary" onclick="saveUser()">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Modal — Voir utilisateur ══ --}}
    <div class="modal-overlay" id="modalViewUser">
        <div class="modal-box lg">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-user" style="color:var(--primary)"></i> Profil utilisateur</h5>
                <button class="modal-close" data-modal-close="modalViewUser">✕</button>
            </div>
            <div class="modal-body">

                {{-- Header profil --}}
                <div
                    style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
                    <div id="viewAvatar" class="u-avatar"
                        style="width:54px;height:54px;font-size:18px;background:linear-gradient(135deg,#FF6B35,#FF9B6B)">
                    </div>
                    <div>
                        <div id="viewName" style="font-size:18px;font-weight:700;color:var(--text)"></div>
                        <div id="viewSub" style="color:var(--text-muted);font-size:13px;margin-top:2px"></div>
                        <div style="margin-top:8px;display:flex;gap:6px" id="viewBadges">
                            <span id="viewPlanBadge"></span>
                            <span id="viewStatusBadge"></span>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-lbl">Véhicules</div>
                        <div class="info-cell-val" id="viewVehicles">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-lbl">Documents</div>
                        <div class="info-cell-val" id="viewDocuments">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-lbl">Premium expire</div>
                        <div class="info-cell-val" style="font-size:14px" id="viewExpiry">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-lbl">Inscrit le</div>
                        <div class="info-cell-val" style="font-size:14px" id="viewCreated">—</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:10px;margin-top:4px">
                    <button id="viewBtnPremium" class="btn btn-success w-100">
                        <i class="fa-solid fa-crown"></i> Accorder Premium
                    </button>
                    <button id="viewBtnSuspend" class="btn btn-danger w-100">
                        <i class="fa-solid fa-ban"></i> Suspendre
                    </button>
                    <button id="viewBtnReact" class="btn btn-secondary w-100" style="display:none">
                        <i class="fa-solid fa-rotate-left"></i> Réactiver
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
