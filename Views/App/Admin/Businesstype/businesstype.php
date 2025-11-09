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

