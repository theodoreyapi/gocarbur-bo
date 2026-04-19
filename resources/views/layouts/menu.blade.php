<aside class="sidebar" id="sidebar">
    <a class="sidebar-logo" href="{{ url('index') }}">
        <div class="sidebar-logo-icon">⛽</div>
        <div class="sidebar-logo-text">GoCarbu <span>Back-Office Admin</span></div>
    </a>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Général</div>
        <ul class="sidebar-nav">
            <li><a class="nav-link-item {{ Route::is('index.*') ? 'active' : '' }}" href="{{ url('index') }}"
                    data-page="dashboard"><i class="fa-solid fa-gauge-high"></i> Tableau de bord</a></li>
            <li><a class="nav-link-item {{ Route::is('users.*') ? 'active' : '' }}" href="{{ url('users') }}"
                    data-page="users"><i class="fa-solid fa-users"></i>
                    Utilisateurs <span class="nav-badge">1 248</span></a></li>
            <li><a class="nav-link-item {{ Route::is('notifications.*') ? 'active' : '' }}" href="{{ url('notifications') }}" data-page="notifications"><i
                        class="fa-solid fa-bell"></i> Notifications <span class="nav-badge">5</span></a></li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Partenaires</div>
        <ul class="sidebar-nav">
            <li><a class="nav-link-item {{ Route::is('stations.*') ? 'active' : '' }}" href="{{ url('stations') }}"
                    data-page="stations"><i class="fa-solid fa-gas-pump"></i> Stations-service</a></li>
            <li><a class="nav-link-item {{ Route::is('garages.*') ? 'active' : '' }}" href="{{ url('garages') }}"
                    data-page="garages"><i class="fa-solid fa-wrench"></i> Garages & Services</a></li>
            <li><a class="nav-link-item {{ Route::is('partner-requests.*') ? 'active' : '' }}"
                    href="{{ url('partner-requests') }}" data-page="partner-requests"><i
                        class="fa-solid fa-handshake"></i> Demandes partenaires <span class="nav-badge"
                        style="background:#F59E0B">8</span></a></li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Contenu</div>
        <ul class="sidebar-nav">
            <li><a class="nav-link-item {{ Route::is('articles.*') ? 'active' : '' }}" href="{{ url('articles') }}"
                    data-page="articles"><i class="fa-solid fa-newspaper"></i> Articles & Conseils</a></li>
            <li><a class="nav-link-item {{ Route::is('promotions.*') ? 'active' : '' }}"
                    href="{{ url('promotions') }}" data-page="promotions"><i class="fa-solid fa-tag"></i>
                    Promotions</a></li>
            <li><a class="nav-link-item {{ Route::is('banners.*') ? 'active' : '' }}" href="{{ url('banners') }}"
                    data-page="banners"><i class="fa-solid fa-rectangle-ad"></i> Bannières pub</a></li>
            <li><a class="nav-link-item {{ Route::is('reviews.*') ? 'active' : '' }}" href="{{ url('reviews') }}"
                    data-page="reviews"><i class="fa-solid fa-star"></i>
                    Avis clients <span class="nav-badge" style="background:#F59E0B">12</span></a></li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Finances</div>
        <ul class="sidebar-nav">
            <li><a class="nav-link-item {{ Route::is('subscriptions.*') ? 'active' : '' }}"
                    href="{{ url('subscriptions') }}" data-page="subscriptions"><i class="fa-solid fa-crown"></i>
                    Abonnements</a></li>
            <li><a class="nav-link-item {{ Route::is('payments.*') ? 'active' : '' }}" href="{{ url('payments') }}"
                    data-page="payments"><i class="fa-solid fa-credit-card"></i> Paiements</a></li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Système</div>
        <ul class="sidebar-nav">
            <li><a class="nav-link-item {{ Route::is('settings.*') ? 'active' : '' }}" href="{{ url('settings') }}"
                    data-page="settings"><i class="fa-solid fa-sliders"></i> Paramètres</a></li>
            <li><a class="nav-link-item {{ Route::is('activity-logs.*') ? 'active' : '' }}"
                    href="{{ url('activity-logs') }}" data-page="logs"><i class="fa-solid fa-list-check"></i> Journaux
                    d'activité</a></li>
            <li><a class="nav-link-item {{ Route::is('app-versions.*') ? 'active' : '' }}"
                    href="{{ url('app-versions') }}" data-page="versions"><i class="fa-solid fa-mobile-screen"></i>
                    Versions app</a></li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar" style="width:36px;height:36px;font-size:13px">SA</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">Super Admin</div>
                <div class="sidebar-user-role">admin@gocarbu.com</div>
            </div>
            <i class="fa-solid fa-ellipsis-vertical" style="color:rgba(255,255,255,.3);font-size:13px"></i>
        </div>
    </div>
</aside>
