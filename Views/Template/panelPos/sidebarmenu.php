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
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-0" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>Agregar otro negocio</span>
                    </a>
                </div>
            </div>
        </div>


    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item active" href="<?= base_url() ?>/pos/dashboard"><i class="app-menu__icon bi bi-house-door"></i><span class="app-menu__label">Inicio</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/sales"><i class="app-menu__icon bi bi-cart"></i><span class="app-menu__label">Vender</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/movements"><i class="app-menu__icon bi bi-pc-display-horizontal"></i><span class="app-menu__label">Movimientos</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/inventory"><i class="app-menu__icon bi bi-box-seam"></i><span class="app-menu__label">Inventario</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/customers"><i class="app-menu__icon bi bi-person-lines-fill"></i><span class="app-menu__label">Clientes</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/suppliers"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label">Proveedores</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/employee"><i class="app-menu__icon bi bi-people"></i><span class="app-menu__label">Empleados</span></a></li>
        <li><a class="app-menu__item " href="<?= base_url() ?>/pos/roles"><i class="app-menu__icon bi bi-shield-check"></i><span class="app-menu__label">Roles</span></a></li>
    </ul>
</aside>

<!-- Modal: Registrar nuevo negocio -->
<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-labelledby="addBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <select class="form-select" id="businessType" name="businessType" required>
                            <option value="" selected disabled>Selecciona un tipo de negocio</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="businessName" class="form-label">Nombre del negocio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="businessName" name="businessName" maxlength="255" required placeholder="Ingresa el nombre comercial">
                    </div>
                    <div class="col-md-6">
                        <label for="businessDocument" class="form-label">Número de documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="businessDocument" name="businessDocument" maxlength="11" required placeholder="RUC o documento">
                    </div>
                    <div class="col-md-6">
                        <label for="businessEmail" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="businessEmail" name="businessEmail" maxlength="255" required placeholder="correo@ejemplo.com">
                    </div>
                    <div class="col-md-4">
                        <label for="businessTelephonePrefix" class="form-label">Prefijo telefónico <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="businessTelephonePrefix" name="businessTelephonePrefix" maxlength="7" required placeholder="+51" value="+51">
                    </div>
                    <div class="col-md-8">
                        <label for="businessPhone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="businessPhone" name="businessPhone" maxlength="11" required placeholder="Número de contacto">
                    </div>
                    <div class="col-md-6">
                        <label for="businessCountry" class="form-label">País</label>
                        <input type="text" class="form-control" id="businessCountry" name="businessCountry" maxlength="100" placeholder="País del negocio">
                    </div>
                    <div class="col-md-6">
                        <label for="businessCity" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="businessCity" name="businessCity" maxlength="250" placeholder="Ciudad o provincia">
                    </div>
                    <div class="col-12">
                        <label for="businessDirection" class="form-label">Dirección</label>
                        <textarea class="form-control" id="businessDirection" name="businessDirection" rows="2" placeholder="Dirección comercial"></textarea>
                    </div>
                </div>
                <p class="text-muted small mt-3 mb-0">Los campos marcados con * son obligatorios.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnAddBusiness"><i class="bi bi-save"></i> Guardar negocio</button>
            </div>
        </form>
    </div>
</div>