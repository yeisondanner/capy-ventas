<?= headerPos($data) ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-gear"></i> <?= $data['page_title'] ?></h1>
            <p><?= $data['page_description'] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-gear fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/business/configuration">Configuración</a></li>
        </ul>
    </div>
    <div class="tile">
        <?php
        dep($data['sesion_posbusiness_active']);
        ?>
    </div>
</main>
<?= footerPos($data) ?>