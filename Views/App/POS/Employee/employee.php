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
            <div class="tile rounded-3">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $createEmployee = (int) (validate_permission_app(5, "c", false)) ? (int)validate_permission_app(5, "c", false)['create'] : 0;
                    if ($createEmployee === 1): ?>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="btnOpenEmployeeModal">
                            <i class="bi bi-plus-lg"></i> Agregar nuevo empleado
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped" id="table" data-token="<?= csrf(false); ?>">
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
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="txtEmployeeUserSearch" class="form-label">Usuario o correo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="txtEmployeeUserSearch" name="txtEmployeeUserSearch" maxlength="120" placeholder="Ingresa usuario o correo registrado" list="employeeUserSuggestions" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnSearchEmployeeUser"><i class="bi bi-search"></i> Buscar</button>
                        </div>
                        <small class="text-muted">Carga un usuario existente para vincularlo como empleado.</small>
                        <datalist id="employeeUserSuggestions"></datalist>
                        <input type="hidden" id="txtEmployeeUserappId" name="txtEmployeeUserappId">
                    </div>
                    <div class="col-md-4">
                        <label for="txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtEmployeeRolapp" name="txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="employee-user-preview shadow-sm">
                            <div class="employee-user-preview__icon text-primary">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="employee-user-preview__content">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div>
                                        <p class="text-muted small mb-1">Nombre completo</p>
                                        <h5 class="mb-1" id="displayEmployeeFullName">Sin usuario seleccionado</h5>
                                        <p class="mb-0 text-muted" id="displayEmployeeEmail">-</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted small mb-1">Usuario</p>
                                        <span class="badge bg-info text-dark" id="displayEmployeeUser">No asignado</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small" id="displayEmployeeNote">Busca un usuario para mostrar sus datos antes de guardar.</div>
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

<!-- Modal: Reporte de empleado -->
<div class="modal fade" id="modalEmployeeReport" tabindex="-1" aria-labelledby="modalEmployeeReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalEmployeeReportLabel">Reporte del empleado</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_txtEmployeeId" id="update_txtEmployeeId">
                <input type="hidden" name="update_txtEmployeeUserappId" id="update_txtEmployeeUserappId">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="update_txtEmployeeUserSearch" class="form-label">Usuario o correo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="update_txtEmployeeUserSearch" name="update_txtEmployeeUserSearch" maxlength="120" placeholder="Ingresa usuario o correo registrado" list="employeeUserSuggestionsUpdate" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnSearchEmployeeUserUpdate"><i class="bi bi-search"></i> Buscar</button>
                        </div>
                        <small class="text-muted">Solo puedes vincular usuarios activos y disponibles.</small>
                        <datalist id="employeeUserSuggestionsUpdate"></datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtEmployeeRolapp" name="update_txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="employee-user-preview shadow-sm">
                            <div class="employee-user-preview__icon text-success">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="employee-user-preview__content">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div>
                                        <p class="text-muted small mb-1">Nombre completo</p>
                                        <h5 class="mb-1" id="update_displayEmployeeFullName">Sin usuario seleccionado</h5>
                                        <p class="mb-0 text-muted" id="update_displayEmployeeEmail">-</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted small mb-1">Usuario</p>
                                        <span class="badge bg-success bg-opacity-75 text-dark" id="update_displayEmployeeUser">No asignado</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small" id="update_displayEmployeeNote">Busca un usuario activo para actualizar la asignación.</div>
                            </div>
                        </div>
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