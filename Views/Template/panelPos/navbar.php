<!-- Navbar-->
<header class="app-header">
    <a class="app-header__logo" href="<?= base_url() ?>/pos/dashboard">Capy Ventas</a>
    <!-- Sidebar toggle button-->
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav gap-2">
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#openRegisterModal"><i class="bi bi-cash"></i>Caja</button>
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
<!-- ========================================== -->
<!-- MODAL 1: APERTURA (Estilo Puro Bootstrap) -->
<!-- ========================================== -->
<div class="modal fade" id="openRegisterModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header: bg-success para verde, bg-gradient para degradado sutil -->
            <div class="modal-header bg-success bg-gradient text-white px-4 py-2">
                <div>
                    <h4 class="modal-title fw-bold mb-0">Apertura de Caja</h4>
                    <small class="opacity-75">Turno #2459 - Mañana</small>
                </div>
                <i class="bi bi-shop-window display-6 opacity-50"></i>
            </div>

            <div class="modal-body p-4 p-lg-5">
                <form id="openRegisterForm">
                    <div class="row mb-4 g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-body-tertiary d-flex align-items-center h-100">
                                <div class="bg-white rounded-circle p-2 text-success border me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person-fill h4 mb-0"></i>
                                </div>
                                <div>
                                    <div class="text-secondary small text-uppercase fw-bold">Cajero Asignado</div>
                                    <div class="fw-bold fs-5 text-dark"><?= $_SESSION[$nameVarLoginInfo]['fullName'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-body-tertiary d-flex align-items-center h-100">
                                <div class="bg-white rounded-circle p-2 text-primary border me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-pc-display-horizontal h4 mb-0"></i>
                                </div>
                                <div>
                                    <div class="text-secondary small text-uppercase fw-bold">Terminal / Caja</div>
                                    <div class="fw-bold fs-5 text-primary">CAJA PRINCIPAL 01</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Monto Inicial (Base)</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-light text-muted fw-bold border-end-0"><?= getCurrency() ?></span>
                            <input type="number" class="form-control fw-bold fs-2 text-center text-success border-start-0"
                                id="initialAmount" value="150.00" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg py-3 fw-bold shadow">
                            <i class="bi bi-unlock-fill me-2"></i>CONFIRMAR APERTURA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>