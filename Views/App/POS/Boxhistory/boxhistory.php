<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-cash"></i> Historial de cierre de caja</h1>
            <p>Administra los movimientos de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/boxhistory">Historial de cierre de caja</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5" href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i> Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active" aria-current="page" href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body border p-2 rounded-3 bg-light">
                    <h6 class="text-center text-primary mb-3">Filtrar Cierrres de caja</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <div class="flex-fill __filter_col">
                            <label for="filter-type" class="text-muted fw-bold d-block text-uppercase small form-label">Tipo de Filtro:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-filter"></i></span>
                                <select id="filter-type" class="form-select">
                                    <option value="daily">Diario</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="monthly">Mensual</option>
                                    <option value="yearly">Anual</option>
                                    <option value="custom">Rango Personalizado</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex-fill" id="date-container">
                            <label for="filter-date" class="text-muted fw-bold d-block text-uppercase small form-label" id="date-label">Fecha:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-range-container" style="display: none;">
                            <label for="min-date" class="text-muted fw-bold d-block text-uppercase small form-label">Desde:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="min-date" class="form-control">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-to-container" style="display: none;">
                            <label for="max-date" class="text-muted fw-bold d-block text-uppercase small form-label">Hasta:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="max-date" class="form-control">
                            </div>
                        </div>
                        <div class="flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
                            <button id="filter-btn" class="btn_filter flex-fill btn btn-outline-primary me-2"><i class="bi bi-funnel"></i> Filtrar</button>
                            <button id="reset-btn" class="btn_clean flex-fill btn btn-outline-secondary "><i class="bi bi-arrow-counterclockwise"></i> Limpiar</button>
                        </div>
                    </div>
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
                                    <th>Usuario</th>
                                    <th>Valor</th>
                                    <th>Medios de Pago</th>
                                    <th>Nombre de Vendedor</th>
                                    <th>Fecha y Hora</th>
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