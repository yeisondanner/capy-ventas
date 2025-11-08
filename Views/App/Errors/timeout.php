<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/main.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/loader.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS de la las alertas -->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/toastr.min.css">
    <link rel="shortcut icon"
        href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>"
        type="image/x-icon">
    <link rel="stylesheet"
        href="<?= media() ?>/css/app/<?= $data["page_js_css"] ?>/style_<?= $data["page_js_css"] ?>.css">
    <style>
        :root {
            --color-primary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_primary"] : "#4da8da"; ?>
            ;
            --color-secondary:
                <?= (getSystemInfo()) ? getSystemInfo()["c_color_secondary"] : "#004e89"; ?>
            ;
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

        <div class="login-box text-center">
            <h3 class="text-danger font-weight-bold text-center mt-4">¡Enlace Vencido!</h3>
            <p class="mt-2 text-muted">
                Este enlace ha expirado después de <strong>30 minutos</strong>.<br>
                Para continuar, por favor genere un nuevo enlace.
            </p>
            <a href="<?= base_url() ?>/login" class="btn btn-primary mt-3">
                <i class="fas fa-redo-alt"></i> Generar nuevo enlace
            </a>
        </div>
    </section>

    <!-- Essential javascripts for application to work-->
    <script src="<?= media() ?>/js/libraries/jquery-3.7.1.min.js"></script>
    <script src="<?= media() ?>/js/libraries/popper.min.js"></script>
    <script src="<?= media() ?>/js/libraries/bootstrap.min.js"></script>
    <script src="<?= media() ?>/js/libraries/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?= media() ?>/js/libraries/plugins/pace.min.js"></script>
    <!--Libreria de sweetalert-->
    <script type="text/javascript" src="<?= media() ?>/js/libraries/toastr.min.js"></script>
    <script type="text/javascript">
        const base_url = "<?= base_url(); ?>";
    </script>
    <script src="<?= media() ?>/js/app/<?= $data["page_js_css"] ?>/functions_<?= $data["page_js_css"] ?>.js"></script>
</body>

</html>