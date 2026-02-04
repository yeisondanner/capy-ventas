<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-box-seam"></i> Gestión de Cajas</h1>
            <p>Administra las cajas de tu negocio: registra nuevas cajas y controla su estado</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/boxmanagement">Gestión de Cajas</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $boxmanagement = (int) validate_permission_app(13, "c", false)['create'];
                    if ($boxmanagement === 1): ?>
                        <button class="btn btn-outline-primary btn-sm" type="button" id="btnOpenBoxModal">
                            <i class="bi bi-plus-lg"></i> Agregar nueva caja
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
                            id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>

                                    <th>Fecha de Registro</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="small text-muted mt-2">
                        * Las cajas inactivas no se muestran en el listado.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= footerPos($data) ?>

<!-- Modal: Registrar caja -->
<!-- Modal: Registrar caja -->
<div class="modal fade" id="modalBox" tabindex="-1" aria-labelledby="modalBoxLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formBox" autocomplete="off">
            <div class="modal-header bg-primary text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalBoxLabel">Registrar caja</h5>
                        <p class="mb-0 small text-white text-opacity-75">Gestión de puntos de venta</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nameBox" class="form-label">Nombre de la Caja <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="nameBox" name="nameBox" maxlength="255"
                                        required placeholder="Ej. Caja Principal">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="descriptionBox" class="form-label">Descripción</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                                    <input type="text" class="form-control" id="descriptionBox" name="descriptionBox"
                                        placeholder="Describe las características principales de la caja">
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
                                        <i class="bi bi-inbox display-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3">Nueva Caja</h4>
                                <p class="mb-0 opacity-75">
                                    Registre una nueva caja para gestionar las operaciones de venta y flujo de dinero en
                                    su negocio.
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

<!-- Modal: Reporte de caja -->
<div class="modal fade" id="modalBoxReport" tabindex="-1" aria-labelledby="modalBoxReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalBoxReportLabel">Ficha de la Caja</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Detalles de la caja seleccionada</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>

            <div class="modal-body" id="boxReportContainer">
                <div class="receipt-container p-4 border rounded shadow-sm bg-white">
                    
                    <!-- Header -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark" id="reportBoxName">-</h4>
                            <div class="small text-muted">Nombre de la caja</div>
                        </div>
                        <span class="badge bg-secondary px-3 py-2" id="reportBoxStatus">-</span>
                    </div>

                    <!-- Details -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Descripción:</label>
                            <div class="mt-1 fs-5" id="reportBoxDescription">-</div>
                        </div>

                        <div class="col-12">
                            <hr class="my-3 opacity-25">
                        </div>

                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Registro:</label>
                            <div class="mt-1 fw-bold" id="reportBoxRegistrationDate">-</div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar caja -->
<div class="modal fade" id="modalUpdateBox" tabindex="-1" aria-labelledby="modalUpdateBoxLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formUpdateBox" autocomplete="off">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalUpdateBoxLabel">Actualizar caja</h5>
                        <p class="mb-0 small text-white text-opacity-75">Modifica los datos de la caja seleccionada</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_idBox" id="update_idBox">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-12 text-primary border-bottom pb-2">
                                <h6><i class="bi bi-box-seam me-2"></i>Datos de la Caja</h6>
                            </div>

                            <div class="col-12">
                                <label for="update_nameBox" class="form-label">Nombre de la Caja <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="update_nameBox" name="update_nameBox"
                                        maxlength="255" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="update_descriptionBox" class="form-label">Descripción</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                                    <input type="text" class="form-control" id="update_descriptionBox" name="update_descriptionBox">
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
                                    Actualice la información de la caja. Asegúrese de guardar los cambios para mantener la integridad de los datos.
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