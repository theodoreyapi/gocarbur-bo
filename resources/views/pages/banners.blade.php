@extends('layouts.master', ['title' => 'Bannières publicitaires', 'subTitle' => 'Bannières publicitaires'])

@push('scripts')
<script>
    /* ── Toast flash ─────────────────────────────── */
    @if(session('toast_success'))
        showToast(@json(session('toast_success')), 'success');
    @endif
    @if(session('toast_info'))
        showToast(@json(session('toast_info')), 'info');
    @endif
    @if(session('toast_error'))
        showToast(@json(session('toast_error')), 'error');
    @endif

    /* ── Helpers ─────────────────────────────────── */
    function openEditModal(id) {
        fetch(`/banners/${id}/edit`)
            .then(r => r.json())
            .then(b => {
                const f = document.getElementById('editForm');
                f.action = `/banners/${b.id_banner}`;
                f.querySelector('[name=title]').value           = b.title ?? '';
                f.querySelector('[name=position]').value        = b.position ?? '';
                f.querySelector('[name=advertiser_name]').value = b.advertiser_name ?? '';
                f.querySelector('[name=starts_at]').value       = b.starts_at ?? '';
                f.querySelector('[name=ends_at]').value         = b.ends_at ?? '';
                f.querySelector('[name=action_url]').value      = b.action_url ?? '';
                f.querySelector('[name=target_type]').value     = b.target_type ?? 'all';
                f.querySelector('[name=target_city]').value     = b.target_city ?? '';
                f.querySelector('[name=price_paid]').value      = b.price_paid ?? '';
                openModal('modalEditBanner');
            });
    }

    function submitToggle(id, currentState) {
        const form = document.getElementById(`toggleForm_${id}`);
        document.getElementById(`toggleActive_${id}`).value = currentState ? '0' : '1';
        form.submit();
    }

    function confirmDelete(id) {
        confirmAction('Supprimer définitivement cette bannière ?', () => {
            document.getElementById(`deleteForm_${id}`).submit();
        });
    }
</script>
@endpush

@section('content')
<main class="page-content">

    {{-- ── Page header ─────────────────────────────── --}}
    <div class="page-header"
        style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1>Bannières publicitaires</h1>
            <p>Gérez les espaces publicitaires affichés dans l'application.</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalAddBanner')">
            <i class="fa-solid fa-plus"></i> Nouvelle bannière
        </button>
    </div>

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-rectangle-ad" style="color:var(--success)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ $stats['active_count'] }}</div>
                    <div class="stat-label">Bannières actives</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#DBEAFE;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-eye" style="color:var(--info)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ number_format($stats['impressions_total'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Impressions ce mois</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#FFF0EB;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-arrow-pointer" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ number_format($stats['clicks_total'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Clics ce mois</div>
                </div>
            </div>
        </div>
        <div class="stat-card" style="padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px">
                    <i class="fa-solid fa-percent" style="color:var(--warning)"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:22px">{{ $stats['ctr_avg'] }}%</div>
                    <div class="stat-label">Taux de clic (CTR)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Cards visuelles ─────────────────────────── --}}
    @if($banners->isEmpty())
        <div class="card" style="text-align:center;padding:48px;color:var(--text-muted)">
            <i class="fa-solid fa-rectangle-ad" style="font-size:40px;margin-bottom:12px;opacity:.3"></i>
            <p>Aucune bannière pour l'instant. Créez-en une !</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;margin-bottom:24px">
            @foreach($banners as $banner)
                @php
                    $ctr = $banner->impressions_count > 0
                        ? round(($banner->clicks_count / $banner->impressions_count) * 100, 1)
                        : 0;
                    $ctrColor = $ctr >= 4 ? 'var(--success)' : ($ctr >= 3 ? 'var(--warning)' : 'var(--text-muted)');
                    $gradient = match($banner->position) {
                        'home_top'      => 'linear-gradient(135deg,#FF6B35,#FF9A6C)',
                        'home_middle'   => 'linear-gradient(135deg,#F59E0B,#D97706)',
                        'articles_list' => 'linear-gradient(135deg,#3B82F6,#1D4ED8)',
                        'stations_list' => 'linear-gradient(135deg,#10B981,#059669)',
                        'garages_list'  => 'linear-gradient(135deg,#8B5CF6,#6D28D9)',
                        'map_bottom'    => 'linear-gradient(135deg,#EC4899,#BE185D)',
                        default         => 'linear-gradient(135deg,#64748B,#475569)',
                    };
                @endphp
                <div class="card" style="overflow:hidden{{ !$banner->is_active ? ';opacity:.65' : '' }}">

                    {{-- Preview header --}}
                    <div style="height:100px;background:{{ $gradient }};display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden">
                        @if($banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
                            <div style="position:absolute;inset:0;background:rgba(0,0,0,.35)"></div>
                        @endif
                        <div style="text-align:center;color:#fff;position:relative;z-index:1;padding:0 12px">
                            <div style="font-size:17px;font-weight:800">{{ $banner->advertiser_name ?? $banner->title }}</div>
                            <div style="font-size:11px;opacity:.9">{{ $banner->position_label }}</div>
                        </div>
                        <div style="position:absolute;top:8px;right:8px">
                            @if($banner->is_active)
                                <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active</span>
                            @else
                                <span class="badge badge-gray"><i class="fa-solid fa-circle" style="font-size:7px"></i> Désactivée</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                            <div>
                                <div class="fw-700" style="font-size:14px">{{ $banner->title }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ $banner->position_label }}</div>
                            </div>

                            {{-- Actions dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary" data-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="openEditModal({{ $banner->id_banner }})">
                                        <i class="fa-solid fa-pen"></i> Modifier
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="submitToggle({{ $banner->id_banner }}, {{ $banner->is_active ? 1 : 0 }})">
                                        @if($banner->is_active)
                                            <i class="fa-solid fa-pause"></i> Désactiver
                                        @else
                                            <i class="fa-solid fa-play"></i> Réactiver
                                        @endif
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $banner->id_banner }})">
                                        <i class="fa-solid fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>

                            {{-- Hidden toggle form --}}
                            <form id="toggleForm_{{ $banner->id_banner }}" method="POST"
                                action="{{ route('banners.update', $banner->id_banner) }}" style="display:none">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" id="toggleActive_{{ $banner->id_banner }}">
                            </form>

                            {{-- Hidden delete form --}}
                            <form id="deleteForm_{{ $banner->id_banner }}" method="POST"
                                action="{{ route('banners.destroy', $banner->id_banner) }}" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                        </div>

                        {{-- Stats --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px">
                            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
                                <div class="fw-700" style="font-size:15px">{{ number_format($banner->impressions_count, 0, ',', ' ') }}</div>
                                <div style="font-size:10px;color:var(--text-muted)">Impressions</div>
                            </div>
                            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
                                <div class="fw-700" style="font-size:15px">{{ number_format($banner->clicks_count, 0, ',', ' ') }}</div>
                                <div style="font-size:10px;color:var(--text-muted)">Clics</div>
                            </div>
                            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
                                <div class="fw-700" style="font-size:15px;color:{{ $ctrColor }}">{{ $ctr }}%</div>
                                <div style="font-size:10px;color:var(--text-muted)">CTR</div>
                            </div>
                        </div>

                        <div style="font-size:11px;color:var(--text-muted)">
                            {{ $banner->starts_at->format('d M Y') }} → {{ $banner->ends_at->format('d M Y') }}
                            • {{ $banner->target_label }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Tableau détaillé ─────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-table" style="color:var(--info)"></i> Vue tableau — Toutes les bannières
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Position</th>
                            <th>Annonceur</th>
                            <th>Impressions</th>
                            <th>Clics</th>
                            <th>CTR</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                            @php
                                $ctr = $banner->impressions_count > 0
                                    ? round(($banner->clicks_count / $banner->impressions_count) * 100, 1)
                                    : 0;
                                $ctrColor = $ctr >= 4 ? 'var(--success)' : ($ctr >= 3 ? 'var(--warning)' : 'var(--text-muted)');
                            @endphp
                            <tr style="{{ !$banner->is_active ? 'opacity:.65' : '' }}">
                                <td class="fw-600">{{ $banner->title }}</td>
                                <td><span class="badge badge-primary">{{ $banner->position_label }}</span></td>
                                <td>{{ $banner->advertiser_name ?? '—' }}</td>
                                <td>{{ number_format($banner->impressions_count, 0, ',', ' ') }}</td>
                                <td>{{ number_format($banner->clicks_count, 0, ',', ' ') }}</td>
                                <td><span class="fw-700" style="color:{{ $ctrColor }}">{{ $ctr }}%</span></td>
                                <td style="font-size:12px">
                                    {{ $banner->starts_at->format('d M') }} → {{ $banner->ends_at->format('d M Y') }}
                                </td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-gray">Désactivée</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary" data-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" onclick="openEditModal({{ $banner->id_banner }})">
                                                <i class="fa-solid fa-pen"></i> Modifier
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="submitToggle({{ $banner->id_banner }}, {{ $banner->is_active ? 1 : 0 }})">
                                                @if($banner->is_active)
                                                    <i class="fa-solid fa-pause"></i> Désactiver
                                                @else
                                                    <i class="fa-solid fa-play"></i> Réactiver
                                                @endif
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $banner->id_banner }})">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</main>

{{-- ── Modal — Nouvelle bannière ───────────────── --}}
<div class="modal-overlay" id="modalAddBanner">
    <div class="modal-box" style="max-width:560px">
        <div class="modal-header">
            <h5><i class="fa-solid fa-rectangle-ad" style="color:var(--primary)"></i> Nouvelle bannière</h5>
            <button class="modal-close" data-modal-close="modalAddBanner">✕</button>
        </div>
        <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                @include('pages.banners._form', ['positionLabels' => $positionLabels, 'targetLabels' => $targetLabels])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close="modalAddBanner">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Créer la bannière
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal — Modifier bannière ───────────────── --}}
<div class="modal-overlay" id="modalEditBanner">
    <div class="modal-box" style="max-width:560px">
        <div class="modal-header">
            <h5><i class="fa-solid fa-pen" style="color:var(--primary)"></i> Modifier la bannière</h5>
            <button class="modal-close" data-modal-close="modalEditBanner">✕</button>
        </div>
        <form id="editForm" method="POST" action="" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                @include('pages.banners._form', ['positionLabels' => $positionLabels, 'targetLabels' => $targetLabels])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close="modalEditBanner">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="toast-container"></div>
@endsection
