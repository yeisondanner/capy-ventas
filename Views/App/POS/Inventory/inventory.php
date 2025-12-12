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
            <div class="tile">
                <div class="tile-body d-flex flex-wrap gap-2">
                    <?php
                    $inventory = (int) (validate_permission_app(3, "c", false)) ? (int) validate_permission_app(3, "c", false)['create'] : 0;
                    if ($inventory === 1): ?>
                        <button class="btn btn-primary" type="button" id="btnOpenProductModal">
                            <i class="bi bi-plus-lg"></i> Agregar nuevo producto
                        </button>
                    <?php endif; ?>
                    <?php
                    $category = (int) (validate_permission_app(10, "r", false)) ? (int)validate_permission_app(10, "r", false)['read'] : 0;
                    if ($category === 1): ?>
                        <button class="btn btn-outline-info" type="button" id="btnOpenCategoryModal">
                            <i class="bi bi-collection"></i> Categorías
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered table-striped" id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                    <th>Stock (Unidad)</th>
                                    <th>Precio venta</th>
                                    <th>Precio compra</th>
                                    <th>Ganancia</th>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtProductName" name="txtProductName" maxlength="255"
                            required placeholder="Ej. Café molido premium">
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductCategory" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductCategory" name="txtProductCategory" required>
                            <option value="" selected disabled>Selecciona una categoría</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductSupplier" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductSupplier" name="txtProductSupplier" required>
                            <option value="" selected disabled>Selecciona un proveedor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="txtProductMeasurement" class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                        <select class="form-select" id="txtProductMeasurement" name="txtProductMeasurement" required>
                            <option value="" selected disabled>Selecciona una unidad</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductStock" class="form-label">Stock (opcional)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductStock"
                            name="txtProductStock" placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductPurchasePrice" class="form-label">Precio compra <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductPurchasePrice"
                            name="txtProductPurchasePrice" required placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label for="txtProductSalesPrice" class="form-label">Precio venta <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="txtProductSalesPrice"
                            name="txtProductSalesPrice" required placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label for="txtProductDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="txtProductDescription" name="txtProductDescription"
                            rows="3" placeholder="Describe las características principales del producto"></textarea>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalProductReportLabel">Reporte del producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h4 class="mb-0" id="reportProductName">Nombre del producto</h4>
                    <small class="text-muted" id="reportProductStatus">Estado</small>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Categoría</dt>
                    <dd class="col-sm-8" id="reportProductCategory">-</dd>
                    <dt class="col-sm-4">Proveedor</dt>
                    <dd class="col-sm-8" id="reportProductSupplier">-</dd>
                    <dt class="col-sm-4">Unidad de medida</dt>
                    <dd class="col-sm-8" id="reportProductMeasurement">-</dd>
                    <dt class="col-sm-4">Stock disponible</dt>
                    <dd class="col-sm-8" id="reportProductStock">-</dd>
                    <dt class="col-sm-4">Precio de compra</dt>
                    <dd class="col-sm-8" id="reportProductPurchase">-</dd>
                    <dt class="col-sm-4">Precio de venta</dt>
                    <dd class="col-sm-8" id="reportProductSale">-</dd>
                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8" id="reportProductDescription">Sin descripción registrada.</dd>
                </dl>
            </div>
            <div class="modal-footer">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fw-semibold">Registrar nueva categoría</h6>
                    <form class="row g-2 align-items-center" id="formCreateCategory" autocomplete="off">
                        <?= csrf(); ?>
                        <div class="col-sm-8 col-md-9">
                            <label for="txtCategoryName" class="visually-hidden">Nombre de la categoría</label>
                            <input type="text" class="form-control" id="txtCategoryName" name="txtCategoryName" maxlength="255"
                                required placeholder="Ej. Bebidas calientes">
                        </div>
                        <div class="col-sm-4 col-md-3 d-grid">
                            <button class="btn btn-info text-white" type="submit">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= csrf(); ?>
                <input type="hidden" name="update_txtProductId" id="update_txtProductId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="update_txtProductName" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="update_txtProductName" name="update_txtProductName"
                            maxlength="255" required>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductCategory" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtProductCategory" name="update_txtProductCategory" required>
                            <option value="" selected disabled>Selecciona una categoría</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductSupplier" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtProductSupplier" name="update_txtProductSupplier" required>
                            <option value="" selected disabled>Selecciona un proveedor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductMeasurement" class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtProductMeasurement" name="update_txtProductMeasurement" required>
                            <option value="" selected disabled>Selecciona una unidad</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="update_txtProductStatus" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="update_txtProductStatus" name="update_txtProductStatus" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductStock" class="form-label">Stock (opcional)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductStock"
                            name="update_txtProductStock">
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductPurchasePrice" class="form-label">Precio compra <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductPurchasePrice"
                            name="update_txtProductPurchasePrice" required>
                    </div>
                    <div class="col-md-4">
                        <label for="update_txtProductSalesPrice" class="form-label">Precio venta <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="update_txtProductSalesPrice"
                            name="update_txtProductSalesPrice" required>
                    </div>
                    <div class="col-12">
                        <label for="update_txtProductDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="update_txtProductDescription" name="update_txtProductDescription"
                            rows="3"></textarea>
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