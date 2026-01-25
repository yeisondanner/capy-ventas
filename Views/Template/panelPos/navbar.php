<?php
$validationCreateBox = (int) validate_permission_app(11, "c", false)['create'];
if (empty($_SESSION[$nameVarBusiness]['logo'])) {
    $logoBusiness = GENERAR_PERFIL . htmlspecialchars($_SESSION[$nameVarBusiness]['business'] ?? 'Negocio', ENT_QUOTES, 'UTF-8');
} else {
    $logoBusiness = base_url() . '/Loadfile/iconbusiness?f=' . $_SESSION[$nameVarBusiness]['logo'];
}
?>
<!-- Navbar-->
<header class="app-header">
    <a class="app-header__logo" href="<?= base_url() ?>/pos/dashboard">
        <img class="p-1 bg-white rounded-5" style="width: 2rem;" src="<?= media() ?>/capysm.png" alt="">
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
                <li><a class="dropdown-item" href="<?= base_url() ?>/pos/Profile"><i class="bi bi-person me-2 fs-5"></i>
                        Perfil</a></li>
                <li><a class="dropdown-item" href="<?= base_url() ?>/pos/LogOut"><i
                            class="bi bi-box-arrow-right me-2 fs-5"></i> Cerrar Sesión</a></li>
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
                        <img src="<?= $logoBusiness ?>" alt="Avatar"
                            class="rounded-circle border border-2 border-white shadow-sm"
                            style="width: 48px; height: 48px; object-fit: cover;">
                        <span
                            class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                    </div>
                    <div class="flex-fill lh-1">
                        <h6 class="mb-1 fw-bold text-dark">
                            <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.85rem;">
                            ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span
                                class="text-primary fw-medium">Cajero</span>
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
                            <select class="form-select border-start-0 py-2 fw-medium" name="selectBox" id="selectBox"
                                required>
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
                            <input type="number" class="form-control border-start-0 text-center fw-bold fs-3 text-dark"
                                name="cash_opening_amount" id="cash_opening_amount" value="0" placeholder="0.00"
                                step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button"
                        class="btn btn-lg btn-light border flex-grow-1 text-muted fw-bold rounded-pill"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="btnOpenBox" type="button"
                        class="btn btn-lg btn-primary flex-grow-1 fw-bold rounded-pill shadow-sm">
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
                            <span id="gestion_box_name">Caja 01 - Principal</span>
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
                                        <h6 class="text-success fw-bold text-uppercase mb-2"
                                            style="font-size: 0.75rem; letter-spacing: 1px;">Total en Caja</h6>
                                        <h2 id="quick_access_total_general" class="display-6 fw-bold text-dark mb-0">
                                            <!-- Aqui carga el total general -->
                                        </h2>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="fs-6 badge border border-success text-success bg-white rounded-pill px-3 py-2">
                                            <i class="bi bi-graph-up-arrow me-1"></i>
                                            <small
                                                id="quick_access_base_amount"><!-- Aqui carga el monto inicial de apertura --></small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="quick_access_card_payment_method" class="row g-3">
                        </div>
                        <div class="card border rounded-4 h-100 shadow-sm">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">
                                    <i class="bi bi-graph-up me-2"></i>Ventas por Hora
                                </h6>
                                <div style="position: relative; height: 260px; width: 100%;">
                                    <canvas id="graphic_sales_hour"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-2 pe-3 border rounded-pill bg-body-tertiary">
                            <div class="position-relative">
                                <img src="<?= $logoBusiness ?>" alt="Avatar"
                                    class="rounded-circle border border-2 border-white shadow-sm"
                                    style="width: 48px; height: 48px; object-fit: cover;">
                                <span
                                    class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                            </div>
                            <div class="flex-fill lh-1">
                                <h6 class="mb-1 fw-bold text-dark">
                                    <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                                </h6>
                                <small class="text-muted" style="font-size: 0.85rem;">
                                    ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span
                                        class="text-primary fw-medium">Cajero</span>
                                </small>
                            </div>
                            <div class="text-opacity-75 opacity-50 px-2">
                                <i class="bi bi-person-badge-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex gap-2 w-100">
                                <button id="btnOpenModalMovement" data-header="1" class="btn btn-primary w-50 rounded-pill py-2 fw-bold">
                                    <i class="bi bi-arrow-left-right me-2"></i> Ingreso
                                </button>
                                <button id="btnOpenModalRetireCash" data-header="1" class="btn btn-danger w-50 rounded-pill py-2 fw-bold">
                                    <i class="bi bi-dash-circle me-2"></i> Retiro
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button id="btnOpenModalArqueoBox"
                                        class="btn btn-outline-light border w-100 p-3 rounded-4 h-100 text-start">
                                        <i class="bi bi-calculator fs-4 mb-1 d-block text-primary"></i>
                                        <span class="fw-bold text-dark d-block">Arqueo</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Contar dinero</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button id="btnOpenModalCloseBox"
                                        class="btn btn-outline-light border w-100 p-3 rounded-4 h-100 text-start">
                                        <i class="bi bi-lock-fill fs-4 mb-1 d-block text-danger"></i>
                                        <span class="fw-bold text-dark d-block">Cierre</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Finalizar turno</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card border rounded-4 flex-fill">
                            <div
                                class="card-header bg-transparent border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                                <h6 id="quick_access_title_list_movements" class="fw-bold mb-0">Últimos Movimientos</h6>
                                <a href="<?= base_url() ?>/pos/boxhistory"
                                    class="text-decoration-none small fw-bold">Ver todos</a>
                            </div>
                            <div id="quick_access_card_list_movements"
                                class="list-group list-group-flush rounded-bottom-4">
                                <div class="list-group-item px-3 py-3 border-bottom-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="bi bi-cart-fill"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">Venta #234
                                            </h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Hace 2 min</small>
                                        </div>
                                        <div class="text-end lh-1">
                                            <span
                                                class="d-block fw-bold text-success">+<?= getCurrency(); ?>50.00</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">Efectivo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item px-3 py-3 border-bottom-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="bi bi-arrow-up-right"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">Retiro Parcial
                                            </h6>
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
                                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                        <div class="flex-fill lh-1">
                                            <h6 class="mb-1 text-dark fw-bold">Apertura</h6>
                                            <small class="text-muted">09:00 AM • Inicio</small>
                                        </div>
                                        <div class="text-end lh-1">
                                            <span
                                                class="d-block text-success fw-bold">+<?= getCurrency(); ?>100.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2 w-100">
                    <button type="button" class="btn btn-light border text-muted fw-bold rounded-4"
                        data-bs-dismiss="modal">
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
                            <span id="arqueo_box_name">Caja 01 - Principal</span>
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
                                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-hdd-rack-fill me-2"></i>Esperado por
                                    Sistema</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-end mb-3">
                                    <div>
                                        <small class="text-uppercase fw-bold text-muted"
                                            style="font-size: 0.7rem;">Efectivo Total</small>


                                        <h3 id="quick_access_arqueo_total_efectivo" class="fw-bold text-dark mb-0">
                                            <?= getCurrency(); ?>950.00
                                        </h3>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-uppercase fw-bold text-muted"
                                            style="font-size: 0.7rem;">Ventas Totales</small>
                                        <h6 id="quick_access_arqueo_total_general" class="mb-0 text-muted">
                                            <?= getCurrency(); ?>12,540.00
                                        </h6>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2" id="quick_access_arqueo_total_payment_method">
                                    <!-- Aqui los demas card de las targetas -->
                                </div>
                            </div>
                        </div>

                        <div class="card border rounded-4 bg-white shadow-sm flex-fill">
                            <div class="p-2" id="quick_access_arqueo_message">
                                <!-- Aqui va el mensaje si es descuadre o esta ok -->
                            </div>
                            <h6 class="px-3 text-muted text-center mt-2 mb-0 small text-uppercase fw-bold">Total contado
                            </h6>
                            <div class="d-flex flex-column px-3 pb-3">
                                <h1 id="quick_access_arqueo_count_efectivo"
                                    class="text-center w-100 mb-2 fw-bold text-primary"><?= getCurrency(); ?>0.00</h1>
                                <div class="d-flex flex-wrap align-items-center w-100 gap-2">
                                    <div id="quick_access_arqueo_diference" class="d-flex gap-2 align-items-center p-1">
                                        <!-- Aqui va la diferencia -->
                                    </div>
                                    <div class="flex-fill">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-4 border-opacity-75"
                                                id="quick_access_arqueo_justificacion" placeholder="Motivo">
                                            <label for="quick_access_arqueo_justificacion" class="text-muted"><i
                                                    class="bi bi-pencil-square me-1"></i>Justificación</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border rounded-4 h-100 bg-white shadow-sm">
                            <div
                                class="card-header bg-transparent border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-muted mb-0"><i
                                        class="bi bi-cash-stack me-2 text-primary"></i>Conteo de Efectivo</h6>
                                <button id="btnLimpiarArqueo"
                                    class="btn btn-sm btn-light border rounded-pill px-3">Limpiar</button>
                            </div>
                            <div class="card-body p-4">
                                <div id="quick_access_arqueo_currency_denominations">
                                    <!-- Denominaciones de monedas -->
                                </div>
                                <div id="quick_access_desgloce_efectivo"
                                    class="d-flex justify-content-between pt-3 border bg-light rounded-4 p-2">
                                    <div class="text-center w-50 border-end">
                                        <small class="text-muted text-uppercase fw-bold"
                                            style="font-size: 0.8rem;">Billetes</small>
                                        <div class="fw-bold text-dark"><?= getCurrency() ?>1,280.00</div>
                                    </div>
                                    <div class="text-center w-50">
                                        <small class="text-muted text-uppercase fw-bold"
                                            style="font-size: 0.8rem;">Monedas</small>
                                        <div class="fw-bold text-dark"><?= getCurrency() ?>65.80</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 text-muted"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="setArqueoCaja" type="button"
                        class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Confirmar Arqueo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cerrar caja -->
<div class="modal fade" id="modalCloseBox" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 bg-white">
                <div>
                    <h4 class="fw-bold mb-1">Cierre de Caja</h4>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Por Cerrar
                        </span>
                        <span id="close_box_name">Caja Principal</span>
                        <span>•</span>
                        <span id="reloj_3" class="fw-bold text-dark"><?= date('H:i:s A'); ?></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card rounded-4 shadow-sm h-100">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">
                                    <i class="bi bi-shop me-2"></i>Resumen Ventas
                                </h6>

                                <div class="bg-light rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <small class="text-muted d-block fw-semibold"
                                                style="font-size: 0.75rem;">Total Generado</small>
                                            <h3 class="fw-bold text-dark mb-0" id="close_box_total_sales">S/ 0.00</h3>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block fw-semibold"
                                                style="font-size: 0.75rem;">Transacc.</small>
                                            <span class="badge bg-white text-dark border shadow-sm"
                                                id="close_box_total_transactions">0</span>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-muted small mb-2" style="font-size: 0.75rem;">Desglose por
                                    Método</h6>
                                <div id="close_box_total_payment_method" class="d-flex flex-column gap-2">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card rounded-4 shadow-sm h-100">
                            <div class="card-body p-3 d-flex flex-column">
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">
                                    <i class="bi bi-calculator me-2"></i>Conciliación
                                </h6>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Fondo Inicial</span>
                                    <span class="fw-bold text-dark" id="close_box_base">S/ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Ingresos Efectivo</span>
                                    <span class="fw-bold text-success" id="close_box_income">+S/ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="text-muted">Egresos</span>
                                    <span class="fw-bold text-danger" id="close_box_expenses">-S/ 0.00</span>
                                </div>
                                <hr class="my-2 border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-muted">Esperado (Sistema):</span>
                                    <span class="fw-bold text-dark" id="close_box_expected">S/ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-muted">Contado (Arqueo):</span>
                                    <span class="fw-bold text-primary" id="close_box_contado">S/ 0.00</span>
                                </div>
                                <div
                                    class="d-flex justify-content-between align-items-center mt-2 p-2 bg-light rounded-3 border">
                                    <span class="small fw-bold text-dark">Diferencia:</span>
                                    <span class="fw-bold fs-5" id="close_box_difference">S/ 0.00</span>
                                </div>
                                <div id="close_box_status_container" class="mt-3"></div>
                                <span class="d-none" id="close_box_sistema"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control bg-light rounded-3" placeholder="Observaciones"
                                        id="close_box_notes" style="height: 80px; resize: none;"></textarea>
                                    <label for="close_box_notes" class="text-muted opacity-75">Notas finales del
                                        cierre...</label>
                                </div>
                                <div
                                    class="d-flex gap-2 align-items-start border border-warning text-warning-emphasis bg-warning-subtle p-2 rounded-3 small">
                                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                                    <div style="line-height: 1.3;">
                                        <strong>Acción Irreversible:</strong> Al confirmar, se cerrará el turno y se
                                        generará el reporte Z. Asegúrese de haber validado el arqueo.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-white p-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 text-muted"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-printer me-2"></i>Reporte
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button id="btnFinalizarCierre" type="button"
                        class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-lock-fill me-2"></i> Finalizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Registrar nuevo movimiento -->
<div class="modal fade" id="modalMovementBox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 py-3 bg-primary text-white">
                <h5 class="fw-bold mb-0">Venta Rápida</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 p-2 pe-3 border rounded-pill bg-body-tertiary mb-3">
                    <div class="position-relative">
                        <img src="<?= $logoBusiness ?>" alt="Avatar"
                            class="rounded-circle border border-2 border-white shadow-sm"
                            style="width: 48px; height: 48px; object-fit: cover;">
                        <span
                            class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                    </div>
                    <div class="flex-fill lh-1">
                        <h6 class="mb-1 fw-bold text-dark">
                            <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.85rem;">
                            ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span
                                class="text-primary fw-medium">Cajero</span>
                        </small>
                    </div>
                    <div class="text-opacity-75 opacity-50 px-2">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2" for="movement_customer">Cliente (<span
                            class="text-danger">*</span>)</label>
                    <select class="form-select" id="movement_customer" name="movement_customer">
                    </select>
                </div>
                <input type="hidden" id="movement_type" value="Ingreso">
                <div class="mb-4 item-box">
                    <label class="small text-muted fw-bold text-uppercase mb-2">Monto del movimiento (<span
                            class="text-danger">*</span>)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-transparent text-muted ps-5">S/</span>
                        <input type="number" id="movement_amount"
                            class="form-control fw-bold fs-1 shadow-none bg-white text-end" min="0" step="0.1"
                            placeholder="0.0" style="margin-left: -10px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="mb-4 item-box">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="movement_check_tax">
                                <label id="label_tax_name" class="small text-muted fw-bold text-uppercase mb-2">IGV</label>
                            </div>
                            <div class="input-group">
                                <span id="span_tax" class="input-group-text bg-dark-subtle text-muted fw-bold">18%</span>
                                <input disabled type="text" id="movement_tax"
                                    class="form-control fw-bold bg-dark-subtle shadow-none text-end" value="S/ 0.00"
                                    placeholder="0.0">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="mb-4 item-box">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Total</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted fw-bold bg-white"></span>
                                <input disabled type="text" id="movement_total"
                                    class="form-control fw-bold shadow-none bg-white text-end" value="0.00"
                                    placeholder="0.0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2" for="movement_payment_method">Metodo de pago (<span
                            class="text-danger">*</span>)</label>
                    <select class="form-select" id="movement_payment_method" name="movement_payment_method">
                        <option disabled>Seleccionar</option>
                        <option value="1" selected>Efectivo</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control bg-white rounded-4" placeholder="Motivo" id="movement_description"
                        style="height: 100px; resize: none;"></textarea>
                    <label for="movement_description" class="text-muted">Descripción o Motivo</label>
                </div>
                <div class="alert alert-light border border-2 rounded-4 d-flex align-items-center gap-3 p-3">
                    <div class="bg-success-subtle text-success py-1 px-2 rounded-circle" id="movement_icon_wrapper">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                    <div class="small text-muted lh-sm">
                        Este movimiento afectará el <strong>Arqueo Final</strong> y el saldo actual de la caja.
                    </div>
                </div>
                <div class="d-grid">
                    <button type="button" id="btnSaveMovement"
                        class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Registrar Ingreso
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Retirar movimiento movimiento -->
<div class="modal fade" id="modalRetireMovementBox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 py-3 bg-danger text-white">
                <h5 class="fw-bold mb-0">Retiro Rápido</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 p-2 pe-3 border rounded-pill bg-body-tertiary mb-3">
                    <div class="position-relative">
                        <img src="<?= $logoBusiness ?>" alt="Avatar"
                            class="rounded-circle border border-2 border-white shadow-sm"
                            style="width: 48px; height: 48px; object-fit: cover;">
                        <span
                            class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                    </div>
                    <div class="flex-fill lh-1">
                        <h6 class="mb-1 fw-bold text-dark">
                            <?= ucwords(strtolower($_SESSION[$nameVarLoginInfo]['name'] . " " . $_SESSION[$nameVarLoginInfo]['lastname'])) ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.85rem;">
                            ID: <?= $_SESSION[$nameVarLoginInfo]['idUser'] ?> <span class="mx-1">•</span> <span
                                class="text-primary fw-medium">Cajero</span>
                        </small>
                    </div>
                    <div class="text-opacity-75 opacity-50 px-2">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2" for="retire_supplier">Proveedor (<span
                            class="text-danger">*</span>)</label>
                    <select class="form-select" id="retire_supplier" name="retire_supplier">
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2" for="retire_expense_category">Categoría de Gasto (<span
                            class="text-danger">*</span>)</label>
                    <select class="form-select" id="retire_expense_category" name="retire_expense_category">
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4 item-box">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Monto de Gasto (<span
                                    class="text-danger">*</span>)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-muted">S/</span>
                                <input type="number" id="retire_amount"
                                    class="form-control fw-bold shadow-none bg-white" min="0" step="0.1"
                                    placeholder="0.0" style="margin-left: -10px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Fecha (<span
                                    class="text-danger">*</span>)</label>
                            <div class="input-group">
                                <input type="datetime-local" id="retire_date"
                                    class="form-control shadow-none bg-white" value="<?= date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2" for="retire_payment_method">Metodo de pago (<span
                            class="text-danger">*</span>)</label>
                    <select class="form-select" id="retire_payment_method" name="retire_payment_method">
                        <option disabled>Seleccionar</option>
                        <option value="1" selected>Efectivo</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="small text-muted fw-bold text-uppercase mb-2">Nombre del gasto</label>
                    <div class="input-group">
                        <input placeholder="Gasto en ..." type="text" id="retire_name"
                            class="form-control shadow-none bg-white">
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control bg-white rounded-4" placeholder="Descripción o Comentario" id="retire_description"
                        style="height: 100px; resize: none;"></textarea>
                    <label for="retire_description" class="text-muted">Descripción o Comentario</label>
                </div>
                <div class="alert alert-light border border-2 rounded-4 d-flex align-items-center gap-3 p-3">
                    <div class="bg-danger-subtle text-danger py-1 px-2 rounded-circle" id="movement_icon_wrapper">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                    <div class="small text-muted lh-sm">
                        Este movimiento afectará el <strong>Arqueo Final</strong> y el saldo actual de la caja.
                    </div>
                </div>
                <div class="d-grid">
                    <button type="button" id="btnSaveRetireCash"
                        class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-dash-circle me-2"></i> Registrar Retiro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>