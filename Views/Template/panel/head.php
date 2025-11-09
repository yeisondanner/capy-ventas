<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $data["page_title"] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--Colocar las descripciones de la pagina-->
    <meta name="description" content="<?= getSystemInfo()["c_description"] ?>">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/main.css?<?= versionSystem() ?>">
    <!-- Font-icon css
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/Admin/font-awesome-4.7.0/css/font-awesome.min.css?<?= versionSystem() ?>">

    <!-- CSS de la las alertas -->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <!-- Data table plugin-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/dataTables.dataTables.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/buttons.dataTables.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/loader.css?<?= versionSystem() ?>">

    <!--Cargamos el inco de la pagina-->
    <link rel="shortcut icon"
        href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>"
        type="image/x-icon">
    <!-- CSS de la vista -->
    <?php
    $pageAssets = $data['page_assets'] ?? [];

    // Documentación (español):
    // Validamos y normalizamos el valor de 'page_container' para evitar pasar un array a strtolower().
    // - Si es string: lo usamos directamente.
    // - Si es array: intentamos obtener la clave 'name' o el primer elemento string disponible.
    // - Si no se puede obtener un string, usamos un valor por defecto 'default'.
    $pageContainerRaw = $data['page_container'] ?? '';
    if (is_string($pageContainerRaw)) {
        $pageCssFolder = strtolower($pageContainerRaw);
    } elseif (is_array($pageContainerRaw)) {
        $pageCssFolder = '';
        if (isset($pageContainerRaw['name']) && is_string($pageContainerRaw['name'])) {
            $pageCssFolder = strtolower($pageContainerRaw['name']);
        } else {
            $first = reset($pageContainerRaw);
            if (is_string($first)) {
                $pageCssFolder = strtolower($first);
            }
        }
    } else {
        $pageCssFolder = '';
    }

    // Fallback: si está vacío, usar carpeta por defecto 'default'
    if ($pageCssFolder === '') {
        $pageCssFolder = 'default';
    }

    $pageCssFile = "style_" . ($data['page_js_css'] ?? 'main') . ".css";
    $pageCssVersion = $pageAssets['css_version'] ?? null;
    $pageCssQuery = $pageCssVersion ? '?v=' . $pageCssVersion : '';
    ?>
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/app/Admin/<?= $pageCssFolder ?>/<?= $pageCssFile ?><?= $pageCssQuery ?>?<?= versionSystem() ?>">
    <style>
        :root {
            --color-primary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_primary"] : "#4da8da"; ?>;
            --color-secondary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_secondary"] : "#004e89"; ?>;
        }
    </style>
    <?php require_once "./Views/App/Admin/" . ucfirst($data["page_container"]) . "/Libraries/head.php"; ?>
    <script type="text/javascript">
        const getcurrency = "<?= getCurrency(); ?>";
    </script>
</head>

<body class="app sidebar-mini">
    <!-- Preloader -->
    <div id="loaderOverlay">
        <?= getSystemInfo()["c_contentLoader"] ?>
        <h5> <?= getSystemInfo()["c_textLoader"] ?></h5>
    </div>

    <!-- Modal de lista de notificaciones -->
    <div class="modal fade" id="seeMoreNotifications" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel"> <i class="fa fa-list mr-2"></i> Todas las
                        notificaciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tableNotifications" class="table table-bordered table-hover table-sm w-100">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center"><i class="fa fa-bell"></i></th>
                                    <th>Asunto </th>
                                    <th>Mensaje</th>
                                    <th class="text-center"><i class="fa fa-link"></i></th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th><i class="fa fa-check-square-o"></i></th>
                                    <th>Fecha</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de notificaciones -->
    <div class="modal fade" id="modalNotificacion" tabindex="1" aria-labelledby="modalNotificacionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg rounded-lg">
                <div id="modalNotificacionHeader">
                    <h5 class="modal-title" id="modalNotificacionLabel">
                        <i class="fa fa-bell mr-2"></i> Notificación Importante
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- Cabecera de Notificación -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" id="notificacionIcon">
                        </div>
                        <div id="notificacionHeader">

                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <p class="text-dark" style="white-space: pre-wrap; word-break: break-word;"
                            id="notificacionDescription">
                            Estimado usuario, le informamos que su cuenta ha sido activada exitosamente. Puede comenzar
                            a utilizar todas las funcionalidades disponibles en el sistema.
                        </p>
                    </div>

                    <!-- Información adicional con iconos -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p id="notificationPriority"></p>
                            <p id="notificationRead"></p>
                        </div>
                        <div class="col-md-6">
                            <p id="notificationState"></p>
                            <p id="notificationId">
                            </p>
                        </div>
                    </div>

                    <!-- Enlace externo si aplica -->
                    <div class="bg-light p-3 rounded border" id="notificationLink">
                    </div>
                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar-->
    <?php include "./Views/Template/panel/navbar.php"; ?>
    <!-- Sidebar menu-->
    <?php include "./Views/Template/panel/sidebarmenu.php"; ?>
    <!-- Panel lateral -->
    <div id="sidebar">
        <button id="closeSidebar" title="Cerrar">&times;</button>
        <div class="p-3 border-bottom">
            <h5 class="mb-0">Usuarios en línea</h5>
        </div>
        <ul class="list-group list-group-flush">
            <?php
            foreach (getAllUsersOnline() as $key => $value) {
            ?>
                <li class="list-group-item">
                    <span class="online-dot"></span><?= $value["u_fullname"] ?>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>