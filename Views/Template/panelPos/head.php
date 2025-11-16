<?php
//nombres de las variables de sesion
$name_sesion = config_sesion(1)['name'];
$nameVarLogin = $name_sesion . 'login';
$nameVarBusiness = $name_sesion . 'business_active';
$nameVarLoginInfo = $name_sesion . 'login_info';
$nameVarCart = $name_sesion . 'cart';
//vaiables de las rutas del css
$pageCssFolder = strtolower($data["page_container"]);
$pageCssFile = "style_" . strtolower($data["page_js_css"]) . ".css?" . versionSystem();
//variables del contendor
$pageContainer = ucfirst($data["page_container"]);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $data["page_title"] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--TODO: Colocar las descripciones de la pagina-->
    <meta name="description" content="<?= getSystemInfo()["c_description"] ?>">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/plugins/dataTables.bootstrap.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/plugins/buttons.bootstrap5.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/plugins/autoFill.bootstrap5.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/plugins/colReorder.bootstrap5.min.css?<?= versionSystem() ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <!--TODO: Cargamos el icono de la pagina-->
    <link rel="shortcut icon" href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>" type="image/x-icon">
    <!-- CSS de la vista -->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/app/POS/<?= $pageCssFolder ?>/<?= $pageCssFile ?>">
    <?php require_once "./Views/App/POS/" . $pageContainer . "/Libraries/head.php"; ?>
    <script type="text/javascript">
        // TODO: Base url
        const base_url = "<?= base_url(); ?>";
        // TODO: Moneda
        const getcurrency = "<?= getCurrency(); ?>";
    </script>
</head>

<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include "./Views/Template/panelPos/navbar.php"; ?>
    <!-- Sidebar menu-->
    <?php include "./Views/Template/panelPos/sidebarmenu.php"; ?>