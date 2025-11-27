<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <link rel="shortcut icon" href="<?= media() ?>/head-capibara.png?<?= versionSystem() ?>" type="image/x-icon">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/app/POS/login/style_login.css?<?= versionSystem() ?>">
    <title><?= $data["page_title"] ?></title>
    <script>
        const base_url = "<?= base_url() ?>/pos";
        const currency = "<?= getCurrency() ?>";
    </script>
</head>

<body>
    <section class="material-half-bg">
        <div class="cover"></div>
    </section>
    <section class="login-content">
        <div class="card border-0 shadow-lg rounded-4 text-center" style="max-width: 450px; width: 100%;">
            <div class="card-body p-5">

                <div class="mb-4 d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-lock" viewBox="0 0 16 16">
                        <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 5.996V14H3s-1 0-1-1 1-4 6-4c.564 0 1.077.038 1.544.107a4.524 4.524 0 0 0-.803.918A10.46 10.46 0 0 0 8 12.996Z" />
                        <path d="M10 13a1 1 0 0 0 1-1v-1a2 2 0 0 0-4 0v1a1 1 0 0 0 1 1v2a1 1 0 0 0 2 0v-2Ym-1.5 2.5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1Z" />
                        <path d="M9 13v-1a1 1 0 0 1 2 0v1h.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5H9Z" />
                    </svg>
                </div>
                <h2 class="h3 fw-bold text-dark mb-3">
                    Permisos insuficientes del plan
                </h2>
                <p class="text-muted lead fs-6 mb-4">
                    El plan actual no cuenta con los permisos necesarios para operar en esta sección.
                </p>
                <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                    <?php
                    $sessionName = config_sesion(1)['name'] ?? '';
                    $nameVarMessagePermission = $sessionName . 'message_permission';
                    $crudArray = [
                        'create' => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">
                        <i class="bi bi-x-circle me-1"></i> Crear: NO
                    </span>',
                        'read' => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">
                        <i class="bi bi-x-circle me-1"></i> Leer: NO
                    </span>',
                        'update' => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">
                        <i class="bi bi-x-circle me-1"></i> Editar: NO
                    </span>',
                        'delete' => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">
                        <i class="bi bi-x-circle me-1"></i> Borrar: NO
                    </span>'
                    ];
                    echo $crudArray[$_SESSION[$nameVarMessagePermission]];
                    ?>
                </div>
                <div class="d-grid gap-2">
                    <div class="d-inline-flex justify-content-between gap-2">
                        <a href="<?= base_url() ?>/pos/dashboard" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-box-arrow-left"></i> Volver al inicio
                        </a>
                        <a href="<?= base_url() ?>/pos/LogOut" class="btn btn-danger btn-lg rounded-pill">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </div>
                    <a href="#" class="btn btn-link text-decoration-none text-muted btn-sm mt-2">
                        Contactar soporte
                    </a>
                </div>

            </div>
        </div>
    </section>
    <!-- TODO: Essential javascripts for application to work-->
    <script src="<?= media() ?>/js/libraries/POS/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/popper.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/SweerAlert2.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js?<?= versionSystem() ?>"></script>
</body>

</html>