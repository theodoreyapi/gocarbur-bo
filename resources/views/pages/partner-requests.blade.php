<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Demandes partenaires — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations-service</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages & Services</a></li><li><a class="nav-link-item active" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge" style="background:#F59E0B">8</span></a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Demandes partenaires</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header">
      <h1>Demandes partenaires</h1>
      <p>Gérez les inscriptions de stations et garages depuis le site web.</p>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-clock" style="color:var(--warning)"></i></div><div><div class="stat-value" style="font-size:22px">8</div><div class="stat-label">En attente</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#DBEAFE;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-phone" style="color:var(--info)"></i></div><div><div class="stat-value" style="font-size:22px">3</div><div class="stat-label">Contactées</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-check" style="color:var(--success)"></i></div><div><div class="stat-value" style="font-size:22px">47</div><div class="stat-label">Approuvées</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEE2E2;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-times" style="color:var(--danger)"></i></div><div><div class="stat-value" style="font-size:22px">12</div><div class="stat-label">Rejetées</div></div></div></div>
    </div>

    <!-- Alerte en attente -->
    <div class="alert alert-warning" style="margin-bottom:20px">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span><strong>8 demandes</strong> attendent votre traitement. Répondez dans les 48h pour maintenir la qualité de service.</span>
    </div>

    <!-- Onglets statut -->
    <div class="card">
      <div class="card-header" style="padding:0 20px" data-tabs>
        <div class="tab-nav">
          <button class="tab-btn active" data-tab="tab-pending">En attente <span class="nav-badge" style="background:var(--warning);margin-left:6px">8</span></button>
          <button class="tab-btn" data-tab="tab-contacted">Contactées</button>
          <button class="tab-btn" data-tab="tab-approved">Approuvées</button>
          <button class="tab-btn" data-tab="tab-rejected">Rejetées</button>
        </div>
      </div>

      <!-- EN ATTENTE -->
      <div id="tab-pending" class="tab-content active">
        <div style="display:flex;flex-direction:column">

          <!-- Demande 1 -->
          <div style="padding:20px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">
              <div style="width:44px;height:44px;background:#FFF0EB;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:18px"></i>
              </div>
              <div style="flex:1;min-width:200px">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:15px">Total Marcory</span>
                  <span class="badge badge-primary"><i class="fa-solid fa-gas-pump" style="font-size:9px"></i> Station</span>
                  <span class="badge badge-warning"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:12px;color:var(--text-muted);margin-left:auto">Reçu il y a 2h</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-bottom:10px">
                  <div style="font-size:13px"><i class="fa-solid fa-user" style="color:var(--text-muted);width:16px"></i> <span class="fw-600">Konan Adjoa</span> (Responsable)</div>
                  <div style="font-size:13px"><i class="fa-solid fa-phone" style="color:var(--text-muted);width:16px"></i> +225 07 45 67 89</div>
                  <div style="font-size:13px"><i class="fa-solid fa-envelope" style="color:var(--text-muted);width:16px"></i> konan@totalci.com</div>
                  <div style="font-size:13px"><i class="fa-solid fa-location-dot" style="color:var(--text-muted);width:16px"></i> Av. Houphouët, Marcory, Abidjan</div>
                </div>
                <div style="background:var(--bg);padding:10px 14px;border-radius:var(--radius-sm);font-size:13px;color:var(--text-muted);margin-bottom:12px">
                  <i class="fa-solid fa-comment"></i> <em>"Nous souhaitons référencer notre station sur votre plateforme. Nous avons 3 pompes et proposons lavage auto et boutique."</em>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                  <button class="btn btn-success btn-sm" onclick="approveRequest(1)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-secondary btn-sm" onclick="contactRequest(1)"><i class="fa-solid fa-phone"></i> Marquer contacté</button>
                  <button class="btn btn-secondary btn-sm" onclick="viewOnMap(1)"><i class="fa-solid fa-map-location-dot"></i> Voir sur carte</button>
                  <button class="btn btn-danger btn-sm" onclick="rejectRequest(1)"><i class="fa-solid fa-times"></i> Rejeter</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Demande 2 -->
          <div style="padding:20px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">
              <div style="width:44px;height:44px;background:#EDE9FE;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:18px"></i>
              </div>
              <div style="flex:1;min-width:200px">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:15px">Garage Mécanique Pro</span>
                  <span class="badge badge-purple"><i class="fa-solid fa-wrench" style="font-size:9px"></i> Garage</span>
                  <span class="badge badge-warning"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:12px;color:var(--text-muted);margin-left:auto">Reçu il y a 5h</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-bottom:10px">
                  <div style="font-size:13px"><i class="fa-solid fa-user" style="color:var(--text-muted);width:16px"></i> <span class="fw-600">Diallo Moussa</span></div>
                  <div style="font-size:13px"><i class="fa-solid fa-phone" style="color:var(--text-muted);width:16px"></i> +225 05 12 34 56</div>
                  <div style="font-size:13px"><i class="fa-solid fa-envelope" style="color:var(--text-muted);width:16px"></i> diallo.garage@gmail.com</div>
                  <div style="font-size:13px"><i class="fa-solid fa-location-dot" style="color:var(--text-muted);width:16px"></i> Zone 4, Abidjan</div>
                </div>
                <div style="background:var(--bg);padding:10px 14px;border-radius:var(--radius-sm);font-size:13px;color:var(--text-muted);margin-bottom:12px">
                  <i class="fa-solid fa-comment"></i> <em>"Garage spécialisé en vidange, freins et climatisation. 10 ans d'expérience. RCCM CI-ABJ-2019-B-12345."</em>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                  <button class="btn btn-success btn-sm" onclick="approveRequest(2)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-secondary btn-sm" onclick="contactRequest(2)"><i class="fa-solid fa-phone"></i> Marquer contacté</button>
                  <button class="btn btn-danger btn-sm" onclick="rejectRequest(2)"><i class="fa-solid fa-times"></i> Rejeter</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Demande 3 -->
          <div style="padding:20px">
            <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">
              <div style="width:44px;height:44px;background:#FFF0EB;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:18px"></i>
              </div>
              <div style="flex:1;min-width:200px">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:15px">Petro Plus Bouaké</span>
                  <span class="badge badge-primary"><i class="fa-solid fa-gas-pump" style="font-size:9px"></i> Station</span>
                  <span class="badge badge-warning"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:12px;color:var(--text-muted);margin-left:auto">Reçu il y a 1j</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-bottom:10px">
                  <div style="font-size:13px"><i class="fa-solid fa-user" style="color:var(--text-muted);width:16px"></i> <span class="fw-600">Yao Eric</span></div>
                  <div style="font-size:13px"><i class="fa-solid fa-phone" style="color:var(--text-muted);width:16px"></i> +225 07 98 76 54</div>
                  <div style="font-size:13px"><i class="fa-solid fa-location-dot" style="color:var(--text-muted);width:16px"></i> Centre-ville, Bouaké</div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                  <button class="btn btn-success btn-sm" onclick="approveRequest(3)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-secondary btn-sm" onclick="contactRequest(3)"><i class="fa-solid fa-phone"></i> Marquer contacté</button>
                  <button class="btn btn-danger btn-sm" onclick="rejectRequest(3)"><i class="fa-solid fa-times"></i> Rejeter</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- CONTACTÉES -->
      <div id="tab-contacted" class="tab-content">
        <div style="padding:20px;border-bottom:1px solid var(--border)">
          <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <div style="width:44px;height:44px;background:#DBEAFE;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid fa-gas-pump" style="color:var(--info);font-size:18px"></i></div>
            <div style="flex:1">
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
                <span class="fw-700">Shell Koumassi</span>
                <span class="badge badge-info">Station</span>
                <span class="badge badge-info"><i class="fa-solid fa-phone" style="font-size:9px"></i> Contacté</span>
              </div>
              <div style="font-size:13px;color:var(--text-muted)">Koffi Bernard • +225 01 23 45 67 • Koumassi, Abidjan</div>
              <div style="font-size:12px;color:var(--text-muted);margin-top:3px">Contact le 16 Mar 2024 par Super Admin</div>
            </div>
            <div style="display:flex;gap:8px">
              <button class="btn btn-success btn-sm" onclick="approveRequest(4)"><i class="fa-solid fa-check"></i> Approuver</button>
              <button class="btn btn-danger btn-sm" onclick="rejectRequest(4)"><i class="fa-solid fa-times"></i> Rejeter</button>
            </div>
          </div>
        </div>
      </div>

      <!-- APPROUVÉES -->
      <div id="tab-approved" class="tab-content">
        <div class="table-wrapper">
          <table class="table">
            <thead><tr><th>Établissement</th><th>Type</th><th>Contact</th><th>Ville</th><th>Plan attribué</th><th>Approuvé le</th></tr></thead>
            <tbody>
              <tr><td class="fw-600">Total Énergies Cocody</td><td><span class="badge badge-primary">Station</span></td><td>+225 07 00 11 22</td><td>Abidjan</td><td><span class="badge badge-purple">Premium</span></td><td>10 Mar 2024</td></tr>
              <tr><td class="fw-600">Garage Auto Plus</td><td><span class="badge badge-purple">Garage</span></td><td>+225 05 33 44 55</td><td>Abidjan</td><td><span class="badge badge-info">Pro</span></td><td>08 Mar 2024</td></tr>
              <tr><td class="fw-600">Petro Ivoire Yopougon</td><td><span class="badge badge-primary">Station</span></td><td>+225 01 66 77 88</td><td>Abidjan</td><td><span class="badge badge-gray">Gratuit</span></td><td>05 Mar 2024</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- REJETÉES -->
      <div id="tab-rejected" class="tab-content">
        <div class="table-wrapper">
          <table class="table">
            <thead><tr><th>Établissement</th><th>Type</th><th>Contact</th><th>Raison</th><th>Rejeté le</th></tr></thead>
            <tbody>
              <tr>
                <td class="fw-600">Station X</td>
                <td><span class="badge badge-primary">Station</span></td>
                <td>+225 07 11 22 33</td>
                <td style="color:var(--danger);font-size:12px">Informations incomplètes</td>
                <td>12 Mar 2024</td>
              </tr>
              <tr>
                <td class="fw-600">Garage Y</td>
                <td><span class="badge badge-purple">Garage</span></td>
                <td>+225 05 44 55 66</td>
                <td style="color:var(--danger);font-size:12px">Zone non couverte</td>
                <td>07 Mar 2024</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- Modal Approuver -->
<div class="modal-overlay" id="modalApprove">
  <div class="modal-box" style="max-width:480px">
    <div class="modal-header"><h5><i class="fa-solid fa-check" style="color:var(--success)"></i> Approuver la demande</h5><button class="modal-close" data-modal-close="modalApprove">✕</button></div>
    <div class="modal-body">
      <div class="alert alert-success" style="margin-bottom:16px"><i class="fa-solid fa-info-circle"></i> Cette action créera le compte professionnel et activera l'établissement sur la plateforme.</div>
      <div style="margin-bottom:14px">
        <label class="form-label">Plan à attribuer</label>
        <select class="form-select">
          <option value="free">Gratuit (accès de base)</option>
          <option value="pro">Pro — 12 500 FCFA/mois</option>
          <option value="premium">Premium — 32 500 FCFA/mois</option>
        </select>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Email de connexion (compte pro)</label>
        <input type="email" class="form-control" placeholder="contact@etablissement.ci">
      </div>
      <div>
        <label class="form-label">Note interne (optionnelle)</label>
        <textarea class="form-control" rows="2" placeholder="Notes visibles uniquement par les admins..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalApprove">Annuler</button>
      <button class="btn btn-success" onclick="confirmApprove()"><i class="fa-solid fa-check"></i> Confirmer l'approbation</button>
    </div>
  </div>
</div>

<!-- Modal Rejeter -->
<div class="modal-overlay" id="modalReject">
  <div class="modal-box" style="max-width:440px">
    <div class="modal-header"><h5><i class="fa-solid fa-times" style="color:var(--danger)"></i> Rejeter la demande</h5><button class="modal-close" data-modal-close="modalReject">✕</button></div>
    <div class="modal-body">
      <div style="margin-bottom:14px">
        <label class="form-label">Raison du rejet *</label>
        <select class="form-select" style="margin-bottom:10px">
          <option value="">Sélectionner une raison</option>
          <option>Informations incomplètes</option>
          <option>Zone géographique non couverte</option>
          <option>Établissement non conforme</option>
          <option>Doublon détecté</option>
          <option>Autre</option>
        </select>
        <textarea class="form-control" rows="3" placeholder="Message à envoyer au demandeur (optionnel)..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalReject">Annuler</button>
      <button class="btn btn-danger" onclick="confirmReject()"><i class="fa-solid fa-times"></i> Rejeter la demande</button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function approveRequest(id) { openModal('modalApprove'); }
function rejectRequest(id) { openModal('modalReject'); }
function contactRequest(id) { showToast('Demande marquée comme contactée','info'); }
function viewOnMap(id) { showToast('Ouverture de la localisation sur carte','info'); }
function confirmApprove() { showToast('Demande approuvée ! Compte pro créé.','success'); closeModal('modalApprove'); }
function confirmReject() { showToast('Demande rejetée','warning'); closeModal('modalReject'); }
</script>
</body>
</html>
