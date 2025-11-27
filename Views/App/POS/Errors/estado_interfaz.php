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
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-slash-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.646-2.646a.5.5 0 0 0-.708-.708l-6 6a.5.5 0 0 0 .708.708l6-6z" />
                    </svg>
                </div>

                <h2 class="h3 fw-bold text-dark mb-3">
                    Esta función no está disponible por ahora
                </h2>

                <p class="text-muted lead fs-6 mb-4">
                    Lo sentimos, esta sección se encuentra temporalmente inactiva.
                    Estamos trabajando para restablecerla lo antes posible.
                    <br>¡Gracias por tu paciencia!
                </p>

                <div class="d-grid gap-2">
                    <div class="d-inline-flex gap-2">
                        <a href="<?= base_url() ?>/pos/LogOut" class="btn btn-danger btn-lg rounded-pill">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                        <a href="<?= base_url() ?>/pos/dashboard" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-box-arrow-left"></i> Volver al inicio
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