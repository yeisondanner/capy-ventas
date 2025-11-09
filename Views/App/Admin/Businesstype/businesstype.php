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
                <h5 class="modal-title" id="modalSaveLabel">Registro de Negocios</h5>
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
                            <label class="control-label" for="txtBusinesstypeName">Nombre
                                <span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="text" id="txtBusinesstypeName" name="txtBusinesstypeName" required
                                placeholder="Ingrese el nombre del tipo de negocio" maxlength="250" minlength="4"
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,250}$"
                                title="El nombre debe contener entre 4 y 250 caracteres y solo incluir letras y espacios.">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtBusinesstypeName">Descripción
                            </label>
                            <textarea class="form-control" id="txtBusinesstypeName" name="txtBusinesstypeName"
                                minlength="20" pattern="^[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+$"
                                placeholder="Ingrese la descripción del tipo de negocio"></textarea>
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
