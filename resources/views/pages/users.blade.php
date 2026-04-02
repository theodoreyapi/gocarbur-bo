<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Utilisateurs — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
    <li><a class="nav-link-item active" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs <span class="nav-badge">1 248</span></a></li>
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
    <li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li>
    <li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li>
  </ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav">
    <li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li>
    <li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux</a></li>
    <li><a class="nav-link-item" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li>
  </ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Utilisateurs</div>
    <div class="topbar-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Rechercher..."></div>
    <div class="topbar-actions">
      <div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div>
      <div class="avatar-btn">SA</div>
    </div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
      <div><h1>Utilisateurs</h1><p>Gérez tous les comptes utilisateurs de la plateforme.</p></div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-secondary" onclick="exportUsers()"><i class="fa-solid fa-download"></i> Export CSV</button>
        <button class="btn btn-primary" data-modal-open="modalAddUser"><i class="fa-solid fa-user-plus"></i> Ajouter</button>
      </div>
    </div>

    <!-- KPI mini -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#FFF0EB;margin:0;width:38px;height:38px"><i class="fa-solid fa-users" style="color:var(--primary);font-size:16px"></i></div>
          <div><div class="stat-value" style="font-size:20px">1 248</div><div class="stat-label">Total</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#D1FAE5;margin:0;width:38px;height:38px"><i class="fa-solid fa-crown" style="color:var(--success);font-size:16px"></i></div>
          <div><div class="stat-value" style="font-size:20px">347</div><div class="stat-label">Premium</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#DBEAFE;margin:0;width:38px;height:38px"><i class="fa-solid fa-user-check" style="color:var(--info);font-size:16px"></i></div>
          <div><div class="stat-value" style="font-size:20px">1 190</div><div class="stat-label">Actifs</div></div>
        </div>
      </div>
      <div class="stat-card" style="padding:16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div class="stat-icon" style="background:#FEE2E2;margin:0;width:38px;height:38px"><i class="fa-solid fa-user-slash" style="color:var(--danger);font-size:16px"></i></div>
          <div><div class="stat-value" style="font-size:20px">58</div><div class="stat-label">Suspendus</div></div>
        </div>
      </div>
    </div>

    <div class="card">
      <!-- Filtres -->
      <div class="filter-bar">
        <div class="search-input" style="position:relative;flex:1;min-width:200px">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
          <input type="text" placeholder="Nom, téléphone, ville..." style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)" oninput="filterUsers(this.value)">
        </div>
        <select class="form-select" style="width:150px" onchange="filterByPlan(this.value)">
          <option value="">Tous les plans</option>
          <option value="free">Gratuit</option>
          <option value="premium">Premium</option>
        </select>
        <select class="form-select" style="width:150px" onchange="filterByStatus(this.value)">
          <option value="">Tous les statuts</option>
          <option value="active">Actif</option>
          <option value="suspended">Suspendu</option>
        </select>
        <select class="form-select" style="width:150px">
          <option value="">Toutes les villes</option>
          <option>Abidjan</option>
          <option>Bouaké</option>
          <option>Daloa</option>
          <option>Yamoussoukro</option>
        </select>
      </div>

      <!-- Table -->
      <div class="table-wrapper">
        <table class="table" id="usersTable">
          <thead>
            <tr>
              <th><input type="checkbox" onchange="selectAll(this)"></th>
              <th>Utilisateur</th>
              <th>Téléphone</th>
              <th>Ville</th>
              <th>Véhicules</th>
              <th>Plan</th>
              <th>Statut</th>
              <th>Dernière connexion</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="user-avatar">KA</div><div><div class="fw-600">Kouassi Aya</div><div style="font-size:11px;color:var(--text-muted)">#1248</div></div></div></td>
              <td>+225 07 12 34 56</td><td>Abidjan</td><td>2</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td>il y a 5 min</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#" onclick="viewUser(1248)"><i class="fa-solid fa-eye"></i> Voir</a><a class="dropdown-item" href="#" onclick="grantPremium(1248)"><i class="fa-solid fa-crown"></i> Accorder Premium</a><a class="dropdown-item" href="#" onclick="suspendUser(1248)"><i class="fa-solid fa-ban"></i> Suspendre</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#" onclick="deleteUser(1248)"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="user-avatar" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8)">BK</div><div><div class="fw-600">Bamba Koné</div><div style="font-size:11px;color:var(--text-muted)">#1247</div></div></div></td>
              <td>+225 05 98 76 54</td><td>Bouaké</td><td>1</td>
              <td><span class="badge badge-gray">Gratuit</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td>il y a 2h</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Voir</a><a class="dropdown-item" href="#"><i class="fa-solid fa-crown"></i> Accorder Premium</a><a class="dropdown-item" href="#"><i class="fa-solid fa-ban"></i> Suspendre</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="user-avatar" style="background:linear-gradient(135deg,#10B981,#059669)">NA</div><div><div class="fw-600">N'Guessan Ahou</div><div style="font-size:11px;color:var(--text-muted)">#1246</div></div></div></td>
              <td>+225 01 23 45 67</td><td>Abidjan</td><td>3</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span></td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actif</span></td>
              <td>il y a 1j</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Voir</a><a class="dropdown-item" href="#"><i class="fa-solid fa-crown"></i> Accorder Premium</a><a class="dropdown-item" href="#"><i class="fa-solid fa-ban"></i> Suspendre</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
            <tr>
              <td><input type="checkbox"></td>
              <td><div style="display:flex;align-items:center;gap:10px"><div class="user-avatar" style="background:linear-gradient(135deg,#EF4444,#B91C1C)">DT</div><div><div class="fw-600">Diaby Tiémoko</div><div style="font-size:11px;color:var(--text-muted)">#1245</div></div></div></td>
              <td>+225 07 65 43 21</td><td>Daloa</td><td>1</td>
              <td><span class="badge badge-gray">Gratuit</span></td>
              <td><span class="badge badge-danger"><i class="fa-solid fa-circle" style="font-size:7px"></i> Suspendu</span></td>
              <td>il y a 3j</td>
              <td><div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button><div class="dropdown-menu"><a class="dropdown-item" href="#"><i class="fa-solid fa-eye"></i> Voir</a><a class="dropdown-item" href="#"><i class="fa-solid fa-rotate-left"></i> Réactiver</a><div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-trash"></i> Supprimer</a></div></div></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Affichage 1–25 sur 1 248 utilisateurs</span>
        <div class="pagination">
          <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
          <div class="page-item active">1</div>
          <div class="page-item">2</div>
          <div class="page-item">3</div>
          <div class="page-item">...</div>
          <div class="page-item">50</div>
          <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Ajouter utilisateur -->
<div class="modal-overlay" id="modalAddUser">
  <div class="modal-box">
    <div class="modal-header">
      <h5><i class="fa-solid fa-user-plus" style="color:var(--primary)"></i> Ajouter un utilisateur</h5>
      <button class="modal-close" data-modal-close="modalAddUser">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div><label class="form-label">Nom complet *</label><input type="text" class="form-control" placeholder="Ex: Kouassi Aya"></div>
        <div><label class="form-label">Téléphone *</label><input type="text" class="form-control" placeholder="+225 07 ..."></div>
        <div><label class="form-label">Ville</label><select class="form-select"><option>Abidjan</option><option>Bouaké</option><option>Daloa</option></select></div>
        <div><label class="form-label">Plan</label><select class="form-select"><option value="free">Gratuit</option><option value="premium">Premium</option></select></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalAddUser">Annuler</button>
      <button class="btn btn-primary" onclick="saveUser()"><i class="fa-solid fa-check"></i> Enregistrer</button>
    </div>
  </div>
</div>

<!-- Modal Voir utilisateur -->
<div class="modal-overlay" id="modalViewUser">
  <div class="modal-box" style="max-width:640px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-user" style="color:var(--primary)"></i> Profil utilisateur</h5>
      <button class="modal-close" data-modal-close="modalViewUser">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <div class="user-avatar" style="width:56px;height:56px;font-size:20px">KA</div>
        <div>
          <div style="font-size:18px;font-weight:700">Kouassi Aya</div>
          <div style="color:var(--text-muted);font-size:13px">+225 07 12 34 56 • Abidjan</div>
          <div style="margin-top:6px;display:flex;gap:8px"><span class="badge badge-success"><i class="fa-solid fa-crown" style="font-size:9px"></i> Premium</span><span class="badge badge-success">Actif</span></div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px"><div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Véhicules</div><div class="fw-700" style="font-size:18px">2</div></div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px"><div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Documents</div><div class="fw-700" style="font-size:18px">6</div></div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px"><div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Premium expire</div><div class="fw-700" style="font-size:14px">14 Avr 2024</div></div>
        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px"><div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Inscrit le</div><div class="fw-700" style="font-size:14px">18 Mar 2024</div></div>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-success w-100" onclick="grantPremium(1248)"><i class="fa-solid fa-crown"></i> Accorder Premium</button>
        <button class="btn btn-danger w-100" onclick="suspendUser(1248)"><i class="fa-solid fa-ban"></i> Suspendre</button>
      </div>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function filterUsers(q) { console.log('search:', q); }
function filterByPlan(v) { showToast('Filtre plan: ' + (v || 'tous'), 'info'); }
function filterByStatus(v) { showToast('Filtre statut: ' + (v || 'tous'), 'info'); }
function selectAll(cb) { document.querySelectorAll('#usersTable tbody input[type=checkbox]').forEach(c => c.checked = cb.checked); }
function viewUser(id) { openModal('modalViewUser'); }
function grantPremium(id) { showToast('Premium accordé à l\'utilisateur #' + id, 'success'); closeModal('modalViewUser'); }
function suspendUser(id) { confirmAction('Suspendre cet utilisateur ?', () => showToast('Utilisateur suspendu', 'warning')); }
function deleteUser(id) { confirmAction('Supprimer définitivement cet utilisateur ?', () => showToast('Utilisateur supprimé', 'error')); }
function saveUser() { showToast('Utilisateur créé avec succès', 'success'); closeModal('modalAddUser'); }
function exportUsers() { showToast('Export CSV en cours...', 'info'); }
</script>
</body>
</html>
