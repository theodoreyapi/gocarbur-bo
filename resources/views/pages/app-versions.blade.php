<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Versions app — AutoPlatform Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<aside class="sidebar">
  <a class="sidebar-logo" href="../index.html"><div class="sidebar-logo-icon">⛽</div><div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div></a>
  <div class="sidebar-section"><div class="sidebar-section-label">Général</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="../index.html"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li><li><a class="nav-link-item" href="users.html"><i class="fa-solid fa-users"></i> Utilisateurs</a></li><li><a class="nav-link-item" href="notifications.html"><i class="fa-solid fa-bell"></i> Notifications</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Partenaires</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="stations.html"><i class="fa-solid fa-gas-pump"></i> Stations</a></li><li><a class="nav-link-item" href="garages.html"><i class="fa-solid fa-wrench"></i> Garages</a></li><li><a class="nav-link-item" href="partner-requests.html"><i class="fa-solid fa-handshake"></i> Demandes</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Contenu</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="articles.html"><i class="fa-solid fa-newspaper"></i> Articles</a></li><li><a class="nav-link-item" href="promotions.html"><i class="fa-solid fa-tag"></i> Promotions</a></li><li><a class="nav-link-item" href="banners.html"><i class="fa-solid fa-rectangle-ad"></i> Bannières</a></li><li><a class="nav-link-item" href="reviews.html"><i class="fa-solid fa-star"></i> Avis clients</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Finances</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="subscriptions.html"><i class="fa-solid fa-crown"></i> Abonnements</a></li><li><a class="nav-link-item" href="payments.html"><i class="fa-solid fa-credit-card"></i> Paiements</a></li></ul></div>
  <div class="sidebar-section"><div class="sidebar-section-label">Système</div><ul class="sidebar-nav"><li><a class="nav-link-item" href="settings.html"><i class="fa-solid fa-sliders"></i> Paramètres</a></li><li><a class="nav-link-item" href="activity-logs.html"><i class="fa-solid fa-list-check"></i> Journaux d'activité</a></li><li><a class="nav-link-item active" href="app-versions.html"><i class="fa-solid fa-mobile-screen"></i> Versions app</a></li></ul></div>
  <div class="sidebar-footer"><div class="sidebar-user"><div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div><div class="sidebar-user-info"><div class="sidebar-user-name">Super Admin</div><div class="sidebar-user-role">admin@autoplatform.ci</div></div></div></div>
</aside>

<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-title">Versions de l'application</div>
    <div class="topbar-actions"><div class="btn-icon"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></div><div class="avatar-btn">SA</div></div>
  </header>

  <main class="page-content">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div><h1>Gestion des versions</h1><p>Contrôlez les mises à jour forcées et la compatibilité des versions mobiles.</p></div>
      <button class="btn btn-primary" onclick="openModal('modalAddVersion')"><i class="fa-solid fa-plus"></i> Déclarer une version</button>
    </div>

    <!-- Versions actives -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">

      <!-- Android -->
      <div class="card">
        <div class="card-header">
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#D1FAE5;border-radius:10px;display:flex;align-items:center;justify-content:center">
              <i class="fa-brands fa-android" style="color:#10B981;font-size:22px"></i>
            </div>
            <div><div class="card-title">Android</div><div style="font-size:12px;color:var(--text-muted)">Google Play Store</div></div>
          </div>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version actuelle</div>
              <div class="fw-800" style="font-size:22px;color:var(--success)">1.4.2</div>
              <span class="badge badge-success">Production</span>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version minimum</div>
              <div class="fw-800" style="font-size:22px;color:var(--warning)">1.2.0</div>
              <span class="badge badge-warning">Force update &lt; 1.2.0</span>
            </div>
          </div>

          <div style="margin-bottom:16px">
            <div class="fw-600" style="font-size:13px;margin-bottom:8px">Paramètres de mise à jour</div>
            <div style="display:flex;flex-direction:column;gap:8px">
              <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div><div class="fw-600" style="font-size:13px">Forcer la mise à jour</div><div style="font-size:11px;color:var(--text-muted)">Bloque l'accès si version < min</div></div>
                <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
              </div>
              <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div><div class="fw-600" style="font-size:13px">Mise à jour recommandée</div><div style="font-size:11px;color:var(--text-muted)">Affiche une suggestion de mise à jour</div></div>
                <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
              </div>
            </div>
          </div>

          <div style="margin-bottom:14px">
            <label class="form-label" style="font-size:12px">Lien Play Store</label>
            <input type="url" class="form-control" value="https://play.google.com/store/apps/details?id=ci.autoplatform.app" style="font-size:12px;padding:7px 10px">
          </div>

          <div style="margin-bottom:16px">
            <label class="form-label" style="font-size:12px">Message de mise à jour forcée</label>
            <textarea class="form-control" rows="2" style="font-size:12px;padding:7px 10px">Une nouvelle version d'AutoPlatform est disponible avec des améliorations importantes. Veuillez mettre à jour pour continuer.</textarea>
          </div>

          <button class="btn btn-primary" onclick="saveVersionConfig('android')"><i class="fa-solid fa-check"></i> Sauvegarder (Android)</button>
        </div>
      </div>

      <!-- iOS -->
      <div class="card">
        <div class="card-header">
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#DBEAFE;border-radius:10px;display:flex;align-items:center;justify-content:center">
              <i class="fa-brands fa-apple" style="color:#3B82F6;font-size:22px"></i>
            </div>
            <div><div class="card-title">iOS</div><div style="font-size:12px;color:var(--text-muted)">Apple App Store</div></div>
          </div>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version actuelle</div>
              <div class="fw-800" style="font-size:22px;color:var(--success)">1.4.1</div>
              <span class="badge badge-success">Production</span>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:12px;text-align:center">
              <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Version minimum</div>
              <div class="fw-800" style="font-size:22px;color:var(--warning)">1.2.0</div>
              <span class="badge badge-warning">Force update &lt; 1.2.0</span>
            </div>
          </div>

          <div style="margin-bottom:16px">
            <div class="fw-600" style="font-size:13px;margin-bottom:8px">Paramètres de mise à jour</div>
            <div style="display:flex;flex-direction:column;gap:8px">
              <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div><div class="fw-600" style="font-size:13px">Forcer la mise à jour</div><div style="font-size:11px;color:var(--text-muted)">Bloque l'accès si version < min</div></div>
                <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
              </div>
              <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <div><div class="fw-600" style="font-size:13px">Mise à jour recommandée</div><div style="font-size:11px;color:var(--text-muted)">Affiche une suggestion de mise à jour</div></div>
                <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
              </div>
            </div>
          </div>

          <div style="margin-bottom:14px">
            <label class="form-label" style="font-size:12px">Lien App Store</label>
            <input type="url" class="form-control" value="https://apps.apple.com/ci/app/autoplatform/id123456789" style="font-size:12px;padding:7px 10px">
          </div>

          <div style="margin-bottom:16px">
            <label class="form-label" style="font-size:12px">Message de mise à jour forcée</label>
            <textarea class="form-control" rows="2" style="font-size:12px;padding:7px 10px">Une nouvelle version d'AutoPlatform est disponible avec des améliorations importantes. Veuillez mettre à jour pour continuer.</textarea>
          </div>

          <button class="btn btn-primary" onclick="saveVersionConfig('ios')"><i class="fa-solid fa-check"></i> Sauvegarder (iOS)</button>
        </div>
      </div>
    </div>

    <!-- Historique des versions -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--info)"></i> Historique des versions</div>
        <div style="display:flex;gap:8px">
          <select class="form-select" style="width:130px"><option>Toutes plateformes</option><option>Android</option><option>iOS</option></select>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Version</th>
              <th>Plateforme</th>
              <th>Statut</th>
              <th>Type release</th>
              <th>Nouveautés principales</th>
              <th>Publiée le</th>
              <th>Adoptée par</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr style="background:#F0FDF4">
              <td><span class="fw-800" style="font-size:15px;color:var(--success)">1.4.2</span></td>
              <td><i class="fa-brands fa-android" style="color:#10B981"></i> Android</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actuelle</span></td>
              <td><span class="badge badge-info">Minor</span></td>
              <td style="font-size:12px;max-width:220px">Correctif bug OTP, amélioration carte, nouveau filtre stations</td>
              <td style="font-size:12px">15 Mar 2024</td>
              <td>
                <div style="font-size:12px">78% (971 users)</div>
                <div class="progress" style="margin-top:4px;width:80px"><div class="progress-bar" style="width:78%;background:var(--success)"></div></div>
              </td>
              <td><button class="btn btn-sm btn-secondary" onclick="viewVersion('1.4.2','android')"><i class="fa-solid fa-eye"></i></button></td>
            </tr>
            <tr style="background:#EFF6FF">
              <td><span class="fw-800" style="font-size:15px;color:var(--info)">1.4.1</span></td>
              <td><i class="fa-brands fa-apple" style="color:#3B82F6"></i> iOS</td>
              <td><span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i> Actuelle</span></td>
              <td><span class="badge badge-info">Minor</span></td>
              <td style="font-size:12px;max-width:220px">Correctif affichage prix, optimisation mémoire</td>
              <td style="font-size:12px">10 Mar 2024</td>
              <td>
                <div style="font-size:12px">65% (192 users)</div>
                <div class="progress" style="margin-top:4px;width:80px"><div class="progress-bar" style="width:65%;background:var(--info)"></div></div>
              </td>
              <td><button class="btn btn-sm btn-secondary" onclick="viewVersion('1.4.1','ios')"><i class="fa-solid fa-eye"></i></button></td>
            </tr>
            <tr>
              <td><span class="fw-700" style="font-size:14px;color:var(--text-muted)">1.4.0</span></td>
              <td><i class="fa-brands fa-android" style="color:#10B981"></i> Android</td>
              <td><span class="badge badge-gray">Ancienne</span></td>
              <td><span class="badge badge-warning">Major</span></td>
              <td style="font-size:12px;max-width:220px">Carte interactive, carnet d'entretien, abonnement Premium</td>
              <td style="font-size:12px">01 Feb 2024</td>
              <td>
                <div style="font-size:12px">18% (224 users)</div>
                <div class="progress" style="margin-top:4px;width:80px"><div class="progress-bar" style="width:18%;background:var(--warning)"></div></div>
              </td>
              <td><button class="btn btn-sm btn-secondary" onclick="viewVersion('1.4.0','android')"><i class="fa-solid fa-eye"></i></button></td>
            </tr>
            <tr>
              <td><span class="fw-700" style="font-size:14px;color:var(--text-muted)">1.3.5</span></td>
              <td><i class="fa-brands fa-android" style="color:#10B981"></i> Android + <i class="fa-brands fa-apple" style="color:#3B82F6"></i> iOS</td>
              <td>
                <span class="badge badge-danger" title="Force update activé"><i class="fa-solid fa-circle-exclamation" style="font-size:9px"></i> Force update</span>
              </td>
              <td><span class="badge badge-gray">Patch</span></td>
              <td style="font-size:12px;max-width:220px">Correctif sécurité critique</td>
              <td style="font-size:12px">15 Jan 2024</td>
              <td>
                <div style="font-size:12px">3% (37 users)</div>
                <div class="progress" style="margin-top:4px;width:80px"><div class="progress-bar" style="width:3%;background:var(--danger)"></div></div>
              </td>
              <td><button class="btn btn-sm btn-secondary" onclick="viewVersion('1.3.5','both')"><i class="fa-solid fa-eye"></i></button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="padding:14px 20px;border-top:1px solid var(--border)">
        <div class="alert alert-info" style="margin:0">
          <i class="fa-solid fa-circle-info"></i>
          <span>Les versions &lt; 1.2.0 sont bloquées avec une obligation de mise à jour. 37 utilisateurs (3%) sont concernés.</span>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- Modal Déclarer version -->
<div class="modal-overlay" id="modalAddVersion">
  <div class="modal-box" style="max-width:520px">
    <div class="modal-header">
      <h5><i class="fa-solid fa-mobile-screen" style="color:var(--primary)"></i> Déclarer une nouvelle version</h5>
      <button class="modal-close" data-modal-close="modalAddVersion">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div>
            <label class="form-label">Numéro de version *</label>
            <input type="text" class="form-control" placeholder="Ex: 1.5.0">
          </div>
          <div>
            <label class="form-label">Plateforme *</label>
            <select class="form-select">
              <option>Android</option>
              <option>iOS</option>
              <option>Les deux</option>
            </select>
          </div>
          <div>
            <label class="form-label">Type de release</label>
            <select class="form-select">
              <option value="major">Major — Nouvelles fonctionnalités majeures</option>
              <option value="minor">Minor — Améliorations et corrections</option>
              <option value="patch">Patch — Correctif urgent</option>
            </select>
          </div>
          <div>
            <label class="form-label">Date de publication</label>
            <input type="date" class="form-control">
          </div>
        </div>
        <div>
          <label class="form-label">Notes de version (changelog)</label>
          <textarea class="form-control" rows="4" placeholder="• Nouvelle fonctionnalité X&#10;• Correction du bug Y&#10;• Amélioration des performances Z"></textarea>
        </div>
        <div style="border-top:1px solid var(--border);padding-top:14px">
          <div class="fw-600" style="font-size:13px;margin-bottom:10px">Contrôle des mises à jour</div>
          <div style="display:flex;flex-direction:column;gap:8px">
            <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              <div><div class="fw-600" style="font-size:13px">Mise à jour recommandée</div><div style="font-size:11px;color:var(--text-muted)">Suggérer aux utilisateurs de mettre à jour</div></div>
              <label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label>
            </div>
            <div style="padding:10px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              <div><div class="fw-600" style="font-size:13px">Mise à jour forcée</div><div style="font-size:11px;color:var(--danger)">⚠️ Bloque l'application pour les versions antérieures</div></div>
              <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
            </div>
          </div>
        </div>
        <div>
          <label class="form-label">Version minimum requise (après ce déploiement)</label>
          <input type="text" class="form-control" placeholder="Ex: 1.2.0">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close="modalAddVersion">Annuler</button>
      <button class="btn btn-primary" onclick="saveVersion()"><i class="fa-solid fa-check"></i> Enregistrer la version</button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="../js/app.js"></script>
<script>
function saveVersionConfig(platform){ showToast('Configuration '+platform.toUpperCase()+' sauvegardée','success'); }
function viewVersion(v, platform){ showToast('Détail version '+v+' ('+platform+')','info'); }
function saveVersion(){ showToast('Version enregistrée avec succès','success'); closeModal('modalAddVersion'); }
</script>
</body>
</html>
