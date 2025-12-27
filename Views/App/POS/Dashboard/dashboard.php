<?php
headerPos($data);
$widget_alert = $data['page_vars'][2];
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-house-door"></i> Inicio</h1>
            <p>Bienvenido al inicio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/dashboard">Inicio</a></li>
        </ul>
    </div>
    <?php
    dep($_SESSION);
    if (isset($_SESSION[$widget_alert])):
        $alert = $_SESSION[$widget_alert];
    ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show p-0 rounded-4 overflow-hidden position-relative" role="alert">
            <div class="row g-0">
                <!-- COLUMNA 1: ICONO GRANDE 
                 Usamos un calendario para indicar "fecha" de forma visual rápida -->
                <div class="col-12 col-sm-auto">
                    <div class="icon-container bg-<?= $alert['color'] ?> d-flex align-items-center justify-content-center p-4 w-100">
                        <i class="bi <?= $alert['icon'] ?> display-4"></i>
                    </div>
                </div>
                <!-- COLUMNA 2: TEXTO Y ACCIÓN -->
                <div class="col p-4 d-flex flex-column justify-content-center">

                    <!-- Título directo y humano -->
                    <div class="pe-4 mb-3">
                        <h5 class="fw-bold text-dark mb-1">
                            <?= $alert['title'] ?>
                        </h5>
                        <p class="text-secondary mb-0 small" style="line-height: 1.5;">
                            <?= $alert['message_main'] ?>
                        </p>
                    </div>

                    <!-- Caja de Consecuencia y Botón -->
                    <div class="info-box rounded-3 p-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">

                        <!-- Explicación clara de lo que sucederá (con icono de info) -->
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill text-warning flex-shrink-0 mt-1"></i>
                            <div class="small text-muted" style="line-height: 1.3;">
                                <span class="fw-bold text-dark">Nota importante:</span><br>
                                <?= $alert['message_note'] ?>
                            </div>
                        </div>

                        <!-- Botón de Acción (Call to Action) -->
                        <a href="<?= $alert['url'] ?>" class="<?= $alert['btn_class'] ?> rounded-pill px-4 py-2 fw-semibold text-nowrap flex-shrink-0 shadow-sm align-self-start align-self-sm-center">
                            <i class="<?= $alert['btn_icon'] ?>"></i> <?= $alert['btn_text'] ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Inicio del Componente Tablón -->
    <div class="card changelog-card mb-5">

        <!-- Encabezado del Tablón -->
        <div class="changelog-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center">
                <div class="brand-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold">Actualización Beta</h2>
                    <p class="mb-0 opacity-90">Lanzada el 13 de Diciembre, 2025</p>
                </div>
            </div>
            <div class="text-end">
                <span class="version-badge"><?= versionSystem() ?> (Beta)</span>
            </div>
        </div>

        <!-- Cuerpo del Tablón: Lista de Cambios -->
        <div class="card-body p-0">

            <!-- Feature 1: Cuentas y Negocios -->
            <div class="change-item d-flex align-items-start gap-4 type-new">
                <div class="icon-box shadow-sm">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-success text-white type-badge">¡NUEVO!</span>
                        <small class="text-muted">Administración</small>
                    </div>
                    <h5 class="fw-bold mb-2">Gestión de Cuentas y Negocios</h5>
                    <p class="text-muted mb-0">Ahora puedes crear tu propia cuenta de usuario para acceder al sistema de forma segura. Además, hemos añadido la capacidad de registrar nuevos negocios y actualizar la información de los existentes fácilmente.</p>
                </div>
            </div>

            <!-- Feature 2: Roles y Empleados -->
            <div class="change-item d-flex align-items-start gap-4 type-new">
                <div class="icon-box shadow-sm">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-success text-white type-badge">¡NUEVO!</span>
                        <small class="text-muted">Seguridad</small>
                    </div>
                    <h5 class="fw-bold mb-2">Roles y Permisos Avanzados</h5>
                    <p class="text-muted mb-0">Control total sobre tu personal. Crea roles personalizados y administra a tus empleados asignándoles permisos específicos según su función en el negocio.</p>
                </div>
            </div>

            <!-- Feature 3: Inventario Ganancia -->
            <div class="change-item d-flex align-items-start gap-4 type-improvement">
                <div class="icon-box shadow-sm">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary type-badge">Mejora</span>
                        <small class="text-muted">Inventario</small>
                    </div>
                    <h5 class="fw-bold mb-2">Visualización de Ganancias</h5>
                    <p class="text-muted mb-0">El módulo de inventarios ha sido actualizado. Ahora puedes ver claramente el margen de ganancia estimado por cada producto directamente en la lista.</p>
                </div>
            </div>

            <!-- Feature 4: Comprobante Imagen -->
            <div class="change-item d-flex align-items-start gap-4 type-new">
                <div class="icon-box shadow-sm">
                    <i class="bi bi-file-earmark-image"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-success text-white type-badge">¡NUEVO!</span>
                        <small class="text-muted">Ventas</small>
                    </div>
                    <h5 class="fw-bold mb-2">Comprobantes Listos para Compartir</h5>
                    <p class="text-muted mb-0">Al realizar una venta, el sistema genera automáticamente el comprobante en formato de imagen. Ideal para enviarlo rápidamente por WhatsApp o redes sociales a tus clientes.</p>
                </div>
            </div>

        </div>

        <!-- Sección En Construcción (Próximamente) -->
        <div class="roadmap-section">
            <h6 class="fw-bold text-muted text-uppercase mb-3 d-flex align-items-center">
                <i class="bi bi-cone-striped me-2"></i> Próximamente en Capy Ventas
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="roadmap-item shadow-sm">
                        <i class="bi bi-cash-stack"></i>
                        <div>
                            <div class="fw-bold small">Gestión de Caja</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Control de apertura y cierre</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="roadmap-item shadow-sm">
                        <i class="bi bi-receipt"></i>
                        <div>
                            <div class="fw-bold small">Gestión de Gastos</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Registro de salidas de dinero</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="roadmap-item shadow-sm">
                        <i class="bi bi-bar-chart-fill"></i>
                        <div>
                            <div class="fw-bold small">Estadísticas Avanzadas</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Reportes detallados del negocio</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="roadmap-item shadow-sm">
                        <i class="bi bi-clock-history"></i>
                        <div>
                            <div class="fw-bold small">Sesiones e Historial</div>
                            <div class="text-muted" style="font-size: 0.8rem;">Auditoría completa de cajas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer del Tablón -->
        <div class="card-footer-custom text-center border-top py-2">
            <!-- <p class="mb-3 text-muted small">Gracias por probar nuestra versión Beta.</p>
            <button class="btn btn-outline-primary rounded-pill px-4 fw-medium btn-sm">
                <i class="bi bi-bug-fill me-2"></i> Reportar un problema
            </button>-->
        </div>
    </div>
    <!-- Fin del Componente -->
</main>
<?= footerPos($data) ?>