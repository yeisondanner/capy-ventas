<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-wallet"></i> Creditos</h1>
            <p>Administra los creditos de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/credits">Creditos</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill gap-2">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5"
                            href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i>
                            Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5" aria-current="page"
                            href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active"
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
                    <h6 class="text-center text-primary mb-3">Filtrar Creditos</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <div class="flex-fill __filter_col">
                            <label for="filter-search"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Nombre del
                                Cliente:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="filter-search"
                                    placeholder="Nombre del cliente o DNI">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-container">
                            <label for="filter-date-start"
                                class="text-muted fw-bold d-block text-uppercase small form-label" id="date-label">Fecha
                                de Inicio:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date-start" class="form-control"
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="flex-fill" id="date-container">
                            <label for="filter-date-end"
                                class="text-muted fw-bold d-block text-uppercase small form-label" id="date-label">Fecha
                                de Fin:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date-end" class="form-control"
                                    value="<?= date('Y-m-d') ?>">
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
        <div class="col-12 mb-2">
            <div class="p-3 border-start border-4 border-info bg-light shadow-sm rounded">
                <p class="mb-0 text-primary">
                    <span class="fw-bold text-info text-uppercase small">Información:</span><br>
                    Para que se muestre el cliente en la lista, el cliente debe tener una fecha de facturación
                    establecida.
                </p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive"
                            id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Cliente</th>
                                    <th title="Fecha de Facturación">Fecha de Fac.</th>
                                    <th title="Limite de Crédito">Limite de Cré.</th>
                                    <th title="Saldo Pendiente">Saldo Pend.</th>
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
<?= footerPos($data) ?>

<!-- Modal reporte de creditos -->
<div class="modal fade" id="creditsReportModal" tabindex="-1" aria-labelledby="creditsReportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-dark bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-wallet fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="creditsReportModalLabel">Reporte de Créditos</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras ver el reporte de creditos</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body" id="creditsReportContainer">
                <!-- ======================================================= -->
                <!--  CONTENIDO DEL CUERPO (MODAL-BODY)     -->
                <!-- ======================================================= -->
                <div class="row g-0 h-100">

                    <!-- 1. PANEL IZQUIERDO: PERFIL DEL CLIENTE -->
                    <div class="col-lg-3 bg-body-tertiary border rounded-2">
                        <div class="p-4 h-100 d-flex flex-column">

                            <!-- Foto y Datos Principales -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <!-- Placeholder de imagen -->
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 90px; height: 90px; font-size: 2.5rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <span
                                        class="position-absolute bottom-0 end-0 badge rounded-pill border border-white"
                                        id="detailCustomerStatus">--</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" id="detailCustomerName">--</h5>
                                <p class="text-muted small mb-0" id="detailCustomerCode">--</p>
                            </div>

                            <!-- Barra de Crédito (Visual) -->
                            <div class="card bg-white border-0 shadow-sm mb-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="fw-bold text-muted">Línea de Crédito</span>
                                        <span class="text-danger fw-bold" id="detailCustomerPercentConsu">--</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            id="detailCustomerIndicadorPercent" style="width: 62%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 small tabular-nums">
                                        <span class="text-muted">Disp: <span class="text-success fw-bold"
                                                id="detailCustomerAmountDisp">--</span></span>
                                        <span class="text-muted" id="detailCustomerCreditLimit">--</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Datos de Contacto (BD: phone_number, email, direction) -->
                            <div class="mb-4 small">
                                <p class="fw-bold text-dark mb-2">Datos de Contacto</p>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-card-heading text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.8em;">DOCUMENTO</span>
                                        <span class="fw-medium text-dark" id="detailCustomerDocument">45.890.123</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-telephone text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted"
                                            style="font-size: 0.8em;">TELÉFONO/CELULAR</span>
                                        <span class="fw-medium text-dark" id="detailCustomerPhone">+51 987 654
                                            321</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-geo-alt text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.8em;">DIRECCIÓN</span>
                                        <span class="fw-medium text-dark" id="detailCustomerDirection">Av.
                                            Principal 123, Lima</span>
                                    </div>
                                </div>
                                <p class="fw-bold text-dark mb-2">Datos de financiamiento</p>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-credit-card text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.8em;">LIMITE DE
                                            CREDITO</span>
                                        <span class="fw-medium text-dark"
                                            id="detailCustomerCreditLimitFinancing">--</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-percent text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.8em;">INTERES MENSUAL X
                                            MORA</span>
                                        <span class="fw-medium text-dark" id="detailCustomerMonthlyInterest">--</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-calendar-check text-secondary fs-5 me-3"></i>
                                    <div>
                                        <span class="d-block text-muted" style="font-size: 0.8em;">INTERES
                                            FINANCIAMIENTO</span>
                                        <span class="fw-medium text-dark"
                                            id="detailCustomerMonthlyInterestFinancing">--</span>
                                    </div>
                                </div>

                            </div>

                            <!-- Footer Sidebar -->
                            <div class="mt-auto pt-3 border-top text-center small text-muted">
                                <i class="bi bi-calendar-event me-1"></i> Facturación: <strong
                                    id="detailCustomerBillingDay">Día 15</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2. PANEL DERECHO: MOVIMIENTOS Y DEUDA -->
                    <div class="col-lg-9 bg-white">
                        <div class="d-flex flex-column h-100">

                            <!-- Encabezado Sección Derecha -->
                            <div class="py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-1"></i> Estado de Cuenta</h5>
                            </div>

                            <!-- Filtros de Fecha -->
                            <div class="px-4 py-3 border-bottom bg-white">
                                <div class="row g-2 align-items-end">
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-12 col-xl-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Fecha
                                            Inicio</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="date" class="form-control" id="modal-filter-date-start">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Fecha
                                            Fin</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="date" class="form-control" id="modal-filter-date-end">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Tipo
                                            venta</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                            <select class="form-select" id="modal-filter-sale-type">
                                                <option value="All">Todos</option>
                                                <option value="Credito">Crédito</option>
                                                <option value="Contado">Contado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Estado
                                            Pago</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                            <select class="form-select" id="modal-filter-payment-status">
                                                <option value="All">Todos</option>
                                                <option value="Pagado">Pagado</option>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Anulado">Anulado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-4">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary flex-fill"
                                                id="modal-filter-btn"><i class="bi bi-funnel"></i> Filtrar</button>
                                            <button class="btn btn-sm btn-outline-secondary flex-fill"
                                                id="modal-filter-reset"><i class="bi bi-arrow-counterclockwise"></i>
                                                Limpiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KPIs Rápidos -->
                            <div class="row g-0 border-bottom bg-light">
                                <div class="col-4 border-end p-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Total
                                        Comprado</small>
                                    <div class="h5 mb-0 fw-bold tabular-nums text-dark"
                                        id="detailCustomerTotalPurchased">S/. --</div>
                                </div>
                                <div class="col-4 border-end p-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"
                                        style="font-size: 0.7rem;">Pagado</small>
                                    <div class="h5 mb-0 fw-bold tabular-nums text-success" id="detailCustomerTotalPaid">
                                        S/. --</div>
                                </div>
                                <div class="col-4 p-3 text-center bg-danger bg-opacity-10">
                                    <small class="text-uppercase text-danger fw-bold" style="font-size: 0.7rem;">Deuda
                                        Pendiente</small>
                                    <div class="h5 mb-0 fw-bold tabular-nums text-danger" id="detailCustomerTotalDebt">
                                        S/. --</div>
                                </div>
                            </div>

                            <!-- Tabla Scrollable -->
                            <div class="flex-grow-1 overflow-auto p-0" style="max-height: 60vh;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th class="ps-4 py-3 small text-muted text-uppercase fw-bold border-0">
                                                Fecha</th>
                                            <th class="py-3 small text-muted text-uppercase fw-bold border-0">
                                                Comprobante</th>
                                            <th class="py-3 small text-muted text-uppercase fw-bold border-0 text-end">
                                                Monto</th>
                                            <th
                                                class="py-3 small text-muted text-uppercase fw-bold border-0 text-center">
                                                Estado</th>
                                            <th class="py-3 small text-muted text-uppercase fw-bold border-0 text-center">
                                                <input type="checkbox" name="checkAllCredits" id="checkAllCredits" class="form-check-input">
                                            </th>
                                            <th
                                                class="pe-4 py-3 small text-muted text-uppercase fw-bold border-0 text-end">
                                                Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customerSalesBody">
                                        <!-- Movimiento 1 (Pagado / Contado) -->
                                        <tr>
                                            <td class="ps-4 text-nowrap">
                                                <div class="fw-medium">01/10/2023</div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">Factura F001-204</div>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10"
                                                    style="font-size: 0.65em;">CONTADO</span>
                                            </td>
                                            <td class="text-end tabular-nums">S/. 2,400.00</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-success-subtle text-success">Pagado</span>
                                            </td>
                                            <td class="text-end pe-4"><button class="btn btn-sm btn-light border"><i
                                                        class="bi bi-eye"></i></button></td>
                                        </tr>
                                        <!-- Movimiento 2 (Pagado / Crédito) -->
                                        <tr>
                                            <td class="ps-4 text-nowrap">
                                                <div class="fw-medium">05/10/2023</div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">Boleta B001-089</div>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-10"
                                                    style="font-size: 0.65em;">CRÉDITO</span>
                                            </td>
                                            <td class="text-end tabular-nums">S/. 450.00</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-success-subtle text-success">Pagado</span>
                                            </td>
                                            <td class="text-end pe-4"><button class="btn btn-sm btn-light border"><i
                                                        class="bi bi-eye"></i></button></td>
                                        </tr>

                                        <!-- Movimiento 3 (DEUDA) -->
                                        <!-- Nota: table-danger para resaltar fila completa -->
                                        <tr class="table-danger border-start border-4 border-danger">
                                            <td class="ps-4 text-nowrap fw-bold text-danger">
                                                <div class="fw-medium">12/10/2023</div>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10"
                                                    style="font-size: 0.65em;">Vencido 4 dias</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">Factura F001-215</div>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10"
                                                    style="font-size: 0.65em;">CRÉDITO</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger tabular-nums">S/. 1,200.00</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-danger text-white">Pendiente</span>
                                            </td>
                                            <td class="text-end pe-4"><button
                                                    class="btn btn-sm btn-dark px-3 rounded-pill shadow-sm">Pagar</button>
                                            </td>
                                        </tr>

                                        <!-- Movimiento 4 (DEUDA) -->
                                        <tr class="table-danger border-start border-4 border-danger">
                                            <td class="ps-4 text-nowrap fw-bold text-danger">
                                                <div class="fw-medium">15/10/2023</div>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10"
                                                    style="font-size: 0.65em;">A tiempo</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">Factura F001-220</div>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10"
                                                    style="font-size: 0.65em;">CRÉDITO</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger tabular-nums">S/. 350.50</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-danger text-white">Pendiente</span>
                                            </td>
                                            <td class="text-end pe-4"><button
                                                    class="btn btn-sm btn-dark px-3 rounded-pill shadow-sm">Pagar</button>
                                            </td>
                                        </tr>
                                        <tr class="table-danger border-start border-4 border-danger">
                                            <td class="ps-4 text-nowrap fw-bold text-danger">
                                                <div class="fw-medium">15/10/2023</div>
                                                <span class="badge bg-warning text-dark border border-warning"
                                                    style="font-size: 0.65em;">2 dias por vencer</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">Factura F001-220</div>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10"
                                                    style="font-size: 0.65em;">CRÉDITO</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger tabular-nums">S/. 350.50</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-danger text-white">Pendiente</span>
                                            </td>
                                            <td class="text-end pe-4"><button
                                                    class="btn btn-sm btn-dark px-3 rounded-pill shadow-sm">Pagar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- ======================================================= -->
                <!-- FIN DEL CONTENIDO                                       -->
                <!-- ======================================================= -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i
                        class="bi bi-x-lg"></i></button>
                <button class="btn btn-outline-primary shadow-sm" style="transition: all 300ms ease-in-out;" disabled
                    id="btn-pay-selected-credits" onclick="alert('En desarrollo')"><i class="bi bi-ban me-1"></i> No disponible</button>
            </div>
        </div>
    </div>
</div>
<!--Fin del modal de reporte de creditos -->