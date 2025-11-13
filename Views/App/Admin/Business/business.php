<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-briefcase"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-briefcase fa-lg"></i></li>
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
                        <table class="table table-hover table-bordered table-sm w-100" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tipo de Negocio</th>
                                    <th>Nombre</th>
                                    <th>N° Documento</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Ciudad</th>
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
                <h5 class="modal-title" id="modalSaveLabel">Registro de Negocio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formSave" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Datos del Negocio</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctTypeBusiness">Tipo de Negocio <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="slctTypeBusiness" name="slctTypeBusiness" required aria-describedby="iconTypeBusiness">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconTypeBusiness"><i class="fa fa-building" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtName">Nombre <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="txtName" name="txtName" required
                                                    placeholder="Ingrese el nombre del negocio" maxlength="255"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconName">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconName"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="txtDirection">Dirección</label>
                                            <div class="input-group">
                                                <input class="form-control" id="txtDirection" name="txtDirection"
                                                    placeholder="Ingrese la dirección (opcional)"
                                                    aria-describedby="iconDirection">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconDirection"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="txtCity">Ciudad</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="txtCity" name="txtCity"
                                                    placeholder="Ingrese la ciudad" maxlength="250"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconCity">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCity"><i class="fa fa-map" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="txtCountry">País</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="txtCountry" name="txtCountry"
                                                    placeholder="Ingrese el país" maxlength="100"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconCountry">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCountry"><i class="fa fa-globe" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="txtDocumentNumber">N° Documento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="txtDocumentNumber" name="txtDocumentNumber" required
                                                    placeholder="12345678901" maxlength="11" pattern="^\d{11}$"
                                                    title="Debe contener exactamente 11 números"
                                                    aria-describedby="iconDocument">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconDocument"><i class="fa fa-id-card" aria-hidden="true"></i></span>
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
                                                <div class="input-group-append">
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
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconPhone"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="txtEmail">Email <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                                    class="form-control" id="txtEmail" name="txtEmail" required
                                                    placeholder="Ingrese el correo electrónico"
                                                    oninput="this.value = this.value.toLowerCase();"
                                                    aria-describedby="iconEmail">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconEmail"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="slctUserApp">Usuario de Aplicación <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="slctUserApp" name="slctUserApp" required aria-describedby="iconUserApp">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUserApp"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
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
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Negocio</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-uppercase font-weight-bold text-primary" id="reportName"></h3>
                    </div>
                </div>
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Negocio</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Tipo de Negocio</strong></td>
                            <td id="reportTypeBusiness"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td id="reportNameDetail"></td>
                        </tr>
                        <tr>
                            <td><strong>N° Documento</strong></td>
                            <td id="reportDocumentNumber"></td>
                        </tr>
                        <tr>
                            <td><strong>Dirección</strong></td>
                            <td id="reportDirection">-</td>
                        </tr>
                        <tr>
                            <td><strong>Ciudad</strong></td>
                            <td id="reportCity">-</td>
                        </tr>
                        <tr>
                            <td><strong>País</strong></td>
                            <td id="reportCountry">-</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono</strong></td>
                            <td id="reportPhone"></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td id="reportEmail"></td>
                        </tr>
                        <tr>
                            <td><strong>Usuario de Aplicación</strong></td>
                            <td id="reportUserApp"></td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td id="reportStatus"></td>
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
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar información del Negocio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile-body">
                    <form id="formUpdate" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="update_txtId" id="update_txtId">
                                <h5>Datos del Negocio</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctTypeBusiness">Tipo de Negocio <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctTypeBusiness" name="update_slctTypeBusiness" required aria-describedby="iconTypeBusinessUpdate">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconTypeBusinessUpdate"><i class="fa fa-building" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtName">Nombre <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="update_txtName" name="update_txtName" required
                                                    placeholder="Ingrese el nombre del negocio" maxlength="255"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconNameUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconNameUpdate"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtDirection">Dirección</label>
                                            <div class="input-group">
                                                <textarea class="form-control" id="update_txtDirection" name="update_txtDirection" rows="2"
                                                    placeholder="Ingrese la dirección (opcional)"
                                                    aria-describedby="iconDirectionUpdate"></textarea>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconDirectionUpdate"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtCity">Ciudad</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="update_txtCity" name="update_txtCity"
                                                    placeholder="Ingrese la ciudad" maxlength="250"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconCityUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCityUpdate"><i class="fa fa-map" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtCountry">País</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="update_txtCountry" name="update_txtCountry"
                                                    placeholder="Ingrese el país" maxlength="100"
                                                    oninput="this.value = this.value.toUpperCase()"
                                                    aria-describedby="iconCountryUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconCountryUpdate"><i class="fa fa-globe" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Este campo es opcional.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtDocumentNumber">N° Documento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="update_txtDocumentNumber" name="update_txtDocumentNumber" required
                                                    placeholder="12345678901" maxlength="11" pattern="^\d{11}$"
                                                    title="Debe contener exactamente 11 números"
                                                    aria-describedby="iconDocumentUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconDocumentUpdate"><i class="fa fa-id-card" aria-hidden="true"></i></span>
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
                                                <div class="input-group-append">
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
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconPhoneUpdate"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_txtEmail">Email <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                                    class="form-control" id="update_txtEmail" name="update_txtEmail" required
                                                    placeholder="Ingrese el correo electrónico"
                                                    oninput="this.value = this.value.toLowerCase();"
                                                    aria-describedby="iconEmailUpdate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconEmailUpdate"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctUserApp">Usuario de Aplicación <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctUserApp" name="update_slctUserApp" required aria-describedby="iconUserAppUpdate">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconUserAppUpdate"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="update_slctStatus">Estado <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control" id="update_slctStatus" name="update_slctStatus" required aria-describedby="iconStatusUpdate">
                                                    <option value="" selected disabled>Seleccione un elemento</option>
                                                    <option value="Activo">Activo</option>
                                                    <option value="Inactivo">Inactivo</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="iconStatusUpdate"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                        </div>
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