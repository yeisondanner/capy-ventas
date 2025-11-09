<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/main.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/loader.css?<?= versionSystem() ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/Admin/font-awesome-4.7.0/css/font-awesome.min.css?<?= versionSystem() ?>">
    <!-- CSS de la las alertas -->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <link rel="shortcut icon"
        href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>"
        type="image/x-icon">
    <link rel="stylesheet"
        href="<?= media() ?>/css/app/<?= strtolower($data["page_container"]) ?>/style_<?= $data["page_js_css"] ?>.css?<?= versionSystem() ?>">
    <style>
        :root {
            --color-primary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_primary"] : "#4da8da"; ?>;
            --color-secondary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_secondary"] : "#004e89"; ?>;
        }
    </style>
    <title><?= $data["page_title"] ?></title>
</head>

<body>
    <div id="loaderOverlay">
        <?= getSystemInfo()["c_contentLoader"] ?>
        <h5> <?= getSystemInfo()["c_textLoader"] ?></h5>
    </div>

    <section class="material-half-bg">
        <div class="cover"></div>
    </section>
    <section class="login-content">
        <div class="logo">
            <h1><?= (getSystemInfo()) ? getSystemInfo()["c_name"] : getSystemName(); ?></h1>
        </div>
        <div class="login-box">
            <!-- Formulario de login -->
            <form class="p-5 container" id="formPassword" autocomplete="off">
                <h4 class="text-center"><i class="fa fa-lg fa-fw fa-user"></i>Restablecer la contraseña</h4>
                <div class="text-center">
                    <img class="rounded-circle user-image"
                        src="<?= (empty($data['page_info_user']) ? generateAvatar($data['page_info_user']['u_fullname']) : base_url() . "/loadfile/profile/?f=" . $data['page_info_user']['u_profile']) ?>"
                        alt="<?= $data['page_info_user']['u_fullname'] ?>">
                    <p class="text-center user-name"><?= $data['page_info_user']['u_fullname'] ?></p>
                    <p class="text-danger">Dispone de
                        <?= (30 - $data['page_info_user']['tiempo_restante']['total_minutos']) ?> minutos antes de que
                        el enlace sea bloqueado de manera automática.
                    </p>
                </div>
                <input type="hidden" name="txtToken" id="txtToken"
                    value="<?= $data['page_info_user']['u_reset_token_password'] ?>">
                <div class="form-group">
                    <label class="control-label" for="txtPassword">Nueva contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="txtPassword" id="txtPassword"
                            placeholder="Ingrese su contraseña" required minlength="8">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="button" id="togglePassword">
                                <i class="fa fa-eye" id="iconoPassword"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group btn-container">
                    <button class="btn btn-primary btn-block"><i class="fa fa-refresh fa-lg fa-fw"></i>Cambiar</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Essential javascripts for application to work-->
    <script src="<?= media() ?>/js/libraries/Admin/jquery-3.7.1.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/Admin/popper.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/Admin/bootstrap.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/Admin/main.js?<?= versionSystem() ?>"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?= media() ?>/js/libraries/Admin/plugins/pace.min.js?<?= versionSystem() ?>"></script>
    <!--Libreria de sweetalert-->
    <script type="text/javascript" src="<?= media() ?>/js/libraries/Admin/toastr.min.js?<?= versionSystem() ?>"></script>
    <script type="text/javascript">
        const base_url = "<?= base_url(); ?>";
    </script>
    <script
        src="<?= media() ?>/js/app/Admin/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.?<?= versionSystem() ?>"></script>
</body>

</html>