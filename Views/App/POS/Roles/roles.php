<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-ui-checks-grid"></i> Roles de aplicación</h1>
            <p>Administra los roles disponibles para asignar a los empleados de tu negocio.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item active" aria-current="page">Roles</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="tile">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="button" id="btnOpenModalAddRole">
                        <i class="bi bi-plus-lg"></i> Registrar rol
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="rolesTable" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Actualizado</th>
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

<!-- Modal: Add Role and permissions -->
<div class="modal fade" id="modalAddRole" tabindex="-1" aria-labelledby="modalAddRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h1 class="modal-title fs-5" id="modalAddRoleLabel">Registrar Rol</h1>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="txtName" class="form-label fw-bold"><i class="bi bi-shield-check"></i> Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" class="form-control" id="txtName" placeholder="Ej. Administrador">
                        </div>
                        <div class="mb-3">
                            <label for="txtDescription" class="form-label fw-bold"><i class="bi bi-chat-left-text"></i> Descripción <span class="fw-medium text-muted"><i>(Opcional)</i></span></label>
                            <textarea class="form-control" id="txtDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <h6 class="card-header bg-secondary text-white"><i class="bi bi-ui-checks-grid"></i> Permisos</h6>
                            <div class="card-body pb-0" id="cardPermissions" style="max-height: 50vh; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="">
                    <label class="cust-chk-wrapper m-0">
                        <input type="checkbox" class="checkAllPermissions">
                        <span class="cust-chk-box"></span>
                        <span class="fw-semibold user-select-none">Marcar todos</span>
                    </label>
                </div>
                <button type="button" class="btn btn-primary" id="btnAddRole">
                    <i class="bi bi-save2"></i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Update Role and permissions -->
<div class="modal fade" id="modalUpdateRole" tabindex="-1" aria-labelledby="modalUpdateRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h1 class="modal-title fs-5" id="modalUpdateRoleLabel">Actualizar Rol #Administrador</h1>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="txtNameUpdate" class="form-label fw-bold"><i class="bi bi-shield-check"></i> Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" class="form-control" id="txtNameUpdate" placeholder="Ej. Administrador">
                        </div>
                        <div class="mb-3">
                            <label for="selectStatusUpdate" class="form-label fw-bold"><i class="bi bi-toggle-on"></i> Estado (<span class="text-danger">*</span>)</label>
                            <select class="form-select" id="selectStatusUpdate">
                                <option disabled>Seleccionar</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="txtDescriptionUpdate" class="form-label fw-bold"><i class="bi bi-chat-left-text"></i> Descripción <span class="fw-medium text-muted"><i>(Opcional)</i></span></label>
                            <textarea class="form-control" id="txtDescriptionUpdate" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <h6 class="card-header bg-secondary text-white"><i class="bi bi-ui-checks-grid"></i> Permisos</h6>
                            <div class="card-body pb-0" id="cardPermissionsUpdate" style="max-height: 50vh; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="">
                    <label class="cust-chk-wrapper m-0">
                        <input type="checkbox" class="checkAllPermissions">
                        <span class="cust-chk-box"></span>
                        <span class="fw-semibold user-select-none">Marcar todos</span>
                    </label>
                </div>
                <button type="button" class="btn btn-primary" id="btnUpdateRole">
                    <i class="bi bi-save2"></i>
                    Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detalle de rol -->
<div class="modal fade" id="roleReportModal" tabindex="-1" aria-labelledby="roleReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="roleReportModalLabel">Detalle del rol</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="reportRoleName">-</dd>
                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8" id="reportRoleDescription">-</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="reportRoleStatus">-</dd>
                    <dt class="col-sm-4">Última actualización</dt>
                    <dd class="col-sm-8" id="reportRoleUpdated">-</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Role And Permissions -->
<div class="modal fade" id="modalDeleteRole" tabindex="-1" aria-labelledby="modalDeleteRoleLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h1 class="modal-title fs-5" id="modalDeleteRoleLabel">Eliminar Rol #12</h1>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary"><i class="bi bi-trash"></i> Eliminar</button>
      </div>
    </div>
  </div>
</div>