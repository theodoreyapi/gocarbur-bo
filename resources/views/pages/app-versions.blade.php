@extends('layouts.master', ['title' => 'Versions app', 'subTitle' => "Versions de l'application"])

@push('scripts')
    <script>
        /* ── Flash toasts ────────────────────────────── */
        @if (session('toast_success'))
            showToast(@json(session('toast_success')), 'success');
        @endif

        /* ── Modal détail version ────────────────────── */
        function viewVersion(id) {
            fetch(`/app-versions/${id}`)
                .then(r => r.json())
                .then(v => {
                    document.getElementById('vVersion').textContent = v.version;
                    document.getElementById('vPlatform').textContent = v.platform.charAt(0).toUpperCase() + v.platform
                        .slice(1);
                    document.getElementById('vBuild').textContent = v.build_number;
                    document.getElementById('vStatus').innerHTML =
                        `<span class="badge ${v.status_badge}">${v.status_label}</span>`;
                    document.getElementById('vType').innerHTML =
                        `<span class="badge ${v.release_badge}">${v.release_type.charAt(0).toUpperCase() + v.release_type.slice(1)}</span>`;
                    document.getElementById('vForce').textContent = v.force_label;
                    document.getElementById('vStore').textContent = v.store_url;
                    document.getElementById('vDate').textContent = v.released_at;
                    document.getElementById('vChangelog').textContent = v.changelog;
                    openModal('modalVersionDetail');
                });
        }
    </script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-header"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Gestion des versions</h1>
                <p>Contrôlez les mises à jour forcées et la compatibilité des versions mobiles.</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalAddVersion')">
                <i class="fa-solid fa-plus"></i> Déclarer une version
            </button>
        </div>

        {{-- ── Cartes Android + iOS ────────────────────── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">

            @foreach ([['android', $currentAndroid, '#D1FAE5', '#10B981', 'Google Play Store', $minAndroid], ['ios', $currentIos, '#DBEAFE', '#3B82F6', 'Apple App Store', $minIos]] as [$platform, $current, $bg, $color, $store, $minVer])
                <div class="card">
                    <div class="card-header">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div
                                style="width:40px;height:40px;background:{{ $bg }};border-radius:10px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-brands fa-{{ $platform }}"
                                    style="color:{{ $color }};font-size:22px"></i>
                            </div>
                            <div>
                                <div class="card-title">{{ strtoupper($platform) === 'IOS' ? 'iOS' : 'Android' }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ $store }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Version actuelle vs minimum --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
                                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version actuelle</div>
                                @if ($current)
                                    <div class="fw-800" style="font-size:22px;color:var(--success)">{{ $current->version }}
                                    </div>
                                    <span class="badge badge-success">Production</span>
                                @else
                                    <div class="fw-700" style="font-size:16px;color:var(--text-muted)">—</div>
                                    <span class="badge badge-gray">Non définie</span>
                                @endif
                            </div>
                            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
                                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version minimum</div>
                                <div class="fw-800" style="font-size:22px;color:var(--warning)">{{ $minVer }}</div>
                                <span class="badge badge-warning">Force update &lt; {{ $minVer }}</span>
                            </div>
                        </div>

                        {{-- Formulaire config --}}
                        <form method="POST" action="{{ route('app-versions.config', $platform) }}">
                            @csrf @method('PATCH')

                            <div class="fw-600" style="font-size:13px;margin-bottom:8px">Paramètres de mise à jour</div>
                            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">

                                {{-- Toggle forcer --}}
                                <div
                                    style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Forcer la mise à jour</div>
                                        <div style="font-size:11px;color:var(--text-muted)">Bloque l'accès si version &lt;
                                            min</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" name="force_required"
                                            {{ $current?->force_update === 'required' ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                {{-- Toggle recommandée --}}
                                <div
                                    style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Mise à jour recommandée</div>
                                        <div style="font-size:11px;color:var(--text-muted)">Affiche une suggestion de mise à
                                            jour</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" name="force_optional"
                                            {{ in_array($current?->force_update, ['optional', 'required']) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div style="margin-bottom:14px">
                                <label class="form-label" style="font-size:12px">Lien
                                    {{ $platform === 'android' ? 'Play Store' : 'App Store' }}</label>
                                <input type="url" name="store_url" class="form-control"
                                    value="{{ $current?->store_url }}" style="font-size:12px;padding:7px 10px">
                            </div>

                            {{-- Champ caché force_update calculé via JS --}}
                            <input type="hidden" name="force_update" value="{{ $current?->force_update ?? 'none' }}">

                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i>
                                Sauvegarder ({{ $platform === 'ios' ? 'iOS' : 'Android' }})
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ── Historique des versions ─────────────────── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--info)"></i>
                    Historique des versions
                </div>
                <form method="GET" action="{{ route('app-versions.index') }}">
                    <select name="platform" class="form-select" style="width:160px" onchange="this.form.submit()">
                        <option value="all" {{ request('platform', 'all') === 'all' ? 'selected' : '' }}>Toutes
                            plateformes</option>
                        <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android</option>
                        <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                    </select>
                </form>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Plateforme</th>
                            <th>Statut</th>
                            <th>Type release</th>
                            <th>Changelog</th>
                            <th>Publiée le</th>
                            <th>Build</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $v)
                            @php
                                $rowBg = $v->is_current
                                    ? ($v->platform === 'android'
                                        ? 'background:#F0FDF4'
                                        : 'background:#EFF6FF')
                                    : '';
                            @endphp
                            <tr style="{{ $rowBg }}">

                                {{-- Version --}}
                                <td>
                                    <span class="fw-800"
                                        style="font-size:15px;color:{{ $v->is_current ? 'var(--success)' : 'var(--text-muted)' }}">
                                        {{ $v->version }}
                                    </span>
                                </td>

                                {{-- Plateforme --}}
                                <td>
                                    <i class="{{ $v->platform_icon }}" style="color:{{ $v->platform_color }}"></i>
                                    {{ $v->platform === 'ios' ? 'iOS' : 'Android' }}
                                </td>

                                {{-- Statut --}}
                                <td>
                                    <span class="badge {{ $v->status_badge }}">
                                        <i class="fa-solid fa-circle{{ $v->force_update === 'required' ? '-exclamation' : '' }}"
                                            style="font-size:{{ $v->force_update === 'required' ? '9' : '7' }}px"></i>
                                        {{ $v->status_label }}
                                    </span>
                                </td>

                                {{-- Type release --}}
                                <td>
                                    <span class="badge {{ $v->release_type_badge }}">
                                        {{ ucfirst($v->release_type) }}
                                    </span>
                                </td>

                                {{-- Changelog --}}
                                <td
                                    style="font-size:12px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted)">
                                    {{ Str::limit($v->changelog, 60) ?? '—' }}
                                </td>

                                {{-- Date --}}
                                <td style="font-size:12px">
                                    {{ $v->released_at?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Build --}}
                                <td style="font-size:12px;color:var(--text-muted)">#{{ $v->build_number }}</td>

                                {{-- Actions --}}
                                <td>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="viewVersion({{ $v->id_app_version }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;color:var(--text-muted);padding:32px">
                                    Aucune version déclarée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Alerte force update --}}
            @php
                $forceCount = $history->where('force_update', 'required')->count();
                $minVer = $history->where('force_update', 'required')->sortBy('version')->first()?->version;
            @endphp
            @if ($forceCount > 0)
                <div style="padding:14px 20px;border-top:1px solid var(--border)">
                    <div class="alert alert-info" style="margin:0">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>
                            Les versions &lt; <strong>{{ $minVer }}</strong> sont bloquées avec une obligation de
                            mise à jour.
                            <strong>{{ $forceCount }}</strong> entrée(s) concernée(s) dans l'historique.
                        </span>
                    </div>
                </div>
            @endif
        </div>

    </main>

    {{-- ── Modal Déclarer une version ──────────────── --}}
    <div class="modal-overlay" id="modalAddVersion">
        <div class="modal-box" style="max-width:520px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-mobile-screen" style="color:var(--primary)"></i> Déclarer une nouvelle version
                </h5>
                <button class="modal-close" data-modal-close="modalAddVersion">✕</button>
            </div>
            <form method="POST" action="{{ route('app-versions.store') }}">
                @csrf
                <div class="modal-body">
                    <div style="display:flex;flex-direction:column;gap:14px">

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                            <div>
                                <label class="form-label">Numéro de version *</label>
                                <input type="text" name="version"
                                    class="form-control @error('version') is-invalid @enderror" placeholder="Ex: 1.5.0"
                                    value="{{ old('version') }}">
                                @error('version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Plateforme *</label>
                                <select name="platform" class="form-select">
                                    <option value="android" {{ old('platform') === 'android' ? 'selected' : '' }}>Android
                                    </option>
                                    <option value="ios" {{ old('platform') === 'ios' ? 'selected' : '' }}>iOS
                                    </option>
                                    <option value="both" {{ old('platform') === 'both' ? 'selected' : '' }}>Les deux
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Build number *</label>
                                <input type="number" name="build_number"
                                    class="form-control @error('build_number') is-invalid @enderror" placeholder="Ex: 142"
                                    value="{{ old('build_number') }}" min="1">
                                @error('build_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Date de publication</label>
                                <input type="date" name="released_at" class="form-control"
                                    value="{{ old('released_at', now()->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Notes de version (changelog)</label>
                            <textarea name="changelog" class="form-control" rows="4"
                                placeholder="• Nouvelle fonctionnalité X&#10;• Correction du bug Y&#10;• Amélioration des performances Z">{{ old('changelog') }}</textarea>
                        </div>

                        <div style="border-top:1px solid var(--border);padding-top:14px">
                            <div class="fw-600" style="font-size:13px;margin-bottom:10px">Contrôle des mises à jour</div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div
                                    style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Version actuelle (production)</div>
                                        <div style="font-size:11px;color:var(--text-muted)">Marquer comme version en cours
                                        </div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" name="is_current" value="1"
                                            {{ old('is_current') ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div
                                    style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
                                    <label class="form-label" style="margin-bottom:6px">Type de mise à jour</label>
                                    <select name="force_update" class="form-select">
                                        @foreach (\App\Models\AppVersion::forceUpdateLabels() as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ old('force_update') === $val ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Version minimum requise (après ce déploiement)</label>
                            <input type="text" name="min_version" class="form-control"
                                placeholder="Ex: 1.2.0 — laissez vide pour ne pas changer"
                                value="{{ old('min_version') }}">
                        </div>

                        <div>
                            <label class="form-label">Lien store</label>
                            <input type="url" name="store_url" class="form-control"
                                placeholder="https://play.google.com/... ou https://apps.apple.com/..."
                                value="{{ old('store_url') }}">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close="modalAddVersion">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Enregistrer la version
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Détail version ─────────────────────── --}}
    <div class="modal-overlay" id="modalVersionDetail">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-mobile-screen" style="color:var(--info)"></i> Détail de la version</h5>
                <button class="modal-close" data-modal-close="modalVersionDetail">✕</button>
            </div>
            <div class="modal-body">
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:14px;margin-bottom:14px">
                    <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;font-size:13px">
                        <span style="color:var(--text-muted)">Version</span>
                        <span class="fw-800" style="font-size:16px" id="vVersion">—</span>

                        <span style="color:var(--text-muted)">Plateforme</span>
                        <span class="fw-600" id="vPlatform">—</span>

                        <span style="color:var(--text-muted)">Build</span>
                        <span class="fw-600" id="vBuild">—</span>

                        <span style="color:var(--text-muted)">Statut</span>
                        <span id="vStatus">—</span>

                        <span style="color:var(--text-muted)">Type</span>
                        <span id="vType">—</span>

                        <span style="color:var(--text-muted)">Mise à jour</span>
                        <span class="fw-600" id="vForce">—</span>

                        <span style="color:var(--text-muted)">Store URL</span>
                        <span style="font-size:11px;word-break:break-all" id="vStore">—</span>

                        <span style="color:var(--text-muted)">Publiée le</span>
                        <span class="fw-600" id="vDate">—</span>
                    </div>
                </div>
                <div>
                    <div class="fw-600" style="font-size:13px;margin-bottom:6px">Changelog</div>
                    <pre id="vChangelog"
                        style="background:#1E293B;color:#94A3B8;padding:12px;border-radius:var(--radius-sm);font-size:11px;white-space:pre-wrap;margin:0">—</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalVersionDetail">Fermer</button>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection
