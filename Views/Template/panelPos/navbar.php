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
    <a class="app-header__logo" href="<?= base_url() ?>/pos/dashboard">
        <img class="p-1 bg-white rounded-5" style="width: 2rem;" src="<?= media() ?>/carpincho.png" alt="">
        Capy Ventas
    </a>
    <!-- Sidebar toggle button-->
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav gap-2">
        <?php if ($validationCreateBox === 1):
        ?>
            <div class="d-flex gap-2" id="divOpenBox">
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
            <div class="modal-body d-flex flex-column p-4 gap-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">Apertura de Caja</h3>
                        <p class="text-muted mb-0 small">Configure los detalles para iniciar turno</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="d-flex align-items-center gap-3 p-2 pe-3 border rounded-pill bg-body-tertiary">
                    <div class="position-relative">
                        <img src="<?= $logoBusiness ?>" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                    </div>
                    <div class="flex-fill lh-1">
                        <h6 class="mb-1 fw-bold text-dark">
                            <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.85rem;">
                            ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span class="text-primary fw-medium">Cajero</span>
                        </small>
                    </div>
                    <div class="text-opacity-75 opacity-50 px-2">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="item-box">
                        <label class="form-label fw-bold small text-muted text-uppercase" for="select_box">
                            Caja de destino <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 ps-3">
                                <i class="bi bi-shop-window"></i>
                            </span>
                            <select class="form-select border-start-0 py-2 fw-medium" name="selectBox" id="selectBox" required>
                            </select>
                        </div>
                    </div>
                    <div class="item-box">
                        <label class="form-label fw-bold small text-muted text-uppercase" for="cash_opening_amount">
                            Efectivo Inicial <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text border-end-0 ps-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </span>
                            <input type="number"
                                class="form-control border-start-0 text-center fw-bold fs-3 text-dark"
                                name="cash_opening_amount"
                                id="cash_opening_amount"
                                value="0"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-lg btn-light border flex-grow-1 text-muted fw-bold rounded-pill" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="btnOpenBox" type="button" class="btn btn-lg btn-primary flex-grow-1 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-unlock-fill me-2"></i> Abrir Turno
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
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Movimientos y Gestión</h4>
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <span class="badge border border-success text-success bg-success-subtle rounded-pill">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Activa
                            </span>
                            <span>Caja 01 - Principal</span>
                            <span>•</span>
                            <span id="reloj" class="text-dark fw-bold"><?= date('H:i:s A'); ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row g-4">
                    <div class="col-lg-7 d-flex flex-column gap-3">
                        <div class="card border border-success bg-success-subtle rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-success fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total en Caja</h6>
                                        <h2 id="quick_access_total_general" class="display-6 fw-bold text-dark mb-0">
                                            <!-- Aqui carga el total general -->
                                        </h2>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-6 badge border border-success text-success bg-white rounded-pill px-3 py-2">
                                            <i class="bi bi-graph-up-arrow me-1"></i>
                                            <small id="quick_access_base_amount"><!-- Aqui carga el monto inicial de apertura --></small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="quick_access_card_payment_method" class="row g-3">
                        </div>
                        <div class="card border rounded-4 flex-fill">
                            <div class="card-body d-flex align-items-center justify-content-center text-muted p-4" style="min-height: 140px;">
                                <div class="text-center opacity-50">
                                    <i class="bi bi-bar-chart-line fs-2 mb-2 d-block"></i>
                                    <small>Estadística de ventas por hora</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-2 pe-3 border rounded-pill bg-body-tertiary">
                            <div class="position-relative">
                                <img src="<?= $logoBusiness ?>" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                            </div>
                            <div class="flex-fill lh-1">
                                <h6 class="mb-1 fw-bold text-dark">
                                    <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                                </h6>
                                <small class="text-muted" style="font-size: 0.85rem;">
                                    ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span class="text-primary fw-medium">Cajero</span>
                                </small>
                            </div>
                            <div class="text-opacity-75 opacity-50 px-2">
                                <i class="bi bi-person-badge-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                                <i class="bi bi-arrow-left-right me-2"></i> Ingreso / Retiro
                            </button>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button id="btnOpenModalArqueoBox" class="btn btn-outline-light border w-100 p-3 rounded-4 h-100 text-start">
                                        <i class="bi bi-calculator fs-4 mb-1 d-block text-primary"></i>
                                        <span class="fw-bold text-dark d-block">Arqueo</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Contar dinero</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button id="btnOpenModalCloseBox" class="btn btn-outline-light border w-100 p-3 rounded-4 h-100 text-start">
                                        <i class="bi bi-lock-fill fs-4 mb-1 d-block text-danger"></i>
                                        <span class="fw-bold text-dark d-block">Cierre</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Finalizar turno</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card border rounded-4 flex-fill">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                                <h6 id="quick_access_title_list_movements" class="fw-bold mb-0">Últimos Movimientos</h6>
                                <a href="#" class="text-decoration-none small fw-bold">Ver todos</a>
                            </div>
                            <div id="quick_access_card_list_movements" class="list-group list-group-flush rounded-bottom-4">
                                <div class="list-group-item px-3 py-3 border-bottom-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-cart-fill"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">Venta #234</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Hace 2 min</small>
                                        </div>
                                        <div class="text-end lh-1">
                                            <span class="d-block fw-bold text-success">+<?= getCurrency(); ?>50.00</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">Efectivo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item px-3 py-3 border-bottom-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-arrow-up-right"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">Retiro Parcial</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Hace 15 min</small>
                                        </div>
                                        <div class="text-end lh-1">
                                            <span class="d-block fw-bold text-danger">-<?= getCurrency(); ?>10.00</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">Admin</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item px-3 py-3 border-bottom-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 text-dark fw-bold">Apertura</h6>
                                            <small class="text-muted">09:00 AM • Inicio</small>
                                        </div>
                                        <div class="text-end lh-1">
                                            <span class="d-block text-success fw-bold">+<?= getCurrency(); ?>100.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2 w-100">
                    <button type="button" class="btn btn-light border text-muted fw-bold rounded-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Arqueo de caja -->
<div class="modal fade" id="modalArqueoBox" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 bg-light">
                <div class="d-flex justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Arqueo de Caja</h4>
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <span class="badge border border-success text-success bg-success-subtle rounded-pill">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Activa
                            </span>
                            <span>Caja 01 - Principal</span>
                            <span>•</span>
                            <span id="reloj_2" class="fw-bold text-dark"><?= date('H:i:s A'); ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="row g-4">
                    <div class="col-lg-5 d-flex flex-column gap-3">
                        <div class="card border rounded-4 bg-white shadow-sm">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-2">
                                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-hdd-rack-fill me-2"></i>Esperado por Sistema</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-end mb-3">
                                    <div>
                                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Efectivo Total</small>
                                        <h3 id="quick_access_arqueo_total_efectivo" class="fw-bold text-dark mb-0"><?= getCurrency(); ?>950.00</h3>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Ventas Totales</small>
                                        <h6 id="quick_access_arqueo_total_general" class="mb-0 text-muted"><?= getCurrency(); ?>12,540.00</h6>
                                    </div>
                                </div>
                                <div class="d-flex gap-2" id="quick_access_arqueo_total_payment_method">
                                    <!-- Aqui los demas card de las targetas -->
                                </div>
                            </div>
                        </div>

                        <div class="card border rounded-4 bg-white shadow-sm flex-fill">
                            <div class="p-2" id="quick_access_arqueo_message">
                                <!-- Aqui va el mensaje si es descuadre o esta ok -->
                            </div>
                            <h6 class="px-3 text-muted text-center mt-2 mb-0 small text-uppercase fw-bold">Total contado</h6>
                            <div class="d-flex flex-column px-3 pb-3">
                                <h1 id="quick_access_arqueo_count_efectivo" class="text-center w-100 mb-2 fw-bold text-primary"><?= getCurrency(); ?>0.00</h1>
                                <div class="d-flex flex-wrap align-items-center w-100 gap-2">
                                    <div id="quick_access_arqueo_diference" class="d-flex gap-2 align-items-center p-1">
                                        <!-- Aqui va la diferencia -->
                                    </div>
                                    <div class="flex-fill">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-4 border-opacity-75" id="quick_access_arqueo_justificacion" placeholder="Motivo">
                                            <label for="quick_access_arqueo_justificacion" class="text-muted"><i class="bi bi-pencil-square me-1"></i>Justificación</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border rounded-4 h-100 bg-white shadow-sm">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-cash-stack me-2 text-primary"></i>Conteo de Efectivo</h6>
                                <button id="btnLimpiarArqueo" class="btn btn-sm btn-light border rounded-pill px-3">Limpiar</button>
                            </div>
                            <div class="card-body p-4">
                                <div id="quick_access_arqueo_currency_denominations">
                                    <!-- Denominaciones de monedas -->
                                </div>
                                <div id="quick_access_desgloce_efectivo" class="d-flex justify-content-between pt-3 border bg-light rounded-4 p-2">
                                    <div class="text-center w-50 border-end">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">Billetes</small>
                                        <div class="fw-bold text-dark"><?= getCurrency() ?>1,280.00</div>
                                    </div>
                                    <div class="text-center w-50">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">Monedas</small>
                                        <div class="fw-bold text-dark"><?= getCurrency() ?>65.80</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 text-muted" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="setArqueoCaja" type="button" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Confirmar Arqueo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cerrar caja -->
<div class="modal fade" id="modalCloseBox" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 bg-white">
                <div>
                    <h4 class="fw-bold mb-1">Cierre de Caja</h4>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Por Cerrar
                        </span>
                        <span>Caja Principal</span>
                        <span>•</span>
                        <span id="reloj_3" class="fw-bold text-dark"><?= date('H:i:s A'); ?></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-lg-4 d-flex flex-column gap-3">
                        <div class="card rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted small text-uppercase mb-4">
                                    <i class="bi bi-shop me-2"></i>Resumen del Turno
                                </h6>
                                <div class="d-flex justify-content-between align-items-end mb-4">
                                    <div>
                                        <small class="text-muted d-block fw-semibold">Ventas Totales</small>
                                        <h2 class="fw-bold text-dark mb-0" id="close_box_total_sales">S/ 0.00</h2>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block fw-semibold">Transacciones</small>
                                        <span class="fs-5 fw-bold text-dark" id="close_box_total_transactions">0</span>
                                    </div>
                                </div>
                                <div id="close_box_total_payment_method" class="d-flex flex-column gap-2 border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-success fw-bold"><i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>Efectivo</span>
                                        <span class="fw-bold" id="close_box_breakdown_cash">S/ 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-primary fw-bold"><i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>Tarjetas / Digital</span>
                                        <span class="fw-bold" id="close_box_breakdown_digital">S/ 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-info fw-bold"><i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>Crédito / Otros</span>
                                        <span class="fw-bold" id="close_box_breakdown_other">S/ 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card rounded-4 shadow-sm flex-fill">
                            <div class="card-body p-4 d-flex flex-column">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">
                                    <i class="bi bi-wallet2 me-2"></i>Balance de Efectivo
                                </h6>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted fw-semibold">Fondo Inicial</span>
                                    <span class="fw-bold text-dark" id="close_box_base">S/ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted fw-semibold">Ingresos Efectivo</span>
                                    <span class="fw-bold text-success" id="close_box_income">+S/ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4 small">
                                    <span class="text-muted fw-semibold">Salidas/Retiros</span>
                                    <span class="fw-bold text-danger" id="close_box_expenses">-S/ 0.00</span>
                                </div>
                                <div class="mt-auto border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold text-dark">Total Esperado</span>
                                        <span class="h5 fw-bold text-dark mb-0" id="close_box_expected">S/ 0.00</span>
                                    </div>

                                    <div id="close_box_status_container">
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted fst-italic" style="font-size: 0.7rem;">
                                            Comparado con arqueo físico previo
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="card shadow-sm rounded-4 h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Pendientes</h6>
                                                <small class="text-muted">Órdenes sin finalizar</small>
                                            </div>
                                            <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                                                <i class="bi bi-clock-history fs-5"></i>
                                            </div>
                                        </div>
                                        <div id="close_box_pending_list">
                                            <div class="d-flex align-items-center gap-2 text-success small fw-bold">
                                                <i class="bi bi-check-all fs-5"></i> Todo procesado correctamente
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm rounded-4 h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Alertas</h6>
                                                <small class="text-muted">Estado del sistema</small>
                                            </div>
                                            <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                                                <i class="bi bi-bell fs-5"></i>
                                            </div>
                                        </div>
                                        <div id="close_box_alerts_list">
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <i class="bi bi-shield-check fs-5"></i> Sin alertas críticas
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm rounded-4 mb-3">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">Notas Finales del Cierre</h6>
                                <div class="form-floating">
                                    <textarea class="form-control bg-light rounded-4"
                                        placeholder="Observaciones"
                                        id="close_box_notes"
                                        style="height: 100px; resize: none;"></textarea>
                                    <label for="close_box_notes" class="text-muted">Ingrese observaciones relevantes sobre el turno...</label>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning rounded-4 d-flex gap-3 align-items-start p-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-4 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-warning-emphasis mb-1">Confirmación Requerida</h6>
                                <p class="small text-muted mb-0">
                                    Al cerrar la caja, se generará el reporte Z y no podrá realizar nuevas ventas en este turno.
                                    Asegúrese de haber realizado el <strong>Arqueo de Efectivo</strong> previamente.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer border-top bg-white p-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 text-muted">
                    <i class="bi bi-printer me-2"></i>Imprimir Pre-Cierre
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="btnFinalizarCierre" type="button" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-lock-fill me-2"></i> Finalizar Cierre
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>