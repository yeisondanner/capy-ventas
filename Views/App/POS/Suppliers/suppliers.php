<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-people"></i> Proveedores</h1>
            <p>Administra los proveedores de tu negocio: registra nuevos contactos, actualiza sus datos y controla su
                disponibilidad.</p>
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
                    $createSupplier = (int) (validate_permission_app(7, "c", false)) ? (int) validate_permission_app(7, "c", false)['create'] : 0;
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
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive"
                            id="supplierTable" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
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

<!-- Modal: Registrar proveedor -->
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formSupplier" autocomplete="off">
            <div class="modal-header bg-primary text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalSupplierLabel">Registrar proveedor</h5>
                        <p class="mb-0 small text-white text-opacity-75">Aquí podrás gestionar tus proveedores</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="supplierId" id="supplierId" value="0">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="txtSupplierDocument" class="form-label">Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierDocument"
                                        name="txtSupplierDocument" maxlength="11" placeholder="Número de documento">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierName" class="form-label">Nombre <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierName" name="txtSupplierName"
                                        maxlength="255" required placeholder="Nombre comercial del proveedor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierPhone" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierPhone"
                                        name="txtSupplierPhone" maxlength="11" placeholder="Número de contacto">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierEmail" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="txtSupplierEmail"
                                        name="txtSupplierEmail" maxlength="255" placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="txtSupplierAddress" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierAddress"
                                        name="txtSupplierAddress"
                                        placeholder="Dirección o información adicional del proveedor">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Panel Informativo -->
                    <div class="col-lg-4 order-first order-lg-last mb-4 mb-lg-0">
                        <div class="card bg-primary text-white h-100 border-0 shadow-sm" style="border-radius: 1rem;">
                            <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                                <div class="mb-4">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px;">
                                        <i class="bi bi-truck display-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3">Gestión de Proveedores</h4>
                                <p class="mb-0 opacity-75">
                                    Complete los datos del formulario para registrar un nuevo proveedor. Esta
                                    información es esencial para el control de stock y compras.
                                </p>
                            </div>
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

<!-- Modal: Editar proveedor -->
<div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-labelledby="modalEditSupplierLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formEditSupplier" autocomplete="off">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalEditSupplierLabel">Actualizar proveedor</h5>
                        <p class="mb-0 small text-white text-opacity-75">Modifica los datos del proveedor seleccionado
                        </p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="supplierId" id="supplierIdEdit" value="0">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-12 mt-2">
                                <h6 class="text-primary border-bottom pb-2"><i class="bi bi-building me-2"></i>Datos de
                                    la empresa</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierDocumentEdit" class="form-label">Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierDocumentEdit"
                                        name="txtSupplierDocument" maxlength="11" placeholder="Número de documento">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierNameEdit" class="form-label">Nombre <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierNameEdit"
                                        name="txtSupplierName" maxlength="255" required
                                        placeholder="Nombre comercial del proveedor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierPhoneEdit" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierPhoneEdit"
                                        name="txtSupplierPhone" maxlength="11" placeholder="Número de contacto">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtSupplierEmailEdit" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="txtSupplierEmailEdit"
                                        name="txtSupplierEmail" maxlength="255" placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="txtSupplierAddressEdit" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="txtSupplierAddressEdit"
                                        name="txtSupplierAddress"
                                        placeholder="Dirección o información adicional del proveedor">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Panel Informativo -->
                    <div class="col-lg-4 order-first order-lg-last mb-4 mb-lg-0">
                        <div class="card bg-success text-white h-100 border-0 shadow-sm" style="border-radius: 1rem;">
                            <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                                <div class="mb-4">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px;">
                                        <i class="bi bi-arrow-repeat display-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3">Edición de Datos</h4>
                                <p class="mb-0 opacity-75">
                                    Actualice la información del proveedor. Asegúrese de guardar los cambios para
                                    mantener la integridad de los datos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Detalle del proveedor (Estilo Reporte) -->
<div class="modal fade" id="modalSupplierDetail" tabindex="-1" aria-labelledby="modalSupplierDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalSupplierDetailLabel">Ficha del Proveedor</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Información detallada del proveedor seleccionado
                        </p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
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
                            <div><span id="report_supplier_status" class="badge bg-light text-dark border">--</span>
                            </div>
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
                            <img src="<?= base_url() ?>/Assets/capysm.png" alt="Logo"
                                style="height: 20px; width: auto; margin-right: 5px; opacity: 0.8;">
                            <small class="text-muted fst-italic">Generado por Capy Ventas</small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="btnDownloadSupplierPng"><i
                        class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>