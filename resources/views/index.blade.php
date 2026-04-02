<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — AutoPlatform Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>

    <!-- ══════════════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <a class="sidebar-logo" href="index.html">
            <div class="sidebar-logo-icon">⛽</div>
            <div class="sidebar-logo-text">AutoPlatform <span>Back-Office Admin</span></div>
        </a>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Général</div>
            <ul class="sidebar-nav">
                <li><a class="nav-link-item active" href="index.html" data-page="dashboard"><i
                            class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
                <li><a class="nav-link-item" href="pages/users.html" data-page="users"><i class="fa-solid fa-users"></i>
                        Utilisateurs <span class="nav-badge">1 248</span></a></li>
                <li><a class="nav-link-item" href="pages/notifications.html" data-page="notifications"><i
                            class="fa-solid fa-bell"></i> Notifications <span class="nav-badge">5</span></a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Partenaires</div>
            <ul class="sidebar-nav">
                <li><a class="nav-link-item" href="pages/stations.html" data-page="stations"><i
                            class="fa-solid fa-gas-pump"></i> Stations-service</a></li>
                <li><a class="nav-link-item" href="pages/garages.html" data-page="garages"><i
                            class="fa-solid fa-wrench"></i> Garages & Services</a></li>
                <li><a class="nav-link-item" href="pages/partner-requests.html" data-page="partner-requests"><i
                            class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge"
                            style="background:#F59E0B">8</span></a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Contenu</div>
            <ul class="sidebar-nav">
                <li><a class="nav-link-item" href="pages/articles.html" data-page="articles"><i
                            class="fa-solid fa-newspaper"></i> Articles & Conseils</a></li>
                <li><a class="nav-link-item" href="pages/promotions.html" data-page="promotions"><i
                            class="fa-solid fa-tag"></i> Promotions</a></li>
                <li><a class="nav-link-item" href="pages/banners.html" data-page="banners"><i
                            class="fa-solid fa-rectangle-ad"></i> Bannières pub</a></li>
                <li><a class="nav-link-item" href="pages/reviews.html" data-page="reviews"><i
                            class="fa-solid fa-star"></i> Avis clients <span class="nav-badge"
                            style="background:#F59E0B">12</span></a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Finances</div>
            <ul class="sidebar-nav">
                <li><a class="nav-link-item" href="pages/subscriptions.html" data-page="subscriptions"><i
                            class="fa-solid fa-crown"></i> Abonnements</a></li>
                <li><a class="nav-link-item" href="pages/payments.html" data-page="payments"><i
                            class="fa-solid fa-credit-card"></i> Paiements</a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Système</div>
            <ul class="sidebar-nav">
                <li><a class="nav-link-item" href="pages/settings.html" data-page="settings"><i
                            class="fa-solid fa-sliders"></i> Paramètres</a></li>
                <li><a class="nav-link-item" href="pages/activity-logs.html" data-page="logs"><i
                            class="fa-solid fa-list-check"></i> Journaux d'activité</a></li>
                <li><a class="nav-link-item" href="pages/app-versions.html" data-page="versions"><i
                            class="fa-solid fa-mobile-screen"></i> Versions app</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">Super Admin</div>
                    <div class="sidebar-user-role">admin@autoplatform.ci</div>
                </div>
                <i class="fa-solid fa-ellipsis-vertical" style="color:rgba(255,255,255,.3);font-size:13px"></i>
            </div>
        </div>
    </aside>

    <!-- ══════════════════════════════════════════════════════════
     MAIN
══════════════════════════════════════════════════════════ -->
    <div class="main-wrapper">

        <!-- Topbar -->
        <header class="topbar">
            <button class="btn-icon d-lg-none" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-title">Tableau de bord</div>
            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Rechercher...">
            </div>
            <div class="topbar-actions">
                <div class="btn-icon" title="Actualiser" onclick="loadDashboard()"><i
                        class="fa-solid fa-rotate-right"></i></div>
                <div class="btn-icon" title="Notifications" style="position:relative">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notif-dot"></span>
                </div>
                <div class="dropdown">
                    <div class="avatar-btn" data-toggle="dropdown">SA</div>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="fa-solid fa-user"></i> Mon profil</a>
                        <a class="dropdown-item" href="pages/settings.html"><i class="fa-solid fa-sliders"></i>
                            Paramètres</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="pages/login.html"><i
                                class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="page-content">

            <!-- Page Header -->
            <div class="page-header"
                style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div>
                    <h1>Tableau de bord</h1>
                    <p>Bienvenue, Super Admin. Voici un aperçu de votre plateforme.</p>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <select class="form-select" style="width:auto;padding:8px 12px"
                        onchange="changePeriod(this.value)">
                        <option value="7">7 derniers jours</option>
                        <option value="30" selected>30 derniers jours</option>
                        <option value="90">3 derniers mois</option>
                        <option value="365">Cette année</option>
                    </select>
                    <button class="btn btn-primary" onclick="exportReport()"><i class="fa-solid fa-download"></i>
                        Exporter</button>
                </div>
            </div>

            <!-- ── KPI Cards ── -->
            <div
                style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:24px">

                <div class="stat-card">
                    <div class="stat-icon" style="background:#FFF0EB"><i class="fa-solid fa-users"
                            style="color:var(--primary)"></i></div>
                    <div class="stat-value" id="kpi-users">1 248</div>
                    <div class="stat-label">Utilisateurs actifs</div>
                    <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +12% ce mois</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#D1FAE5"><i class="fa-solid fa-crown"
                            style="color:var(--success)"></i></div>
                    <div class="stat-value" id="kpi-premium">347</div>
                    <div class="stat-label">Abonnés Premium</div>
                    <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +8% ce mois</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#DBEAFE"><i class="fa-solid fa-gas-pump"
                            style="color:var(--info)"></i></div>
                    <div class="stat-value" id="kpi-stations">84</div>
                    <div class="stat-label">Stations actives</div>
                    <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +3 ce mois</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#EDE9FE"><i class="fa-solid fa-wrench"
                            style="color:var(--purple)"></i></div>
                    <div class="stat-value" id="kpi-garages">156</div>
                    <div class="stat-label">Garages actifs</div>
                    <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +7 ce mois</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#FEF3C7"><i class="fa-solid fa-sack-dollar"
                            style="color:var(--warning)"></i></div>
                    <div class="stat-value" id="kpi-revenue">4 230 000</div>
                    <div class="stat-label">Revenus FCFA ce mois</div>
                    <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +18% vs mois dernier</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#FEE2E2"><i class="fa-solid fa-clock"
                            style="color:var(--danger)"></i></div>
                    <div class="stat-value" id="kpi-pending">8</div>
                    <div class="stat-label">Demandes en attente</div>
                    <div class="stat-change down"><i class="fa-solid fa-arrow-down"></i> -2 depuis hier</div>
                </div>

            </div>

            <!-- ── Charts Row ── -->
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">

                <!-- Revenus mensuels -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-chart-line" style="color:var(--primary)"></i>
                            Revenus mensuels (FCFA)</div>
                        <div style="display:flex;gap:8px">
                            <span
                                style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-muted)"><span
                                    style="width:10px;height:10px;background:var(--primary);border-radius:2px;display:inline-block"></span>Revenus</span>
                            <span
                                style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-muted)"><span
                                    style="width:10px;height:10px;background:var(--info);border-radius:2px;display:inline-block"></span>Abonnements</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container"><canvas id="revenueChart"></canvas></div>
                    </div>
                </div>

                <!-- Répartition abonnements -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--purple)"></i>
                            Répartition plans</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height:200px"><canvas id="plansChart"></canvas></div>
                        <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                                <span style="display:flex;align-items:center;gap:6px"><span
                                        style="width:10px;height:10px;background:#FF6B35;border-radius:50%;display:inline-block"></span>User
                                    Premium</span>
                                <strong>241 (69%)</strong>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                                <span style="display:flex;align-items:center;gap:6px"><span
                                        style="width:10px;height:10px;background:#3B82F6;border-radius:50%;display:inline-block"></span>Station
                                    Pro</span>
                                <strong>68 (20%)</strong>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
                                <span style="display:flex;align-items:center;gap:6px"><span
                                        style="width:10px;height:10px;background:#8B5CF6;border-radius:50%;display:inline-block"></span>Garage
                                    Pro</span>
                                <strong>38 (11%)</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Croissance utilisateurs ── -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-user-plus" style="color:var(--success)"></i>
                            Nouveaux utilisateurs / jour</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container"><canvas id="growthChart"></canvas></div>
                    </div>
                </div>

                <!-- Activité par ville -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-map-location-dot"
                                style="color:var(--info)"></i> Activité par ville</div>
                    </div>
                    <div class="card-body">
                        <div style="display:flex;flex-direction:column;gap:14px">
                            <div>
                                <div
                                    style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>Abidjan</span><strong>68%</strong></div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:68%;background:var(--primary)"></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>Bouaké</span><strong>12%</strong></div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:12%;background:var(--info)"></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>Daloa</span><strong>7%</strong></div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:7%;background:var(--success)"></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>Yamoussoukro</span><strong>5%</strong></div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:5%;background:var(--warning)"></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
                                    <span>Autres</span><strong>8%</strong></div>
                                <div class="progress">
                                    <div class="progress-bar" style="width:8%;background:var(--purple)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Derniers utilisateurs + Demandes ── -->
            <div style="display:grid;grid-template-columns:3fr 2fr;gap:20px;margin-bottom:24px">

                <!-- Derniers inscrits -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-users" style="color:var(--primary)"></i>
                            Dernières inscriptions</div>
                        <a href="pages/users.html" class="btn btn-sm btn-secondary">Voir tout</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Téléphone</th>
                                    <th>Ville</th>
                                    <th>Plan</th>
                                    <th>Inscrit le</th>
                                </tr>
                            </thead>
                            <tbody id="recent-users">
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar">KA</div><span class="fw-600">Kouassi Aya</span>
                                        </div>
                                    </td>
                                    <td>+225 07 12 34 56</td>
                                    <td>Abidjan</td>
                                    <td><span class="badge badge-success">Premium</span></td>
                                    <td>18 mars 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar"
                                                style="background:linear-gradient(135deg,#3B82F6,#1D4ED8)">BK</div>
                                            <span class="fw-600">Bamba Koné</span>
                                        </div>
                                    </td>
                                    <td>+225 05 98 76 54</td>
                                    <td>Bouaké</td>
                                    <td><span class="badge badge-gray">Gratuit</span></td>
                                    <td>17 mars 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar"
                                                style="background:linear-gradient(135deg,#10B981,#059669)">NA</div>
                                            <span class="fw-600">N'Guessan Ahou</span>
                                        </div>
                                    </td>
                                    <td>+225 01 23 45 67</td>
                                    <td>Abidjan</td>
                                    <td><span class="badge badge-success">Premium</span></td>
                                    <td>16 mars 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar"
                                                style="background:linear-gradient(135deg,#8B5CF6,#6D28D9)">DT</div>
                                            <span class="fw-600">Diaby Tiémoko</span>
                                        </div>
                                    </td>
                                    <td>+225 07 65 43 21</td>
                                    <td>Daloa</td>
                                    <td><span class="badge badge-gray">Gratuit</span></td>
                                    <td>15 mars 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div class="user-avatar"
                                                style="background:linear-gradient(135deg,#F59E0B,#D97706)">TO</div>
                                            <span class="fw-600">Touré Oumar</span>
                                        </div>
                                    </td>
                                    <td>+225 05 11 22 33</td>
                                    <td>San-Pédro</td>
                                    <td><span class="badge badge-success">Premium</span></td>
                                    <td>14 mars 2024</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Demandes partenaires -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-handshake" style="color:var(--warning)"></i>
                            Demandes en attente</div>
                        <a href="pages/partner-requests.html" class="btn btn-sm btn-secondary">Voir tout</a>
                    </div>
                    <div class="card-body" style="padding:0">
                        <div style="display:flex;flex-direction:column">
                            <div
                                style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                                <div
                                    style="width:38px;height:38px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                    <i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i></div>
                                <div style="flex:1">
                                    <div class="fw-600" style="font-size:13.5px">Total Marcory</div>
                                    <div style="font-size:12px;color:var(--text-muted)">Station • Abidjan</div>
                                </div>
                                <div style="display:flex;gap:5px">
                                    <button class="btn btn-sm btn-success"
                                        onclick="showToast('Demande approuvée','success')"><i
                                            class="fa-solid fa-check"></i></button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="showToast('Demande rejetée','error')"><i
                                            class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                            <div
                                style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                                <div
                                    style="width:38px;height:38px;background:#DBEAFE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                    <i class="fa-solid fa-wrench" style="color:var(--info)"></i></div>
                                <div style="flex:1">
                                    <div class="fw-600" style="font-size:13.5px">Garage Auto Plus</div>
                                    <div style="font-size:12px;color:var(--text-muted)">Garage • Cocody</div>
                                </div>
                                <div style="display:flex;gap:5px">
                                    <button class="btn btn-sm btn-success"
                                        onclick="showToast('Demande approuvée','success')"><i
                                            class="fa-solid fa-check"></i></button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="showToast('Demande rejetée','error')"><i
                                            class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                            <div
                                style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
                                <div
                                    style="width:38px;height:38px;background:#FFF0EB;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                    <i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i></div>
                                <div style="flex:1">
                                    <div class="fw-600" style="font-size:13.5px">Petro Ivoire Yop.</div>
                                    <div style="font-size:12px;color:var(--text-muted)">Station • Yopougon</div>
                                </div>
                                <div style="display:flex;gap:5px">
                                    <button class="btn btn-sm btn-success"
                                        onclick="showToast('Demande approuvée','success')"><i
                                            class="fa-solid fa-check"></i></button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="showToast('Demande rejetée','error')"><i
                                            class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                            <div style="padding:14px 20px;display:flex;align-items:center;gap:12px">
                                <div
                                    style="width:38px;height:38px;background:#EDE9FE;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                    <i class="fa-solid fa-wrench" style="color:var(--purple)"></i></div>
                                <div style="flex:1">
                                    <div class="fw-600" style="font-size:13.5px">Centre Vidange Exp.</div>
                                    <div style="font-size:12px;color:var(--text-muted)">Garage • Plateau</div>
                                </div>
                                <div style="display:flex;gap:5px">
                                    <button class="btn btn-sm btn-success"
                                        onclick="showToast('Demande approuvée','success')"><i
                                            class="fa-solid fa-check"></i></button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="showToast('Demande rejetée','error')"><i
                                            class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Activité récente ── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--info)"></i>
                        Activité récente</div>
                    <a href="pages/activity-logs.html" class="btn btn-sm btn-secondary">Journal complet</a>
                </div>
                <div class="card-body" style="padding:0">
                    <div id="activity-feed" style="display:flex;flex-direction:column">
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                            <div
                                style="width:36px;height:36px;background:#D1FAE5;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fa-solid fa-user-plus" style="color:var(--success)"></i></div>
                            <div style="flex:1"><strong>Nouvel utilisateur inscrit</strong> — Kouassi Aya (+225 07 12
                                34 56)</div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 5 min</span>
                        </div>
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                            <div
                                style="width:36px;height:36px;background:#FEF3C7;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fa-solid fa-crown" style="color:var(--warning)"></i></div>
                            <div style="flex:1"><strong>Abonnement Premium activé</strong> — N'Guessan Ahou — 1 500
                                FCFA via Orange Money</div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 12
                                min</span>
                        </div>
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                            <div
                                style="width:36px;height:36px;background:#DBEAFE;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fa-solid fa-gas-pump" style="color:var(--info)"></i></div>
                            <div style="flex:1"><strong>Prix carburant mis à jour</strong> — Total Énergies Cocody —
                                Essence : 695 FCFA/L</div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 28
                                min</span>
                        </div>
                        <div
                            style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
                            <div
                                style="width:36px;height:36px;background:#FEE2E2;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fa-solid fa-shield-check" style="color:var(--danger)"></i></div>
                            <div style="flex:1"><strong>Station vérifiée</strong> — Shell Plateau — Badge vérifié
                                attribué par Super Admin</div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 45
                                min</span>
                        </div>
                        <div style="padding:14px 20px;display:flex;align-items:center;gap:14px">
                            <div
                                style="width:36px;height:36px;background:#EDE9FE;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fa-solid fa-newspaper" style="color:var(--purple)"></i></div>
                            <div style="flex:1"><strong>Article publié</strong> — "5 signes que votre voiture a besoin
                                d'une vidange" — Par Admin</div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">il y a 1h</span>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ── Toast container ── -->
    <div class="toast-container"></div>

    <script src="js/app.js"></script>
    <script>
        // ── Charts ────────────────────────────────────────────────────
        const months = ['Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar'];

        // Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: 'Revenus',
                        data: [2800000, 3100000, 3600000, 3200000, 3900000, 4230000],
                        backgroundColor: 'rgba(255,107,53,.85)',
                        borderRadius: 6
                    },
                    {
                        label: 'Abonnements',
                        data: [1200000, 1400000, 1700000, 1500000, 1800000, 2100000],
                        backgroundColor: 'rgba(59,130,246,.85)',
                        borderRadius: 6
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            callback: v => (v / 1000000).toFixed(1) + 'M',
                            font: {
                                size: 12
                            }
                        }
                    },
                },
            },
        });

        // Plans Pie
        new Chart(document.getElementById('plansChart'), {
            type: 'doughnut',
            data: {
                labels: ['User Premium', 'Station Pro', 'Garage Pro'],
                datasets: [{
                    data: [241, 68, 38],
                    backgroundColor: ['#FF6B35', '#3B82F6', '#8B5CF6'],
                    borderWidth: 0
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
            },
        });

        // Growth Chart
        const days = Array.from({
            length: 30
        }, (_, i) => {
            const d = new Date();
            d.setDate(d.getDate() - (29 - i));
            return d.getDate() + '/' + (d.getMonth() + 1);
        });
        new Chart(document.getElementById('growthChart'), {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Nouveaux users',
                    data: Array.from({
                        length: 30
                    }, () => Math.floor(Math.random() * 40) + 5),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 8,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                },
            },
        });

        function loadDashboard() {
            showToast('Données actualisées', 'success');
        }

        function changePeriod(v) {
            showToast('Période : ' + v + ' jours', 'info');
        }

        function exportReport() {
            showToast('Export en cours...', 'info');
        }
    </script>
</body>

</html>
