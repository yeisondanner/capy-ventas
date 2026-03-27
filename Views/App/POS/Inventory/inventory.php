<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-box-seam"></i> Inventario</h1>
            <p>Administra los productos de tu negocio: registra nuevas referencias, actualiza precios y controla el
                stock</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/inventory">Inventario</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $inventory = validate_permission_app(3, "c", false)['create'];
                    if ($inventory == 1): ?>
                        <button class="btn btn-outline-primary btn-sm" type="button" id="btnOpenProductModal">
                            <i class="bi bi-plus-lg"></i> Agregar nuevo producto
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-outline-success btn-sm" type="button" id="btnGenerateAllBarcodes">
                        <i class="bi bi-upc-scan"></i> Generar códigos de barras
                    </button>
                    <?php
                    $category = validate_permission_app(10, "r", false)['read'];
                    if ($category == 1): ?>
                        <button class="btn btn-outline-info btn-sm" type="button" id="btnOpenCategoryModal">
                            <i class="bi bi-collection"></i> Categorías
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="tile rounded-3">
                <div class="tile-body py-2 px-3">
                    <p class="text-muted fw-semibold small mb-2">
                        <i class="bi bi-question-circle me-1"></i> ¿Qué significan los colores de las filas?
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Leyenda: Peligro -->
                        <div
                            class="d-flex align-items-center flex-wrap gap-1 bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-3 px-3 py-2 fw-normal small">
                            <i class="bi bi-exclamation-octagon-fill flex-shrink-0"></i>
                            <span>Poco stock o vence pronto — <strong>atención inmediata</strong></span>
                            <span class="vr opacity-50 mx-1"></span>
                            <span class="opacity-75">Stock ≤ 5 &nbsp;|&nbsp; Vence en ≤ 5 días</span>
                        </div>
                        <!-- Leyenda: Advertencia -->
                        <div
                            class="d-flex align-items-center flex-wrap gap-1 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3 px-3 py-2 fw-normal small">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <span>Stock o vencimiento en <strong>nivel de alerta</strong></span>
                            <span class="vr opacity-50 mx-1"></span>
                            <span class="opacity-75">Stock ≤ 10 &nbsp;|&nbsp; Vence en ≤ 10 días</span>
                        </div>
                        <!-- Leyenda: Informacion -->
                        <div
                            class="d-flex align-items-center flex-wrap gap-1 bg-info-subtle text-info-emphasis border border-info-subtle rounded-3 px-3 py-2 fw-normal small">
                            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                            <span>Stock o vencimiento a <strong>tener en cuenta</strong></span>
                            <span class="vr opacity-50 mx-1"></span>
                            <span class="opacity-75">Stock ≤ 15 &nbsp;|&nbsp; Vence en ≤ 15 días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped display nowrap w-100"
                            id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Vencimiento</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                    <th>Stock</th>
                                    <th>Precio venta</th>
                                    <th>Precio compra</th>
                                    <th>Ganancia</th>
                                    <th>Publico</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= footerPos($data) ?>

<!-- Modal: Registrar producto -->
<div class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formSaveProduct" autocomplete="off">
            <div class="modal-header bg-primary text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalProductLabel">Registrar producto</h5>
                        <p class="mb-0 small text-white text-opacity-75">Aqui podras registrar un nuevo producto</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-md-12 d-flex justify-content-center">
                        <div class="card-body p-4">
                            <div class="logo-upload-area mb-3 position-relative" onclick="document.getElementById('flInput').click()">
                                <!-- Skeleton / Spinner -->
                                <div class="d-flex justify-content-center align-items-center bg-light position-absolute w-100 h-100 top-0 start-0 z-1" style="border-radius: inherit;">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png" id="logoPreview"
                                    class="logo-preview-img mb-2 position-relative z-2 opacity-0" alt="Logo" loading="lazy" style="transition: opacity 0.3s ease;" onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('d-none');">
                                <div class="text-primary fw-medium small position-relative z-2"><i class="bi bi-cloud-upload me-1"></i> Subir
                                    foto del producto</div>
                                <div class="text-muted small mt-1 position-relative z-2" style="font-size: 0.75rem;">Click para subir (Max
                                    2MB)</div>
                            </div>
                            <input type="file" class="d-none" id="flInput" name="flInput" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <label for="txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <input type="text" class="form-control" id="txtProductName" name="txtProductName"
                                maxlength="255" required placeholder="Ej. Café molido premium">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <label class="form-label" for="slctBarcodeFormat">Tipo de Código (Uso) <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-upc"></i></span>
                            <select id="slctBarcodeFormat" name="slctBarcodeFormat" class="form-select">
                                <optgroup label="Estándares para Productos (Retail)">
                                    <option value="EAN13" selected>EAN-13 (13 dígitos)</option>
                                    <option value="EAN8">EAN-8 (8 dígitos)</option>
                                    <option value="EAN5">EAN-5 (Suplemento 5 dígitos)</option>
                                    <option value="EAN2">EAN-2 (Suplemento 2 dígitos)</option>
                                    <option value="UPC">UPC-A (12 dígitos)</option>
                                    <option value="UPCE">UPC-E (6 a 8 dígitos)</option>
                                </optgroup>
                                <optgroup label="Alfanuméricos (Versátiles)">
                                    <option value="CODE128" selected>CODE 128 (Automático - Recomendado)</option>
                                    <option value="CODE128A">CODE 128 A (Mayúsculas y control)</option>
                                    <option value="CODE128B">CODE 128 B (Mayúsculas y minúsculas)</option>
                                    <option value="CODE128C">CODE 128 C (Solo pares numéricos)</option>
                                    <option value="CODE39">CODE 39 (Básico)</option>
                                </optgroup>
                                <optgroup label="Logística y Transporte">
                                    <option value="ITF14">ITF-14 (Cajas de cartón)</option>
                                    <option value="ITF">ITF / Interleaved 2 of 5</option>
                                </optgroup>
                                <optgroup label="Industriales y Especiales">
                                    <option value="codabar">Codabar (Bibliotecas/Salud)</option>
                                    <option value="PHARMACODE">Pharmacode (Fármacos)</option>
                                    <option value="MSI">MSI (Inventarios)</option>
                                    <option value="MSI10">MSI 10 (Mod 10)</option>
                                    <option value="MSI11">MSI 11 (Mod 11)</option>
                                    <option value="MSI1010">MSI 1010 (Mod 1010)</option>
                                    <option value="MSI1110">MSI 1110 (Mod 1110)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <label for="txtProductCode" class="form-label">Código <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary" type="button" id="btnGenerateCode" title="Generar código" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-upc"></i>
                            </button>
                            <input type="text" class="form-control" id="txtProductCode" name="txtProductCode"
                                maxlength="60" minlength="1" required placeholder="Ej. 123456789">
                            <button class="btn btn-outline-secondary" type="button" id="btnScanCode" title="Escanear código" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <!-- Previsualización del código de barras generado -->
                        <div class="border rounded-3 bg-light p-3 text-center" id="barcodePreviewWrapper">
                            <label class="text-muted fw-bold d-block text-uppercase mb-2"
                                style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                <i class="bi bi-upc-scan me-1"></i> Previsualización del código de barras
                            </label>
                            <!-- Estado vacío: visible cuando aún no se ha ingresado un código -->
                            <div id="barcodeEmptyState" class="text-muted small py-2">
                                <i class="bi bi-upc fs-3 d-block mb-1 opacity-50"></i>
                                Ingresa o genera un código para previsualizar
                            </div>
                            <!-- SVG del código de barras: se renderiza con JsBarcode -->
                            <svg id="barcode" class="d-none img-fluid"></svg>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="slctProductCategory" class="form-label">Categoría <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-collection"></i> </span>
                            <select class="form-select" id="slctProductCategory" name="slctProductCategory" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="slctProductSupplier" class="form-label">Proveedor <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select" id="slctProductSupplier" name="slctProductSupplier" required>
                                <option value="" selected disabled>Selecciona un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="slctProductMeasurement" class="form-label">Unidad de medida <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <select class="form-select" id="slctProductMeasurement" name="slctProductMeasurement"
                                required>
                                <option value="" selected disabled>Selecciona una unidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="txtProductDateExpirated" class="form-label">Fecha de vencimiento(Opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-range"></i></span>
                            <input type="date" name="txtProductDateExpirated" id="txtProductDateExpirated"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="txtProductStock" class="form-label">Stock (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-numeric-up-alt"></i></span>
                            <input type="number" step="0.01" min="0" max="99999999.99" class="form-control"
                                id="txtProductStock" name="txtProductStock" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="txtProductPurchasePrice" class="form-label">Precio compra <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" max="99999999.99" class="form-control"
                                id="txtProductPurchasePrice" name="txtProductPurchasePrice" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <label for="txtProductSalesPrice" class="form-label">Precio venta <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" max="99999999.99" class="form-control"
                                id="txtProductSalesPrice" name="txtProductSalesPrice" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="txtProductDescription" class="form-label">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                            <textarea class="form-control" id="txtProductDescription" name="txtProductDescription"
                                rows="3" placeholder="Describe las características principales del producto"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card shadow-sm border-light">
                            <div class="card-body d-flex justify-content-between align-items-center p-4">
                                <!-- Columna de Texto -->
                                <div class="me-3">
                                    <label class="form-check-label fw-bold text-dark d-block mb-1"
                                        for="chkProductStatus">
                                        Mostrar en el catálogo
                                    </label>
                                    <small class="text-muted">
                                        El producto será visible en el catálogo de ventas si esta opción está activada.
                                    </small>
                                </div>
                                <div class="form-check form-switch fs-2 m-0">
                                    <!-- Agregamos la clase 'switch-success' solo para el color verde -->
                                    <input class="form-check-input switch-success" type="checkbox" role="switch"
                                        id="chkProductStatus" name="chkProductStatus">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reporte de producto -->
<div class="modal fade" id="modalProductReport" tabindex="-1" aria-labelledby="modalProductReportLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">

            <!-- Encabezado -->
            <div class="modal-header bg-secondary text-dark border-bottom-0 py-2">

                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-text fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalProductReportLabel">Reporte del producto</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras ver el reporte del producto</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

            </div>

            <div class="modal-body bg-light p-4">
                <div class="row g-3">

                    <!-- Columna Izquierda: Imagen y Datos Básicos -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <!-- Contenedor para la imagen principal -->
                            <div class="mb-3 position-relative">
                                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=600&auto=format&fit=crop"
                                    class="rounded border object-fit-cover w-100 position-relative z-2 opacity-1" id="reportImageMain" loading="lazy"
                                    alt="Producto Principal" style="aspect-ratio: 4/3;">
                            </div>

                            <div class="mb-3 border-bottom pb-2">
                                <h4 class="fw-bold mb-0 text-dark" id="reportProductName">Nombre del producto</h4>
                                <span class="mt-1" id="reportProductStatus">Estado</span>
                            </div>

                            <div class="small">
                                <div class="mb-3 text-center">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Código</label>
                                    <svg id="reportProductBarcode" class="img-fluid d-none"></svg>
                                    <span id="reportProductCode" class="d-none">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Categoría</label>
                                    <span id="reportProductCategory">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Proveedor</label>
                                    <span id="reportProductSupplier">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Fecha de expiración</label>
                                    <span id="reportProductExpirationDate">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Unidad de medida</label>
                                    <span id="reportProductMeasurement">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small"
                                        style="font-size: 0.7rem;">Es Publico</label>
                                    <span id="reportProductIsPublic">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Columna Derecha: Métricas, Galería y Descripción -->
                    <div class="col-12 col-md-6 col-xl-9">
                        <div class="row g-2">
                            <!-- Métricas -->
                            <div class="col-12 mb-3">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white text-center">
                                            <small class="text-muted d-block fw-bold text-uppercase"
                                                style="font-size: 0.65rem;">Stock</small>
                                            <span class="h6 mb-0 fw-bold text-primary" id="reportProductStock">0</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white text-center">
                                            <small class="text-muted d-block fw-bold text-uppercase"
                                                style="font-size: 0.65rem;">Compra</small>
                                            <span class="h6 mb-0 fw-bold text-dark"
                                                id="reportProductPurchase">$0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-white text-center">
                                            <small class="text-muted d-block fw-bold text-uppercase"
                                                style="font-size: 0.65rem;">Venta</small>
                                            <span class="h6 mb-0 fw-bold text-success"
                                                id="reportProductSale">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Galería Visual con Proporción 1:1 (Cuadrada) -->
                            <div class="col-12 col-md-7 col-xl-6 bg-white p-3 border rounded shadow-sm mb-3">
                                <label class="text-muted fw-bold d-block text-uppercase small mb-2"
                                    style="font-size: 0.7rem;">Galería de Fotos</label>
                                <div class="row g-2 overflow-y-auto" style="max-height: 200px;" id="listReportImages">
                                    --
                                </div>
                            </div>
                            <!-- Descripción -->
                            <div class="col-12 col-md-5 col-xl-6 bg-white p-3 border rounded shadow-sm mb-3">
                                <label class="text-muted fw-bold d-block text-uppercase small mb-1 border-bottom pb-1"
                                    style="font-size: 0.7rem;">Descripción</label>
                                <p class="mb-0 small text-secondary" id="reportProductDescription">Sin descripción
                                    registrada.</p>
                            </div>
                            <!-- Historial del Producto -->
                            <div class="col-12 bg-white p-3 border rounded shadow-sm h-100">
                                <label class="text-muted fw-bold d-block text-uppercase small mb-2 border-bottom pb-1"
                                    style="font-size: 0.7rem;"><i class="bi bi-clock-history me-1"></i> Historial de
                                    modificaciones</label>
                                <div id="sectionTableReport" class="bg-light rounded-3 border p-1"
                                    style="max-height: 350px; overflow-y: auto; overflow-x: hidden;">
                                    <table class="table table-sm table-hover table-bordered table-striped display nowrap w-100"
                                        style="font-size: 0.85rem;" id="reportTableHistoryProduct">
                                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th></th>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Stock</th>
                                                <th>Precio Compra</th>
                                                <th>Precio Venta</th>
                                                <th>Categoría</th>
                                                <th>Vencimiento</th>
                                                <th>Usuario</th>
                                                <th style="min-width: 120px;">Fecha / Hora</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyProductHistory">
                                        </tbody>
                                        <tfoot class="table-warning">
                                            <tr>
                                                <th colspan="3" class="text-end text-uppercase">Total:</th>
                                                <th id="totalStockFooter" class="text-primary fw-bold text-start">0.00
                                                </th>
                                                <th colspan="6"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Contenedor genérico para restricciones (Planes o Permisos) -->
                                <div id="restrictionPromptContainer" style="max-height: 350px;"
                                    class="d-none text-center p-4">
                                    <div id="restrictionIconWrapper"
                                        class="mb-3 d-inline-flex p-3 rounded-circle bg-info bg-opacity-10 text-info">
                                        <i id="restrictionIcon" class="bi bi-gem fs-2"></i>
                                    </div>
                                    <h5 id="restrictionTitle" class="fw-bold text-dark mb-2">
                                        Historial completo disponible en planes superiores
                                    </h5>
                                    <p id="restrictionMessage" class="text-muted small mb-3">
                                        Actualiza tu plan para ver el historial detallado de modificaciones y auditoría
                                        de este producto.
                                    </p>
                                    <a id="restrictionActionBtn" href="<?= base_url() ?>/pos/dashboard"
                                        class="btn btn-primary btn-sm rounded-pill px-4">
                                        <i id="restrictionActionIcon" class="bi bi-arrow-up-circle"></i>
                                        <span id="restrictionActionText">Mejorar mi plan</span>
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar producto -->
<div class="modal fade" id="modalUpdateProduct" tabindex="-1" aria-labelledby="modalUpdateProductLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formUpdateProduct" autocomplete="off">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalUpdateProductLabel">Actualizar producto</h5>
                        <p class="mb-0 small text-white text-opacity-75">Aqui podras actualizar la informacion de tu
                            producto</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <input type="hidden" name="update_txtProductId" id="update_txtProductId">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                        <div class="card-body p-4">
                            <div class="logo-upload-area mb-3 position-relative"
                                onclick="document.getElementById('update_flInput').click()">
                                <!-- Skeleton / Spinner -->
                                <div class="d-flex justify-content-center align-items-center bg-light position-absolute w-100 h-100 top-0 start-0 z-1" style="border-radius: inherit;">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png"
                                    id="update_logoPreview" class="logo-preview-img mb-2 position-relative z-2 opacity-0" loading="lazy" style="object-fit: contain; aspect-ratio: 1/1; transition: opacity 0.3s ease;" onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('d-none');">
                                <div class="text-primary fw-medium small position-relative z-2"><i class="bi bi-cloud-upload me-1"></i> Subir
                                    foto del producto</div>
                                <div class="text-muted small mt-1 position-relative z-2" style="font-size: 0.75rem;">Click para subir (Max
                                    2MB)</div>
                            </div>
                            <input type="file" class="d-none" id="update_flInput" name="update_flInput"
                                accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-7 col-xl-8">
                        <div class="accordion accordion-flush border rounded-3 bg-light" id="listAccordionImages">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button collapsed bg-light" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#listImages" aria-expanded="false"
                                        aria-controls="listImages">
                                        <i class="bi bi-images me-2"></i> Imagenes del producto
                                    </button>
                                </h2>
                                <div id="listImages" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingOne" data-bs-parent="#listAccordionImages">
                                    <div class="accordion-body overflow-y-auto" style="max-height: 250px;">
                                        <div class="row" id="listImagesContainer">
                                            <div class="col-4 p-2">
                                                <div class=" border rounded-3 bg-light position-relative">
                                                    <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png"
                                                        class="img-fluid" alt="" loading="lazy">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm position-absolute top-0 end-0"><i
                                                            class="bi bi-x-lg"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <label for="update_txtProductName" class="form-label">Nombre <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <input type="text" class="form-control" id="update_txtProductName"
                                name="update_txtProductName" maxlength="255" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <label class="form-label" for="update_slctBarcodeFormat">Tipo de Código (Uso) <span class="text-danger">*</span> </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-upc"></i></span>
                            <select id="update_slctBarcodeFormat" name="update_slctBarcodeFormat" class="form-select">
                                <optgroup label="Estándares para Productos (Retail)">
                                    <option value="EAN13" selected>EAN-13 (13 dígitos)</option>
                                    <option value="EAN8">EAN-8 (8 dígitos)</option>
                                    <option value="EAN5">EAN-5 (Suplemento 5 dígitos)</option>
                                    <option value="EAN2">EAN-2 (Suplemento 2 dígitos)</option>
                                    <option value="UPC">UPC-A (12 dígitos)</option>
                                    <option value="UPCE">UPC-E (6 a 8 dígitos)</option>
                                </optgroup>
                                <optgroup label="Alfanuméricos (Versátiles)">
                                    <option value="CODE128" selected>CODE 128 (Automático - Recomendado)</option>
                                    <option value="CODE128A">CODE 128 A (Mayúsculas y control)</option>
                                    <option value="CODE128B">CODE 128 B (Mayúsculas y minúsculas)</option>
                                    <option value="CODE128C">CODE 128 C (Solo pares numéricos)</option>
                                    <option value="CODE39">CODE 39 (Básico)</option>
                                </optgroup>
                                <optgroup label="Logística y Transporte">
                                    <option value="ITF14">ITF-14 (Cajas de cartón)</option>
                                    <option value="ITF">ITF / Interleaved 2 of 5</option>
                                </optgroup>
                                <optgroup label="Industriales y Especiales">
                                    <option value="codabar">Codabar (Bibliotecas/Salud)</option>
                                    <option value="PHARMACODE">Pharmacode (Fármacos)</option>
                                    <option value="MSI">MSI (Inventarios)</option>
                                    <option value="MSI10">MSI 10 (Mod 10)</option>
                                    <option value="MSI11">MSI 11 (Mod 11)</option>
                                    <option value="MSI1010">MSI 1010 (Mod 1010)</option>
                                    <option value="MSI1110">MSI 1110 (Mod 1110)</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <label for="update_txtProductCode" class="form-label">Código <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <button class="btn btn-outline-primary" type="button" id="update_btnGenerateCode" title="Generar código" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-upc"></i>
                            </button>
                            <input type="text" class="form-control" id="update_txtProductCode"
                                name="update_txtProductCode" maxlength="60" minlength="1" required placeholder="Ej. 123456789">
                            <button class="btn btn-outline-secondary" type="button" id="update_btnScanCode" title="Escanear código" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <!-- Previsualización del código de barras generado -->
                        <div class="border rounded-3 bg-light p-3 text-center" id="update_barcodePreviewWrapper">
                            <label class="text-muted fw-bold d-block text-uppercase mb-2"
                                style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                <i class="bi bi-upc-scan me-1"></i> Previsualización del código de barras
                            </label>
                            <!-- Estado vacío: visible cuando aún no se ha ingresado un código -->
                            <div id="update_barcodeEmptyState" class="text-muted small py-2">
                                <i class="bi bi-upc fs-3 d-block mb-1 opacity-50"></i>
                                Ingresa o genera un código para previsualizar
                            </div>
                            <!-- SVG del código de barras: se renderiza con JsBarcode -->
                            <svg id="update_barcode" class="d-none img-fluid"></svg>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="update_slctProductCategory" class="form-label">Categoría <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-collection"></i></span>
                            <select class="form-select" id="update_slctProductCategory"
                                name="update_slctProductCategory" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="update_slctProductSupplier" class="form-label">Proveedor <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select" id="update_slctProductSupplier"
                                name="update_slctProductSupplier" required>
                                <option value="" selected disabled>Selecciona un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="update_slctProductMeasurement" class="form-label">Unidad de medida <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <select class="form-select" id="update_slctProductMeasurement"
                                name="update_slctProductMeasurement" required>
                                <option value="" selected disabled>Selecciona una unidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <label for="update_txtProductDateExpirated" class="form-label">Fecha de
                            vencimiento(Opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-range"></i></span>
                            <input type="date" name="update_txtProductDateExpirated" id="update_txtProductDateExpirated"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="update_txtProductStock" class="form-label">Stock (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-numeric-up-alt"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductStock"
                                name="update_txtProductStock">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="update_txtProductPurchasePrice" class="form-label">Precio compra
                            <?= getCurrency() ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="update_txtProductPurchasePrice" name="update_txtProductPurchasePrice" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <label for="update_txtProductSalesPrice" class="form-label">Precio venta
                            <?= getCurrency() ?><span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="update_txtProductSalesPrice" name="update_txtProductSalesPrice" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="update_txtProductDescription" class="form-label">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                            <textarea class="form-control" id="update_txtProductDescription"
                                name="update_txtProductDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card shadow-sm border-light">
                            <div class="card-body d-flex justify-content-between align-items-center p-4">
                                <!-- Columna de Texto -->
                                <div class="me-3">
                                    <label class="form-check-label fw-bold text-dark d-block mb-1"
                                        for="update_chkProductStatus">
                                        Mostrar en el catálogo
                                    </label>
                                    <small class="text-muted">
                                        El producto será visible en el catálogo de ventas si esta opción está activada.
                                    </small>
                                </div>
                                <div class="form-check form-switch fs-2 m-0">
                                    <!-- Agregamos la clase 'switch-success' solo para el color verde -->
                                    <input class="form-check-input switch-success" type="checkbox" role="switch"
                                        id="update_chkProductStatus" name="update_chkProductStatus">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Gestionar categorías -->
<div class="modal fade" id="modalCategory" aria-labelledby="modalCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-dark border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-collection fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCategoryLabel">Categorías</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras gestionar tus categorias de tus
                            productos</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body">
                <?php
                $category = validate_permission_app(10, "c", false)['create'];
                if ($category == 1): ?>
                    <div class="mb-4 p-3 bg-light border rounded-3 position-relative">
                        <h6 class="fw-semibold mb-3 text-dark"><i class="bi bi-plus-circle text-info me-1"></i> Registrar nueva categoría</h6>
                        <form id="formCreateCategory" autocomplete="off">
                            <label for="txtCategoryName" class="visually-hidden">Nombre de la categoría</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-collection text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="txtCategoryName" name="txtCategoryName"
                                    maxlength="255" required placeholder="Ej. Bebidas calientes">
                                <button class="btn btn-info text-white px-4 fw-medium" type="submit">
                                    <i class="bi bi-check2-circle d-none d-sm-inline-block me-1"></i> Registrar
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                <div>
                    <h6 class="fw-semibold">Categorías registradas</h6>
                    <div class="bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped display nowrap w-100" id="tableCategorys">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>Nombre de la categoría</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-2">No hay categorías registradas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Generar códigos de barras masivos -->
<div class="modal fade" id="modalGenerateAllBarcodes" tabindex="-1" aria-labelledby="modalGenerateAllBarcodesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-success text-white border-bottom-0 py-2">
                <div class="d-flex align-items-center gap-3 w-100 m-0 p-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-upc-scan fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalGenerateAllBarcodesLabel">Generar códigos de barras masivos</h5>
                        <p class="mb-0 small text-dark text-opacity-75">Aqui podras generar códigos de barras masivos para tus productos</p>
                    </div>
                    <button type="button" class="btn-close ms-auto bg-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body bg-light p-3">
                <!-- Formulario: seleccionar producto y cantidad a imprimir -->
                <div class="bg-white border rounded-3 p-3 mb-3 shadow-sm">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        Seleccione un producto, indique cuántas etiquetas necesita y presiónne <strong>Agregar</strong>. Repita el proceso para agregar más productos a la lista de impresión.
                    </p>
                    <div class="row g-3 align-items-end">
                        <!-- Selector de producto -->
                        <div class="col-8 col-md-12 col-lg-6 col-xl-8">
                            <label for="print_slctProduct" class="form-label fw-medium">
                                Producto con codigo de barras <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-box-seam text-muted"></i>
                                </span>
                                <select name="print_slctProduct" id="print_slctProduct" class="form-select border-start-0">
                                    <option value="" disabled selected>Seleccione un producto</option>
                                </select>
                            </div>
                        </div>
                        <!-- Cantidad de etiquetas -->
                        <div class="col-4 col-md-6 col-lg-3 col-xl-2">
                            <label for="print_txtQuantity" class="form-label fw-medium">
                                Etiquetas <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-hash text-muted"></i>
                                </span>
                                <input type="number" class="form-control border-start-0" id="print_txtQuantity" name="print_txtQuantity" min="1" max="1000" value="1">
                            </div>
                        </div>
                        <!-- Botón agregar -->
                        <div class="col-12 col-md-6 col-lg-3 col-xl-2 d-flex">
                            <button type="button" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" id="print_btnAddProduct">
                                <i class="bi bi-plus-lg"></i>
                                <span>Agregar</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Lista de productos a imprimir -->
                <div class="bg-white border rounded-3 shadow-sm">
                    <div class="border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
                        <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing: 0.05em;">
                            <i class="bi bi-list-ul me-1"></i> Productos en cola de impresión
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" id="badgeBarcodeCount">0 productos</span>
                    </div>
                    <!-- Estado vacío -->
                    <div id="barcodeEmptyResult" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                        <p class="mb-0 small">Aún no ha agregado ningún producto. Use el formulario de arriba para agregar productos a la lista.</p>
                    </div>
                    <!-- Tabla de productos agregados -->
                    <div id="barcodeResultContainer" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="tablePrintQueue">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Producto</th>
                                        <th class="text-center" style="width: 130px;">Etiquetas a imprimir</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPrintQueue">
                                    <!-- COMPONENTE: fila de producto en cola de impresión -->
                                    <tr data-product-id="1">
                                        <!-- Nombre del producto -->
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:32px; height:32px;">
                                                    <i class="bi bi-box-seam" style="font-size:0.8rem;"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-medium text-dark small">Café molido premium</div>
                                                    <div class="text-muted" style="font-size:0.72rem;">
                                                        <span class="font-monospace">123456789012</span>
                                                        &middot;
                                                        <span>EAN-13</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Cantidad de etiquetas a imprimir -->
                                        <td class="text-center">
                                            <input type="number"
                                                class="form-control form-control-sm text-center mx-auto"
                                                value="2" min="1" max="1000"
                                                style="width:80px;">
                                        </td>
                                        <!-- Acción: quitar de la lista -->
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger border-0"
                                                title="Quitar producto">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Segunda fila de ejemplo -->
                                    <tr data-product-id="2">
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:32px; height:32px;">
                                                    <i class="bi bi-box-seam" style="font-size:0.8rem;"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-medium text-dark small">Leche entera 1L</div>
                                                    <div class="text-muted" style="font-size:0.72rem;">
                                                        <span class="font-monospace">7501234567890</span>
                                                        &middot;
                                                        <span>EAN-13</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number"
                                                class="form-control form-control-sm text-center mx-auto"
                                                value="5" min="1" max="1000"
                                                style="width:80px;">
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger border-0"
                                                title="Quitar producto">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ===== FOOTER CON ACCIONES ===== -->
            <div class="modal-footer bg-white border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger" id="btnPrintBarcodes" title="Imprimir códigos de barras">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-success" id="btnDownloadBarcodesPdf" title="Descargar PDF con los códigos de barras">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>