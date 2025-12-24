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
                    <div class="d-flex flex-wrap gap-1"></div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= footerPos($data) ?>