<!-- Essential javascripts for application to work-->
<script src="<?= media() ?>/js/libraries/jquery-3.7.1.min.js?<?= versionSystem() ?>"></script>
<!--Libreria de sweetalert-->
<script type="text/javascript" src="<?= media() ?>/js/libraries/toastr.min.js?<?= versionSystem() ?>"></script>
<script src="<?= media() ?>/js/libraries/popper.min.js?<?= versionSystem() ?>"></script>
<script src="<?= media() ?>/js/libraries/bootstrap.min.js?<?= versionSystem() ?>"></script>
<!-- Data table plugin-->
<script type="text/javascript" src="<?= media() ?>/js/libraries/plugins/jquery.dataTables.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" src="<?= media() ?>/js/libraries/plugins/dataTables.bootstrap.min.js?<?= versionSystem() ?>"></script>

<!-- Buttons for DataTables-->
<script type="text/javascript" language="javascript"
    src="<?= media() ?>/js/libraries/plugins/dataTables.buttons.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/plugins/jszip.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/plugins/pdfmake.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/plugins/vfs_fonts.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript"
    src="<?= media() ?>/js/libraries/plugins/buttons.html5.min.js?<?= versionSystem() ?>"></script>
<!--Libreria prinicipal de la app-->
<script src="<?= media() ?>/js/libraries/main.js?<?= versionSystem() ?>"></script>
<!--Libreria que valida la sesion de usuario-->
<script src="<?= media() ?>/js/libraries/validateSesionActivity.js?<?= versionSystem() ?>"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="<?= media() ?>/js/libraries/plugins/pace.min.js?<?= versionSystem() ?>"></script>

<!--Librerias de la view-->
<?php require_once "./Views/App/Admin/" . ucfirst($data["page_container"]) . "/Libraries/foot.php"; ?>
<!-- Page specific javascripts-->
<script type="text/javascript">
    const base_url = "<?= base_url(); ?>/im";
</script>
<?php
$pageAssets = $data['page_assets'] ?? [];
// Obtiene el nombre de la carpeta de assets JS para la página.
// Acepta $data['page_container'] como string o array.
// - Si es string, se convierte a minúsculas.
// - Si es array, se usa el elemento 'name' si existe; si no, se concatenan los valores con '_'.
// - Si no está definido o queda vacío, se usa 'default'.
// Se normaliza reemplazando caracteres no permitidos por '_' para evitar rutas inválidas.
$pageContainer = $data['page_container'] ?? '';

if (is_array($pageContainer)) {
    if (!empty($pageContainer['name']) && is_scalar($pageContainer['name'])) {
        $rawFolder = (string) $pageContainer['name'];
    } else {
        $rawFolder = implode('_', array_map('strval', $pageContainer));
    }
} else {
    $rawFolder = (string) $pageContainer;
}

$rawFolder = trim($rawFolder);
if ($rawFolder === '') {
    $rawFolder = 'default';
}

// Forzar minúsculas y sanear la cadena para usar en nombres de carpeta/archivo
$pageJsFolder = strtolower($rawFolder);
$pageJsFolder = preg_replace('/[^a-z0-9_\-]/', '_', $pageJsFolder);
$pageJsFile = "functions_" . $data['page_js_css'] . ".js";
$pageJsVersion = $pageAssets['js_version'] ?? null;
$pageJsQuery = $pageJsVersion ? '?v=' . $pageJsVersion : '';
?>
<script src="<?= media() ?>/js/app/Admin/<?= $pageJsFolder ?>/<?= $pageJsFile ?><?= $pageJsQuery ?>?<?= versionSystem() ?>"></script>

</body>

</html>