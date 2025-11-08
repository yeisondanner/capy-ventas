<?= headerAdmin($data) ?>

<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-flag-checkered" aria-hidden="true"></i> <?= $data["page_title"] ?>
            </h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-flag-checkered" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a
                    href="<?= base_url() ?>/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="tile">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalSave">
            <i class="fa fa-plus"></i> Nuevo
        </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
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

<!-- Sección de Modals -->
<!-- Modal Save -->
<div class="modal fade" id="modalSave" tabindex="-1" role="dialog" aria-labelledby="modalSaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSaveLabel">Registro de Roles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Registro de Roles -->
                <div class="tile-body">
                    <form id="formSave" enctype="multipart/form-data" autocomplete="off">
                        <?= csrf(); ?>

                        <div class="form-group">
                            <label class="control-label" for="txtRoleName">Nombre
                                <span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="text" id="txtRoleName" name="txtRoleName" required
                                placeholder="Ingrese el nombre del rol" maxlength="250" minlength="4"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,250}$"
                                title="El nombre debe contener entre 4 y 250 caracteres y solo incluir letras y espacios.">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtRoleDescription">Descripción
                            </label>
                            <textarea class="form-control" id="txtRoleDescription" name="txtRoleDescription"
                                minlength="20" pattern="^[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+$"
                                placeholder="Ingrese la descripción del rol"></textarea>
                            <small class="form-text text-muted">
                                La descripción debe tener al menos 20 caracteres y solo puede incluir letras, números,
                                espacios, guiones altos y bajos.
                            </small>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-fw fa-lg fa-save"></i> Registrar
                            </button>
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
<div class="modal fade" id="confirmModalDelete" tabindex="-1" role="dialog" aria-labelledby="confirmModalDeleteLabel"
    aria-hidden="true">
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
                <button type="button" class="btn btn-danger" data-token="<?= csrf(false); ?>" id="confirmDelete">
                    <i class="fa fa-check"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Update-->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del rol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Actualziacion de Usuario -->
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <input type="hidden" name="update_txtId" id="update_txtId">
                        <div class="form-group">
                            <label class="control-label" for="update_txtRoleName">Nombre
                                <span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="text" id="update_txtRoleName" name="update_txtRoleName"
                                required placeholder="Ingrese el nombre del rol" maxlength="250" minlength="4"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,250}$"
                                title="El nombre debe contener entre 4 y 250 caracteres y solo incluir letras y espacios.">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="update_txtRoleDescription">Descripción
                            </label>
                            <textarea class="form-control" id="update_txtRoleDescription"
                                name="update_txtRoleDescription" minlength="20"
                                pattern="^[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+$"
                                placeholder="Ingrese la descripción del rol"></textarea>
                            <small class="form-text text-muted">
                                La descripción debe tener al menos 20 caracteres y solo puede incluir letras,
                                números,
                                espacios, guiones altos y bajos.
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="update_txtRoleStatus">Estado
                                <span class="text-danger">*</span></label>
                            <select class="form-control" id="update_txtRoleStatus" name="update_txtRoleStatus" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-success btn-block" type="submit">
                                <i class="fa fa-fw fa-lg fa-pencil"></i>Actualizar</button>
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
<!-- Modal de Report -->
<div class="modal fade" id="modalReport" tabindex="-1" role="dialog" aria-labelledby="modalReportLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte del Rol</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Contenedor principal con foto y datos -->
                <!-- Contenedor principal con foto y datos -->
                <div class="d-flex justify-content-center  align-items-center">
                    <h3 class="text-uppercase font-weight-bold text-primary" id="reportTitle">Título del registro</h3>
                </div>
                <!-- Datos Personales -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Rol</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Código</strong></td>
                            <td id="reportCode">#12345678</td>
                        </tr>
                        <tr>
                            <td><strong>Descripción</strong></td>
                            <td id="reportDescription">12345678</td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td id="reportEstado">Root</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Datos Personales -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Detalle de permisos habilitados</h6>
                <hr>
                <div id="datailsModules"></div>
                <!-- Datos de registro: Fecha de registro y actualización -->
                <div class="p-3 bg-light border rounded">
                    <p class="text-muted mb-1">
                        <strong>Fecha de registro:</strong> <span class="text-dark"
                            id="reportRegistrationDate">29/01/2025</span>
                    </p>
                    <p class="text-muted mb-0">
                        <strong>Fecha de actualización:</strong> <span class="text-dark"
                            id="reportUpdateDate">29/01/2025</span>
                    </p>
                </div>
            </div>
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!--Modal que permite agregar permiso de acceso a un rol-->
<!-- Modal de Report -->
<div class="modal fade" id="modalPermission" tabindex="-1" role="dialog" aria-labelledby="modalPermissionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title font-weight-bold" id="modalPermissionLabel">Gestión de permisos</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="card  mb-3">
                    <div class="card-header text-center">
                        <h4 class="font-weight-bold" id="permissionTitleRol">Root</h4>
                        <p class="mb-0 text-justify" id="permissionDescriptionRol">Lorem ipsum dolor sit amet
                            consectetur adipisicing elit. Non
                            quos quae libero nam inventore, itaque doloremque fugit tenetur mollitia sed rerum veritatis
                            voluptatum molestiae neque quisquam? Aliquid assumenda labore molestias.</p>
                    </div>
                    <div class="card-body">
                        <div class="row" id="modulesInterfaces">
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-light border rounded mb-3">
                    <h5 class="text-center font-weight-bold mb-3">Información adicional</h5>
                    <div class="row">
                        <div class="col-md-6 border p-2 font-weight-bold d-flex align-items-center">
                            Tipo de interfaz
                        </div>
                        <div class="col-md-6 border p-2 text-center">
                            <span class="badge badge-info">Vista</span>
                            <span class="badge badge-primary">Opción</span>
                        </div>

                        <div class="col-md-6 border p-2 font-weight-bold d-flex align-items-center">
                            ¿Es de acceso público?
                        </div>
                        <div class="col-md-6 border p-2 text-center">
                            <span class="badge badge-danger">Público</span>
                            <span class="badge badge-success">Privado</span>
                        </div>

                        <div class="col-md-6 border p-2 font-weight-bold d-flex align-items-center">
                            ¿Se mostrará en la navegación del sistema?
                        </div>
                        <div class="col-md-6 border p-2 text-center">
                            <span class="badge badge-warning">Visible en navegación</span>
                            <span class="badge badge-secondary">Oculto en navegación</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-block" id="btn-send-permission"> <i
                        class="fa fa-save"></i>
                    Guardar</button>
            </div>
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>