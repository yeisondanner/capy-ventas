<?= headerPos($data) ?>
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
    <div class="tile">
        <div class="alert alert-info" role="alert">
            <strong>¡Atención!</strong> Esta es una alerta en HTML.
        </div>
    </div>
    <div class="tile">

    </div>
</main>
<?= footerPos($data) ?>