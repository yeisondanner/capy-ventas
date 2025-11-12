<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-file-text"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-file-text fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/im/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="tile">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalSave"><i class="fa fa-plus"></i> Nuevo</button>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm w-100" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
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

<?= footerAdmin($data) ?>

<!-- Modal Save-->
<div class="modal fade" id="modalSave" tabindex="-1" role="dialog" aria-labelledby="modalSaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSaveLabel">Registro de Tipo de Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formSave" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="txtName">Nombre <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="txtName" name="txtName" required
                                               placeholder="Ingrese el nombre del tipo de documento" maxlength="255"
                                               oninput="this.value = this.value.toUpperCase()"
                                               aria-describedby="iconName">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconName"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="txtDescription">Descripción</label>
                                    <div class="input-group">
                                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3"
                                                  placeholder="Ingrese una descripción (opcional)"
                                                  aria-describedby="iconDescription"></textarea>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconDescription"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Este campo es opcional.</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-fw fa-lg fa-save"></i>Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete-->
<div class="modal fade" id="confirmModalDelete" tabindex="-1" role="dialog" aria-labelledby="confirmModalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabel">Confirmación de Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fa fa-exclamation-triangle fa-5x text-danger mb-3"></i>
                <p class="font-weight-bold">¿Estás seguro?</p>
                <p class="" id="txtDelete"></p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" data-token="<?= csrf(false) ?>" id="confirmDelete">
                    <i class="fa fa-check"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Report -->
<div class="modal fade" id="modalReport" tabindex="-1" role="dialog" aria-labelledby="modalReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Tipo de Documento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-uppercase font-weight-bold text-primary" id="reportName"></h3>
                    </div>
                </div>
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Tipo de Documento</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td id="reportNameDetail"></td>
                        </tr>
                        <tr>
                            <td><strong>Descripción</strong></td>
                            <td id="reportDescription">-</td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td id="reportStatus"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-3 bg-light border rounded">
                    <p class="text-muted mb-1">
                        <strong>Fecha de registro:</strong> <span class="text-dark" id="reportRegistrationDate"></span>
                    </p>
                    <p class="text-muted mb-0">
                        <strong>Fecha de actualización:</strong> <span class="text-dark" id="reportUpdateDate"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update-->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Tipo de Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="update_txtId" id="update_txtId">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtName">Nombre <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="update_txtName" name="update_txtName" required
                                               placeholder="Ingrese el nombre del tipo de documento" maxlength="255"
                                               oninput="this.value = this.value.toUpperCase()"
                                               aria-describedby="iconNameUpdate">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconNameUpdate"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtDescription">Descripción</label>
                                    <div class="input-group">
                                        <textarea class="form-control" id="update_txtDescription" name="update_txtDescription" rows="3"
                                                  placeholder="Ingrese una descripción (opcional)"
                                                  aria-describedby="iconDescriptionUpdate"></textarea>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconDescriptionUpdate"><i class="fa fa-file-text" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Este campo es opcional.</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="update_slctStatus">Estado <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" id="update_slctStatus" name="update_slctStatus" required aria-describedby="iconStatusUpdate">
                                            <option value="" selected disabled>Seleccione un elemento</option>
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                        </select>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconStatusUpdate"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-success btn-block" type="submit"><i class="fa fa-fw fa-lg fa-pencil"></i>Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
