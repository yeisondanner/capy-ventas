<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <link rel="shortcut icon" href="<?= media() ?>/capysm.png?<?= versionSystem() ?>" type="image/x-icon">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/app/POS/account/style_account.css?<?= versionSystem() ?>">
    <title>Recuperar contraseña | Capy Ventas</title>
    <script>
        const base_url = "<?= base_url() ?>/pos";
        const media_url = "<?= media() ?>";
        const currency = "<?= getCurrency() ?>";
    </script>
</head>

<body>
    <section class="material-half-bg">
        <div class="cover"></div>
    </section>

    <section class="d-flex justify-content-center align-items-center min-vh-100 p-3 pt-4 pt-md-3">
        <div id="cardAccount" class="card shadow-lg border-0 overflow-hidden w-100 rounded-3" style="max-width: 1000px;">

        </div>
    </section>

    <a target="_blank" href="https://wa.me/51910367611?text=Hola,%20tengo%20problemas%20con%20la%20recuperación%20de%20contraseña%20en%20Capy%20Ventas" class="btn-whatsapp-float">
        <i class="bi bi-whatsapp me-2"></i> ¿Tienes problemas con la recuperación?
    </a>
    <!-- TODO: Essential javascripts for application to work-->
    <script src="<?= media() ?>/js/libraries/POS/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/popper.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/SweerAlert2.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/resetpassword/functions_resetpassword_api.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/resetpassword/functions_resetpassword.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js?<?= versionSystem() ?>"></script>
</body>

</html>