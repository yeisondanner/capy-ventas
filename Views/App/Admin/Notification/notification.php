<?= headerAdmin($data) ?>

<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-bell-o" aria-hidden="true"></i> <?= $data["page_title"] ?>
            </h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-bell-o" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a
                    href="<?= base_url() ?>/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
    <div class="tile">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalSave">
            <i class="fa fa-plus"></i> Nuevo
        </button>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>ID</th>
                                    <th>Titulo</th>
                                    <th>Descripción</th>
                                    <th>Link</th>
                                    <th>Icono</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Leido</th>
                                    <th>Envio Correo</th>
                                    <th>Estado</th>
                                    <th>Fecha registro</th>
                                    <th>Acciones</th>
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

<?= footerAdmin($data) ?>

<!-- Sección de Modals -->
<!-- Modal Save -->
<div class="modal fade" id="modalSave" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="formSave">
            <?= csrf(); ?>
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-bell"></i> Enviar Notificación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Formulario -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slctUsers"><i class="fa fa-users"></i> Seleccione destinatarios</label>
                            <div class="d-flex align-items-center justify-content-between">
                                <select class="js-states form-control" id="slctUsers" name="slctUsers[]" multiple
                                    required>
                                    <!-- Cargar usuarios dinámicamente -->
                                </select>
                                <button class="btn btn-primary" id="allUsers" title="Seleccionar a todos los usuarios"
                                    data-toggle="tooltip" type="button">
                                    <i class="fa fa-users"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Puede elegir uno o varios usuarios.</small>
                        </div>

                        <div class="form-group">
                            <label for="txtTitle"><i class="fa fa-header"></i> Título <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="txtTitle" name="txtTitle" maxlength="255"
                                required onkeyup="previewTitle.innerHTML = this.value">
                        </div>

                        <div class="form-group">
                            <label for="txtDescription"><i class="fa fa-align-left"></i> Descripción <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="txtDescription" name="txtDescription"
                                onkeyup="previewText.textContent = decodeURI(encodeURI(this.value))" rows="5"
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="txtLink"><i class="fa fa-link"></i> Enlace (opcional)</label>
                            <input type="url" id="txtLink" name="txtLink"
                                onkeyup="previewLink.innerHTML = this.value; previewLink.href = this.value"
                                class=" form-control" placeholder="https://ejemplo.com">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="slctIcon"><i class="fa fa-picture-o"></i> Ícono</label>
                                <select class="form-control" id="slctIcon" name="slctIcon" onchange="previewIcon.classList.remove(...previewIcon.classList);
                previewIcon.classList.add('fa','fa-stack-1x','fa-inverse', this.value)">
                                    <!-- Web Application Icons -->
                                    <optgroup label="Web Application Icons">
                                        <option value="fa-adjust">fa-adjust</option>
                                        <option value="fa-anchor">fa-anchor</option>
                                        <option value="fa-archive">fa-archive</option>
                                        <option value="fa-area-chart">fa-area-chart</option>
                                        <option value="fa-arrows">fa-arrows</option>
                                        <option value="fa-arrows-h">fa-arrows-h</option>
                                        <option value="fa-arrows-v">fa-arrows-v</option>
                                        <option value="fa-asterisk">fa-asterisk</option>
                                        <option value="fa-at">fa-at</option>
                                        <option value="fa-automobile">fa-automobile</option>
                                        <option value="fa-balance-scale">fa-balance-scale</option>
                                        <option value="fa-ban">fa-ban</option>
                                        <option value="fa-bank">fa-bank</option>
                                        <option value="fa-bar-chart">fa-bar-chart</option>
                                        <option value="fa-barcode">fa-barcode</option>
                                        <option value="fa-bars">fa-bars</option>
                                        <option value="fa-battery-full">fa-battery-full</option>
                                        <option value="fa-beer">fa-beer</option>
                                        <option value="fa-bell">fa-bell</option>
                                        <option value="fa-bell-o">fa-bell-o</option>
                                        <option value="fa-bolt">fa-bolt</option>
                                        <option value="fa-book">fa-book</option>
                                    </optgroup>

                                    <!-- Accessibility Icons -->
                                    <optgroup label="Accessibility Icons">
                                        <option value="fa-american-sign-language-interpreting">
                                            fa-american-sign-language-interpreting</option>
                                        <option value="fa-assistive-listening-systems">fa-assistive-listening-systems
                                        </option>
                                        <option value="fa-audio-description">fa-audio-description</option>
                                        <option value="fa-blind">fa-blind</option>
                                        <option value="fa-braille">fa-braille</option>
                                        <option value="fa-cc">fa-cc</option>
                                        <option value="fa-deaf">fa-deaf</option>
                                        <option value="fa-low-vision">fa-low-vision</option>
                                        <option value="fa-sign-language">fa-sign-language</option>
                                        <option value="fa-universal-access">fa-universal-access</option>
                                        <option value="fa-wheelchair">fa-wheelchair</option>
                                    </optgroup>

                                    <!-- Hand Icons -->
                                    <optgroup label="Hand Icons">
                                        <option value="fa-hand-grab-o">fa-hand-grab-o</option>
                                        <option value="fa-hand-lizard-o">fa-hand-lizard-o</option>
                                        <option value="fa-hand-paper-o">fa-hand-paper-o</option>
                                        <option value="fa-hand-peace-o">fa-hand-peace-o</option>
                                        <option value="fa-hand-pointer-o">fa-hand-pointer-o</option>
                                        <option value="fa-hand-rock-o">fa-hand-rock-o</option>
                                        <option value="fa-hand-scissors-o">fa-hand-scissors-o</option>
                                        <option value="fa-hand-spock-o">fa-hand-spock-o</option>
                                        <option value="fa-hand-stop-o">fa-hand-stop-o</option>
                                        <option value="fa-thumbs-down">fa-thumbs-down</option>
                                        <option value="fa-thumbs-up">fa-thumbs-up</option>
                                    </optgroup>

                                    <!-- Transportation Icons -->
                                    <optgroup label="Transportation Icons">
                                        <option value="fa-ambulance">fa-ambulance</option>
                                        <option value="fa-bicycle">fa-bicycle</option>
                                        <option value="fa-bus">fa-bus</option>
                                        <option value="fa-car">fa-car</option>
                                        <option value="fa-fighter-jet">fa-fighter-jet</option>
                                        <option value="fa-motorcycle">fa-motorcycle</option>
                                        <option value="fa-plane">fa-plane</option>
                                        <option value="fa-rocket">fa-rocket</option>
                                        <option value="fa-ship">fa-ship</option>
                                        <option value="fa-subway">fa-subway</option>
                                        <option value="fa-taxi">fa-taxi</option>
                                        <option value="fa-train">fa-train</option>
                                        <option value="fa-truck">fa-truck</option>
                                    </optgroup>

                                    <!-- File Type Icons -->
                                    <optgroup label="File Type Icons">
                                        <option value="fa-file">fa-file</option>
                                        <option value="fa-file-o">fa-file-o</option>
                                        <option value="fa-file-text">fa-file-text</option>
                                        <option value="fa-file-text-o">fa-file-text-o</option>
                                        <option value="fa-file-pdf-o">fa-file-pdf-o</option>
                                        <option value="fa-file-word-o">fa-file-word-o</option>
                                        <option value="fa-file-excel-o">fa-file-excel-o</option>
                                        <option value="fa-file-powerpoint-o">fa-file-powerpoint-o</option>
                                        <option value="fa-file-image-o">fa-file-image-o</option>
                                        <option value="fa-file-archive-o">fa-file-archive-o</option>
                                        <option value="fa-file-audio-o">fa-file-audio-o</option>
                                        <option value="fa-file-movie-o">fa-file-movie-o</option>
                                    </optgroup>
                                    <optgroup label="All Font Awesome 4.7 Icons">
                                        <option value="fa-adjust">fa-adjust</option>
                                        <option value="fa-anchor">fa-anchor</option>
                                        <option value="fa-archive">fa-archive</option>
                                        <option value="fa-area-chart">fa-area-chart</option>
                                        <option value="fa-arrows">fa-arrows</option>
                                        <option value="fa-arrows-h">fa-arrows-h</option>
                                        <option value="fa-arrows-v">fa-arrows-v</option>
                                        <option value="fa-asterisk">fa-asterisk</option>
                                        <option value="fa-at">fa-at</option>
                                        <option value="fa-automobile">fa-automobile</option>
                                        <option value="fa-balance-scale">fa-balance-scale</option>
                                        <option value="fa-ban">fa-ban</option>
                                        <option value="fa-bank">fa-bank</option>
                                        <option value="fa-bar-chart">fa-bar-chart</option>
                                        <option value="fa-barcode">fa-barcode</option>
                                        <option value="fa-bars">fa-bars</option>
                                        <option value="fa-battery-empty">fa-battery-empty</option>
                                        <option value="fa-battery-full">fa-battery-full</option>
                                        <option value="fa-beer">fa-beer</option>
                                        <option value="fa-bell">fa-bell</option>
                                        <option value="fa-bicycle">fa-bicycle</option>
                                        <option value="fa-binoculars">fa-binoculars</option>
                                        <option value="fa-birthday-cake">fa-birthday-cake</option>
                                        <option value="fa-bolt">fa-bolt</option>
                                        <option value="fa-bomb">fa-bomb</option>
                                        <option value="fa-book">fa-book</option>
                                        <option value="fa-bookmark">fa-bookmark</option>
                                        <option value="fa-briefcase">fa-briefcase</option>
                                        <option value="fa-bug">fa-bug</option>
                                        <option value="fa-building">fa-building</option>
                                        <option value="fa-bullhorn">fa-bullhorn</option>
                                        <option value="fa-bullseye">fa-bullseye</option>
                                        <option value="fa-bus">fa-bus</option>
                                        <option value="fa-calculator">fa-calculator</option>
                                        <option value="fa-calendar">fa-calendar</option>
                                        <option value="fa-camera">fa-camera</option>
                                        <option value="fa-car">fa-car</option>
                                        <option value="fa-certificate">fa-certificate</option>
                                        <option value="fa-check">fa-check</option>
                                        <option value="fa-child">fa-child</option>
                                        <option value="fa-circle">fa-circle</option>
                                        <option value="fa-clipboard">fa-clipboard</option>
                                        <option value="fa-clock-o">fa-clock-o</option>
                                        <option value="fa-cloud">fa-cloud</option>
                                        <option value="fa-code">fa-code</option>
                                        <option value="fa-coffee">fa-coffee</option>
                                        <option value="fa-cog">fa-cog</option>
                                        <option value="fa-comment">fa-comment</option>
                                        <option value="fa-compass">fa-compass</option>
                                        <option value="fa-credit-card">fa-credit-card</option>
                                        <option value="fa-cube">fa-cube</option>
                                        <option value="fa-database">fa-database</option>
                                        <option value="fa-desktop">fa-desktop</option>
                                        <option value="fa-diamond">fa-diamond</option>
                                        <option value="fa-download">fa-download</option>
                                        <option value="fa-edit">fa-edit</option>
                                        <option value="fa-envelope">fa-envelope</option>
                                        <option value="fa-exclamation">fa-exclamation</option>
                                        <option value="fa-eye">fa-eye</option>
                                        <option value="fa-fax">fa-fax</option>
                                        <option value="fa-female">fa-female</option>
                                        <option value="fa-fighter-jet">fa-fighter-jet</option>
                                        <option value="fa-file">fa-file</option>
                                        <option value="fa-flag">fa-flag</option>
                                        <option value="fa-flash">fa-flash</option>
                                        <option value="fa-flask">fa-flask</option>
                                        <option value="fa-folder">fa-folder</option>
                                        <option value="fa-gift">fa-gift</option>
                                        <option value="fa-glass">fa-glass</option>
                                        <option value="fa-globe">fa-globe</option>
                                        <option value="fa-graduation-cap">fa-graduation-cap</option>
                                        <option value="fa-group">fa-group</option>
                                        <option value="fa-hand-paper-o">fa-hand-paper-o</option>
                                        <option value="fa-headphones">fa-headphones</option>
                                        <option value="fa-heart">fa-heart</option>
                                        <option value="fa-home">fa-home</option>
                                        <option value="fa-hourglass">fa-hourglass</option>
                                        <option value="fa-image">fa-image</option>
                                        <option value="fa-inbox">fa-inbox</option>
                                        <option value="fa-industry">fa-industry</option>
                                        <option value="fa-info">fa-info</option>
                                        <option value="fa-key">fa-key</option>
                                        <option value="fa-language">fa-language</option>
                                        <option value="fa-laptop">fa-laptop</option>
                                        <option value="fa-leaf">fa-leaf</option>
                                        <option value="fa-legal">fa-legal</option>
                                        <option value="fa-lemon-o">fa-lemon-o</option>
                                        <option value="fa-life-ring">fa-life-ring</option>
                                        <option value="fa-lightbulb-o">fa-lightbulb-o</option>
                                        <option value="fa-line-chart">fa-line-chart</option>
                                        <option value="fa-location-arrow">fa-location-arrow</option>
                                        <option value="fa-lock">fa-lock</option>
                                        <option value="fa-magic">fa-magic</option>
                                        <option value="fa-magnet">fa-magnet</option>
                                        <option value="fa-male">fa-male</option>
                                        <option value="fa-map">fa-map</option>
                                        <option value="fa-medkit">fa-medkit</option>
                                        <option value="fa-meh-o">fa-meh-o</option>
                                        <option value="fa-microphone">fa-microphone</option>
                                        <option value="fa-mobile">fa-mobile</option>
                                        <option value="fa-motorcycle">fa-motorcycle</option>
                                        <option value="fa-music">fa-music</option>
                                        <option value="fa-paper-plane">fa-paper-plane</option>
                                        <option value="fa-pencil">fa-pencil</option>
                                        <option value="fa-phone">fa-phone</option>
                                        <option value="fa-picture-o">fa-picture-o</option>
                                        <option value="fa-plane">fa-plane</option>
                                        <option value="fa-plug">fa-plug</option>
                                        <option value="fa-plus">fa-plus</option>
                                        <option value="fa-print">fa-print</option>
                                        <option value="fa-puzzle-piece">fa-puzzle-piece</option>
                                        <option value="fa-question">fa-question</option>
                                        <option value="fa-quote-left">fa-quote-left</option>
                                        <option value="fa-random">fa-random</option>
                                        <option value="fa-recycle">fa-recycle</option>
                                        <option value="fa-refresh">fa-refresh</option>
                                        <option value="fa-rocket">fa-rocket</option>
                                        <option value="fa-rss">fa-rss</option>
                                        <option value="fa-save">fa-save</option>
                                        <option value="fa-search">fa-search</option>
                                        <option value="fa-send">fa-send</option>
                                        <option value="fa-server">fa-server</option>
                                        <option value="fa-shield">fa-shield</option>
                                        <option value="fa-ship">fa-ship</option>
                                        <option value="fa-shopping-bag">fa-shopping-bag</option>
                                        <option value="fa-sign-in">fa-sign-in</option>
                                        <option value="fa-sitemap">fa-sitemap</option>
                                        <option value="fa-sliders">fa-sliders</option>
                                        <option value="fa-smile-o">fa-smile-o</option>
                                        <option value="fa-soccer-ball-o">fa-soccer-ball-o</option>
                                        <option value="fa-space-shuttle">fa-space-shuttle</option>
                                        <option value="fa-spinner">fa-spinner</option>
                                        <option value="fa-star">fa-star</option>
                                        <option value="fa-street-view">fa-street-view</option>
                                        <option value="fa-subway">fa-subway</option>
                                        <option value="fa-suitcase">fa-suitcase</option>
                                        <option value="fa-sun-o">fa-sun-o</option>
                                        <option value="fa-table">fa-table</option>
                                        <option value="fa-taxi">fa-taxi</option>
                                        <option value="fa-terminal">fa-terminal</option>
                                        <option value="fa-thumbs-up">fa-thumbs-up</option>
                                        <option value="fa-ticket">fa-ticket</option>
                                        <option value="fa-toggle-on">fa-toggle-on</option>
                                        <option value="fa-train">fa-train</option>
                                        <option value="fa-trash">fa-trash</option>
                                        <option value="fa-tree">fa-tree</option>
                                        <option value="fa-trophy">fa-trophy</option>
                                        <option value="fa-truck">fa-truck</option>
                                        <option value="fa-tv">fa-tv</option>
                                        <option value="fa-university">fa-university</option>
                                        <option value="fa-unlock">fa-unlock</option>
                                        <option value="fa-upload">fa-upload</option>
                                        <option value="fa-user">fa-user</option>
                                        <option value="fa-video-camera">fa-video-camera</option>
                                        <option value="fa-volume-up">fa-volume-up</option>
                                        <option value="fa-wheelchair">fa-wheelchair</option>
                                        <option value="fa-wifi">fa-wifi</option>
                                        <option value="fa-wrench">fa-wrench</option>
                                    </optgroup>
                                </select>
                                <small class="form-text text-muted">Selecciona un ícono de FontAwesome v4.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="slctColor"><i class="fa fa-paint-brush"></i> Color Bootstrap</label>
                                <select class="form-control" id="slctColor" name="slctColor"
                                    onchange="chageColorPreviewNotification(this.value)">
                                    <option value="primary" selected>Primary (color del tema atual)</option>
                                    <option value="secondary">Secondary (Gris oscuro)</option>
                                    <option value="success">Success (Verde)</option>
                                    <option value="danger">Danger (Rojo)</option>
                                    <option value="warning">Warning (Amarillo)</option>
                                    <option value="info">Info (Celeste)</option>
                                    <option value="dark">Dark (Negro grisáceo)</option>
                                </select>
                                <small class="form-text text-muted">Color del borde o fondo.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="slctType"><i class="fa fa-flag"></i> Tipo de notificación</label>
                                <select class="form-control" id="slctType" name="slctType"
                                    onchange="changeTitleHeader(this.value)">
                                    <option value="info" selected>Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="slctPriority"><i class="fa fa-exclamation-circle"></i> Prioridad</label>
                                <select class="form-control" id="slctPriority" name="slctPriority"
                                    onchange="previewPriority.innerHTML = `<i class='fa fa-flag text-danger mr-2'></i><strong>Prioridad:</strong> ${this.value==1?'Baja':this.value==2?'Media':'Alta'}`">
                                    <option value="1" selected>Baja</option>
                                    <option value="2">Media</option>
                                    <option value="3">Alta</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12 rounded pb-1 pt-1"
                                style="background-color: #f5f5f5; border: 1px solid #ddd;">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="chckNotification"
                                        name="chckNotification">
                                    <label class="custom-control-label" for="chckNotification"
                                        title="Esto solo se podra enviar cuando se registre la notificacion al registrar">
                                        <i class="fa fa-exclamation-circle text-warning"></i> ¿Notificar al correo?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista previa -->
                    <div class="col-md-6">
                        <h2 class="text-center mb-2">Vista previa</h2>
                        <div class="modal-content shadow-lg rounded-lg">
                            <div class="bg-primary text-white p-3 rounded-top" id="previewNotification">
                                <h5 class="modal-title mb-0" id="previewTitlePrincipal">
                                    <i class="fa fa-bell mr-2"></i> Notificación Importante
                                </h5>
                            </div>

                            <div class="modal-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <span class="fa-stack fa-2x text-primary" id="previewIconContainer">
                                            <i class="fa fa-circle fa-stack-2x text-primary" id="previewIconCircle""></i>
                                            <i class=" fa fa-envelope fa-stack-1x fa-inverse" id="previewIcon"></i>
                                        </span>
                                    </div>
                                    <div id="notificacionHeader">
                                        <h4 class="mb-1 text-dark font-weight-bold" id="previewTitle">Lorem ipsum dolor
                                            sit amet
                                            consectetur adipisicing elit. Voluptatum, recusandae.</h4>
                                        <span class="badge badge-primary" id="previewType">Tipo: info</span>
                                        <small class="text-muted d-block mt-1">Fecha: Hace 1 hora</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="text-dark" style="white-space: pre-wrap; word-break: break-word;"
                                        id="previewText">Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore
                                        recusandae facilis dolorum mollitia non assumenda!
                                    </p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p id="previewPriority"><i
                                                class="fa fa-flag text-danger mr-2"></i><strong>Prioridad:</strong> Baja
                                        </p>
                                        <p><i class="fa fa-times text-danger mr-2"></i><strong>Leída:</strong>
                                            No</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fa fa-info-circle text-info mr-2"></i><strong>Estado:</strong>
                                            Activo
                                        </p>
                                        <p><i class="fa fa-hashtag text-dark mr-2"></i><strong>ID:</strong> #1234</p>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded border">
                                    <a href="#" target="_blank" id="previewLink">https://mipagina.com/info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin vista previa -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-paper-plane"></i> Enviar Notificación
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Delete-->
<div class="modal fade" id="confirmModalDelete" tabindex="-1" role="dialog" aria-labelledby="confirmModalDeleteLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabel">Confirmación de Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fa fa-exclamation-triangle fa-5x text-danger mb-3"></i>
                <p class="font-weight-bold">¿Estás seguro?</p>
                <p class="" id="txtDelete"></p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" data-token="<?= csrf(false); ?>" id="confirmDelete">
                    <i class="fa fa-check"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Update-->
<div class="modal fade" id="modalUpdate" tabindex="-1" data-backdrop="static" role="dialog"
    aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="formUpdate">
            <?= csrf(); ?>
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalUpdateLabel">Actualizar la notificación</h5>
                <button type="button" class="close btn-close-modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de Actualziacion de Usuario -->
                <div class="row">
                    <!-- Formulario -->
                    <div class="col-md-6">

                        <p class="text-muted"><i class="fa fa-user"></i> Usuario</p>

                        <div class="d-flex bg-light rounded mb-2">
                            <img src="http://localhost/sis-roles/loadfile/profile/?f=user.png"
                                alt="Super Administrador Sistema Roles" style="height: 125px; width: 125px;"
                                id="updateImgUser">

                            <div class="px-2 py-2">
                                <p class="text-primary font-weight-bold" style="font-size: 1.2rem;" id="updateNameUser">
                                    Super Administrador Sistema Roles</p>
                                <p class="text-muted" id="updateEmailUser">admin@test.com</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="updateTxtTitle"><i class="fa fa-header"></i> Título <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="updateTxtTitle" name="updateTxtTitle"
                                maxlength="255" required onkeyup="updatePreviewTitle.innerHTML = this.value">
                        </div>

                        <div class="form-group">
                            <label for="updateTxtDescription"><i class="fa fa-align-left"></i> Descripción <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="updateTxtDescription" name="updateTxtDescription"
                                onkeyup="updatePreviewText.textContent = decodeURI(encodeURI(this.value))" rows="5"
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="updateTxtLink"><i class="fa fa-link"></i> Enlace (opcional)</label>
                            <input type="url" id="updateTxtLink" name="updateTxtLink"
                                onkeyup="updatePreviewLink.innerHTML = this.value; updatePreviewLink.href = this.value"
                                class=" form-control" placeholder="https://ejemplo.com">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="updateSlctIcon"><i class="fa fa-picture-o"></i> Ícono</label>
                                <select class="form-control" id="updateSlctIcon" name="updateSlctIcon" onchange="updatePreviewIcon.classList.remove(...updatePreviewIcon.classList);
                updatePreviewIcon.classList.add('fa','fa-stack-1x','fa-inverse', this.value)">
                                    <!-- Web Application Icons -->
                                    <optgroup label="Web Application Icons">
                                        <option value="fa-adjust">fa-adjust</option>
                                        <option value="fa-anchor">fa-anchor</option>
                                        <option value="fa-archive">fa-archive</option>
                                        <option value="fa-area-chart">fa-area-chart</option>
                                        <option value="fa-arrows">fa-arrows</option>
                                        <option value="fa-arrows-h">fa-arrows-h</option>
                                        <option value="fa-arrows-v">fa-arrows-v</option>
                                        <option value="fa-asterisk">fa-asterisk</option>
                                        <option value="fa-at">fa-at</option>
                                        <option value="fa-automobile">fa-automobile</option>
                                        <option value="fa-balance-scale">fa-balance-scale</option>
                                        <option value="fa-ban">fa-ban</option>
                                        <option value="fa-bank">fa-bank</option>
                                        <option value="fa-bar-chart">fa-bar-chart</option>
                                        <option value="fa-barcode">fa-barcode</option>
                                        <option value="fa-bars">fa-bars</option>
                                        <option value="fa-battery-full">fa-battery-full</option>
                                        <option value="fa-beer">fa-beer</option>
                                        <option value="fa-bell">fa-bell</option>
                                        <option value="fa-bell-o">fa-bell-o</option>
                                        <option value="fa-bolt">fa-bolt</option>
                                        <option value="fa-book">fa-book</option>
                                    </optgroup>

                                    <!-- Accessibility Icons -->
                                    <optgroup label="Accessibility Icons">
                                        <option value="fa-american-sign-language-interpreting">
                                            fa-american-sign-language-interpreting</option>
                                        <option value="fa-assistive-listening-systems">fa-assistive-listening-systems
                                        </option>
                                        <option value="fa-audio-description">fa-audio-description</option>
                                        <option value="fa-blind">fa-blind</option>
                                        <option value="fa-braille">fa-braille</option>
                                        <option value="fa-cc">fa-cc</option>
                                        <option value="fa-deaf">fa-deaf</option>
                                        <option value="fa-low-vision">fa-low-vision</option>
                                        <option value="fa-sign-language">fa-sign-language</option>
                                        <option value="fa-universal-access">fa-universal-access</option>
                                        <option value="fa-wheelchair">fa-wheelchair</option>
                                    </optgroup>

                                    <!-- Hand Icons -->
                                    <optgroup label="Hand Icons">
                                        <option value="fa-hand-grab-o">fa-hand-grab-o</option>
                                        <option value="fa-hand-lizard-o">fa-hand-lizard-o</option>
                                        <option value="fa-hand-paper-o">fa-hand-paper-o</option>
                                        <option value="fa-hand-peace-o">fa-hand-peace-o</option>
                                        <option value="fa-hand-pointer-o">fa-hand-pointer-o</option>
                                        <option value="fa-hand-rock-o">fa-hand-rock-o</option>
                                        <option value="fa-hand-scissors-o">fa-hand-scissors-o</option>
                                        <option value="fa-hand-spock-o">fa-hand-spock-o</option>
                                        <option value="fa-hand-stop-o">fa-hand-stop-o</option>
                                        <option value="fa-thumbs-down">fa-thumbs-down</option>
                                        <option value="fa-thumbs-up">fa-thumbs-up</option>
                                    </optgroup>

                                    <!-- Transportation Icons -->
                                    <optgroup label="Transportation Icons">
                                        <option value="fa-ambulance">fa-ambulance</option>
                                        <option value="fa-bicycle">fa-bicycle</option>
                                        <option value="fa-bus">fa-bus</option>
                                        <option value="fa-car">fa-car</option>
                                        <option value="fa-fighter-jet">fa-fighter-jet</option>
                                        <option value="fa-motorcycle">fa-motorcycle</option>
                                        <option value="fa-plane">fa-plane</option>
                                        <option value="fa-rocket">fa-rocket</option>
                                        <option value="fa-ship">fa-ship</option>
                                        <option value="fa-subway">fa-subway</option>
                                        <option value="fa-taxi">fa-taxi</option>
                                        <option value="fa-train">fa-train</option>
                                        <option value="fa-truck">fa-truck</option>
                                    </optgroup>

                                    <!-- File Type Icons -->
                                    <optgroup label="File Type Icons">
                                        <option value="fa-file">fa-file</option>
                                        <option value="fa-file-o">fa-file-o</option>
                                        <option value="fa-file-text">fa-file-text</option>
                                        <option value="fa-file-text-o">fa-file-text-o</option>
                                        <option value="fa-file-pdf-o">fa-file-pdf-o</option>
                                        <option value="fa-file-word-o">fa-file-word-o</option>
                                        <option value="fa-file-excel-o">fa-file-excel-o</option>
                                        <option value="fa-file-powerpoint-o">fa-file-powerpoint-o</option>
                                        <option value="fa-file-image-o">fa-file-image-o</option>
                                        <option value="fa-file-archive-o">fa-file-archive-o</option>
                                        <option value="fa-file-audio-o">fa-file-audio-o</option>
                                        <option value="fa-file-movie-o">fa-file-movie-o</option>
                                    </optgroup>
                                    <optgroup label="All Font Awesome 4.7 Icons">
                                        <option value="fa-adjust">fa-adjust</option>
                                        <option value="fa-anchor">fa-anchor</option>
                                        <option value="fa-archive">fa-archive</option>
                                        <option value="fa-area-chart">fa-area-chart</option>
                                        <option value="fa-arrows">fa-arrows</option>
                                        <option value="fa-arrows-h">fa-arrows-h</option>
                                        <option value="fa-arrows-v">fa-arrows-v</option>
                                        <option value="fa-asterisk">fa-asterisk</option>
                                        <option value="fa-at">fa-at</option>
                                        <option value="fa-automobile">fa-automobile</option>
                                        <option value="fa-balance-scale">fa-balance-scale</option>
                                        <option value="fa-ban">fa-ban</option>
                                        <option value="fa-bank">fa-bank</option>
                                        <option value="fa-bar-chart">fa-bar-chart</option>
                                        <option value="fa-barcode">fa-barcode</option>
                                        <option value="fa-bars">fa-bars</option>
                                        <option value="fa-battery-empty">fa-battery-empty</option>
                                        <option value="fa-battery-full">fa-battery-full</option>
                                        <option value="fa-beer">fa-beer</option>
                                        <option value="fa-bell">fa-bell</option>
                                        <option value="fa-bicycle">fa-bicycle</option>
                                        <option value="fa-binoculars">fa-binoculars</option>
                                        <option value="fa-birthday-cake">fa-birthday-cake</option>
                                        <option value="fa-bolt">fa-bolt</option>
                                        <option value="fa-bomb">fa-bomb</option>
                                        <option value="fa-book">fa-book</option>
                                        <option value="fa-bookmark">fa-bookmark</option>
                                        <option value="fa-briefcase">fa-briefcase</option>
                                        <option value="fa-bug">fa-bug</option>
                                        <option value="fa-building">fa-building</option>
                                        <option value="fa-bullhorn">fa-bullhorn</option>
                                        <option value="fa-bullseye">fa-bullseye</option>
                                        <option value="fa-bus">fa-bus</option>
                                        <option value="fa-calculator">fa-calculator</option>
                                        <option value="fa-calendar">fa-calendar</option>
                                        <option value="fa-camera">fa-camera</option>
                                        <option value="fa-car">fa-car</option>
                                        <option value="fa-certificate">fa-certificate</option>
                                        <option value="fa-check">fa-check</option>
                                        <option value="fa-child">fa-child</option>
                                        <option value="fa-circle">fa-circle</option>
                                        <option value="fa-clipboard">fa-clipboard</option>
                                        <option value="fa-clock-o">fa-clock-o</option>
                                        <option value="fa-cloud">fa-cloud</option>
                                        <option value="fa-code">fa-code</option>
                                        <option value="fa-coffee">fa-coffee</option>
                                        <option value="fa-cog">fa-cog</option>
                                        <option value="fa-comment">fa-comment</option>
                                        <option value="fa-compass">fa-compass</option>
                                        <option value="fa-credit-card">fa-credit-card</option>
                                        <option value="fa-cube">fa-cube</option>
                                        <option value="fa-database">fa-database</option>
                                        <option value="fa-desktop">fa-desktop</option>
                                        <option value="fa-diamond">fa-diamond</option>
                                        <option value="fa-download">fa-download</option>
                                        <option value="fa-edit">fa-edit</option>
                                        <option value="fa-envelope">fa-envelope</option>
                                        <option value="fa-exclamation">fa-exclamation</option>
                                        <option value="fa-eye">fa-eye</option>
                                        <option value="fa-fax">fa-fax</option>
                                        <option value="fa-female">fa-female</option>
                                        <option value="fa-fighter-jet">fa-fighter-jet</option>
                                        <option value="fa-file">fa-file</option>
                                        <option value="fa-flag">fa-flag</option>
                                        <option value="fa-flash">fa-flash</option>
                                        <option value="fa-flask">fa-flask</option>
                                        <option value="fa-folder">fa-folder</option>
                                        <option value="fa-gift">fa-gift</option>
                                        <option value="fa-glass">fa-glass</option>
                                        <option value="fa-globe">fa-globe</option>
                                        <option value="fa-graduation-cap">fa-graduation-cap</option>
                                        <option value="fa-group">fa-group</option>
                                        <option value="fa-hand-paper-o">fa-hand-paper-o</option>
                                        <option value="fa-headphones">fa-headphones</option>
                                        <option value="fa-heart">fa-heart</option>
                                        <option value="fa-home">fa-home</option>
                                        <option value="fa-hourglass">fa-hourglass</option>
                                        <option value="fa-image">fa-image</option>
                                        <option value="fa-inbox">fa-inbox</option>
                                        <option value="fa-industry">fa-industry</option>
                                        <option value="fa-info">fa-info</option>
                                        <option value="fa-key">fa-key</option>
                                        <option value="fa-language">fa-language</option>
                                        <option value="fa-laptop">fa-laptop</option>
                                        <option value="fa-leaf">fa-leaf</option>
                                        <option value="fa-legal">fa-legal</option>
                                        <option value="fa-lemon-o">fa-lemon-o</option>
                                        <option value="fa-life-ring">fa-life-ring</option>
                                        <option value="fa-lightbulb-o">fa-lightbulb-o</option>
                                        <option value="fa-line-chart">fa-line-chart</option>
                                        <option value="fa-location-arrow">fa-location-arrow</option>
                                        <option value="fa-lock">fa-lock</option>
                                        <option value="fa-magic">fa-magic</option>
                                        <option value="fa-magnet">fa-magnet</option>
                                        <option value="fa-male">fa-male</option>
                                        <option value="fa-map">fa-map</option>
                                        <option value="fa-medkit">fa-medkit</option>
                                        <option value="fa-meh-o">fa-meh-o</option>
                                        <option value="fa-microphone">fa-microphone</option>
                                        <option value="fa-mobile">fa-mobile</option>
                                        <option value="fa-motorcycle">fa-motorcycle</option>
                                        <option value="fa-music">fa-music</option>
                                        <option value="fa-paper-plane">fa-paper-plane</option>
                                        <option value="fa-pencil">fa-pencil</option>
                                        <option value="fa-phone">fa-phone</option>
                                        <option value="fa-picture-o">fa-picture-o</option>
                                        <option value="fa-plane">fa-plane</option>
                                        <option value="fa-plug">fa-plug</option>
                                        <option value="fa-plus">fa-plus</option>
                                        <option value="fa-print">fa-print</option>
                                        <option value="fa-puzzle-piece">fa-puzzle-piece</option>
                                        <option value="fa-question">fa-question</option>
                                        <option value="fa-quote-left">fa-quote-left</option>
                                        <option value="fa-random">fa-random</option>
                                        <option value="fa-recycle">fa-recycle</option>
                                        <option value="fa-refresh">fa-refresh</option>
                                        <option value="fa-rocket">fa-rocket</option>
                                        <option value="fa-rss">fa-rss</option>
                                        <option value="fa-save">fa-save</option>
                                        <option value="fa-search">fa-search</option>
                                        <option value="fa-send">fa-send</option>
                                        <option value="fa-server">fa-server</option>
                                        <option value="fa-shield">fa-shield</option>
                                        <option value="fa-ship">fa-ship</option>
                                        <option value="fa-shopping-bag">fa-shopping-bag</option>
                                        <option value="fa-sign-in">fa-sign-in</option>
                                        <option value="fa-sitemap">fa-sitemap</option>
                                        <option value="fa-sliders">fa-sliders</option>
                                        <option value="fa-smile-o">fa-smile-o</option>
                                        <option value="fa-soccer-ball-o">fa-soccer-ball-o</option>
                                        <option value="fa-space-shuttle">fa-space-shuttle</option>
                                        <option value="fa-spinner">fa-spinner</option>
                                        <option value="fa-star">fa-star</option>
                                        <option value="fa-street-view">fa-street-view</option>
                                        <option value="fa-subway">fa-subway</option>
                                        <option value="fa-suitcase">fa-suitcase</option>
                                        <option value="fa-sun-o">fa-sun-o</option>
                                        <option value="fa-table">fa-table</option>
                                        <option value="fa-taxi">fa-taxi</option>
                                        <option value="fa-terminal">fa-terminal</option>
                                        <option value="fa-thumbs-up">fa-thumbs-up</option>
                                        <option value="fa-ticket">fa-ticket</option>
                                        <option value="fa-toggle-on">fa-toggle-on</option>
                                        <option value="fa-train">fa-train</option>
                                        <option value="fa-trash">fa-trash</option>
                                        <option value="fa-tree">fa-tree</option>
                                        <option value="fa-trophy">fa-trophy</option>
                                        <option value="fa-truck">fa-truck</option>
                                        <option value="fa-tv">fa-tv</option>
                                        <option value="fa-university">fa-university</option>
                                        <option value="fa-unlock">fa-unlock</option>
                                        <option value="fa-upload">fa-upload</option>
                                        <option value="fa-user">fa-user</option>
                                        <option value="fa-video-camera">fa-video-camera</option>
                                        <option value="fa-volume-up">fa-volume-up</option>
                                        <option value="fa-wheelchair">fa-wheelchair</option>
                                        <option value="fa-wifi">fa-wifi</option>
                                        <option value="fa-wrench">fa-wrench</option>
                                    </optgroup>
                                </select>
                                <small class="form-text text-muted">Selecciona un ícono de FontAwesome v4.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="updateSlctColor"><i class="fa fa-paint-brush"></i> Color Bootstrap</label>
                                <select class="form-control" id="updateSlctColor" name="updateSlctColor"
                                    onchange="updateChageColorPreviewNotification(this.value)">
                                    <option value="primary" selected>Primary (color del tema atual)</option>
                                    <option value="secondary">Secondary (Gris oscuro)</option>
                                    <option value="success">Success (Verde)</option>
                                    <option value="danger">Danger (Rojo)</option>
                                    <option value="warning">Warning (Amarillo)</option>
                                    <option value="info">Info (Celeste)</option>
                                    <option value="dark">Dark (Negro grisáceo)</option>
                                </select>
                                <small class="form-text text-muted">Color del borde o fondo.</small>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="updateSlctType"><i class="fa fa-flag"></i> Tipo de notificación</label>
                                <select class="form-control" id="updateSlctType" name="updateSlctType"
                                    onchange="updateChangeTitleHeader(this.value)">
                                    <option value="info" selected>Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="updateSlctPriority"><i class="fa fa-exclamation-circle"></i>
                                    Prioridad</label>
                                <select class="form-control" id="updateSlctPriority" name="updateSlctPriority"
                                    onchange="updatePreviewPriority.innerHTML = `<i class='fa fa-flag text-danger mr-2'></i><strong>Prioridad:</strong> ${this.value==1?'Baja':this.value==2?'Media':'Alta'}`">
                                    <option value="1" selected>Baja</option>
                                    <option value="2">Media</option>
                                    <option value="3">Alta</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="updatePreviewStatus"><i class="fa fa-check-circle"></i>
                                    Estado</label>
                                <select class="form-control" id="updatePreviewStatus" name="updatePreviewStatus">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Vista previa -->
                    <div class="col-md-6">
                        <h2 class="text-center mb-2">Vista previa</h2>
                        <div class="modal-content shadow-lg rounded-lg">
                            <div class="bg-primary text-white p-3 rounded-top" id="updatePreviewNotification">
                                <h5 class="modal-title mb-0" id="updatePreviewTitlePrincipal">
                                    <i class="fa fa-bell mr-2"></i> Notificación Importante
                                </h5>
                            </div>

                            <div class="modal-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <span class="fa-stack fa-2x text-primary" id="updatePreviewIconContainer">
                                            <i class="fa fa-circle fa-stack-2x text-primary"
                                                id="updatePreviewIconCircle""></i>
                                            <i class=" fa fa-envelope fa-stack-1x fa-inverse"
                                                id="updatePreviewIcon"></i>
                                        </span>
                                    </div>
                                    <div id="notificacionHeader">
                                        <h4 class="mb-1 text-dark font-weight-bold" id="updatePreviewTitle">Lorem ipsum
                                            dolor
                                            sit amet
                                            consectetur adipisicing elit. Voluptatum, recusandae.</h4>
                                        <span class="badge badge-primary" id="updatePreviewType">Tipo: info</span>
                                        <small class="text-muted d-block mt-1">Fecha: Hace 1 hora</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="text-dark" style="white-space: pre-wrap; word-break: break-word;"
                                        id="updatePreviewText">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                        Dolore
                                        recusandae facilis dolorum mollitia non assumenda!
                                    </p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p id="updatePreviewPriority"><i
                                                class="fa fa-flag text-danger mr-2"></i><strong>Prioridad:</strong> Baja
                                        </p>
                                        <p><i class="fa fa-times text-danger mr-2"></i><strong>Leída:</strong>
                                            No</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fa fa-info-circle text-info mr-2"></i><strong>Estado:</strong>
                                            Activo
                                        </p>
                                        <p><i class="fa fa-hashtag text-dark mr-2"></i><strong>ID:</strong> #1234</p>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded border">
                                    <a href="#" target="_blank" id="updatePreviewLink">https://mipagina.com/info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin vista previa -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-fw fa-lg fa-pencil"></i>
                    Actualizar</button>
                <button type="button" class="btn btn-secondary btn-close-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>