@extends('layouts.master', ['title' => 'Garages & services', 'subTitle' => 'Garages & services'])

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
            transition: transform .15s, box-shadow .15s;
        }

        .kpi-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .09);
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

        /* Type breakdown row */
        .type-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .type-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow);
        }

        .type-card-count {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: var(--text);
        }

        .type-card-lbl {
            font-size: 11px;
            color: var(--text-muted);
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

        .badge-active {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-type {
            background: #F1F5F9;
            color: var(--text);
        }

        .garage-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .star-row {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .star-row i {
            color: var(--warning);
            font-size: 12px;
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
            max-width: 520px;
            overflow: hidden;
            position: relative;
            z-index: 9001;
        }

        .modal-box.md {
            max-width: 640px;
        }

        .modal-box.sm {
            max-width: 480px;
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
            max-height: 72vh;
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
            border-color: var(--purple);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .col-span-2 {
            grid-column: span 2;
        }

        /* Toggle */
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
            background: var(--purple);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(18px);
        }

        /* Service row in modal */
        .svc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: var(--bg);
            border-radius: 9px;
            border: 1px solid var(--border);
            margin-bottom: 8px;
        }

        .svc-name {
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
            width: 130px;
        }

        .svc-price {
            flex: 1;
            margin: 0 10px;
        }

        .svc-price input {
            font-size: 12px;
            padding: 5px 9px;
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

        // ─── Config types ─────────────────────────────────────────────────────────────
        const TYPE_CONFIG = {
            garage_general: {
                label: 'Garage général',
                icon: 'fa-car-wrench',
                bg: '#EDE9FE',
                color: '#8B5CF6'
            },
            centre_vidange: {
                label: 'Centre vidange',
                icon: 'fa-oil-can',
                bg: '#D1FAE5',
                color: '#10B981'
            },
            lavage_auto: {
                label: 'Lavage auto',
                icon: 'fa-car-wash',
                bg: '#DBEAFE',
                color: '#3B82F6'
            },
            pneus: {
                label: 'Pneus',
                icon: 'fa-circle',
                bg: '#FEF3C7',
                color: '#F59E0B'
            },
            batterie: {
                label: 'Batterie',
                icon: 'fa-battery-full',
                bg: '#FEF3C7',
                color: '#F59E0B'
            },
            climatisation: {
                label: 'Climatisation',
                icon: 'fa-snowflake',
                bg: '#DBEAFE',
                color: '#3B82F6'
            },
            electricite_auto: {
                label: 'Électricité auto',
                icon: 'fa-bolt',
                bg: '#FEE2E2',
                color: '#EF4444'
            },
            depannage: {
                label: 'Dépannage',
                icon: 'fa-truck-ramp-box',
                bg: '#FEE2E2',
                color: '#EF4444'
            },
            carrosserie: {
                label: 'Carrosserie',
                icon: 'fa-car-side',
                bg: '#EDE9FE',
                color: '#8B5CF6'
            },
            vitrage: {
                label: 'Vitrage',
                icon: 'fa-window-maximize',
                bg: '#DBEAFE',
                color: '#3B82F6'
            },
        };

        const SVC_LABELS = {
            vidange: 'Vidange',
            freins: 'Freins',
            pneus: 'Pneus',
            batterie: 'Batterie',
            climatisation: 'Climatisation',
            electricite: 'Électricité',
            carrosserie: 'Carrosserie',
            vitrage: 'Vitrage',
            courroie_distribution: 'Courroie',
            amortisseurs: 'Amortisseurs',
            echappement: 'Échappement',
            revision_complete: 'Révision complète',
            diagnostic_electronique: 'Diagnostic',
            depannage_route: 'Dépannage route',
            remorquage: 'Remorquage',
            lavage_interieur: 'Lavage intérieur',
            lavage_exterieur: 'Lavage extérieur',
            polissage: 'Polissage',
        };

        // ─── Filtres ─────────────────────────────────────────────────────────────────
        let searchTimer;

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const t = document.getElementById('filterType').value;
            const p = document.getElementById('filterPlan').value;
            const c = document.getElementById('filterCity').value;
            const st = document.getElementById('filterStatus').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
            t ? url.searchParams.set('type', t) : url.searchParams.delete('type');
            p ? url.searchParams.set('plan', p) : url.searchParams.delete('plan');
            c ? url.searchParams.set('city', c) : url.searchParams.delete('city');
            st ? url.searchParams.set('status', st) : url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 380);
        });

        function filterVerified() {
            const url = new URL(window.location.href);
            url.searchParams.has('verified') ? url.searchParams.delete('verified') : url.searchParams.set('verified', '1');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        // ─── View garage ─────────────────────────────────────────────────────────────
        let currentGarageId = null;

        function viewGarage(id) {
            currentGarageId = id;
            document.getElementById('viewLoading').style.display = 'flex';
            document.getElementById('viewContent').style.display = 'none';
            openModal('modalViewGarage');

            fetch(`/admin/garages/${id}`, {
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
                    const g = d.data;
                    const tc = TYPE_CONFIG[g.type] || {
                        label: g.type,
                        icon: 'fa-wrench',
                        bg: '#EDE9FE',
                        color: '#8B5CF6'
                    };

                    // Header
                    document.getElementById('vIcon').className = `fa-solid ${tc.icon}`;
                    document.getElementById('vIcon').style.color = tc.color;
                    document.getElementById('vIconWrap').style.background = tc.bg;
                    document.getElementById('vName').textContent = g.name;
                    document.getElementById('vTypeBadge').textContent = tc.label;
                    document.getElementById('vSubInfo').textContent = `${g.address} • ${g.city}`;

                    const planBadge = {
                        premium: 'badge-premium',
                        pro: 'badge-pro',
                        free: 'badge-free'
                    } [g.subscription_type] || 'badge-free';
                    document.getElementById('vPlanBadge').className = `badge ${planBadge}`;
                    document.getElementById('vPlanBadge').textContent = g.subscription_type.toUpperCase();
                    document.getElementById('vStatusBadge').className =
                        `badge ${g.is_active ? 'badge-active' : 'badge-inactive'}`;
                    document.getElementById('vStatusBadge').textContent = g.is_active ? 'Actif' : 'Désactivé';
                    document.getElementById('vVerifiedBadge').style.display = g.is_verified ? 'inline-flex' : 'none';

                    // Stats
                    document.getElementById('vRatingVal').textContent = g.rating > 0 ?
                        `${Number(g.rating).toFixed(1)}/5` : '—';
                    document.getElementById('vRatingCount').textContent = `(${g.rating_count} avis)`;
                    document.getElementById('vViews').textContent = Number(g.views_count).toLocaleString('fr');
                    document.getElementById('vPhone').textContent = g.phone || '—';
                    document.getElementById('vHours').textContent = g.is_open_24h ? '24h/24' : ((g.opens_at || '—') +
                        ' – ' + (g.closes_at || '—'));

                    // Stats mois
                    document.getElementById('vStatViews').textContent = d.data.stats_month?.view_profile?.count || 0;
                    document.getElementById('vStatCalls').textContent = d.data.stats_month?.call?.count || 0;
                    document.getElementById('vStatWhatsapp').textContent = d.data.stats_month?.whatsapp?.count || 0;
                    document.getElementById('vStatItinerary').textContent = d.data.stats_month?.itinerary?.count || 0;

                    // Services
                    const svcHtml = (d.data.services || []).length ?
                        d.data.services.map(s =>
                            `<span class="badge badge-type">${SVC_LABELS[s.service] || s.service}${s.price_range ? ' · <small>' + s.price_range + '</small>' : ''}</span>`
                            ).join(' ') :
                        '<span style="font-size:13px;color:var(--text-muted)">Aucun service enregistré</span>';
                    document.getElementById('vServices').innerHTML = svcHtml;

                    // Avis récents
                    const reviewsHtml = (d.data.recent_reviews || []).length ?
                        d.data.recent_reviews.map(r => `
                    <div style="padding:8px 0;border-bottom:1px solid var(--border)">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px">
                            <span style="font-size:13px;font-weight:600">${r.user_name}</span>
                            <span style="color:var(--warning);font-size:12px">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span>
                        </div>
                        <div style="font-size:12.5px;color:var(--text-muted)">${r.comment || '<em>Sans commentaire</em>'}</div>
                        ${!r.is_approved ? '<span style="font-size:11px;color:var(--warning)">En attente de modération</span>' : ''}
                    </div>`).join('') :
                        '<p style="font-size:13px;color:var(--text-muted);margin:0">Aucun avis</p>';
                    document.getElementById('vReviews').innerHTML = reviewsHtml;

                    // Boutons dynamiques
                    document.getElementById('vBtnVerify').textContent = g.is_verified ? '✕ Retirer badge' :
                    '✅ Vérifier';
                    document.getElementById('vBtnVerify').onclick = () => verifyGarage(id);
                    document.getElementById('vBtnToggle').textContent = g.is_active ? 'Désactiver' : 'Activer';
                    document.getElementById('vBtnToggle').className = 'btn ' + (g.is_active ? 'btn-secondary' :
                        'btn-success');
                    document.getElementById('vBtnToggle').onclick = () => toggleGarage(id);
                    document.getElementById('vBtnServices').onclick = () => {
                        closeModal('modalViewGarage');
                        openServicesModal(id, g.name, d.data.services);
                    };

                    document.getElementById('viewLoading').style.display = 'none';
                    document.getElementById('viewContent').style.display = 'block';
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Modal Services ───────────────────────────────────────────────────────────
        const ALL_SERVICES = ['vidange', 'freins', 'pneus', 'batterie', 'climatisation', 'electricite', 'carrosserie',
            'vitrage', 'courroie_distribution', 'amortisseurs', 'echappement', 'revision_complete',
            'diagnostic_electronique', 'depannage_route', 'remorquage', 'lavage_interieur', 'lavage_exterieur',
            'polissage'
        ];

        let currentServiceGarageId = null;

        function openServicesModal(id, name, existingServices) {
            currentServiceGarageId = id;
            document.getElementById('svcModalGarageName').textContent = name;

            const svcMap = {};
            (existingServices || []).forEach(s => {
                svcMap[s.service] = s.price_range || '';
            });

            const html = ALL_SERVICES.map(svc => `
        <div class="svc-row">
            <span class="svc-name">${SVC_LABELS[svc] || svc}</span>
            <div class="svc-price">
                <input type="text" class="form-control" id="svcprice_${svc}"
                       value="${svcMap[svc] || ''}" placeholder="Ex: 15 000 – 25 000 FCFA">
            </div>
            <label class="toggle" title="Activer ce service">
                <input type="checkbox" id="svccheck_${svc}" ${svcMap[svc] !== undefined || svcMap.hasOwnProperty(svc) ? 'checked' : ''}>
                <span class="toggle-slider"></span>
            </label>
        </div>`).join('');

            document.getElementById('svcList').innerHTML = html;

            // Remettre les checks corrects depuis la map
            Object.keys(svcMap).forEach(k => {
                const el = document.getElementById('svccheck_' + k);
                if (el) el.checked = true;
            });

            openModal('modalServices');
        }

        function manageServices(id) {
            fetch(`/admin/garages/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) openServicesModal(id, d.data.name, d.data.services);
                });
        }

        function saveServices() {
            const services = ALL_SERVICES
                .filter(svc => document.getElementById('svccheck_' + svc)?.checked)
                .map(svc => ({
                    service: svc,
                    price_range: document.getElementById('svcprice_' + svc)?.value.trim() || null,
                }));

            postAction(`/admin/garages/${currentServiceGarageId}/services`, {
                services
            }, d => {
                showToast(d.message, 'success');
                closeModal('modalServices');
            });
        }

        // ─── Actions ─────────────────────────────────────────────────────────────────
        function verifyGarage(id) {
            postAction(`/admin/garages/${id}/verify`, {}, d => {
                showToast(d.message, 'success');
                closeModal('modalViewGarage');
                updateRowVerified(id, d.is_verified);
            });
        }

        function toggleGarage(id) {
            postAction(`/admin/garages/${id}/toggle`, {}, d => {
                showToast(d.message, d.is_active ? 'success' : 'warning');
                closeModal('modalViewGarage');
                updateRowActive(id, d.is_active);
            });
        }

        function deleteGarage(id) {
            confirmAction('Supprimer ce garage définitivement ?', () => {
                fetch(`/admin/garages/${id}`, {
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
                            const row = document.querySelector(`tr[data-garage-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transition = 'opacity .3s';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
            });
        }

        function saveGarage() {
            const services = Array.from(document.querySelectorAll('#addServicesWrap input[type=checkbox]:checked'))
                .map(cb => cb.value);

            const data = {
                name: document.getElementById('addName').value.trim(),
                type: document.getElementById('addType').value,
                address: document.getElementById('addAddress').value.trim(),
                city: document.getElementById('addCity').value,
                latitude: document.getElementById('addLat').value,
                longitude: document.getElementById('addLng').value,
                phone: document.getElementById('addPhone').value.trim(),
                whatsapp: document.getElementById('addWhatsapp').value.trim(),
                opens_at: document.getElementById('addOpens').value,
                closes_at: document.getElementById('addCloses').value,
                is_open_24h: document.getElementById('add24h').checked,
                subscription_type: document.getElementById('addPlan').value,
                description: document.getElementById('addDescription').value.trim(),
                services,
            };

            if (!data.name || !data.type || !data.address || !data.city) {
                showToast('Nom, type, adresse et ville requis', 'error');
                return;
            }

            postAction('/admin/garages', data, d => {
                showToast(d.message, 'success');
                closeModal('modalAddGarage');
                setTimeout(() => location.reload(), 800);
            });
        }

        function exportGarages() {
            showToast('Export CSV en cours...', 'info');
            window.location.href = '/admin/garages/export' + window.location.search;
        }

        function selectAll(cb) {
            document.querySelectorAll('#garagesTable tbody input[type=checkbox]').forEach(c => c.checked = cb.checked);
        }

        // ─── DOM row updates ──────────────────────────────────────────────────────────
        function updateRowVerified(id, isVerified) {
            const row = document.querySelector(`tr[data-garage-id="${id}"]`);
            if (!row) return;
            const el = row.querySelector('.verified-badge');
            if (el) el.style.display = isVerified ? 'inline' : 'none';
        }

        function updateRowActive(id, isActive) {
            const row = document.querySelector(`tr[data-garage-id="${id}"]`);
            if (!row) return;
            const badge = row.querySelector('.status-badge');
            if (badge) {
                badge.className = 'badge status-badge ' + (isActive ? 'badge-active' : 'badge-inactive');
                badge.innerHTML =
                    `<i class="fa-solid fa-circle" style="font-size:7px"></i> ${isActive ? 'Actif' : 'Désactivé'}`;
            }
        }

        // ─── Modal helpers ────────────────────────────────────────────────────────────
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
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openModal(this.dataset.modalOpen);
                };
            });
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal(this.dataset.modalClose);
                };
            });
            document.querySelectorAll('.modal-overlay').forEach(o => {
                o.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(this.id);
                });
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        });

        // ─── Utilities ────────────────────────────────────────────────────────────────
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

        {{-- ── Header ── --}}
        <div class="page-hdr">
            <div>
                <h1>Garages & Services</h1>
                <p>Gérez tous les garages et services automobiles partenaires.</p>
            </div>
            <div style="display:flex;gap:10px">
                <button class="btn btn-secondary" onclick="exportGarages()">
                    <i class="fa-solid fa-download"></i> Export CSV
                </button>
                <button class="btn btn-primary" data-modal-open="modalAddGarage">
                    <i class="fa-solid fa-plus"></i> Ajouter garage
                </button>
            </div>
        </div>

        {{-- ── KPIs ── --}}
        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#EDE9FE">
                    <i class="fa-solid fa-wrench" style="color:var(--purple)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['total'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Total garages</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#D1FAE5">
                    <i class="fa-solid fa-shield-check" style="color:var(--success)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['verified'] }}</div>
                    <div class="kpi-mini-lbl">Vérifiés</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#DBEAFE">
                    <i class="fa-solid fa-crown" style="color:var(--info)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['pro'] }}</div>
                    <div class="kpi-mini-lbl">Pro / Premium</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEF3C7">
                    <i class="fa-solid fa-star" style="color:var(--warning)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['avg_rating'] }}</div>
                    <div class="kpi-mini-lbl">Note moyenne</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEE2E2">
                    <i class="fa-solid fa-ban" style="color:var(--danger)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['inactive'] }}</div>
                    <div class="kpi-mini-lbl">Désactivés</div>
                </div>
            </div>
        </div>

        {{-- ── Répartition par type ── --}}
        @php
            $typeIcons = [
                'garage_general' => ['fa-car-wrench', '#8B5CF6', '#EDE9FE'],
                'centre_vidange' => ['fa-oil-can', '#10B981', '#D1FAE5'],
                'lavage_auto' => ['fa-car-wash', '#3B82F6', '#DBEAFE'],
                'pneus' => ['fa-circle', '#F59E0B', '#FEF3C7'],
                'batterie' => ['fa-battery-full', '#F59E0B', '#FEF3C7'],
                'climatisation' => ['fa-snowflake', '#3B82F6', '#DBEAFE'],
                'electricite_auto' => ['fa-bolt', '#EF4444', '#FEE2E2'],
                'depannage' => ['fa-truck-ramp-box', '#6366F1', '#EDE9FE'],
                'carrosserie' => ['fa-car-side', '#8B5CF6', '#EDE9FE'],
                'vitrage' => ['fa-window-maximize', '#3B82F6', '#DBEAFE'],
            ];
            $typeLabels = [
                'garage_general' => 'Garage général',
                'centre_vidange' => 'Centre vidange',
                'lavage_auto' => 'Lavage auto',
                'pneus' => 'Pneus',
                'batterie' => 'Batterie',
                'climatisation' => 'Climatisation',
                'electricite_auto' => 'Électricité auto',
                'depannage' => 'Dépannage',
                'carrosserie' => 'Carrosserie',
                'vitrage' => 'Vitrage',
            ];
        @endphp
        <div class="type-row">
            @foreach ($typeIcons as $typeKey => [$icon, $color, $bg])
                @if ($byType->has($typeKey))
                    <div class="type-card">
                        <i class="fa-solid {{ $icon }}"
                            style="color:{{ $color }};font-size:18px;width:22px;text-align:center;flex-shrink:0"></i>
                        <div>
                            <div class="type-card-count">{{ $byType[$typeKey]->count }}</div>
                            <div class="type-card-lbl">{{ $typeLabels[$typeKey] ?? $typeKey }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- ── Table principale ── --}}
        <div class="card">

            {{-- Filtres --}}
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Nom, ville, type..."
                        value="{{ $search }}">
                </div>
                <select id="filterType" class="form-select" style="width:170px" onchange="applyFilters()">
                    <option value="">Tous les types</option>
                    @foreach ($typeLabels as $val => $lbl)
                        <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>{{ $lbl }}
                        </option>
                    @endforeach
                </select>
                <select id="filterPlan" class="form-select" style="width:140px" onchange="applyFilters()">
                    <option value="">Tous les plans</option>
                    <option value="free" {{ $plan === 'free' ? 'selected' : '' }}>Gratuit</option>
                    <option value="pro" {{ $plan === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="premium" {{ $plan === 'premium' ? 'selected' : '' }}>Premium</option>
                </select>
                <select id="filterCity" class="form-select" style="width:150px" onchange="applyFilters()">
                    <option value="">Toutes les villes</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}
                        </option>
                    @endforeach
                </select>
                <select id="filterStatus" class="form-select" style="width:140px" onchange="applyFilters()">
                    <option value="">Statut</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Désactivé</option>
                </select>
                <button class="btn btn-sm {{ $verified ? 'btn-success' : 'btn-secondary' }}" onclick="filterVerified()">
                    <i class="fa-solid fa-shield-check"></i> {{ $verified ? 'Tous' : 'Vérifiés' }}
                </button>
            </div>

            {{-- Table --}}
            <div class="tbl-wrap">
                <table class="data-tbl" id="garagesTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" onchange="selectAll(this)"></th>
                            <th>Garage</th>
                            <th>Type</th>
                            <th>Ville</th>
                            <th>Note</th>
                            <th>Avis</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Vues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($garages as $garage)
                            @php
                                $tc = $typeIcons[$garage->type] ?? ['fa-wrench', '#8B5CF6', '#EDE9FE'];
                                [$tIcon, $tColor, $tBg] = $tc;
                                $tLabel = $typeLabels[$garage->type] ?? $garage->type;
                            @endphp
                            <tr data-garage-id="{{ $garage->id_garage }}">
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="garage-icon" style="background:{{ $tBg }}">
                                            <i class="fa-solid {{ $tIcon }}"
                                                style="color:{{ $tColor }};font-size:15px"></i>
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $garage->name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">{{ $garage->address }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-type">{{ $tLabel }}</span></td>
                                <td>{{ $garage->city }}</td>
                                <td>
                                    @if ($garage->rating > 0)
                                        <div class="star-row">
                                            <i class="fa-solid fa-star"></i>
                                            <span class="fw-600">{{ number_format($garage->rating, 1) }}</span>
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);font-size:12px">—</span>
                                    @endif
                                </td>
                                <td style="font-weight:600">{{ $garage->rating_count }}</td>
                                <td>
                                    @if ($garage->subscription_type === 'premium')
                                        <span class="badge badge-premium"><i class="fa-solid fa-crown"
                                                style="font-size:9px"></i> Premium</span>
                                    @elseif($garage->subscription_type === 'pro')
                                        <span class="badge badge-pro"><i class="fa-solid fa-crown"
                                                style="font-size:9px"></i> Pro</span>
                                    @else
                                        <span class="badge badge-free">Gratuit</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge status-badge {{ $garage->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                        {{ $garage->is_active ? 'Actif' : 'Désactivé' }}
                                    </span>
                                    <i class="fa-solid fa-shield-check verified-badge"
                                        style="color:var(--success);margin-left:5px;font-size:13px;{{ $garage->is_verified ? '' : 'display:none' }}"
                                        title="Vérifié"></i>
                                </td>
                                <td style="font-weight:600">{{ number_format($garage->views_count, 0, ',', ' ') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" onclick="viewGarage({{ $garage->id_garage }})">
                                                <i class="fa-solid fa-eye"></i> Voir détail
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="manageServices({{ $garage->id_garage }})">
                                                <i class="fa-solid fa-list"></i> Gérer services
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="verifyGarage({{ $garage->id_garage }})">
                                                <i
                                                    class="fa-solid fa-shield-{{ $garage->is_verified ? 'xmark' : 'check' }}"></i>
                                                {{ $garage->is_verified ? 'Retirer badge' : 'Vérifier' }}
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="toggleGarage({{ $garage->id_garage }})">
                                                <i
                                                    class="fa-solid fa-{{ $garage->is_active ? 'ban' : 'rotate-right' }}"></i>
                                                {{ $garage->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                onclick="deleteGarage({{ $garage->id_garage }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-wrench"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucun garage trouvé
                                    @if ($search || $type || $plan || $city || $status)
                                        — <a href="{{ route('admin.garages.index') }}"
                                            style="color:var(--purple)">réinitialiser les filtres</a>
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
                    Affichage {{ $garages->firstItem() ?? 0 }}–{{ $garages->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} garages
                </span>
                <div class="pagi">
                    @if ($garages->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $garages->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></a>
                    @endif

                    @foreach ($garages->getUrlRange(max(1, $garages->currentPage() - 2), min($garages->lastPage(), $garages->currentPage() + 2)) as $page => $url)
                        @if ($page == $garages->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($garages->lastPage() > $garages->currentPage() + 2)
                        <span class="pagi-btn disabled">…</span>
                        <a class="pagi-btn"
                            href="{{ $garages->url($garages->lastPage()) }}">{{ $garages->lastPage() }}</a>
                    @endif

                    @if ($garages->hasMorePages())
                        <a class="pagi-btn" href="{{ $garages->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>

    </main>

    {{-- ══ Modal Ajouter Garage ══ --}}
    <div class="modal-overlay" id="modalAddGarage">
        <div class="modal-box md">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-wrench" style="color:var(--purple)"></i> Ajouter un garage</h5>
                <button class="modal-close" data-modal-close="modalAddGarage">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="col-span-2">
                        <label class="form-label">Nom du garage *</label>
                        <input type="text" id="addName" class="form-control"
                            placeholder="Ex: Garage Auto Plus Cocody">
                    </div>
                    <div>
                        <label class="form-label">Type *</label>
                        <select id="addType" class="form-select" style="width:100%">
                            <option value="">— Sélectionner —</option>
                            @foreach ($typeLabels as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ville *</label>
                        <select id="addCity" class="form-select" style="width:100%">
                            <option value="">— Sélectionner —</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                            <option value="Abidjan">Abidjan</option>
                            <option value="Bouaké">Bouaké</option>
                            <option value="Daloa">Daloa</option>
                            <option value="Yamoussoukro">Yamoussoukro</option>
                            <option value="San-Pédro">San-Pédro</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Adresse complète *</label>
                        <input type="text" id="addAddress" class="form-control"
                            placeholder="Ex: Rue des Jardins, Cocody">
                    </div>
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="number" id="addLat" class="form-control" placeholder="5.3544" step="0.000001">
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="number" id="addLng" class="form-control" placeholder="-4.0082"
                            step="0.000001">
                    </div>
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="text" id="addPhone" class="form-control" placeholder="+225 07 ...">
                    </div>
                    <div>
                        <label class="form-label">WhatsApp</label>
                        <input type="text" id="addWhatsapp" class="form-control" placeholder="+225 07 ...">
                    </div>
                    <div>
                        <label class="form-label">Ouverture</label>
                        <input type="time" id="addOpens" class="form-control" value="07:00">
                    </div>
                    <div>
                        <label class="form-label">Fermeture</label>
                        <input type="time" id="addCloses" class="form-control" value="20:00">
                    </div>
                    <div>
                        <label class="form-label">Plan</label>
                        <select id="addPlan" class="form-select" style="width:100%">
                            <option value="free">Gratuit</option>
                            <option value="pro">Pro</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding-top:22px">
                        <label class="toggle">
                            <input type="checkbox" id="add24h">
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:13px;font-weight:600">Ouvert 24h/24</span>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Description</label>
                        <textarea id="addDescription" class="form-control" rows="2" placeholder="Description du garage..."></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Services proposés</label>
                        <div id="addServicesWrap" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px">
                            @foreach ($typeLabels as $svc => $lbl)
                                <label
                                    style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;background:#F1F5F9;padding:5px 10px;border-radius:20px">
                                    <input type="checkbox" value="{{ $svc }}"> {{ $lbl }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalAddGarage">Annuler</button>
                <button class="btn btn-primary" onclick="saveGarage()">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Modal Voir Garage ══ --}}
    <div class="modal-overlay" id="modalViewGarage">
        <div class="modal-box md">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-wrench" style="color:var(--purple)"></i> Détail du garage</h5>
                <button class="modal-close" data-modal-close="modalViewGarage">✕</button>
            </div>
            <div class="modal-body">

                <div id="viewLoading" style="display:flex;align-items:center;justify-content:center;padding:40px">
                    <div style="text-align:center;color:var(--text-muted)">
                        <i class="fa-solid fa-spinner fa-spin"
                            style="font-size:24px;margin-bottom:10px;display:block"></i>
                        Chargement...
                    </div>
                </div>

                <div id="viewContent" style="display:none">
                    {{-- Header --}}
                    <div
                        style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid var(--border)">
                        <div id="vIconWrap"
                            style="width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i id="vIcon" class="fa-solid fa-wrench" style="font-size:22px"></i>
                        </div>
                        <div>
                            <div id="vName" style="font-size:17px;font-weight:700;color:var(--text)"></div>
                            <div id="vSubInfo" style="color:var(--text-muted);font-size:13px;margin-top:2px"></div>
                            <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
                                <span id="vTypeBadge" class="badge badge-type"></span>
                                <span id="vPlanBadge" class="badge"></span>
                                <span id="vStatusBadge" class="badge"></span>
                                <span id="vVerifiedBadge" class="badge badge-active" style="display:none">
                                    <i class="fa-solid fa-shield-check" style="font-size:9px"></i> Vérifié
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Stats mois --}}
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px">
                        @foreach ([['vStatViews', 'Vues', '#EDE9FE', 'var(--purple)', 'fa-eye'], ['vStatCalls', 'Appels', '#D1FAE5', 'var(--success)', 'fa-phone'], ['vStatWhatsapp', 'WhatsApp', '#DBEAFE', 'var(--info)', 'fa-brands fa-whatsapp'], ['vStatItinerary', 'Itinéraires', '#FFF0EB', 'var(--primary)', 'fa-route']] as [$elId, $lbl, $bg, $c, $icon])
                            <div style="background:{{ $bg }};border-radius:9px;padding:10px;text-align:center">
                                <i class="fa-solid {{ $icon }}"
                                    style="color:{{ $c }};font-size:14px;display:block;margin-bottom:4px"></i>
                                <div id="{{ $elId }}"
                                    style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--text)">
                                    —</div>
                                <div style="font-size:10.5px;color:var(--text-muted)">{{ $lbl }} (mois)</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Infos grille --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                        @foreach ([['vRatingVal vRatingCount', 'Note', 'flex'], ['vViews', 'Vues total', 'block'], ['vPhone', 'Téléphone', 'block'], ['vHours', 'Horaires', 'block']] as [$elIds, $lbl, $disp])
                            <div style="background:var(--bg);border-radius:9px;padding:10px 12px">
                                <div
                                    style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">
                                    {{ $lbl }}</div>
                                <div style="display:{{ $disp }};align-items:center;gap:6px">
                                    @if (str_contains($elIds, ' '))
                                        <span id="vRatingVal"
                                            style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800">—</span>
                                        <span id="vRatingCount" style="font-size:12px;color:var(--text-muted)"></span>
                                    @else
                                        <div id="{{ $elIds }}"
                                            style="font-size:14px;font-weight:600;color:var(--text)">—</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Services --}}
                    <div style="margin-bottom:16px">
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                            Services</div>
                        <div id="vServices" style="display:flex;flex-wrap:wrap;gap:6px"></div>
                    </div>

                    {{-- Avis récents --}}
                    <div style="margin-bottom:18px">
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                            Avis récents</div>
                        <div id="vReviews"></div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:10px">
                        <button id="vBtnServices" class="btn btn-primary" style="flex:1;justify-content:center">
                            <i class="fa-solid fa-list"></i> Services
                        </button>
                        <button id="vBtnVerify" class="btn btn-secondary" style="flex:1;justify-content:center"></button>
                        <button id="vBtnToggle" class="btn btn-secondary" style="flex:1;justify-content:center"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Modal Gérer Services ══ --}}
    <div class="modal-overlay" id="modalServices">
        <div class="modal-box sm">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-list" style="color:var(--info)"></i> Gérer les services</h5>
                <button class="modal-close" data-modal-close="modalServices">✕</button>
            </div>
            <div class="modal-body">
                <p id="svcModalGarageName" style="color:var(--text-muted);font-size:13px;margin:0 0 14px;font-weight:600">
                </p>
                <p style="font-size:12px;color:var(--text-muted);margin:0 0 12px">Cochez les services proposés et
                    renseignez la fourchette de prix.</p>
                <div id="svcList"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalServices">Annuler</button>
                <button class="btn btn-primary" onclick="saveServices()">
                    <i class="fa-solid fa-check"></i> Sauvegarder
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
