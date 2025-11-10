<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <title>Capy Login</title>
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
        <div class="logo">
            <h1>CapyVentas</h1>
        </div>

        <div class="login-box">
            <form class="login-form" id="formLogin" autocomplete="off">
                <h3 class="login-head"><i class="bi bi-person me-2"></i>Iniciar Sesión</h3>

                <div class="mb-3">
                    <label for="txtUser" class="form-label">Usuario o Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input class="form-control" type="text" id="txtUser" name="txtUser"
                            placeholder="Ingrese su usuario o Email" autofocus>
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

                <div class="mb-3">
                    <div class="utility">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="chbxRemember" name="chbxRemember">
                                <label class="form-check-label" for="chbxRemember">Recuérdame</label>
                            </label>
                        </div>
                        <p class="semibold-text mb-0">
                            <a href="#" data-toggle="flip">¿Olvidaste tu contraseña?</a>
                        </p>
                    </div>
                </div>


                <div class="mb-3 btn-container d-grid">
                    <button class="btn btn-primary btn-block"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i>Ingresar</button>
                </div>
            </form>


            <!-- Formulario de recuperar contraseña -->
            <form class="forget-form" autocomplete="off" id="formReset">
                <h3 class="login-head"><i class="bi bi-person-lock me-2"></i>¿Olvidaste tu contraseña?</h3>

                <div class="mb-3">
                    <label for="txtEmail" class="form-label">EMAIL</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-at-fill"></i></span>
                        <input class="form-control" type="text" placeholder="Correo electrónico" id="txtEmail" name="txtEmail" autocomplete="off">
                    </div>
                </div>

                <div class="mb-3 btn-container d-grid">
                    <button class="btn btn-primary btn-block"><i class="bi bi-unlock me-2 fs-5"></i>Reiniciar</button>
                </div>

                <div class="mb-3 mt-3">
                    <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="bi bi-chevron-left me-1"></i> Ir al login</a></p>
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
    <script src="<?= media() ?>/js/app/POS/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js?<?= versionSystem() ?>"></script>
</body>

</html>