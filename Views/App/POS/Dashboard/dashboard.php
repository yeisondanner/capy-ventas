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
                        <a href="<?= $alert['url'] ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold text-nowrap flex-shrink-0 shadow-sm align-self-start align-self-sm-center">
                            <?= $alert['btn_text'] ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>
</main>
<?= footerPos($data) ?>