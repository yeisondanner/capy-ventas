<!-- TODO: jquery - falta actualizar-->
<script src="<?= media() ?>/js/libraries/POS/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>
<!-- TODO: boostrap.min.js -->
<script src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
<!-- Data table plugin-->
<script type="text/javascript" src="<?= media() ?>/js/libraries/Admin/plugins/jquery.dataTables.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" src="<?= media() ?>/js/libraries/Admin/plugins/dataTables.bootstrap.min.js?<?= versionSystem() ?>"></script>

<!-- Buttons for DataTables-->
<script type="text/javascript" language="javascript"
    src="<?= media() ?>/js/libraries/Admin/plugins/dataTables.buttons.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/Admin/plugins/jszip.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/Admin/plugins/pdfmake.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/Admin/plugins/vfs_fonts.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript"
    src="<?= media() ?>/js/libraries/Admin/plugins/buttons.html5.min.js?<?= versionSystem() ?>"></script>
<!-- TODO: libreria principal main-->
<script src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>

<!-- TODO: Librerias de la view-->
<?php
//variables de las rutas del js
$pageJsFolder = strtolower($data["page_container"]);
$pageJsFile = "functions_" . strtolower($data["page_js_css"]) . ".js?" . versionSystem();
require_once "./Views/App/POS/" . ucfirst($data["page_container"]) . "/Libraries/foot.php";
?>

<script src="<?= media() ?>/js/app/POS/<?= $pageJsFolder ?>/<?= $pageJsFile ?>"></script>
</body>

</html>