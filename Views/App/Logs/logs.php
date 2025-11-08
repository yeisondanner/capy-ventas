<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-microchip"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-microchip"></i></i></li>
            <li class="breadcrumb-item"><a
                    href="<?= base_url() ?>/<? $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="row">
                        <div class="col-xl-8 mb-4">
                            <div class="card log-analytics-card shadow-sm h-100">
                                <div
                                    class="card-header bg-transparent border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                    <div>
                                        <h5 class="mb-1 text-primary font-weight-bold">Comportamiento anual</h5>
                                        <p class="mb-0 text-muted small">Tendencia mensual de registros según su tipo</p>
                                    </div>
                                    <div class="mt-3 mt-md-0">
                                        <label for="logs-year-filter" class="small font-weight-bold mb-1">Selecciona el
                                            año</label>
                                        <select id="logs-year-filter" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="position-relative">
                                        <canvas id="logsTrendChart" height="240"></canvas>
                                        <p id="logsTrendEmpty" class="text-center text-muted mt-3 d-none">
                                            No existen registros para el año seleccionado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 mb-4">
                            <div class="card log-summary-card shadow-sm h-100">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-1 text-primary font-weight-bold">Resumen por tipo</h5>
                                    <p class="mb-0 text-muted small">Cantidad total de registros registrados en el año
                                        elegido</p>
                                </div>
                                <div class="card-body">
                                    <div class="log-summary-item log-summary-item--error">
                                        <div class="log-summary-icon">
                                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <p class="log-summary-label mb-1">Errores</p>
                                            <h4 class="log-summary-total" data-type="1">0</h4>
                                        </div>
                                    </div>
                                    <div class="log-summary-item log-summary-item--success">
                                        <div class="log-summary-icon">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <p class="log-summary-label mb-1">Correctos</p>
                                            <h4 class="log-summary-total" data-type="2">0</h4>
                                        </div>
                                    </div>
                                    <div class="log-summary-item log-summary-item--info">
                                        <div class="log-summary-icon">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <p class="log-summary-label mb-1">Informativos</p>
                                            <h4 class="log-summary-total" data-type="3">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card log-filter-card p-3 shadow-sm">
                        <h6 class="text-center text-primary mb-3">Filtrar Registros</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="min-datetime" class="small font-weight-bold">Desde:</label>
                                <input type="date" id="min-datetime" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label for="max-datetime" class="small font-weight-bold">Hasta:</label>
                                <input type="date" id="max-datetime" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label for="filter-type" class="small font-weight-bold">Filtrar por Tipo:</label>
                                <select id="filter-type" class="form-control form-control-sm">
                                    <option value="0" selected>Todos</option>
                                    <option value="1">Error</option>
                                    <option value="2">Correcto</option>
                                    <option value="3">Información</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button id="filter-btn" class="btn btn-primary btn-sm">Filtrar</button>
                            <button id="reset-btn" class="btn btn-secondary btn-sm">Limpiar</button>
                        </div>
                    </div>

                    <!-- Tabla de datos -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover table-bordered table-sm" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Por</th>
                                    <th>Fecha registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí van los registros -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= footerAdmin($data) ?>
<!-- Seccion de Modals -->
<!-- Modal de Report -->
<div class="modal fade" id="modalReport" tabindex="-1" role="dialog" aria-labelledby="modalReportLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalReportLabel">Reporte de Registro</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Contenedor principal con foto y datos -->
                <div class="d-flex justify-content-center  align-items-center">
                    <h3 class="text-uppercase font-weight-bold text-primary" id="reportTitle">Título del registro</h3>
                </div>
                <!-- Datos Personales -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Información del Registro</h6>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Código</strong></td>
                                <td id="reportCode">#</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de registro</strong></td>
                                <td id="reportType"></td>
                            </tr>
                            <tr>
                                <td><strong>Descripción</strong></td>
                                <td id="reportDescription"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Datos de la Cuenta -->
                <h6 class="text-uppercase font-weight-bold text-danger mt-4">Informacion de Usuario</h6>
                <hr>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Nombre completo</strong></td>
                            <td id="reportFullname">ydcarhuapoma</td>
                        </tr>
                        <tr>
                            <td><strong>Usuario</strong></td>
                            <td id="reportUser">ydcarhuapoma</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td id="reportEmail">yeison@example.com</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Datos de registro: Fecha de registro y actualización -->
                <div class="p-3 bg-light border rounded">
                    <p class="text-muted mb-1">
                        <strong>Fecha de registro:</strong> <span class="text-dark"
                            id="reportRegistrationDate">29/01/2025</span>
                    </p>
                    <p class="text-muted mb-0">
                        <strong>Fecha de actualización:</strong> <span class="text-dark"
                            id="reportUpdateDate">29/01/2025</span>
                    </p>
                </div>
            </div>
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>