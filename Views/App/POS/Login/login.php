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
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/app/POS/login/style_login.css?<?= versionSystem() ?>">
    <title>Inicio de Sesion | Capy Ventas</title>
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
        <!--        <div class="logo">-->
        <!--            <h1>CapyVentas</h1>-->
        <!--        </div>-->

        <div class="login-box">
            <form class="login-form" id="formLogin" autocomplete="off">
                <div class="login-head">
                    <img src="<?= media() ?>/capysm.png" alt="" class="img-fluid rounded-circle" style="width: 50px; height: 50px;">
                    <h3>
                        <!--                    <i class="bi bi-person me-2"></i>-->

                        Capy Ventas
                    </h3>
                </div>
                <div class="login-message">
                    <span>
                        Para ingresar a tu app <strong>CapyVentas</strong>, ingresa tus capy credenciales.
                    </span>
                </div>
                <div class="mb-3">
                    <label for="txtUser" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input class="form-control" type="text" id="txtUser" name="txtUser"
                            placeholder="micapycorreo@example.com" autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="txtPassword" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="txtPassword" id="txtPassword"
                            placeholder="Ingrese su contraseña" required autocomplete="off">
                        <button class="btn btn-outline-primary" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill" id="iconoPassword"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 password text-center text-sm-end">
                    <p class="mb-0">
                        <span class="text-primary align-items-center"><i class="bi bi-x-diamond-fill"></i> <a class="semibold-text" href="./resetpassword">¿Olvidaste tu contraseña?</a></span>
                    </p>
                </div>

                <div class="mb-3 btn-container d-grid">
                    <button class="btn btn-primary btn-block d-flex align-items-center justify-content-center" id="btnLogin"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i>Ingresar</button>
                </div>

                <div class="contacto">
                    <p class="mb-0 d-flex flex-column-reverse align-items-center flex-sm-row justify-content-sm-between">
                        <span>¿No tienes cuenta en <strong>Capy Ventas</strong>?</span>
                        <span class="text-primary"><i class="bi bi-person-fill-add"></i> <a class="semibold-text" href="./account">Crear Cuenta</a></span>
                    </p>
                </div>
                <div class="d-flex justify-content-center py-2">
                    <span class="text-center badge bg-primary"><?= versionSystem() ?></span>
                </div>
            </form>

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