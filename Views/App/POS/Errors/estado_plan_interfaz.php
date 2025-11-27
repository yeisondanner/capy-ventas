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

                <div class="mb-4 d-inline-flex p-3 rounded-circle bg-info bg-opacity-10 text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-gem" viewBox="0 0 16 16">
                        <path d="M3.1.7a.5.5 0 0 1 .4-.2h9a.5.5 0 0 1 .4.2l2.976 3.974c.149.185.156.45.01.644L8.4 15.3a.5.5 0 0 1-.8 0L.1 5.3a.5.5 0 0 1 .01-.644L3.1.7z" />
                    </svg>
                </div>

                <h2 class="h3 fw-bold text-dark mb-3">
                    Lleva tu gestión al siguiente nivel
                </h2>

                <p class="text-muted lead fs-6 mb-4">
                    Esta herramienta avanzada está disponible exclusivamente en nuestros planes superiores.
                    No te limites: contacta a soporte hoy mismo para actualizar tu cuenta y desbloquear todo el potencial.
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