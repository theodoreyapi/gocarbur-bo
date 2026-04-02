<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Avis clients — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
    <li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li>
    <li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations-service</a></li>
    <li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages & Services</a></li>
    <li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge" style="background:#F59E0B">8</span></a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles & Conseils</a></li>
    <li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li>
    <li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières pub</a></li>
    <li><a class="nav-link-item active" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients <span class="nav-badge" style="background:#F59E0B">12</span></a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li>
    <li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li>
    <li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux d'activité</a></li>
    <li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li>
  </ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Avis clients</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher un avis..."></div>
    <div class="topbar-actions">
      <div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div>
      <div class="avatar-btn">SA</div>
    </div>
  </header>

  <main class="page-content">

    <div class="page-header">
      <h1>Modération des avis</h1>
      <p>Approuvez ou supprimez les avis déposés par les utilisateurs.</p>
    </div>

    <!-- Alerte modération -->
    <div class="alert alert-warning" style="margin-bottom:20px">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span><strong>12 avis</strong> sont en attente de modération. Pensez à les traiter régulièrement.</span>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-clock" style="color:var(--warning)"></i></div><div><div class="stat-value" style="font-size:22px">12</div><div class="stat-label">En attente</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-check" style="color:var(--success)"></i></div><div><div class="stat-value" style="font-size:22px">847</div><div class="stat-label">Approuvés</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEE2E2;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-trash" style="color:var(--danger)"></i></div><div><div class="stat-value" style="font-size:22px">34</div><div class="stat-label">Supprimés</div></div></div></div>
      <div class="stat-card" style="padding:16px"><div style="display:flex;align-items:center;gap:10px"><div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-star" style="color:var(--warning)"></i></div><div><div class="stat-value" style="font-size:22px">4.3</div><div class="stat-label">Note moyenne</div></div></div></div>
    </div>

    <!-- Onglets -->
    <div class="card">
      <div class="card-header" style="padding:0 20px" data-tabs>
        <div class="tab-nav">
          <button class="tab-btn active" data-tab="tab-pending">En attente <span class="nav-badge" style="background:var(--warning);margin-left:6px;vertical-align:middle">12</span></button>
          <button class="tab-btn" data-tab="tab-approved">Approuvés</button>
          <button class="tab-btn" data-tab="tab-all">Tous les avis</button>
        </div>
      </div>

      <!-- EN ATTENTE -->
      <div id="tab-pending" class="tab-content active">
        <div style="display:flex;flex-direction:column">

          <!-- Avis 1 — Station -->
          <div style="padding:20px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:flex-start;gap:14px">
              <div class="user-avatar" style="width:40px;height:40px;font-size:14px;flex-shrink:0">KA</div>
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:14px">Kouassi Aya</span>
                  <span style="font-size:12px;color:var(--text-muted)">sur</span>
                  <span style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:var(--primary)">
                    <i class="fa-solid fa-gas-pump" style="font-size:11px"></i> Total Énergies Cocody
                  </span>
                  <span class="badge badge-warning" style="margin-left:auto"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:11px;color:var(--text-muted)">il y a 2h</span>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:8px">
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <span class="fw-700" style="margin-left:6px;font-size:13px">5/5</span>
                </div>
                <p style="font-size:14px;color:var(--text);line-height:1.6;margin-bottom:12px">
                  "Excellent service, les pompes sont toujours bien entretenues et le personnel est très accueillant. Les prix sont affichés clairement. Je recommande vivement cette station !"
                </p>
                <div style="display:flex;gap:10px">
                  <button class="btn btn-success btn-sm" onclick="approveReview(1)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-danger btn-sm" onclick="deleteReview(1)"><i class="fa-solid fa-trash"></i> Supprimer</button>
                  <button class="btn btn-secondary btn-sm" onclick="viewReview(1)"><i class="fa-solid fa-eye"></i> Voir le contexte</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Avis 2 — Garage, mauvaise note -->
          <div style="padding:20px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:flex-start;gap:14px">
              <div class="user-avatar" style="width:40px;height:40px;font-size:14px;flex-shrink:0;background:linear-gradient(135deg,#EF4444,#B91C1C)">DT</div>
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:14px">Diaby Tiémoko</span>
                  <span style="font-size:12px;color:var(--text-muted)">sur</span>
                  <span style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:#8B5CF6">
                    <i class="fa-solid fa-wrench" style="font-size:11px"></i> Garage Auto Plus
                  </span>
                  <span class="badge badge-warning" style="margin-left:auto"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:11px;color:var(--text-muted)">il y a 5h</span>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:8px">
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-regular fa-star" style="color:var(--border);font-size:14px"></i>
                  <i class="fa-regular fa-star" style="color:var(--border);font-size:14px"></i>
                  <i class="fa-regular fa-star" style="color:var(--border);font-size:14px"></i>
                  <span class="fw-700" style="margin-left:6px;font-size:13px">2/5</span>
                </div>
                <div class="alert alert-warning" style="margin-bottom:10px;padding:8px 12px">
                  <i class="fa-solid fa-flag"></i>
                  <span style="font-size:12px">Note basse détectée — vérifiez le contenu avant approbation.</span>
                </div>
                <p style="font-size:14px;color:var(--text);line-height:1.6;margin-bottom:12px">
                  "J'ai attendu 2h pour une simple vidange. Pas de communication sur les délais. Le prix final était différent du devis donné au départ. Très déçu."
                </p>
                <div style="display:flex;gap:10px">
                  <button class="btn btn-success btn-sm" onclick="approveReview(2)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-danger btn-sm" onclick="deleteReview(2)"><i class="fa-solid fa-trash"></i> Supprimer</button>
                  <button class="btn btn-secondary btn-sm" onclick="viewReview(2)"><i class="fa-solid fa-eye"></i> Voir le contexte</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Avis 3 -->
          <div style="padding:20px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:flex-start;gap:14px">
              <div class="user-avatar" style="width:40px;height:40px;font-size:14px;flex-shrink:0;background:linear-gradient(135deg,#3B82F6,#1D4ED8)">BK</div>
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px">
                  <span class="fw-700" style="font-size:14px">Bamba Koné</span>
                  <span style="font-size:12px;color:var(--text-muted)">sur</span>
                  <span style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:var(--primary)">
                    <i class="fa-solid fa-gas-pump" style="font-size:11px"></i> Petro Ivoire Yopougon
                  </span>
                  <span class="badge badge-warning" style="margin-left:auto"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>
                  <span style="font-size:11px;color:var(--text-muted)">il y a 1j</span>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:8px">
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:14px"></i>
                  <i class="fa-regular fa-star" style="color:var(--border);font-size:14px"></i>
                  <span class="fw-700" style="margin-left:6px;font-size:13px">4/5</span>
                </div>
                <p style="font-size:14px;color:var(--text);line-height:1.6;margin-bottom:12px">
                  "Station propre, prix correct. Un peu d'attente le week-end mais normal. Le service de lavage est top."
                </p>
                <div style="display:flex;gap:10px">
                  <button class="btn btn-success btn-sm" onclick="approveReview(3)"><i class="fa-solid fa-check"></i> Approuver</button>
                  <button class="btn btn-danger btn-sm" onclick="deleteReview(3)"><i class="fa-solid fa-trash"></i> Supprimer</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Approuver tout -->
          <div style="padding:16px 20px;background:var(--bg);display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--text-muted)">12 avis en attente de modération</span>
            <button class="btn btn-success btn-sm" onclick="approveAll()">
              <i class="fa-solid fa-check-double"></i> Tout approuver
            </button>
          </div>
        </div>
      </div>

      <!-- APPROUVÉS -->
      <div id="tab-approved" class="tab-content">
        <div class="filter-bar">
          <div style="position:relative;flex:1;min-width:200px">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
            <input type="text" placeholder="Rechercher..."
              style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
          </div>
          <select class="form-select" style="width:130px">
            <option>Toutes notes</option>
            <option>5 étoiles</option>
            <option>4 étoiles</option>
            <option>3 étoiles</option>
            <option>2 étoiles</option>
            <option>1 étoile</option>
          </select>
          <select class="form-select" style="width:140px">
            <option>Stations & Garages</option>
            <option>Stations seulement</option>
            <option>Garages seulement</option>
          </select>
        </div>
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr><th>Utilisateur</th><th>Établissement</th><th>Note</th><th>Commentaire</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar">NA</div><span class="fw-600">N'Guessan Ahou</span></div></td>
                <td><span style="font-size:12px"><i class="fa-solid fa-gas-pump" style="color:var(--primary);margin-right:4px"></i>Total Énergies Cocody</span></td>
                <td><div style="display:flex;align-items:center;gap:3px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><span class="fw-600" style="margin-left:4px;font-size:12px">5</span></div></td>
                <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:var(--text-muted)">"Très bonne station, pompes bien entretenues..."</td>
                <td style="font-size:12px;color:var(--text-muted)">15 Mar 2024</td>
                <td><button class="btn btn-sm btn-danger" onclick="deleteReview(10)"><i class="fa-solid fa-trash"></i></button></td>
              </tr>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar" style="background:linear-gradient(135deg,#F59E0B,#D97706)">TO</div><span class="fw-600">Touré Oumar</span></div></td>
                <td><span style="font-size:12px"><i class="fa-solid fa-wrench" style="color:#8B5CF6;margin-right:4px"></i>Garage Auto Plus</span></td>
                <td><div style="display:flex;align-items:center;gap:3px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><i class="fa-regular fa-star" style="color:var(--border);font-size:12px"></i><span class="fw-600" style="margin-left:4px;font-size:12px">4</span></div></td>
                <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:var(--text-muted)">"Bonne mécanique, délai raisonnable..."</td>
                <td style="font-size:12px;color:var(--text-muted)">12 Mar 2024</td>
                <td><button class="btn btn-sm btn-danger" onclick="deleteReview(11)"><i class="fa-solid fa-trash"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
          <span style="font-size:13px;color:var(--text-muted)">847 avis approuvés</span>
          <div class="pagination"><div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div><div class="page-item active">1</div><div class="page-item">2</div><div class="page-item">3</div><div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div></div>
        </div>
      </div>

      <!-- TOUS -->
      <div id="tab-all" class="tab-content">
        <div class="filter-bar">
          <div style="position:relative;flex:1;min-width:200px">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
            <input type="text" placeholder="Rechercher..." style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
          </div>
          <select class="form-select" style="width:130px"><option>Tous statuts</option><option>En attente</option><option>Approuvé</option><option>Supprimé</option></select>
          <select class="form-select" style="width:130px"><option>Toutes notes</option><option>5 ⭐</option><option>4 ⭐</option><option>3 ⭐</option><option>≤ 2 ⭐</option></select>
        </div>
        <div class="table-wrapper">
          <table class="table">
            <thead><tr><th>Utilisateur</th><th>Établissement</th><th>Note</th><th>Aperçu</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar">KA</div>Kouassi Aya</div></td>
                <td>Total Énergies Cocody</td>
                <td>⭐⭐⭐⭐⭐</td>
                <td style="max-width:180px;font-size:12px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">"Excellent service..."</td>
                <td><span class="badge badge-warning">En attente</span></td>
                <td style="font-size:12px">18 Mar 2024</td>
                <td><div style="display:flex;gap:5px"><button class="btn btn-success btn-sm" onclick="approveReview(1)"><i class="fa-solid fa-check"></i></button><button class="btn btn-danger btn-sm" onclick="deleteReview(1)"><i class="fa-solid fa-trash"></i></button></div></td>
              </tr>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar" style="background:linear-gradient(135deg,#EF4444,#B91C1C)">DT</div>Diaby Tiémoko</div></td>
                <td>Garage Auto Plus</td>
                <td>⭐⭐</td>
                <td style="max-width:180px;font-size:12px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">"J'ai attendu 2h..."</td>
                <td><span class="badge badge-warning">En attente</span></td>
                <td style="font-size:12px">17 Mar 2024</td>
                <td><div style="display:flex;gap:5px"><button class="btn btn-success btn-sm" onclick="approveReview(2)"><i class="fa-solid fa-check"></i></button><button class="btn btn-danger btn-sm" onclick="deleteReview(2)"><i class="fa-solid fa-trash"></i></button></div></td>
              </tr>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px"><div class="user-avatar" style="background:linear-gradient(135deg,#10B981,#059669)">NA</div>N'Guessan Ahou</div></td>
                <td>Total Énergies Cocody</td>
                <td>⭐⭐⭐⭐⭐</td>
                <td style="max-width:180px;font-size:12px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">"Très bonne station..."</td>
                <td><span class="badge badge-success">Approuvé</span></td>
                <td style="font-size:12px">15 Mar 2024</td>
                <td><div style="display:flex;gap:5px"><button class="btn btn-danger btn-sm" onclick="deleteReview(10)"><i class="fa-solid fa-trash"></i></button></div></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
          <span style="font-size:13px;color:var(--text-muted)">859 avis au total</span>
          <div class="pagination"><div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div><div class="page-item active">1</div><div class="page-item">2</div><div class="page-item">...</div><div class="page-item">35</div><div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div></div>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function approveReview(id){showToast('Avis approuvé et publié','success');}
function deleteReview(id){confirmAction('Supprimer cet avis définitivement ?',()=>showToast('Avis supprimé','error'));}
function approveAll(){confirmAction('Approuver les 12 avis en attente ?',()=>showToast('12 avis approuvés','success'));}
function viewReview(id){showToast('Chargement du contexte...','info');}
</script>
</body>
</html>
