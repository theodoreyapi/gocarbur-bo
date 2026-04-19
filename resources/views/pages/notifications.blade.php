@extends('layouts.master', ['title' => 'Notifications', 'subTitle' => 'Notifications'])

@push('scripts')
    <script>
        function updateTarget(v) {
            document.getElementById('cityField').style.display = v === 'city' ? 'block' : 'none';
        }

        function sendNotification() {
            showToast('Notification envoyée à 1 248 utilisateurs', 'success');
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
            <button class="btn btn-primary" data-modal-open="modalSendNotif"><i class="fa-solid fa-paper-plane"></i>
                Envoyer notification</button>
        </div>
        <!-- Nouvelle notification -->
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-paper-plane" style="color:var(--primary)"></i>
                    Envoyer une notification</div>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
                    <div><label class="form-label">Cible</label><select class="form-select"
                            onchange="updateTarget(this.value)">
                            <option value="all">Tous les utilisateurs</option>
                            <option value="premium">Utilisateurs Premium</option>
                            <option value="city">Par ville</option>
                            <option value="user">Utilisateur spécifique</option>
                        </select></div>
                    <div id="cityField" style="display:none"><label class="form-label">Ville cible</label><select
                            class="form-select">
                            <option>Abidjan</option>
                            <option>Bouaké</option>
                            <option>Daloa</option>
                        </select></div>
                    <div><label class="form-label">Type</label><select class="form-select">
                            <option>Système</option>
                            <option>Alerte carburant</option>
                            <option>Promotion</option>
                            <option>Conseil</option>
                            <option>Broadcast</option>
                        </select></div>
                </div>
                <div style="margin-bottom:12px"><label class="form-label">Titre *</label><input type="text"
                        class="form-control" placeholder="Ex: Nouvelle promotion !"></div>
                <div style="margin-bottom:12px"><label class="form-label">Message *</label>
                    <textarea class="form-control" rows="3" placeholder="Contenu de la notification..."></textarea>
                </div>
                <div style="margin-bottom:16px"><label class="form-label">Lien (optionnel)</label><input type="text"
                        class="form-control" placeholder="Ex: /stations/detail/5"></div>
                <div style="display:flex;gap:10px">
                    <button class="btn btn-primary" onclick="sendNotification()"><i class="fa-solid fa-paper-plane"></i>
                        Envoyer maintenant</button>
                    <button class="btn btn-secondary"><i class="fa-solid fa-clock"></i> Planifier</button>
                </div>
            </div>
        </div>
        <!-- Historique -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-history" style="color:var(--info)"></i> Historique
                    des broadcasts</div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Cible</th>
                            <th>Envoyés</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-600">Prix carburant en baisse !</td>
                            <td>Total Cocody : Essence à 680 FCFA/L</td>
                            <td><span class="badge badge-success">Premium (347)</span></td>
                            <td>312 / 347</td>
                            <td>18 Mar 2024 14:30</td>
                        </tr>
                        <tr>
                            <td class="fw-600">Nouveau garage partenaire</td>
                            <td>Garage Auto Plus Cocody rejoint la plateforme</td>
                            <td><span class="badge badge-info">Tous (1 248)</span></td>
                            <td>1 134 / 1 248</td>
                            <td>15 Mar 2024 09:00</td>
                        </tr>
                        <tr>
                            <td class="fw-600">Rappel maintenance</td>
                            <td>N'oubliez pas de vérifier votre véhicule avant les fêtes</td>
                            <td><span class="badge badge-purple">Abidjan (856)</span></td>
                            <td>798 / 856</td>
                            <td>10 Mar 2024 08:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <div class="toast-container"></div>
@endsection
