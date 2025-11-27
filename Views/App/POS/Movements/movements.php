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
            <div class="tile">
                <div class="tile-body">
                    <h6 class="text-center text-primary mb-3">Filtrar Movimientos</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <div class="flex-fill __filter_col">
                            <label for="filter-type" class="small font-weight-bold">Tipo de Filtro:</label>
                            <select id="filter-type" class="form-select form-select-sm">
                                <option value="daily">Diario</option>
                                <option value="weekly">Semanal</option>
                                <option value="monthly">Mensual</option>
                                <option value="yearly">Anual</option>
                                <option value="custom">Rango Personalizado</option>
                            </select>
                        </div>

                        <div class="__fecha flex-fill" id="date-container">
                            <label for="filter-date" class="small font-weight-bold" id="date-label">Fecha:</label>
                            <input type="date" id="filter-date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                        </div>


                        <div class="__fecha flex-fill" id="date-range-container" style="display: none;">
                            <label for="min-date" class="small font-weight-bold">Desde:</label>
                            <input type="date" id="min-date" class="form-control form-control-sm">
                        </div>

                        <div class="__fecha flex-fill" id="date-to-container" style="display: none;">
                            <label for="max-date" class="small font-weight-bold">Hasta:</label>
                            <input type="date" id="max-date" class="form-control form-control-sm">
                        </div>

                        <div class="__search flex-fill">
                            <label for="search-concept" class="small font-weight-bold">Buscar por Concepto:</label>
                            <input type="text" id="search-concept" class="form-control form-control-sm" placeholder="Concepto de venta...">
                        </div>

                        <div class="__buttons flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
                            <button id="filter-btn" class="btn_filter flex-fill btn btn-primary btn- me-2"><i class="bi bi-funnel"></i> Filtrar</button>
                            <button id="reset-btn" class="btn_clean flex-fill btn btn-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Limpiar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="tile">
                <div class="row g-3">

                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card border-0 shadow-sm h-100 card-enhanced">
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
                        <div class="card border-0 shadow-sm h-100 card-enhanced">
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
                        <div class="card border-0 shadow-sm h-100 card-enhanced">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-container bg-danger-subtle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-currency-dollar fs-4 text-danger"></i>
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


        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Concepto</th>
                                    <th>Valor</th>
                                    <th>Medios de Pago</th>
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

<!-- Modal: Registrar producto -->
<div class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formSaveProduct" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProductLabel">Registrar producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtProductName" name="txtProductName" maxlength="255"
                            required placeholder="Ej. Café molido premium">
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductCategory" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductCategory" name="txtProductCategory" required>
                            <option value="" selected disabled>Selecciona una categoría</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductSupplier" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductSupplier" name="txtProductSupplier" required>
                            <option value="" selected disabled>Selecciona un proveedor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductMeasurement" class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductMeasurement" name="txtProductMeasurement" required>
                            <option value="" selected disabled>Selecciona una unidad</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductStock" class="form-label">Stock (opcional)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductStock"
                            name="txtProductStock" placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductPurchasePrice" class="form-label">Precio compra <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductPurchasePrice"
                            name="txtProductPurchasePrice" required placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductSalesPrice" class="form-label">Precio venta <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductSalesPrice"
                            name="txtProductSalesPrice" required placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label for="txtProductDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="txtProductDescription" name="txtProductDescription"
                            rows="3" placeholder="Describe las características principales del producto"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="voucherModalLabel">Comprobante de venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="voucher-container p-3">

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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>