<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-people-fill"></i> Clientes</h1>
            <p>Administra los clientes de tu negocio: registra nuevos contactos, actualiza sus datos y consulta su información.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item active" aria-current="page">Clientes</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $createCustomer = (int) isset(validate_permission_app(4, "c", false)['create']) ? validate_permission_app(4, "c", false)['create'] : 0;
                    if ($createCustomer === 1): ?>
                        <button class="btn btn-primary" type="button" id="btnOpenCustomerModal">
                            <i class="bi bi-plus-lg"></i> Registrar cliente
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="customerTable" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Tipo de documento</th>
                                    <th>Número de documento</th>
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

<!-- Modal: Registrar/editar cliente -->
<div class="modal fade" id="modalCustomer" tabindex="-1" aria-labelledby="modalCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formCustomer" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCustomerLabel">Registrar cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="customerId" id="customerId" value="0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="txtCustomerDocumentType" class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtCustomerDocumentType" name="txtCustomerDocumentType" required>
                            <option value="" selected disabled>Selecciona un tipo de documento</option>
                            <?php foreach (($data['document_types'] ?? []) as $type): ?>
                                <option value="<?= (int) ($type['id'] ?? 0); ?>">
                                    <?= htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtCustomerDocument" class="form-label">Número de documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtCustomerDocument" name="txtCustomerDocument" maxlength="11"
                            required placeholder="Número de documento">
                    </div>
                    <div class="col-md-6">
                        <label for="txtCustomerName" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtCustomerName" name="txtCustomerName" maxlength="255" required
                            placeholder="Nombre completo del cliente">
                    </div>
                    <div class="col-md-6">
                        <label for="txtCustomerPhone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="txtCustomerPhone" name="txtCustomerPhone" maxlength="11"
                            placeholder="Número de contacto">
                    </div>
                    <div class="col-md-6">
                        <label for="txtCustomerEmail" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="txtCustomerEmail" name="txtCustomerEmail" maxlength="255"
                            placeholder="correo@ejemplo.com">
                    </div>
                    <div class="col-12">
                        <label for="txtCustomerAddress" class="form-label">Dirección</label>
                        <textarea class="form-control" id="txtCustomerAddress" name="txtCustomerAddress" rows="3"
                            placeholder="Dirección o información adicional del cliente"></textarea>
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

<!-- Modal: Detalle del cliente -->
<div class="modal fade" id="modalCustomerDetail" tabindex="-1" aria-labelledby="modalCustomerDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalCustomerDetailLabel">Detalle del cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="detailCustomerName">-</dd>
                    <dt class="col-sm-4">Tipo de documento</dt>
                    <dd class="col-sm-8" id="detailCustomerDocumentType">-</dd>
                    <dt class="col-sm-4">Número de documento</dt>
                    <dd class="col-sm-8" id="detailCustomerDocument">-</dd>
                    <dt class="col-sm-4">Teléfono</dt>
                    <dd class="col-sm-8" id="detailCustomerPhone">-</dd>
                    <dt class="col-sm-4">Correo</dt>
                    <dd class="col-sm-8" id="detailCustomerEmail">-</dd>
                    <dt class="col-sm-4">Dirección</dt>
                    <dd class="col-sm-8" id="detailCustomerAddress">-</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="detailCustomerStatus">-</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>