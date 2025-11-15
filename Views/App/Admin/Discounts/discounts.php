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
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Plan Aplicable</th>
                                    <th>Máx. Usos</th>
                                    <th>Recurrente</th>
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
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Datos del Descuento</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtCode">Código <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="txtCode" name="txtCode" required
                                                       placeholder="Ej: CAPY10, DESCUENTO20" maxlength="50"
                                                       pattern="^[A-Z0-9\-]+$"
                                                       oninput="this.value = this.value.toUpperCase()"
                                                       aria-describedby="iconCode">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCode"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Solo letras mayúsculas, números y guiones. Máximo 50 caracteres.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctType">Tipo <span class="text-danger">*</span></label>
                                            <select class="form-control" id="slctType" name="slctType" required>
                                                <option value="" selected disabled>Seleccione un tipo</option>
                                                <option value="percentage">Porcentaje (%)</option>
                                                <option value="fixed">Monto Fijo ($)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtValue">Valor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend" id="valuePrefix">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <input class="form-control" type="number" id="txtValue" name="txtValue" required
                                                       placeholder="0.00" step="0.01" min="0"
                                                       aria-describedby="iconValue">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconValue"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted" id="valueHelp">Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctAppliesToPlanId">Plan Aplicable</label>
                                            <select class="form-control" id="slctAppliesToPlanId" name="slctAppliesToPlanId">
                                                <option value="" selected>Todos los planes</option>
                                            </select>
                                            <small class="form-text text-muted">Si no se selecciona, aplica a todos los planes.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtStartDate">Fecha de Inicio</label>
                                            <div class="input-group">
                                                <input class="form-control" type="datetime-local" id="txtStartDate" name="txtStartDate"
                                                       aria-describedby="iconStartDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconStartDate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Fecha y hora desde la que el descuento está vigente.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtEndDate">Fecha de Fin</label>
                                            <div class="input-group">
                                                <input class="form-control" type="datetime-local" id="txtEndDate" name="txtEndDate"
                                                       aria-describedby="iconEndDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconEndDate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Fecha y hora hasta la que el descuento está vigente.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtMaxUses">Máximo de Usos</label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" id="txtMaxUses" name="txtMaxUses"
                                                       placeholder="Ilimitado" min="1"
                                                       aria-describedby="iconMaxUses">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconMaxUses"><i class="fa fa-repeat" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Dejar vacío para uso ilimitado.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctIsRecurring">¿Es Recurrente? <span class="text-danger">*</span></label>
                                            <select class="form-control" id="slctIsRecurring" name="slctIsRecurring" required>
                                                <option value="0">No (Solo primera factura)</option>
                                                <option value="1">Sí (Todos los ciclos)</option>
                                            </select>
                                            <small class="form-text text-muted">Si es recurrente, se aplica en todos los ciclos de facturación.</small>
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
                            <td><strong>Fecha de Inicio</strong></td>
                            <td id="reportStartDate"></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Fin</strong></td>
                            <td id="reportEndDate"></td>
                        </tr>
                        <tr>
                            <td><strong>Plan Aplicable</strong></td>
                            <td id="reportPlanName"></td>
                        </tr>
                        <tr>
                            <td><strong>Máximo de Usos</strong></td>
                            <td id="reportMaxUses"></td>
                        </tr>
                        <tr>
                            <td><strong>¿Es Recurrente?</strong></td>
                            <td id="reportIsRecurring"></td>
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
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Descuento</h5>
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
                                <input type="hidden" name="idDiscount" id="update_idDiscount">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtCode">Código <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="update_txtCode" name="update_txtCode" required
                                                       placeholder="Ej: CAPY10, DESCUENTO20" maxlength="50"
                                                       pattern="^[A-Z0-9\-]+$"
                                                       oninput="this.value = this.value.toUpperCase()"
                                                       aria-describedby="iconUpdateCode">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUpdateCode"><i class="fa fa-tag" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Solo letras mayúsculas, números y guiones. Máximo 50 caracteres.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctType">Tipo <span class="text-danger">*</span></label>
                                            <select class="form-control" id="update_slctType" name="update_slctType" required>
                                                <option value="" selected disabled>Seleccione un tipo</option>
                                                <option value="percentage">Porcentaje (%)</option>
                                                <option value="fixed">Monto Fijo ($)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtValue">Valor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend" id="updateValuePrefix">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <input class="form-control" type="number" id="update_txtValue" name="update_txtValue" required
                                                       placeholder="0.00" step="0.01" min="0"
                                                       aria-describedby="iconUpdateValue">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUpdateValue"><i class="fa fa-dollar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted" id="updateValueHelp">Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctAppliesToPlanId">Plan Aplicable</label>
                                            <select class="form-control" id="update_slctAppliesToPlanId" name="update_slctAppliesToPlanId">
                                                <option value="" selected>Todos los planes</option>
                                            </select>
                                            <small class="form-text text-muted">Si no se selecciona, aplica a todos los planes.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtStartDate">Fecha de Inicio</label>
                                            <div class="input-group">
                                                <input class="form-control" type="datetime-local" id="update_txtStartDate" name="update_txtStartDate"
                                                       aria-describedby="iconUpdateStartDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUpdateStartDate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Fecha y hora desde la que el descuento está vigente.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtEndDate">Fecha de Fin</label>
                                            <div class="input-group">
                                                <input class="form-control" type="datetime-local" id="update_txtEndDate" name="update_txtEndDate"
                                                       aria-describedby="iconUpdateEndDate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUpdateEndDate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Fecha y hora hasta la que el descuento está vigente.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtMaxUses">Máximo de Usos</label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" id="update_txtMaxUses" name="update_txtMaxUses"
                                                       placeholder="Ilimitado" min="1"
                                                       aria-describedby="iconUpdateMaxUses">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUpdateMaxUses"><i class="fa fa-repeat" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Opcional. Dejar vacío para uso ilimitado.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctIsRecurring">¿Es Recurrente? <span class="text-danger">*</span></label>
                                            <select class="form-control" id="update_slctIsRecurring" name="update_slctIsRecurring" required>
                                                <option value="0">No (Solo primera factura)</option>
                                                <option value="1">Sí (Todos los ciclos)</option>
                                            </select>
                                            <small class="form-text text-muted">Si es recurrente, se aplica en todos los ciclos de facturación.</small>
                                        </div>
                                    </div>
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
