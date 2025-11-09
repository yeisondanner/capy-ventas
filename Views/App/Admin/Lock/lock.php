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
                <?= (getSystemInfo()) ?  getSystemInfo()["c_color_secondary"] : "#004e89"; ?>;
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
    <section class="lockscreen-content">
        <div class="logo">
            <h1><?= (getSystemInfo()) ? getSystemInfo()["c_name"] : getSystemName(); ?></h1>
        </div>
        <div class="lock-box">
            <img class="rounded-circle user-image"
                src="<?= ($_SESSION['login_info']['profile'] == "" ? generateAvatar($_SESSION['login_info']['fullName']) : base_url() . "/loadfile/profile/?f=" . $_SESSION['login_info']['profile']) ?>"
                alt="<?= $_SESSION['login_info']['fullName'] ?>">
            <h4 class=" text-center user-name"><?= $_SESSION['login_info']['fullName'] ?></h4>
            <p class="text-center text-muted">Bloqueado</p>
            <p class="text-center text-muted">La sesión se bloqueo porque se detecto
                <?= (getSystemInfo()["c_duration_lock"] / 60) ?> minutos de inactividad
            </p>
            <form class="unlock-form" id="formUnlock" name="formUnlock">
                <input type="hidden" id="txtUser" name="txtUser"
                    value="<?= decryption($_SESSION['login_info']['user']) ?>">
                <div class="form-group">
                    <label class="control-label" for="txtPassword">Contraseña</label>
                    <input class="form-control" type="password" id="txtPassword" name="txtPassword"
                        placeholder="Ingrese su contraseña" autofocus>
                </div>
                <div class="form-group btn-container">
                    <button class="btn btn-primary btn-block" type="submit"><i
                            class="fa fa-unlock fa-lg"></i>Desbloquear
                    </button>
                </div>
            </form>
            <p><a href="<?= base_url() ?>/im/LogOut">¿No eres <?= decryption($_SESSION['login_info']['user']) ?>? Inicie
                    sesión aquí.</a></p>
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
        const base_url = "<?= base_url(); ?>/im";
    </script>
    <script
        src="<?= media() ?>/js/app/Admin/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js?<?= versionSystem() ?>"></script>

</body>