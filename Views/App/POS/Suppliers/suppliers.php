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
            <div class="tile">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="button" id="btnOpenSupplierModal">
                        <i class="bi bi-plus-lg"></i> Registrar proveedor
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="supplierTable" data-token="<?= csrf(false); ?>">
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
                        <input type="text" class="form-control" id="txtSupplierDocument" name="txtSupplierDocument" maxlength="11"
                            placeholder="Número de documento">
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtSupplierName" name="txtSupplierName" maxlength="255" required
                            placeholder="Nombre comercial del proveedor">
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierPhone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="txtSupplierPhone" name="txtSupplierPhone" maxlength="11"
                            placeholder="Número de contacto">
                    </div>
                    <div class="col-md-6">
                        <label for="txtSupplierEmail" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="txtSupplierEmail" name="txtSupplierEmail" maxlength="255"
                            placeholder="correo@ejemplo.com">
                    </div>
                    <div class="col-12">
                        <label for="txtSupplierAddress" class="form-label">Dirección</label>
                        <textarea class="form-control" id="txtSupplierAddress" name="txtSupplierAddress" rows="3"
                            placeholder="Dirección o información adicional del proveedor"></textarea>
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

<!-- Modal: Detalle del proveedor -->
<div class="modal fade" id="modalSupplierDetail" tabindex="-1" aria-labelledby="modalSupplierDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalSupplierDetailLabel">Detalle del proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="detailSupplierName">-</dd>
                    <dt class="col-sm-4">Documento</dt>
                    <dd class="col-sm-8" id="detailSupplierDocument">-</dd>
                    <dt class="col-sm-4">Teléfono</dt>
                    <dd class="col-sm-8" id="detailSupplierPhone">-</dd>
                    <dt class="col-sm-4">Correo</dt>
                    <dd class="col-sm-8" id="detailSupplierEmail">-</dd>
                    <dt class="col-sm-4">Dirección</dt>
                    <dd class="col-sm-8" id="detailSupplierAddress">-</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="detailSupplierStatus">-</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>