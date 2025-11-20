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
                    <button class="btn btn-primary" type="button" id="btnOpenRoleModal">
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

<!-- Modal: Registrar/editar rol -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="roleForm" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="roleModalLabel">Registrar rol</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="roleId" id="roleId" value="0">
                <div class="mb-3">
                    <label for="txtRoleAppName" class="form-label">Nombre del rol <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="txtRoleAppName" name="txtRoleAppName" maxlength="255" required
                        placeholder="Ej: Cajero, Supervisor">
                </div>
                <div class="mb-3">
                    <label for="txtRoleAppDescription" class="form-label">Descripción</label>
                    <textarea class="form-control" id="txtRoleAppDescription" name="txtRoleAppDescription" rows="3"
                        placeholder="Describe las funciones o alcance del rol"></textarea>
                </div>
                <div class="mb-3 d-none" id="roleStatusGroup">
                    <label for="txtRoleAppStatus" class="form-label">Estado</label>
                    <select class="form-select" id="txtRoleAppStatus" name="txtRoleAppStatus" disabled>
                        <option value="Activo" selected>Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </form>
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
