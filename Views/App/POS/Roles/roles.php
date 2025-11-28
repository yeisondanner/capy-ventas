<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-shield-check"></i> Roles de aplicación</h1>
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
                    <button class="btn btn-primary" type="button" id="btnAddRole">
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
                                    <th>Nombre</th>
                                    <th>Descripción</th>
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

<!-- Agregar Role and permissions -->
<div class="modal fade" id="openModalRole" tabindex="-1" aria-labelledby="openModalRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h1 class="modal-title fs-5" id="openModalRoleLabel">Registrar Rol</h1>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="txtName" class="form-label fw-bold">Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" class="form-control" id="txtName" placeholder="Ej. Administrador">
                        </div>
                        <div class="mb-3">
                            <label for="txtDescription" class="form-label fw-bold">Descripción <span class="fw-medium text-muted"><i>(Opcional)</i></span></label>
                            <textarea class="form-control" id="txtDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <h6 class="card-header bg-secondary text-white"><i class="bi bi-shield-check"></i> Permisos</h6>
                            <div class="card-body pb-0" id="cardPermissions" style="max-height: 50vh; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><i class="bi bi-save2"></i> Guardar</button>
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