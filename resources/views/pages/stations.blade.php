@extends('layouts.master', ['title' => 'Stations & service', 'subTitle' => 'Stations & service'])

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

        .price-tag {
            font-weight: 700;
            font-size: 13px;
        }

        .price-essence {
            color: var(--info);
        }

        .price-gasoil {
            color: var(--success);
        }

        .price-na {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic;
        }

        .station-icon {
            width: 36px;
            height: 36px;
            background: var(--primary-light);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
            min-width: 180px;
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

        .btn-sm {
            padding: 5px 11px;
            font-size: 12px;
            border-radius: 7px;
        }

        .btn-verified {
            background: #D1FAE5;
            color: #065F46;
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-verified:hover {
            background: #A7F3D0;
        }

        .w-full {
            width: 100%;
            justify-content: center;
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
            max-width: 440px;
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
            background: var(--primary);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(18px);
        }

        /* Fuel price row */
        .fuel-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .fuel-row:last-child {
            border-bottom: none;
        }

        .fuel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .fuel-name {
            width: 110px;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .fuel-input {
            width: 110px;
        }

        .fuel-unit {
            font-size: 13px;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        /* Alert */
        .alert-info {
            background: #DBEAFE;
            color: #1D4ED8;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
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

        // ─── Filtres ─────────────────────────────────────────────────────────────────
        let searchTimer;

        function applyFilters() {
            const url = new URL(window.location.href);
            const s = document.getElementById('searchInput').value;
            const p = document.getElementById('filterPlan').value;
            const c = document.getElementById('filterCity').value;
            const st = document.getElementById('filterStatus').value;
            s ? url.searchParams.set('search', s) : url.searchParams.delete('search');
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
            const cur = url.searchParams.get('verified');
            cur ? url.searchParams.delete('verified') : url.searchParams.set('verified', '1');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        // ─── View station (modal détail) ─────────────────────────────────────────────
        function viewStation(id) {
            document.getElementById('viewLoading').style.display = 'flex';
            document.getElementById('viewContent').style.display = 'none';
            openModal('modalViewStation');

            fetch(`/admin/stations/${id}`, {
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
                    const s = d.data;

                    document.getElementById('vName').textContent = s.name;
                    document.getElementById('vBrand').textContent = s.brand || '—';
                    document.getElementById('vCity').textContent = s.city;
                    document.getElementById('vAddress').textContent = s.address;
                    document.getElementById('vPhone').textContent = s.phone || '—';
                    document.getElementById('vViews').textContent = (s.views_count || 0).toLocaleString('fr');
                    document.getElementById('vPlan').textContent = s.subscription_type.toUpperCase();
                    document.getElementById('vVerified').textContent = s.is_verified ? '✅ Vérifiée' : '❌ Non vérifiée';
                    document.getElementById('vStatus').textContent = s.is_active ? 'Active' : 'Désactivée';
                    document.getElementById('vRating').textContent = s.rating.total > 0 ?
                        `${s.rating.avg}/5 (${s.rating.total} avis)` : 'Aucun avis';
                    document.getElementById('vLastPrice').textContent = s.last_price_update ?
                        new Date(s.last_price_update).toLocaleDateString('fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) :
                        'Jamais';

                    // Prix
                    const FUEL_LABELS = {
                        essence: 'Essence',
                        gasoil: 'Gasoil',
                        sans_plomb: 'Sans plomb',
                        super: 'Super',
                        gpl: 'GPL'
                    };
                    const FUEL_COLORS = {
                        essence: '#3B82F6',
                        gasoil: '#10B981',
                        sans_plomb: '#F59E0B',
                        super: '#8B5CF6',
                        gpl: '#64748B'
                    };
                    const priceHtml = (s.prices || []).map(p =>
                            `<div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                    <span style="display:flex;align-items:center;gap:8px">
                        <span style="width:10px;height:10px;background:${FUEL_COLORS[p.fuel_type]||'#CBD5E1'};border-radius:50%;display:inline-block"></span>
                        <span style="font-size:13.5px">${FUEL_LABELS[p.fuel_type]||p.fuel_type}</span>
                    </span>
                    ${p.is_available
                        ? `<strong style="color:${FUEL_COLORS[p.fuel_type]||'#0F172A'}">${Number(p.price).toLocaleString('fr')} FCFA/L</strong>`
                        : `<span style="font-size:12px;color:var(--text-muted)">Indisponible</span>`}
                </div>`
                        ).join('') ||
                        '<p style="font-size:13px;color:var(--text-muted);margin:0">Aucun prix enregistré</p>';
                    document.getElementById('vPrices').innerHTML = priceHtml;

                    // Services
                    const svcLabels = {
                        lavage_auto: 'Lavage auto',
                        gonflage_pneus: 'Gonflage pneus',
                        boutique: 'Boutique',
                        restaurant: 'Restaurant',
                        toilettes: 'Toilettes',
                        wifi: 'WiFi',
                        atm: 'ATM',
                        parking: 'Parking',
                        gonflage_gratuit: 'Gonflage gratuit',
                        huile_moteur: 'Huile moteur',
                        reparation_rapide: 'Réparation rapide'
                    };
                    const svcHtml = (s.services || []).map(sv =>
                        `<span style="background:#F1F5F9;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600">${svcLabels[sv]||sv}</span>`
                    ).join(' ') || '<span style="font-size:13px;color:var(--text-muted)">Aucun service</span>';
                    document.getElementById('vServices').innerHTML = svcHtml;

                    // Stats mois
                    document.getElementById('vStatViews').textContent = s.stats_month?.view_profile?.count || 0;
                    document.getElementById('vStatCalls').textContent = s.stats_month?.call?.count || 0;
                    document.getElementById('vStatWhatsapp').textContent = s.stats_month?.whatsapp?.count || 0;
                    document.getElementById('vStatItinerary').textContent = s.stats_month?.itinerary?.count || 0;

                    // Boutons d'action dynamiques
                    document.getElementById('vBtnVerify').textContent = s.is_verified ? '✕ Retirer badge' :
                        '✅ Vérifier';
                    document.getElementById('vBtnVerify').onclick = () => verifyStation(id);
                    document.getElementById('vBtnToggle').textContent = s.is_active ? 'Désactiver' : 'Activer';
                    document.getElementById('vBtnToggle').className = 'btn ' + (s.is_active ? 'btn-secondary' :
                        'btn-success');
                    document.getElementById('vBtnToggle').onclick = () => toggleStation(id);
                    document.getElementById('vBtnPrices').onclick = () => {
                        closeModal('modalViewStation');
                        openUpdatePrices(id, s.name, s.prices);
                    };

                    document.getElementById('viewLoading').style.display = 'none';
                    document.getElementById('viewContent').style.display = 'block';
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Modal Modifier Prix ──────────────────────────────────────────────────────
        let currentPriceStationId = null;

        function openUpdatePrices(id, name, prices) {
            currentPriceStationId = id;
            document.getElementById('priceModalTitle').textContent = name;

            const FUELS = [{
                    type: 'essence',
                    label: 'Essence',
                    color: '#3B82F6'
                },
                {
                    type: 'gasoil',
                    label: 'Gasoil',
                    color: '#10B981'
                },
                {
                    type: 'sans_plomb',
                    label: 'Sans plomb',
                    color: '#F59E0B'
                },
                {
                    type: 'super',
                    label: 'Super',
                    color: '#8B5CF6'
                },
                {
                    type: 'gpl',
                    label: 'GPL',
                    color: '#64748B'
                },
            ];

            const priceMap = {};
            (prices || []).forEach(p => {
                priceMap[p.fuel_type] = p;
            });

            const html = FUELS.map(f => {
                const existing = priceMap[f.type];
                const val = existing ? existing.price : '';
                const avail = existing ? existing.is_available : true;
                return `
        <div class="fuel-row">
            <div class="fuel-dot" style="background:${f.color}"></div>
            <span class="fuel-name">${f.label}</span>
            <input type="number" class="form-control fuel-input"
                   id="price_${f.type}" value="${val}" placeholder="—" min="0" step="0.01">
            <span class="fuel-unit">FCFA/L</span>
            <label class="toggle" style="margin-left:auto" title="Disponible">
                <input type="checkbox" id="avail_${f.type}" ${avail ? 'checked' : ''}>
                <span class="toggle-slider"></span>
            </label>
        </div>`;
            }).join('');

            document.getElementById('fuelPricesList').innerHTML = html;
            openModal('modalUpdatePrices');
        }

        function updatePrices(id) {
            // Appelé depuis le dropdown de la table — charge d'abord les prix existants
            fetch(`/admin/stations/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) openUpdatePrices(id, d.data.name, d.data.prices);
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        function savePrices() {
            const FUELS = ['essence', 'gasoil', 'sans_plomb', 'super', 'gpl'];
            const prices = FUELS.map(t => {
                const val = document.getElementById('price_' + t)?.value;
                if (!val || val === '') return null;
                return {
                    fuel_type: t,
                    price: parseFloat(val),
                    is_available: document.getElementById('avail_' + t)?.checked ?? true,
                };
            }).filter(Boolean);

            if (!prices.length) {
                showToast('Aucun prix à enregistrer', 'warning');
                return;
            }

            postAction(`/admin/stations/${currentPriceStationId}/prices`, {
                prices
            }, d => {
                showToast(d.message, 'success');
                closeModal('modalUpdatePrices');
                setTimeout(() => location.reload(), 800);
            });
        }

        // ─── Actions ─────────────────────────────────────────────────────────────────
        function verifyStation(id) {
            postAction(`/admin/stations/${id}/verify`, {}, d => {
                showToast(d.message, 'success');
                closeModal('modalViewStation');
                updateRowVerified(id, d.is_verified);
            });
        }

        function toggleStation(id) {
            postAction(`/admin/stations/${id}/toggle`, {}, d => {
                showToast(d.message, d.is_active ? 'success' : 'warning');
                closeModal('modalViewStation');
                updateRowActive(id, d.is_active);
            });
        }

        function deleteStation(id) {
            confirmAction('Supprimer cette station ? Cette action est irréversible.', () => {
                fetch(`/admin/stations/${id}`, {
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
                            const row = document.querySelector(`tr[data-station-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transition = 'opacity .3s';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
            });
        }

        function saveStation() {
            const data = {
                name: document.getElementById('addName').value.trim(),
                brand: document.getElementById('addBrand').value,
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
                price_essence: document.getElementById('addPriceEssence').value || null,
                price_gasoil: document.getElementById('addPriceGasoil').value || null,
                price_sans_plomb: document.getElementById('addPriceSansPlomb').value || null,
            };

            if (!data.name || !data.address || !data.city) {
                showToast('Nom, adresse et ville requis', 'error');
                return;
            }

            postAction('/admin/stations', data, d => {
                showToast(d.message, 'success');
                closeModal('modalAddStation');
                setTimeout(() => location.reload(), 800);
            });
        }

        function exportStations() {
            showToast('Export CSV en cours...', 'info');
            window.location.href = '/admin/stations/export' + window.location.search;
        }

        function selectAll(cb) {
            document.querySelectorAll('#stationsTable tbody input[type=checkbox]').forEach(c => c.checked = cb.checked);
        }

        // ─── DOM update helpers ───────────────────────────────────────────────────────
        function updateRowVerified(id, isVerified) {
            const row = document.querySelector(`tr[data-station-id="${id}"]`);
            if (!row) return;
            const el = row.querySelector('.verified-badge');
            if (el) el.style.display = isVerified ? 'inline' : 'none';
        }

        function updateRowActive(id, isActive) {
            const row = document.querySelector(`tr[data-station-id="${id}"]`);
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

        // Attacher les handlers via onclick (écrase les handlers du layout master)
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
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(this.id);
                });
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        });

        // ─── Utilities ───────────────────────────────────────────────────────────────
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
                <h1>Stations & service</h1>
                <p>Gérez toutes les stations partenaires de la plateforme.</p>
            </div>
            <div style="display:flex;gap:10px">
                <button class="btn btn-secondary" onclick="exportStations()">
                    <i class="fa-solid fa-download"></i> Export CSV
                </button>
                <button class="btn btn-primary" data-modal-open="modalAddStation">
                    <i class="fa-solid fa-plus"></i> Ajouter station
                </button>
            </div>
        </div>

        {{-- ── KPIs ── --}}
        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:var(--primary-light)">
                    <i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ number_format($kpis['total'], 0, ',', ' ') }}</div>
                    <div class="kpi-mini-lbl">Total stations</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#D1FAE5">
                    <i class="fa-solid fa-shield-check" style="color:var(--success)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['verified'] }}</div>
                    <div class="kpi-mini-lbl">Vérifiées</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#DBEAFE">
                    <i class="fa-solid fa-crown" style="color:var(--info)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['pro'] }}</div>
                    <div class="kpi-mini-lbl">Plan Pro/Premium</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#FEE2E2">
                    <i class="fa-solid fa-ban" style="color:var(--danger)"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $kpis['inactive'] }}</div>
                    <div class="kpi-mini-lbl">Désactivées</div>
                </div>
            </div>
        </div>

        {{-- ── Card principale ── --}}
        <div class="card">

            {{-- Filtres --}}
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Nom, ville, marque..."
                        value="{{ $search }}">
                </div>
                <select id="filterPlan" class="form-select" style="width:150px" onchange="applyFilters()">
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
                <button class="btn {{ $verified ? 'btn-success' : 'btn-secondary' }}" onclick="filterVerified()">
                    <i class="fa-solid fa-shield-check"></i>
                    {{ $verified ? 'Toutes' : 'Vérifiées' }}
                </button>
            </div>

            {{-- Table --}}
            <div class="tbl-wrap">
                <table class="data-tbl" id="stationsTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" onchange="selectAll(this)"></th>
                            <th>Station</th>
                            <th>Marque</th>
                            <th>Ville</th>
                            <th>Prix Essence</th>
                            <th>Prix Gasoil</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Vues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $station)
                            @php
                                $stationPrices = $prices->get($station->id_station, collect());
                                $essence = $stationPrices->firstWhere('fuel_type', 'essence');
                                $gasoil = $stationPrices->firstWhere('fuel_type', 'gasoil');
                            @endphp
                            <tr data-station-id="{{ $station->id_station }}">
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="station-icon">
                                            <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:14px"></i>
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $station->name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">{{ $station->address }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $station->brand ?? '—' }}</td>
                                <td>{{ $station->city }}</td>
                                <td>
                                    @if ($essence && $essence->is_available)
                                        <span
                                            class="price-tag price-essence">{{ number_format($essence->price, 0, ',', ' ') }}
                                            FCFA/L</span>
                                    @else
                                        <span class="price-na">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($gasoil && $gasoil->is_available)
                                        <span
                                            class="price-tag price-gasoil">{{ number_format($gasoil->price, 0, ',', ' ') }}
                                            FCFA/L</span>
                                    @else
                                        <span class="price-na">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($station->subscription_type === 'premium')
                                        <span class="badge badge-premium"><i class="fa-solid fa-crown"
                                                style="font-size:9px"></i> Premium</span>
                                    @elseif($station->subscription_type === 'pro')
                                        <span class="badge badge-pro"><i class="fa-solid fa-crown"
                                                style="font-size:9px"></i> Pro</span>
                                    @else
                                        <span class="badge badge-free">Gratuit</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge status-badge {{ $station->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                        {{ $station->is_active ? 'Actif' : 'Désactivé' }}
                                    </span>
                                    @if ($station->is_verified)
                                        <i class="fa-solid fa-shield-check verified-badge"
                                            style="color:var(--success);margin-left:4px" title="Vérifiée"></i>
                                    @else
                                        <i class="fa-solid fa-shield-check verified-badge"
                                            style="color:#CBD5E1;margin-left:4px;display:none" title="Non vérifiée"></i>
                                    @endif
                                </td>
                                <td style="font-weight:600">{{ number_format($station->views_count, 0, ',', ' ') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item"
                                                onclick="viewStation({{ $station->id_station }})">
                                                <i class="fa-solid fa-eye"></i> Voir détail
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="updatePrices({{ $station->id_station }})">
                                                <i class="fa-solid fa-gas-pump"></i> Modifier prix
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="verifyStation({{ $station->id_station }})">
                                                <i
                                                    class="fa-solid fa-shield-{{ $station->is_verified ? 'xmark' : 'check' }}"></i>
                                                {{ $station->is_verified ? 'Retirer badge' : 'Vérifier' }}
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="toggleStation({{ $station->id_station }})">
                                                <i
                                                    class="fa-solid fa-{{ $station->is_active ? 'ban' : 'rotate-right' }}"></i>
                                                {{ $station->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                onclick="deleteStation({{ $station->id_station }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-gas-pump"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucune station trouvée
                                    @if ($search || $plan || $city || $status)
                                        — <a href="{{ route('admin.stations.index') }}"
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
                    Affichage {{ $stations->firstItem() ?? 0 }}–{{ $stations->lastItem() ?? 0 }}
                    sur {{ number_format($total, 0, ',', ' ') }} stations
                </span>
                <div class="pagi">
                    @if ($stations->onFirstPage())
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:11px"></i></span>
                    @else
                        <a class="pagi-btn" href="{{ $stations->previousPageUrl() }}"><i
                                class="fa-solid fa-chevron-left" style="font-size:11px"></i></a>
                    @endif

                    @foreach ($stations->getUrlRange(max(1, $stations->currentPage() - 2), min($stations->lastPage(), $stations->currentPage() + 2)) as $page => $url)
                        @if ($page == $stations->currentPage())
                            <span class="pagi-btn active">{{ $page }}</span>
                        @else
                            <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($stations->lastPage() > $stations->currentPage() + 2)
                        <span class="pagi-btn disabled">…</span>
                        <a class="pagi-btn"
                            href="{{ $stations->url($stations->lastPage()) }}">{{ $stations->lastPage() }}</a>
                    @endif

                    @if ($stations->hasMorePages())
                        <a class="pagi-btn" href="{{ $stations->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></a>
                    @else
                        <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:11px"></i></span>
                    @endif
                </div>
            </div>
        </div>

    </main>

    {{-- ══ Modal — Ajouter station ══ --}}
    <div class="modal-overlay" id="modalAddStation">
        <div class="modal-box md">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Ajouter une station</h5>
                <button class="modal-close" data-modal-close="modalAddStation">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="col-span-2">
                        <label class="form-label">Nom de la station *</label>
                        <input type="text" id="addName" class="form-control"
                            placeholder="Ex: Total Énergies Cocody">
                    </div>
                    <div>
                        <label class="form-label">Marque</label>
                        <select id="addBrand" class="form-select" style="width:100%">
                            <option value="">— Choisir —</option>
                            <option>Total</option>
                            <option>Shell</option>
                            <option>Petro Ivoire</option>
                            <option>Oryx</option>
                            <option>Vivo Energy</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ville *</label>
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
                    <div class="col-span-2">
                        <label class="form-label">Adresse complète *</label>
                        <input type="text" id="addAddress" class="form-control"
                            placeholder="Ex: Bvd Latrille, Cocody">
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
                        <input type="text" id="addPhone" class="form-control" placeholder="+225 ...">
                    </div>
                    <div>
                        <label class="form-label">WhatsApp</label>
                        <input type="text" id="addWhatsapp" class="form-control" placeholder="+225 ...">
                    </div>
                    <div>
                        <label class="form-label">Ouverture</label>
                        <input type="time" id="addOpens" class="form-control" value="06:00">
                    </div>
                    <div>
                        <label class="form-label">Fermeture</label>
                        <input type="time" id="addCloses" class="form-control" value="22:00">
                    </div>
                    <div>
                        <label class="form-label">Plan</label>
                        <select id="addPlan" class="form-select" style="width:100%">
                            <option value="free">Gratuit</option>
                            <option value="pro">Pro</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding-top:20px">
                        <label class="toggle">
                            <input type="checkbox" id="add24h">
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="form-label" style="margin:0">Ouverte 24h/24</span>
                    </div>
                </div>
                <div style="margin-top:18px">
                    <label class="form-label" style="margin-bottom:10px">Prix carburant (optionnel)</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
                        <div>
                            <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Essence
                                (FCFA/L)</label>
                            <input type="number" id="addPriceEssence" class="form-control" placeholder="695">
                        </div>
                        <div>
                            <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Gasoil
                                (FCFA/L)</label>
                            <input type="number" id="addPriceGasoil" class="form-control" placeholder="615">
                        </div>
                        <div>
                            <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Sans
                                plomb (FCFA/L)</label>
                            <input type="number" id="addPriceSansPlomb" class="form-control" placeholder="720">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalAddStation">Annuler</button>
                <button class="btn btn-primary" onclick="saveStation()">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Modal — Modifier prix ══ --}}
    <div class="modal-overlay" id="modalUpdatePrices">
        <div class="modal-box sm">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-gas-pump" style="color:var(--info)"></i> Mettre à jour les prix</h5>
                <button class="modal-close" data-modal-close="modalUpdatePrices">✕</button>
            </div>
            <div class="modal-body">
                <div class="alert-info" style="margin-bottom:16px">
                    <i class="fa-solid fa-circle-info"></i>
                    <span id="priceModalTitle">Station</span>
                </div>
                <div id="fuelPricesList"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalUpdatePrices">Annuler</button>
                <button class="btn btn-primary" onclick="savePrices()">
                    <i class="fa-solid fa-check"></i> Sauvegarder
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Modal — Voir station ══ --}}
    <div class="modal-overlay" id="modalViewStation">
        <div class="modal-box md">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Détail station</h5>
                <button class="modal-close" data-modal-close="modalViewStation">✕</button>
            </div>
            <div class="modal-body">

                {{-- Loading --}}
                <div id="viewLoading" style="display:flex;align-items:center;justify-content:center;padding:40px">
                    <div style="text-align:center;color:var(--text-muted)">
                        <i class="fa-solid fa-spinner fa-spin"
                            style="font-size:24px;margin-bottom:10px;display:block"></i>
                        Chargement...
                    </div>
                </div>

                {{-- Content --}}
                <div id="viewContent" style="display:none">
                    {{-- Header --}}
                    <div
                        style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid var(--border)">
                        <div class="station-icon" style="width:48px;height:48px">
                            <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:18px"></i>
                        </div>
                        <div>
                            <div id="vName" style="font-size:17px;font-weight:700;color:var(--text)"></div>
                            <div style="font-size:13px;color:var(--text-muted);margin-top:2px">
                                <span id="vBrand"></span> · <span id="vCity"></span>
                            </div>
                            <div style="font-size:12.5px;color:var(--text-muted);margin-top:2px" id="vAddress"></div>
                        </div>
                    </div>

                    {{-- Stats mois --}}
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px">
                        @foreach ([['vStatViews', 'Vues', '#FFF0EB', 'var(--primary)', 'fa-eye'], ['vStatCalls', 'Appels', '#D1FAE5', 'var(--success)', 'fa-phone'], ['vStatWhatsapp', 'WhatsApp', '#DBEAFE', 'var(--info)', 'fa-brands fa-whatsapp'], ['vStatItinerary', 'Itinéraires', '#EDE9FE', 'var(--purple)', 'fa-route']] as [$elId, $label, $bg, $color, $icon])
                            <div style="background:{{ $bg }};border-radius:9px;padding:10px;text-align:center">
                                <i class="fa-solid {{ $icon }}"
                                    style="color:{{ $color }};font-size:14px;display:block;margin-bottom:4px"></i>
                                <div id="{{ $elId }}"
                                    style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--text)">
                                    —</div>
                                <div style="font-size:10.5px;color:var(--text-muted)">{{ $label }} (mois)</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Infos --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                        @foreach ([['vPhone', 'Téléphone'], ['vViews', 'Vues total'], ['vPlan', 'Plan'], ['vVerified', 'Badge'], ['vStatus', 'Statut'], ['vRating', 'Note'], ['vLastPrice', 'Dernier prix']] as [$elId, $label])
                            <div style="background:var(--bg);border-radius:9px;padding:10px 12px">
                                <div
                                    style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">
                                    {{ $label }}</div>
                                <div id="{{ $elId }}" style="font-size:14px;font-weight:600;color:var(--text)">—
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Prix --}}
                    <div style="margin-bottom:16px">
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                            Prix carburant</div>
                        <div id="vPrices"></div>
                    </div>

                    {{-- Services --}}
                    <div style="margin-bottom:18px">
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                            Services</div>
                        <div id="vServices" style="display:flex;flex-wrap:wrap;gap:6px"></div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:10px">
                        <button id="vBtnPrices" class="btn btn-primary" style="flex:1;justify-content:center">
                            <i class="fa-solid fa-gas-pump"></i> Modifier prix
                        </button>
                        <button id="vBtnVerify" class="btn btn-secondary" style="flex:1;justify-content:center"></button>
                        <button id="vBtnToggle" class="btn btn-secondary" style="flex:1;justify-content:center"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
