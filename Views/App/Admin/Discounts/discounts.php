<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-percent"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-percent fa-lg"></i></li>
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
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Plan</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
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
                <h5 class="modal-title" id="modalSaveLabel">Registro de Descuento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formSave" autocomplete="off">
                        <?= csrf(); ?>
                        <input type="hidden" id="idDiscount" name="idDiscount" value="0">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Información del Descuento</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtCode">Código <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="txtCode" name="txtCode" required
                                                       placeholder="Ingrese el código del descuento" maxlength="50"
                                                       pattern="^[A-Z0-9]+$"
                                                       aria-describedby="iconCode">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCode"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Solo letras mayúsculas y números (ej. CAPY10)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctType">Tipo de Descuento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="slctType" name="slctType" required aria-describedby="iconType">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                    <option value="percentage">Porcentaje</option>
                                                    <option value="fixed">Monto Fijo</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconType"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtValue">Valor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" class="form-control" id="txtValue" name="txtValue" required
                                                       placeholder="Ingrese el valor del descuento" min="0"
                                                       aria-describedby="iconValue">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconValue"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctPlanId">Plan Asociado</label>
                                            <div class="input-group">
                                                <select class="form-control" id="slctPlanId" name="slctPlanId" aria-describedby="iconPlan">
                                                    <option value="">Todos los planes</option>
                                                    <!-- Las opciones se cargarán dinámicamente -->
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconPlan"><i class="fa fa-list" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtStartDate">Fecha de Inicio <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="datetime-local" class="form-control" id="txtStartDate" name="txtStartDate" required
                                                       aria-describedby="iconStartDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconStartDate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtEndDate">Fecha de Fin <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="datetime-local" class="form-control" id="txtEndDate" name="txtEndDate" required
                                                       aria-describedby="iconEndDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconEndDate"><i class="fa fa-calendar-times" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtMaxUses">Uso Máximo</label>
                                            <div class="input-group">
                                                <input type="number" min="0" class="form-control" id="txtMaxUses" name="txtMaxUses"
                                                       placeholder="Ingrese el límite de usos (deje vacío para ilimitado)" min="0"
                                                       aria-describedby="iconMaxUses">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconMaxUses"><i class="fa fa-repeat" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="chkIsRecurring" name="chkIsRecurring">
                                                <label class="form-check-label" for="chkIsRecurring">¿Es Recurrente?</label>
                                            </div>
                                            <small class="form-text text-muted">Si está marcado, se aplica en todos los ciclos; si no, solo en la primera factura</small>
                                        </div>
                                    </div>
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
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Descuento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-uppercase font-weight-bold text-primary" id="reportCode"></h3>
                    </div>
                </div>
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Descuento</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Código</strong></td>
                            <td id="reportCodeDetail"></td>
                        </tr>
                        <tr>
                            <td><strong>Tipo</strong></td>
                            <td id="reportType"></td>
                        </tr>
                        <tr>
                            <td><strong>Valor</strong></td>
                            <td id="reportValue"></td>
                        </tr>
                        <tr>
                            <td><strong>Plan Asociado</strong></td>
                            <td id="reportPlanName"></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Inicio</strong></td>
                            <td id="reportStartDate"></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Fin</strong></td>
                            <td id="reportEndDate"></td>
                        </tr>
                        <tr>
                            <td><strong>Uso Máximo</strong></td>
                            <td id="reportMaxUses"></td>
                        </tr>
                        <tr>
                            <td><strong>¿Es Recurrente?</strong></td>
                            <td id="reportIsRecurring"></td>
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
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Descuento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <input type="hidden" id="update_idDiscount" name="idDiscount" value="0">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Información del Descuento</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtCode">Código <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="update_txtCode" name="txtCode" required
                                                       placeholder="Ingrese el código del descuento" maxlength="50"
                                                       pattern="^[A-Z0-9]+$"
                                                       aria-describedby="iconCodeUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCodeUpdate"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Solo letras mayúsculas y números (ej. CAPY10)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctType">Tipo de Descuento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctType" name="slctType" required aria-describedby="iconTypeUpdate">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                    <option value="percentage">Porcentaje</option>
                                                    <option value="fixed">Monto Fijo</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconTypeUpdate"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtValue">Valor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" class="form-control" id="update_txtValue" name="txtValue" required
                                                       placeholder="Ingrese el valor del descuento" min="0"
                                                       aria-describedby="iconValueUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconValueUpdate"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctPlanId">Plan Asociado</label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctPlanId" name="slctPlanId" aria-describedby="iconPlanUpdate">
                                                    <option value="">Todos los planes</option>
                                                    <!-- Las opciones se cargarán dinámicamente -->
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconPlanUpdate"><i class="fa fa-list" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtStartDate">Fecha de Inicio <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="datetime-local" class="form-control" id="update_txtStartDate" name="txtStartDate" required
                                                       aria-describedby="iconStartDateUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconStartDateUpdate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtEndDate">Fecha de Fin <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="datetime-local" class="form-control" id="update_txtEndDate" name="txtEndDate" required
                                                       aria-describedby="iconEndDateUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconEndDateUpdate"><i class="fa fa-calendar-times" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtMaxUses">Uso Máximo</label>
                                            <div class="input-group">
                                                <input type="number" min="0" class="form-control" id="update_txtMaxUses" name="txtMaxUses"
                                                       placeholder="Ingrese el límite de usos (deje vacío para ilimitado)" min="0"
                                                       aria-describedby="iconMaxUsesUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconMaxUsesUpdate"><i class="fa fa-repeat" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="update_chkIsRecurring" name="chkIsRecurring">
                                                <label class="form-check-label" for="update_chkIsRecurring">¿Es Recurrente?</label>
                                            </div>
                                            <small class="form-text text-muted">Si está marcado, se aplica en todos los ciclos; si no, solo en la primera factura</small>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctStatus">Estado <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctStatus" name="slctStatus" required aria-describedby="iconStatusUpdate">
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