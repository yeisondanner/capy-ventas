<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-users"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-users fa-lg"></i></li>
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
                        <table class="table table-hover table-bordered table-sm" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Fecha de Nacimiento</th>
                                    <th>País</th>
                                    <th>Teléfono</th>
                                    <th>Usuario App</th>
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
                <h5 class="modal-title" id="modalSaveLabel">Registro de Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formSave" autocomplete="off">
                        <?= csrf(); ?>
                        <h5>Datos Personales</h5>
                        <hr>
                        <div class="bg-light p-2 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtNames">Nombres <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" id="txtNames" name="txtNames" required 
                                                placeholder="Ingrese los nombres" maxlength="255"
                                                pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconNames">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconNames"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtLastname">Apellidos <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" id="txtLastname" name="txtLastname" required 
                                                placeholder="Ingrese los apellidos" maxlength="255"
                                                pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconLastname">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconLastname"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtEmail">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                                class="form-control" id="txtEmail" name="txtEmail" required
                                                placeholder="Ingrese el correo electrónico"
                                                title="Por favor, ingrese un correo electrónico válido."
                                                oninput="this.value = this.value.toLowerCase();"
                                                aria-describedby="iconEmail">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconEmail"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtDateOfBirth">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="txtDateOfBirth" name="txtDateOfBirth" required
                                                aria-describedby="iconDateOfBirth">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconDateOfBirth"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="txtCountry">País <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="txtCountry" name="txtCountry" required
                                                placeholder="Ingrese el país" maxlength="50"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconCountry">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconCountry"><i class="fa fa-globe" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="txtTelephonePrefix">Prefijo Tel. <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="txtTelephonePrefix" name="txtTelephonePrefix" required
                                                placeholder="+51" maxlength="7"
                                                aria-describedby="iconPrefix">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPrefix"><i class="fa fa-phone" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="txtPhoneNumber">Número de Teléfono <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="txtPhoneNumber" name="txtPhoneNumber" required
                                                placeholder="987654321" maxlength="11" pattern="^\d+$"
                                                title="Solo se permiten números"
                                                aria-describedby="iconPhone">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPhone"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="slctStatus">Estado <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" id="slctStatus" name="slctStatus" required aria-describedby="iconStatus">
                                        <option value="" selected disabled>Seleccione un elemento</option>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="iconStatus"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5>Datos de Usuario de la App (Opcional)</h5>
                        <hr>
                        <div class="bg-light p-2 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtUser">Usuario</label>
                                        <div class="input-group">
                                            <input type="text" pattern="^[a-zA-Z0-9_-]{3,15}$" minlength="3" maxlength="15" 
                                                class="form-control" id="txtUser" name="txtUser"
                                                placeholder="Ingrese el usuario"
                                                title="El usuario debe tener entre 3 y 15 caracteres y solo puede contener letras, números, guiones bajos (_) o guiones (-)."
                                                aria-describedby="iconUser">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconUser"><i class="fa fa-id-badge" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Si se proporciona usuario, también debe proporcionar contraseña.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtPassword">Contraseña</label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="txtPassword" name="txtPassword"
                                                placeholder="Ingrese la contraseña" minlength="8"
                                                aria-describedby="iconPassword">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPassword"><i class="fa fa-lock" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Mínimo 8 caracteres. Requerida si se proporciona usuario.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="slctUserStatus">Estado del Usuario</label>
                                <div class="input-group">
                                    <select class="form-control" id="slctUserStatus" name="slctUserStatus" aria-describedby="iconUserStatus">
                                        <option value="Activo" selected>Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="iconUserStatus"><i class="fa fa-check-square" aria-hidden="true"></i></span>
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
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Cliente</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-uppercase font-weight-bold text-primary" id="reportFullName"></h3>
                    </div>
                </div>
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Datos Personales</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Nombres</strong></td>
                            <td id="reportNames"></td>
                        </tr>
                        <tr>
                            <td><strong>Apellidos</strong></td>
                            <td id="reportLastname"></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td id="reportEmail"></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Nacimiento</strong></td>
                            <td id="reportDateOfBirth"></td>
                        </tr>
                        <tr>
                            <td><strong>País</strong></td>
                            <td id="reportCountry"></td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono</strong></td>
                            <td id="reportPhone"></td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td id="reportStatus"></td>
                        </tr>
                    </tbody>
                </table>
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Datos de Usuario de la App</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Usuario</strong></td>
                            <td id="reportUser">Sin usuario</td>
                        </tr>
                        <tr>
                            <td><strong>Contraseña</strong></td>
                            <td id="reportPassword">-</td>
                        </tr>
                        <tr>
                            <td><strong>Estado del Usuario</strong></td>
                            <td id="reportUserStatus">-</td>
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
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <input type="hidden" name="update_txtId" id="update_txtId">
                        <h5>Datos Personales</h5>
                        <div class="bg-light p-2 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtNames">Nombres <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" id="update_txtNames" name="update_txtNames" required 
                                                placeholder="Ingrese los nombres" maxlength="255"
                                                pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconNamesUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconNamesUpdate"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtLastname">Apellidos <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" id="update_txtLastname" name="update_txtLastname" required 
                                                placeholder="Ingrese los apellidos" maxlength="255"
                                                pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconLastnameUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconLastnameUpdate"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtEmail">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                                class="form-control" id="update_txtEmail" name="update_txtEmail" required
                                                placeholder="Ingrese el correo electrónico"
                                                oninput="this.value = this.value.toLowerCase();"
                                                aria-describedby="iconEmailUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconEmailUpdate"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtDateOfBirth">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="update_txtDateOfBirth" name="update_txtDateOfBirth" required
                                                aria-describedby="iconDateOfBirthUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconDateOfBirthUpdate"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtCountry">País <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="update_txtCountry" name="update_txtCountry" required
                                                placeholder="Ingrese el país" maxlength="50"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconCountryUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconCountryUpdate"><i class="fa fa-globe" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtTelephonePrefix">Prefijo Tel. <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="update_txtTelephonePrefix" name="update_txtTelephonePrefix" required
                                                placeholder="+51" maxlength="7"
                                                aria-describedby="iconPrefixUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPrefixUpdate"><i class="fa fa-phone" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtPhoneNumber">Número de Teléfono <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="update_txtPhoneNumber" name="update_txtPhoneNumber" required
                                                placeholder="987654321" maxlength="11" pattern="^\d+$"
                                                title="Solo se permiten números"
                                                aria-describedby="iconPhoneUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPhoneUpdate"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="update_slctStatus">Estado <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" id="update_slctStatus" name="update_slctStatus" required aria-describedby="iconStatusUpdate">
                                        <option value="" selected disabled>Seleccione un elemento</option>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="iconStatusUpdate"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5>Datos de Usuario de la App (Opcional)</h5>
                        <hr>
                        <div class="bg-light p-2 rounded">
                            <input type="hidden" name="update_txtUserAppId" id="update_txtUserAppId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtUser">Usuario</label>
                                        <div class="input-group">
                                            <input type="text" pattern="^[a-zA-Z0-9_-]{3,15}$" minlength="3" maxlength="15" 
                                                class="form-control" id="update_txtUser" name="update_txtUser"
                                                placeholder="Ingrese el usuario"
                                                title="El usuario debe tener entre 3 y 15 caracteres y solo puede contener letras, números, guiones bajos (_) o guiones (-)."
                                                aria-describedby="iconUserUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconUserUpdate"><i class="fa fa-id-badge" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Si se proporciona usuario, también debe proporcionar contraseña.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="update_txtPassword">Contraseña</label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="update_txtPassword" name="update_txtPassword"
                                                placeholder="Deje vacío para mantener la actual" minlength="8"
                                                aria-describedby="iconPasswordUpdate">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconPasswordUpdate"><i class="fa fa-lock" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Mínimo 8 caracteres. Deje vacío para mantener la contraseña actual.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="update_slctUserStatus">Estado del Usuario</label>
                                <div class="input-group">
                                    <select class="form-control" id="update_slctUserStatus" name="update_slctUserStatus" aria-describedby="iconUserStatusUpdate">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="iconUserStatusUpdate"><i class="fa fa-check-square" aria-hidden="true"></i></span>
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
