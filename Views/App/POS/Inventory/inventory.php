<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-box-seam"></i> Inventario</h1>
            <p>Administra los productos de tu negocio: registra nuevas referencias, actualiza precios y controla el stock</p>
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
                    $inventory = (int) (validate_permission_app(3, "c", false)) ? (int) validate_permission_app(3, "c", false)['create'] : 0;
                    if ($inventory === 1): ?>
                        <button class="btn btn-outline-primary btn-sm" type="button" id="btnOpenProductModal">
                            <i class="bi bi-plus-lg"></i> Agregar nuevo producto
                        </button>
                    <?php endif; ?>
                    <?php
                    $category = (int) (validate_permission_app(10, "r", false)) ? (int)validate_permission_app(10, "r", false)['read'] : 0;
                    if ($category === 1): ?>
                        <button class="btn btn-outline-info btn-sm" type="button" id="btnOpenCategoryModal">
                            <i class="bi bi-collection"></i> Categorías
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive" id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProductLabel">Registrar producto</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-12 d-flex justify-content-center">
                        <div class="card-body p-4">
                            <div class="logo-upload-area mb-3" onclick="document.getElementById('flInput').click()">
                                <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png" id="logoPreview" class="logo-preview-img mb-2" alt="Logo">
                                <div class="text-primary fw-medium small"><i class="bi bi-cloud-upload me-1"></i> Subir foto del producto</div>
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">Click para subir (Max 2MB)</div>
                            </div>
                            <input type="file" class="d-none" id="flInput" name="flInput" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <input type="text" class="form-control" id="txtProductName" name="txtProductName" maxlength="255"
                                required placeholder="Ej. Café molido premium">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductCategory" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-collection"></i> </span>
                            <select class="form-select" id="txtProductCategory" name="txtProductCategory" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductSupplier" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select" id="txtProductSupplier" name="txtProductSupplier" required>
                                <option value="" selected disabled>Selecciona un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductMeasurement" class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <select class="form-select" id="txtProductMeasurement" name="txtProductMeasurement" required>
                                <option value="" selected disabled>Selecciona una unidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductStock" class="form-label">Stock (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-numeric-up-alt"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="txtProductStock"
                                name="txtProductStock" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductPurchasePrice" class="form-label">Precio compra <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="txtProductPurchasePrice"
                                name="txtProductPurchasePrice" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductSalesPrice" class="form-label">Precio venta <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="txtProductSalesPrice"
                                name="txtProductSalesPrice" required placeholder="0.00">
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
                                    <label class="form-check-label fw-bold text-dark d-block mb-1" for="chkProductStatus">
                                        Mostrar en el catálogo
                                    </label>
                                    <small class="text-muted">
                                        El producto será visible en el catálogo de ventas si esta opción está activada.
                                    </small>
                                </div>
                                <div class="form-check form-switch fs-2 m-0">
                                    <!-- Agregamos la clase 'switch-success' solo para el color verde -->
                                    <input class="form-check-input switch-success" type="checkbox" role="switch" id="chkProductStatus" name="chkProductStatus">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reporte de producto -->
<div class="modal fade" id="modalProductReport" tabindex="-1" aria-labelledby="modalProductReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">

            <!-- Encabezado -->
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalProductReportLabel">
                    <i class="bi bi-file-earmark-text me-2"></i> Reporte del producto
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body bg-light p-4">
                <div class="row g-3">

                    <!-- Columna Izquierda: Imagen y Datos Básicos -->
                    <div class="col-md-5">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <!-- Contenedor con proporción fija 4:3 para la imagen principal -->
                            <div class="ratio ratio-4x3 mb-3">
                                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=600&auto=format&fit=crop"
                                    class="rounded border object-fit-cover" id="reportImageMain" loading="lazy"
                                    alt="Producto Principal">
                            </div>

                            <div class="mb-3 border-bottom pb-2">
                                <h4 class="fw-bold mb-0 text-dark" id="reportProductName">Nombre del producto</h4>
                                <span class="badge bg-info text-dark mt-1" id="reportProductStatus">Estado</span>
                            </div>

                            <div class="small">
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small" style="font-size: 0.7rem;">Categoría</label>
                                    <span id="reportProductCategory">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small" style="font-size: 0.7rem;">Proveedor</label>
                                    <span id="reportProductSupplier">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small" style="font-size: 0.7rem;">Unidad de medida</label>
                                    <span id="reportProductMeasurement">-</span>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted fw-bold d-block text-uppercase small" style="font-size: 0.7rem;">Es Publico</label>
                                    <span id="reportProductIsPublic">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Métricas, Galería y Descripción -->
                    <div class="col-md-7">
                        <!-- Métricas -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="p-2 border rounded bg-white text-center">
                                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem;">Stock</small>
                                    <span class="h6 mb-0 fw-bold text-primary" id="reportProductStock">0</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-white text-center">
                                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem;">Compra</small>
                                    <span class="h6 mb-0 fw-bold text-dark" id="reportProductPurchase">$0.00</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-white text-center">
                                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem;">Venta</small>
                                    <span class="h6 mb-0 fw-bold text-success" id="reportProductSale">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Galería Visual con Proporción 1:1 (Cuadrada) -->
                        <div class="bg-white p-3 border rounded shadow-sm mb-3">
                            <label class="text-muted fw-bold d-block text-uppercase small mb-2" style="font-size: 0.7rem;">Galería de Fotos</label>
                            <div class="row g-2 overflow-y-auto" style="max-height: 200px;" id="listReportImages">
                                --
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="bg-white p-3 border rounded shadow-sm">
                            <label class="text-muted fw-bold d-block text-uppercase small mb-1 border-bottom pb-1" style="font-size: 0.7rem;">Descripción</label>
                            <p class="mb-0 small text-secondary" id="reportProductDescription">Sin descripción registrada.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gestionar categorías -->
<div class="modal fade" id="modalCategory" aria-labelledby="modalCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalCategoryLabel">Categorías</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fw-semibold">Registrar nueva categoría</h6>
                    <form class="row g-2 align-items-center" id="formCreateCategory" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="col-sm-8 col-md-9">
                            <label for="txtCategoryName" class="visually-hidden">Nombre de la categoría</label>
                            <input type="text" class="form-control form-control-sm" id="txtCategoryName" name="txtCategoryName" maxlength="255"
                                required placeholder="Ej. Bebidas calientes">
                        </div>
                        <div class="col-sm-4 col-md-3 d-grid">
                            <button class="btn btn-sm btn-outline-info" type="submit">
                                <i class="bi bi-plus-lg"></i> Registrar
                            </button>
                        </div>
                    </form>
                </div>
                <div>
                    <h6 class="fw-semibold">Categorías registradas</h6>
                    <ul class="list-group" id="categoryList">
                        <li class="list-group-item text-center text-muted">No hay categorías registradas.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Actualizar producto -->
<div class="modal fade" id="modalUpdateProduct" tabindex="-1" aria-labelledby="modalUpdateProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="formUpdateProduct" autocomplete="off">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateProductLabel">Actualizar producto</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_txtProductId" id="update_txtProductId">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card-body p-4">
                            <div class="logo-upload-area mb-3" onclick="document.getElementById('update_flInput').click()">
                                <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png" id="update_logoPreview" class="logo-preview-img mb-2" alt="Logo">
                                <div class="text-primary fw-medium small"><i class="bi bi-cloud-upload me-1"></i> Subir foto del producto</div>
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">Click para subir (Max 2MB)</div>
                            </div>
                            <input type="file" class="d-none" id="update_flInput" name="update_flInput" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="accordion accordion-flush border rounded-3 bg-light" id="listAccordionImages">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#listImages" aria-expanded="false" aria-controls="listImages">
                                        <i class="bi bi-images me-2"></i> Imagenes del producto
                                    </button>
                                </h2>
                                <div id="listImages" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#listAccordionImages">
                                    <div class="accordion-body overflow-y-auto" style="max-height: 250px;">
                                        <div class="row" id="listImagesContainer">
                                            <div class="col-4 p-2">
                                                <div class=" border rounded-3 bg-light position-relative">
                                                    <img src="<?= base_url(); ?>/Loadfile/iconproducts?f=product.png" class="img-fluid" alt="" loading="lazy">
                                                    <button type="button" class="btn btn-secondary btn-sm position-absolute top-0 end-0"><i class="bi bi-x-lg"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <input type="text" class="form-control" id="update_txtProductName" name="update_txtProductName"
                                maxlength="255" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductCategory" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-collection"></i></span>
                            <select class="form-select" id="update_txtProductCategory" name="update_txtProductCategory" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductSupplier" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select" id="update_txtProductSupplier" name="update_txtProductSupplier" required>
                                <option value="" selected disabled>Selecciona un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductMeasurement" class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <select class="form-select" id="update_txtProductMeasurement" name="update_txtProductMeasurement" required>
                                <option value="" selected disabled>Selecciona una unidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductStock" class="form-label">Stock (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-numeric-up-alt"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductStock"
                                name="update_txtProductStock">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductPurchasePrice" class="form-label">Precio compra <?= getCurrency() ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductPurchasePrice"
                                name="update_txtProductPurchasePrice" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductSalesPrice" class="form-label">Precio venta <?= getCurrency() ?><span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductSalesPrice"
                                name="update_txtProductSalesPrice" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="update_txtProductDescription" class="form-label">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                            <textarea class="form-control" id="update_txtProductDescription" name="update_txtProductDescription"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card shadow-sm border-light">
                            <div class="card-body d-flex justify-content-between align-items-center p-4">
                                <!-- Columna de Texto -->
                                <div class="me-3">
                                    <label class="form-check-label fw-bold text-dark d-block mb-1" for="update_chkProductStatus">
                                        Mostrar en el catálogo
                                    </label>
                                    <small class="text-muted">
                                        El producto será visible en el catálogo de ventas si esta opción está activada.
                                    </small>
                                </div>
                                <div class="form-check form-switch fs-2 m-0">
                                    <!-- Agregamos la clase 'switch-success' solo para el color verde -->
                                    <input class="form-check-input switch-success" type="checkbox" role="switch" id="update_chkProductStatus" name="update_chkProductStatus">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>