@extends('layouts.master', ['title' => "Journaux d'activité", 'subTitle' => "Journaux d'activité"])

@push('scripts')
    <script>
        function showLogDetail(id) {
            openModal('modalLogDetail');
        }

        function exportLogs() {
            showToast('Export des journaux en cours...', 'info');
        }
    </script>
@endpush

@section('content')
    <main class="page-content">
        <div class="page-header"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Journaux d'activité</h1>
                <p>Toutes les actions effectuées par les administrateurs et le système.</p>
            </div>
            <button class="btn btn-secondary" onclick="exportLogs()"><i class="fa-solid fa-download"></i> Exporter
                les logs</button>
        </div>

        <!-- KPIs rapides -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:20px">
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Actions aujourd'hui</div>
                <div class="fw-700" style="font-size:22px">47</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Cette semaine</div>
                <div class="fw-700" style="font-size:22px">312</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Ce mois</div>
                <div class="fw-700" style="font-size:22px">1 487</div>
            </div>
            <div class="stat-card" style="padding:14px">
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Admin actifs</div>
                <div class="fw-700" style="font-size:22px">3</div>
            </div>
        </div>

        <div class="card">
            <!-- Filtres -->
            <div class="filter-bar">
                <div style="position:relative;flex:1;min-width:200px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" placeholder="Description, utilisateur, IP..."
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>
                <select class="form-select" style="width:170px">
                    <option value="">Toutes les actions</option>
                    <option value="user">Gestion utilisateurs</option>
                    <option value="station">Gestion stations</option>
                    <option value="garage">Gestion garages</option>
                    <option value="article">Gestion articles</option>
                    <option value="subscription">Abonnements</option>
                    <option value="payment">Paiements</option>
                    <option value="setting">Paramètres</option>
                    <option value="auth">Authentification</option>
                    <option value="system">Système</option>
                </select>
                <select class="form-select" style="width:150px">
                    <option>Tous les admins</option>
                    <option>Super Admin</option>
                    <option>Admin 2</option>
                    <option>Admin 3</option>
                    <option>Système (auto)</option>
                </select>
                <input type="date" class="form-control" style="width:150px" value="2024-03-18">
                <select class="form-select" style="width:130px">
                    <option>Tous niveaux</option>
                    <option>Info</option>
                    <option>Succès</option>
                    <option>Avertissement</option>
                    <option>Erreur</option>
                </select>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Horodatage</th>
                            <th>Niveau</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Admin</th>
                            <th>IP</th>
                            <th>Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">14:32:08</div>
                            </td>
                            <td><span class="badge badge-success">Succès</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">subscription</span></td>
                            <td style="font-size:13px">Abonnement Premium accordé à <strong>Kouassi Aya</strong>
                                (#1248) — 1 mois</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar" style="width:26px;height:26px;font-size:10px">SA
                                    </div><span style="font-size:12px">Super Admin</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">102.176.4.12</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(1)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">14:15:44</div>
                            </td>
                            <td><span class="badge badge-success">Succès</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">station</span></td>
                            <td style="font-size:13px">Badge vérifié attribué à <strong>Total Énergies
                                    Cocody</strong> (#12)</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar" style="width:26px;height:26px;font-size:10px">SA
                                    </div><span style="font-size:12px">Super Admin</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">102.176.4.12</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(2)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">13:50:21</div>
                            </td>
                            <td><span class="badge badge-info">Info</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">article</span></td>
                            <td style="font-size:13px">Article publié : <strong>"5 signes que votre voiture a
                                    besoin d'une vidange"</strong></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar"
                                        style="width:26px;height:26px;font-size:10px;background:linear-gradient(135deg,#3B82F6,#1D4ED8)">
                                        A2</div><span style="font-size:12px">Admin 2</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">41.207.12.88</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(3)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">12:30:05</div>
                            </td>
                            <td><span class="badge badge-warning">Attention</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">user</span></td>
                            <td style="font-size:13px">Compte suspendu : <strong>Diaby Tiémoko</strong> (#1245) —
                                Raison : Abus signalé</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar" style="width:26px;height:26px;font-size:10px">SA
                                    </div><span style="font-size:12px">Super Admin</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">102.176.4.12</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(4)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">11:00:00</div>
                            </td>
                            <td><span class="badge badge-info">Info</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">système</span></td>
                            <td style="font-size:13px">⚙️ Tâche automatique : vérification documents expirés — 3
                                alertes envoyées</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div
                                        style="width:26px;height:26px;background:#F1F5F9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px">
                                        🤖</div><span style="font-size:12px;color:var(--text-muted)">Système</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">127.0.0.1</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(5)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">09:45:18</div>
                            </td>
                            <td><span class="badge badge-danger">Erreur</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">payment</span></td>
                            <td style="font-size:13px">Webhook paiement échoué — Référence <code
                                    style="font-size:11px">SUB-BK3M7N2S</code> — MTN timeout</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div
                                        style="width:26px;height:26px;background:#F1F5F9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px">
                                        🤖</div><span style="font-size:12px;color:var(--text-muted)">Système</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">127.0.0.1</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(6)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>18 Mar 2024</div>
                                <div style="color:var(--text-muted)">09:02:33</div>
                            </td>
                            <td><span class="badge badge-success">Succès</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">auth</span></td>
                            <td style="font-size:13px">Connexion admin réussie — Super Admin</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar" style="width:26px;height:26px;font-size:10px">SA
                                    </div><span style="font-size:12px">Super Admin</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">102.176.4.12</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(7)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>17 Mar 2024</div>
                                <div style="color:var(--text-muted)">17:22:01</div>
                            </td>
                            <td><span class="badge badge-success">Succès</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">setting</span></td>
                            <td style="font-size:13px">Paramètres mis à jour : <strong>prix User Premium</strong> —
                                1 200 FCFA → 1 500 FCFA</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar" style="width:26px;height:26px;font-size:10px">SA
                                    </div><span style="font-size:12px">Super Admin</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">102.176.4.12</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(8)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;white-space:nowrap">
                                <div>17 Mar 2024</div>
                                <div style="color:var(--text-muted)">15:10:45</div>
                            </td>
                            <td><span class="badge badge-info">Info</span></td>
                            <td><span class="badge badge-gray" style="font-size:10px">garage</span></td>
                            <td style="font-size:13px">Demande partenaire approuvée : <strong>Garage Auto Plus
                                    Cocody</strong> — Plan Pro attribué</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <div class="user-avatar"
                                        style="width:26px;height:26px;font-size:10px;background:linear-gradient(135deg,#3B82F6,#1D4ED8)">
                                        A2</div><span style="font-size:12px">Admin 2</span>
                                </div>
                            </td>
                            <td style="font-size:11px;color:var(--text-muted)">41.207.12.88</td>
                            <td><button class="btn btn-sm btn-secondary" onclick="showLogDetail(9)"><i
                                        class="fa-solid fa-eye"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:10px">
                <span style="font-size:13px;color:var(--text-muted)">1 487 entrées ce mois</span>
                <div class="pagination">
                    <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
                    <div class="page-item active">1</div>
                    <div class="page-item">2</div>
                    <div class="page-item">3</div>
                    <div class="page-item">...</div>
                    <div class="page-item">75</div>
                    <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Détail log -->
    <div class="modal-overlay" id="modalLogDetail">
        <div class="modal-box" style="max-width:520px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-list-check" style="color:var(--info)"></i> Détail de l'entrée</h5>
                <button class="modal-close" data-modal-close="modalLogDetail">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:10px">
                    <div style="background:var(--bg);border-radius:var(--radius-sm);padding:14px">
                        <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;font-size:13px">
                            <span style="color:var(--text-muted)">Horodatage</span><span class="fw-600">18 Mar 2024 —
                                14:32:08</span>
                            <span style="color:var(--text-muted)">Niveau</span><span><span
                                    class="badge badge-success">Succès</span></span>
                            <span style="color:var(--text-muted)">Catégorie</span><span><span
                                    class="badge badge-gray">subscription</span></span>
                            <span style="color:var(--text-muted)">Admin</span><span class="fw-600">Super Admin (ID:
                                1)</span>
                            <span style="color:var(--text-muted)">Adresse IP</span><span
                                class="fw-600">102.176.4.12</span>
                            <span style="color:var(--text-muted)">User-Agent</span><span
                                style="font-size:11px;color:var(--text-muted)">Chrome 122 / Windows 11</span>
                        </div>
                    </div>
                    <div>
                        <div class="fw-600" style="font-size:13px;margin-bottom:6px">Description</div>
                        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;font-size:13px">
                            Abonnement Premium accordé à Kouassi Aya (#1248) — Durée : 1 mois
                        </div>
                    </div>
                    <div>
                        <div class="fw-600" style="font-size:13px;margin-bottom:6px">Données associées (JSON)</div>
                        <pre
                            style="background:#1E293B;color:#94A3B8;padding:12px;border-radius:var(--radius-sm);font-size:11px;overflow-x:auto;margin:0">{
                            "user_id": 1248,
                            "user_name": "Kouassi Aya",
                            "plan": "premium",
                            "duration_months": 1,
                            "expires_at": "2024-04-18",
                            "granted_by": 1,
                            "method": "manual"
                            }</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalLogDetail">Fermer</button>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection
