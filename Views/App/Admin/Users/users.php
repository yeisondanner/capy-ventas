<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-users"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-users fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="<?= base_url() ?>/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="tile">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalSave"><i
                class="fa fa-plus"></i> Nuevo</button>
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
                                    <th>Nombres Completos</th>
                                    <th>DNI</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Capacidad de Almacenamiento</th>
                                    <th>Carpeta Raiz</th>
                                    <th>Online</th>
                                    <th title="Intentos de Inicio de Sesión">I.I.S.</th>
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

<!-- Seccion de Modals -->
<!-- Modal Save-->
<div class="modal fade" id="modalSave" tabindex="-1" role="dialog" aria-labelledby="modalSaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSaveLabel">Registro de Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Registro de Usuario -->
                <div class="tile-body">
                    <form id="formSave" enctype="multipart/form-data" autocomplete="off">
                        <?= csrf(); ?>
                        <h5>Datos Personales</h5>
                        <hr>
                        <div class="bg-light p-2 rounded">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label" for="txtFullName">Nombres Completos <span
                                                class="text-danger">*</span> </label>

                                        <div class="input-group">
                                            <input class="form-control" type="text" id="txtFullName" name="txtFullName"
                                                required placeholder="Ingrese sus nombres completos" maxlength="200"
                                                minlength="20"
                                                pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                                oninput="this.value = this.value.toUpperCase()"
                                                aria-describedby="iconFullname">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconFullname"><i class="fa fa-user"
                                                        aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="txtDNI">DNI <span class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input class="form-control" type="text" maxlength="8" minlength="8"
                                                pattern="^\d{8}$" id="txtDNI" name="txtDNI" required
                                                placeholder="Ingrese su número de DNI (Documento Nacional de Identidad)">
                                            <div class=" input-group-prepend">
                                                <button class="btn btn-outline-primary input-group-text" type="button"
                                                    id="btnSearchApi"><i class="fa fa-search"
                                                        aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Género <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" value="Masculino" type="radio"
                                            name="txtGender">Masculino
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" value="Femenino" type="radio"
                                            name="txtGender">Femenino
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" value="Otro" type="radio" name="txtGender">Otro
                                    </label>
                                </div>
                            </div>
                        </div>
                        <h5>Datos de la Cuenta</h5>
                        <hr>
                        <div class="bg-light p-2 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtUser">Usuario <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" pattern="^[a-zA-Z0-9_-]{3,15}$" minlength="3"
                                                maxlength="15" class="form-control" id="txtUser" name="txtUser"
                                                placeholder="Ingrese su usuario"
                                                title="El usuario debe tener entre 3 y 15 caracteres y solo puede contener letras, números, guiones bajos (_) o guiones (-)."
                                                required aria-describedby="iconUser">
                                            <div class=" input-group-prepend">
                                                <span class="input-group-text" id="iconUser"><i class="fa fa-id-badge"
                                                        aria-hidden="true"></i></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtEmail">Email <span
                                                class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input type="email"
                                                pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" minlength="2"
                                                class="form-control" id="txtEmail" name="txtEmail"
                                                placeholder="Ingrese su correo que desea vincular a la cuenta"
                                                title="Por favor, ingrese un correo electrónico válido (ejemplo: usuario@dominio.com)."
                                                required oninput="this.value = this.value.toLowerCase();"
                                                aria-describedby="iconMail">
                                            <div class=" input-group-prepend">
                                                <span class="input-group-text" id="iconMail"><i class="fa fa-envelope"
                                                        aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="slctRole">Rol <span
                                                class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <select class="form-control" id="slctRole" name="slctRole" required
                                                aria-describedby="iconRole">
                                                <option value="" selected disabled>Seleccione un elemento</option>
                                            </select>
                                            <div class=" input-group-prepend">
                                                <span class="input-group-text" id="iconRole"><i class="fa fa-users"
                                                        aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="txtPassword">Contraseña <span
                                                class="text-danger">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input class="form-control" type="password" id="txtPassword"
                                                name="txtPassword" required
                                                placeholder="Ingrese su correo que desea vincular a la cuenta"
                                                minlength="8" aria-describedby="iconPassword">
                                            <div class=" input-group-prepend">
                                                <span class="input-group-text" id="iconPassword"><i class="fa fa-lock"
                                                        aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="flPhoto">Foto de Perfil (JPG,JPEG,PNG) <span
                                        class="text-danger">2 MB</span></label>

                                <div class="input-group">
                                    <input class="form-control" type="file" id="flPhoto" name="flPhoto"
                                        accept="image/jpg, image/png, image/jpeg" aria-describedby="iconProfile">
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text" id="iconProfile"><i class="fa fa-upload"
                                                aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex" style="justify-content: center;">
                                <img class="upload-img-profile" id="imgNewProfile" alt="Previsualización"
                                    src="<?= base_url() ?>/loadfile/profile/?f=user.png">
                            </div>
                        </div>
                        <h5>Adicionales a la cuenta</h5>
                        <hr>
                        <div class="bg-light p-4 rounded">
                            <div class="row">
                                <!-- Espacio de almacenamiento -->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label" for="rngStorage">Espacio de almacenamiento
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <!-- Icono -->
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconSpace">
                                                    <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <!-- Rango -->
                                            <input type="range" class="form-control" id="rngStorage" name="rngStorage"
                                                min="0" max="1024" value="2" required>
                                            <!-- Número -->
                                            <input type="number" class="form-control" id="numStorage" name="numStorage"
                                                min="0" max="1024" value="2" required style="max-width: 80px;">
                                            <!-- Etiqueta GB -->
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="lblStorage">2 GB</span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Si asigna <strong>0</strong> GB, el espacio será <strong>ilimitado</strong>.
                                            El mínimo permitido es <strong>1 GB</strong> y el máximo <strong>1024
                                                GB</strong>.
                                        </small>
                                    </div>
                                </div>


                                <!-- Carpeta raíz -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="txtRootFolder">Nombre de la carpeta raíz
                                        </label>
                                        <div class="input-group">
                                            <input type="text" pattern="^[a-zA-Z0-9_-]{3,15}$" minlength="3"
                                                maxlength="15" class="form-control" id="txtRootFolder"
                                                name="txtRootFolder" placeholder="Ingrese nombre de carpeta"
                                                title="La carpeta debe tener entre 3 y 15 caracteres y solo puede contener letras, números, guiones bajos (_) o guiones (-)."
                                                aria-describedby="iconRootFolder">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="iconRootFolder">
                                                    <i class="fa fa-folder" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Si no se asigna un nombre el sistema colocara un nombre aleatorio
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button class="btn btn-primary btn-block" type="submit"><i
                                    class="fa fa-fw fa-lg fa-save"></i>Registrar</button>
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
                <button type="button" class="btn btn-danger" data-token="<?= csrf(false) ?>" id="confirmDelete">
                    <i class="fa fa-check"></i> Eliminar
                </button>
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
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Contenedor principal con foto y datos -->
                <div class="d-flex justify-content-between  align-items-center">
                    <!-- Nombre -->
                    <div>
                        <h3 class="text-uppercase font-weight-bold text-primary" id="reportFullName">Yeison Danner
                            Carhuapoma Dett</h3>
                    </div>
                    <!-- Foto de Perfil -->
                    <div>
                        <img id="reportPhotoProfile" src="ruta_imagen.jpg" class="img-thumbnail" width="100"
                            height="100" alt="Foto de usuario">
                    </div>
                </div>
                <!-- Datos Personales -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Datos Personales</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>DNI</strong></td>
                            <td id="reportDNI">12345678</td>
                        </tr>
                        <tr>
                            <td><strong>Género</strong></td>
                            <td id="reportGender">Masculino</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Datos de la Cuenta -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Datos de la Cuenta</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Usuario</strong></td>
                            <td id="reportUser">ydcarhuapoma</td>
                        </tr>
                        <tr>
                            <td><strong>Contraseña</strong></td>
                            <td id="reportPassword">ydcarhuapoma</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td id="reportEmail">yeison@example.com</td>
                        </tr>
                        <tr>
                            <td><strong>Rol</strong></td>
                            <td id="reportRole">Administrador</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Datos de almacenamiento de la cuenta -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Datos de Almacenamiento</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Carpeta del usuario</strong></td>
                            <td id="reportStorageFolder"><span class="badge badge-primary">./root</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Espacio utilizado</strong></td>
                            <td id="reportStorageUsed">
                                <small class="text-muted">1.5 GB de 2 GB</small>
                                <div class="progress">
                                    <div class="progress-bar w-75" role="progressbar" aria-valuenow="75"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--Estado de recuperacion -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Estado de recuperación</h6>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Intentos de Inicio de Sesión</strong></td>
                                <td id="reportAttemp">00</td>
                            </tr>
                            <tr>
                                <td><strong>Token de recuperacion de contraseña</strong></td>
                                <td id="reportToken">_token_</td>
                            </tr>
                            <tr>
                                <td><strong>Link de recuperación</strong></td>
                                <td id="reportUrl" style="cursor: pointer;" class="text-primary" title="Copiar enlace">
                                    https::/url.com</td>
                            </tr>
                            <tr>
                                <td><strong>Estado Link</strong></td>
                                <td id="reportStatusLink">Activo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
<!-- Modal Update-->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Registro de Usuario -->
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <input type="hidden" name="update_txtId" id="update_txtId">
                        <h5>Datos Personales</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtFullName">Nombres Completos <span
                                            class="text-danger">*</span> </label>

                                    <div class="input-group">
                                        <input class="form-control" type="text" id="update_txtFullName"
                                            name="update_txtFullName" required
                                            placeholder="Ingrese sus nombres completos" maxlength="200" minlength="20"
                                            pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"
                                            oninput="this.value = this.value.toUpperCase()"
                                            aria-describedby="iconFullnameUserUpdate">
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconFullnameUserUpdate"><i
                                                    class="fa fa-user" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtDNI">DNI <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" maxlength="8" minlength="8"
                                            pattern="^\d{8}$" id="update_txtDNI" name="update_txtDNI" required
                                            placeholder="Ingrese su número de DNI (Documento Nacional de Identidad)"
                                            aria-describedby="iconDNIUserUpdate">
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconDNIUserUpdate"><i
                                                    class="fa fa-id-card" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Género <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <label class="form-check-label" for="update_Masculino">
                                    <input class="form-check-input" value="Masculino" type="radio"
                                        name="update_txtGender" id="update_Masculino">Masculino
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label" for="update_Femenino">
                                    <input class="form-check-input" value="Femenino" type="radio"
                                        name="update_txtGender" id="update_Femenino">Femenino
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label" for="update_Otro">
                                    <input class="form-check-input" value="Otro" type="radio" name="update_txtGender"
                                        id="update_Otro">Otro
                                </label>
                            </div>
                        </div>
                        <h5>Datos de la Cuenta</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtUser">Usuario <span
                                            class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <input type="text" pattern="^[a-zA-Z0-9_-]{3,15}$" class="form-control"
                                            id="update_txtUser" name="update_txtUser" placeholder="Ingrese su usuario"
                                            title="El usuario debe tener entre 3 y 15 caracteres y solo puede contener letras, números, guiones bajos (_) o guiones (-)."
                                            required aria-describedby="iconUserUserUpdate">
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconUserUserUpdate"><i
                                                    class="fa fa-id-badge" aria-hidden="true"></i></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtEmail">Email <span
                                            class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                            class="form-control" id="update_txtEmail" name="update_txtEmail"
                                            placeholder="Ingrese su correo que desea vincular a la cuenta"
                                            title="Por favor, ingrese un correo electrónico válido (ejemplo: usuario@dominio.com)."
                                            required oninput="this.value = this.value.toLowerCase();"
                                            aria-describedby="iconMailUserUpdate">
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconMailUserUpdate"><i
                                                    class="fa fa-envelope" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="update_slctRole">Rol <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-control" id="update_slctRole" name="update_slctRole"
                                            aria-describedby="iconRoleUserUpdate" required>
                                            <option value="" selected disabled>Seleccione un elemento</option>
                                        </select>
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconRoleUserUpdate"><i
                                                    class="fa fa-users" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="update_slctStatus">Estado <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-control" id="update_slctStatus" name="update_slctStatus"
                                            aria-describedby="iconStatusUserUpdate" required>
                                            <option value="" selected disabled>Seleccione un elemento</option>
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                        </select>
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconStatusUserUpdate"><i
                                                    class="fa fa-check-square" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="update_txtPassword">Contraseña <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="update_txtPassword"
                                            name="update_txtPassword" required
                                            placeholder="Ingrese su correo que desea vincular a la cuenta" minlength="8"
                                            aria-describedby="iconPasswordUserUpdate" required>
                                        <div class=" input-group-prepend">
                                            <span class="input-group-text" id="iconPasswordUserUpdate"><i
                                                    class="fa fa-lock" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="update_flPhoto">Foto de Perfil (JPG,JPEG,PNG) <span
                                    class="text-danger">2 MB</span></label>

                            <div class="input-group">
                                <input class="form-control" type="file" id="update_flPhoto" name="update_flPhoto"
                                    accept="image/jpg, image/png, image/jpeg" aria-describedby="iconProfileUserUpdate">
                                <div class=" input-group-prepend">
                                    <span class="input-group-text" id="iconProfileUserUpdate"><i class="fa fa-upload"
                                            aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex" style="justify-content: center;">
                            <img class="upload-img-profile" id="imgUpdateProfile" alt="Previsualización"
                                src="<?= base_url() ?>/loadfile/profile/?f=user.png">
                        </div>
                        <input type="hidden" name="update_txtFotoActual" id="update_txtFotoActual">
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