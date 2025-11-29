<?php

/**
 * Obtenemos los nombres de las variables de sesion
 */
$sessionName = config_sesion(1)['name'] ?? '';
$nameVarPermissionEmployee = $sessionName . 'menu_employee_permission';
/**
 * Creamos la ejecucion de las funciones anonias para 
 * darle el comportamiento a nuestro menu
 */
/**
 * Menu cuando el usuario es dueño
 */
$data_menu = function (int $interface): array {
    $sessionName = config_sesion(1)['name'] ?? '';
    $nameVarPermission = $sessionName . 'menu_permission';
    return array_merge(...array_values(array_filter($_SESSION[$nameVarPermission] ?? [], function ($item) use ($interface) {
        return $item['idInterface'] == $interface;
    })));
};
/**
 * Menu cuando el usuario tiene un rol asociado
 */
$data_menu_employee = function (int $interface): array {
    $sessionName = config_sesion(1)['name'] ?? '';
    $nameVarPermission = $sessionName . 'menu_employee_permission';
    return array_merge(...array_values(array_filter($_SESSION[$nameVarPermission] ?? [], function ($item) use ($interface) {
        return $item['idInterface'] == $interface;
    })));
};
$linkestadointerfaz = base_url() . '/pos/Errors/estado_plan_interfaz';
?>
<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="<?= GENERAR_PERFIL ?><?= $_SESSION[$nameVarLoginInfo]['name'] ?>" alt="User Image">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION[$nameVarLoginInfo]['name'] ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION[$nameVarLoginInfo]['lastname'] ?></p>
            <?= get_widget_plan($_SESSION[$nameVarLoginInfo]['plan'] ?? 'Gratis')['sm'] ?>
        </div>
    </div>
    <div class="px-1" id="cardBusiness">
        <div class="card shadow-sm" style="max-width: 280px;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-1">
                    <!-- Avatar -->
                    <img class="app-sidebar__user-avatar p-0 m-0" id="currentBusinessAvatar" src="<?= GENERAR_PERFIL ?><?= htmlspecialchars($_SESSION[$nameVarBusiness]['business'] ?? 'Negocio', ENT_QUOTES, 'UTF-8'); ?>" alt="User Image">
                    <!-- Nombre y rol -->
                    <div class="flex-grow-1">
                        <div class="fw-semibold" id="currentBusinessName"><?= htmlspecialchars($_SESSION[$nameVarBusiness]['business'] ?? 'Negocio', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-muted small" id="currentBusinessCategory"><?= htmlspecialchars($_SESSION[$nameVarBusiness]['category'] ?? 'Propietario', ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <!-- Caret / Dropdown selector -->
                    <div class="dropdown ms-auto">
                        <button class="btn btn-link p-0 text-secondary" id="businessDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Seleccionar negocio">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="businessDropdownBtn" id="businessListDropdown">
                            <li class="px-3 py-2 text-muted small">Cargando negocios...</li>
                        </ul>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Opciones -->
                <div class="list-group list-group-flush">
                    <!--<a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-0">
                        <i class="bi bi-gear"></i>
                        <span>Configuraciones</span>
                    </a>-->
                    <?php
                    if ($data_menu(8)):
                    ?>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-0" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
                            <i class="bi bi-plus-circle"></i>
                            <span>Agregar otro negocio</span>
                        </a>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <ul class="app-menu">
        <?php if (!isset($_SESSION[$nameVarPermissionEmployee])):  ?>
            <li><a class="app-menu__item <?= $data['page_id'] === 0 ? 'active' : '' ?>" href="<?= base_url() ?>/pos/dashboard"><i class="app-menu__icon bi bi-house-door"></i><span class="app-menu__label">Inicio</span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 1 ? 'active' : '' ?>" href=" <?= $data_menu(1) ? base_url() . '/pos/sales' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-cart"></i><span class="app-menu__label"><?= $data_menu(1) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(1) ? $data_menu(1)['Interface'] : 'Vender' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 2 ? 'active' : '' ?>" href="<?= $data_menu(2) ? base_url() . '/pos/movements' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-pc-display-horizontal"></i><span class="app-menu__label"><?= $data_menu(2) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(2) ? $data_menu(2)['Interface'] : 'Movimientos' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 3 ? 'active' : '' ?>" href="<?= $data_menu(3) ? base_url() . '/pos/inventory' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-box-seam"></i><span class="app-menu__label"><?= $data_menu(3) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(3) ? $data_menu(3)['Interface'] : 'Inventario' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 4 ? 'active' : '' ?>" href="<?= $data_menu(4) ? base_url() . '/pos/customers' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-person-lines-fill"></i><span class="app-menu__label"><?= $data_menu(4) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(4) ? $data_menu(4)['Interface'] : 'Clientes' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 7 ? 'active' : '' ?>" href="<?= $data_menu(7) ? base_url() . '/pos/suppliers' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label"><?= $data_menu(7) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(7) ? $data_menu(7)['Interface'] : 'Proveedores' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 5 ? 'active' : '' ?>" href="<?= $data_menu(5) ? base_url() . '/pos/employee' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label"><?= $data_menu(5) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(5) ? $data_menu(5)['Interface'] : 'Empleados' ?></span></a></li>
            <li><a class="app-menu__item <?= $data['page_id'] === 6 ? 'active' : '' ?>" href="<?= $data_menu(6) ? base_url() . '/pos/roles' : $linkestadointerfaz ?>"><i class="app-menu__icon bi bi-shield-check"></i><span class="app-menu__label"><?= $data_menu(6) ? '' : '<span class="badge bg-success text-white shadow"><i class="bi bi-arrow-up"></i> Mejorar</span>' ?> <?= $data_menu(6) ? $data_menu(6)['Interface'] : 'Roles' ?></span></a></li>
        <?php else: ?>
            <li>
                <a class="app-menu__item <?= $data['page_id'] === 0 ? 'active' : '' ?>" href="<?= base_url() ?>/pos/dashboard">
                    <i class="app-menu__icon bi bi-house-door"></i>
                    <span class="app-menu__label">Inicio</span>
                </a>
            </li>
            <?php if ($data_menu_employee(1)) : ?>
                <li>
                    <a <?= $data_menu_employee(1) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 1 ? 'active' : '' ?>" href=" <?= $data_menu_employee(1) ? base_url() . '/pos/sales' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-cart"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(1) ? $data_menu_employee(1)['Interface'] : 'Vender' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(2)) : ?>
                <li>
                    <a <?= $data_menu_employee(2) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 2 ? 'active' : '' ?>" href="<?= $data_menu_employee(2) ? base_url() . '/pos/movements' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-pc-display-horizontal"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(2) ? $data_menu_employee(2)['Interface'] : 'Movimientos' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(3)) : ?>
                <li>
                    <a <?= $data_menu_employee(3) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 3 ? 'active' : '' ?>" href="<?= $data_menu_employee(3) ? base_url() . '/pos/inventory' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-box-seam"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(3) ? $data_menu_employee(3)['Interface'] : 'Inventario' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(4)) : ?>
                <li>
                    <a <?= $data_menu_employee(4) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 4 ? 'active' : '' ?>" href="<?= $data_menu_employee(4) ? base_url() . '/pos/customers' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-person-lines-fill"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(4) ? $data_menu_employee(4)['Interface'] : 'Clientes' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(7)) : ?>
                <li>
                    <a <?= $data_menu_employee(7) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 7 ? 'active' : '' ?>" href="<?= $data_menu_employee(7) ? base_url() . '/pos/suppliers' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-people"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(7) ? $data_menu_employee(7)['Interface'] : 'Proveedores' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(5)) : ?>
                <li>
                    <a <?= $data_menu_employee(5) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 5 ? 'active' : '' ?>" href="<?= $data_menu_employee(5) ? base_url() . '/pos/employee' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-people"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(5) ? $data_menu_employee(5)['Interface'] : 'Empleados' ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($data_menu_employee(6)) : ?>
                <li>
                    <a <?= $data_menu_employee(6) ? '' : 'style="cursor: no-drop;"' ?> class="app-menu__item <?= $data['page_id'] === 6 ? 'active' : '' ?>" href="<?= $data_menu_employee(6) ? base_url() . '/pos/roles' : base_url() . '/pos/Errors/no_permisos' ?>">
                        <i class="app-menu__icon bi bi-shield-check"></i>
                        <span class="app-menu__label"><?= $data_menu_employee(6) ? $data_menu_employee(6)['Interface'] : 'Roles' ?></span>
                    </a>
                </li>
            <?php endif; ?>

        <?php endif; ?>
    </ul>
    <div class="w-100 text-center">
        <span class="version text-center text-white mt-3 badge bg-primary">Version: <?= VERSION_SISTEMA ?> (Beta)</span>
    </div>
</aside>

<!-- Modal: Registrar nuevo negocio -->
<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-labelledby="addBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form class="modal-content" id="formAddBusiness" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addBusinessModalLabel">Registrar nuevo negocio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(true, 1); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="businessType" class="form-label">Tipo de negocio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select class="form-select" id="businessType" name="businessType" required>
                                <option value="" selected disabled>Selecciona un tipo de negocio</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="businessName" class="form-label">Nombre del negocio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shop"></i></span>
                            <input type="text" class="form-control" id="businessName" name="businessName" maxlength="255" required placeholder="Ingresa el nombre comercial">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="businessDocument" class="form-label">Número de documento <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control" id="businessDocument" name="businessDocument" maxlength="11" required placeholder="RUC o documento">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="businessEmail" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="businessEmail" name="businessEmail" maxlength="255" required placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label for="businessTelephonePrefix" class="form-label">Prefijo telefónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-plus"></i></span>
                            <input type="text" class="form-control" id="businessTelephonePrefix" name="businessTelephonePrefix" maxlength="7" required placeholder="+51" value="+51">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label for="businessCountry" class="form-label">País</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control" id="businessCountry" value="PERU" onkeyup="this.value = this.value.toUpperCase()" name="businessCountry" maxlength="100" placeholder="País del negocio">
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-8">
                        <label for="businessPhone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control" id="businessPhone" name="businessPhone" maxlength="11" required placeholder="Número de contacto">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="businessCity" class="form-label">Ciudad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo"></i></span>
                            <input type="text" class="form-control" id="businessCity" onkeyup="this.value = this.value.toUpperCase()" name="businessCity" maxlength="250" placeholder="Ciudad o provincia">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="businessDirection" class="form-label">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-signpost-2"></i></span>
                            <input type="text" class="form-control" id="businessDirection" onkeyup="this.value = this.value.toUpperCase()" name="businessDirection" placeholder="Dirección comercial">
                        </div>
                    </div>
                </div>
                <p class="text-muted small mt-3 mb-0">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnAddBusiness"><i class="bi bi-save"></i> Guardar negocio</button>
            </div>
        </form>
    </div>
</div>