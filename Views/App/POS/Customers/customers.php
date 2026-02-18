<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-people-fill"></i> Clientes</h1>
            <p>Administra los clientes de tu negocio: registra nuevos contactos, actualiza sus datos y consulta su
                información.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item active" aria-current="page">Clientes</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $createCustomer = (int) (validate_permission_app(4, "c", false)) ? (int) validate_permission_app(4, "c", false)['create'] : 0;
                    if ($createCustomer === 1): ?>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="btnOpenCustomerModal">
                            <i class="bi bi-plus-lg"></i> Registrar cliente
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
                            id="customerTable" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Tipo de documento</th>
                                    <th>Número de documento</th>
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

<!-- Modal: Registrar cliente -->
<div class="modal fade" id="modalCustomer" tabindex="-1" aria-labelledby="modalCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formCustomer" autocomplete="off">
            <div class="modal-header bg-primary text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-person fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCustomerLabel">Registrar cliente</h5>
                        <p class="mb-0 small text-white text-opacity-75">Aqui podras gestionar tus clientes</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="customerId" id="customerId" value="0">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="txtCustomerDocumentType" class="form-label">Tipo de documento <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                    <select class="form-select" id="txtCustomerDocumentType"
                                        name="txtCustomerDocumentType" required>
                                        <option value="" selected disabled>Selecciona un tipo de documento</option>
                                        <?php foreach (($data['document_types'] ?? []) as $type): ?>
                                            <option value="<?= (int) ($type['id'] ?? 0); ?>">
                                                <?= htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerDocument" class="form-label">Número de documento <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerDocument"
                                        name="txtCustomerDocument" maxlength="8" required
                                        placeholder="Número de documento" pattern="[0-9]{8}"
                                        title="El documento debe contener exactamente 8 dígitos numéricos">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="txtCustomerName" class="form-label">Nombre completo <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerName" name="txtCustomerName"
                                        maxlength="255" required placeholder="Nombre completo del cliente"
                                        pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+" title="Solo se permiten letras y espacios">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerPhone" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerPhone"
                                        name="txtCustomerPhone" maxlength="15" placeholder="Número de contacto"
                                        pattern="[0-9]{9,15}" title="Solo se permiten números (9-15 dígitos)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerEmail" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="txtCustomerEmail"
                                        name="txtCustomerEmail" maxlength="255" placeholder="correo@ejemplo.com"
                                        title="Ingrese un correo electrónico válido">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="txtCustomerAddress" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerAddress"
                                        name="txtCustomerAddress"
                                        placeholder="Dirección o información adicional del cliente">
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
                                        <i class="bi bi-people-fill display-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3">Gestión de Clientes</h4>
                                <p class="mb-0 opacity-75">
                                    Complete los datos del formulario para registrar un nuevo cliente. Esta información
                                    es esencial para la gestión de ventas y facturación.
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

<!-- Modal: Editar cliente -->
<div class="modal fade" id="modalEditCustomer" tabindex="-1" aria-labelledby="modalEditCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formEditCustomer" autocomplete="off">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalEditCustomerLabel">Actualizar cliente</h5>
                        <p class="mb-0 small text-white text-opacity-75">Modifica los datos del cliente seleccionado</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="customerId" id="customerIdEdit" value="0">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <!-- Section: Personal Data -->
                            <div class="col-12 mt-2">
                                <h6 class="text-primary border-bottom pb-2"><i
                                        class="bi bi-person-lines-fill me-2"></i>Datos personales</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="txtCustomerDocumentTypeEdit" class="form-label">Tipo de documento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <select class="form-select" id="txtCustomerDocumentTypeEdit"
                                        name="txtCustomerDocumentType" required>
                                        <?php foreach (($data['document_types'] ?? []) as $type): ?>
                                            <option value="<?= (int) ($type['id'] ?? 0); ?>">
                                                <?= htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerDocumentEdit" class="form-label">Número de documento <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerDocumentEdit"
                                        name="txtCustomerDocument" maxlength="8" required
                                        placeholder="Número de documento" pattern="[0-9]{8}"
                                        title="El documento debe contener exactamente 8 dígitos numéricos">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="txtCustomerNameEdit" class="form-label">Nombre completo <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerNameEdit"
                                        name="txtCustomerName" maxlength="255" required
                                        placeholder="Nombre completo del cliente" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+"
                                        title="Solo se permiten letras y espacios">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerPhoneEdit" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerPhoneEdit"
                                        name="txtCustomerPhone" maxlength="15" placeholder="Número de contacto"
                                        pattern="[0-9]{9,15}" title="Solo se permiten números (9-15 dígitos)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="txtCustomerEmailEdit" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="txtCustomerEmailEdit"
                                        name="txtCustomerEmail" maxlength="255" placeholder="correo@ejemplo.com"
                                        title="Ingrese un correo electrónico válido">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="txtCustomerAddressEdit" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="txtCustomerAddressEdit"
                                        name="txtCustomerAddress"
                                        placeholder="Dirección o información adicional del cliente">
                                </div>
                            </div>

                            <!-- Section: Credit Data -->
                            <div class="col-12 mt-4">
                                <h6 class="text-success border-bottom pb-2"><i
                                        class="bi bi-credit-card-2-front me-2"></i>Datos de crédito</h6>
                            </div>

                            <div class="col-6 col-md-6">
                                <label for="txtCustomerCreditLimit" class="form-label">Límite de crédito</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-cash-coin"></i></span>
                                    <input type="number" class="form-control" id="txtCustomerCreditLimit"
                                        name="txtCustomerCreditLimit" step="0.01" min="0" max="99999999.99"
                                        placeholder="50.00">
                                </div>
                                <div class="form-text">0.00 = Crédito ilimitado.</div>
                            </div>
                            <div class="col-6 col-md-6">
                                <label for="txtCustomerDefaultInterest" class="form-label">Tasa mora mensual (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                    <input type="number" class="form-control" id="txtCustomerDefaultInterest"
                                        name="txtCustomerDefaultInterest" step="0.01" min="0" max="100"
                                        placeholder="0.00">
                                </div>
                                <div class="form-text">Interés por pago tardío.</div>
                            </div>
                            <div class="col-6 col-md-6">
                                <label for="txtCustomerCurrentInterest" class="form-label">Tasa financiamiento (%)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-graph-up"></i></span>
                                    <input type="number" class="form-control" id="txtCustomerCurrentInterest"
                                        name="txtCustomerCurrentInterest" step="0.01" min="0" max="100"
                                        placeholder="0.00">
                                </div>
                                <div class="form-text">Interés por financiamiento, esto solo se aplica una sola ves al monto que se financio o venta realizado a credito.</div>
                            </div>
                            <div class="col-6 col-md-6">
                                <label for="txtCustomerBillingDate" class="form-label">Fecha de facturación <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" class="form-control" id="txtCustomerBillingDate"
                                        name="txtCustomerBillingDate" required>
                                </div>
                                <div class="form-text">Fecha prefijada de cobro.</div>
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
                                    Actualice la información del cliente. Asegúrese de guardar los cambios para que se
                                    reflejen en futuros comprobantes.
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

<!-- Modal: Detalle del cliente (Estilo Reporte) -->
<div class="modal fade" id="modalCustomerDetail" tabindex="-1" aria-labelledby="modalCustomerDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-person-badge fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCustomerDetailLabel">Ficha del Cliente</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Información detallada del cliente seleccionado
                        </p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body" id="customerReportContainer">
                <div class="receipt-container report-card-customers p-4 border rounded shadow-sm bg-white">

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
                            <h5 class="fw-bold text-decoration-underline text-uppercase">Información del Cliente</h5>
                        </div>
                    </div>

                    <!-- Client Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Nombre Completo:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="report_customer_name">--</div>
                        </div>

                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Tipo Documento:</label>
                            <div class="fw-bold" id="report_customer_doctype">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Nro. Documento:</label>
                            <div class="fw-bold" id="report_customer_document">--</div>
                        </div>

                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Teléfono:</label>
                            <div class="fw-bold" id="report_customer_phone">--</div>
                        </div>
                        <div class="col-6 mt-3 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Estado:</label>
                            <div><span id="report_customer_status" class="badge bg-light text-dark border">--</span>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Correo Electrónico:</label>
                            <div class="border-bottom border-dark pb-1" id="report_customer_email">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Dirección:</label>
                            <div class="border-bottom border-dark pb-1" id="report_customer_address">--</div>
                        </div>
                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Facturación:</label>
                            <div class="fw-bold" id="report_customer_facturation_date">--</div>
                        </div>
                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Limite de Credito:</label>
                            <div class="fw-bold" id="report_customer_credit_limit">--</div>
                        </div>
                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Interes mensual por
                                financiamiento:</label>
                            <div class="fw-bold" id="report_customer_interest">--</div>
                        </div>
                        <div class="col-6 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Interes mensual por
                                vencimiento:</label>
                            <div class="fw-bold" id="report_customer_interest_overdue">--</div>
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
                <button type="button" class="btn btn-outline-warning" id="btnDownloadCustomerPng"><i
                        class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>