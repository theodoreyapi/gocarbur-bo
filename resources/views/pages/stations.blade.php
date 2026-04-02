<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stations — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item active" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations-service</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages & Services</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge" style="background:#F59E0B">8</span></a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Stations-service</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
      <div><h1>Stations-service</h1><p>Gérez toutes les stations partenaires de la plateforme.</p></div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-secondary"><i class="fa-solid fa-download"></i> Export</button>
        <button class="btn btn-primary" data-modal-open="modalAddStation"><i class="fa-solid fa-plus"></i> Ajouter station</button>
      </div>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FFF0EB;margin:0;width:38px;height:38px"><i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:16px"></i></div><div><div class="stat-value" style="font-size:20px">84</div><div class="stat-label">Total stations</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#D1FAE5;margin:0;width:38px;height:38px"><i class="fa-solid fa-shield-check" style="color:var(--success);font-size:16px"></i></div><div><div class="stat-value" style="font-size:20px">61</div><div class="stat-label">Vérifiées</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#DBEAFE;margin:0;width:38px;height:38px"><i class="fa-solid fa-crown" style="color:var(--info);font-size:16px"></i></div><div><div class="stat-value" style="font-size:20px">38</div><div class="stat-label">Plan Pro/Premium</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEE2E2;margin:0;width:38px;height:38px"><i class="fa-solid fa-ban" style="color:var(--danger);font-size:16px"></i></div><div><div class="stat-value" style="font-size:20px">6</div><div class="stat-label">Désactivées</div></div></div></div>
    </div>

    <div class="card">
      <div class="filter-bar">
        <div style="position:relative;flex:1;min-width:200px"><i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i><input type="text" placeholder="Nom, ville, marque..." style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)"></div>
        <select class="form-select" style="width:160px"><option value="">Tous les plans</option><option>Gratuit</option><option>Pro</option><option>Premium</option></select>
        <select class="form-select" style="width:140px"><option value="">Toutes les villes</option><option>Abidjan</option><option>Bouaké</option><option>Daloa</option></select>
        <select class="form-select" style="width:140px"><option value="">Statut</option><option>Actif</option><option>Désactivé</option></select>
        <button class="btn btn-secondary" onclick="filterVerified()"><i class="fa-solid fa-shield-check"></i> Vérifiées seulement</button>
      </div>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th><input type="checkbox"></th>
              <th>Station</th>
              <th>Marque</th>
              <th>Ville</th>
              <th>Prix Essence</th>
              <th>Prix Gasoil</th>
              <th>Plan</th>
              <th>Statut</th>
              <th>Vues</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div style="width:36px;height:36px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:14px"></i></div><div><div class="fw-600">Total Énergies Cocody</div><div style="font-size:11px;color:var(--text-muted)">Bvd Latrille, Cocody</div></div></div></td>
              <td>Total</td><td>Abidjan</td>
              <td><span class="fw-600" style="color:#3B82F6">695 FCFA/L</span></td>
              <td><span class="fw-600" style="color:#10B981">615 FCFA/L</span></td>
              <td><span class="badge badge-purple"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span> <i class="fa-solid fa-shield-check" style="color:var(--success);margin-left:4px" title="Vérifiée"></i></td>
              <td>4 289</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#" onclick="viewStation(1)"><i class="fa-solid fa-eye"></i> Voir détail</a><a class="dropdown-item" href="#" onclick="editStation(1)"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#" onclick="updatePrices(1)"><i class="fa-solid fa-gas-pump"></i> Modifier prix</a><a class="dropdown-item" href="#" onclick="unverifyStation(1)"><i class="fa-solid fa-shield-xmark"></i> Retirer badge</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#" onclick="deleteStation(1)"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div style="width:36px;height:36px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:14px"></i></div><div><div class="fw-600">Shell Plateau</div><div style="font-size:11px;color:var(--text-muted)">Rue du Commerce, Plateau</div></div></div></td>
              <td>Shell</td><td>Abidjan</td>
              <td><span class="fw-600" style="color:#3B82F6">690 FCFA/L</span></td>
              <td><span class="fw-600" style="color:#10B981">610 FCFA/L</span></td>
              <td><span class="badge badge-info"><i class="fa-solid fa-crown" style="font-size:9px"></i> Pro</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td>2 156</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Voir détail</a><a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#"><i class="fa-solid fa-gas-pump"></i> Modifier prix</a><a class="dropdown-item" href="#"><i class="fa-solid fa-shield-check"></i> Vérifier</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div style="width:36px;height:36px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:14px"></i></div><div><div class="fw-600">Petro Ivoire Yopougon</div><div style="font-size:11px;color:var(--text-muted)">Rue des Jardins, Yopougon</div></div></div></td>
              <td>Petro Ivoire</td><td>Abidjan</td>
              <td><span class="fw-600" style="color:#3B82F6">680 FCFA/L</span></td>
              <td><span class="fw-600" style="color:#10B981">605 FCFA/L</span></td>
              <td><span class="badge badge-gray">Gratuit</span></td>
              <td><span class="badge badge-warning"><i class="fa-solid fa-circle" style="font-size:7px"></i> Désactivé</span></td>
              <td>987</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Voir détail</a><a class="dropdown-item" href="#"><i class="fa-solid fa-rotate-right"></i> Activer</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Affichage 1–20 sur 84 stations</span>
        <div class="pagination"><div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div><div class="page-item active">1</div><div class="page-item">2</div><div class="page-item">3</div><div class="page-item">4</div><div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div></div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Ajouter Station -->
<div class="modal-overlay" id="modalAddStation">
  <div class="modal-box" style="max-width:600px">
    <div class="modal-header"><h5><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Ajouter une station</h5><button class="modal-close" data-modal-close="modalAddStation">✕</button></div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div style="grid-column:span 2"><label class="form-label">Nom de la station *</label><input type="text" class="form-control" placeholder="Ex: Total Énergies Cocody"></div>
        <div><label class="form-label">Marque</label><select class="form-select"><option>Total</option><option>Shell</option><option>Petro Ivoire</option><option>Oryx</option><option>Autre</option></select></div>
        <div><label class="form-label">Ville *</label><select class="form-select"><option>Abidjan</option><option>Bouaké</option><option>Daloa</option></select></div>
        <div style="grid-column:span 2"><label class="form-label">Adresse complète *</label><input type="text" class="form-control" placeholder="Ex: Bvd Latrille, Cocody"></div>
        <div><label class="form-label">Latitude</label><input type="number" class="form-control" placeholder="5.3544" step="0.0001"></div>
        <div><label class="form-label">Longitude</label><input type="number" class="form-control" placeholder="-4.0082" step="0.0001"></div>
        <div><label class="form-label">Téléphone</label><input type="text" class="form-control" placeholder="+225 ..."></div>
        <div><label class="form-label">WhatsApp</label><input type="text" class="form-control" placeholder="+225 ..."></div>
        <div><label class="form-label">Ouverture</label><input type="time" class="form-control" value="06:00"></div>
        <div><label class="form-label">Fermeture</label><input type="time" class="form-control" value="22:00"></div>
        <div><label class="form-label">Plan</label><select class="form-select"><option value="free">Gratuit</option><option value="pro">Pro</option><option value="premium">Premium</option></select></div>
        <div style="display:flex;align-items:center;gap:10px;padding-top:20px"><label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label><span class="form-label" style="margin:0">Ouverte 24h/24</span></div>
      </div>
      <div style="margin-top:16px"><label class="form-label">Prix carburant</label><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px"><div><label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Essence (FCFA/L)</label><input type="number" class="form-control" placeholder="695"></div><div><label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Gasoil (FCFA/L)</label><input type="number" class="form-control" placeholder="615"></div><div><label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:4px">Sans plomb (FCFA/L)</label><input type="number" class="form-control" placeholder="720"></div></div></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-modal-close="modalAddStation">Annuler</button><button class="btn btn-primary" onclick="saveStation()"><i class="fa-solid fa-check"></i> Enregistrer</button></div>
  </div>
</div>

<!-- Modal Modifier prix -->
<div class="modal-overlay" id="modalUpdatePrices">
  <div class="modal-box" style="max-width:440px">
    <div class="modal-header"><h5><i class="fa-solid fa-gas-pump" style="color:var(--info)"></i> Mettre à jour les prix</h5><button class="modal-close" data-modal-close="modalUpdatePrices">✕</button></div>
    <div class="modal-body">
      <div class="alert alert-info" style="margin-bottom:16px"><i class="fa-solid fa-circle-info"></i><span>Total Énergies Cocody — Dernière mise à jour il y a 2 jours</span></div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;align-items:center;gap:12px"><div style="width:12px;height:12px;background:#3B82F6;border-radius:50%;flex-shrink:0"></div><span style="width:120px;font-weight:600;font-size:14px">Essence</span><input type="number" class="form-control" value="695" style="width:120px"><span style="font-size:13px;color:var(--text-muted)">FCFA/L</span><label class="toggle" style="margin-left:auto"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
        <div style="display:flex;align-items:center;gap:12px"><div style="width:12px;height:12px;background:#10B981;border-radius:50%;flex-shrink:0"></div><span style="width:120px;font-weight:600;font-size:14px">Gasoil</span><input type="number" class="form-control" value="615" style="width:120px"><span style="font-size:13px;color:var(--text-muted)">FCFA/L</span><label class="toggle" style="margin-left:auto"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
        <div style="display:flex;align-items:center;gap:12px"><div style="width:12px;height:12px;background:#F59E0B;border-radius:50%;flex-shrink:0"></div><span style="width:120px;font-weight:600;font-size:14px">Sans plomb</span><input type="number" class="form-control" value="720" style="width:120px"><span style="font-size:13px;color:var(--text-muted)">FCFA/L</span><label class="toggle" style="margin-left:auto"><input type="checkbox"><span class="toggle-slider"></span></label></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-modal-close="modalUpdatePrices">Annuler</button><button class="btn btn-primary" onclick="savePrices()"><i class="fa-solid fa-check"></i> Sauvegarder</button></div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function viewStation(id) { showToast('Ouverture détail station #'+id, 'info'); }
function editStation(id) { showToast('Ouverture modification station #'+id, 'info'); }
function updatePrices(id) { openModal('modalUpdatePrices'); }
function unverifyStation(id) { confirmAction('Retirer le badge vérifié ?', () => showToast('Badge retiré', 'warning')); }
function deleteStation(id) { confirmAction('Supprimer cette station ?', () => showToast('Station supprimée', 'error')); }
function filterVerified() { showToast('Filtre : vérifiées seulement', 'info'); }
function saveStation() { showToast('Station créée avec succès', 'success'); closeModal('modalAddStation'); }
function savePrices() { showToast('Prix mis à jour avec succès', 'success'); closeModal('modalUpdatePrices'); }
</script>
</body>
</html>
