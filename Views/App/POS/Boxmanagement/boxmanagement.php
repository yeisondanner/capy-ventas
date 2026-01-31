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
                    $boxmanagement = (int) (validate_permission_app(12, "c", false)) ? (int) validate_permission_app(12, "c", false)['create'] : 0;
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
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive" id="table" data-token="<?= csrf(false); ?>">
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
<div class="modal fade" id="modalBox" tabindex="-1" aria-labelledby="modalBoxLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formBox" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBoxLabel">Registrar caja</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="nameBox" class="form-label">Nombre de la Caja <span class="text-danger">*</span></label>
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
                            <textarea class="form-control" id="descriptionBox" name="descriptionBox"
                                rows="3" placeholder="Describe las características principales de la caja"></textarea>
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
    <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalBoxReportLabel">
                    <i class="bi bi-file-earmark-text me-2"></i> Reporte de la caja
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body bg-light p-4">
                <div class="bg-white border rounded-3 shadow-sm p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark" id="reportBoxName">-</h4>
                            <div class="small text-muted">Información de la caja</div>
                        </div>
                        <span class="badge bg-secondary px-3 py-2" id="reportBoxStatus">-</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="small text-muted fw-bold text-uppercase" style="font-size:.75rem;">Descripción</div>
                            <div class="mt-1" id="reportBoxDescription">-</div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted fw-bold text-uppercase" style="font-size:.75rem;">Fecha de creación</div>
                            <div class="mt-1" id="reportBoxRegistrationDate">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar caja -->
<div class="modal fade" id="modalUpdateBox" tabindex="-1" aria-labelledby="modalUpdateBoxLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formUpdateBox" autocomplete="off">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateBoxLabel">Actualizar caja</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_idBox" id="update_idBox">
                <div class="row g-3">
                    <div class="col-md-12">
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
                            <textarea class="form-control" id="update_descriptionBox" name="update_descriptionBox"
                                rows="3"></textarea>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>