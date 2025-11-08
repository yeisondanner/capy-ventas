<?= headerAdmin($data) ?>
<style>
    /* Estilos específicos del visor de archivos */
    .viewer-wrapper {
        height: 100%;
        min-height: 26rem;
    }

    .viewer-stage {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 24rem;
        padding: 2rem;
        border-radius: 1.5rem;
        background: linear-gradient(145deg, rgba(15, 23, 42, 0.9), rgba(30, 58, 138, 0.85));
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.1), 0 25px 45px -30px rgba(15, 23, 42, 0.85);
        overflow: hidden;
    }

    .viewer-stage::before,
    .viewer-stage::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        filter: blur(0.85rem);
        opacity: 0.25;
        transition: opacity 0.3s ease;
    }

    .viewer-stage::before {
        width: 20rem;
        height: 20rem;
        top: -7rem;
        left: -8rem;
        background: radial-gradient(circle at center, rgba(96, 165, 250, 0.9), transparent 70%);
    }

    .viewer-stage::after {
        width: 18rem;
        height: 18rem;
        bottom: -10rem;
        right: -8rem;
        background: radial-gradient(circle at center, rgba(37, 99, 235, 0.75), transparent 75%);
    }

    .viewer-stage--active::before,
    .viewer-stage--active::after {
        opacity: 0.05;
    }

    .viewer-stage-inner {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.25rem;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(6px);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.25);
        overflow: hidden;
    }

    .viewer-stage--active .viewer-stage-inner {
        background: rgba(15, 23, 42, 0.2);
        box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.35);
    }

    .viewer-message {
        max-width: 28rem;
        text-align: center;
        color: #f8fafc;
    }

    .viewer-message .alert {
        border-radius: 1rem;
        border: none;
        background: rgba(15, 23, 42, 0.75);
        color: #e2e8f0;
        box-shadow: 0 20px 35px -25px rgba(15, 23, 42, 0.9);
    }

    .viewer-message .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .viewer-preview {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .viewer-preview-image {
        object-fit: contain;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.5);
    }

    .viewer-preview-document {
        background: rgba(15, 23, 42, 0.7);
    }

    .viewer-preview-text {
        margin: 0;
        padding: 2rem;
        font-family: "Fira Code", "Courier New", monospace;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #e2e8f0;
        background: rgba(15, 23, 42, 0.75);
        white-space: pre-wrap;
        word-break: break-word;
        overflow-y: auto;
        width: 100%;
        height: 100%;
    }

    .viewer-details {
        background: rgba(255, 255, 255, 0.9);
        border-left: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 0 1.5rem 1.5rem 0;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.1);
    }

    .viewer-details .badge {
        padding: 0.5rem 0.85rem;
        border-radius: 999px;
        letter-spacing: 0.03em;
    }

    .viewer-details .list-group {
        background: transparent;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 12px 30px -25px rgba(15, 23, 42, 0.25);
    }

    .viewer-details .list-group-item {
        border: none;
        padding: 1rem 1.25rem;
        background: rgba(248, 250, 252, 0.95);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .viewer-details .list-group-item+.list-group-item {
        border-top: 1px solid rgba(226, 232, 240, 0.75);
    }

    .viewer-details small {
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(100, 116, 139, 0.85);
        font-weight: 600;
    }

    .viewer-details span {
        color: #1f2937;
    }

    .viewer-details .alert {
        border-radius: 1rem;
        border: none;
        background: rgba(59, 130, 246, 0.1);
        color: #1e40af;
    }

    .viewer-details .alert i {
        color: #2563eb;
    }

    .modal-footer .btn {
        border-radius: 999px;
        padding: 0.55rem 1.4rem;
    }

    #btnDownloadFile {
        box-shadow: 0 18px 35px -25px rgba(37, 99, 235, 0.9);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    #btnDownloadFile:not(.disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 40px -20px rgba(37, 99, 235, 0.9);
    }

    @media (max-width: 991.98px) {
        .viewer-wrapper {
            min-height: 22rem;
        }

        .viewer-stage {
            min-height: 20rem;
            margin-bottom: 1.75rem;
        }

        .viewer-details {
            border-left: none;
            border-radius: 1.5rem;
            padding-top: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .viewer-preview-text {
            padding: 1.5rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 575.98px) {
        .viewer-stage {
            padding: 1.25rem;
        }

        .viewer-preview-text {
            padding: 1.25rem;
            font-size: 0.85rem;
        }
    }
</style>

<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-dashboard"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $data["page_title"] ?></li>
        </ul>
    </div>
    <div class="row bg-white border rounded shadow-md mx-1">
        <!--Elemento sidebar y almacenamiento-->
        <div class="col-md-4 col-lg-3 col-xl-2 border border-right-1 shadow-sm p-0">
            <!-- Sidebar -->
            <div class="sidebar w-100" id="sidebarClust">
                <div class="d-flex w-100 justify-content-between align-items-center gap-1 mb-2">
                    <!-- Dropdown Nuevo -->
                    <div class="dropdown w-100">
                        <button class="btn btn-new text-left text-primary dropdown-toggle rounded-pill px-3"
                            type="button" id="dropdownNuevo" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-plus"></i> Nuevo
                        </button>
                        <div class="dropdown-menu w-100" aria-labelledby="dropdownNuevo">
                            <a class="dropdown-item text-warning" href="#" data-toggle="modal"
                                data-target="#modalCarpeta">
                                <i class="fa fa-folder"></i> Carpeta
                            </a>
                            <!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalArchivo">
                                <i class="fa fa-file"></i> Archivo
                            </a>-->
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-success" href="#" data-toggle="modal"
                                data-target="#modalSubir">
                                <i class="fa fa-upload"></i> Subir archivo
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light d-md-none rounded-pill btn-outline-danger" id="menu-close"
                        aria-label="Cerrar menú">
                        <i class="fa fa-times text-danger"></i>
                    </button>
                </div>
                <!-- Menú -->
                <ul class="nav flex-column mb-1">
                    <li class="nav-item">
                        <a href="#" class="nav-link active"><i class="fa fa-hdd-o"></i> Mi unidad</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fa fa-image"></i> Imagenes</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"> <i class="fa fa-file-pdf-o"></i> PDF</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"> <i class="fa fa-file-word-o"></i> Word</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"> <i class="fa fa-file-excel-o"></i> Excel</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fa fa-clock-o"></i> Recientes</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="fa fa-star"></i> Destacados</a>
                    </li>-->
                </ul>
                <hr class="p-0 m-0 mb-2">
                <!-- Almacenamiento -->
                <?= $data['page_components']['storage'] ?>
            </div>
        </div>
        <div class="col-md-8 col-lg-9 col-xl-10 col-12 p-2">
            <div>
                <!-- Barra de búsqueda -->
                <form action="" class="w-100 mb-4 /*d-flex*/ justify-content-between align-items-center d-none">
                    <!-- Botón Toggle (solo móviles) -->
                    <button class="btn btn-light d-md-none rounded-pill btn-outline-primary toggle-btn mr-1"
                        id="menu-toggle" type="button" aria-label="Abrir menú">
                        <i class="fa fa-bars text-primary"></i>
                    </button>
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill" placeholder="Buscar carpetas o archivos..."
                            aria-label="Buscar">
                        <div class="input-group-append">
                            <button class="btn btn-primary rounded-pill ml-1" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Encabezado con título y botones de vista -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h2 class="text-primary mb-2 mb-md-0">Mi unidad</h2>
                    <div class="btn-group w-50">
                        <form
                            class="border-primary border rounded d-none justify-content-between align-items-center w-100"
                            id="formSelecction">
                            <?= csrf(); ?>
                            <button type="button" class="btn btn-light" id="btn_close_form_selecction">
                                <i class="fa fa-times text-muted"></i>
                            </button>
                            <input type="text" class="form-control mr-1" id="update_txtName" name="update_txtName"
                                pattern="[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ_-]{1,255}"
                                title="Solo letras (con acentos y ñ), números, espacios, guion medio y guion bajo. Longitud: 1 a 255 caracteres."
                                required autofocus>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success" id="btnUpdateFiles" data-toggle="tooltip"
                                    data-placement="top" title="Cambiar nombre de la carpeta">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                    id="btnDeleteFiles" title="Eliminar la carpeta" data-token="<?= csrf(false); ?>">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ul class="app-breadcrumb breadcrumb" id="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Mi unidad</li>
                    </ul>
                </nav>
            </div>
            <!-- Archivos y Carpetas -->
            <div id="container_files" style="height: 50vh; overflow-y: auto; overflow-x: hidden;">
                <div class="col-12">
                    <div class="alert alert-info" role="alert">No hay carpetas ni archivos disponibles</div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= footerAdmin($data) ?>

<!-- Modal Carpeta -->
<div class="modal fade" id="modalCarpeta" tabindex="-1" role="dialog" aria-labelledby="modalCarpetaLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formSave" class="modal-content">
            <?= csrf(); ?>
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalCarpetaLabel">Nueva Carpeta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="txtIdFather" id="txtIdFather" value="">

                <div class="form-group">
                    <label class="control-label" for="txtName">Nombre de la carpeta <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Nombre de carpeta" id="txtName"
                            name="txtName" pattern="[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ_-]{1,255}" minlength="1" maxlength="255"
                            title="Solo letras (con acentos y ñ), números, espacios, guion medio y guion bajo. Longitud: 1 a 255 caracteres."
                            required autofocus="true" autocomplete="off">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="iconName"><i class="fa fa-folder"
                                    aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <small class="text-muted"><span class="text-danger">*</span>Solo letras (con acentos y ñ), números,
                        espacios, guion medio y guion
                        bajo. Longitud: 1 a 255
                        caracteres.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning text-white"><i class="fa fa-folder"></i> Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Archivo -->
<div class="modal fade" id="modalArchivo" tabindex="-1" role="dialog" aria-labelledby="modalArchivoLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalArchivoLabel">Nuevo Archivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" placeholder="Nombre del archivo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Crear</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir -->
<div class="modal fade" id="modalSubir" tabindex="-1" role="dialog" aria-labelledby="modalSubirLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="formUpload" enctype="multipart/form-data">
            <?= csrf(); ?>
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSubirLabel">Subir Archivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label" for="inputFiles">Subir Archivo<span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="file" class="form-control" id="inputFiles" name="inputFiles" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="iconFile"><i class="fa fa-upload"
                                    aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="inputName">Nombre del archivo<span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" pattern="^[a-zA-Z0-9_-]{1,255}$"
                            title="Solo letras, números, guion y guion bajo. Máx. 255 caracteres" minlength="1"
                            maxlength="255" id="inputName" name="inputName" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputName"><i class="fa fa-text-width"
                                    aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Subir</button>
            </div>
        </form>
    </div>
</div>
<!--Modal de apertura de archivos-->
<div class="modal fade" id="modalViewFile" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0 align-items-center">
                <div class="d-flex align-items-center">
                    <i id="modalFileIcon" class="fa fa-file-o fa-2x mr-3 text-light" aria-hidden="true"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="modalFileName">Visor de archivos</h5>
                        <small class="text-white-50" id="modalFileDetails">Selecciona un archivo para iniciar la vista
                            previa.</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="viewer-wrapper">
                    <div class="row no-gutters h-100">
                        <div class="col-lg-8 p-3">
                            <div class="viewer-stage" id="fileViewerStage">
                                <div class="viewer-stage-inner" id="fileViewerContainer">
                                    <div id="fileViewerMessage" class="viewer-message">
                                        <div class="alert alert-info" role="alert">
                                            Selecciona un archivo para visualizarlo.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 viewer-details p-3">
                            <div class="d-flex flex-column h-100">
                                <div>
                                    <span id="modalFileBadge" class="badge badge-primary badge-pill">SIN ARCHIVO</span>
                                    <p class="mt-3 mb-3 text-muted" id="modalFileInfo">Selecciona un archivo para
                                        iniciar la vista previa.</p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-uppercase">Ubicación</small>
                                            <span class="font-weight-bold text-right" id="viewerMetaLocation">-</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-uppercase">Tamaño</small>
                                            <span class="font-weight-bold text-right" id="viewerMetaSize">-</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-uppercase">Registrado</small>
                                            <span class="font-weight-bold text-right" id="viewerMetaRegistered">-</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-uppercase">Actualizado</small>
                                            <span class="font-weight-bold text-right" id="viewerMetaUpdated">-</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-uppercase">Vista previa</small>
                                            <span class="font-weight-bold text-right" id="modalFileType">La vista previa
                                                se mostrará aquí cuando esté disponible.</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="alert alert-secondary mt-3 mb-0" role="alert">
                                    <i class="fa fa-info-circle mr-2" aria-hidden="true"></i>
                                    Si no se muestra la vista previa, utiliza el botón <strong>Descargar</strong> para
                                    abrir el archivo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times mr-1" aria-hidden="true"></i>Cerrar
                </button>
                <a id="btnDownloadFile" href="#" target="_blank" rel="noopener"
                    class="btn btn-primary btn-sm px-3 disabled" aria-disabled="true">
                    <i class="fa fa-download mr-1" aria-hidden="true"></i>Descargar
                </a>
            </div>
        </div>
    </div>
</div>