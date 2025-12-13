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
                <div class="d-flex justify-content-between align-items-start">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
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
                                    <button class="btn btn-outline-light border w-100 p-3 rounded-4 h-100 text-start">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
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
                                        <h3 class="fw-bold text-dark mb-0"><?= getCurrency(); ?>950.00</h3>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Ventas Totales</small>
                                        <h6 class="mb-0 text-muted"><?= getCurrency(); ?>12,540.00</h6>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="flex-fill p-2 rounded-4 bg-body-tertiary border text-center">
                                        <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem;">TARJETA</small>
                                        <span class="fw-bold text-dark"><?= getCurrency() ?>200.00</span>
                                    </div>
                                    <div class="flex-fill p-2 rounded-4 bg-body-tertiary border text-center">
                                        <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem;">DIGITAL (QR)</small>
                                        <span class="fw-bold text-dark"><?= getCurrency() ?>120.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border rounded-4 bg-white shadow-sm flex-fill">
                            <div class="p-2">
                                <div class="alert alert-danger d-flex align-items-center gap-2 p-2 rounded-4 mb-0" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Descuadre detectado</strong>
                                </div>
                            </div>
                            <h6 class="px-3 text-muted text-center mt-2 mb-0 small text-uppercase fw-bold">Total contado</h6>
                            <div class="d-flex flex-column px-3 pb-3">
                                <h1 class="text-center w-100 mb-2 fw-bold text-primary"><?= getCurrency(); ?>950.00</h1>
                                <div class="d-flex flex-wrap align-items-center w-100 gap-2">
                                    <div class="d-flex gap-2 align-items-center p-1">
                                        <p class="mb-0 fw-bold small text-muted">Diferencia:</p>
                                        <div class="card rounded-4 border-danger bg-danger-subtle">
                                            <h5 class="mb-0 px-3 py-1 text-danger fw-bold"><?= getCurrency() ?>22.80</h5>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-4 border-opacity-75" id="txtJustification" placeholder="Motivo">
                                            <label for="txtJustification" class="text-muted"><i class="bi bi-pencil-square me-1"></i>Justificación</label>
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
                                <button class="btn btn-sm btn-light border rounded-pill px-3">Limpiar</button>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <h6 class="fw-bold text-success mb-0 me-3" style="min-width: 80px;"><i class="bi bi-cash"></i> BILLETES</h6>
                                    <div class="flex-grow-1 border-bottom"></div>
                                </div>
                                <div class="row g-2 mb-4">
                                    <?php
                                    $billetes = [200, 100, 50, 20, 10];
                                    foreach ($billetes as $val): ?>
                                        <div class="col-6 item-box">
                                            <div class="input-group">
                                                <span class="input-group-text bg-success-subtle text-success fw-bold border-end-0" style="width: 65px;"><?= getCurrency() . $val ?></span>
                                                <input type="number" class="form-control border-start-0 bg-light" placeholder="0" min="0">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <h6 class="fw-bold text-warning mb-0 me-3" style="min-width: 80px;"><i class="bi bi-coin"></i> MONEDAS</h6>
                                    <div class="flex-grow-1 border-bottom"></div>
                                </div>
                                <div class="row g-2">
                                    <?php
                                    $monedas = [5, 2, 1, 0.50, 0.20, 0.10];
                                    foreach ($monedas as $val): ?>
                                        <div class="col-4 item-box">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-warning-subtle text-warning fw-bold border-end-0"><?= $val < 1 ? $val : getCurrency() . $val ?></span>
                                                <input type="number" class="form-control border-start-0 bg-light" placeholder="0">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="d-flex justify-content-between mt-4 pt-3 border-top bg-light rounded-3 p-2">
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
                    <button type="button" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Confirmar Arqueo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>