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
                        <h6 class="mb-0 fw-bold"><?= $_SESSION[$nameVarLoginInfo]['name']." ".$_SESSION[$nameVarLoginInfo]['lastname'] ?></h6>
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
                        <select class="form-select" name="select_box" required="" id="select_box">
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
                        <input type="number" class="form-control fs-4" name="update_documentNumber" id="update_documentNumber" required="" placeholder="0.00" step="0.01" min="0">
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