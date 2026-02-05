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
                    $createEmployee = (int) validate_permission_app(5, "c", false)['create'];
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
                        <table class="table table-sm table-hover table-bordered table-striped" id="table"
                            data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Empleado</th>
                                    <th>Usuario App</th>
                                    <th>Rol</th>
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

<div class="modal fade" id="modalEmployee" tabindex="-1" aria-labelledby="modalEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formSaveEmployee" autocomplete="off">
            <div class="modal-header bg-primary text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalEmployeeLabel">Registrar empleado</h5>
                        <p class="mb-0 small text-white text-opacity-75">Gestión de personal</p>
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
                            <div class="col-md-8">
                                <label for="txtEmployeeUserSearch" class="form-label">Usuario o correo <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="txtEmployeeUserSearch"
                                        name="txtEmployeeUserSearch" maxlength="120"
                                        placeholder="Ingresa usuario o correo registrado" list="employeeUserSuggestions"
                                        required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="btnSearchEmployeeUser"><i class="bi bi-search"></i> Buscar</button>
                                </div>
                                <small class="text-muted">Carga un usuario existente para vincularlo como
                                    empleado.</small>
                                <datalist id="employeeUserSuggestions"></datalist>
                                <input type="hidden" id="txtEmployeeUserappId" name="txtEmployeeUserappId">
                            </div>
                            <div class="col-md-4">
                                <label for="txtEmployeeRolapp" class="form-label">Rol de Aplicación <span
                                        class="text-danger">*</span></label>
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
                                                <h5 class="mb-1" id="displayEmployeeFullName">Sin usuario seleccionado
                                                </h5>
                                                <p class="mb-0 text-muted" id="displayEmployeeEmail">-</p>
                                            </div>
                                            <div class="text-end">
                                                <p class="text-muted small mb-1">Usuario</p>
                                                <span class="badge bg-info text-dark" id="displayEmployeeUser">No
                                                    asignado</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-muted small" id="displayEmployeeNote">Busca un usuario
                                            para
                                            mostrar sus datos antes de guardar.</div>
                                    </div>
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
                                        <i class="bi bi-person-plus display-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3">Nuevo Empleado</h4>
                                <p class="mb-0 opacity-75">
                                    Registre un nuevo colaborador. Asegúrese de vincular un usuario existente y asignar
                                    el rol adecuado.
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

<!-- Modal: Reporte de empleado -->
<div class="modal fade" id="modalEmployeeReport" tabindex="-1" aria-labelledby="modalEmployeeReportLabel"
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
                        <h5 class="modal-title fw-bold mb-0" id="modalEmployeeReportLabel">Ficha del Empleado</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Información detallada del colaborador</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body" id="employeeReportContainer">
                <div class="receipt-container report-card-employee p-4 border rounded shadow-sm bg-white">

                    <!-- Header -->
                    <div
                        class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark" id="reportEmployeeName">--</h4>
                            <div class="small text-muted">Nombre del empleado</div>
                        </div>
                        <div><span id="reportEmployeeStatusBadge" class="badge">--</span></div>
                    </div>

                    <!-- Details -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Usuario de App:</label>
                            <div class="mt-1 fs-5" id="reportEmployeeUserApp">--</div>
                        </div>

                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Email:</label>
                            <div class="mt-1" id="reportEmployeeEmail">--</div>
                        </div>

                        <div class="col-12">
                            <hr class="my-3 opacity-25">
                        </div>

                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Rol:</label>
                            <div class="mt-1 fw-bold" id="reportEmployeeRole">--</div>
                        </div>

                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Estado:</label>
                            <div class="mt-1" id="reportEmployeeStatus">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Descripción del Rol:</label>
                            <div class="mt-1 small" id="reportEmployeeRoleDescription">--</div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar empleado -->
<div class="modal fade" id="modalUpdateEmployee" tabindex="-1" aria-labelledby="modalUpdateEmployeeLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" id="formUpdateEmployee" autocomplete="off">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalUpdateEmployeeLabel">Actualizar empleado</h5>
                        <p class="mb-0 small text-white text-opacity-75">Modifica los datos del colaborador</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_txtEmployeeId" id="update_txtEmployeeId">
                <input type="hidden" name="update_txtEmployeeUserappId" id="update_txtEmployeeUserappId">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="update_txtEmployeeUserSearch" class="form-label">Usuario o correo <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="update_txtEmployeeUserSearch"
                                        name="update_txtEmployeeUserSearch" maxlength="120"
                                        placeholder="Ingresa usuario o correo registrado"
                                        list="employeeUserSuggestionsUpdate" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="btnSearchEmployeeUserUpdate"><i class="bi bi-search"></i> Buscar</button>
                                </div>
                                <small class="text-muted">Solo puedes vincular usuarios activos y disponibles.</small>
                                <datalist id="employeeUserSuggestionsUpdate"></datalist>
                            </div>
                            <div class="col-md-4">
                                <label for="update_txtEmployeeRolapp" class="form-label">Rol de Aplicación <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="update_txtEmployeeRolapp"
                                    name="update_txtEmployeeRolapp" required>
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
                                                <h5 class="mb-1" id="update_displayEmployeeFullName">Sin usuario
                                                    seleccionado</h5>
                                                <p class="mb-0 text-muted" id="update_displayEmployeeEmail">-</p>
                                            </div>
                                            <div class="text-end">
                                                <p class="text-muted small mb-1">Usuario</p>
                                                <span class="badge bg-success bg-opacity-75 text-dark"
                                                    id="update_displayEmployeeUser">No asignado</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-muted small" id="update_displayEmployeeNote">Busca un
                                            usuario activo para actualizar la asignación.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="update_txtEmployeeStatus" class="form-label">Estado <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="update_txtEmployeeStatus"
                                    name="update_txtEmployeeStatus" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
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
                                    Actualice la información del empleado. Asegúrese de guardar los cambios para
                                    mantener
                                    la integridad de los datos.
                                </p>
                            </div>
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