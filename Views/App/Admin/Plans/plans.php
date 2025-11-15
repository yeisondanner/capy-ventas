<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-credit-card"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-credit-card fa-lg"></i></li>
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
                                    <th>Precio Base</th>
                                    <th>Periodo de Facturación</th>
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
                <h5 class="modal-title" id="modalSaveLabel">Registro de Plan</h5>
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
                                    <label class="control-label" for="txtName">Nombre del Plan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="txtName" name="txtName" required
                                               placeholder="Ej: Free, Pro, Business" maxlength="50"
                                               oninput="this.value = this.value.toUpperCase()"
                                               aria-describedby="iconName">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconName"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Máximo 50 caracteres</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="txtDescription">Descripción</label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3"
                                              placeholder="Ingrese una descripción del plan y sus características principales"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="txtBasePrice">Precio Base <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input class="form-control" type="number" id="txtBasePrice" name="txtBasePrice" required
                                               placeholder="0.00" step="0.01" min="0"
                                               aria-describedby="iconBasePrice">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconBasePrice"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="slctBillingPeriod">Periodo de Facturación <span class="text-danger">*</span></label>
                                    <select class="form-control" id="slctBillingPeriod" name="slctBillingPeriod" required>
                                        <option value="monthly">Mensual</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="slctIsActive">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control" id="slctIsActive" name="slctIsActive" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
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
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Plan</h5>
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
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Plan</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td id="reportNameDetail"></td>
                        </tr>
                        <tr>
                            <td><strong>Descripción</strong></td>
                            <td id="reportDescription"></td>
                        </tr>
                        <tr>
                            <td><strong>Precio Base</strong></td>
                            <td id="reportBasePrice"></td>
                        </tr>
                        <tr>
                            <td><strong>Periodo de Facturación</strong></td>
                            <td id="reportBillingPeriod"></td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td id="reportStatus"></td>
                        </tr>
                    </tbody>
                </table>
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
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Plan</h5>
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
                                <input type="hidden" name="idPlan" id="update_idPlan">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtName">Nombre del Plan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="update_txtName" name="update_txtName" required
                                               placeholder="Ej: Free, Pro, Business" maxlength="50"
                                               oninput="this.value = this.value.toUpperCase()"
                                               aria-describedby="iconUpdateName">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconUpdateName"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Máximo 50 caracteres</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtDescription">Descripción</label>
                                    <textarea class="form-control" id="update_txtDescription" name="update_txtDescription" rows="3"
                                              placeholder="Ingrese una descripción del plan y sus características principales"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtBasePrice">Precio Base <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input class="form-control" type="number" id="update_txtBasePrice" name="update_txtBasePrice" required
                                               placeholder="0.00" step="0.01" min="0"
                                               aria-describedby="iconUpdateBasePrice">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="iconUpdateBasePrice"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="update_slctBillingPeriod">Periodo de Facturación <span class="text-danger">*</span></label>
                                    <select class="form-control" id="update_slctBillingPeriod" name="update_slctBillingPeriod" required>
                                        <option value="monthly">Mensual</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="update_slctIsActive">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control" id="update_slctIsActive" name="update_slctIsActive" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-success btn-block" type="submit"><i class="fa fa-fw fa-lg fa-save"></i>Actualizar</button>
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
