<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Articles — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item active" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Articles & Conseils</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
      <div><h1>Articles & Conseils</h1><p>Gérez le contenu éditorial de l'application.</p></div>
      <button class="btn btn-primary" data-modal-open="modalAddArticle"><i class="fa-solid fa-plus"></i> Nouvel article</button>
    </div>

    <div class="card">
      <div class="card-header" data-tabs>
        <div class="tab-nav">
          <button class="tab-btn active" data-tab="tab-all">Tous (42)</button>
          <button class="tab-btn" data-tab="tab-published">Publiés (35)</button>
          <button class="tab-btn" data-tab="tab-draft">Brouillons (7)</button>
          <button class="tab-btn" data-tab="tab-sponsored">Sponsorisés (8)</button>
        </div>
      </div>
      <div class="filter-bar">
        <div style="position:relative;flex:1"><i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i><input type="text" placeholder="Titre, catégorie..." style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)"></div>
        <select class="form-select" style="width:180px"><option>Toutes catégories</option><option>Entretien auto</option><option>Économie carburant</option><option>Sécurité</option><option>Documents admin</option><option>Astuces</option></select>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Article</th><th>Catégorie</th><th>Vues</th><th>Lecture</th><th>Sponsorisé</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td><div class="fw-600" style="max-width:320px">5 signes que votre voiture a besoin d'une vidange</div></td>
              <td><span class="badge badge-primary">Entretien auto</span></td>
              <td>1 248</td><td>3 min</td>
              <td><i class="fa-solid fa-times" style="color:var(--text-light)"></i></td>
              <td><span class="badge badge-success">Publié</span></td>
              <td>15 Mar 2024</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item" href="#" onclick="editArticle()"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#"><i class="fa-solid fa-eye-slash"></i> Dépublier</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><div class="fw-600" style="max-width:320px">Comment réduire de 20% sa consommation de carburant</div></td>
              <td><span class="badge badge-info">Économie carburant</span></td>
              <td>987</td><td>5 min</td>
              <td><i class="fa-solid fa-check" style="color:var(--warning)"></i></td>
              <td><span class="badge badge-success">Publié</span></td>
              <td>12 Mar 2024</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i> Modifier</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><div class="fw-600" style="max-width:320px">Renouvellement visite technique : tout ce qu'il faut savoir</div></td>
              <td><span class="badge badge-warning">Documents admin</span></td>
              <td>0</td><td>6 min</td>
              <td><i class="fa-solid fa-times" style="color:var(--text-light)"></i></td>
              <td><span class="badge badge-gray">Brouillon</span></td>
              <td>18 Mar 2024</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item" href="#"><i class="fa-solid fa-pen"></i> Modifier</a><a class="dropdown-item" href="#"><i class="fa-solid fa-upload"></i> Publier</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)"><span style="font-size:13px;color:var(--text-muted)">42 articles au total</span><div class="pagination"><div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div><div class="page-item active">1</div><div class="page-item">2</div><div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div></div></div>
    </div>
  </main>
</div>

<!-- Modal Ajouter Article -->
<div class="modal-overlay" id="modalAddArticle">
  <div class="modal-box" style="max-width:700px">
    <div class="modal-header"><h5><i class="fa-solid fa-newspaper" style="color:var(--primary)"></i> Nouvel article</h5><button class="modal-close" data-modal-close="modalAddArticle">✕</button></div>
    <div class="modal-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div><label class="form-label">Titre *</label><input type="text" class="form-control" placeholder="Ex: 5 signes que votre voiture a besoin d'une vidange"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div><label class="form-label">Catégorie *</label><select class="form-select"><option>Entretien auto</option><option>Économie carburant</option><option>Sécurité</option><option>Documents admin</option><option>Astuces mécaniques</option><option>Actualités</option></select></div>
          <div><label class="form-label">Temps de lecture (min)</label><input type="number" class="form-control" value="5" min="1"></div>
        </div>
        <div><label class="form-label">Extrait</label><textarea class="form-control" rows="2" placeholder="Résumé court affiché dans la liste..."></textarea></div>
        <div><label class="form-label">Contenu *</label><textarea class="form-control" rows="8" placeholder="Rédigez votre article ici..."></textarea></div>
        <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg);border-radius:var(--radius-sm)">
          <label class="toggle"><input type="checkbox" onchange="toggleSponsor(this)"><span class="toggle-slider"></span></label>
          <span class="fw-600" style="font-size:13px">Article sponsorisé</span>
        </div>
        <div id="sponsorFields" style="display:none;display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div><label class="form-label">Nom du sponsor</label><input type="text" class="form-control" placeholder="Ex: Castrol"></div>
          <div><label class="form-label">URL sponsor</label><input type="url" class="form-control" placeholder="https://..."></div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
          <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
          <span class="fw-600" style="font-size:13px">Publier immédiatement</span>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-modal-close="modalAddArticle">Annuler</button><button class="btn btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Sauver brouillon</button><button class="btn btn-primary" onclick="saveArticle()"><i class="fa-solid fa-upload"></i> Publier</button></div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function toggleSponsor(cb) { document.getElementById('sponsorFields').style.display = cb.checked ? 'grid' : 'none'; }
function editArticle() { openModal('modalAddArticle'); }
function saveArticle() { showToast('Article publié avec succès', 'success'); closeModal('modalAddArticle'); }
</script>
</body>
</html>
