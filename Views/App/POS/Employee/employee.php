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
                        <label for="txtEmployeeNames" class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtEmployeeNames" name="txtEmployeeNames" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="txtEmployeeLastname" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtEmployeeLastname" name="txtEmployeeLastname" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="txtEmployeeEmail" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="txtEmployeeEmail" name="txtEmployeeEmail" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label for="txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtEmployeeRolapp" name="txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="chkEmployeeCreateUser" name="chkEmployeeCreateUser">
                            <label class="form-check-label" for="chkEmployeeCreateUser">Crear usuario para ingresar al sistema</label>
                        </div>
                    </div>
                    <div class="col-12 d-none" id="employeeUserFields">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="txtEmployeeUser" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="txtEmployeeUser" name="txtEmployeeUser" maxlength="80">
                            </div>
                            <div class="col-md-6">
                                <label for="txtEmployeePassword" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="txtEmployeePassword" name="txtEmployeePassword" minlength="8">
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
                        <label for="update_txtEmployeeNames" class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="update_txtEmployeeNames" name="update_txtEmployeeNames" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtEmployeeLastname" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="update_txtEmployeeLastname" name="update_txtEmployeeLastname" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtEmployeeEmail" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="update_txtEmployeeEmail" name="update_txtEmployeeEmail" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtEmployeeRolapp" class="form-label">Rol de Aplicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtEmployeeRolapp" name="update_txtEmployeeRolapp" required>
                            <option value="" selected disabled>Selecciona un rol</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update_chkEmployeeCreateUser" name="update_chkEmployeeCreateUser">
                            <label class="form-check-label" for="update_chkEmployeeCreateUser">Crear usuario para ingresar al sistema</label>
                        </div>
                    </div>
                    <div class="col-12 d-none" id="update_employeeUserFields">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="update_txtEmployeeUser" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="update_txtEmployeeUser" name="update_txtEmployeeUser" maxlength="80">
                            </div>
                            <div class="col-md-6">
                                <label for="update_txtEmployeePassword" class="form-label">Contraseña (dejar vacío para mantener)</label>
                                <input type="password" class="form-control" id="update_txtEmployeePassword" name="update_txtEmployeePassword" minlength="8">
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
