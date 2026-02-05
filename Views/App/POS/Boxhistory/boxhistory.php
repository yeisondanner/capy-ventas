<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-cash"></i> Historial de cierre de caja</h1>
            <p>Administra los movimientos de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/boxhistory">Historial de cierre de caja</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5"
                            href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i>
                            Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active" aria-current="page"
                            href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5"
                            href="<?= base_url() ?>/pos/credits"><i class="bi bi-wallet fs-4"></i> Creditos</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body border p-2 rounded-3 bg-light">
                    <h6 class="text-center text-primary mb-3">Filtrar Cierrres de caja</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <div class="flex-fill __filter_col">
                            <label for="filter-type"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Tipo de
                                Filtro:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-filter"></i></span>
                                <select id="filter-type" class="form-select">
                                    <option value="daily">Diario</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="monthly">Mensual</option>
                                    <option value="yearly">Anual</option>
                                    <option value="all">Todos</option>
                                    <option value="custom">Rango Personalizado</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex-fill" id="date-container">
                            <label for="filter-date" class="text-muted fw-bold d-block text-uppercase small form-label"
                                id="date-label">Fecha:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-range-container" style="display: none;">
                            <label for="min-date"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Desde:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="min-date" class="form-control">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-to-container" style="display: none;">
                            <label for="max-date"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Hasta:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="max-date" class="form-control">
                            </div>
                        </div>
                        <div class="flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
                            <button id="filter-btn" class="btn_filter flex-fill btn btn-outline-primary me-2"><i
                                    class="bi bi-funnel"></i> Filtrar</button>
                            <button id="reset-btn" class="btn_clean flex-fill btn btn-outline-secondary "><i
                                    class="bi bi-arrow-counterclockwise"></i> Limpiar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive"
                            id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Usuario</th>
                                    <th>Se abrió</th>
                                    <th>Se cerró</th>
                                    <th>Diferencia</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Reporte Caja Cerrada -->
<div class="modal fade" id="boxSessionModal" tabindex="-1" aria-labelledby="boxSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-info border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="boxSessionModalLabel">Reporte de Cierre de Caja</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras ver el reporte de cierre de caja</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body">
                <div id="voucherContainer" class="p-4 bg-white">

                    <!-- ENCABEZADO -->
                    <div class="text-center mb-4">
                        <img src="" alt="" id="logo_business" class="img-fluid mb-2" style="max-height: 60px;">
                        <h5 class="fw-bold text-uppercase mb-1" id="name_business">NOMBRE DEL NEGOCIO</h5>
                        <div class="text-muted small" id="direction_business">Dirección del negocio</div>
                        <div class="text-muted small">
                            RUC / Doc: <span id="document_business" class="fw-semibold">00000000000</span>
                        </div>
                    </div>

                    <!-- TARJETAS DE INFORMACION -->
                    <div class="row g-3 mb-4">
                        <!-- Usuario y Caja -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle fs-4 text-primary me-2"></i>
                                    <span class="fw-bold text-muted small text-uppercase">Responsable</span>
                                </div>
                                <h6 id="user_fullname" class="mb-0 fw-bold">Nombre Usuario</h6>
                                <small id="box_name" class="text-muted">Caja 1</small>
                            </div>
                        </div>
                        <!-- Fechas -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock-history fs-4 text-info me-2"></i>
                                    <span class="fw-bold text-muted small text-uppercase">Sesión</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Apertura:</span>
                                    <span id="opening_date" class="fw-semibold">--/--/---- --:--</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Cierre:</span>
                                    <span id="closing_date" class="fw-semibold">--/--/---- --:--</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RESUMEN FINANCIERO -->
                    <div class="card border-0 shadow-sm mb-4" style="background-color: #f8f9fa;">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-uppercase mb-3 border-bottom pb-2">
                                <i class="bi bi-wallet2 me-2"></i>Resumen Financiero
                            </h6>
                            <div class="row text-center row-cols-3 g-2">
                                <div class="col border-end">
                                    <small class="text-muted d-block mb-1">Monto Inicial</small>
                                    <span id="initial_amount" class="fw-bold fs-6 text-secondary">0.00</span>
                                </div>
                                <div class="col border-end">
                                    <small class="text-muted d-block mb-1">Monto Contado</small>
                                    <span id="counted_amount" class="fw-bold fs-6 text-dark">0.00</span>
                                </div>
                                <div class="col">
                                    <small class="text-muted d-block mb-1">Diferencia</small>
                                    <span id="difference_amount" class="fw-bold fs-6">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NOTAS -->
                    <div class="mb-4">
                        <h6 class="fw-bold small text-uppercase mb-2"><i class="bi bi-sticky me-1"></i>Notas de Cierre
                        </h6>
                        <div class="p-3 bg-light rounded fst-italic text-muted border-start border-4 border-info small"
                            id="session_notes">
                            Sin notas.
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- GRAFICAS (New) -->
                    <div class="row mb-4">
                        <div class="col-12 mb-3">
                            <h6 class="fw-bold text-uppercase text-center mb-3 text-muted small">Gráficas de Sesión</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-2">
                                <canvas id="financialChart" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" id="movementsChartContainer">
                            <div class="border rounded p-2">
                                <canvas id="movementsChart" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- HISTORIAL DE ARQUEOS (Timeline Style) -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase mb-3"><i class="bi bi-list-check me-2"></i>Historial de
                            Arqueos</h6>
                        <div id="counts_history_container" class="vstack gap-3">
                            <!-- Contenido generado por JS -->
                        </div>
                    </div>

                    <!-- MOVIMIENTOS DE CAJA -->
                    <div id="movements_general_container" style="display:none;">
                        <h6 class="fw-bold text-uppercase mb-3 mt-4"><i
                                class="bi bi-arrow-left-right me-2"></i>Movimientos de Caja</h6>
                        <div id="movements_history_container" class="vstack gap-2">
                            <!-- Contenido generado por JS -->
                        </div>
                    </div>

                    <!-- PIE -->
                    <div class="mt-5 text-center small text-muted">
                        <div>Reporte generado automáticamente el <?= date('d/m/Y H:i') ?></div>
                        <div class="fst-italic">Sistema de Ventas</div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="download-png"><i class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= footerPos($data) ?>