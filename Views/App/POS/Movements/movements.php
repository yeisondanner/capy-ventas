<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-pc-display-horizontal"></i> Movimientos</h1>
            <p>Administra los movimientos de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/inventory">Movimientos</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active" aria-current="page"
                            href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i>
                            Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5"
                            href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
                    </li>
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
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive"
                            id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
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

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="voucherModalLabel">Comprobante de Venta</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" id="voucherContainer">
                <div class="receipt-container p-4 border rounded shadow-sm bg-white"
                    style="border: 2px solid #ddd; max-width: 100%; margin: 0 auto; font-family: 'Courier New', Courier, monospace;">
                    <!-- Header -->
                    <div class="row align-items-center mb-4 border-bottom pb-3">
                        <div class="col-3 text-center">
                            <img id="logo_voucher" src="" alt="Logo" class="img-fluid"
                                style="max-height: 80px; filter: grayscale(100%);">
                        </div>
                        <div class="col-9 text-end">
                            <h4 class="fw-bold text-uppercase mb-1" id="name_bussines">--</h4>
                            <p class="mb-0 text-muted small" id="direction_bussines">--</p>
                            <p class="mb-0 text-muted small">RUC: <span id="document_bussines">--</span></p>
                        </div>
                    </div>

                    <!-- Title & Date -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5 class="fw-bold text-decoration-underline text-uppercase">Comprobante de Venta</h5>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Codigo de Venta:</label>
                            <div class="fw-bold" id="voucher_code">--</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Emisión:</label>
                            <div class="fw-bold" id="date_time">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Vendedor:</label>
                            <div class="fw-bold" id="fullname">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Cliente:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="name_customer">--</div>
                            <div class="small text-muted" id="direction_customer">--</div>
                        </div>
                    </div>

                    <!-- Product Details Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered border-dark table-sm mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 10%;">Cant.</th>
                                    <th style="width: 50%;">Descripción</th>
                                    <th style="width: 20%;">P. Unit</th>
                                    <th style="width: 20%;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVoucherDetails">
                                <!-- Dynamic Items -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="row justify-content-end">
                        <div class="col-8">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-end fw-bold small py-0">Subtotal:</td>
                                        <td class="text-end small py-0" style="width: 120px;"><span
                                                id="subtotal_amount">--</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0">Descuento (<span
                                                id="percentage_discount">0</span>%):</td>
                                        <td class="text-end text-danger small py-0"><span id="discount_amount">--</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0"><span id="tax_name">IGV</span> (<span
                                                id="tax_percentage">0</span>%):</td>
                                        <td class="text-end small py-0"><span id="tax_amount">--</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-2 border border-2 border-dark rounded bg-light mt-2 text-end">
                                <label class="small text-uppercase text-muted fw-bold d-block">Total a Pagar</label>
                                <span class="fs-4 fw-bold text-dark" id="total_amount">--</span>
                            </div>
                        </div>
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

<!-- Modal Gasto -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="expenseModalLabel">Comprobante de Egreso</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" id="expenseContainer">
                <div class="receipt-container p-4 border rounded shadow-sm bg-white"
                    style="border: 2px solid #ddd; max-width: 100%; margin: 0 auto; font-family: 'Courier New', Courier, monospace;">
                    <!-- Header -->
                    <div class="row align-items-center mb-4 border-bottom pb-3">
                        <div class="col-3 text-center">
                            <img id="logo_expense" src="" alt="Logo" class="img-fluid"
                                style="max-height: 80px; filter: grayscale(100%);">
                        </div>
                        <div class="col-9 text-end">
                            <h4 class="fw-bold text-uppercase mb-1" id="name_business_expense">--</h4>
                            <p class="mb-0 text-muted small" id="direction_business_expense">--</p>
                            <p class="mb-0 text-muted small">RUC: <span id="document_business_expense">--</span></p>
                        </div>
                    </div>

                    <!-- Title & Date -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5 class="fw-bold text-decoration-underline text-uppercase" id="expense_title">Comprobante de Gasto/Egreso</h5>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Codigo de Gasto/Egreso:</label>
                            <div class="fw-bold" id="expense_code">--</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Emisión:</label>
                            <div class="fw-bold" id="expense_date">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Estado:</label>
                            <div><span id="expense_status" class="badge bg-light text-dark border">--</span></div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Referencia / Comprobante:</label>
                            <div class="border-bottom border-dark pb-1" id="expense_voucher_reference">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Beneficiario / Proveedor:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="expense_supplier">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Registrado Por:</label>
                            <div class="border-bottom pb-1" id="expense_fullname">--</div>
                        </div>
                    </div>

                    <!-- Concept & Category Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered border-dark table-sm mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 30%;">Categoría</th>
                                    <th style="width: 70%;">Concepto / Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center align-middle" id="expense_category">--</td>
                                    <td>
                                        <strong id="expense_name">--</strong><br>
                                        <small class="text-muted" id="expense_description">--</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Info & Total -->
                    <div class="row">
                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Método de Pago:</label>
                            <div class="fw-bold" id="expense_payment_method">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="p-2 border border-2 border-dark rounded d-inline-block bg-light">
                                <label class="small text-uppercase text-muted fw-bold d-block">Monto Total</label>
                                <span class="fs-4 fw-bold text-dark" id="expense_total_amount">--</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="download-expense-png"><i
                        class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>