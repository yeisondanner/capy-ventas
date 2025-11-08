<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar"
            src="<?= ($_SESSION['login_info']['profile'] == "" ? generateAvatar($_SESSION['login_info']['fullName']) : base_url() . "/loadfile/profile/?f=" . $_SESSION['login_info']['profile']) ?>"
            alt="<?= $_SESSION['login_info']['fullName'] ?>">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION['login_info']['fullName'] ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION['login_info']['role'] ?></p>
        </div>
    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item <?= activeItem(2, $data["page_id"]) ?>" href="<?= base_url() ?>/dashboard"><i
                    class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
        <?= loadOptions($_SESSION['login_info']['idUser'], $data) ?>
    </ul>
</aside>