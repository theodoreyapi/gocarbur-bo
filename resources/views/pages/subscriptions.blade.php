<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Abonnements — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes partenaires</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item active" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li><li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Abonnements</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header"><h1>Abonnements</h1><p>Gestion des abonnements actifs et des revenus.</p></div>

    <!-- Revenue KPIs -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
      <div class="stat-card">
        <div class="stat-icon" style="background:#FEF3C7"><i class="fa-solid fa-sack-dollar" style="color:var(--warning)"></i></div>
        <div class="stat-value">4 230 000</div>
        <div class="stat-label">Revenus ce mois (FCFA)</div>
        <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +18%</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#D1FAE5"><i class="fa-solid fa-crown" style="color:var(--success)"></i></div>
        <div class="stat-value">347</div>
        <div class="stat-label">Abonnements actifs</div>
        <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +24 ce mois</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#FEE2E2"><i class="fa-solid fa-clock-rotate-left" style="color:var(--danger)"></i></div>
        <div class="stat-value">28</div>
        <div class="stat-label">Expirent ce mois</div>
        <div class="stat-change down"><i class="fa-solid fa-exclamation"></i> À renouveler</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#DBEAFE"><i class="fa-solid fa-chart-line" style="color:var(--info)"></i></div>
        <div class="stat-value">89%</div>
        <div class="stat-label">Taux de renouvellement</div>
        <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> Excellent</div>
      </div>
    </div>

    <!-- Graphe revenus par plan -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-chart-bar" style="color:var(--primary)"></i> Revenus par plan — 6 derniers mois</div></div>
        <div class="card-body"><div class="chart-container"><canvas id="subRevenueChart"></canvas></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-list" style="color:var(--info)"></i> Revenus par plan</div></div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:14px">
            <div>
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span><i class="fa-solid fa-circle" style="color:var(--primary);font-size:10px"></i> User Premium (1 500/mois)</span><strong>363 500 FCFA</strong></div>
              <div class="progress"><div class="progress-bar" style="width:56%;background:var(--primary)"></div></div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span><i class="fa-solid fa-circle" style="color:var(--info);font-size:10px"></i> Station Pro (12 500/mois)</span><strong>2 375 000 FCFA</strong></div>
              <div class="progress"><div class="progress-bar" style="width:36%;background:var(--info)"></div></div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span><i class="fa-solid fa-circle" style="color:var(--purple);font-size:10px"></i> Station Premium (32 500/mois)</span><strong>1 267 500 FCFA</strong></div>
              <div class="progress"><div class="progress-bar" style="width:19%;background:var(--purple)"></div></div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span><i class="fa-solid fa-circle" style="color:var(--success);font-size:10px"></i> Garage Pro/Premium</span><strong>224 000 FCFA</strong></div>
              <div class="progress"><div class="progress-bar" style="width:3%;background:var(--success)"></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table abonnements -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Abonnements actifs</div>
        <div style="display:flex;gap:10px">
          <select class="form-select" style="width:150px"><option>Tous les plans</option><option>User Premium</option><option>Station Pro</option><option>Station Premium</option><option>Garage Pro</option></select>
          <button class="btn btn-secondary btn-sm"><i class="fa-solid fa-download"></i> Export</button>
        </div>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr><th>Abonné</th><th>Type</th><th>Plan</th><th>Méthode</th><th>Montant</th><th>Début</th><th>Expiration</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar">KA</div><span class="fw-600">Kouassi Aya</span></div></td>
              <td><span class="badge badge-primary"><i class="fa-solid fa-user" style="font-size:9px"></i> Utilisateur</span></td>
              <td><span class="badge badge-success">Premium</span></td>
              <td><span style="display:flex;align-items:center;gap:5px;font-size:12px"><i class="fa-solid fa-circle" style="color:#FF6600;font-size:8px"></i> Orange Money</span></td>
              <td class="fw-600">1 500 FCFA</td>
              <td>18 Mar 2024</td>
              <td>18 Avr 2024</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-plus"></i> Prolonger</a><a class="dropdown-item text-danger" href="#" onclick="cancelSub()"><i class="fa-solid fa-ban"></i> Annuler</a></div></div></td>
            </tr>
            <tr>
              <td><div style="display:flex;align-items:center;gap:8px"><div style="width:34px;height:34px;background:linear-gradient(135deg,#3B82F6,#1D4ED8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700">TE</div><span class="fw-600">Total Énergies Cocody</span></div></td>
              <td><span class="badge badge-info"><i class="fa-solid fa-gas-pump" style="font-size:9px"></i> Station</span></td>
              <td><span class="badge badge-purple">Premium</span></td>
              <td><span style="display:flex;align-items:center;gap:5px;font-size:12px"><i class="fa-solid fa-circle" style="color:#1CB5E0;font-size:8px"></i> Wave</span></td>
              <td class="fw-600">32 500 FCFA</td>
              <td>01 Mar 2024</td>
              <td>01 Avr 2024</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-plus"></i> Prolonger</a><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-ban"></i> Annuler</a></div></div></td>
            </tr>
            <tr>
              <td><div style="display:flex;align-items:center;gap:8px"><div style="width:34px;height:34px;background:linear-gradient(135deg,#10B981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700">GA</div><span class="fw-600">Garage Auto Plus</span></div></td>
              <td><span class="badge badge-purple"><i class="fa-solid fa-wrench" style="font-size:9px"></i> Garage</span></td>
              <td><span class="badge badge-info">Pro</span></td>
              <td><span style="display:flex;align-items:center;gap:5px;font-size:12px"><i class="fa-solid fa-circle" style="color:#FFCC00;font-size:8px"></i> MTN MoMo</span></td>
              <td class="fw-600">12 500 FCFA</td>
              <td>05 Mar 2024</td>
              <td>05 Avr 2024</td>
              <td><span class="badge badge-warning"><i class="fa-solid fa-circle" style="font-size:7px"></i> Expire bientôt</span></td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-plus"></i> Prolonger</a><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-ban"></i> Annuler</a></div></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)"><span style="font-size:13px;color:var(--text-muted)">347 abonnements actifs</span><div class="pagination"><div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div><div class="page-item active">1</div><div class="page-item">2</div><div class="page-item">3</div><div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div></div></div>
    </div>
  </main>
</div>
<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
new Chart(document.getElementById('subRevenueChart'), {
  type: 'bar',
  data: {
    labels: ['Oct','Nov','Déc','Jan','Fév','Mar'],
    datasets: [
      { label: 'User Premium', data: [180000,210000,240000,195000,280000,363500], backgroundColor: '#FF6B35', borderRadius: 4 },
      { label: 'Station Pro/Premium', data: [1800000,2100000,2500000,2200000,3100000,3642500], backgroundColor: '#3B82F6', borderRadius: 4 },
      { label: 'Garage Pro/Premium', data: [100000,120000,150000,140000,190000,224000], backgroundColor: '#8B5CF6', borderRadius: 4 },
    ],
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10 } } },
    scales: { x: { grid: { display: false } }, y: { grid: { color: '#F1F5F9' }, ticks: { callback: v => (v/1000000).toFixed(1)+'M' } } },
  },
});
function cancelSub() { confirmAction('Annuler cet abonnement ?', () => showToast('Abonnement annulé', 'warning')); }
</script>
</body>
</html>
