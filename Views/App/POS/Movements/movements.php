<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-pc-display-horizontal"></i> Movimientos</h1>
            <p>Administra los movimientos de tu negocio de ingresos y egresos</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/inventory">Movimientos</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill gap-2">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active" aria-current="page"
                            href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i>
                            Movimientos</a>
                    </li>
                    <?php
                    $readHC = validate_permission_app(12, "r", false)['read'];
                    if ($readHC === 1): ?>
                        <li class="nav-item">
                            <a class="nav-link border border-primary shadow-sm rounded-5"
                                href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Historial de
                                cierres de caja</a>
                        </li>
                    <?php endif; ?>
                    <?php
                    $readCR = validate_permission_app(15, "r", false)['read'];
                    if ($readCR === 1): ?>
                        <li class="nav-item">
                            <a class="nav-link border border-primary shadow-sm rounded-5"
                                href="<?= base_url() ?>/pos/credits"><i class="bi bi-wallet fs-4"></i> Creditos</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body bg-light border p-2 rounded-3">
                    <h6 class="text-center text-primary mb-3">Filtrar Movimientos</h6>
                    <div class="d-flex flex-wrap gap-1 __filter-container">
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
                                    <option value="custom">Rango Personalizado</option>
                                </select>
                            </div>
                        </div>

                        <div class="__fecha flex-fill" id="date-container">
                            <label for="filter-date" class="text-muted fw-bold d-block text-uppercase small form-label"
                                id="date-label">Fecha:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="__fecha flex-fill" id="date-range-container" style="display: none;">
                            <label for="min-date"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Desde:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="min-date" class="form-control">
                            </div>
                        </div>

                        <div class="__fecha flex-fill" id="date-to-container" style="display: none;">
                            <label for="max-date"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Hasta:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="max-date" class="form-control">
                            </div>
                        </div>

                        <div class="__search flex-fill">
                            <label for="search-concept"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Buscar por
                                Concepto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="search-concept" class="form-control"
                                    placeholder="Concepto de venta/egreso...">
                            </div>
                        </div>

                        <div
                            class="__buttons flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
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
                <div class="tile-body  bg-light border p-2 rounded-3">
                    <div class="row g-3">
                        <div class="col-12 col-sm-12 col-md-4">
                            <div class="card border-0 shadow-sm h-100 card-enhanced rounded-5">
                                <div class="card-body d-flex align-items-center">
                                    <div
                                        class="icon-container bg-success-subtle d-flex align-items-center justify-content-center me-3">
                                        <i class="bi bi-graph-up-arrow fs-4 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Balance</div>
                                        <div class="fw-semibold fs-5" id="balance">
                                            <?= getCurrency() . ' ' . number_format($data['totals']['balance'], 2, '.', ',') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card border-0 shadow-sm h-100 card-enhanced rounded-5">
                                <div class="card-body d-flex align-items-center">
                                    <div
                                        class="icon-container bg-success-subtle d-flex align-items-center justify-content-center me-3">
                                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Ventas totales</div>
                                        <div class="fw-semibold fs-5 text-success" id="totalSales">
                                            <?= getCurrency() . ' ' . number_format($data['totals']['total_sales'], 2, '.', ',') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card border-0 shadow-sm h-100 card-enhanced rounded-5">
                                <div class="card-body d-flex align-items-center">
                                    <div
                                        class="icon-container bg-danger-subtle d-flex align-items-center justify-content-center me-3">
                                        <i class="bi bi-cash-stack fs-4 text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Gastos totales</div>
                                        <div class="fw-semibold fs-5 text-danger" id="totalExpenses">
                                            <?= getCurrency() . ' ' . number_format($data['totals']['total_expenses'], 2, '.', ',') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <!-- Grupo de Radio Buttons -->
                <div class="d-flex gap-2 mb-3">

                    <!-- Radio Ingresos (Seleccionado por defecto) -->
                    <div class="flex-fill">
                        <input type="radio" class="btn-check btn-movement" name="movementType" id="radio-income"
                            autocomplete="off" value="income" checked>
                        <label class="btn btn-outline-success w-100 rounded-5 border border-success py-2"
                            for="radio-income">
                            <i class="bi bi-plus"></i> Ingresos
                        </label>
                    </div>

                    <!-- Radio Egresos -->
                    <div class="flex-fill">
                        <input type="radio" class="btn-check btn-movement" name="movementType" id="radio-expense"
                            autocomplete="off" value="expense">
                        <label class="btn btn-outline-danger w-100 rounded-5 border border-danger py-2"
                            for="radio-expense">
                            <i class="bi bi-dash"></i> Egresos
                        </label>
                    </div>

                </div>
                <div class="tile-body">
                    <div class="bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped display nowrap w-100"
                            id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Concepto</th>
                                    <th>Valor</th>
                                    <th>Medios de Pago</th>
                                    <th>Nombre de Vendedor</th>
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
<?= footerPos($data) ?>
<!-- Modal Comprobante -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-dark bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="voucherModalLabel">Comprobante de Venta</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras ver la nota de venta</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body" id="voucherContainer" style="background-color: #f8f9fa; border-radius: 0 0 8px 8px;">
                <div class="ticket-wrapper">
                    <div class="ticket-58">
                        <!-- Cabecera -->
                        <div class="text-center">
                            <img id="logo_voucher" src="" alt="Logo" style="max-height: 60px; filter: grayscale(100%); margin-bottom: 5px;">
                            <div class="fw-bold fs-title" id="name_bussines">--</div>
                            <div class="fs-subtitle" id="direction_bussines">--</div>
                            <div>RUC: <span id="document_bussines">--</span></div>
                            <div class="separator"></div>
                            <div class="fw-bold fs-title">COMPROBANTE DE VENTA</div>
                            <div class="separator"></div>
                        </div>

                        <!-- Datos Generales -->
                        <div>
                            <table style="width: 100%;">
                                <tr>
                                    <td><span class="fw-bold">Código:</span></td>
                                    <td class="text-right" id="voucher_code">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Fecha:</span></td>
                                    <td class="text-right" id="date_time">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Estado:</span></td>
                                    <td class="text-right" id="voucher_state">----</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">T. Venta:</span></td>
                                    <td class="text-right" id="voucher_type">------</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Vencim.:</span></td>
                                    <td class="text-right" id="voucher_expiration_date">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Vendedor:</span></td>
                                    <td class="text-right" id="fullname">--</td>
                                </tr>
                            </table>
                            <div class="separator-solid"></div>
                            <div><span class="fw-bold">Cliente:</span></div>
                            <div id="name_customer">--</div>
                            <div id="direction_customer">--</div>
                            <div class="separator-solid"></div>
                        </div>

                        <!-- Detalles del Producto -->
                        <div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: left;">CANT</th>
                                        <th style="width: 50%; text-align: left;">DESCRIPCIÓN</th>
                                        <th style="width: 35%; text-align: right;">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyVoucherDetails">
                                    <!-- Dynamic Items -->
                                </tbody>
                            </table>
                            <div class="separator-solid"></div>
                        </div>

                        <!-- Totales Section -->
                        <div>
                            <table class="totals-table">
                                <tr>
                                    <td class="fw-bold text-right" style="width: 60%">Subtotal:</td>
                                    <td class="text-right" id="subtotal_amount">--</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-right">Descto (<span id="percentage_discount">0</span>%):</td>
                                    <td class="text-right" id="discount_amount">--</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-right"><span id="tax_name">IGV</span> (<span id="tax_percentage">0</span>%):</td>
                                    <td class="text-right" id="tax_amount">--</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-right">Imp. Finac (<span id="input_finac_percentage">0</span>%):</td>
                                    <td class="text-right" id="input_finac_amount">--</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-right">Imp. Mora (<span id="input_mora_percentage">0</span>%):</td>
                                    <td class="text-right" id="input_mora_amount">--</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><div class="separator"></div></td>
                                </tr>
                                <tr>
                                    <td class="grand-total text-right">TOTAL:</td>
                                    <td class="grand-total text-right" id="total_amount">--</td>
                                </tr>
                            </table>
                        </div>

                        <!-- System Footer -->
                        <div class="brand-footer mt-4">
                            <div class="separator"></div>
                            <div class="text-center mb-2">
                                <!-- Placeholder de Código de barras simulado con texto -->
                                <div style="font-size: 16px; letter-spacing: 2px; line-height: 1;">
                                    || ||| | ||| || ||
                                </div>
                            </div>
                            <div class="text-center fw-bold" style="font-size: 12px; margin-bottom: 5px;">¡Gracias por su preferencia!</div>
                            <div class="separator"></div>
                            <div style="margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                                <img src="<?= base_url() ?>/Assets/capysm.png" alt="Logo" style="height: 12px; margin-right: 5px; opacity: 0.8; filter: grayscale(100%);">
                                <span style="font-weight: bold;">CapyVentas</span>
                            </div>
                            <div style="font-weight: 600;">capyventas.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" id="btnPrintVoucherMovements">
                    <i class="bi bi-printer"></i> Imprimir (P)
                </button>
                <button type="button" class="btn btn-outline-warning" id="download-png">
                    <i class="bi bi-card-image"></i> Exportar PNG
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Gasto -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="expenseModalLabel">Comprobante de Egreso</h5>
                        <p class="mb-0 small text-white text-opacity-75">Aqui podras ver la nota de egreso</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body" id="expenseContainer" style="background-color: #f8f9fa; border-radius: 0 0 8px 8px;">
                <div class="ticket-wrapper">
                    <div class="ticket-58">
                        <!-- Cabecera -->
                        <div class="text-center">
                            <img id="logo_expense" src="" alt="Logo" style="max-height: 60px; filter: grayscale(100%); margin-bottom: 5px;">
                            <div class="fw-bold fs-title" id="name_business_expense">--</div>
                            <div class="fs-subtitle" id="direction_business_expense">--</div>
                            <div>RUC: <span id="document_business_expense">--</span></div>
                            <div class="separator"></div>
                            <div class="fw-bold fs-title" id="expense_title">COMPROBANTE DE GASTO</div>
                            <div class="separator"></div>
                        </div>

                        <!-- Details Grid -->
                        <div>
                            <table style="width: 100%;">
                                <tr>
                                    <td><span class="fw-bold">Código:</span></td>
                                    <td class="text-right" id="expense_code">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Fecha:</span></td>
                                    <td class="text-right" id="expense_date">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Registrado:</span></td>
                                    <td class="text-right" id="expense_fullname">--</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">M. Pago:</span></td>
                                    <td class="text-right" id="expense_payment_method">--</td>
                                </tr>
                            </table>
                            
                            <div class="separator"></div>
                            <div class="text-center">
                                <span id="expense_status" class="fs-subtitle fw-bold">--</span>
                            </div>
                            <div class="separator"></div>

                            <div><span class="fw-bold">Referencia:</span></div>
                            <div id="expense_voucher_reference">--</div>
                            <div class="separator-solid"></div>
                            <div><span class="fw-bold">Proveedor:</span></div>
                            <div id="expense_supplier">--</div>
                            <div class="separator-solid"></div>
                        </div>

                        <!-- Concept & Category Table -->
                        <div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 40%; text-align: left;">CATEGORÍA</th>
                                        <th style="width: 60%; text-align: right;">CONCEPTO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="expense_category">--</td>
                                        <td class="text-right">
                                            <strong id="expense_name">--</strong><br>
                                            <span style="font-size: 10px;" id="expense_description">--</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="separator-solid"></div>
                        </div>

                        <!-- Payment Info & Total -->
                        <div>
                            <table class="totals-table mt-2">
                                <tr>
                                    <td class="grand-total text-right">TOTAL:</td>
                                    <td class="grand-total text-right" id="expense_total_amount">--</td>
                                </tr>
                            </table>
                        </div>

                        <!-- System Footer -->
                        <div class="brand-footer mt-4">
                            <div class="separator"></div>
                            <div class="text-center mb-2">
                                <div style="font-size: 16px; letter-spacing: 2px; line-height: 1;">
                                    || ||| | ||| || ||
                                </div>
                            </div>
                            <div class="text-center fw-bold" style="font-size: 12px; margin-bottom: 5px;">Comprobante Interno</div>
                            <div class="separator"></div>
                            <div style="margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                                <img src="<?= base_url() ?>/Assets/capysm.png" alt="Logo" style="height: 12px; margin-right: 5px; opacity: 0.8; filter: grayscale(100%);">
                                <span style="font-weight: bold;">CapyVentas</span>
                            </div>
                            <div style="font-weight: 600;">capyventas.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" id="btnPrintExpenseMovements">
                    <i class="bi bi-printer"></i> Imprimir (P)
                </button>
                <button type="button" class="btn btn-outline-warning" id="download-expense-png">
                    <i class="bi bi-card-image"></i> Exportar PNG
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>