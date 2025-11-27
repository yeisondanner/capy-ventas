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
                <div class="mb-4 d-inline-flex p-3 rounded-circle bg-secondary bg-opacity-10 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                        <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z" />
                        <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z" />
                    </svg>
                </div>
                <h2 class="h3 fw-bold text-dark mb-3">
                    Acceso restringido
                </h2>

                <p class="text-muted lead fs-6 mb-4">
                    Esta sección se encuentra actualmente inhabilitada por la administración del negocio.
                    Si necesitas ingresar, por favor solicita la reactivación al propietario de la cuenta.
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