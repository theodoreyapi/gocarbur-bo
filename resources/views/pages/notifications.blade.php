@extends('layouts.master', ['title' => 'Notifications', 'subTitle' => 'Notifications'])

@push('scripts')
    <script>
        /* ── Flash toasts ────────────────────────────── */
        @if (session('toast_success'))
            showToast(@json(session('toast_success')), 'success');
        @endif
        @if (session('toast_error'))
            showToast(@json(session('toast_error')), 'error');
        @endif

        /* ── Affiche/masque le champ ville ─────────────── */
        function updateTarget(v) {
            document.getElementById('cityField').style.display = v === 'city' ? '' : 'none';
            document.getElementById('userField').style.display = v === 'specific' ? '' : 'none';
        }

        /* ── Suppression broadcast ───────────────────── */
        function deleteBroadcast(id) {
            confirmAction('Supprimer ce broadcast et toutes ses notifications ?', () => {
                document.getElementById(`deleteNotifForm_${id}`).submit();
            });
        }
    </script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1>Notifications Push</h1>
                <p>Envoyez des notifications aux utilisateurs de l'application.</p>
            </div>
        </div>

        {{-- ── KPIs ─────────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
            <div class="stat-card" style="padding:16px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="stat-icon" style="background:#FFF0EB;margin:0;width:40px;height:40px;font-size:17px">
                        <i class="fa-solid fa-paper-plane" style="color:var(--primary)"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size:22px">
                            {{ number_format($stats['sent_today'], 0, ',', ' ') }}</div>
                        <div class="stat-label">Envoyées aujourd'hui</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" style="padding:16px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px">
                        <i class="fa-solid fa-calendar-check" style="color:var(--success)"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size:22px">
                            {{ number_format($stats['sent_month'], 0, ',', ' ') }}</div>
                        <div class="stat-label">Ce mois</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" style="padding:16px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px">
                        <i class="fa-solid fa-bell" style="color:var(--warning)"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size:22px">
                            {{ number_format($stats['unread_total'], 0, ',', ' ') }}</div>
                        <div class="stat-label">Non lues (total)</div>
                    </div>
                </div>
            </div>
            <div class="stat-card" style="padding:16px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="stat-icon" style="background:#DBEAFE;margin:0;width:40px;height:40px;font-size:17px">
                        <i class="fa-solid fa-chart-simple" style="color:var(--info)"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size:22px">{{ $stats['delivery_rate'] }}%</div>
                        <div class="stat-label">Taux de lecture</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Formulaire d'envoi ───────────────────────── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-paper-plane" style="color:var(--primary)"></i>
                    Envoyer une notification
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('notifications.store') }}">
                    @csrf

                    {{-- Ligne 1 : cible + ville + type --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
                        <div>
                            <label class="form-label">Cible *</label>
                            <select name="target" class="form-select @error('target') is-invalid @enderror"
                                onchange="updateTarget(this.value)">
                                <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>Tous les
                                    utilisateurs</option>
                                <option value="premium" {{ old('target') === 'premium' ? 'selected' : '' }}>Utilisateurs
                                    Premium</option>
                                <option value="city" {{ old('target') === 'city' ? 'selected' : '' }}>Par ville
                                </option>
                                <option value="specific" {{ old('target') === 'specific' ? 'selected' : '' }}>Utilisateur
                                    spécifique</option>
                            </select>
                            @error('target')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Champ ville (conditionnel) --}}
                        <div id="cityField" style="{{ old('target') === 'city' ? '' : 'display:none' }}">
                            <label class="form-label">Ville cible</label>
                            <select name="target_city" class="form-select">
                                @foreach ($cities as $city)
                                    <option value="{{ $city }}"
                                        {{ old('target_city') === $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Champ utilisateur spécifique (conditionnel) --}}
                        <div id="userField" style="{{ old('target') === 'specific' ? '' : 'display:none' }}">
                            <label class="form-label">Téléphone ou email</label>
                            <input type="text" name="target_user" class="form-control" placeholder="+225 07 12 34 56"
                                value="{{ old('target_user') }}">
                            @error('target_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                @foreach (\App\Models\Notification::typeLabels() as $val => $label)
                                    <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Titre --}}
                    <div style="margin-bottom:12px">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            placeholder="Ex: Nouvelle promotion !" value="{{ old('title') }}" maxlength="100">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Message --}}
                    <div style="margin-bottom:12px">
                        <label class="form-label">Message *</label>
                        <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="3"
                            placeholder="Contenu de la notification..." maxlength="500">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Lien --}}
                    <div style="margin-bottom:16px">
                        <label class="form-label">Lien deep link (optionnel)</label>
                        <input type="text" name="action_url" class="form-control"
                            placeholder="Ex: /stations/detail/5" value="{{ old('action_url') }}">
                    </div>

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i> Envoyer maintenant
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Historique broadcasts ────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-history" style="color:var(--info)"></i>
                    Historique des broadcasts
                </div>
            </div>

            @if ($broadcasts->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--text-muted)">
                    <i class="fa-solid fa-bullhorn" style="font-size:36px;opacity:.3;margin-bottom:12px"></i>
                    <p>Aucun broadcast envoyé pour l'instant.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Message</th>
                                <th>Envoyés</th>
                                <th>Lus</th>
                                <th>Taux</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($broadcasts as $broadcast)
                                @php
                                    $taux =
                                        $broadcast->total_sent > 0
                                            ? round(($broadcast->total_read / $broadcast->total_sent) * 100)
                                            : 0;
                                    $icon = \App\Models\Notification::typeIcons()[$broadcast->type] ?? 'fa-bell';
                                    $color =
                                        \App\Models\Notification::typeColors()[$broadcast->type] ?? 'var(--primary)';
                                    $label =
                                        \App\Models\Notification::typeLabels()[$broadcast->type] ?? $broadcast->type;
                                @endphp
                                <tr>
                                    {{-- Type --}}
                                    <td>
                                        <span
                                            style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                            <i class="fa-solid {{ $icon }}"
                                                style="color:{{ $color }};font-size:13px"></i>
                                            {{ $label }}
                                        </span>
                                    </td>

                                    {{-- Titre --}}
                                    <td class="fw-600" style="font-size:13px">{{ $broadcast->title }}</td>

                                    {{-- Message --}}
                                    <td
                                        style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;color:var(--text-muted)">
                                        {{ $broadcast->body }}
                                    </td>

                                    {{-- Envoyés --}}
                                    <td class="fw-600">{{ number_format($broadcast->total_sent, 0, ',', ' ') }}</td>

                                    {{-- Lus --}}
                                    <td style="font-size:13px">{{ number_format($broadcast->total_read, 0, ',', ' ') }}
                                    </td>

                                    {{-- Taux lecture --}}
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px">
                                            <div class="progress" style="width:60px;margin:0">
                                                <div class="progress-bar"
                                                    style="width:{{ $taux }}%;background:{{ $taux >= 70 ? 'var(--success)' : ($taux >= 40 ? 'var(--warning)' : 'var(--danger)') }}">
                                                </div>
                                            </div>
                                            <span style="font-size:12px;font-weight:600">{{ $taux }}%</span>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td style="font-size:12px;color:var(--text-muted)">
                                        {{ \Carbon\Carbon::parse($broadcast->sent_at)->format('d M Y H:i') }}
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="deleteBroadcast({{ $broadcast->id_notification }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="deleteNotifForm_{{ $broadcast->id_notification }}" method="POST"
                                            action="{{ route('notifications.destroy', $broadcast->id_notification) }}"
                                            style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </main>

    <div class="toast-container"></div>
@endsection
