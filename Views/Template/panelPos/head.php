<?php
//nombres de las variables de sesion
$name_sesion = config_sesion(1)['name'];
$nameVarLogin = $name_sesion . 'login';
$nameVarBusiness = $name_sesion . 'business_active';
$nameVarLoginInfo = $name_sesion . 'login_info';
$nameVarCart = $name_sesion . 'cart';
//destruimos cualquier valor agregado al carro
unset($_SESSION[$nameVarCart]);
//vaiables de las rutas del css
$pageCssFolder = strtolower($data["page_container"]);
if (is_array($data["page_js_css"])) {
    $pageCssFile = [];
    foreach ($data["page_js_css"] as $key => $value) {
        array_push($pageCssFile, "style_" . strtolower($value) . ".css?" . versionSystem());
    }
} else {
    $pageCssFile = "style_" . strtolower($data["page_js_css"]) . ".css?" . versionSystem();
}

//variables del contendor
$pageContainer = ucfirst($data["page_container"]);
//obtiene las opciones y permisos de la app
get_option_and_permission_app();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>CAPY VENTAS - <?= $data["page_title"] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--TODO: Colocar las descripciones de la pagina-->
    <meta name="description" content="<?= getSystemInfo()["c_description"] ?>">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/POS/plugins/dataTables.bootstrap.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/POS/plugins/buttons.bootstrap5.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/POS/plugins/autoFill.bootstrap5.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css"
        href="<?= media() ?>/css/libraries/POS/plugins/colReorder.bootstrap5.min.css?<?= versionSystem() ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <!--TODO: Cargamos el icono de la pagina-->
    <link rel="shortcut icon" href="<?= media() ?>/capysm.png?<?= versionSystem() ?>" type="image/x-icon">
    <!-- CSS de la vista -->
    <?php
    if (is_array($pageCssFile)) {
        foreach ($pageCssFile as $key => $value) {
            echo "<link rel='stylesheet' type='text/css' href='" . media() . "/css/app/POS/" . $pageCssFolder . "/" . $value . "'>";
        }
    } else {
        echo "<link rel='stylesheet' type='text/css' href='" . media() . "/css/app/POS/" . $pageCssFolder . "/" . $pageCssFile . "'>";
    }
    ?>
    <!-- Css: para caja -->
    <link rel="stylesheet" href="<?= media() ?>/css/app/POS/box/style_box.css?<?= versionSystem() ?>">
    <?php require_once "./Views/App/POS/" . $pageContainer . "/Libraries/head.php"; ?>
    <script type="text/javascript">
        // TODO: Base url
        const base_url = "<?= base_url(); ?>";
        // TODO: Base para las imagenes
        const media_url = "<?= media(); ?>";
        // TODO: Moneda
        const getcurrency = "<?= getCurrency(); ?>";
        //TODO: Generar perfil
        const generate_profile = "<?= GENERAR_PERFIL ?>";
    </script>
</head>

<body class="app sidebar-mini">
    <div class="position-fixed bottom-0 end-0 p-1 rounded-top-2 bg-dark bg-opacity-50 border border-white text-white"
        onclick="showAlert({title:'Identificador de Usuario', message:'Código único e invariable que identifica al usuario dentro de la plataforma.', type:'info', icon:'info',position:'bottom-left', timer:1000, status:true, url:''})"
        style="z-index: 9999;">
        UID: <?= str_pad($_SESSION[$nameVarLoginInfo]['idUser'], 11, "0", STR_PAD_LEFT); ?>
    </div>
    <!-- Navbar-->
    <?php include "./Views/Template/panelPos/navbar.php"; ?>
    <!-- Sidebar menu-->
    <?php include "./Views/Template/panelPos/sidebarmenu.php"; ?>