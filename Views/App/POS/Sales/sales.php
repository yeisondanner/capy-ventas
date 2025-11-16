<?= headerPos($data) ?>
<?php
// Listado de productos demostrativos para poblar la vista.
$demoProducts = [
    [
        'emoji' => '🎧',
        'name' => 'Audífonos inalámbricos',
        'price' => 89.90,
        'stock' => 18,
    ],
    [
        'emoji' => '🖱️',
        'name' => 'Mouse ergonómico',
        'price' => 64.90,
        'stock' => 6,
    ],
    [
        'emoji' => '⌨️',
        'name' => 'Teclado mecánico',
        'price' => 219.00,
        'stock' => 14,
    ],
    [
        'emoji' => '🔊',
        'name' => 'Parlante bluetooth',
        'price' => 129.00,
        'stock' => 22,
    ],
    [
        'emoji' => '🔋',
        'name' => 'Power bank 20 000 mAh',
        'price' => 119.90,
        'stock' => 31,
    ],
    [
        'emoji' => '📱',
        'name' => 'Soporte para celular',
        'price' => 24.90,
        'stock' => 11,
    ],
    [
        'emoji' => '💡',
        'name' => 'Foco inteligente WiFi',
        'price' => 45.50,
        'stock' => 8,
    ],
    [
        'emoji' => '🖥️',
        'name' => 'Monitor 27" 144Hz',
        'price' => 1599.00,
        'stock' => 4,
    ],
    [
        'emoji' => '🎮',
        'name' => 'Control inalámbrico',
        'price' => 249.00,
        'stock' => 16,
    ],
    [
        'emoji' => '📸',
        'name' => 'Cámara web Full HD',
        'price' => 189.90,
        'stock' => 7,
    ],
    [
        'emoji' => '🧊',
        'name' => 'Cooler para laptop',
        'price' => 79.90,
        'stock' => 25,
    ],
    [
        'emoji' => '📦',
        'name' => 'Organizador de cables',
        'price' => 29.90,
        'stock' => 42,
    ],
];

$emojiPool = ['🧾', '🧮', '🛰️', '🛜', '💻', '🔌', '📀', '⌚', '🧰', '🧴', '⌛', '📚'];

while (count($demoProducts) < 50) {
    $index = count($demoProducts) + 1;
    $demoProducts[] = [
        'emoji' => $emojiPool[$index % count($emojiPool)],
        'name' => "Producto destacado {$index}",
        'price' => (float) number_format(21.90 + ($index * 3.15), 2, '.', ''),
        'stock' => ($index * 2) % 35,
    ];
}

$basketItems = [
    [
        'name' => 'Audífonos Bluetooth',
        'stock' => -24,
        'qty' => 1,
        'price' => 89.90,
    ],
    [
        'name' => 'Mouse Gamer RGB',
        'stock' => -1,
        'qty' => 2,
        'price' => 59.90,
    ],
    [
        'name' => 'Teclado mecánico',
        'stock' => 15,
        'qty' => 1,
        'price' => 199.00,
    ],
    [
        'name' => 'Memoria RAM 16GB',
        'stock' => 14,
        'qty' => 1,
        'price' => 310.00,
    ],
    [
        'name' => 'Router WiFi 6',
        'stock' => 6,
        'qty' => 1,
        'price' => 459.00,
    ],
    [
        'name' => 'Hub USB-C 6 en 1',
        'stock' => 12,
        'qty' => 1,
        'price' => 189.90,
    ],
    [
        'name' => 'Cámara de seguridad',
        'stock' => 3,
        'qty' => 1,
        'price' => 349.00,
    ],
    [
        'name' => 'Silla gamer',
        'stock' => 5,
        'qty' => 1,
        'price' => 920.00,
    ],
    [
        'name' => 'Laptop ultrabook',
        'stock' => 2,
        'qty' => 1,
        'price' => 5290.00,
    ],
    [
        'name' => 'Smartwatch deportivo',
        'stock' => 9,
        'qty' => 1,
        'price' => 780.00,
    ],
];

$basketSeed = 1;
while (count($basketItems) < 20) {
    $basketItems[] = [
        'name' => "Accesorio demo {$basketSeed}",
        'stock' => ($basketSeed % 4 === 0) ? 0 : (9 - $basketSeed),
        'qty' => ($basketSeed % 3) + 1,
        'price' => (float) number_format(35 + ($basketSeed * 6.75), 2, '.', ''),
    ];
    $basketSeed++;
}

$basketSubtotal = 0;
foreach ($basketItems as $item) {
    $basketSubtotal += $item['qty'] * $item['price'];
}
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-cart"></i> Ventas</h1>
            <p>Administra las ventas de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/sales">Ventas</a></li>
        </ul>
    </div>
    <div class="row g-2 p-0 align-items-stretch">
        <!-- PASO 1: Elegir producto -->
        <div class="col-12 col-lg-8 step-mobile active-step" id="step1">
            <div class="card shadow-sm border-0 pos-step-card">
                <div class="card-header bg-white">
                    <div class="pos-step-title">
                        <div class="pos-step-badge">1</div>
                        <span><i class="bi bi-box-seam me-2"></i> Elegir producto</span>
                    </div>
                </div>
                <div class="card-body step-card-body">
                    <!-- Buscador interno de productos dentro del paso 1 -->
                    <div class="mb-3">
                        <label class="form-label small mb-1">Buscar producto</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Escribe nombre o código del producto...">
                        </div>
                    </div>

                    <!-- Categorías rápidas (chips) de productos populares -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Categorías populares</small>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline_secondary btn-sm">Todos</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Accesorios PC</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Audio</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Almacenamiento</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Ofertas</button>
                        </div>
                    </div>

                    <!-- Grid de productos con scroll interno y 50 artículos de muestra -->
                    <div class="product-grid-scroll">
                        <div class="row g-2" id="productGrid">
                            <?php foreach ($demoProducts as $product): ?>
                                <div class="col-6 col-md-4 col-xl-3">
                                    <button class="product-card">
                                        <div class="product-thumb">
                                            <span class="emoji"><?= $product['emoji'] ?></span>
                                        </div>
                                        <div class="product-price text-dark">S/ <?= number_format($product['price'], 2) ?></div>
                                        <div class="product-name"><?= $product['name'] ?></div>
                                        <span class="badge product-stock-badge mt-1" data-stock="<?= $product['stock'] ?>">
                                            <i class="bi bi-info-circle"></i>
                                            <?= $product['stock'] ?> disponibles
                                        </span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Botón para pasar a la canasta en móvil -->
                    <div class="mt-3 d-grid d-lg-none">
                        <button id="btnToStep2" class="btn btn-primary btn-nav">
                            Siguiente: Canasta <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha (en PC): Canasta o Pago -->
        <div class="col-12 col-lg-4">
            <div class="row g-3">

                <!-- PASO 2: Canasta / Rectificar cantidades -->
                <div class="col-12 step-mobile desktop-step active-desktop" id="step2">
                    <div class="card shadow-sm border-0 pos-step-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div class="pos-step-title">
                                <div class="pos-step-badge">2</div>
                                <span><i class="bi bi-basket me-2"></i> Canasta</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Vaciar
                            </button>
                        </div>
                        <div class="card-body p-0 step-card-body">
                            <!-- Lista de productos en la canasta con scroll propio -->
                            <div class="basket-list basket-scroll">
                                <?php foreach ($basketItems as $item): ?>
                                    <?php
                                    $stockText = $item['stock'] . ' Disponibles';
                                    $stockClass = 'text-muted';
                                    if ($item['stock'] <= 0) {
                                        $stockClass = 'text-danger';
                                    } elseif ($item['stock'] <= 3) {
                                        $stockClass = 'text-warning';
                                    }
                                    $lineSubtotal = $item['qty'] * $item['price'];
                                    ?>
                                    <div class="basket-item">
                                        <div class="basket-header">
                                            <div class="basket-info">
                                                <div class="basket-icon">
                                                    <i class="bi bi-bag"></i>
                                                </div>
                                                <div>
                                                    <div class="basket-name"><?= $item['name'] ?></div>
                                                    <div class="basket-stock <?= $stockClass ?>"><?= $stockText ?></div>
                                                </div>
                                            </div>
                                            <button class="btn btn-outline-danger btn-sm rounded-circle">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Cantidad y precio mitad / mitad -->
                                        <div class="basket-controls">
                                            <div class="basket-half">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary"><i class="bi bi-dash"></i></button>
                                                    <input type="number" class="form-control text-center" value="<?= $item['qty'] ?>" min="0">
                                                    <button class="btn btn-outline-secondary"><i class="bi bi-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="basket-half">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="text" class="form-control text-end" value="<?= number_format($item['price'], 2) ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="basket-price-line text-muted mt-1">
                                            Precio por <span class="fw-semibold"><?= $item['qty'] ?></span> unidades:
                                            <span class="fw-semibold">S/ <?= number_format($lineSubtotal, 2) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Subtotal visible en la parte inferior de la canasta -->
                            <div class="p-3 border-top">
                                <div class="d-flex justify-content-between mb-2 fw-bold fs-5 totales-pos">
                                    <span>Subtotal</span>
                                    <span>S/ <?= number_format($basketSubtotal, 2) ?></span>
                                </div>

                                <!-- Navegación móvil compacta entre paso 1 y 3 -->
                                <div class="d-flex justify-content-between gap-2 d-lg-none mt-2">
                                    <button id="btnBackToStep1" class="btn btn-outline-secondary w-50 btn-nav btn-nav-small">
                                        <i class="bi bi-arrow-left-circle me-1"></i> Productos
                                    </button>
                                    <button id="btnToStep3" class="btn btn-success w-50 btn-nav btn-nav-small">
                                        Cobrar <i class="bi bi-arrow-right-circle ms-1"></i>
                                    </button>
                                </div>

                                <!-- Cambio de paso en escritorio -->
                                <div class="d-none d-lg-block mt-3">
                                    <button id="btnDesktopToStep3" class="btn btn-success w-100 btn-nav">
                                        Cobrar y pasar a pago <i class="bi bi-arrow-right-circle ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Pago / Nueva venta -->
                <div class="col-12 step-mobile desktop-step" id="step3">
                    <div class="card shadow-sm border-0 mb-3 pos-step-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div class="pos-step-title">
                                <!-- Badge morado para diferenciar el paso 3 -->
                                <div class="pos-step-badge" style="background:#6f42c1;">3</div>
                                <span><i class="bi bi-cash-stack me-2"></i> Pago / Nueva venta</span>
                            </div>
                            <button id="btnDesktopBackToStep2" class="btn btn-outline-secondary btn-sm d-none d-lg-inline-flex">
                                <i class="bi bi-arrow-left-circle me-1"></i> Canasta
                            </button>
                        </div>
                        <div class="card-body step-card-body">
                            <!-- Totales de la venta con descuento -->
                            <div class="d-flex justify-content-between mb-1 small">
                                <span>Subtotal</span>
                                <span id="lblSubtotal" data-valor="<?= number_format($basketSubtotal, 2, '.', '') ?>">S/ <?= number_format($basketSubtotal, 2) ?></span>
                            </div>

                            <!-- Bloque de descuento con monto y porcentaje sincronizados -->
                            <div class="d-flex justify-content-between mb-1 small align-items-start">
                                <span>Descuento</span>
                                <div class="descuento-wrap">
                                    <div class="small text-muted w-100 text-end mb-1">
                                        Monto o porcentaje, se calculan juntos
                                    </div>
                                    <!-- Descuento en monto fijo -->
                                    <div class="input-group input-group-sm descuento-group">
                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="number"
                                            class="form-control text-end"
                                            id="descuentoMonto"
                                            value="0"
                                            min="0"
                                            step="0.10"
                                            placeholder="Monto">
                                    </div>
                                    <!-- Descuento en porcentaje -->
                                    <div class="input-group input-group-sm descuento-group">
                                        <input
                                            type="number"
                                            class="form-control text-end"
                                            id="descuentoPorc"
                                            value="0"
                                            min="0"
                                            step="0.10"
                                            placeholder="%">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total final luego del descuento -->
                            <div class="d-flex justify-content-between mb-3 fw-bold fs-5 totales-pos">
                                <span>Total a pagar</span>
                                <span id="lblTotal">S/ <?= number_format($basketSubtotal, 2) ?></span>
                            </div>

                            <!-- Datos básicos de la venta -->
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label form-label-sm mb-1 small">Fecha de venta</label>
                                    <input type="date" id="fechaVenta" class="form-control form-control-sm">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label form-label-sm mb-1 small">Medio de pago</label>
                                    <select class="form-select form-select-sm">
                                        <option>Efectivo</option>
                                        <option>Tarjeta</option>
                                        <option>Yape/Plin</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label form-label-sm mb-1 small">Cliente</label>
                                    <select class="form-select form-select-sm">
                                        <option value="" selected>Sin cliente</option>
                                        <option value="cliente1">Juan Pérez</option>
                                        <option value="cliente2">María López</option>
                                        <option value="cliente3">Cliente frecuente</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Navegación móvil debajo del formulario de pago -->
                            <div class="mt-3 d-lg-none d-flex justify-content-between gap-2">
                                <button id="btnBackToStep2" class="btn btn-outline-secondary w-50 btn-nav btn-nav-small">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Canasta
                                </button>
                                <!-- Botón de cobrar en móvil -->
                                <button class="btn btn-success w-50 btn-cobrar btn-nav">
                                    <i class="bi bi-cash-stack me-1"></i> Cobrar
                                </button>
                            </div>

                            <!-- Botón grande de cobro para pantallas grandes -->
                            <div class="mt-3 d-none d-lg-block">
                                <button class="btn btn-success w-100 btn-cobrar btn-nav">
                                    <i class="bi bi-cash-stack me-1"></i> Cobrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
<?= footerPos($data) ?>
<!-- Modal de cobro: se abre cuando se presiona "Cobrar" -->
<div class="modal fade" id="modalCobro" tabindex="-1" aria-labelledby="modalCobroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCobroLabel">
                    <i class="bi bi-cash-stack me-2"></i> Confirmar cobro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Total que viene del paso 3 -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Total a pagar</span>
                        <span class="fw-bold fs-5">S/ <span id="modalTotal">0.00</span></span>
                    </div>
                </div>

                <!-- Monto con el que paga el cliente -->
                <div class="mb-3">
                    <label class="form-label form-label-sm small">Con cuánto está pagando</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">S/</span>
                        <input type="number" class="form-control text-end" id="montoPaga" min="0" step="0.10" placeholder="0.00">
                    </div>
                </div>

                <!-- Cálculo del vuelto -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between small">
                        <span>Vuelto</span>
                        <span class="fw-bold text-success">S/ <span id="montoVuelto">0.00</span></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <!-- Al confirmar, se cierra este modal y se abre el de resumen/voucher -->
                <button type="button" class="btn btn-success" id="btnFinalizarVenta">
                    <i class="bi bi-check-circle me-1"></i> Finalizar venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal post-venta: resumen tipo voucher y opciones de comprobante -->
<div class="modal fade" id="modalPostVenta" tabindex="-1" aria-labelledby="modalPostVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Encabezado visual tipo comprobante/voucher -->
                <h5 class="modal-title w-100 text-center" id="modalPostVentaLabel">
                    <div class="mb-2">
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Venta completada
                        </span>
                    </div>
                    <div class="fw-semibold text-dark mt-1">Comprobante de pago</div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">¿Quieres darle un nombre a esta venta?</p>

                <!-- Nombre opcional para identificar la venta luego -->
                <div class="mb-3">
                    <label class="form-label form-label-sm small">Nombre de la venta (opcional)</label>
                    <input type="text" class="form-control form-control-sm" id="nombreVenta" placeholder="Ej. Venta Samuel - Audífonos">
                </div>

                <!-- Total resumido de la venta -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center small voucher-total">
                        <span class="text-muted">Total de la venta</span>
                        <span class="fw-bold fs-5">S/ <span id="resumenTotalVenta">0.00</span></span>
                    </div>
                </div>

                <hr class="my-2">
                <p class="small text-muted mb-2">Opciones de comprobante / voucher</p>

                <!-- Acciones posibles luego de finalizar la venta -->
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Descargar PDF
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope me-1"></i> Correo
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>