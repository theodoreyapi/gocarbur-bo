<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item active" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications <span class="nav-badge">5</span></a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>
<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Notifications Push</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>
  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
      <div><h1>Notifications Push</h1><p>Envoyez des notifications aux utilisateurs de l'application.</p></div>
      <button class="btn btn-primary" data-modal-open="modalSendNotif"><i class="fa-solid fa-paper-plane"></i> Envoyer notification</button>
    </div>
    <!-- Nouvelle notification -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><div class="card-title"><i class="fa-solid fa-paper-plane" style="color:var(--primary)"></i> Envoyer une notification</div></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
          <div><label class="form-label">Cible</label><select class="form-select" onchange="updateTarget(this.value)"><option value="all">Tous les utilisateurs</option><option value="premium">Utilisateurs Premium</option><option value="city">Par ville</option><option value="user">Utilisateur spécifique</option></select></div>
          <div id="cityField" style="display:none"><label class="form-label">Ville cible</label><select class="form-select"><option>Abidjan</option><option>Bouaké</option><option>Daloa</option></select></div>
          <div><label class="form-label">Type</label><select class="form-select"><option>Système</option><option>Alerte carburant</option><option>Promotion</option><option>Conseil</option><option>Broadcast</option></select></div>
        </div>
        <div style="margin-bottom:12px"><label class="form-label">Titre *</label><input type="text" class="form-control" placeholder="Ex: Nouvelle promotion !"></div>
        <div style="margin-bottom:12px"><label class="form-label">Message *</label><textarea class="form-control" rows="3" placeholder="Contenu de la notification..."></textarea></div>
        <div style="margin-bottom:16px"><label class="form-label">Lien (optionnel)</label><input type="text" class="form-control" placeholder="Ex: /stations/detail/5"></div>
        <div style="display:flex;gap:10px">
          <button class="btn btn-primary" onclick="sendNotification()"><i class="fa-solid fa-paper-plane"></i> Envoyer maintenant</button>
          <button class="btn btn-secondary"><i class="fa-solid fa-clock"></i> Planifier</button>
        </div>
      </div>
    </div>
    <!-- Historique -->
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="fa-solid fa-history" style="color:var(--info)"></i> Historique des broadcasts</div></div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Titre</th><th>Message</th><th>Cible</th><th>Envoyés</th><th>Date</th></tr></thead>
          <tbody>
            <tr><td class="fw-600">Prix carburant en baisse !</td><td>Total Cocody : Essence à 680 FCFA/L</td><td><span class="badge badge-success">Premium (347)</span></td><td>312 / 347</td><td>18 Mar 2024 14:30</td></tr>
            <tr><td class="fw-600">Nouveau garage partenaire</td><td>Garage Auto Plus Cocody rejoint la plateforme</td><td><span class="badge badge-info">Tous (1 248)</span></td><td>1 134 / 1 248</td><td>15 Mar 2024 09:00</td></tr>
            <tr><td class="fw-600">Rappel maintenance</td><td>N'oubliez pas de vérifier votre véhicule avant les fêtes</td><td><span class="badge badge-purple">Abidjan (856)</span></td><td>798 / 856</td><td>10 Mar 2024 08:00</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function updateTarget(v){document.getElementById('cityField').style.display=v==='city'?'block':'none';}
function sendNotification(){showToast('Notification envoyée à 1 248 utilisateurs','success');}
</script>
</body>
</html>
