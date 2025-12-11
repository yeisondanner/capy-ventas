<?php
$validationCreateBox = (validate_permission_app(11, "c", false)) ? (int) validate_permission_app(11, "c", false)['create'] : 0;
if (empty($_SESSION[$nameVarBusiness]['logo'])) {
    $logoBusiness = GENERAR_PERFIL . htmlspecialchars($_SESSION[$nameVarBusiness]['business'] ?? 'Negocio', ENT_QUOTES, 'UTF-8');
} else {
    $logoBusiness = base_url() . '/Loadfile/iconbusiness?f=' . $_SESSION[$nameVarBusiness]['logo'];
}
?>
<!-- Navbar-->
<header class="app-header">
    <a class="app-header__logo" href="<?= base_url() ?>/pos/dashboard">Capy Ventas</a>
    <!-- Sidebar toggle button-->
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav gap-2">
        <?php if ($validationCreateBox === 1):
        ?>
            <div class="d-flex justify-content-center align-items-center">
                <button id="btnOpenModalGestionBox" class="btn btn-warning rounded-5 px-3 d-flex align-items-center gap-2 font-weight-bold">
                    <img style="width: 22px;" src="<?= media()  ?>/icons/POS/open-box.png" alt="">
                    <span class="fw-bold">Movimientos y Gestión de Caja</span>
                </button>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <button id="btnOpenModalBox" class="btn btn-warning rounded-5 px-3 d-flex align-items-center gap-2 font-weight-bold">
                    <img style="width: 22px;" src="<?= media()  ?>/icons/POS/open-box.png" alt="">
                    <span class="fw-bold">Caja</span>
                </button>
            </div>
        <?php endif;
        ?>
        <!--Notification Menu-->
        <!--  <li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Show notifications"><i class="bi bi-bell fs-5"></i></a>
            <ul class="app-notification dropdown-menu dropdown-menu-right">
                <li class="app-notification__title">You have 4 new notifications.</li>
                <div class="app-notification__content">
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-envelope fs-4 text-primary"></i></span>
                            <div>
                                <p class="app-notification__message">Lisa sent you a mail</p>
                                <p class="app-notification__meta">2 min ago</p>
                            </div>
                        </a></li>
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-exclamation-triangle fs-4 text-warning"></i></span>
                            <div>
                                <p class="app-notification__message">Mail server not working</p>
                                <p class="app-notification__meta">5 min ago</p>
                            </div>
                        </a></li>
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-cash fs-4 text-success"></i></span>
                            <div>
                                <p class="app-notification__message">Transaction complete</p>
                                <p class="app-notification__meta">2 days ago</p>
                            </div>
                        </a></li>
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-envelope fs-4 text-primary"></i></span>
                            <div>
                                <p class="app-notification__message">Lisa sent you a mail</p>
                                <p class="app-notification__meta">2 min ago</p>
                            </div>
                        </a></li>
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-exclamation-triangle fs-4 text-warning"></i></span>
                            <div>
                                <p class="app-notification__message">Mail server not working</p>
                                <p class="app-notification__meta">5 min ago</p>
                            </div>
                        </a></li>
                    <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><i class="bi bi-cash fs-4 text-success"></i></span>
                            <div>
                                <p class="app-notification__message">Transaction complete</p>
                                <p class="app-notification__meta">2 days ago</p>
                            </div>
                        </a></li>
                </div>
                <li class="app-notification__footer"><a href="#">See all notifications.</a></li>
            </ul>
        </li>-->
        <!-- User Menu-->

        <li class="dropdown d-flex align-items-center">
            <?= get_widget_plan($_SESSION[$nameVarLoginInfo]['plan'] ?? 'Gratis')['sm'] ?>
            <a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
                <i class="bi bi-person fs-4"></i>
            </a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <!-- <li><a class="dropdown-item" href="page-user.html"><i class="bi bi-gear me-2 fs-5"></i> Settings</a></li>-->
                <li><a class="dropdown-item" href="<?= base_url() ?>/pos/Profile"><i class="bi bi-person me-2 fs-5"></i> Perfil</a></li>
                <li><a class="dropdown-item" href="<?= base_url() ?>/pos/LogOut"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Cerrar Sesión</a></li>
            </ul>
        </li>
    </ul>
</header>

<!-- Modal: Add Box -->
<div class="modal fade" id="modalAddBox" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body d-flex flex-column p-4 gap-3">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <div>
                        <h3 class="fw-bold mb-1">Apertura de Caja</h3>
                        <p class="text-muted mb-0 small">Configure los detalles para iniciar turno</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="item-box d-flex align-items-center gap-3 border rounded-5 p-2" style="background-color: var(--input-bg)">
                    <div class="avatar-wrapper flex-shrink-0">
                        <img src="<?= $logoBusiness ?>" style="width: 3rem;" alt="User" alt="User Image" class="rounded-circle">
                        <span class="status-dot position-absolute rounded-circle"></span>
                    </div>
                    <div class="flex-fill overflow-hidden">
                        <h6 class="mb-0 fw-bold"><?= $_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'] ?></h6>
                        <div class="text-secondary small text-muted">
                            ID: 4829 - Cajero
                        </div>
                    </div>
                    <div class="icon-action px-2">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>

                </div>
                <div class="flex-fill item-box">
                    <label class="form-label" for="select_box">Seleccionar Caja <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-wallet-fill"></i></span>
                        <select class="form-select" name="select_box" id="select_box">
                            <option value="" disabled="" selected="">Selecciona caja disponible</option>
                            <option value="1">Caja 01 - Principal</option>
                            <option value="2">Caja 03 - Secundaria</option>
                        </select>
                    </div>
                </div>
                <div class="flex-fill item-box">
                    <label class="form-label" for="update_documentNumber">Monto Inicial de Efectivo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                        <input type="number" class="form-control fs-4" name="update_documentNumber" id="update_documentNumber" value="0.00" placeholder="0.00" step="0.01" min="0">
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2 w-100">
                    <button type="button" class="btn btn-lg btn-light border flex-grow-1 text-muted fw-bold rounded-5" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-lg btn-primary flex-grow-1 fw-bold rounded-5">
                        <i class="bi bi-unlock2-fill"></i> Confirmar y Abrir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gestión de caja -->
<div class="modal fade" id="modalGestionBox" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body d-flex flex-column p-4 gap-3">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <div>
                        <h3 class="fw-bold mb-1">Movimientos y Gestión de Caja</h3>
                        <p class="text-muted mb-0 small"><i class="bi bi-circle-fill text-success"></i> Turno actual: <strong id="reloj"><?= date('H:i:s A'); ?></strong></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="card rounded-4 mb-2">
                            <div class="d-flex justify-content-between align-items-start w-100 p-3 pb-0">
                                <div>
                                    <h5 class="text-muted mb-1">Total en Caja</h5>
                                    <h3><?= getCurrency(); ?>2,500<span class="text-muted fs-6">.00</span></h3>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span style="color: greenyellow !important;" class="badge text-bg-success rounded-4 fs-6"><i class="bi bi-graph-up-arrow"></i>&nbsp;<i class="bi bi-plus-lg"></i><?= getCurrency(); ?>100.00</span>
                                    <p>Base Inicial</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="flex-fill">
                                        <div class="card card-body rounded-4">
                                            <label class="d-flex gap-2 align-items-center mb-2"><span class="badge rounded-pill text-bg-primary"><i class="bi bi-cash-stack"></i></span> Efectivo</label>
                                            <h4 class="fw-bold text-muted mb-0"><?= getCurrency(); ?>200.00</h4>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="card card-body rounded-4">
                                            <label class="d-flex gap-2 align-items-center mb-2"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-credit-card-fill"></i></span> Tarjeta</label>
                                            <h4 class="fw-bold text-muted mb-0"><?= getCurrency(); ?>280.00</h4>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="card card-body rounded-4">
                                            <label class="d-flex gap-1 align-items-center mb-2"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-credit-card-fill"></i></span> Pagos Qr <span class="badge rounded-pill text-bg-primary">Yape</span><span class="badge rounded-pill text-bg-info">Plin</span></label>
                                            <h4 class="fw-bold text-muted mb-0"><?= getCurrency(); ?>280.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card rounded-4 p-4">
                            <h3>Aqui muestra el grafico de estadistica</h3>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="item-box d-flex align-items-center gap-3 border rounded-4 p-2 mb-2" style="background-color: var(--input-bg)">
                            <div class="avatar-wrapper flex-shrink-0">
                                <img src="<?= $logoBusiness ?>" style="width: 3rem;" alt="User" alt="User Image" class="rounded-circle">
                                <span class="status-dot position-absolute rounded-circle"></span>
                            </div>
                            <div class="flex-fill overflow-hidden">
                                <h6 class="mb-0 fw-bold"><?= $_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'] ?></h6>
                                <div class="text-secondary small text-muted">
                                    ID: 4829 - Cajero
                                </div>
                            </div>
                            <div class="icon-action px-2">
                                <i class="bi bi-person-vcard-fill"></i>
                            </div>

                        </div>
                        <div class="card rounded-4 mb-2">
                            <h5 class="text-center text-muted pt-2 mb-0">Acciones de Caja</h5>
                            <div class="card-body">
                                <div class="d-flex gap-2">
                                    <div class="flex-fill">
                                        <button class="btn btn-light border border-1 rounded-4 d-flex flex-column p-2 align-items-center bg-white w-100">
                                            <i class="bi bi-card-checklist fs-4 text-primary"></i>
                                            <h6>Realizar Arqueo</h6>
                                            <p class="mb-0 fw-normal">Contar efectivo</p>
                                        </button>
                                    </div>
                                    <div class="flex-fill">
                                        <button class="btn btn-light border border-1 rounded-4 d-flex flex-column p-2 align-items-center bg-white w-100">
                                            <i class="bi bi-lock-fill fs-4 text-danger"></i>
                                            <h6>Cierre de Caja</h6>
                                            <p class="mb-0 fw-normal">Finalizar turno</p>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-2 w-100">
                                    <button type="button" class="btn btn-primary flex-grow-1 rounded-5">
                                        <i class="bi bi-plus-circle-dotted"></i> Ingreso / Retiro Manual
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card rounded-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-muted mb-0">Movimientos Recientes</h6>
                                <a class="text-decoration-none fw-bold" href="#">Ver Todo</a>
                            </div>
                            <ul class="list-group list-group-flush rounded-4">
                                <li class="list-group-item d-flex align-items-center gap-2">
                                    <span class="badge fs-6 rounded-pill text-bg-success"><i class="bi bi-cart-fill"></i></span>
                                    <div class="d-flex justify-content-between w-100">
                                        <div>
                                            <h6 class="mb-0">Venta #234</h6>
                                            <p class="mb-0">Hace 2 min</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-success"><i class="bi bi-plus-lg"></i><?= getCurrency(); ?>50.00</span>
                                            <p class="mb-0">Efectivo</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-2">
                                    <span class="badge fs-6 rounded-pill text-bg-danger"><i class="bi bi-dash-circle-fill"></i></span>
                                    <div class="d-flex justify-content-between w-100">
                                        <div>
                                            <h6 class="mb-0">Retiro Parcial</h6>
                                            <p class="mb-0">Hace 15 min</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-danger"><i class="bi bi-dash"></i><?= getCurrency(); ?>10.00</span>
                                            <p class="mb-0">Admin</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-2">
                                    <span class="badge fs-6 rounded-pill text-bg-success"><i class="bi bi-cart-fill"></i></span>
                                    <div class="d-flex justify-content-between w-100">
                                        <div>
                                            <h6 class="mb-0">Venta #1123</h6>
                                            <p class="mb-0">Hace 22 min</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-success"><i class="bi bi-plus-lg"></i><?= getCurrency(); ?>80.00</span>
                                            <p class="mb-0">Tarjeta</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-2">
                                    <span class="badge fs-6 rounded-pill text-bg-info text-white"><i class="bi bi-plus-circle-fill"></i></span>
                                    <div class="d-flex justify-content-between w-100">
                                        <div>
                                            <h6 class="mb-0">Ingreso Inicial</h6>
                                            <p class="mb-0">09:00 AM</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-success"><i class="bi bi-plus-lg"></i><?= getCurrency(); ?>100.00</span>
                                            <p class="mb-0">Sistema</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>