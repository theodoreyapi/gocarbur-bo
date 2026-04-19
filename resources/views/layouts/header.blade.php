<!-- Topbar -->
<header class="topbar">
    <button class="btn-icon d-lg-none" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    <div class="topbar-title">{{ $subTitle }}</div>
    <div class="topbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Rechercher...">
    </div>
    <div class="topbar-actions">
        <div class="btn-icon" title="Actualiser" onclick="loadDashboard()"><i class="fa-solid fa-rotate-right"></i>
        </div>
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
