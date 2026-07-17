@extends('layouts.master', ['title' => 'Propriétaires', 'subTitle' => 'Propriétaires'])

@push('csss')
    <style>
        :root {
            --primary: #16A34A;
            --primary-light: #DCFCE7;
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

        .badge-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-approved {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-suspended {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-rejected {
            background: #F1F5F9;
            color: #64748B;
        }

        .count-chip {
            background: #F1F5F9;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text);
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            color: var(--primary);
            font-size: 13px;
        }

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
            min-width: 190px;
            z-index: 200;
            padding: 6px 0;
            overflow: hidden;
        }

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
            border: none;
            background: none;
            width: 100%;
            text-align: left;
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
            background: #15803D;
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
            max-width: 560px;
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

        .fw-600 {
            font-weight: 600;
        }

        .biz-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px;
        }

        .biz-row:last-child {
            border-bottom: none;
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

        @keyframes dropIn {
            from {
                opacity: 0;
                transform: translateY(-6px)
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

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const st = document.getElementById('filterStatus').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            st ? url.searchParams.set('status', st) : url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 380);
        });

        function viewOwner(id) {
            fetch(`/admin/owners/${id}`, {
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
                    const o = d.data;
                    document.getElementById('oName').textContent = o.name;
                    document.getElementById('oCompany').textContent = o.company_name || '—';
                    document.getElementById('oEmail').textContent = o.email;
                    document.getElementById('oPhone').textContent = o.phone || '—';
                    document.getElementById('oRccm').textContent = o.rccm || '—';
                    document.getElementById('oStatus').textContent = ({
                        pending: 'En attente',
                        approved: 'Approuvé',
                        suspended: 'Suspendu',
                        rejected: 'Rejeté'
                    })[o.status];

                    const bizHtml = [
                            ...(o.stations || []).map(s =>
                                `<div class="biz-row"><span><i class="fa-solid fa-gas-pump" style="color:#FF6B35;margin-right:6px"></i>${s.name}</span><span style="color:var(--text-muted)">${s.city}</span></div>`
                                ),
                            ...(o.garages || []).map(g =>
                                `<div class="biz-row"><span><i class="fa-solid fa-wrench" style="color:#0EA5E9;margin-right:6px"></i>${g.name}</span><span style="color:var(--text-muted)">${g.city}</span></div>`
                                ),
                        ].join('') ||
                        '<p style="font-size:13px;color:var(--text-muted);margin:0">Aucun établissement enregistré</p>';
                    document.getElementById('oBusinesses').innerHTML = bizHtml;

                    document.getElementById('oBtnSuspend').textContent = o.status === 'suspended' ?
                        'Réactiver le compte' : 'Suspendre le compte';
                    document.getElementById('oBtnSuspend').className = 'btn ' + (o.status === 'suspended' ?
                        'btn-success' : 'btn-secondary');
                    document.getElementById('oBtnSuspend').onclick = () => suspendOwner(id);

                    const approveBtn = document.getElementById('oBtnApprove');
                    approveBtn.style.display = o.status === 'pending' ? 'inline-flex' : 'none';
                    approveBtn.onclick = () => approveOwner(id);

                    openModal('modalViewOwner');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function approveOwner(id) {
            postAction(`/admin/owners/${id}/approve`, {}, d => {
                showToast(d.message, 'success');
                closeModal('modalViewOwner');
                setTimeout(() => location.reload(), 800);
            });
        }

        function suspendOwner(id) {
            postAction(`/admin/owners/${id}/suspend`, {}, d => {
                showToast(d.message, d.status === 'suspended' ? 'warning' : 'success');
                closeModal('modalViewOwner');
                setTimeout(() => location.reload(), 800);
            });
        }

        function deleteOwner(id) {
            confirmAction('Supprimer ce propriétaire ? Ses établissements seront aussi supprimés.', () => {
                fetch(`/admin/owners/${id}`, {
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
                            const row = document.querySelector(`tr[data-owner-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transition = 'opacity .3s';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
            });
        }

        // ─── Ajouter / Modifier (CRUD) ──────────────────────────────────────────
        let editingOwnerId = null;

        function openAddOwner() {
            editingOwnerId = null;
            document.getElementById('formModalTitle').textContent = 'Ajouter un propriétaire';
            document.getElementById('fName').value = '';
            document.getElementById('fEmail').value = '';
            document.getElementById('fPassword').value = '';
            document.getElementById('fPassword').placeholder = 'Mot de passe';
            document.getElementById('fPasswordHint').style.display = 'none';
            document.getElementById('fPhone').value = '';
            document.getElementById('fCompany').value = '';
            document.getElementById('fRccm').value = '';
            document.getElementById('fStatus').value = 'pending';
            openModal('modalFormOwner');
        }

        function editOwner(id) {
            fetch(`/admin/owners/${id}`, {
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
                    const o = d.data;
                    editingOwnerId = id;
                    document.getElementById('formModalTitle').textContent = 'Modifier le propriétaire';
                    document.getElementById('fName').value = o.name;
                    document.getElementById('fEmail').value = o.email;
                    document.getElementById('fPassword').value = '';
                    document.getElementById('fPassword').placeholder = 'Laisser vide pour ne pas changer';
                    document.getElementById('fPasswordHint').style.display = 'block';
                    document.getElementById('fPhone').value = o.phone || '';
                    document.getElementById('fCompany').value = o.company_name || '';
                    document.getElementById('fRccm').value = o.rccm || '';
                    document.getElementById('fStatus').value = o.status;
                    openModal('modalFormOwner');
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function saveOwnerForm() {
            const data = {
                name: document.getElementById('fName').value.trim(),
                email: document.getElementById('fEmail').value.trim(),
                password: document.getElementById('fPassword').value,
                phone: document.getElementById('fPhone').value.trim(),
                company_name: document.getElementById('fCompany').value.trim(),
                rccm: document.getElementById('fRccm').value.trim(),
                status: document.getElementById('fStatus').value,
            };

            if (!data.name || !data.email) {
                showToast('Nom et email requis', 'error');
                return;
            }
            if (!editingOwnerId && !data.password) {
                showToast('Mot de passe requis à la création', 'error');
                return;
            }

            if (editingOwnerId) {
                fetch(`/admin/owners/${editingOwnerId}`, {
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
                        closeModal('modalFormOwner');
                        setTimeout(() => location.reload(), 800);
                    })
                    .catch(() => showToast('Erreur réseau', 'error'));
            } else {
                postAction('/admin/owners', data, d => {
                    showToast(d.message, 'success');
                    closeModal('modalFormOwner');
                    setTimeout(() => location.reload(), 800);
                });
            }
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
        document.addEventListener('click', e => {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
            const btn = e.target.closest('.dropdown > .btn');
            if (btn) {
                btn.parentElement.classList.toggle('open');
                e.stopPropagation();
            }
        });
    </script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-hdr">
            <div>
                <h1>Propriétaires</h1>
                <p>Gérez les comptes propriétaires de stations et garages partenaires.</p>
            </div>
            <button class="btn btn-primary" onclick="openAddOwner()">
                <i class="fa-solid fa-plus"></i> Ajouter propriétaire
            </button>
        </div>

        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:var(--primary-light)"><i class="fa-solid fa-user-tie"
                        style="color:var(--primary)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['total'] }}</div>
                    <div class="kpi-mini-lbl">Total propriétaires</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#D1FAE5"><i class="fa-solid fa-check"
                        style="color:var(--success)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['approved'] }}</div>
                    <div class="kpi-mini-lbl">Approuvés</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEF3C7"><i class="fa-solid fa-clock"
                        style="color:var(--warning)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['pending'] }}</div>
                    <div class="kpi-mini-lbl">En attente</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEE2E2"><i class="fa-solid fa-ban"
                        style="color:var(--danger)"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['suspended'] }}</div>
                    <div class="kpi-mini-lbl">Suspendus</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Nom, entreprise, email..."
                        value="{{ $search }}">
                </div>
                <select id="filterStatus" class="form-select" style="width:160px" onchange="applyFilters()">
                    <option value="">Tous statuts</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvés</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetés</option>
                </select>
            </div>

            <div class="tbl-wrap">
                <table class="data-tbl">
                    <thead>
                        <tr>
                            <th>Propriétaire</th>
                            <th>Entreprise</th>
                            <th>Téléphone</th>
                            <th>Établissements</th>
                            <th>Statut</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $owner)
                            <tr data-owner-id="{{ $owner->id_station_owner }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="avatar-circle">{{ strtoupper(substr($owner->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="fw-600">{{ $owner->name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">{{ $owner->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $owner->company_name ?? '—' }}</td>
                                <td>{{ $owner->phone ?? '—' }}</td>
                                <td>
                                    <span class="count-chip"><i class="fa-solid fa-gas-pump" style="font-size:9px"></i>
                                        {{ $owner->stations_count }}</span>
                                    <span class="count-chip"><i class="fa-solid fa-wrench" style="font-size:9px"></i>
                                        {{ $owner->garages_count }}</span>
                                </td>
                                <td>
                                    @if ($owner->status === 'approved')
                                        <span class="badge badge-approved"><i class="fa-solid fa-check"
                                                style="font-size:9px"></i> Approuvé</span>
                                    @elseif($owner->status === 'pending')
                                        <span class="badge badge-pending"><i class="fa-solid fa-clock"
                                                style="font-size:9px"></i> En attente</span>
                                    @elseif($owner->status === 'suspended')
                                        <span class="badge badge-suspended"><i class="fa-solid fa-ban"
                                                style="font-size:9px"></i> Suspendu</span>
                                    @else
                                        <span class="badge badge-rejected">Rejeté</span>
                                    @endif
                                </td>
                                <td style="font-size:12.5px;color:var(--text-muted)">
                                    {{ $owner->last_login_at ? $owner->last_login_at->diffForHumans() : 'Jamais' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary"><i
                                                class="fa-solid fa-ellipsis"></i></button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item"
                                                onclick="viewOwner({{ $owner->id_station_owner }})">
                                                <i class="fa-solid fa-eye"></i> Voir détail
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="editOwner({{ $owner->id_station_owner }})">
                                                <i class="fa-solid fa-pen"></i> Modifier
                                            </button>
                                            @if ($owner->status === 'pending')
                                                <button class="dropdown-item"
                                                    onclick="approveOwner({{ $owner->id_station_owner }})">
                                                    <i class="fa-solid fa-check"></i> Approuver
                                                </button>
                                            @endif
                                            <button class="dropdown-item"
                                                onclick="suspendOwner({{ $owner->id_station_owner }})">
                                                <i
                                                    class="fa-solid fa-{{ $owner->status === 'suspended' ? 'rotate-right' : 'ban' }}"></i>
                                                {{ $owner->status === 'suspended' ? 'Réactiver' : 'Suspendre' }}
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                onclick="deleteOwner({{ $owner->id_station_owner }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-user-tie"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucun propriétaire trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagi-bar">
                <span class="pagi-info">
                    Affichage {{ $owners->firstItem() ?? 0 }}–{{ $owners->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} propriétaires
                </span>
                <div class="pagi">
                    @if ($owners->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $owners->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></a>
                    @endif

                    @foreach ($owners->getUrlRange(max(1, $owners->currentPage() - 2), min($owners->lastPage(), $owners->currentPage() + 2)) as $page => $url)
                        @if ($page == $owners->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($owners->hasMorePages())
                        <a class="pagi-btn" href="{{ $owners->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- ══ Modal — Voir propriétaire ══ --}}
    <div class="modal-overlay" id="modalViewOwner">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-user-tie" style="color:var(--primary)"></i> Détail propriétaire</h5>
                <button class="modal-close" data-modal-close="modalViewOwner">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                    @foreach ([['oName', 'Nom'], ['oCompany', 'Entreprise'], ['oEmail', 'Email'], ['oPhone', 'Téléphone'], ['oRccm', 'RCCM'], ['oStatus', 'Statut']] as [$elId, $label])
                        <div style="background:var(--bg);border-radius:9px;padding:10px 12px">
                            <div
                                style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">
                                {{ $label }}</div>
                            <div id="{{ $elId }}" style="font-size:13.5px;font-weight:600;color:var(--text)">—
                            </div>
                        </div>
                    @endforeach
                </div>
                <div>
                    <div
                        style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                        Établissements</div>
                    <div id="oBusinesses"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="oBtnApprove" class="btn btn-success" style="display:none"><i class="fa-solid fa-check"></i>
                    Approuver</button>
                <button id="oBtnSuspend" class="btn btn-secondary"></button>
            </div>
        </div>
    </div>

    {{-- ══ Modal — Ajouter / Modifier propriétaire ══ --}}
    <div class="modal-overlay" id="modalFormOwner">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-user-tie" style="color:var(--primary)"></i> <span id="formModalTitle">Ajouter
                        un propriétaire</span></h5>
                <button class="modal-close" data-modal-close="modalFormOwner">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div style="grid-column:span 2">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" id="fName" class="form-control" placeholder="Ex: Koffi Yao">
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" id="fEmail" class="form-control" placeholder="email@exemple.com">
                    </div>
                    <div>
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" id="fPassword" class="form-control" placeholder="Mot de passe">
                        <div id="fPasswordHint"
                            style="display:none;font-size:11px;color:var(--text-muted);margin-top:4px">Laisser vide pour ne
                            pas changer</div>
                    </div>
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="text" id="fPhone" class="form-control" placeholder="+225 ...">
                    </div>
                    <div>
                        <label class="form-label">Statut</label>
                        <select id="fStatus" class="form-select" style="width:100%">
                            <option value="pending">En attente</option>
                            <option value="approved">Approuvé</option>
                            <option value="suspended">Suspendu</option>
                            <option value="rejected">Rejeté</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Nom de la société</label>
                        <input type="text" id="fCompany" class="form-control" placeholder="Ex: Corlay SARL">
                    </div>
                    <div>
                        <label class="form-label">RCCM</label>
                        <input type="text" id="fRccm" class="form-control" placeholder="CI-ABJ-...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalFormOwner">Annuler</button>
                <button class="btn btn-primary" onclick="saveOwnerForm()"><i class="fa-solid fa-check"></i>
                    Enregistrer</button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
