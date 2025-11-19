<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-people"></i> Empleados</h1>
            <p>Administra los empleados de tu negocio: registra nuevos empleados, asigna roles y gestiona su estado</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/employee">Empleados</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="button" id="btnOpenEmployeeModal">
                        <i class="bi bi-plus-lg"></i> Agregar nuevo empleado
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Empleado</th>
                                    <th>Usuario App</th>
                                    <th>Rol</th>
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

<!-- Modal: Registrar empleado -->
<div class="modal fade" id="modalEmployee" tabindex="-1" aria-labelledby="modalEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formSaveEmployee" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEmployeeLabel">Registrar empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="txtEmployeeUserapp" class="form-label">Usuario de Aplicación (opcional)</label>
                        <select class="form-select" id="txtEmployeeUserapp" name="txtEmployeeUserapp">
                            <option value="" selected>Sin usuario asignado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtEmployeeRolapp" name="txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtEmployeeStatus" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtEmployeeStatus" name="txtEmployeeStatus" required>
                            <option value="Activo" selected>Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
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

<!-- Modal: Reporte de empleado -->
<div class="modal fade" id="modalEmployeeReport" tabindex="-1" aria-labelledby="modalEmployeeReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalEmployeeReportLabel">Reporte del empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h4 class="mb-0" id="reportEmployeeName">Nombre del empleado</h4>
                    <small class="text-muted" id="reportEmployeeStatusBadge">Estado</small>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Usuario de Aplicación</dt>
                    <dd class="col-sm-8" id="reportEmployeeUserApp">-</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8" id="reportEmployeeEmail">-</dd>
                    <dt class="col-sm-4">Rol</dt>
                    <dd class="col-sm-8" id="reportEmployeeRole">-</dd>
                    <dt class="col-sm-4">Descripción del Rol</dt>
                    <dd class="col-sm-8" id="reportEmployeeRoleDescription">Sin descripción registrada.</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="reportEmployeeStatus">-</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar empleado -->
<div class="modal fade" id="modalUpdateEmployee" tabindex="-1" aria-labelledby="modalUpdateEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formUpdateEmployee" autocomplete="off">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateEmployeeLabel">Actualizar empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_txtEmployeeId" id="update_txtEmployeeId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="update_txtEmployeeUserapp" class="form-label">Usuario de Aplicación (opcional)</label>
                        <select class="form-select" id="update_txtEmployeeUserapp" name="update_txtEmployeeUserapp">
                            <option value="">Sin usuario asignado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtEmployeeRolapp" name="update_txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtEmployeeStatus" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtEmployeeStatus" name="update_txtEmployeeStatus" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
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
