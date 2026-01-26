<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-people"></i> Proveedores</h1>
            <p>Administra los proveedores de tu negocio: registra nuevos contactos, actualiza sus datos y controla su disponibilidad.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $createSupplier = (int) (validate_permission_app(7, "c", false)) ? (int)validate_permission_app(7, "c", false)['create'] : 0;
                    if ($createSupplier === 1): ?>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="btnOpenSupplierModal">
                            <i class="bi bi-plus-lg"></i> Registrar proveedor
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive" id="supplierTable" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
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

<!-- Modal: Registrar/editar proveedor -->
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formSupplier" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSupplierLabel">Registrar proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="supplierId" id="supplierId" value="0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="txtSupplierDocument" class="form-label">Documento</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control" id="txtSupplierDocument" name="txtSupplierDocument" maxlength="15"
                                placeholder="Número de documento" pattern="[0-9]{8,15}" title="Solo se permiten números (8-15 dígitos)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" class="form-control" id="txtSupplierName" name="txtSupplierName" maxlength="255" required
                                placeholder="Nombre comercial del proveedor" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierPhone" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control" id="txtSupplierPhone" name="txtSupplierPhone" maxlength="15"
                                placeholder="Número de contacto" pattern="[0-9]{9,15}" title="Solo se permiten números (9-15 dígitos)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierEmail" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="txtSupplierEmail" name="txtSupplierEmail" maxlength="255"
                                placeholder="correo@ejemplo.com" title="Ingrese un correo electrónico válido">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="txtSupplierAddress" class="form-label">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control" id="txtSupplierAddress" name="txtSupplierAddress" maxlength="255"
                                placeholder="Dirección o información adicional del proveedor">
                        </div>
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

<!-- Modal: Detalle del proveedor (Estilo Reporte) -->
<div class="modal fade" id="modalSupplierDetail" tabindex="-1" aria-labelledby="modalSupplierDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSupplierDetailLabel">Ficha del Proveedor</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" id="supplierReportContainer">
                <div class="receipt-container report-card-suppliers p-4 border rounded shadow-sm bg-white">

                    <!-- Header Negocio -->
                    <div class="row align-items-center mb-4 border-bottom pb-3">
                        <div class="col-3 text-center">
                            <img id="report_logo" src="" alt="Logo" class="img-fluid"
                                style="max-height: 80px; filter: grayscale(100%);">
                        </div>
                        <div class="col-9 text-end">
                            <h4 class="fw-bold text-uppercase mb-1" id="report_business_name">--</h4>
                            <p class="mb-0 text-muted small" id="report_business_address">--</p>
                            <p class="mb-0 text-muted small">RUC: <span id="report_business_document">--</span></p>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5 class="fw-bold text-decoration-underline text-uppercase">Información del Proveedor</h5>
                        </div>
                    </div>

                    <!-- Supplier Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Razón Social / Nombre:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="report_supplier_name">--</div>
                        </div>

                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Documento (RUC/DNI):</label>
                            <div class="fw-bold" id="report_supplier_document">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Estado:</label>
                            <div><span id="report_supplier_status" class="badge bg-light text-dark border">--</span></div>
                        </div>

                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Teléfono:</label>
                            <div class="fw-bold" id="report_supplier_phone">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Correo Electrónico:</label>
                            <div class="border-bottom border-dark pb-1" id="report_supplier_email">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Dirección:</label>
                            <div class="border-bottom border-dark pb-1" id="report_supplier_address">--</div>
                        </div>
                    </div>

                    <!-- System Footer -->
                    <div class="row mt-4">
                        <div class="col-12 text-center d-flex align-items-center justify-content-center">
                            <img src="<?= base_url() ?>/Assets/capysm.png" alt="Logo" style="height: 20px; width: auto; margin-right: 5px; opacity: 0.8;">
                            <small class="text-muted fst-italic">Generado por Capy Ventas</small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="btnDownloadSupplierPng"><i class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>