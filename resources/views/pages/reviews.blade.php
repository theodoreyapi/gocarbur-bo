@extends('layouts.master', ['title' => 'Avis clients', 'subTitle' => 'Avis clients'])

@push('scripts')
<script>
    /* ── Flash toasts ────────────────────────────── */
    @if(session('toast_success'))
        showToast(@json(session('toast_success')), 'success');
    @endif
    @if(session('toast_error'))
        showToast(@json(session('toast_error')), 'error');
    @endif
    @if(session('toast_info'))
        showToast(@json(session('toast_info')), 'info');
    @endif

    /* ── Actions ─────────────────────────────────── */
    function submitApprove(id) {
        document.getElementById(`approveForm_${id}`).submit();
    }

    function submitDelete(id) {
        confirmAction('Supprimer cet avis définitivement ?', () => {
            document.getElementById(`deleteForm_${id}`).submit();
        });
    }

    function submitApproveAll() {
        confirmAction('Approuver tous les avis en attente ?', () => {
            document.getElementById('approveAllForm').submit();
        });
    }
</script>
@endpush

@section('content')
<main class="page-content">

    {{-- ── Page header ─────────────────────────────── --}}
    <div class="page-header">
        <h1>Modération des avis</h1>
        <p>Approuvez ou supprimez les avis déposés par les utilisateurs.</p>
    </div>

    {{-- ── Alerte en attente ───────────────────────── --}}
    @if($stats['pending'] > 0)
        <div class="alert alert-warning" style="margin-bottom:20px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>
                <strong>{{ $stats['pending'] }} avis</strong>
                {{ $stats['pending'] === 1 ? 'est en attente' : 'sont en attente' }} de modération. Pensez à les traiter régulièrement.
            </span>
        </div>
    @endif

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-clock" style="color:var(--warning)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ $stats['pending'] }}</div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-check" style="color:var(--success)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ number_format($stats['approved'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Approuvés</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#FEE2E2;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-trash" style="color:var(--danger)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ $stats['deleted'] }}</div>
                    <div class="stat-label">Supprimés</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-star" style="color:var(--warning)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ $stats['avg_rating'] }}</div>
                    <div class="stat-label">Note moyenne</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Formulaire approuver tout (caché) ──────── --}}
    <form id="approveAllForm" method="POST" action="{{ route('reviews.approve-all') }}" style="display:none">
        @csrf
    </form>

    {{-- ── Onglets ──────────────────────────────────── --}}
    <div class="card">
        <div class="card-header" style="padding:0 20px" data-tabs>
            <div class="tab-nav">
                <button class="tab-btn active" data-tab="tab-pending">
                    En attente
                    @if($stats['pending'] > 0)
                        <span class="nav-badge" style="background:var(--warning);margin-left:6px;vertical-align:middle">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </button>
                <button class="tab-btn" data-tab="tab-approved">Approuvés</button>
                <button class="tab-btn" data-tab="tab-all">Tous les avis</button>
            </div>
        </div>

        {{-- ══ ONGLET EN ATTENTE ══════════════════════════ --}}
        <div id="tab-pending" class="tab-content active">
            @if($pendingReviews->isEmpty())
                <div style="padding:48px;text-align:center;color:var(--text-muted)">
                    <i class="fa-solid fa-circle-check" style="font-size:40px;margin-bottom:12px;color:var(--success);opacity:.5"></i>
                    <p>Aucun avis en attente. Tout est à jour !</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column">
                    @foreach($pendingReviews as $review)
                        @php
                            $initials = $review->initials;
                            $avatarColors = ['#3B82F6,#1D4ED8','#EF4444,#B91C1C','#10B981,#059669','#F59E0B,#D97706','#8B5CF6,#6D28D9','#EC4899,#BE185D'];
                            $color = $avatarColors[$review->id_review % count($avatarColors)];
                            $isGarage = $review->establishment_type === 'garage';
                            $isLowRating = $review->rating <= 2;
                        @endphp
                        <div style="padding:20px;border-bottom:1px solid var(--border)">
                            <div style="display:flex;align-items:flex-start;gap:14px">

                                {{-- Avatar --}}
                                <div class="user-avatar"
                                    style="width:40px;height:40px;font-size:14px;flex-shrink:0;background:linear-gradient(135deg,{{ $color }})">
                                    {{ $initials }}
                                </div>

                                <div style="flex:1">
                                    {{-- Header --}}
                                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                                        <span class="fw-700" style="font-size:14px">{{ $review->user?->name ?? 'Utilisateur supprimé' }}</span>
                                        <span style="font-size:12px;color:var(--text-muted)">sur</span>
                                        <span style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:{{ $isGarage ? '#8B5CF6' : 'var(--primary)' }}">
                                            <i class="fa-solid {{ $isGarage ? 'fa-wrench' : 'fa-gas-pump' }}" style="font-size:11px"></i>
                                            {{ $review->establishment_name }}
                                        </span>
                                        <span class="badge badge-warning" style="margin-left:auto">
                                            <i class="fa-solid fa-clock" style="font-size:9px"></i> En attente
                                        </span>
                                        <span style="font-size:11px;color:var(--text-muted)">
                                            {{ $review->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    {{-- Étoiles --}}
                                    <div style="display:flex;gap:2px;margin-bottom:8px">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                                            @else
                                                <i class="fa-regular fa-star" style="color:var(--border);font-size:14px"></i>
                                            @endif
                                        @endfor
                                        <span class="fw-700" style="margin-left:6px;font-size:13px">{{ $review->rating }}/5</span>
                                    </div>

                                    {{-- Alerte note basse --}}
                                    @if($isLowRating)
                                        <div class="alert alert-warning" style="margin-bottom:10px;padding:8px 12px">
                                            <i class="fa-solid fa-flag"></i>
                                            <span style="font-size:12px">Note basse détectée — vérifiez le contenu avant approbation.</span>
                                        </div>
                                    @endif

                                    {{-- Commentaire --}}
                                    @if($review->comment)
                                        <p style="font-size:14px;color:var(--text);line-height:1.6;margin-bottom:12px">
                                            "{{ $review->comment }}"
                                        </p>
                                    @else
                                        <p style="font-size:13px;color:var(--text-muted);font-style:italic;margin-bottom:12px">Aucun commentaire.</p>
                                    @endif

                                    {{-- Boutons d'action --}}
                                    <div style="display:flex;gap:10px">
                                        <button class="btn btn-success btn-sm" onclick="submitApprove({{ $review->id_review }})">
                                            <i class="fa-solid fa-check"></i> Approuver
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="submitDelete({{ $review->id_review }})">
                                            <i class="fa-solid fa-trash"></i> Supprimer
                                        </button>
                                    </div>

                                    {{-- Formulaires cachés --}}
                                    <form id="approveForm_{{ $review->id_review }}" method="POST"
                                        action="{{ route('reviews.approve', $review->id_review) }}" style="display:none">
                                        @csrf
                                    </form>
                                    <form id="deleteForm_{{ $review->id_review }}" method="POST"
                                        action="{{ route('reviews.destroy', $review->id_review) }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Footer — approuver tout --}}
                    <div style="padding:16px 20px;background:var(--bg);display:flex;align-items:center;justify-content:space-between">
                        <span style="font-size:13px;color:var(--text-muted)">
                            {{ $stats['pending'] }} {{ $stats['pending'] === 1 ? 'avis en attente' : 'avis en attente' }} de modération
                        </span>
                        <button class="btn btn-success btn-sm" onclick="submitApproveAll()">
                            <i class="fa-solid fa-check-double"></i> Tout approuver
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- ══ ONGLET APPROUVÉS ═══════════════════════════ --}}
        <div id="tab-approved" class="tab-content">

            {{-- Filtres --}}
            <form method="GET" action="{{ route('reviews.index') }}#tab-approved" class="filter-bar">
                <div style="position:relative;flex:1;min-width:200px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" name="search_approved" placeholder="Rechercher..."
                        value="{{ request('search_approved') }}"
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>
                <select name="rating_approved" class="form-select" style="width:130px" onchange="this.form.submit()">
                    <option value="all" {{ request('rating_approved', 'all') === 'all' ? 'selected' : '' }}>Toutes notes</option>
                    @foreach([5,4,3,2,1] as $n)
                        <option value="{{ $n }}" {{ request('rating_approved') == $n ? 'selected' : '' }}>{{ $n }} étoile{{ $n > 1 ? 's' : '' }}</option>
                    @endforeach
                </select>
                <select name="type_approved" class="form-select" style="width:150px" onchange="this.form.submit()">
                    <option value="all" {{ request('type_approved', 'all') === 'all' ? 'selected' : '' }}>Stations & Garages</option>
                    <option value="stations" {{ request('type_approved') === 'stations' ? 'selected' : '' }}>Stations seulement</option>
                    <option value="garages" {{ request('type_approved') === 'garages' ? 'selected' : '' }}>Garages seulement</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Établissement</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedReviews as $review)
                            @php
                                $initials = $review->initials;
                                $avatarColors = ['#3B82F6,#1D4ED8','#EF4444,#B91C1C','#10B981,#059669','#F59E0B,#D97706','#8B5CF6,#6D28D9'];
                                $color = $avatarColors[$review->id_review % count($avatarColors)];
                                $isGarage = $review->establishment_type === 'garage';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="user-avatar"
                                            style="background:linear-gradient(135deg,{{ $color }})">
                                            {{ $initials }}
                                        </div>
                                        <span class="fw-600">{{ $review->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:12px">
                                        <i class="fa-solid {{ $isGarage ? 'fa-wrench' : 'fa-gas-pump' }}"
                                            style="color:{{ $isGarage ? '#8B5CF6' : 'var(--primary)' }};margin-right:4px"></i>
                                        {{ $review->establishment_name }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:3px">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"
                                                style="color:{{ $i <= $review->rating ? 'var(--warning)' : 'var(--border)' }};font-size:12px"></i>
                                        @endfor
                                        <span class="fw-600" style="margin-left:4px;font-size:12px">{{ $review->rating }}</span>
                                    </div>
                                </td>
                                <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:var(--text-muted)">
                                    "{{ Str::limit($review->comment, 60) }}"
                                </td>
                                <td style="font-size:12px;color:var(--text-muted)">
                                    {{ $review->approved_at?->format('d M Y') ?? $review->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="submitDelete({{ $review->id_review }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <form id="deleteForm_{{ $review->id_review }}" method="POST"
                                        action="{{ route('reviews.destroy', $review->id_review) }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">
                                    Aucun avis approuvé trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination approuvés --}}
            <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
                <span style="font-size:13px;color:var(--text-muted)">
                    {{ $approvedReviews->total() }} avis approuvés
                </span>
                {{ $approvedReviews->appends(request()->except('page_all'))->links('vendor.pagination.simple') }}
            </div>
        </div>

        {{-- ══ ONGLET TOUS ════════════════════════════════ --}}
        <div id="tab-all" class="tab-content">

            {{-- Filtres --}}
            <form method="GET" action="{{ route('reviews.index') }}#tab-all" class="filter-bar">
                <div style="position:relative;flex:1;min-width:200px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" name="search_all" placeholder="Rechercher..."
                        value="{{ request('search_all') }}"
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>
                <select name="status_all" class="form-select" style="width:130px" onchange="this.form.submit()">
                    <option value="all" {{ request('status_all', 'all') === 'all' ? 'selected' : '' }}>Tous statuts</option>
                    <option value="pending"  {{ request('status_all') === 'pending'  ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status_all') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="deleted"  {{ request('status_all') === 'deleted'  ? 'selected' : '' }}>Supprimé</option>
                </select>
                <select name="rating_all" class="form-select" style="width:130px" onchange="this.form.submit()">
                    <option value="all" {{ request('rating_all', 'all') === 'all' ? 'selected' : '' }}>Toutes notes</option>
                    @foreach([5,4,3,2,1] as $n)
                        <option value="{{ $n }}" {{ request('rating_all') == $n ? 'selected' : '' }}>{{ $n }} ⭐</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Établissement</th>
                            <th>Note</th>
                            <th>Aperçu</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allReviews as $review)
                            @php
                                $initials = $review->initials;
                                $avatarColors = ['#3B82F6,#1D4ED8','#EF4444,#B91C1C','#10B981,#059669','#F59E0B,#D97706','#8B5CF6,#6D28D9'];
                                $color = $avatarColors[$review->id_review % count($avatarColors)];
                                $isGarage = $review->establishment_type === 'garage';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="user-avatar"
                                            style="background:linear-gradient(135deg,{{ $color }})">
                                            {{ $initials }}
                                        </div>
                                        {{ $review->user?->name ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:12px">
                                        <i class="fa-solid {{ $isGarage ? 'fa-wrench' : 'fa-gas-pump' }}"
                                            style="color:{{ $isGarage ? '#8B5CF6' : 'var(--primary)' }};margin-right:4px"></i>
                                        {{ $review->establishment_name }}
                                    </span>
                                </td>
                                <td>
                                    @for($i = 1; $i <= $review->rating; $i++)⭐@endfor
                                </td>
                                <td style="max-width:180px;font-size:12px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    "{{ Str::limit($review->comment, 40) }}"
                                </td>
                                <td>
                                    @if($review->trashed())
                                        <span class="badge badge-gray">Supprimé</span>
                                    @elseif($review->is_approved)
                                        <span class="badge badge-success">Approuvé</span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </td>
                                <td style="font-size:12px;color:var(--text-muted)">
                                    {{ $review->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display:flex;gap:5px">
                                        @if(!$review->is_approved && !$review->trashed())
                                            <button class="btn btn-success btn-sm" onclick="submitApprove({{ $review->id_review }})">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <form id="approveForm_{{ $review->id_review }}_all" method="POST"
                                                action="{{ route('reviews.approve', $review->id_review) }}" style="display:none">
                                                @csrf
                                            </form>
                                        @endif
                                        @if(!$review->trashed())
                                            <button class="btn btn-danger btn-sm" onclick="submitDelete({{ $review->id_review }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <form id="deleteForm_{{ $review->id_review }}" method="POST"
                                                action="{{ route('reviews.destroy', $review->id_review) }}" style="display:none">
                                                @csrf @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:var(--text-muted);padding:32px">
                                    Aucun avis trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination tous --}}
            <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
                <span style="font-size:13px;color:var(--text-muted)">
                    {{ $allReviews->total() }} avis au total
                </span>
                {{ $allReviews->appends(request()->except('page_approved'))->links('vendor.pagination.simple') }}
            </div>
        </div>

    </div>{{-- /card --}}
</main>

<div class="toast-container"></div>
@endsection
