<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="<?= GENERAR_PERFIL ?><?= $_SESSION[$nameVarLoginInfo]['name'] ?>" alt="User Image">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION[$nameVarLoginInfo]['name'] ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION[$nameVarLoginInfo]['lastname'] ?></p>
        </div>
    </div>
    <div class="px-1">
        <div class="card shadow-sm" style="max-width: 280px;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-1">
                    <!-- Avatar -->

                    <img class="app-sidebar__user-avatar p-0 m-0" src="<?= GENERAR_PERFIL ?><?= $_SESSION[$nameVarBusiness]['business'] ?>" alt="User Image">


                    <!-- Nombre y rol -->
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?= $_SESSION[$nameVarBusiness]['business'] ?></div>
                        <div class="text-muted small">Propietario</div>
                    </div>

                    <!-- Caret / Dropdown selector -->
                    <!-- <div class="dropdown ms-auto">
                        <button class="btn btn-link p-0 text-secondary" id="businessDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Seleccionar negocio">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="businessDropdownBtn">
                            <li>
                                <a class="dropdown-item d-flex align-items-center active" href="#" aria-current="true">
                                    <i class="bi bi-check-lg me-2"></i>
                                    CyD Tech
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="me-2"></i>
                                    Mi Tienda
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="me-2"></i>
                                    Otra Empresa
                                </a>
                            </li>
                        </ul>
                    </div>-->
                </div>

                <hr class="my-3">

                <!-- Opciones -->
                <div class="list-group list-group-flush">
                    <!--<a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-0">
                        <i class="bi bi-gear"></i>
                        <span>Configuraciones</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-0" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>Agregar otro negocio</span>
                    </a>-->
                </div>
            </div>
        </div>


    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item active" href="<?= base_url() ?>/pos/dashboard"><i class="app-menu__icon bi bi-house-door"></i>
                <span class="app-menu__label">Inicio</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/sales"><i class="app-menu__icon bi bi-cart"></i><span class="app-menu__label">Vender</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/movements"><i class="app-menu__icon bi bi-pc-display-horizontal"></i><span class="app-menu__label">Movimientos</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/inventory"><i class="app-menu__icon bi bi-box-seam"></i><span class="app-menu__label">Inventario</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/customers"><i class="app-menu__icon bi bi-person-lines-fill"></i><span class="app-menu__label">Clientes</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/suppliers"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label">Proveedores</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/employee"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label">Empleados</span></a></li>
    </ul>
</aside>