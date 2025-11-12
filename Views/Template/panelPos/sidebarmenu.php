<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= GENERAR_PERFIL ?><?= $_SESSION[$nameVarLoginInfo]['name'] ?>" alt="User Image">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION[$nameVarLoginInfo]['name'] ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION[$nameVarLoginInfo]['lastname'] ?></p>
        </div>
    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item active" href="<?= base_url() ?>/pos/dashboard"><i class="app-menu__icon bi bi-house-door"></i>
                <span class="app-menu__label">Inicio</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/inventory"><i class="app-menu__icon bi bi-box-seam"></i><span class="app-menu__label">Inventario</span></a></li>
    </ul>
</aside>