{{--
    Partial partagé : modal créer ET modifier
    Variables attendues :
        $positionLabels : array  (depuis le contrôleur)
        $targetLabels   : array
        $banner         : Banner|null  (null pour la création)
--}}
@php $b = $banner ?? null; @endphp

<div style="display:flex;flex-direction:column;gap:14px">

    {{-- Nom interne --}}
    <div>
        <label class="form-label">Nom interne *</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            placeholder="Ex: Castrol GTX — Accueil Mars 2025"
            value="{{ old('title', $b?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

        {{-- Position --}}
        <div>
            <label class="form-label">Position d'affichage *</label>
            <select name="position" class="form-select @error('position') is-invalid @enderror">
                @foreach($positionLabels as $value => $label)
                    <option value="{{ $value }}" {{ old('position', $b?->position) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Annonceur --}}
        <div>
            <label class="form-label">Annonceur</label>
            <input type="text" name="advertiser_name" class="form-control"
                placeholder="Nom de l'entreprise"
                value="{{ old('advertiser_name', $b?->advertiser_name) }}">
        </div>

        {{-- Date début --}}
        <div>
            <label class="form-label">Date de début *</label>
            <input type="date" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror"
                value="{{ old('starts_at', $b?->starts_at?->format('Y-m-d')) }}">
            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Date fin --}}
        <div>
            <label class="form-label">Date de fin *</label>
            <input type="date" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror"
                value="{{ old('ends_at', $b?->ends_at?->format('Y-m-d')) }}">
            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Image --}}
    <div>
        <label class="form-label">Image de la bannière
            <span style="font-weight:400;color:var(--text-muted)">(recommandé : 1 200 × 400 px)</span>
        </label>
        @if($b?->image_url)
            <div style="margin-bottom:6px">
                <img src="{{ $b->image_url }}" alt="Aperçu"
                    style="height:60px;border-radius:var(--radius-sm);object-fit:cover">
            </div>
        @endif
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- URL de destination --}}
    <div>
        <label class="form-label">URL de destination (lien au clic)</label>
        <input type="url" name="action_url" class="form-control @error('action_url') is-invalid @enderror"
            placeholder="https://..."
            value="{{ old('action_url', $b?->action_url) }}">
        @error('action_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Ciblage --}}
    <div>
        <label class="form-label">Ciblage utilisateurs</label>
        <select name="target_type" class="form-select" id="targetTypeSelect"
            onchange="document.getElementById('cityRow').style.display = this.value === 'city' ? '' : 'none'">
            @foreach($targetLabels as $value => $label)
                <option value="{{ $value }}" {{ old('target_type', $b?->target_type ?? 'all') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Ville (conditionnelle) --}}
    <div id="cityRow" style="{{ old('target_type', $b?->target_type) === 'city' ? '' : 'display:none' }}">
        <label class="form-label">Ville cible</label>
        <input type="text" name="target_city" class="form-control"
            placeholder="Ex: Abidjan"
            value="{{ old('target_city', $b?->target_city) }}">
    </div>

    {{-- Prix --}}
    <div>
        <label class="form-label">Montant payé (FCFA)</label>
        <input type="number" name="price_paid" class="form-control" min="0" step="500"
            placeholder="Ex: 150000"
            value="{{ old('price_paid', $b?->price_paid) }}">
    </div>

</div>
