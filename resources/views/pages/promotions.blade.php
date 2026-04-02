<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Promotions — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes <span class="nav-badge" style="background:#F59E0B">8</span></a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item active" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Promotions</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher une promotion..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div><h1>Promotions</h1><p>Gérez les offres spéciales des stations et garages partenaires.</p></div>
      <button class="btn btn-primary" onclick="openModal('modalAddPromo')"><i class="fa-solid fa-plus"></i> Créer une promotion</button>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-tag" style="color:var(--success)"></i></div><div><div class="stat-value" style="font-size:22px">23</div><div class="stat-label">Actives</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-clock" style="color:var(--warning)"></i></div><div><div class="stat-value" style="font-size:22px">8</div><div class="stat-label">À venir</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#F1F5F9;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-clock-rotate-left" style="color:var(--text-muted)"></i></div><div><div class="stat-value" style="font-size:22px">41</div><div class="stat-label">Expirées</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FFF0EB;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i></div><div><div class="stat-value" style="font-size:22px">15</div><div class="stat-label">Stations</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#EDE9FE;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-wrench" style="color:#8B5CF6"></i></div><div><div class="stat-value" style="font-size:22px">8</div><div class="stat-label">Garages</div></div></div></div>
    </div>

    <!-- Filtres et table -->
    <div class="card">
      <div class="filter-bar">
        <div style="position:relative;flex:1;min-width:200px">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
          <input type="text" placeholder="Titre, établissement..." style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
        </div>
        <select class="form-select" style="width:140px"><option>Tous statuts</option><option>Active</option><option>À venir</option><option>Expirée</option><option>Désactivée</option></select>
        <select class="form-select" style="width:150px"><option>Tous types</option><option>Réduction %</option><option>Montant fixe</option><option>Service gratuit</option><option>Offre spéciale</option></select>
        <select class="form-select" style="width:150px"><option>Tout type d'établ.</option><option>Stations seulement</option><option>Garages seulement</option></select>
      </div>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Promotion</th>
              <th>Établissement</th>
              <th>Type</th>
              <th>Remise</th>
              <th>Période</th>
              <th>Statut</th>
              <th>Push envoyé</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="fw-600" style="font-size:13px">Lavage offert pour tout plein 20L+</div>
                <div style="font-size:11px;color:var(--text-muted)">Service gratuit</div>
              </td>
              <td>
                <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600">
                  <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:10px"></i> Total Énergies Cocody
                </span>
              </td>
              <td><span class="badge badge-info">Service gratuit</span></td>
              <td><span class="fw-700" style="color:var(--success)">100% lavage</span></td>
              <td>
                <div style="font-size:12px">01 Mar → 30 Avr</div>
                <div style="font-size:11px;color:var(--text-muted)">30 jours restants</div>
              </td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-paper-plane" style="font-size:9px"></i> Oui</span></td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="editPromo(1)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#" onclick="togglePromo(1)"><i class="fa-solid fa-pause"></i> Désactiver</a>
                    <a class="dropdown-item" href="#" onclick="sendPushPromo(1)"><i class="fa-solid fa-paper-plane"></i> Renvoyer push</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deletePromo(1)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="fw-600" style="font-size:13px">Vidange -20% ce mois</div>
                <div style="font-size:11px;color:var(--text-muted)">Réduction sur service</div>
              </td>
              <td>
                <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600">
                  <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:10px"></i> Garage Auto Plus
                </span>
              </td>
              <td><span class="badge badge-warning">Réduction %</span></td>
              <td><span class="fw-700" style="color:var(--warning)">-20%</span></td>
              <td>
                <div style="font-size:12px">15 Mar → 15 Avr</div>
                <div style="font-size:11px;color:var(--text-muted)">27 jours restants</div>
              </td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-paper-plane" style="font-size:9px"></i> Oui</span></td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="editPromo(2)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#" onclick="togglePromo(2)"><i class="fa-solid fa-pause"></i> Désactiver</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deletePromo(2)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="fw-600" style="font-size:13px">2 000 FCFA de remise dès 30L</div>
                <div style="font-size:11px;color:var(--text-muted)">Réduction montant fixe</div>
              </td>
              <td>
                <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600">
                  <i class="fa-solid fa-gas-pump" style="color:var(--primary);font-size:10px"></i> Shell Plateau
                </span>
              </td>
              <td><span class="badge badge-primary">Montant fixe</span></td>
              <td><span class="fw-700" style="color:var(--primary)">-2 000 F</span></td>
              <td>
                <div style="font-size:12px">01 Avr → 30 Avr</div>
                <div style="font-size:11px;color:var(--text-muted)">À venir dans 14j</div>
              </td>
              <td><span class="badge badge-info"><i class="fa-solid fa-clock" style="font-size:7px"></i> À venir</span></td>
              <td><span class="badge badge-gray"><i class="fa-solid fa-times" style="font-size:9px"></i> Non</span></td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="editPromo(3)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#" onclick="sendPushPromo(3)"><i class="fa-solid fa-paper-plane"></i> Envoyer push maintenant</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deletePromo(3)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr style="opacity:.6">
              <td>
                <div class="fw-600" style="font-size:13px">Diagnostic gratuit</div>
                <div style="font-size:11px;color:var(--text-muted)">Offre spéciale</div>
              </td>
              <td>
                <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600">
                  <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:10px"></i> Centre Vidange Express
                </span>
              </td>
              <td><span class="badge badge-success">Service gratuit</span></td>
              <td><span class="fw-700" style="color:var(--text-muted)">Gratuit</span></td>
              <td>
                <div style="font-size:12px">01 → 28 Fév 2024</div>
                <div style="font-size:11px;color:var(--danger)">Expirée il y a 18j</div>
              </td>
              <td><span class="badge badge-gray"><i class="fa-solid fa-circle" style="font-size:7px"></i> Expirée</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-paper-plane" style="font-size:9px"></i> Oui</span></td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="duplicatePromo(4)"><i class="fa-solid fa-copy"></i> Dupliquer</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deletePromo(4)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">72 promotions au total</span>
        <div class="pagination">
          <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
          <div class="page-item active">1</div><div class="page-item">2</div><div class="page-item">3</div>
          <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Créer Promotion -->
<div class="modal-overlay" id="modalAddPromo">
  <div class="modal-box" style="max-width:580px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-tag" style="color:var(--primary)"></i> Créer une promotion</h5>
      <button class="modal-close" data-modal-close="modalAddPromo">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div><label class="form-label">Titre de la promotion *</label><input type="text" class="form-control" placeholder="Ex: Lavage offert pour tout plein de 20L+"></div>
        <div><label class="form-label">Description</label><textarea class="form-control" rows="2" placeholder="Détails de l'offre..."></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div>
            <label class="form-label">Type d'établissement *</label>
            <select class="form-select" onchange="updateEstabList(this.value)">
              <option value="">Sélectionner</option>
              <option value="station">Station-service</option>
              <option value="garage">Garage</option>
            </select>
          </div>
          <div>
            <label class="form-label">Établissement *</label>
            <select class="form-select" id="estabSelect"><option>— Choisir d'abord le type —</option></select>
          </div>
          <div>
            <label class="form-label">Type de promotion *</label>
            <select class="form-select" onchange="updateDiscountField(this.value)">
              <option value="">Sélectionner</option>
              <option value="discount_pct">Réduction %</option>
              <option value="discount_amt">Montant fixe (FCFA)</option>
              <option value="service_gratuit">Service gratuit</option>
              <option value="offre_speciale">Offre spéciale</option>
            </select>
          </div>
          <div id="discountField">
            <label class="form-label">Valeur de la remise</label>
            <input type="number" class="form-control" placeholder="Ex: 20 (pour 20%)">
          </div>
          <div>
            <label class="form-label">Date de début *</label>
            <input type="date" class="form-control">
          </div>
          <div>
            <label class="form-label">Date de fin *</label>
            <input type="date" class="form-control">
          </div>
        </div>
        <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <label class="toggle"><input type="checkbox" id="sendPushCheck"><span class="toggle-slider"></span></label>
            <div>
              <div class="fw-600" style="font-size:13px">Envoyer une notification push</div>
              <div style="font-size:12px;color:var(--text-muted)">Notifier les utilisateurs proches de l'établissement</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:13px;white-space:nowrap">Rayon de notification :</span>
            <select class="form-select" style="width:120px"><option>5 km</option><option>10 km</option><option>20 km</option></select>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalAddPromo">Annuler</button>
      <button class="btn btn-primary" onclick="savePromo()"><i class="fa-solid fa-check"></i> Créer la promotion</button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
const stations=['Total Énergies Cocody','Shell Plateau','Petro Ivoire Yopougon','Oryx Marcory'];
const garages=['Garage Auto Plus Cocody','Centre Vidange Express','Flash Lavage Auto','Top Dépannage 24h'];
function updateEstabList(type){
  const sel=document.getElementById('estabSelect');
  const list=type==='station'?stations:type==='garage'?garages:[];
  sel.innerHTML=list.length?list.map(n=>`<option>${n}</option>`).join(''):'<option>— Choisir d\'abord le type —</option>';
}
function updateDiscountField(type){
  const f=document.getElementById('discountField');
  const inp=f.querySelector('input');
  if(type==='service_gratuit'||type==='offre_speciale'){f.style.opacity='.4';inp.disabled=true;inp.placeholder='N/A';}
  else{f.style.opacity='1';inp.disabled=false;inp.placeholder=type==='discount_pct'?'Ex: 20 (pour 20%)':'Ex: 2000 (FCFA)';}
}
function editPromo(id){openModal('modalAddPromo');}
function togglePromo(id){showToast('Promotion mise en pause','warning');}
function deletePromo(id){confirmAction('Supprimer cette promotion ?',()=>showToast('Promotion supprimée','error'));}
function sendPushPromo(id){confirmAction('Envoyer une notification push maintenant ?',()=>showToast('Notification push envoyée','success'));}
function duplicatePromo(id){showToast('Promotion dupliquée — modifiez les dates','info');}
function savePromo(){showToast('Promotion créée avec succès','success');closeModal('modalAddPromo');}
</script>
</body>
</html>
