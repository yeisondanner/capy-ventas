<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-wallet"></i> Creditos</h1>
            <p>Administra los creditos de tu negocio</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/credits">Creditos</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-5 border shadow-sm">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5"
                            href="<?= base_url() ?>/pos/movements"><i class="bi bi-pc-display-horizontal fs-4"></i>
                            Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5" aria-current="page"
                            href="<?= base_url() ?>/pos/boxhistory"><i class="bi bi-cash fs-4"></i> Cierrres de caja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border border-primary shadow-sm rounded-5 active"
                            href="<?= base_url() ?>/pos/credits"><i class="bi bi-wallet fs-4"></i> Creditos</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body border p-2 rounded-3 bg-light">
                    <h6 class="text-center text-primary mb-3">Filtrar Creditos</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <div class="flex-fill __filter_col">
                            <label for="filter-type"
                                class="text-muted fw-bold d-block text-uppercase small form-label">Nombre del
                                Cliente:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="filter-search"
                                    placeholder="Nombre del cliente o DNI">
                            </div>
                        </div>

                        <div class="flex-fill" id="date-container">
                            <label for="filter-date-start" class="text-muted fw-bold d-block text-uppercase small form-label"
                                id="date-label">Fecha de Inicio:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date-start" class="form-control"
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="flex-fill" id="date-container">
                            <label for="filter-date-end" class="text-muted fw-bold d-block text-uppercase small form-label"
                                id="date-label">Fecha de Fin:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="filter-date-end" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="flex-fill d-flex align-items-end justify-content-center justify-content-sm-start">
                            <button id="filter-btn" class="btn_filter flex-fill btn btn-outline-primary me-2"><i
                                    class="bi bi-funnel"></i> Filtrar</button>
                            <button id="reset-btn" class="btn_clean flex-fill btn btn-outline-secondary "><i
                                    class="bi bi-arrow-counterclockwise"></i> Limpiar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tile rounded-3">
                <div class="tile-body">
                    <div class="table-responsive table-responsive-sm bg-light rounded-3 border p-1">
                        <table class="table table-sm table-hover table-bordered table-striped table-responsive"
                            id="table" data-token="<?= csrf(false); ?>">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Acciones</th>
                                    <th>Cliente</th>
                                    <th>Limite de Credito</th>
                                    <th>Saldo Pendiente</th>
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