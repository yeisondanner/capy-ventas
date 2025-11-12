<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-box-seam"></i> Inventario</h1>
            <p>Aquí puedes administrar el inventario de tus productos. Agrega, edita o elimina productos, y mantén un control de las existencias.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/inventory"> </a> Inventario</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Agregar nuevo producto</button>
                    <button class="btn btn-info text-white"><i class="bi bi-collection"></i> Categorias</button>
                    <button class="btn btn-warning text-white"><i class="bi bi-people"></i> Proveedores</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Proveedor</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Costo</th>
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