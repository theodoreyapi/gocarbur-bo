<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Garages & Services — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
    <li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs <span class="nav-badge">1 248</span></a></li>
    <li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations-service</a></li>
    <li><a class="nav-link-item active" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages & Services</a></li>
    <li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge" style="background:#F59E0B">8</span></a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles & Conseils</a></li>
    <li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li>
    <li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières pub</a></li>
    <li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients <span class="nav-badge" style="background:#F59E0B">12</span></a></li>
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
    <button class="btn-icon" onclick="toggleSidebar()" style="display:none"><i class="fa-solid fa-bars"></i></button>
    <div class="topbar-title">Garages & Services</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher un garage..."></div>
    <div class="topbar-actions">
      <div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div>
      <div class="dropdown">
        <div class="avatar-btn" data-toggle="dropdown">SA</div>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#"><i class="fa-solid fa-user"></i> Mon profil</a>
          <a class="dropdown-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="login.html"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </div>
      </div>
    </div>
  </header>

  <main class="page-content">

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <h1>Garages & Services</h1>
        <p>Gérez tous les garages et services automobiles partenaires.</p>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-secondary" onclick="exportGarages()"><i class="fa-solid fa-download"></i> Export CSV</button>
        <button class="btn btn-primary" onclick="openModal('modalAddGarage')"><i class="fa-solid fa-plus"></i> Ajouter garage</button>
      </div>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#EDE9FE;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-wrench" style="color:#8B5CF6"></i></div>
          <div><div class="stat-value" style="font-size:22px">156</div><div class="stat-label">Total garages</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#D1FAE5;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-shield-check" style="color:var(--success)"></i></div>
          <div><div class="stat-value" style="font-size:22px">98</div><div class="stat-label">Vérifiés</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#DBEAFE;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-crown" style="color:var(--info)"></i></div>
          <div><div class="stat-value" style="font-size:22px">47</div><div class="stat-label">Pro / Premium</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#FEF3C7;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-star" style="color:var(--warning)"></i></div>
          <div><div class="stat-value" style="font-size:22px">4.3</div><div class="stat-label">Note moyenne</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#FEE2E2;margin:0;width:40px;height:40px;font-size:17px"><i class="fa-solid fa-ban" style="color:var(--danger)"></i></div>
          <div><div class="stat-value" style="font-size:22px">11</div><div class="stat-label">Désactivés</div></div>
        </div>
      </div>
    </div>

    <!-- Répartition par type (mini cards) -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:20px">
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-car-wrench" style="color:#8B5CF6;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">48</div><div style="font-size:11px;color:var(--text-muted)">Garage général</div></div>
      </div>
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-oil-can" style="color:#10B981;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">24</div><div style="font-size:11px;color:var(--text-muted)">Centre vidange</div></div>
      </div>
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-car-wash" style="color:#3B82F6;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">31</div><div style="font-size:11px;color:var(--text-muted)">Lavage auto</div></div>
      </div>
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-tire" style="color:#F59E0B;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">18</div><div style="font-size:11px;color:var(--text-muted)">Pneus</div></div>
      </div>
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-bolt" style="color:#EF4444;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">22</div><div style="font-size:11px;color:var(--text-muted)">Électricité auto</div></div>
      </div>
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-truck-ramp-box" style="color:#6366F1;font-size:18px;width:22px;text-align:center"></i>
        <div><div class="fw-700" style="font-size:15px">13</div><div style="font-size:11px;color:var(--text-muted)">Dépannage</div></div>
      </div>
    </div>

    <!-- Table principale -->
    <div class="card">
      <div class="filter-bar">
        <div style="position:relative;flex:1;min-width:200px">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
          <input type="text" placeholder="Nom, ville, type..." id="garageSearch"
            style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)"
            oninput="filterTable(this.value)">
        </div>
        <select class="form-select" style="width:160px" onchange="filterByType(this.value)">
          <option value="">Tous les types</option>
          <option value="garage_general">Garage général</option>
          <option value="centre_vidange">Centre vidange</option>
          <option value="lavage_auto">Lavage auto</option>
          <option value="pneus">Pneus</option>
          <option value="batterie">Batterie</option>
          <option value="climatisation">Climatisation</option>
          <option value="electricite_auto">Électricité auto</option>
          <option value="depannage">Dépannage</option>
          <option value="carrosserie">Carrosserie</option>
        </select>
        <select class="form-select" style="width:140px" onchange="filterByPlan(this.value)">
          <option value="">Tous les plans</option>
          <option value="free">Gratuit</option>
          <option value="pro">Pro</option>
          <option value="premium">Premium</option>
        </select>
        <select class="form-select" style="width:140px">
          <option value="">Toutes les villes</option>
          <option>Abidjan</option><option>Bouaké</option><option>Daloa</option>
          <option>Yamoussoukro</option><option>San-Pédro</option>
        </select>
        <button class="btn btn-secondary btn-sm" onclick="filterVerifiedOnly()">
          <i class="fa-solid fa-shield-check"></i> Vérifiés seulement
        </button>
      </div>

      <div class="table-wrapper">
        <table class="table" id="garagesTable">
          <thead>
            <tr>
              <th><input type="checkbox" onchange="selectAll(this)"></th>
              <th>Garage</th>
              <th>Type</th>
              <th>Ville</th>
              <th>Note</th>
              <th>Avis</th>
              <th>Plan</th>
              <th>Statut</th>
              <th>Vues</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:38px;height:38px;background:#EDE9FE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:16px"></i>
                  </div>
                  <div>
                    <div class="fw-600">Garage Auto Plus Cocody</div>
                    <div style="font-size:11px;color:var(--text-muted)">Rue des Jardins, Cocody</div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-purple">Garage général</span></td>
              <td>Abidjan</td>
              <td>
                <div style="display:flex;align-items:center;gap:4px">
                  <i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i>
                  <span class="fw-600">4.6</span>
                </div>
              </td>
              <td>32</td>
              <td><span class="badge badge-purple"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span></td>
              <td>
                <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span>
                <i class="fa-solid fa-shield-check" style="color:var(--success);margin-left:5px;font-size:13px" title="Vérifié"></i>
              </td>
              <td>3 247</td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="viewGarage(1)"><i class="fa-solid fa-eye"></i> Voir détail</a>
                    <a class="dropdown-item" href="#" onclick="editGarage(1)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#" onclick="manageServices(1)"><i class="fa-solid fa-list"></i> Gérer services</a>
                    <a class="dropdown-item" href="#" onclick="unverifyGarage(1)"><i class="fa-solid fa-shield-xmark"></i> Retirer badge</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteGarage(1)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:38px;height:38px;background:#D1FAE5;border-radius:9px;display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid fa-oil-can" style="color:#10B981;font-size:16px"></i>
                  </div>
                  <div>
                    <div class="fw-600">Centre Vidange Express</div>
                    <div style="font-size:11px;color:var(--text-muted)">Bvd VGE, Plateau</div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-success">Centre vidange</span></td>
              <td>Abidjan</td>
              <td><div style="display:flex;align-items:center;gap:4px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><span class="fw-600">4.2</span></div></td>
              <td>18</td>
              <td><span class="badge badge-info"><i class="fa-solid fa-crown" style="font-size:9px"></i> Pro</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td>1 456</td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="viewGarage(2)"><i class="fa-solid fa-eye"></i> Voir détail</a>
                    <a class="dropdown-item" href="#" onclick="editGarage(2)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#"><i class="fa-solid fa-shield-check"></i> Vérifier</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteGarage(2)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:38px;height:38px;background:#DBEAFE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid fa-car-wash" style="color:#3B82F6;font-size:16px"></i>
                  </div>
                  <div>
                    <div class="fw-600">Flash Lavage Auto</div>
                    <div style="font-size:11px;color:var(--text-muted)">Av. Noé, Treichville</div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-info">Lavage auto</span></td>
              <td>Abidjan</td>
              <td><div style="display:flex;align-items:center;gap:4px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><span class="fw-600">4.7</span></div></td>
              <td>56</td>
              <td><span class="badge badge-gray">Gratuit</span></td>
              <td>
                <span class="badge badge-danger"><i class="fa-solid fa-circle" style="font-size:7px"></i> Désactivé</span>
              </td>
              <td>987</td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="viewGarage(3)"><i class="fa-solid fa-eye"></i> Voir détail</a>
                    <a class="dropdown-item" href="#"><i class="fa-solid fa-rotate-right"></i> Réactiver</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteGarage(3)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:38px;height:38px;background:#FEE2E2;border-radius:9px;display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid fa-truck-ramp-box" style="color:#EF4444;font-size:16px"></i>
                  </div>
                  <div>
                    <div class="fw-600">Top Dépannage 24h</div>
                    <div style="font-size:11px;color:var(--text-muted)">Yopougon Selmer</div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-danger">Dépannage</span></td>
              <td>Abidjan</td>
              <td><div style="display:flex;align-items:center;gap:4px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><span class="fw-600">4.8</span></div></td>
              <td>44</td>
              <td><span class="badge badge-purple"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span></td>
              <td>
                <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span>
                <i class="fa-solid fa-shield-check" style="color:var(--success);margin-left:5px;font-size:13px" title="Vérifié"></i>
              </td>
              <td>5 612</td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="viewGarage(4)"><i class="fa-solid fa-eye"></i> Voir détail</a>
                    <a class="dropdown-item" href="#" onclick="editGarage(4)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#"><i class="fa-solid fa-shield-xmark"></i> Retirer badge</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteGarage(4)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:38px;height:38px;background:#FEF3C7;border-radius:9px;display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid fa-tire" style="color:#F59E0B;font-size:16px"></i>
                  </div>
                  <div>
                    <div class="fw-600">Pro Pneus Marcory</div>
                    <div style="font-size:11px;color:var(--text-muted)">Av. Houphouët, Marcory</div>
                  </div>
                </div>
              </td>
              <td><span class="badge badge-warning">Pneus</span></td>
              <td>Abidjan</td>
              <td><div style="display:flex;align-items:center;gap:4px"><i class="fa-solid fa-star" style="color:var(--warning);font-size:12px"></i><span class="fw-600">4.1</span></div></td>
              <td>27</td>
              <td><span class="badge badge-info"><i class="fa-solid fa-crown" style="font-size:9px"></i> Pro</span></td>
              <td>
                <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span>
              </td>
              <td>2 034</td>
              <td>
                <div class="dropdown">
                  <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="viewGarage(5)"><i class="fa-solid fa-eye"></i> Voir détail</a>
                    <a class="dropdown-item" href="#" onclick="editGarage(5)"><i class="fa-solid fa-pen"></i> Modifier</a>
                    <a class="dropdown-item" href="#"><i class="fa-solid fa-shield-check"></i> Vérifier</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteGarage(5)"><i class="fa-solid fa-trash"></i> Supprimer</a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Affichage 1–20 sur 156 garages</span>
        <div class="pagination">
          <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
          <div class="page-item active">1</div>
          <div class="page-item">2</div>
          <div class="page-item">3</div>
          <div class="page-item">4</div>
          <div class="page-item">5</div>
          <div class="page-item">...</div>
          <div class="page-item">8</div>
          <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ══ Modal Ajouter Garage ══ -->
<div class="modal-overlay" id="modalAddGarage">
  <div class="modal-box" style="max-width:620px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-wrench" style="color:#8B5CF6"></i> Ajouter un garage</h5>
      <button class="modal-close" data-modal-close="modalAddGarage">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div style="grid-column:span 2">
          <label class="form-label">Nom du garage *</label>
          <input type="text" class="form-control" placeholder="Ex: Garage Auto Plus Cocody">
        </div>
        <div>
          <label class="form-label">Type *</label>
          <select class="form-select">
            <option value="">Sélectionner</option>
            <option>Garage général</option>
            <option>Centre vidange</option>
            <option>Lavage auto</option>
            <option>Pneus</option>
            <option>Batterie</option>
            <option>Climatisation</option>
            <option>Électricité auto</option>
            <option>Dépannage</option>
            <option>Carrosserie</option>
          </select>
        </div>
        <div>
          <label class="form-label">Ville *</label>
          <select class="form-select">
            <option>Abidjan</option><option>Bouaké</option><option>Daloa</option>
            <option>Yamoussoukro</option><option>San-Pédro</option><option>Korhogo</option>
          </select>
        </div>
        <div style="grid-column:span 2">
          <label class="form-label">Adresse complète *</label>
          <input type="text" class="form-control" placeholder="Ex: Rue des Jardins, Cocody, Abidjan">
        </div>
        <div>
          <label class="form-label">Latitude</label>
          <input type="number" class="form-control" placeholder="5.3544" step="0.0001">
        </div>
        <div>
          <label class="form-label">Longitude</label>
          <input type="number" class="form-control" placeholder="-4.0082" step="0.0001">
        </div>
        <div>
          <label class="form-label">Téléphone</label>
          <input type="text" class="form-control" placeholder="+225 07 ...">
        </div>
        <div>
          <label class="form-label">WhatsApp</label>
          <input type="text" class="form-control" placeholder="+225 07 ...">
        </div>
        <div>
          <label class="form-label">Ouverture</label>
          <input type="time" class="form-control" value="07:00">
        </div>
        <div>
          <label class="form-label">Fermeture</label>
          <input type="time" class="form-control" value="20:00">
        </div>
        <div>
          <label class="form-label">Plan</label>
          <select class="form-select">
            <option value="free">Gratuit</option>
            <option value="pro">Pro</option>
            <option value="premium">Premium</option>
          </select>
        </div>
        <div style="display:flex;align-items:center;gap:10px;padding-top:22px">
          <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
          <span style="font-size:13px;font-weight:600">Ouvert 24h/24</span>
        </div>
        <div style="grid-column:span 2">
          <label class="form-label">Description</label>
          <textarea class="form-control" rows="2" placeholder="Description du garage..."></textarea>
        </div>
        <div style="grid-column:span 2">
          <label class="form-label">Services proposés</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Vidange</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Freins</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Pneus</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Batterie</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Climatisation</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Diagnostic</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Carrosserie</label>
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer"><input type="checkbox"> Remorquage</label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalAddGarage">Annuler</button>
      <button class="btn btn-primary" onclick="saveGarage()"><i class="fa-solid fa-check"></i> Enregistrer</button>
    </div>
  </div>
</div>

<!-- ══ Modal Voir Garage ══ -->
<div class="modal-overlay" id="modalViewGarage">
  <div class="modal-box" style="max-width:640px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-wrench" style="color:#8B5CF6"></i> Détail du garage</h5>
      <button class="modal-close" data-modal-close="modalViewGarage">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <div style="width:56px;height:56px;background:#EDE9FE;border-radius:14px;display:flex;align-items:center;justify-content:center">
          <i class="fa-solid fa-wrench" style="color:#8B5CF6;font-size:24px"></i>
        </div>
        <div>
          <div style="font-size:18px;font-weight:700">Garage Auto Plus Cocody</div>
          <div style="color:var(--text-muted);font-size:13px">Rue des Jardins, Cocody • Abidjan</div>
          <div style="margin-top:6px;display:flex;gap:8px;flex-wrap:wrap">
            <span class="badge badge-purple">Garage général</span>
            <span class="badge badge-purple"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span>
            <span class="badge badge-success">Actif</span>
            <span class="badge badge-success"><i class="fa-solid fa-shield-check" style="font-size:9px"></i> Vérifié</span>
          </div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px">
          <div style="font-size:11px;color:var(--text-muted)">Note moyenne</div>
          <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
            <i class="fa-solid fa-star" style="color:var(--warning)"></i>
            <span class="fw-700" style="font-size:20px">4.6</span>
            <span style="font-size:13px;color:var(--text-muted)">(32 avis)</span>
          </div>
        </div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px">
          <div style="font-size:11px;color:var(--text-muted)">Vues totales</div>
          <div class="fw-700" style="font-size:20px;margin-top:4px">3 247</div>
        </div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px">
          <div style="font-size:11px;color:var(--text-muted)">Téléphone</div>
          <div class="fw-600" style="font-size:13px;margin-top:4px">+225 07 12 34 56</div>
        </div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px">
          <div style="font-size:11px;color:var(--text-muted)">Horaires</div>
          <div class="fw-600" style="font-size:13px;margin-top:4px">07:00 – 20:00</div>
        </div>
      </div>
      <div style="margin-bottom:16px">
        <div class="fw-600" style="margin-bottom:8px;font-size:13px">Services proposés</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          <span class="badge badge-gray">Vidange</span>
          <span class="badge badge-gray">Freins</span>
          <span class="badge badge-gray">Pneus</span>
          <span class="badge badge-gray">Batterie</span>
          <span class="badge badge-gray">Diagnostic électronique</span>
          <span class="badge badge-gray">Carrosserie</span>
        </div>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" style="flex:1" onclick="editGarage(1);closeModal('modalViewGarage')">
          <i class="fa-solid fa-pen"></i> Modifier
        </button>
        <button class="btn btn-success" style="flex:1" onclick="verifyGarage(1)">
          <i class="fa-solid fa-shield-check"></i> Vérifier
        </button>
        <button class="btn btn-danger" style="flex:1" onclick="confirmAction('Désactiver ce garage ?',()=>showToast('Garage désactivé','warning'))">
          <i class="fa-solid fa-ban"></i> Désactiver
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ Modal Services ══ -->
<div class="modal-overlay" id="modalServices">
  <div class="modal-box" style="max-width:480px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-list" style="color:var(--info)"></i> Gérer les services</h5>
      <button class="modal-close" data-modal-close="modalServices">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px">Garage Auto Plus Cocody</p>
      <div style="display:flex;flex-direction:column;gap:10px">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
          <span class="fw-600" style="font-size:13px">Vidange</span>
          <div style="display:flex;align-items:center;gap:10px">
            <input type="text" class="form-control" value="15 000 – 25 000 FCFA" style="width:180px;font-size:12px;padding:5px 9px">
            <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
          </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
          <span class="fw-600" style="font-size:13px">Freins</span>
          <div style="display:flex;align-items:center;gap:10px">
            <input type="text" class="form-control" value="20 000 – 45 000 FCFA" style="width:180px;font-size:12px;padding:5px 9px">
            <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
          </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
          <span class="fw-600" style="font-size:13px">Diagnostic</span>
          <div style="display:flex;align-items:center;gap:10px">
            <input type="text" class="form-control" placeholder="Fourchette de prix" style="width:180px;font-size:12px;padding:5px 9px">
            <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalServices">Annuler</button>
      <button class="btn btn-primary" onclick="saveServices()"><i class="fa-solid fa-check"></i> Sauvegarder</button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function filterTable(q) { /* filtre en temps réel */ }
function filterByType(v) { if(v) showToast('Filtre type : '+v,'info'); }
function filterByPlan(v) { if(v) showToast('Filtre plan : '+v,'info'); }
function filterVerifiedOnly() { showToast('Affichage des garages vérifiés uniquement','info'); }
function selectAll(cb) { document.querySelectorAll('#garagesTable tbody input[type=checkbox]').forEach(c=>c.checked=cb.checked); }
function viewGarage(id) { openModal('modalViewGarage'); }
function editGarage(id) { openModal('modalAddGarage'); }
function manageServices(id) { openModal('modalServices'); }
function verifyGarage(id) { showToast('Badge vérifié attribué','success'); closeModal('modalViewGarage'); }
function unverifyGarage(id) { confirmAction('Retirer le badge vérifié ?',()=>showToast('Badge retiré','warning')); }
function deleteGarage(id) { confirmAction('Supprimer ce garage ?',()=>showToast('Garage supprimé','error')); }
function saveGarage() { showToast('Garage enregistré avec succès','success'); closeModal('modalAddGarage'); }
function saveServices() { showToast('Services mis à jour','success'); closeModal('modalServices'); }
function exportGarages() { showToast('Export CSV en cours...','info'); }
</script>
</body>
</html>
