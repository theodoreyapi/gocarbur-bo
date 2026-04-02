<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bannières — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item active" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières pub</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Bannières publicitaires</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div><h1>Bannières publicitaires</h1><p>Gérez les espaces publicitaires affichés dans l'application.</p></div>
      <button class="btn btn-primary" onclick="openModal('modalAddBanner')"><i class="fa-solid fa-plus"></i> Nouvelle bannière</button>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-rectangle-ad" style="color:var(--success)"></i></div><div><div class="stat-value" style="font-size:22px">6</div><div class="stat-label">Bannières actives</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#DBEAFE;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-eye" style="color:var(--info)"></i></div><div><div class="stat-value" style="font-size:22px">48 240</div><div class="stat-label">Impressions ce mois</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FFF0EB;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-arrow-pointer" style="color:var(--primary)"></i></div><div><div class="stat-value" style="font-size:22px">1 847</div><div class="stat-label">Clics ce mois</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-percent" style="color:var(--warning)"></i></div><div><div class="stat-value" style="font-size:22px">3.8%</div><div class="stat-label">Taux de clic (CTR)</div></div></div></div>
    </div>

    <!-- Bannières en cards visuelles -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;margin-bottom:24px">

      <!-- Banner 1 — Home Hero -->
      <div class="card" style="overflow:hidden">
        <div style="height:100px;background:linear-gradient(135deg,#FF6B35,#FF9A6C);display:flex;align-items:center;justify-content:center;position:relative">
          <div style="text-align:center;color:#fff">
            <div style="font-size:18px;font-weight:800">Castrol GTX</div>
            <div style="font-size:12px;opacity:.9">Huile moteur — Disponible chez nos partenaires</div>
          </div>
          <div style="position:absolute;top:8px;right:8px">
            <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active</span>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div>
              <div class="fw-700" style="font-size:14px">Castrol GTX — Accueil hero</div>
              <div style="font-size:12px;color:var(--text-muted)">Position : Bannière accueil principale</div>
            </div>
            <div class="dropdown">
              <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="editBanner(1)"><i class="fa-solid fa-pen"></i> Modifier</a>
                <a class="dropdown-item" href="#" onclick="toggleBanner(1)"><i class="fa-solid fa-pause"></i> Désactiver</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" onclick="deleteBanner(1)"><i class="fa-solid fa-trash"></i> Supprimer</a>
              </div>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px">
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">18 400</div>
              <div style="font-size:10px;color:var(--text-muted)">Impressions</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">742</div>
              <div style="font-size:10px;color:var(--text-muted)">Clics</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px;color:var(--success)">4.0%</div>
              <div style="font-size:10px;color:var(--text-muted)">CTR</div>
            </div>
          </div>
          <div style="font-size:11px;color:var(--text-muted)">01 Mar → 30 Avr 2024 • Ciblage : Tous</div>
        </div>
      </div>

      <!-- Banner 2 — Entretien -->
      <div class="card" style="overflow:hidden">
        <div style="height:100px;background:linear-gradient(135deg,#3B82F6,#1D4ED8);display:flex;align-items:center;justify-content:center;position:relative">
          <div style="text-align:center;color:#fff">
            <div style="font-size:16px;font-weight:800">Bosch Service</div>
            <div style="font-size:12px;opacity:.9">Pièces auto d'origine</div>
          </div>
          <div style="position:absolute;top:8px;right:8px">
            <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active</span>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div>
              <div class="fw-700" style="font-size:14px">Bosch Service — Section entretien</div>
              <div style="font-size:12px;color:var(--text-muted)">Position : Entre articles conseils</div>
            </div>
            <div class="dropdown">
              <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="editBanner(2)"><i class="fa-solid fa-pen"></i> Modifier</a>
                <a class="dropdown-item" href="#" onclick="toggleBanner(2)"><i class="fa-solid fa-pause"></i> Désactiver</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" onclick="deleteBanner(2)"><i class="fa-solid fa-trash"></i> Supprimer</a>
              </div>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px">
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">12 600</div>
              <div style="font-size:10px;color:var(--text-muted)">Impressions</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">438</div>
              <div style="font-size:10px;color:var(--text-muted)">Clics</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px;color:var(--warning)">3.5%</div>
              <div style="font-size:10px;color:var(--text-muted)">CTR</div>
            </div>
          </div>
          <div style="font-size:11px;color:var(--text-muted)">15 Mar → 15 Avr 2024 • Ciblage : Premium uniquement</div>
        </div>
      </div>

      <!-- Banner 3 — Désactivée -->
      <div class="card" style="overflow:hidden;opacity:.65">
        <div style="height:100px;background:linear-gradient(135deg,#64748B,#475569);display:flex;align-items:center;justify-content:center;position:relative">
          <div style="text-align:center;color:#fff">
            <div style="font-size:16px;font-weight:800">Promo Total CI</div>
            <div style="font-size:12px;opacity:.9">Campagne terminée</div>
          </div>
          <div style="position:absolute;top:8px;right:8px">
            <span class="badge badge-gray"><i class="fa-solid fa-circle" style="font-size:7px"></i> Désactivée</span>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div>
              <div class="fw-700" style="font-size:14px">Total CI — Campagne Noël</div>
              <div style="font-size:12px;color:var(--text-muted)">Position : Bannière accueil secondaire</div>
            </div>
            <div class="dropdown">
              <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="toggleBanner(3)"><i class="fa-solid fa-play"></i> Réactiver</a>
                <a class="dropdown-item" href="#" onclick="editBanner(3)"><i class="fa-solid fa-pen"></i> Modifier</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" onclick="deleteBanner(3)"><i class="fa-solid fa-trash"></i> Supprimer</a>
              </div>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px">
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">17 240</div>
              <div style="font-size:10px;color:var(--text-muted)">Impressions</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px">667</div>
              <div style="font-size:10px;color:var(--text-muted)">Clics</div>
            </div>
            <div style="text-align:center;background:var(--bg);padding:8px;border-radius:var(--radius-sm)">
              <div class="fw-700" style="font-size:15px;color:var(--success)">3.9%</div>
              <div style="font-size:10px;color:var(--text-muted)">CTR</div>
            </div>
          </div>
          <div style="font-size:11px;color:var(--text-muted)">01 Déc 2023 → 02 Jan 2024 • Expirée</div>
        </div>
      </div>

    </div>

    <!-- Tableau détaillé -->
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fa-solid fa-table" style="color:var(--info)"></i> Vue tableau — Toutes les bannières</div></div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Nom</th><th>Position</th><th>Annonceur</th><th>Impressions</th><th>Clics</th><th>CTR</th><th>Période</th><th>Statut</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td class="fw-600">Castrol GTX</td>
              <td><span class="badge badge-primary">Accueil hero</span></td>
              <td>Castrol CI</td>
              <td>18 400</td><td>742</td>
              <td><span class="fw-700" style="color:var(--success)">4.0%</span></td>
              <td style="font-size:12px">01 Mar → 30 Avr</td>
              <td><span class="badge badge-success">Active</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#"><i class="fa-solid fa-pause"></i> Désactiver</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td class="fw-600">Bosch Service</td>
              <td><span class="badge badge-info">Entre articles</span></td>
              <td>Bosch CI</td>
              <td>12 600</td><td>438</td>
              <td><span class="fw-700" style="color:var(--warning)">3.5%</span></td>
              <td style="font-size:12px">15 Mar → 15 Avr</td>
              <td><span class="badge badge-success">Active</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#"><i class="fa-solid fa-pause"></i> Désactiver</a></div></div></td>
            </tr>
            <tr style="opacity:.65">
              <td class="fw-600">Total CI Noël</td>
              <td><span class="badge badge-gray">Accueil secondaire</span></td>
              <td>Total CI</td>
              <td>17 240</td><td>667</td>
              <td><span class="fw-700" style="color:var(--text-muted)">3.9%</span></td>
              <td style="font-size:12px">Déc 23 → Jan 24</td>
              <td><span class="badge badge-gray">Désactivée</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-play"></i> Réactiver</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Modal Nouvelle Bannière -->
<div class="modal-overlay" id="modalAddBanner">
  <div class="modal-box" style="max-width:560px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-rectangle-ad" style="color:var(--primary)"></i> Nouvelle bannière</h5>
      <button class="modal-close" data-modal-close="modalAddBanner">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div><label class="form-label">Nom interne *</label><input type="text" class="form-control" placeholder="Ex: Castrol GTX — Accueil Mars 2024"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div>
            <label class="form-label">Position d'affichage *</label>
            <select class="form-select">
              <option>Bannière accueil principale</option>
              <option>Bannière accueil secondaire</option>
              <option>Entre articles conseils</option>
              <option>Détail station</option>
              <option>Détail garage</option>
              <option>Liste stations</option>
              <option>Liste garages</option>
            </select>
          </div>
          <div>
            <label class="form-label">Annonceur</label>
            <input type="text" class="form-control" placeholder="Nom de l'entreprise">
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
        <div><label class="form-label">Image de la bannière (recommandé : 1200×400 px)</label><input type="file" class="form-control" accept="image/*"></div>
        <div><label class="form-label">URL de destination (lien au clic)</label><input type="url" class="form-control" placeholder="https://..."></div>
        <div>
          <label class="form-label">Ciblage utilisateurs</label>
          <select class="form-select">
            <option>Tous les utilisateurs</option>
            <option>Utilisateurs Premium uniquement</option>
            <option>Utilisateurs gratuits uniquement</option>
            <option>Par ville — Abidjan</option>
            <option>Par ville — Bouaké</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalAddBanner">Annuler</button>
      <button class="btn btn-primary" onclick="saveBanner()"><i class="fa-solid fa-check"></i> Créer la bannière</button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function editBanner(id){openModal('modalAddBanner');}
function toggleBanner(id){showToast('Statut bannière mis à jour','info');}
function deleteBanner(id){confirmAction('Supprimer cette bannière ?',()=>showToast('Bannière supprimée','error'));}
function saveBanner(){showToast('Bannière créée avec succès','success');closeModal('modalAddBanner');}
</script>
</body>
</html>
