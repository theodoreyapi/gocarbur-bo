@extends('layouts.master', ['title' => 'Promotions', 'subTitle' => 'Promotions'])

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
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 20px;
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
            transition: transform .15s, box-shadow .15s;
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
            font-size: 21px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .kpi-mini-lbl {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
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
            transition: border-color .15s;
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

        .data-tbl tr.expired {
            opacity: .6;
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

        .badge-active {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-upcoming {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .badge-expired {
            background: #F1F5F9;
            color: #64748B;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-discount {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-offre_speciale {
            background: #DBEAFE;
            color: var(--info);
        }

        .badge-service_gratuit {
            background: #D1FAE5;
            color: var(--success);
        }

        .badge-cadeau {
            background: #EDE9FE;
            color: var(--purple);
        }

        .badge-autre {
            background: #F1F5F9;
            color: #64748B;
        }

        .badge-push-yes {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-push-no {
            background: #F1F5F9;
            color: #94A3B8;
        }

        /* Remise display */
        .discount-val {
            font-weight: 700;
            font-size: 13.5px;
        }

        /* Dropdown */
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
            min-width: 185px;
            z-index: 200;
            padding: 6px 0;
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
            text-decoration: none;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
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

        /* Buttons */
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

        .btn-sm {
            padding: 5px 11px;
            font-size: 12px;
            border-radius: 7px;
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
            max-width: 580px;
            overflow: hidden;
            position: relative;
            z-index: 9001;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
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
            overflow-y: auto;
            flex: 1;
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
            flex-shrink: 0;
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
            transition: border-color .15s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .toggle {
            position: relative;
            display: inline-flex;
            width: 40px;
            height: 22px;
            cursor: pointer;
        }

        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: #CBD5E1;
            border-radius: 99px;
            transition: .3s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .3s;
        }

        .toggle input:checked+.toggle-slider {
            background: var(--primary);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(18px);
        }

        .push-box {
            padding: 14px;
            background: var(--bg);
            border-radius: 9px;
            border: 1px solid var(--border);
        }

        .push-box-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
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
        let editingId = null;

        // Données pour les selects du modal
        const STATIONS = @json($stations);
        const GARAGES = @json($garages);

        const TYPE_LABELS = {
            discount: 'Réduction %',
            offre_speciale: 'Offre spéciale',
            service_gratuit: 'Service gratuit',
            cadeau: 'Cadeau',
            autre: 'Autre',
        };

        // ─── Filtres ─────────────────────────────────────────────────────────────────
        let searchTimer;

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const st = document.getElementById('filterStatus').value;
            const t = document.getElementById('filterType').value;
            const et = document.getElementById('filterEntity').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            st ? url.searchParams.set('status', st) : url.searchParams.delete('status');
            t ? url.searchParams.set('type', t) : url.searchParams.delete('type');
            et ? url.searchParams.set('entity_type', et) : url.searchParams.delete('entity_type');
            url.searchParams.delete('page');
            window.location.href = url;
        }
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 380);
        });

        function switchKpi(s) {
            const url = new URL(window.location.href);
            s ? url.searchParams.set('status', s) : url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url;
        }

        // ─── Modal Promo ─────────────────────────────────────────────────────────────
        function openPromoModal(id = null) {
            editingId = id;
            resetForm();

            if (id) {
                document.getElementById('modalPromoTitle').textContent = 'Modifier la promotion';
                fetch(`/admin/promotions/${id}`, {
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
                        const p = d.data;
                        document.getElementById('promoTitle').value = p.title;
                        document.getElementById('promoDesc').value = p.description || '';
                        document.getElementById('promoType').value = p.type;
                        document.getElementById('promoEntityType').value = p.entity_type;
                        document.getElementById('promoStartsAt').value = p.starts_at?.substring(0, 10) || '';
                        document.getElementById('promoEndsAt').value = p.ends_at?.substring(0, 10) || '';
                        document.getElementById('promoPct').value = p.discount_percent || '';
                        document.getElementById('promoAmt').value = p.discount_amount || '';
                        document.getElementById('promoRadius').value = p.notification_radius_km || 5;
                        document.getElementById('promoPush').checked = !!p.send_push_notification;
                        document.getElementById('promoActive').checked = !!p.is_active;
                        updateEntityList(p.entity_type, p.promotable_id);
                        updateDiscountFields(p.type);
                    });
            } else {
                document.getElementById('modalPromoTitle').textContent = 'Créer une promotion';
            }

            openModal('modalPromo');
        }

        function editPromo(id) {
            openPromoModal(id);
        }

        function resetForm() {
            ['promoTitle', 'promoDesc', 'promoPct', 'promoAmt', 'promoStartsAt', 'promoEndsAt'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('promoType').value = '';
            document.getElementById('promoEntityType').value = '';
            document.getElementById('promoRadius').value = 5;
            document.getElementById('promoPush').checked = false;
            document.getElementById('promoActive').checked = true;
            document.getElementById('estabSelect').innerHTML = '<option value="">— Choisir le type d\'abord —</option>';
            updateDiscountFields('');
        }

        function updateEntityList(type, selectedId = null) {
            const sel = document.getElementById('estabSelect');
            const list = type === 'station' ? STATIONS : type === 'garage' ? GARAGES : [];
            const idField = type === 'station' ? 'id_station' : 'id_garage';

            if (!list.length) {
                sel.innerHTML = '<option value="">— Choisir le type d\'abord —</option>';
                return;
            }

            sel.innerHTML = '<option value="">— Sélectionner —</option>' +
                list.map(e => `<option value="${e[idField]}" ${e[idField] == selectedId ? 'selected' : ''}>
            ${e.name} · ${e.city}
        </option>`).join('');
        }

        function updateDiscountFields(type) {
            const pctWrap = document.getElementById('discountPctWrap');
            const amtWrap = document.getElementById('discountAmtWrap');
            pctWrap.style.display = type === 'discount' ? 'block' : 'none';
            amtWrap.style.display = (type === 'autre' || type === '') ? 'block' : 'none';
            if (type === 'offre_speciale' || type === 'service_gratuit' || type === 'cadeau') {
                pctWrap.style.display = 'none';
                amtWrap.style.display = 'none';
            }
        }

        function savePromo() {
            const entityType = document.getElementById('promoEntityType').value;
            const entityId = document.getElementById('estabSelect').value;

            const data = {
                title: document.getElementById('promoTitle').value.trim(),
                description: document.getElementById('promoDesc').value.trim(),
                entity_type: entityType,
                entity_id: parseInt(entityId),
                type: document.getElementById('promoType').value,
                discount_percent: document.getElementById('promoPct').value || null,
                discount_amount: document.getElementById('promoAmt').value || null,
                starts_at: document.getElementById('promoStartsAt').value,
                ends_at: document.getElementById('promoEndsAt').value,
                send_push_notification: document.getElementById('promoPush').checked,
                notification_radius_km: parseInt(document.getElementById('promoRadius').value),
                is_active: document.getElementById('promoActive').checked,
            };

            if (!data.title || !data.entity_type || !data.entity_id || !data.type || !data.starts_at || !data.ends_at) {
                showToast('Veuillez remplir tous les champs obligatoires', 'error');
                return;
            }

            const url = editingId ? `/admin/promotions/${editingId}` : '/admin/promotions';
            const method = editingId ? 'PUT' : 'POST';

            fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data),
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        showToast(d.message, 'success');
                        closeModal('modalPromo');
                        setTimeout(() => location.reload(), 700);
                    } else {
                        showToast(d.message || 'Erreur', 'error');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Actions ─────────────────────────────────────────────────────────────────
        function togglePromo(id) {
            fetch(`/admin/promotions/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => {
                    showToast(d.message, d.is_active ? 'success' : 'warning');
                    if (d.success) {
                        const badge = document.querySelector(`tr[data-promo-id="${id}"] .status-badge`);
                        if (badge) {
                            badge.className = 'badge ' + (d.is_active ? 'badge-active' : 'badge-inactive');
                            badge.textContent = d.is_active ? 'Active' : 'Désactivée';
                        }
                    }
                });
        }

        function sendPushPromo(id) {
            if (!confirm('Envoyer une notification push aux utilisateurs proches ?')) return;
            fetch(`/admin/promotions/${id}/send-push`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => showToast(d.message, d.success ? 'success' : 'error'));
        }

        function duplicatePromo(id) {
            fetch(`/admin/promotions/${id}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => {
                    showToast(d.message, 'info');
                    if (d.success) setTimeout(() => location.reload(), 1000);
                });
        }

        function deletePromo(id) {
            if (!confirm('Supprimer cette promotion ?')) return;
            fetch(`/admin/promotions/${id}`, {
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
                        const row = document.querySelector(`tr[data-promo-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transition = 'opacity .3s';
                            setTimeout(() => row.remove(), 300);
                        }
                    }
                });
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
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.onclick = e => {
                    e.preventDefault();
                    e.stopPropagation();
                    openPromoModal();
                };
            });
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
        document.addEventListener('click', e => {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
            const btn = e.target.closest('.dropdown > .btn');
            if (btn) {
                btn.parentElement.classList.toggle('open');
                e.stopPropagation();
            }
        });

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
            <div>
                <h1>Promotions</h1>
                <p>Gérez les offres spéciales des stations et garages partenaires.</p>
            </div>
            <button class="btn btn-primary" data-modal-open="modalPromo">
                <i class="fa-solid fa-plus"></i> Créer une promotion
            </button>
        </div>

        {{-- ── KPIs ── --}}
        @php
            $kpiConfig = [
                ['active', 'Actives', '#D1FAE5', 'var(--success)', 'fa-tag', 'active'],
                ['upcoming', 'À venir', '#FEF3C7', 'var(--warning)', 'fa-clock', 'upcoming'],
                ['expired', 'Expirées', '#F1F5F9', 'var(--text-muted)', 'fa-clock-rotate-left', 'expired'],
                ['stations', 'Stations', '#FFF0EB', 'var(--primary)', 'fa-gas-pump', 'station'],
                ['garages', 'Garages', '#EDE9FE', 'var(--purple)', 'fa-wrench', 'garage'],
            ];
        @endphp
        <div class="kpi-row">
            @foreach ($kpiConfig as [$key, $label, $bg, $color, $icon, $filterVal])
                <div class="kpi-mini {{ in_array($status, [$filterVal, $key]) ? 'kpi-active' : '' }}"
                    onclick="switchKpi('{{ in_array($key, ['stations', 'garages']) ? '' : $key }}')">
                    <div class="kpi-mini-icon" style="background:{{ $bg }}">
                        <i class="fa-solid {{ $icon }}" style="color:{{ $color }}"></i>
                    </div>
                    <div>
                        <div class="kpi-mini-val">{{ $kpis[$key] ?? 0 }}</div>
                        <div class="kpi-mini-lbl">{{ $label }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Table ── --}}
        <div class="card">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Titre, établissement..."
                        value="{{ $search }}">
                </div>
                <select id="filterStatus" class="form-select" style="width:140px" onchange="applyFilters()">
                    <option value="">Tous statuts</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="upcoming" {{ $status === 'upcoming' ? 'selected' : '' }}>À venir</option>
                    <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Expirée</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Désactivée</option>
                </select>
                <select id="filterType" class="form-select" style="width:170px" onchange="applyFilters()">
                    <option value="">Tous les types</option>
                    <option value="discount" {{ $type === 'discount' ? 'selected' : '' }}>Réduction %</option>
                    <option value="offre_speciale" {{ $type === 'offre_speciale' ? 'selected' : '' }}>Offre spéciale
                    </option>
                    <option value="service_gratuit" {{ $type === 'service_gratuit' ? 'selected' : '' }}>Service gratuit
                    </option>
                    <option value="cadeau" {{ $type === 'cadeau' ? 'selected' : '' }}>Cadeau</option>
                    <option value="autre" {{ $type === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
                <select id="filterEntity" class="form-select" style="width:160px" onchange="applyFilters()">
                    <option value="">Tous établissements</option>
                    <option value="station" {{ $entityType === 'station' ? 'selected' : '' }}>Stations seulement</option>
                    <option value="garage" {{ $entityType === 'garage' ? 'selected' : '' }}>Garages seulement</option>
                </select>
            </div>

            <div class="tbl-wrap">
                <table class="data-tbl">
                    <thead>
                        <tr>
                            <th>Promotion</th>
                            <th>Établissement</th>
                            <th>Type</th>
                            <th>Remise</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Push</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promo)
                            @php
                                $isExpired = $promo->status === 'expired';
                                $statusLabels = [
                                    'active' => 'Active',
                                    'upcoming' => 'À venir',
                                    'expired' => 'Expirée',
                                    'inactive' => 'Désactivée',
                                ];
                                $statusBadge = [
                                    'active' => 'badge-active',
                                    'upcoming' => 'badge-upcoming',
                                    'expired' => 'badge-expired',
                                    'inactive' => 'badge-inactive',
                                ];
                                $typeLabel =
                                    [
                                        'discount' => 'Réduction %',
                                        'offre_speciale' => 'Offre spéciale',
                                        'service_gratuit' => 'Service gratuit',
                                        'cadeau' => 'Cadeau',
                                        'autre' => 'Autre',
                                    ][$promo->type] ?? $promo->type;
                            @endphp
                            <tr data-promo-id="{{ $promo->id_promotion }}" class="{{ $isExpired ? 'expired' : '' }}">
                                <td>
                                    <div class="fw-600" style="font-size:13px;max-width:220px">{{ $promo->title }}</div>
                                    @if ($promo->description)
                                        <div
                                            style="font-size:11px;color:var(--text-muted);margin-top:2px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                            {{ $promo->description }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($promo->entity)
                                        <span
                                            style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600">
                                            <i class="fa-solid {{ $promo->entity_type === 'station' ? 'fa-gas-pump' : 'fa-wrench' }}"
                                                style="color:{{ $promo->entity_type === 'station' ? 'var(--primary)' : 'var(--purple)' }};font-size:10px"></i>
                                            {{ $promo->entity->name }}
                                        </span>
                                        <div style="font-size:11px;color:var(--text-muted)">{{ $promo->entity->city }}
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);font-size:12px">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $promo->type }}">{{ $typeLabel }}</span>
                                </td>
                                <td>
                                    @if ($promo->discount_percent)
                                        <span class="discount-val"
                                            style="color:var(--warning)">-{{ $promo->discount_percent }}%</span>
                                    @elseif($promo->discount_amount)
                                        <span class="discount-val"
                                            style="color:var(--primary)">-{{ number_format($promo->discount_amount, 0, ',', ' ') }}
                                            F</span>
                                    @else
                                        <span class="discount-val" style="color:var(--success)">Gratuit</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size:12px;white-space:nowrap">
                                        {{ \Carbon\Carbon::parse($promo->starts_at)->format('d M') }}
                                        →
                                        {{ \Carbon\Carbon::parse($promo->ends_at)->format('d M Y') }}
                                    </div>
                                    <div style="font-size:11px;margin-top:2px">
                                        @if ($promo->status === 'active')
                                            <span style="color:var(--success)">
                                                {{ \Carbon\Carbon::parse($promo->ends_at)->diffInDays(now()) }} j restants
                                            </span>
                                        @elseif($promo->status === 'upcoming')
                                            <span style="color:var(--info)">
                                                dans {{ now()->diffInDays($promo->starts_at) }} j
                                            </span>
                                        @elseif($promo->status === 'expired')
                                            <span style="color:var(--danger)">
                                                Expirée il y a
                                                {{ \Carbon\Carbon::parse($promo->ends_at)->diffInDays(now()) }} j
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-badge {{ $statusBadge[$promo->status] ?? 'badge-expired' }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                        {{ $statusLabels[$promo->status] ?? $promo->status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($promo->send_push_notification)
                                        <span class="badge badge-push-yes">
                                            <i class="fa-solid fa-paper-plane" style="font-size:9px"></i> Oui
                                        </span>
                                    @else
                                        <span class="badge badge-push-no">
                                            <i class="fa-solid fa-times" style="font-size:9px"></i> Non
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if (!$isExpired)
                                                <button class="dropdown-item"
                                                    onclick="editPromo({{ $promo->id_promotion }})">
                                                    <i class="fa-solid fa-pen"></i> Modifier
                                                </button>
                                                <button class="dropdown-item"
                                                    onclick="togglePromo({{ $promo->id_promotion }})">
                                                    <i class="fa-solid fa-{{ $promo->is_active ? 'pause' : 'play' }}"></i>
                                                    {{ $promo->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                @if ($promo->status !== 'expired')
                                                    <button class="dropdown-item"
                                                        onclick="sendPushPromo({{ $promo->id_promotion }})">
                                                        <i class="fa-solid fa-paper-plane"></i>
                                                        {{ $promo->send_push_notification ? 'Renvoyer push' : 'Envoyer push' }}
                                                    </button>
                                                @endif
                                            @endif
                                            <button class="dropdown-item"
                                                onclick="duplicatePromo({{ $promo->id_promotion }})">
                                                <i class="fa-solid fa-copy"></i> Dupliquer
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                onclick="deletePromo({{ $promo->id_promotion }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-tag"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucune promotion trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagi-bar">
                <span class="pagi-info">{{ number_format($total, 0, ',', ' ') }} promotion(s) au total</span>
                @if ($promotions->hasPages())
                    <div class="pagi">
                        @if ($promotions->onFirstPage())
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                    style="font-size:11px"></i></span>
                        @else
                            <a class="pagi-btn" href="{{ $promotions->previousPageUrl() }}"><i
                                    class="fa-solid fa-chevron-left" style="font-size:11px"></i></a>
                        @endif
                        @foreach ($promotions->getUrlRange(max(1, $promotions->currentPage() - 2), min($promotions->lastPage(), $promotions->currentPage() + 2)) as $page => $url)
                            @if ($page == $promotions->currentPage())
                                <span class="pagi-btn active">{{ $page }}</span>
                            @else
                                <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($promotions->hasMorePages())
                            <a class="pagi-btn" href="{{ $promotions->nextPageUrl() }}"><i
                                    class="fa-solid fa-chevron-right" style="font-size:11px"></i></a>
                        @else
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                    style="font-size:11px"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </main>

    {{-- ══ Modal Promotion ══ --}}
    <div class="modal-overlay" id="modalPromo">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-tag" style="color:var(--primary)"></i>
                    <span id="modalPromoTitle">Créer une promotion</span>
                </h5>
                <button class="modal-close" data-modal-close="modalPromo">✕</button>
            </div>
            <div class="modal-body">
                {{-- Titre --}}
                <div>
                    <label class="form-label">Titre *</label>
                    <input type="text" id="promoTitle" class="form-control"
                        placeholder="Ex: Lavage offert pour tout plein de 20L+">
                </div>
                {{-- Description --}}
                <div>
                    <label class="form-label">Description</label>
                    <textarea id="promoDesc" class="form-control" rows="2" placeholder="Détails de l'offre..."></textarea>
                </div>
                {{-- Type établ + Établissement --}}
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Type d'établissement *</label>
                        <select id="promoEntityType" class="form-select" style="width:100%"
                            onchange="updateEntityList(this.value)">
                            <option value="">— Sélectionner —</option>
                            <option value="station">Station-service</option>
                            <option value="garage">Garage</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Établissement *</label>
                        <select id="estabSelect" class="form-select" style="width:100%">
                            <option value="">— Choisir le type d'abord —</option>
                        </select>
                    </div>
                    {{-- Type promo --}}
                    <div>
                        <label class="form-label">Type de promotion *</label>
                        <select id="promoType" class="form-select" style="width:100%"
                            onchange="updateDiscountFields(this.value)">
                            <option value="">— Sélectionner —</option>
                            <option value="discount">Réduction %</option>
                            <option value="offre_speciale">Offre spéciale</option>
                            <option value="service_gratuit">Service gratuit</option>
                            <option value="cadeau">Cadeau</option>
                            <option value="autre">Autre (montant fixe)</option>
                        </select>
                    </div>
                    {{-- Valeur remise --}}
                    <div id="discountPctWrap" style="display:none">
                        <label class="form-label">Pourcentage de réduction (%)</label>
                        <input type="number" id="promoPct" class="form-control" placeholder="Ex: 20" min="0"
                            max="100" step="0.01">
                    </div>
                    <div id="discountAmtWrap" style="display:none">
                        <label class="form-label">Montant fixe (FCFA)</label>
                        <input type="number" id="promoAmt" class="form-control" placeholder="Ex: 2000"
                            min="0">
                    </div>
                    {{-- Dates --}}
                    <div>
                        <label class="form-label">Date de début *</label>
                        <input type="date" id="promoStartsAt" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Date de fin *</label>
                        <input type="date" id="promoEndsAt" class="form-control">
                    </div>
                </div>
                {{-- Push notification --}}
                <div class="push-box">
                    <div class="push-box-header">
                        <label class="toggle">
                            <input type="checkbox" id="promoPush">
                            <span class="toggle-slider"></span>
                        </label>
                        <div>
                            <div class="fw-600" style="font-size:13px">Envoyer une notification push</div>
                            <div style="font-size:12px;color:var(--text-muted)">Notifier les utilisateurs proches de
                                l'établissement</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:13px;white-space:nowrap">Rayon :</span>
                        <select id="promoRadius" class="form-select" style="width:110px">
                            <option value="5">5 km</option>
                            <option value="10">10 km</option>
                            <option value="20">20 km</option>
                            <option value="50">50 km</option>
                        </select>
                    </div>
                </div>
                {{-- Active --}}
                <div
                    style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--bg);border-radius:9px">
                    <label class="toggle">
                        <input type="checkbox" id="promoActive" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="fw-600" style="font-size:13px">Activer immédiatement</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalPromo">Annuler</button>
                <button class="btn btn-primary" onclick="savePromo()">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
