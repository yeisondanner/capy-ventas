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
                        <a class="nav-link border border-primary shadow-sm rounded-5 active" aria-current="page" href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i> Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5" href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
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
                            <label for="filter-type" class="text-muted fw-bold d-block text-uppercase small form-label">Tipo de Filtro:</label>
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
                            <label for="filter-date" class="text-muted fw-bold d-block text-uppercase small form-label" id="date-label">Fecha:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="__fecha flex-fill" id="date-range-container" style="display: none;">
                            <label for="min-date" class="text-muted fw-bold d-block text-uppercase small form-label">Desde:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="min-date" class="form-control">
                            </div>
                        </div>

                        <div class="__fecha flex-fill" id="date-to-container" style="display: none;">
                            <label for="max-date" class="text-muted fw-bold d-block text-uppercase small form-label">Hasta:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="max-date" class="form-control">
                            </div>
                        </div>

                        <div class="__search flex-fill">
                            <label for="search-concept" class="text-muted fw-bold d-block text-uppercase small form-label">Buscar por Concepto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="search-concept" class="form-control" placeholder="Concepto de venta/egreso...">
                            </div>
                        </div>

                        <div class="__buttons flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
                            <button id="filter-btn" class="btn_filter flex-fill btn btn-outline-primary me-2"><i class="bi bi-funnel"></i> Filtrar</button>
                            <button id="reset-btn" class="btn_clean flex-fill btn btn-outline-secondary "><i class="bi bi-arrow-counterclockwise"></i> Limpiar</button>
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
                                    <div class="icon-container bg-success-subtle d-flex align-items-center justify-content-center me-3">
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
                                    <div class="icon-container bg-success-subtle d-flex align-items-center justify-content-center me-3">
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
                                    <div class="icon-container bg-danger-subtle d-flex align-items-center justify-content-center me-3">
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
                <ul class="nav nav-pills nav-fill mb-3">
                    <li class="nav-item">
                        <a class="nav-link active text-success bg-success-subtle rounded-5 border" id="btnIncome" aria-current="page" href="#"> <i class="bi bi-plus"></i> Ingresos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger bg-danger-subtle rounded-5 border" id="btnExpenses" href="#" onclick="Swal.fire({
                        titl:'En desarrollo',
                        text:'Esta funcionalidad se encuentra en desarrollo',
                        icon:'info',
                        confirmButtonText:'Aceptar'
                        })"> <i class="bi bi-dash"></i> Egresos</a>
                    </li>
                </ul>
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive" id="table" data-token="<?= csrf(false); ?>">
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

            <div class="modal-header bg-info">
                <h5 class="modal-title" id="voucherModalLabel">Comprobante de venta</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div id="voucherContainer" class="voucher-container p-3">

                    <div class="d-flex flex-column align-items-center">
                        <img src="" alt="" id="logo_voucher" class="img-fluid mb-2" style="max-height: 40px;">
                        <!-- ENCABEZADO -->
                        <div class="text-center mb-3">
                            <h5 class="mb-0 fw-bold" id="name_bussines">NOMBRE DEL NEGOCIO</h5>
                            <div class="small text-muted" id="direction_bussines">Dirección del negocio</div>
                            <div class="small text-muted">
                                RUC / Documento: <span id="document_bussines">00000000000</span>
                            </div>
                            <div class="small text-muted">
                                Fecha y hora: <span id="date_time">2025-11-19 10:00:00</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- DATOS DE LOS CLIENTES -->
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <div class="small">
                                    <span class="fw-semibold">Cliente:</span>
                                    <span id="name_customer">CLIENTE MOSTRADOR</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="small">
                                    <span class="fw-semibold">Dirección:</span>
                                    <span id="direction_customer">Sin dirección</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="small">
                                    <span class="fw-semibold">Nombre Vendedor:</span>
                                    <span id="fullname">Sin vendedor</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DETALLE DE LOS PRODUCTOS -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="border-bottom">
                                <tr class="text-uppercase small">
                                    <th class="fw-semibold" style="width: 10%;">Cant.</th>
                                    <th class="fw-semibold">Descripción</th>
                                    <th class="text-end fw-semibold" style="width: 15%;">P. Unit.</th>
                                    <th class="text-end fw-semibold" style="width: 15%;">Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVoucherDetails">
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-2">

                    <!-- TOTALES -->
                    <div class="row justify-content-end">
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Subtotal:</span>
                                <span id="subtotal_amount">S/ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Descuento (<span id="percentage_discount">0</span>%):</span>
                                <span id="discount_amount">S/ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between fw-semibold border-top pt-2">
                                <span>Total:</span>
                                <span id="total_amount">S/ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- PIE DEL COMPROBANTE -->
                    <div class="text-center small">
                        <div class="fw-bold text-success mb-1">¡GRACIAS POR SU COMPRA!</div>
                        <div class="text-muted">Conserve este comprobante para cualquier consulta.</div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="download-png"><i class="bi bi-card-image"></i> Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>