<?= headerPos($data) ?>
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
                                <input type="text" class="form-control" id="productSearchInput" placeholder="Escribe nombre, proveedor o categoría...">
                            </div>
                        </div>

                        <!-- Categorías rápidas (chips) de productos populares -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Categorías populares</small>
                            <div class="d-flex flex-wrap gap-2" id="popularCategories"></div>
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
                            <button class="btn btn-sm btn-outline-danger" id="btnEmptyCart">
                                <i class="bi bi-trash me-1"></i> Vaciar
                            </button>
                        </div>
                        <div class="card-body d-flex flex-column p-0">
                            <!-- Lista de productos en la canasta con scroll propio -->
                            <div class="basket-list basket-scroll" id="listCart">
                            </div>

                            <!-- Subtotal visible en la parte inferior de la canasta -->
                            <div class="p-3 border-top mt-auto bg-white">
                                <div class="d-flex justify-content-between mb-2 fw-bold fs-5 totales-pos">
                                    <span>Subtotal</span>
                                    <span id="basketSubtotal">S/ 0.00</span>
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
                                    <span id="lblSubtotal" data-valor="0.00">S/ 0.00</span>
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
                                        <input type="date" id="fechaVenta" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label form-label-sm mb-1 small">Medio de pago</label>
                                        <select class="form-select" id="paymentMethod">
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm mb-1 small">Cliente</label>
                                        <select class="form-select" id="customerSelect">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegación móvil debajo del formulario de pago -->
                            <div class="mt-3 d-lg-none d-flex justify-content-between gap-2">
                                <button id="btnBackToStep2" class="btn btn-outline-secondary w-50 btn-nav btn-nav-small">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Atras: Canasta
                                </button>
                                <!-- Botón de cobrar en móvil -->
                                <button class="btn btn-success w-50 btn-cobrar btn-nav">
                                    <i class="bi bi-cash-stack me-1"></i> Cobrar
                                </button>
                            </div>

                            <!-- Botón grande de cobro y navegación para pantallas grandes -->
                            <div class="mt-3 d-none d-lg-flex gap-2">
                                <button id="btnDesktopBackToStep2" class="btn btn-outline-secondary w-50 btn-nav">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Atras:Canasta
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
                    <label class="form-label small">Con cuánto está pagando</label>
                    <div class="input-group">
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
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="nombreVenta" placeholder="Ej. Venta Samuel - Audífonos">
                        <button class="btn btn-primary" type="button" id="btnGuardarNombreVenta">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
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

<?= footerPos($data) ?>