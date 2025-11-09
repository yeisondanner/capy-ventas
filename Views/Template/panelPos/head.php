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
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!--TODO: Cargamos el icono de la pagina-->
    <link rel="shortcut icon"
          href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>"
          type="image/x-icon">
    <!-- CSS de la vista -->
    <?php
    $pageAssets = $data['page_assets'] ?? [];

    // Documentación (español):
    // Validamos y normalizamos el valor de 'page_container' para evitar pasar un array a strtolower().
    // - Si es string: lo usamos directamente.
    // - Si es array: intentamos obtener la clave 'name' o el primer elemento string disponible.
    // - Si no se puede obtener un string, usamos un valor por defecto 'default'.
    $pageContainerRaw = $data['page_container'] ?? '';
    if (is_string($pageContainerRaw)) {
        $pageCssFolder = strtolower($pageContainerRaw);
    } elseif (is_array($pageContainerRaw)) {
        $pageCssFolder = '';
        if (isset($pageContainerRaw['name']) && is_string($pageContainerRaw['name'])) {
            $pageCssFolder = strtolower($pageContainerRaw['name']);
        } else {
            $first = reset($pageContainerRaw);
            if (is_string($first)) {
                $pageCssFolder = strtolower($first);
            }
        }
    } else {
        $pageCssFolder = '';
    }

    // Fallback: si está vacío, usar carpeta por defecto 'default'
    if ($pageCssFolder === '') {
        $pageCssFolder = 'default';
    }

    $pageCssFile = "style_" . ($data['page_js_css'] ?? 'main') . ".css";
    $pageCssVersion = $pageAssets['css_version'] ?? null;
    $pageCssQuery = $pageCssVersion ? '?v=' . $pageCssVersion : '';
    ?>
    <link rel="stylesheet" type="text/css"
          href="<?= media() ?>/css/app/POS/<?= $pageCssFolder ?>/<?= $pageCssFile ?><?= $pageCssQuery ?>?<?= versionSystem() ?>">
    <?php require_once "./Views/App/POS/" . ucfirst($data["page_container"]) . "/Libraries/head.php"; ?>
    <script type="text/javascript">
        // TODO: Base url
       const base_url = "<?= base_url(); ?>/pos";
        // TODO: Moneda
       const getcurrency = "<?= getCurrency(); ?>";
    </script>
</head>
<body class="app sidebar-mini">
<!-- Navbar-->
<?php include "./Views/Template/panelPos/navbar.php"; ?>
<!-- Sidebar menu-->
<?php include "./Views/Template/panelPos/sidebarmenu.php"; ?>
