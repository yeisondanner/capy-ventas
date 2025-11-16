<?= headerPos($data) ?>
<?php
// Datos de ejemplo para la canasta (20 registros)
$basketTemplates = [
    ["nombre" => "Audífonos Bluetooth", "stock" => -24, "precio" => 89.90, "cantidad" => 1],
    ["nombre" => "Mouse Gamer RGB", "stock" => -1, "precio" => 59.90, "cantidad" => 2],
    ["nombre" => "Teclado mecánico", "stock" => 15, "precio" => 199.00, "cantidad" => 1],
    ["nombre" => "Parlante portátil", "stock" => 4, "precio" => 129.00, "cantidad" => 1],
    ["nombre" => "Silla ergonómica", "stock" => 9, "precio" => 799.00, "cantidad" => 1],
    ["nombre" => "Mousepad XL", "stock" => 37, "precio" => 49.90, "cantidad" => 3],
    ["nombre" => "Base para laptop", "stock" => 6, "precio" => 119.00, "cantidad" => 1],
    ["nombre" => "USB 128GB", "stock" => 28, "precio" => 79.90, "cantidad" => 2],
    ["nombre" => "Hub USB-C", "stock" => 12, "precio" => 159.00, "cantidad" => 1],
    ["nombre" => "Cámara web", "stock" => 5, "precio" => 249.00, "cantidad" => 1],
];

$basketDemo = [];
for ($i = 0; $i < 20; $i++) {
    $base = $basketTemplates[$i % count($basketTemplates)];
    $basketDemo[] = [
        "nombre" => $base["nombre"] . " Lote " . ($i + 1),
        "stock" => $base["stock"] - ($i % 3),
        "precio" => $base["precio"],
        "cantidad" => $base["cantidad"],
    ];
}

/**
 * Devuelve la etiqueta de stock para la canasta.
 *
 * @param int $stock Nivel de stock o faltante para el producto.
 * @return string Texto listo para mostrarse en la vista.
 */
function formatBasketStockLabel(int $stock): string
{
    if ($stock < 0) {
        return $stock . " Disponibles";
    }

    if ($stock === 0) {
        return "Sin stock";
    }

    return $stock . " disponibles";
}

/**
 * Determina la clase de color para la etiqueta de stock.
 *
 * @param int $stock Nivel de stock o faltante para el producto.
 * @return string Clase de texto sugerida para el estado.
 */
function getBasketStockClass(int $stock): string
{
    if ($stock < 0) {
        return "text-danger";
    }

    if ($stock === 0) {
        return "text-warning";
    }

    return "text-muted";
}

$basketSubtotal = 0;
foreach ($basketDemo as $index => $item) {
    $total = $item["precio"] * $item["cantidad"];
    $basketDemo[$index]["total"] = $total;
    $basketSubtotal += $total;
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
    <div class="row g-2 p-0 pos-steps-row">
        <!-- PASO 1: Elegir producto -->
        <div class="col-12 col-lg-8 step-mobile" id="step1">
            <div class="card shadow-sm border-0 pos-step-card h-100">
                <div class="card-header bg-white">
                    <div class="pos-step-title">
                        <div class="pos-step-badge">1</div>
                        <span><i class="bi bi-box-seam me-2"></i> Elegir producto</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="pos-step-content">
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

                        <!-- Grid de productos. Cada card es grande/tocable y amigable para Samuel :) -->
                        <div class="row g-2" id="listProducts">

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

        <!-- Columna derecha (en PC): Canasta arriba y Pago abajo -->
        <div class="col-12 col-lg-4">
            <div class="row g-3 desktop-steps-stack">

                <!-- PASO 2: Canasta / Rectificar cantidades -->
                <div class="col-12 step-mobile desktop-step" id="step2">
                    <div class="card shadow-sm border-0 pos-step-card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div class="pos-step-title">
                                <div class="pos-step-badge bg-info">2</div>
                                <span><i class="bi bi-basket me-2"></i> Canasta</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Vaciar
                            </button>
                        </div>
                        <div class="card-body d-flex flex-column p-0">
                            <!-- Lista de productos en la canasta con scroll propio -->
                            <div class="basket-list basket-scroll">
                                <?php foreach ($basketDemo as $basketItem): ?>
                                    <div class="basket-item">
                                        <div class="basket-header">
                                            <div class="basket-info">
                                                <div class="basket-icon">
                                                    <i class="bi bi-bag"></i>
                                                </div>
                                                <div>
                                                    <div class="basket-name"><?= $basketItem["nombre"] ?></div>
                                                    <div class="basket-stock <?= getBasketStockClass((int) $basketItem["stock"]) ?>">
                                                        <?= formatBasketStockLabel((int) $basketItem["stock"]) ?>
                                                    </div>
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
                                                    <input type="number" class="form-control text-center" value="<?= $basketItem["cantidad"] ?>" min="0">
                                                    <button class="btn btn-outline-secondary"><i class="bi bi-plus"></i></button>
                                                </div>
                                            </div>
                                            <div class="basket-half">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="text" class="form-control text-end" value="<?= number_format($basketItem["precio"], 2) ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="basket-price-line text-muted mt-1">
                                            Precio por <span class="fw-semibold"><?= $basketItem["cantidad"] ?></span> unidades:
                                            <span class="fw-semibold">S/ <?= number_format($basketItem["total"], 2) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Subtotal visible en la parte inferior de la canasta -->
                            <div class="p-3 border-top mt-auto bg-white">
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

                                <!-- Navegación de escritorio entre canasta y pago -->
                                <div class="d-none d-lg-block mt-2">
                                    <button id="btnDesktopToStep3" class="btn btn-success w-100 btn-nav">
                                        Siguiente: Pago <i class="bi bi-arrow-right-circle ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Pago / Nueva venta -->
                <div class="col-12 step-mobile desktop-step desktop-hidden" id="step3">
                    <div class="card shadow-sm border-0 mb-3 pos-step-card h-100">
                        <div class="card-header bg-white">
                            <div class="pos-step-title">
                                <!-- Badge morado para diferenciar el paso 3 -->
                                <div class="pos-step-badge" style="background:#6f42c1;">3</div>
                                <span><i class="bi bi-cash-stack me-2"></i> Pago / Nueva venta</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="pos-step-content">
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

                            <!-- Botón grande de cobro y navegación para pantallas grandes -->
                            <div class="mt-3 d-none d-lg-flex gap-2">
                                <button id="btnDesktopBackToStep2" class="btn btn-outline-secondary w-50 btn-nav">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Canasta
                                </button>
                                <button class="btn btn-success w-50 btn-cobrar btn-nav">
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