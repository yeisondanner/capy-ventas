<?php
headerPos($data); ?>
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
        <div class="col-12 row justify-content-end p-0">
            <div class="col-12 col-md-4">
                <div class="btn-group w-100 p-0">
                    <button class="btn btn-primary btn-nav" data-header="0" id="btnOpenModalMovement">
                        <i class="bi bi-rocket"></i> Venta rápida <i class="bi bi-cash-coin ms-1"></i>
                    </button>
                    <button class="btn btn-danger btn-nav" id="" onclick="showAlert({
                        icon: 'info',
                        title: 'Información',
                        message: 'Funcionalidad en desarrollo',
                        position: 'bottom',
                    }, 'float')">
                        <i class="bi bi-trash"></i> Gasto <i class="bi bi-cash-coin ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
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
                                <input type="text" class="form-control" id="productSearchInput"
                                    placeholder="Escribe nombre, proveedor o categoría...">
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
                                    <button id="btnBackToStep1"
                                        class="btn btn-outline-secondary w-50 btn-nav btn-nav-small">
                                        <i class="bi bi-arrow-left-circle me-1"></i> Productos
                                    </button>
                                    <button id="btnToStep3" class="btn btn-success w-50 btn-nav btn-nav">
                                        Pagar <i class="bi bi-arrow-right-circle ms-1"></i>
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
                                            <input type="number" class="form-control text-end" id="descuentoMonto"
                                                value="0" min="0" step="0.10" placeholder="Monto">
                                        </div>
                                        <!-- Descuento en porcentaje -->
                                        <div class="input-group input-group-sm descuento-group">
                                            <input type="number" class="form-control text-end" id="descuentoPorc"
                                                value="0" min="0" step="0.10" placeholder="%">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="" class="form-label form-label-sm mb-1 small">Impuesto</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><?= $data['taxname'] ?></span>
                                        <input type="number" class="form-control text-end " disabled id="tax" min="0"
                                            step="0.10" placeholder="<?= $data['tax'] ?>" value="<?= $data['tax'] ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <!-- Total final luego del descuento -->
                                <div
                                    class="d-flex justify-content-between mb-3 fw-bold fs-5 totales-pos border border-2 border-success px-2 py-1 bg-dark bg-opacity-10 rounded-2">
                                    <span>Total a pagar</span>
                                    <span id="lblTotal">S/ <?= number_format($basketSubtotal, 2) ?></span>
                                </div>
                                <!-- Datos básicos de la venta -->
                                <div class="row g-2 align-items-end">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm mb-1 small d-block">Tipo de pago</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="tipoPago" id="pagoContado" value="Contado" checked autocomplete="off">
                                            <label class="btn btn-outline-success" for="pagoContado">
                                                <i class="bi bi-cash-stack me-1"></i> Contado
                                            </label>

                                            <input type="radio" class="btn-check" name="tipoPago" id="pagoCredito" value="Credito" autocomplete="off">
                                            <label class="btn btn-outline-danger" for="pagoCredito">
                                                <i class="bi bi-credit-card me-1"></i> Crédito
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label form-label-sm mb-1 small">Fecha de venta</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="date" id="fechaVenta" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label form-label-sm mb-1 small">Medio de pago</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                                            <select class="form-select" id="paymentMethod">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm mb-1 small">Cliente</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <select class="form-select" id="customerSelect">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegación móvil debajo del formulario de pago -->
                            <div class="mt-3 d-lg-none d-flex justify-content-between gap-2">
                                <button id="btnBackToStep2"
                                    class="btn btn-outline-secondary w-50 btn-nav btn-nav-small">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Canasta
                                </button>
                                <!-- Botón de cobrar en móvil -->
                                <button class="btn btn-success w-50 btn-cobrar btn-nav">
                                    <i class="bi bi-cash-stack me-1"></i> Cobrar
                                </button>
                            </div>

                            <!-- Botón grande de cobro y navegación para pantallas grandes -->
                            <div class="mt-3 d-none d-lg-flex justify-content-between gap-2">
                                <button id="btnDesktopBackToStep2" class="btn btn-outline-secondary btn-nav">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Atras:Canasta
                                </button>
                                <button class="btn btn-success btn-cobrar btn-nav">
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
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="modalCobroLabel">
                    <i class="bi bi-cash-stack me-2"></i> Confirmar cobro
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                <div class="mb-3" id="divMontoPaga">
                    <label class="form-label small fw-semibold">Con cuánto está pagando</label>
                    <div class="input-group">
                        <span class="input-group-text">S/</span>
                        <input type="number" class="form-control text-end" id="montoPaga" min="0" step="0.10"
                            placeholder="0.00">
                    </div>
                    <span class="small text-muted"><i class="bi bi-info-circle"></i> Ingrese un el monto que el cliente está pagando, para calcular el vuelto.</span>
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
            <div class="modal-header bg-success">
                <!-- Encabezado visual tipo comprobante/voucher -->
                <h5 class="modal-title text-white" id="modalPostVentaLabel">
                    <i class="bi bi-check-circle me-2"></i> Venta completada
                </h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h5 class="text-center fw-semibold mb-3">
                    <div class="mb-2">
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Venta completada
                        </span>
                    </div>
                    <div class="fw-semibold text-dark mt-1">Comprobante de pago</div>
                </h5>
                <p class="mb-2 fw-semibold">¿Quieres darle un nombre a esta venta?</p>

                <!-- Nombre opcional para identificar la venta luego -->
                <div class="mb-5">
                    <label class="form-label form-label-sm small">Nombre de la venta (opcional)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="nombreVenta"
                            placeholder="Ej. Venta Samuel - Audífonos">
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
                <!--<p class="small text-muted mb-2">Opciones de comprobante / voucher</p>-->

                <!-- Acciones posibles luego de finalizar la venta -->
                <!-- Acciones posibles luego de finalizar la venta (MOVIDO AL FOOTER) -->
                <!-- <div class="d-flex flex-wrap gap-2 justify-content-center"> ... </div> -->
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-dark" id="btnPrintVoucher">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
                <button type="button" class="btn btn-success px-4" id="btnViewVoucher">
                    <i class="bi bi-receipt me-1"></i> Ver Comprobante
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="voucherModalLabel">Comprobante de Venta</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" id="voucherContainer">
                <div class="receipt-container report-card-movements p-4 border rounded shadow-sm bg-white">
                    <!-- Header -->
                    <div class="row align-items-center mb-4 border-bottom pb-3">
                        <div class="col-3 text-center">
                            <img id="logo_voucher" src="" alt="Logo" class="img-fluid"
                                style="max-height: 80px; filter: grayscale(100%);">
                        </div>
                        <div class="col-9 text-end">
                            <h4 class="fw-bold text-uppercase mb-1" id="name_bussines">--</h4>
                            <p class="mb-0 text-muted small" id="direction_bussines">--</p>
                            <p class="mb-0 text-muted small">RUC: <span id="document_bussines">--</span></p>
                        </div>
                    </div>

                    <!-- Title & Date -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5 class="fw-bold text-decoration-underline text-uppercase">Comprobante de Venta</h5>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="small text-uppercase text-muted fw-bold">Codigo de Venta:</label>
                            <div class="fw-bold" id="voucher_code">--</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Emisión:</label>
                            <div class="fw-bold" id="date_time">--</div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="small text-uppercase text-muted fw-bold">Vendedor:</label>
                            <div class="fw-bold" id="fullname">--</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Cliente:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="name_customer">--</div>
                            <div class="small text-muted" id="direction_customer">--</div>
                        </div>
                    </div>

                    <!-- Product Details Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered border-dark table-sm mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 10%;">Cant.</th>
                                    <th style="width: 50%;">Descripción</th>
                                    <th style="width: 20%;">P. Unit</th>
                                    <th style="width: 20%;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVoucherDetails">
                                <!-- Dynamic Items -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="row justify-content-end">
                        <div class="col-8">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-end fw-bold small py-0">Subtotal:</td>
                                        <td class="text-end small py-0" style="width: 120px;"><span
                                                id="subtotal_amount">--</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0">Descuento (<span
                                                id="percentage_discount">0</span>%):</td>
                                        <td class="text-end text-danger small py-0"><span id="discount_amount">--</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0"><span id="tax_name">IGV</span> (<span
                                                id="tax_percentage">0</span>%):</td>
                                        <td class="text-end small py-0"><span id="tax_amount">--</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-2 border border-2 border-dark rounded bg-light mt-2 text-end">
                                <label class="small text-uppercase text-muted fw-bold d-block">Total a Pagar</label>
                                <span class="fs-4 fw-bold text-dark" id="total_amount">--</span>
                            </div>
                        </div>
                    </div>
                    <!-- System Footer -->
                    <div class="row mt-4">
                        <div class="col-12 text-center d-flex align-items-center justify-content-center">
                            <img src="<?= base_url() ?>/Assets/capysm.png" alt="Logo"
                                style="height: 20px; width: auto; margin-right: 5px; opacity: 0.8;">
                            <small class="text-muted fst-italic">Generado por Capy Ventas</small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" id="download-png"><i class="bi bi-card-image"></i>
                    Exportar PNG</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= footerPos($data) ?>