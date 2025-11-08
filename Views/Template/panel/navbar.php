<header class="app-header"><a class="app-header__logo"
        href="<?= base_url(); ?>"><?= (getSystemInfo()) ? getSystemInfo()["c_name"] : getSystemName(); ?></a>
    <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar"
        aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav">
        <!--Notification Menu-->
        <li class="dropdown">
            <a class="app-nav__item position-relative" href="#" data-toggle="dropdown" aria-label="Show notifications">
                <i class="fa fa-bell-o fa-lg"></i>
                <i class="fa fa-circle position-absolute pulse d-none" id="notification-count"
                    style="font-size: 10px; top: 25%; right: 30%;"></i>
            </a>
            <ul class="app-notification dropdown-menu dropdown-menu-right">
                <li class="app-notification__title">Centro de notificaciones</li>
                <div class="app-notification__content" id="notification-list">
                </div>
            </ul>
        </li>
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i
                    class="fa fa-user fa-lg"></i></a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="#" id="sidebarToggle"><i class="fa fa-users fa-lg"></i>
                        Usuarios Activo</a></li>
                <li><a class="dropdown-item" href="<?= base_url() ?>/users/profile"><i class="fa fa-user fa-lg"></i>
                        Perfil</a></li>
                <li><a class="dropdown-item" href="<?= base_url() ?>/LogOut"><i class="fa fa-sign-out fa-lg"></i> Cerrar
                        Sesión</a>
                </li>
            </ul>
        </li>
    </ul>
</header>