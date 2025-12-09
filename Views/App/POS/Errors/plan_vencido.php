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

                <div class="mb-4 d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-calendar-x-fill" viewBox="0 0 16 16">
                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM6.854 8.146 8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 1 1 .708-.708z" />
                    </svg>
                </div>

                <h2 class="h3 fw-bold text-dark mb-3">
                    Plan vencido
                </h2>

                <p class="text-muted mb-4">
                    El negocio no tiene un <span class="text-danger">plan activo</span> o esta en plan <span class="text-danger">free</span>, por favor contacte el dueño del negocio para que pueda renovar el plan.
                </p>

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