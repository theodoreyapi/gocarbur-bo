@extends('layouts.master', ['title' => 'Paramètres', 'subTitle' => 'Paramètres'])

@push('scripts')
    <script>
        /* ── Flash toasts ────────────────────────────── */
        @if (session('toast_success'))
            showToast(@json(session('toast_success')), 'success');
        @endif
    </script>
@endpush

{{-- ── Macro Blade : champ input générique ──────── --}}
@php
    /**
     * Récupère la valeur d'un setting, avec fallback sur le défaut fourni.
 */
function sv(\Illuminate\Support\Collection $settings, string $key, mixed $default = ''): mixed
{
    $s = $settings->get($key);
    if (!$s) {
        return $default;
    }
    if ($s->type === 'boolean') {
        return filter_var($s->value, FILTER_VALIDATE_BOOLEAN);
    }
    if ($s->type === 'integer') {
        return (int) $s->value;
    }
    if ($s->type === 'decimal') {
            return (float) $s->value;
        }
        return $s->value ?? $default;
    }
@endphp

@section('content')
    <main class="page-content">

        <div class="page-header">
            <h1>Paramètres de la plateforme</h1>
            <p>Configuration globale d'AutoPlatform — tous les paramètres applicables à l'application mobile et à l'API.</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

            {{-- ══ TARIFICATION ═══════════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-sack-dollar" style="color:var(--warning)"></i>
                        Tarification abonnements
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'pricing') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div
                                style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);font-size:12px;color:var(--text-muted);border-left:3px solid var(--warning)">
                                ⚠️ La modification des tarifs n'affecte pas les abonnements en cours. Elle s'applique aux
                                nouveaux abonnements uniquement.
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                @foreach ([['price_user_premium_monthly', 'User Premium — Mensuel (FCFA)'], ['price_user_premium_quarterly', 'User Premium — Trimestriel (FCFA)'], ['price_user_premium_annual', 'User Premium — Annuel (FCFA)']] as [$key, $label])
                                    <div>
                                        <label class="form-label" style="font-size:12px">{{ $label }}</label>
                                        <input type="number" name="{{ $key }}" class="form-control"
                                            style="font-size:13px" value="{{ sv($settings, $key) }}">
                                    </div>
                                @endforeach
                            </div>
                            <div
                                style="border-top:1px solid var(--border);padding-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                @foreach ([['price_station_pro_monthly', 'Station Pro — Mensuel (FCFA)'], ['price_station_premium_monthly', 'Station Premium — Mensuel (FCFA)'], ['price_garage_pro_monthly', 'Garage Pro — Mensuel (FCFA)'], ['price_garage_premium_monthly', 'Garage Premium — Mensuel (FCFA)']] as [$key, $label])
                                    <div>
                                        <label class="form-label" style="font-size:12px">{{ $label }}</label>
                                        <input type="number" name="{{ $key }}" class="form-control"
                                            style="font-size:13px" value="{{ sv($settings, $key) }}">
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder les tarifs
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ PARAMÈTRES GÉNÉRAUX ═════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-sliders" style="color:var(--info)"></i>
                        Paramètres généraux
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'general') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div>
                                <label class="form-label">Nom de l'application</label>
                                <input type="text" name="app_name" class="form-control"
                                    value="{{ sv($settings, 'app_name', 'AutoPlatform') }}">
                            </div>
                            <div>
                                <label class="form-label">Slogan</label>
                                <input type="text" name="app_slogan" class="form-control"
                                    value="{{ sv($settings, 'app_slogan') }}">
                            </div>
                            <div>
                                <label class="form-label">Email de support</label>
                                <input type="email" name="support_email" class="form-control"
                                    value="{{ sv($settings, 'support_email') }}">
                            </div>
                            <div>
                                <label class="form-label">WhatsApp support</label>
                                <input type="text" name="support_whatsapp" class="form-control"
                                    value="{{ sv($settings, 'support_whatsapp') }}">
                            </div>
                            <div>
                                <label class="form-label">Rayon de recherche par défaut (km)</label>
                                <input type="number" name="search_radius_km" class="form-control"
                                    value="{{ sv($settings, 'search_radius_km', 10) }}" min="1" max="50">
                            </div>

                            @foreach ([['maintenance_mode', 'Mode maintenance', "L'app affiche un message de maintenance aux utilisateurs"], ['registration_open', 'Inscription ouverte', 'Autoriser les nouvelles inscriptions']] as [$key, $label, $desc])
                                <div
                                    style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between">
                                    <div>
                                        <div class="fw-600" style="font-size:13px">{{ $label }}</div>
                                        <div style="font-size:12px;color:var(--text-muted)">{{ $desc }}</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" name="{{ $key }}" value="1"
                                            {{ sv($settings, $key) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ NOTIFICATIONS AUTO ═══════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-bell" style="color:var(--primary)"></i>
                        Notifications automatiques
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'notifications') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:12px">
                            @foreach ([['notif_document_expiry', 'Rappel expiration documents', 'Notifier J-30 et J-7 avant expiration'], ['notif_fuel_alert', 'Alerte baisse prix carburant', 'Notifier les users Premium des stations proches'], ['notif_sub_expiry', 'Rappel expiration abonnement', 'J-7 avant l\'expiration du plan'], ['notif_promotions', 'Notification nouvelles promotions', 'Envoyer un push quand une promo est créée'], ['notif_welcome', 'Bienvenue nouveaux inscrits', 'Push automatique à l\'inscription']] as [$key, $label, $desc])
                                <div
                                    style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                                    <div>
                                        <div class="fw-600" style="font-size:13px">{{ $label }}</div>
                                        <div style="font-size:12px;color:var(--text-muted)">{{ $desc }}</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" name="{{ $key }}" value="1"
                                            {{ sv($settings, $key) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            @endforeach

                            <div style="border-top:1px solid var(--border);padding-top:12px">
                                <label class="form-label">Rappel document — jours avant expiration</label>
                                <input type="number" name="notif_doc_days_before" class="form-control"
                                    value="{{ sv($settings, 'notif_doc_days_before', 30) }}" min="1"
                                    max="90">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ SÉCURITÉ & OTP ══════════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-shield-halved" style="color:var(--success)"></i>
                        Sécurité & Authentification
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'security') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                <div>
                                    <label class="form-label">Durée validité OTP (minutes)</label>
                                    <input type="number" name="otp_validity_minutes" class="form-control"
                                        value="{{ sv($settings, 'otp_validity_minutes', 5) }}" min="1"
                                        max="15">
                                </div>
                                <div>
                                    <label class="form-label">Max tentatives OTP / heure</label>
                                    <input type="number" name="otp_max_attempts" class="form-control"
                                        value="{{ sv($settings, 'otp_max_attempts', 3) }}" min="1"
                                        max="10">
                                </div>
                                <div>
                                    <label class="form-label">Durée token auth (jours)</label>
                                    <input type="number" name="auth_token_days" class="form-control"
                                        value="{{ sv($settings, 'auth_token_days', 30) }}" min="1"
                                        max="365">
                                </div>
                                <div>
                                    <label class="form-label">Longueur code OTP</label>
                                    <select name="otp_length" class="form-select">
                                        <option value="6"
                                            {{ sv($settings, 'otp_length', 6) == 6 ? 'selected' : '' }}>6 chiffres</option>
                                        <option value="4"
                                            {{ sv($settings, 'otp_length', 6) == 4 ? 'selected' : '' }}>4 chiffres</option>
                                    </select>
                                </div>
                            </div>

                            <div
                                style="display:flex;flex-direction:column;gap:10px;border-top:1px solid var(--border);padding-top:12px">
                                @foreach ([['review_moderation', 'Modération avis obligatoire', 'Les avis passent en attente avant publication'], ['admin_activity_log', "Journal d'activité admin activé", 'Enregistrer toutes les actions des administrateurs'], ['ip_bruteforce_block', 'Blocage IP après 10 tentatives', 'Sécurité anti-brute-force']] as [$key, $label, $desc])
                                    <div
                                        style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                                        <div>
                                            <div class="fw-600" style="font-size:13px">{{ $label }}</div>
                                            <div style="font-size:12px;color:var(--text-muted)">{{ $desc }}</div>
                                        </div>
                                        <label class="toggle">
                                            <input type="checkbox" name="{{ $key }}" value="1"
                                                {{ sv($settings, $key) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ INTÉGRATIONS PAIEMENT ═══════════════════════════ --}}
            <div class="card" style="grid-column:span 2">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-credit-card" style="color:var(--purple)"></i>
                        Intégrations paiement Mobile Money
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'payments') }}">
                        @csrf
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">

                            @foreach ([['orange', 'Orange Money', 'CinetPay Gateway', '#FF6600', 'fff', 'OM', 'payment_orange_enabled', 'payment_orange_site_id', 'payment_orange_secret', 'Site ID', 'Clé secrète'], ['wave', 'Wave', 'Wave Checkout API', '#1CB5E0', 'fff', 'WV', 'payment_wave_enabled', 'payment_wave_api_key', 'payment_wave_webhook', 'API Key', 'Webhook Secret'], ['mtn', 'MTN MoMo', 'MTN Mobile Money API', '#FFCC00', '333', 'MTN', 'payment_mtn_enabled', 'payment_mtn_api_key', 'payment_mtn_sub_key', 'API Key', 'Subscription Key'], ['moov', 'Moov Money', 'Flooz API', '#00A651', 'fff', 'MV', 'payment_moov_enabled', 'payment_moov_token', 'payment_moov_secret', 'Token', 'Secret']] as [$slug, $name, $sub, $bg, $fg, $abbr, $keyEnabled, $keyField1, $keyField2, $label1, $label2])
                                <div
                                    style="padding:16px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--bg)">
                                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                                        <div
                                            style="width:36px;height:36px;background:{{ $bg }};border-radius:8px;display:flex;align-items:center;justify-content:center;color:#{{ $fg }};font-weight:700;font-size:11px;flex-shrink:0">
                                            {{ $abbr }}
                                        </div>
                                        <div>
                                            <div class="fw-700" style="font-size:14px">{{ $name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">{{ $sub }}</div>
                                        </div>
                                        <label class="toggle" style="margin-left:auto">
                                            <input type="checkbox" name="{{ $keyEnabled }}" value="1"
                                                {{ sv($settings, $keyEnabled) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:8px">
                                        <div>
                                            <label class="form-label" style="font-size:11px">{{ $label1 }}</label>
                                            <input type="password" name="{{ $keyField1 }}" class="form-control"
                                                value="{{ sv($settings, $keyField1) ?: '' }}"
                                                placeholder="{{ sv($settings, $keyField1) ? '••••••••••••••••' : 'Non configuré' }}"
                                                style="font-size:12px;padding:6px 10px">
                                        </div>
                                        <div>
                                            <label class="form-label" style="font-size:11px">{{ $label2 }}</label>
                                            <input type="password" name="{{ $keyField2 }}" class="form-control"
                                                value="{{ sv($settings, $keyField2) ?: '' }}"
                                                placeholder="{{ sv($settings, $keyField2) ? '••••••••••••••••' : 'Non configuré' }}"
                                                style="font-size:12px;padding:6px 10px">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div style="margin-top:16px">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder les intégrations
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ SMS & FIREBASE ══════════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-message" style="color:var(--info)"></i>
                        SMS & Push (Firebase)
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'integrations') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div>
                                <label class="form-label">Fournisseur SMS OTP</label>
                                <select name="sms_provider" class="form-select">
                                    @foreach (['Infobip', 'Twilio', 'Orange SMS API'] as $provider)
                                        <option {{ sv($settings, 'sms_provider') === $provider ? 'selected' : '' }}>
                                            {{ $provider }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Infobip API Key</label>
                                <input type="password" name="sms_api_key" class="form-control"
                                    placeholder="{{ sv($settings, 'sms_api_key') ? '••••••••••••••••' : 'Non configuré' }}">
                            </div>
                            <div>
                                <label class="form-label">Expéditeur SMS (SenderID)</label>
                                <input type="text" name="sms_sender_id" class="form-control"
                                    value="{{ sv($settings, 'sms_sender_id', 'AutoPlat') }}">
                            </div>
                            <div style="border-top:1px solid var(--border);padding-top:14px">
                                <div style="margin-bottom:10px">
                                    <label class="form-label">Firebase Server Key (FCM)</label>
                                    <input type="password" name="fcm_server_key" class="form-control"
                                        placeholder="{{ sv($settings, 'fcm_server_key') ? '••••••••••••••••' : 'Non configuré' }}">
                                </div>
                                <div>
                                    <label class="form-label">Firebase Project ID</label>
                                    <input type="text" name="fcm_project_id" class="form-control"
                                        value="{{ sv($settings, 'fcm_project_id', 'autoplatform-ci-prod') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══ LIMITES & QUOTAS ════════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-gauge" style="color:var(--danger)"></i>
                        Limites & quotas
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.group', 'quotas') }}">
                        @csrf
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                @foreach ([['quota_vehicles_free', 'Véhicules max (plan Gratuit)', 1], ['quota_vehicles_premium', 'Véhicules max (Premium)', 10], ['quota_photos_station_pro', 'Photos max par station (Pro)', 6], ['quota_photos_station_prem', 'Photos max station (Premium)', 20], ['quota_reviews_per_user', 'Avis max par utilisateur', 1], ['quota_featured_articles', 'Articles en vedette max', 10]] as [$key, $label, $default])
                                    <div>
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" name="{{ $key }}" class="form-control"
                                            value="{{ sv($settings, $key, $default) }}" min="1">
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- /grid --}}
    </main>

    <div class="toast-container"></div>
@endsection
