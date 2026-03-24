<!-- ==========================================
     1. LIBRERÍAS CORE DE LA APLICACIÓN
     ========================================== -->
<!-- jQuery: Librería esencial requerida por Bootstrap y múltiples plugins como DataTables. (TODO: falta actualizar a la última versión) -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Jquery/v3.7.0/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>

<!-- Bootstrap: Framework CSS y JS básico para el diseño, estructura de la UI, grids y componentes interactivos modales (TODO: verificar versión) -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/popper.min.js?<?= versionSystem() ?>"></script>

<!-- ==========================================
     2. DATATABLES - NÚCLEO Y ADAPTACIÓN VISUAL
     ========================================== -->
<!-- DataTables Core: Motor principal para el manejo avanzado, paginación, búsqueda y renderizado de las tablas de datos -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/DataTables/v2.3.4/jquery.dataTables.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/DataTables/v2.3.4/dataTables.min.js?<?= versionSystem() ?>"></script>

<!-- DataTables Bootstrap 5: Capa de integración para asegurar que las tablas y sus controles luzcan nativos de Bootstrap 5 -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/DataTables/v2.3.4/dataTables.bootstrap5.min.js?<?= versionSystem() ?>"></script>

<!-- ==========================================
     3. DATATABLES - FUNCIONES DE INTERFAZ AVANZADA (PLUGINS)
     ========================================== -->
<!-- AutoFill: Extiende DataTables permitiendo rellenar celdas arrastrando y soltando al puro estilo de Excel -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/AutoFill/v2.7.1/dataTables.autoFill.min.js?<?= versionSystem() ?>"></script>

<!-- ColReorder: Permite a los usuarios reorganizar dinámicamente el orden de las columnas de las tablas usando Drag & Drop -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/ColReorder/v2.1.2/dataTables.colReorder.min.js?<?= versionSystem() ?>"></script>
<!-- ColReorder Bootstrap 5: Capa de estilización para asegurar que el reordenamiento se vea bien en componentes Bootstrap 5 -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/ColReorder/v2.1.2/colReorder.bootstrap5.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Responsive/v3.0.8/dataTables.responsive.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Responsive/v3.0.8/responsive.bootstrap5.min.js?<?= versionSystem() ?>"></script>

<!-- ==========================================
     4. DATATABLES - EXPORTACIÓN Y GENERACIÓN DE REPORTES
     ========================================== -->
<!-- Botones Base: Plugin núcleo que proporciona a las tablas la API general para gestionar cualquier tipo de botón -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Buttons/v3.2.6/dataTables.buttons.min.js?<?= versionSystem() ?>"></script>
<!-- Botones HTML5: Habilita los botones específicos para exportar a formatos modernos basados en navegador como Excel (XLSX), CSV o PDF -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Buttons/v3.2.6/buttons.html5.min.js?<?= versionSystem() ?>"></script>

<!-- JSZip: Dependencia estricta para DataTables al intentar empaquetar y generar archivos comprimidos requeridos por el formato Excel (.xlsx) -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/JSZip/v3.10.1/jszip.min.js?<?= versionSystem() ?>"></script>

<!-- pdfMake y VFS Fonts: Dependencias requeridas por DataTables para generar documentos en PDF. Aportan la capacidad de dibujo PDF y almacenamiento temporal de fuentes -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/PDFMake/v0.1.36/pdfmake.min.js?<?= versionSystem() ?>"></script>
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/PDFMake/v0.1.36/vfs_fonts.js?<?= versionSystem() ?>"></script>

<!-- Chart.js: Herramienta empleada para la visualización de datos estadísticos avanzados mediante distintos tipos de gráficas interactivas -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/ChartJs/v4.3.0/chart.js?<?= versionSystem() ?>"></script>

<!-- ==========================================
     5. LIBRERÍAS DE UTILIDAD GRÁFICA Y UX DE TERCEROS
     ========================================== -->
<!-- Chart.js: Herramienta empleada para la visualización de datos estadísticos avanzados mediante distintos tipos de gráficas interactivas -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/ChartJs/v4.5.1/chart.umd.min.js?<?= versionSystem() ?>"></script>

<!-- SweetAlert2: Librería moderna (TODO) que reemplaza las aburridas alertas estándar (alert()) de JS con ventanas emergentes elegantes, modales dinámicos y responsivos -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/SweetAlert2/v11.26.3/SweerAlert2.js?<?= versionSystem() ?>"></script>
<!-- html2canvas: Librería para capturar y convertir el contenido de una página web en una imagen -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/plugins/Html2Canvas/v1.4.1/html2canvas.min.js?<?= versionSystem() ?>"></script>

<!-- ==========================================
     6. ARCHIVO PRINCIPAL DE LA APLICACIÓN (MAIN)
     ========================================== -->
<!-- Main JS (TODO): Archivo que contiene la inicialización general de la app, variables del DOM, y listeners globales. Debe cargarse al final porque depende del core -->
<script type="text/javascript" language="javascript" src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>

<!-- TODO: Librerias de la view-->
<?php
//variables de las rutas del js
$pageJsFolder = strtolower($data["page_container"]);

if (is_array($data["page_js_css"])) {
    $pageJsFile = [];
    foreach ($data["page_js_css"] as $key => $value) {
        array_push($pageJsFile, "functions_" . strtolower($value) . ".js?" . versionSystem());
    }
} else {
    $pageJsFile = "functions_" . strtolower($data["page_js_css"]) . ".js?" . versionSystem();
}

require_once "./Views/App/POS/" . ucfirst($data["page_container"]) . "/Libraries/foot.php";
/**
 * Validacion de los archivos js cuando el valor es un array
 */
if (is_array($pageJsFile)) {
    foreach ($pageJsFile as $key => $value) {
        echo "<script type='module' src='" . media() . "/js/app/POS/" . $pageJsFolder . "/" . $value . "'></script>";
    }
} else {
    echo "<script type='module' src='" . media() . "/js/app/POS/" . $pageJsFolder . "/" . $pageJsFile . "'></script>";
}
?>
<!-- Servicios para caja -->
<script type="module" src="<?= media() ?>/js/app/POS/box/functions_box_api.js?<?= versionSystem() ?>"></script>
<script type="module" src="<?= media() ?>/js/app/POS/box/functions_box.js?<?= versionSystem() ?>"></script>
</body>

</html>