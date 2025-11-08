<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-dashboard"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a
                    href="<?= base_url() ?>/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="row">
        <?php
        foreach ($data['page_widget'] as $key => $value) {
            ?>
            <div class="col-md-6 col-lg-3">
                <a href="<?= $value['link'] ?>" title="<?= $value['text'] ?>" data-toggle="tooltip"
                    class="bg-white rounded mb-3 widget-small <?= $value['color'] ?> coloured-icon"
                    style="text-decoration: none;"><i class="icon <?= $value['icon'] ?> fa-3x"></i>
                    <div class="info text-dark">
                        <h4><?= $value['title'] ?></h4>
                        <p><b><?= $value['value'] ?></b></p>
                    </div>
                </a>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h2>Interfaces de login</h2>
                <?php dep($_SESSION['login_interface']); ?>
                <h2>Variables de sesion activas</h2>
                <?php dep($_SESSION); ?>
            </div>
        </div>

    </div>

</main>
<?= footerAdmin($data) ?>