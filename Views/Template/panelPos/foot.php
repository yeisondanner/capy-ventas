<!-- TODO: jquery - falta actualizar-->
<script src="<?= media() ?>/js/libraries/POS/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>
<!-- TODO: boostrap.min.js -->
<script src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
<!-- TODO: libreria principal main-->
<script src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>
<!-- TODO: Librerias de la view-->
<?php require_once "./Views/App/POS/" . ucfirst($data["page_container"]) . "/Libraries/foot.php"; ?>

<!-- TODO: Page specific javascripts-->
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
<script src="<?= media() ?>/js/app/POS/<?= $pageJsFolder ?>/<?= $pageJsFile ?><?= $pageJsQuery ?>?<?= versionSystem() ?>"></script>
</body>
</html>