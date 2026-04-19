@extends('layouts.master', ['title' => 'Paramètres', 'subTitle' => 'Paramètres'])

@push('scripts')
    <script>
        function saveSetting(group) {
            showToast('Paramètres "' + group + '" sauvegardés avec succès', 'success');
        }
    </script>
@endpush

@section('content')
    <main class="page-content">
        <div class="page-header">
            <h1>Paramètres de la plateforme</h1>
            <p>Configuration globale d'AutoPlatform — tous les paramètres applicables à l'application mobile et à
                l'API.</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

            <!-- Tarification abonnements -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-sack-dollar" style="color:var(--warning)"></i>
                        Tarification abonnements</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                    <div
                        style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);font-size:12px;color:var(--text-muted);border-left:3px solid var(--warning)">
                        ⚠️ La modification des tarifs n'affecte pas les abonnements en cours. Elle s'applique aux
                        nouveaux abonnements uniquement.
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="form-label" style="font-size:12px">User Premium — Mensuel (FCFA)</label>
                            <input type="number" class="form-control" value="1500" style="font-size:13px">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">User Premium — Trimestriel
                                (FCFA)</label>
                            <input type="number" class="form-control" value="4000" style="font-size:13px">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">User Premium — Annuel (FCFA)</label>
                            <input type="number" class="form-control" value="14000" style="font-size:13px">
                        </div>
                    </div>
                    <div
                        style="border-top:1px solid var(--border);padding-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="form-label" style="font-size:12px">Station Pro — Mensuel (FCFA)</label>
                            <input type="number" class="form-control" value="12500" style="font-size:13px">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">Station Premium — Mensuel
                                (FCFA)</label>
                            <input type="number" class="form-control" value="32500" style="font-size:13px">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">Garage Pro — Mensuel (FCFA)</label>
                            <input type="number" class="form-control" value="12500" style="font-size:13px">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">Garage Premium — Mensuel
                                (FCFA)</label>
                            <input type="number" class="form-control" value="32500" style="font-size:13px">
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('tarification')"><i class="fa-solid fa-check"></i>
                        Sauvegarder les tarifs</button>
                </div>
            </div>

            <!-- Paramètres généraux -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-sliders" style="color:var(--info)"></i>
                        Paramètres généraux</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                    <div><label class="form-label">Nom de l'application</label><input type="text" class="form-control"
                            value="AutoPlatform"></div>
                    <div><label class="form-label">Slogan</label><input type="text" class="form-control"
                            value="Votre compagnon automobile en Côte d'Ivoire"></div>
                    <div><label class="form-label">Email de support</label><input type="email" class="form-control"
                            value="support@autoplatform.ci"></div>
                    <div><label class="form-label">WhatsApp support</label><input type="text" class="form-control"
                            value="+225 07 00 00 00"></div>
                    <div><label class="form-label">Rayon de recherche par défaut (km)</label><input type="number"
                            class="form-control" value="10" min="1" max="50"></div>
                    <div
                        style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <div class="fw-600" style="font-size:13px">Mode maintenance</div>
                            <div style="font-size:12px;color:var(--text-muted)">L'app affiche un message de
                                maintenance aux utilisateurs</div>
                        </div>
                        <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
                    </div>
                    <div
                        style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <div class="fw-600" style="font-size:13px">Inscription ouverte</div>
                            <div style="font-size:12px;color:var(--text-muted)">Autoriser les nouvelles
                                inscriptions</div>
                        </div>
                        <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('general')"><i class="fa-solid fa-check"></i>
                        Sauvegarder</button>
                </div>
            </div>

            <!-- Notifications automatiques -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-bell" style="color:var(--primary)"></i>
                        Notifications automatiques</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Rappel expiration documents</div>
                                <div style="font-size:12px;color:var(--text-muted)">Notifier J-30 et J-7 avant
                                    expiration</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Alerte baisse prix carburant</div>
                                <div style="font-size:12px;color:var(--text-muted)">Notifier les users Premium des
                                    stations proches</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Rappel expiration abonnement</div>
                                <div style="font-size:12px;color:var(--text-muted)">J-7 avant l'expiration du plan
                                </div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Notification nouvelles promotions</div>
                                <div style="font-size:12px;color:var(--text-muted)">Envoyer un push quand une promo
                                    est créée</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Bienvenue nouveaux inscrits</div>
                                <div style="font-size:12px;color:var(--text-muted)">Push automatique à
                                    l'inscription</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                    </div>
                    <div style="border-top:1px solid var(--border);padding-top:12px">
                        <label class="form-label">Rappel document — jours avant expiration</label>
                        <input type="number" class="form-control" value="30" min="1" max="90">
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('notifications')"><i
                            class="fa-solid fa-check"></i> Sauvegarder</button>
                </div>
            </div>

            <!-- Sécurité & OTP -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-shield-halved" style="color:var(--success)"></i>
                        Sécurité & Authentification</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="form-label">Durée validité OTP (minutes)</label>
                            <input type="number" class="form-control" value="5" min="1" max="15">
                        </div>
                        <div>
                            <label class="form-label">Max tentatives OTP / heure</label>
                            <input type="number" class="form-control" value="3" min="1" max="10">
                        </div>
                        <div>
                            <label class="form-label">Durée token auth (jours)</label>
                            <input type="number" class="form-control" value="30" min="1" max="365">
                        </div>
                        <div>
                            <label class="form-label">Longueur code OTP</label>
                            <select class="form-select">
                                <option value="6" selected>6 chiffres</option>
                                <option value="4">4 chiffres</option>
                            </select>
                        </div>
                    </div>
                    <div
                        style="display:flex;flex-direction:column;gap:10px;border-top:1px solid var(--border);padding-top:12px">
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Modération avis obligatoire</div>
                                <div style="font-size:12px;color:var(--text-muted)">Les avis passent en attente
                                    avant publication</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Journal d'activité admin activé</div>
                                <div style="font-size:12px;color:var(--text-muted)">Enregistrer toutes les actions
                                    des administrateurs</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                        <div
                            style="padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                            <div>
                                <div class="fw-600" style="font-size:13px">Blocage IP après 10 tentatives</div>
                                <div style="font-size:12px;color:var(--text-muted)">Sécurité anti-brute-force</div>
                            </div>
                            <label class="toggle"><input type="checkbox" checked><span
                                    class="toggle-slider"></span></label>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('securite')"><i class="fa-solid fa-check"></i>
                        Sauvegarder</button>
                </div>
            </div>

            <!-- Intégrations paiement -->
            <div class="card" style="grid-column:span 2">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-credit-card" style="color:var(--purple)"></i>
                        Intégrations paiement Mobile Money</div>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
                        <!-- Orange Money -->
                        <div
                            style="padding:16px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--bg)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                                <div
                                    style="width:36px;height:36px;background:#FF6600;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px">
                                    OM</div>
                                <div>
                                    <div class="fw-700" style="font-size:14px">Orange Money</div>
                                    <div style="font-size:11px;color:var(--text-muted)">CinetPay Gateway</div>
                                </div>
                                <label class="toggle" style="margin-left:auto"><input type="checkbox" checked><span
                                        class="toggle-slider"></span></label>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div><label class="form-label" style="font-size:11px">Site ID</label><input
                                        type="text" class="form-control" value="5869931"
                                        style="font-size:12px;padding:6px 10px"></div>
                                <div><label class="form-label" style="font-size:11px">Clé secrète</label><input
                                        type="password" class="form-control" value="••••••••••••••••"
                                        style="font-size:12px;padding:6px 10px"></div>
                            </div>
                        </div>
                        <!-- Wave -->
                        <div
                            style="padding:16px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--bg)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                                <div
                                    style="width:36px;height:36px;background:#1CB5E0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px">
                                    WV</div>
                                <div>
                                    <div class="fw-700" style="font-size:14px">Wave</div>
                                    <div style="font-size:11px;color:var(--text-muted)">Wave Checkout API</div>
                                </div>
                                <label class="toggle" style="margin-left:auto"><input type="checkbox" checked><span
                                        class="toggle-slider"></span></label>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div><label class="form-label" style="font-size:11px">API Key</label><input
                                        type="password" class="form-control" value="••••••••••••••••"
                                        style="font-size:12px;padding:6px 10px"></div>
                                <div><label class="form-label" style="font-size:11px">Webhook Secret</label><input
                                        type="password" class="form-control" value="••••••••••••••••"
                                        style="font-size:12px;padding:6px 10px"></div>
                            </div>
                        </div>
                        <!-- MTN MoMo -->
                        <div
                            style="padding:16px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--bg)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                                <div
                                    style="width:36px;height:36px;background:#FFCC00;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#333;font-weight:700;font-size:11px">
                                    MTN</div>
                                <div>
                                    <div class="fw-700" style="font-size:14px">MTN MoMo</div>
                                    <div style="font-size:11px;color:var(--text-muted)">MTN Mobile Money API</div>
                                </div>
                                <label class="toggle" style="margin-left:auto"><input type="checkbox" checked><span
                                        class="toggle-slider"></span></label>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div><label class="form-label" style="font-size:11px">API Key</label><input
                                        type="password" class="form-control" value="••••••••••••••••"
                                        style="font-size:12px;padding:6px 10px"></div>
                                <div><label class="form-label" style="font-size:11px">Subscription
                                        Key</label><input type="password" class="form-control" value="••••••••••••••••"
                                        style="font-size:12px;padding:6px 10px"></div>
                            </div>
                        </div>
                        <!-- Moov Money -->
                        <div
                            style="padding:16px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--bg)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                                <div
                                    style="width:36px;height:36px;background:#00A651;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px">
                                    MV</div>
                                <div>
                                    <div class="fw-700" style="font-size:14px">Moov Money</div>
                                    <div style="font-size:11px;color:var(--text-muted)">Flooz API</div>
                                </div>
                                <label class="toggle" style="margin-left:auto"><input type="checkbox"><span
                                        class="toggle-slider"></span></label>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div><label class="form-label" style="font-size:11px">Token</label><input type="password"
                                        class="form-control" value="" placeholder="Non configuré"
                                        style="font-size:12px;padding:6px 10px"></div>
                                <div><label class="form-label" style="font-size:11px">Secret</label><input
                                        type="password" class="form-control" value="" placeholder="Non configuré"
                                        style="font-size:12px;padding:6px 10px"></div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:16px">
                        <button class="btn btn-primary" onclick="saveSetting('paiements')"><i
                                class="fa-solid fa-check"></i> Sauvegarder les intégrations</button>
                    </div>
                </div>
            </div>

            <!-- SMS & Firebase -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-message" style="color:var(--info)"></i> SMS &
                        Push (Firebase)</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                    <div>
                        <label class="form-label">Fournisseur SMS OTP</label>
                        <select class="form-select">
                            <option>Infobip</option>
                            <option>Twilio</option>
                            <option>Orange SMS API</option>
                        </select>
                    </div>
                    <div><label class="form-label">Infobip API Key</label><input type="password" class="form-control"
                            value="••••••••••••••••"></div>
                    <div><label class="form-label">Expéditeur SMS (SenderID)</label><input type="text"
                            class="form-control" value="AutoPlat"></div>
                    <div style="border-top:1px solid var(--border);padding-top:14px">
                        <label class="form-label">Firebase Server Key (FCM)</label>
                        <input type="password" class="form-control" value="••••••••••••••••" style="margin-bottom:10px">
                        <label class="form-label">Firebase Project ID</label>
                        <input type="text" class="form-control" value="autoplatform-ci-prod">
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('sms_firebase')"><i
                            class="fa-solid fa-check"></i> Sauvegarder</button>
                </div>
            </div>

            <!-- Limites & quotas -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-gauge" style="color:var(--danger)"></i> Limites
                        & quotas</div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="form-label">Véhicules max (plan Gratuit)</label>
                            <input type="number" class="form-control" value="1" min="1">
                        </div>
                        <div>
                            <label class="form-label">Véhicules max (Premium)</label>
                            <input type="number" class="form-control" value="10" min="1">
                        </div>
                        <div>
                            <label class="form-label">Photos max par station (Pro)</label>
                            <input type="number" class="form-control" value="6" min="1">
                        </div>
                        <div>
                            <label class="form-label">Photos max station (Premium)</label>
                            <input type="number" class="form-control" value="20" min="1">
                        </div>
                        <div>
                            <label class="form-label">Avis max par utilisateur</label>
                            <input type="number" class="form-control" value="1" min="1">
                        </div>
                        <div>
                            <label class="form-label">Articles en vedette max</label>
                            <input type="number" class="form-control" value="10" min="1">
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="saveSetting('limites')"><i class="fa-solid fa-check"></i>
                        Sauvegarder</button>
                </div>
            </div>

        </div>
    </main>

    <div class="toast-container"></div>
@endsection
