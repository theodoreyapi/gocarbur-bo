@extends('layouts.master', ['title' => 'Articles & Conseils', 'subTitle' => 'Articles & Conseils'])

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

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* Tab nav */
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

        .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            background: #F1F5F9;
            color: var(--text-muted);
        }

        .tab-btn.active .tab-count {
            background: var(--primary-light);
            color: var(--primary);
        }

        /* Filter bar */
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

        .art-title {
            font-weight: 600;
            max-width: 300px;
            line-height: 1.4;
        }

        .art-excerpt {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .badge-published {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-draft {
            background: #F1F5F9;
            color: #64748B;
        }

        .badge-sponsored {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-cat-entretien_auto {
            background: #FFF0EB;
            color: var(--primary);
        }

        .badge-cat-economie_carburant {
            background: #DBEAFE;
            color: var(--info);
        }

        .badge-cat-conduite_securite {
            background: #D1FAE5;
            color: var(--success);
        }

        .badge-cat-documents_admin {
            background: #FEF3C7;
            color: var(--warning);
        }

        .badge-cat-astuces_mecaniques {
            background: #EDE9FE;
            color: var(--purple);
        }

        .badge-cat-videos_conseils {
            background: #FEE2E2;
            color: var(--danger);
        }

        .badge-cat-actualites {
            background: #DBEAFE;
            color: var(--info);
        }

        .badge-cat-legislation {
            background: #F1F5F9;
            color: #64748B;
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
            min-width: 175px;
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
            max-width: 720px;
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

        /* Form */
        .form-group {
            margin-bottom: 14px;
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

        textarea.form-control {
            resize: vertical;
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

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg);
            border-radius: 9px;
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

        .tags-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 4px;
        }

        .tag-pill {
            background: #F1F5F9;
            color: var(--text-muted);
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
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

        const CATEGORY_LABELS = @json($categories);

        // ─── Tabs & filtres ───────────────────────────────────────────────────────────
        function switchTab(t) {
            const u = new URL(window.location.href);
            u.searchParams.set('tab', t);
            u.searchParams.delete('page');
            window.location.href = u;
        }

        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const u = new URL(window.location.href);
                const v = document.getElementById('searchInput').value;
                v ? u.searchParams.set('search', v) : u.searchParams.delete('search');
                u.searchParams.delete('page');
                window.location.href = u;
            }, 380);
        });

        // ─── Modal Article ────────────────────────────────────────────────────────────
        function openArticleModal(id = null) {
            editingId = id;
            resetForm();

            if (id) {
                document.getElementById('modalTitle').textContent = 'Modifier l\'article';
                fetch(`/admin/articles/${id}`, {
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
                        const a = d.data;
                        document.getElementById('artTitle').value = a.title;
                        document.getElementById('artCategory').value = a.category;
                        document.getElementById('artReadTime').value = a.read_time_minutes || '';
                        document.getElementById('artExcerpt').value = a.excerpt || '';
                        document.getElementById('artContent').value = a.content;
                        document.getElementById('artTags').value = (a.tags || []).join(', ');
                        document.getElementById('artPublish').checked = !!a.is_published;
                        document.getElementById('artSponsored').checked = !!a.is_sponsored;
                        document.getElementById('artSponsorName').value = a.sponsor_name || '';
                        document.getElementById('artSponsorUrl').value = a.sponsor_url || '';
                        toggleSponsor(!!a.is_sponsored);
                    });
            } else {
                document.getElementById('modalTitle').textContent = 'Nouvel article';
            }

            openModal('modalArticle');
        }

        function editArticle(id) {
            openArticleModal(id);
        }

        function resetForm() {
            ['artTitle', 'artExcerpt', 'artContent', 'artTags', 'artSponsorName', 'artSponsorUrl', 'artReadTime']
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('artCategory').value = '';
            document.getElementById('artPublish').checked = false;
            document.getElementById('artSponsored').checked = false;
            toggleSponsor(false);
        }

        function toggleSponsor(show) {
            document.getElementById('sponsorFields').style.display = show ? 'grid' : 'none';
        }

        function getFormData() {
            const tags = document.getElementById('artTags').value
                .split(',').map(t => t.trim()).filter(Boolean);

            return {
                title: document.getElementById('artTitle').value.trim(),
                category: document.getElementById('artCategory').value,
                excerpt: document.getElementById('artExcerpt').value.trim(),
                content: document.getElementById('artContent').value.trim(),
                read_time_minutes: parseInt(document.getElementById('artReadTime').value) || null,
                tags,
                is_sponsored: document.getElementById('artSponsored').checked,
                sponsor_name: document.getElementById('artSponsorName').value.trim(),
                sponsor_url: document.getElementById('artSponsorUrl').value.trim(),
            };
        }

        function saveDraft() {
            const data = {
                ...getFormData(),
                publish: false
            };
            saveArticle(data, false);
        }

        function publishNow() {
            const data = {
                ...getFormData(),
                publish: true
            };
            saveArticle(data, true);
        }

        function saveArticle(data, publish) {
            if (!data.title) {
                showToast('Titre requis', 'error');
                return;
            }
            if (!data.category) {
                showToast('Catégorie requise', 'error');
                return;
            }
            if (!data.content) {
                showToast('Contenu requis', 'error');
                return;
            }

            const url = editingId ? `/admin/articles/${editingId}` : '/admin/articles';
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
                        closeModal('modalArticle');
                        setTimeout(() => location.reload(), 700);
                    } else {
                        showToast(d.message || 'Erreur', 'error');
                    }
                })
                .catch(() => showToast('Erreur réseau', 'error'));
        }

        // ─── Publish toggle ───────────────────────────────────────────────────────────
        function togglePublish(id, currentlyPublished) {
            const action = currentlyPublished ? 'Dépublier' : 'Publier';
            if (!confirm(`${action} cet article ?`)) return;

            fetch(`/admin/articles/${id}/publish`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        showToast(d.message, 'success');
                        const badge = document.querySelector(`tr[data-article-id="${id}"] .status-badge`);
                        if (badge) {
                            badge.className = 'badge ' + (d.is_published ? 'badge-published' : 'badge-draft');
                            badge.textContent = d.is_published ? 'Publié' : 'Brouillon';
                        }
                        // Mettre à jour le bouton du dropdown
                        const btn = document.querySelector(`tr[data-article-id="${id}"] .btn-toggle-publish`);
                        if (btn) {
                            btn.dataset.published = d.is_published ? '1' : '0';
                            btn.innerHTML = d.is_published ?
                                '<i class="fa-solid fa-eye-slash"></i> Dépublier' :
                                '<i class="fa-solid fa-upload"></i> Publier';
                        }
                    } else {
                        showToast(d.message || 'Erreur', 'error');
                    }
                });
        }

        // ─── Delete ───────────────────────────────────────────────────────────────────
        function deleteArticle(id) {
            if (!confirm('Supprimer cet article définitivement ?')) return;
            fetch(`/admin/articles/${id}`, {
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
                        const row = document.querySelector(`tr[data-article-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transition = 'opacity .3s';
                            setTimeout(() => row.remove(), 300);
                        }
                    }
                });
        }

        // ─── Preview ──────────────────────────────────────────────────────────────────
        function previewArticle(id) {
            window.open(`/articles/${id}`, '_blank');
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
                    openArticleModal();
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

        // Dropdown
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
                <h1>Articles & Conseils</h1>
                <p>Gérez le contenu éditorial de l'application.</p>
            </div>
            <button class="btn btn-primary" data-modal-open="modalArticle">
                <i class="fa-solid fa-plus"></i> Nouvel article
            </button>
        </div>

        {{-- ── Card principale ── --}}
        <div class="card">

            {{-- Tabs --}}
            <div class="tab-nav">
                @foreach ([['tout', 'Tous', $counts->tout ?? 0], ['published', 'Publiés', $counts->published ?? 0], ['draft', 'Brouillons', $counts->draft ?? 0], ['sponsored', 'Sponsorisés', $counts->sponsored ?? 0]] as [$key, $label, $count])
                    <button class="tab-btn {{ $tab === $key ? 'active' : '' }}" onclick="switchTab('{{ $key }}')">
                        {{ $label }}
                        <span class="tab-count">{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Filtres --}}
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Titre, catégorie..."
                        value="{{ $search }}">
                </div>
                <select class="form-select" style="width:200px"
                    onchange="(()=>{const u=new URL(window.location.href);this.value?u.searchParams.set('category',this.value):u.searchParams.delete('category');u.searchParams.delete('page');window.location.href=u})()">
                    <option value="">Toutes catégories</option>
                    @foreach ($categories as $val => $label)
                        <option value="{{ $val }}" {{ $category === $val ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Table --}}
            <div class="tbl-wrap">
                <table class="data-tbl">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th>Vues</th>
                            <th>Lecture</th>
                            <th>Sponsorisé</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr data-article-id="{{ $article->id_article }}">
                                <td>
                                    <div class="art-title">{{ $article->title }}</div>
                                    @if ($article->excerpt)
                                        <div class="art-excerpt">{{ $article->excerpt }}</div>
                                    @endif
                                    @if (!empty($article->tags))
                                        <div class="tags-wrap">
                                            @foreach (array_slice($article->tags, 0, 3) as $tag)
                                                <span class="tag-pill">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-cat-{{ $article->category }}">
                                        {{ $categories[$article->category] ?? $article->category }}
                                    </span>
                                </td>
                                <td style="font-weight:600">{{ number_format($article->views_count, 0, ',', ' ') }}</td>
                                <td style="color:var(--text-muted)">
                                    {{ $article->read_time_minutes ? $article->read_time_minutes . ' min' : '—' }}
                                </td>
                                <td style="text-align:center">
                                    @if ($article->is_sponsored)
                                        <i class="fa-solid fa-star" style="color:var(--warning)"
                                            title="{{ $article->sponsor_name }}"></i>
                                    @else
                                        <i class="fa-solid fa-minus" style="color:#D1D5DB;font-size:12px"></i>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge status-badge {{ $article->is_published ? 'badge-published' : 'badge-draft' }}">
                                        {{ $article->is_published ? 'Publié' : 'Brouillon' }}
                                    </span>
                                </td>
                                <td style="color:var(--text-muted);font-size:12.5px;white-space:nowrap">
                                    @if ($article->published_at)
                                        {{ \Carbon\Carbon::parse($article->published_at)->locale('fr')->isoFormat('D MMM YYYY') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($article->created_at)->locale('fr')->isoFormat('D MMM YYYY') }}
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item"
                                                onclick="previewArticle({{ $article->id_article }})">
                                                <i class="fa-solid fa-eye"></i> Prévisualiser
                                            </button>
                                            <button class="dropdown-item"
                                                onclick="editArticle({{ $article->id_article }})">
                                                <i class="fa-solid fa-pen"></i> Modifier
                                            </button>
                                            <button class="dropdown-item btn-toggle-publish"
                                                data-published="{{ $article->is_published ? '1' : '0' }}"
                                                onclick="togglePublish({{ $article->id_article }}, {{ $article->is_published ? 'true' : 'false' }})">
                                                @if ($article->is_published)
                                                    <i class="fa-solid fa-eye-slash"></i> Dépublier
                                                @else
                                                    <i class="fa-solid fa-upload"></i> Publier
                                                @endif
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                onclick="deleteArticle({{ $article->id_article }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:50px 20px;color:var(--text-muted)">
                                    <i class="fa-solid fa-newspaper"
                                        style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                                    Aucun article trouvé
                                    @if ($search || $category)
                                        — <a href="{{ route('admin.articles.index', ['tab' => $tab]) }}"
                                            style="color:var(--primary)">réinitialiser</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagi-bar">
                <span class="pagi-info">{{ number_format($total, 0, ',', ' ') }} article(s) au total</span>
                @if ($articles->hasPages())
                    <div class="pagi">
                        @if ($articles->onFirstPage())
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-left"
                                    style="font-size:11px"></i></span>
                        @else
                            <a class="pagi-btn" href="{{ $articles->previousPageUrl() }}"><i
                                    class="fa-solid fa-chevron-left" style="font-size:11px"></i></a>
                        @endif
                        @foreach ($articles->getUrlRange(max(1, $articles->currentPage() - 2), min($articles->lastPage(), $articles->currentPage() + 2)) as $page => $url)
                            @if ($page == $articles->currentPage())
                                <span class="pagi-btn active">{{ $page }}</span>
                            @else
                                <a class="pagi-btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($articles->hasMorePages())
                            <a class="pagi-btn" href="{{ $articles->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"
                                    style="font-size:11px"></i></a>
                        @else
                            <span class="pagi-btn disabled"><i class="fa-solid fa-chevron-right"
                                    style="font-size:11px"></i></span>
                        @endif
                    </div>
                @endif
            </div>

        </div>{{-- /card --}}

    </main>

    {{-- ══ Modal Article (Ajouter / Modifier) ══ --}}
    <div class="modal-overlay" id="modalArticle">
        <div class="modal-box">
            <div class="modal-hdr">
                <h5><i class="fa-solid fa-newspaper" style="color:var(--primary)"></i>
                    <span id="modalTitle">Nouvel article</span>
                </h5>
                <button class="modal-close" data-modal-close="modalArticle">✕</button>
            </div>
            <div class="modal-body">
                {{-- Titre --}}
                <div class="form-group">
                    <label class="form-label">Titre *</label>
                    <input type="text" id="artTitle" class="form-control"
                        placeholder="Ex: 5 signes que votre voiture a besoin d'une vidange">
                </div>

                {{-- Catégorie + Temps de lecture --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Catégorie *</label>
                        <select id="artCategory" class="form-select" style="width:100%">
                            <option value="">— Sélectionner —</option>
                            @foreach ($categories as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temps de lecture (min)</label>
                        <input type="number" id="artReadTime" class="form-control" placeholder="Auto-calculé"
                            min="1">
                    </div>
                </div>

                {{-- Extrait --}}
                <div class="form-group">
                    <label class="form-label">Extrait</label>
                    <textarea id="artExcerpt" class="form-control" rows="2"
                        placeholder="Résumé court affiché dans la liste (max 500 caractères)..."></textarea>
                </div>

                {{-- Contenu --}}
                <div class="form-group">
                    <label class="form-label">Contenu *</label>
                    <textarea id="artContent" class="form-control" rows="9"
                        placeholder="Rédigez votre article ici... (HTML accepté)"></textarea>
                </div>

                {{-- Tags --}}
                <div class="form-group">
                    <label class="form-label">Tags <span style="font-weight:400;color:var(--text-muted)">(séparés par des
                            virgules)</span></label>
                    <input type="text" id="artTags" class="form-control"
                        placeholder="vidange, entretien, huile moteur">
                </div>

                {{-- Toggle sponsorisé --}}
                <div class="toggle-row" style="margin-bottom:14px">
                    <label class="toggle">
                        <input type="checkbox" id="artSponsored" onchange="toggleSponsor(this.checked)">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="fw-600" style="font-size:13px">Article sponsorisé</span>
                </div>

                {{-- Champs sponsor --}}
                <div id="sponsorFields" style="display:none;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
                    <div>
                        <label class="form-label">Nom du sponsor</label>
                        <input type="text" id="artSponsorName" class="form-control" placeholder="Ex: Castrol">
                    </div>
                    <div>
                        <label class="form-label">URL du sponsor</label>
                        <input type="url" id="artSponsorUrl" class="form-control" placeholder="https://...">
                    </div>
                </div>

                {{-- Toggle publier --}}
                <div class="toggle-row">
                    <label class="toggle">
                        <input type="checkbox" id="artPublish">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="fw-600" style="font-size:13px">Publier immédiatement</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalArticle">Annuler</button>
                <button class="btn btn-secondary" onclick="saveDraft()">
                    <i class="fa-solid fa-floppy-disk"></i> Brouillon
                </button>
                <button class="btn btn-primary" onclick="publishNow()">
                    <i class="fa-solid fa-upload"></i> Publier
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
@endsection
