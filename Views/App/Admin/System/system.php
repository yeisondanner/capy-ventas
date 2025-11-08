<?= headerAdmin($data) ?>
<?php
    $systemInfo = getSystemInfo();
    $loaderType = isset($systemInfo['c_typeLoader']) ? (int) $systemInfo['c_typeLoader'] : 1;
    $durationLock = isset($systemInfo['c_duration_lock']) ? (int) $systemInfo['c_duration_lock'] : 0;
    $emailEncryption = $systemInfo['c_email_encryption'] ?? 'ssl';
    $loaderLabels = [
        1 => 'Default',
        2 => 'Spinning Circle',
        3 => 'Bouncing Dots',
        4 => 'Sliding Bar',
        5 => 'Pulse',
        6 => 'Dual Ring',
        7 => 'Ripple',
        8 => 'Circle Dots',
        9 => 'Growing Bars',
        10 => 'Flip Box',
        11 => 'Fade Circle',
        12 => 'Rotate Square',
        13 => 'Moving Lines',
    ];
?>
<main class="app-content system-module">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-gear"></i> <?= $data['page_title'] ?></h1>
            <p><?= $data['page_description'] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-gear fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/<?= $data['page_view'] ?>"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="card shadow-sm border-0 mb-4 system-intro">
        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center">
            <div class="flex-grow-1">
                <h4 class="mb-2 text-primary"><i class="fa fa-life-ring mr-2"></i>Guía rápida</h4>
                <p class="mb-0 text-muted">
                    Actualice cada bloque con la información institucional oficial. Puede guardar la configuración desde
                    cualquier sección; todos los campos se envían en un único formulario.
                </p>
            </div>
            <div class="mt-3 mt-lg-0 ml-lg-4 text-muted small">
                <i class="fa fa-clock-o mr-1"></i>
                Última actualización: <?= $systemInfo['c_updateDate'] ?? 'Sin registros' ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-4 mb-4">
            <aside class="card shadow-sm border-0 system-sidebar">
                <div class="card-body text-center">
                    <div class="system-logo-wrapper mb-3">
                        <img class="img-fluid" src="<?= base_url() ?>/loadfile/icon?f=<?= (!empty($systemInfo['c_logo'])) ? $systemInfo['c_logo'] : 'sin-content.png'; ?>"
                            alt="Logotipo institucional">
                    </div>
                    <h5 class="mb-1">
                        <?= !empty($systemInfo['c_name']) ? $systemInfo['c_name'] : getSystemName(); ?>
                    </h5>
                    <p class="text-muted small mb-3">
                        <?= !empty($systemInfo['c_description']) ? $systemInfo['c_description'] : 'Sin descripción registrada.'; ?>
                    </p>
                    <div class="d-flex justify-content-center align-items-center mb-3 system-color-palette">
                        <div class="system-color-chip" style="background-color: <?= $systemInfo['c_color_primary'] ?? '#007bff'; ?>;"
                            title="Color primario"></div>
                        <div class="system-color-chip" style="background-color: <?= $systemInfo['c_color_secondary'] ?? '#6c757d'; ?>;"
                            title="Color secundario"></div>
                    </div>
                    <ul class="list-unstyled text-left small mb-0">
                        <li class="mb-2"><i class="fa fa-building mr-2 text-primary"></i><?= $systemInfo['c_company_name'] ?? 'Nombre institucional no registrado'; ?>
                        </li>
                        <li class="mb-2"><i class="fa fa-map-marker mr-2 text-primary"></i><?= $systemInfo['c_address'] ?? 'Dirección no registrada'; ?>
                        </li>
                        <li class="mb-2"><i class="fa fa-phone mr-2 text-primary"></i><?= $systemInfo['c_phone'] ?? 'Teléfono no registrado'; ?>
                        </li>
                        <li><i class="fa fa-envelope mr-2 text-primary"></i><?= $systemInfo['c_mail'] ?? 'Correo no registrado'; ?></li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <p class="text-uppercase text-muted small mb-2">Ir a la sección</p>
                    <nav class="nav flex-column system-nav">
                        <a class="system-nav__link active" href="#system-identity"><i class="fa fa-info-circle mr-2"></i>Identidad del
                            sistema</a>
                        <a class="system-nav__link" href="#system-institution"><i class="fa fa-building mr-2"></i>Datos
                            institucionales</a>
                        <a class="system-nav__link" href="#system-api"><i class="fa fa-cloud mr-2"></i>Servicios externos</a>
                        <a class="system-nav__link" href="#system-email"><i class="fa fa-envelope-open mr-2"></i>Correo electrónico</a>
                        <a class="system-nav__link" href="#system-preferences"><i class="fa fa-sliders mr-2"></i>Personalización</a>
                    </nav>
                </div>
            </aside>
        </div>
        <div class="col-xl-9 col-lg-8">
            <form id="formSave" class="system-form" enctype="multipart/form-data" autocomplete="off">
                <?= csrf() ?>
                <section id="system-identity" class="card shadow-sm border-0 mb-4 system-section">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="fa fa-info-circle mr-2"></i>Identidad del Sistema</h4>
                            <p class="mb-0 text-muted">Logotipo, nombre y descripción visibles en toda la plataforma.</p>
                        </div>
                        <span class="badge badge-pill badge-primary mt-3 mt-md-0">Paso 1</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="logo" class="font-weight-semibold">Logo del Sistema</label>
                                    <div class="custom-file overflow-hidden">
                                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/png, image/jpeg"
                                            onchange="previewLogo(event)">
                                        <label class="custom-file-label" for="logo">Seleccione un archivo (PNG o JPG)</label>
                                    </div>
                                    <small class="form-text text-muted">Resolución recomendada: 256 x 256 px, fondo transparente.</small>
                                    <div class="mt-3 text-center">
                                        <div class="system-logo-preview">
                                            <img id="logoPreview" class="img-fluid" style="max-height: 120px;"
                                                src="<?= base_url() ?>/loadfile/icon?f=<?= (!empty($systemInfo['c_logo'])) ? $systemInfo['c_logo'] : 'sin-content.png'; ?>"
                                                alt="Vista previa del logo">
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($systemInfo['c_logo'])) { ?>
                                    <input type="hidden" name="profile_exist" id="profile_exist" value="<?= $systemInfo['c_logo'] ?>">
                                <?php } ?>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="nombreSistema" class="font-weight-semibold">Nombre del Sistema <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconName"><i class="fa fa-book" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="nombreSistema" name="nombreSistema" class="form-control"
                                            placeholder="Ej: Sistema de Gestión Académica" required
                                            value="<?= $systemInfo ? $systemInfo['c_name'] : getSystemName(); ?>" aria-describedby="iconName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="font-weight-semibold">Descripción Institucional</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                        pattern="^[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+$"
                                        placeholder="Breve descripción sobre el propósito del sistema"><?= $systemInfo ? $systemInfo['c_description'] : getSystemName(); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Guardar configuración de identidad
                        </button>
                    </div>
                </section>

                <section id="system-institution" class="card shadow-sm border-0 mb-4 system-section">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="fa fa-building mr-2"></i>Datos Institucionales</h4>
                            <p class="mb-0 text-muted">Información oficial que se mostrará en documentos y reportes.</p>
                        </div>
                        <span class="badge badge-pill badge-primary mt-3 mt-md-0">Paso 2</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtNameInsitution" class="font-weight-semibold">Razón Social <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconNameCompany"><i class="fa fa-university" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtNameInsitution" name="txtNameInsitution" class="form-control"
                                            placeholder="Ej: Universidad Tecnológica del Perú" required
                                            value="<?= $systemInfo['c_company_name'] ?? ''; ?>" aria-describedby="iconNameCompany">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtRuc" class="font-weight-semibold">RUC <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconNameRUC"><i class="fa fa-id-card" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtRuc" name="txtRuc" class="form-control" placeholder="Ej: 20123456781" required
                                            value="<?= $systemInfo['c_ruc'] ?? ''; ?>" aria-describedby="iconNameRUC">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtAddress" class="font-weight-semibold">Dirección Fiscal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconAddress"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtAddress" name="txtAddress" class="form-control" placeholder="Ej: Av. Arequipa 123, Lima" required
                                            value="<?= $systemInfo['c_address'] ?? ''; ?>" aria-describedby="iconAddress">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtPhone" class="font-weight-semibold">Teléfono/Celular <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconPhone"><i class="fa fa-phone" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtPhone" name="txtPhone" class="form-control"
                                            placeholder="Ej: (01) 1234567 | 987654321" required value="<?= $systemInfo['c_phone'] ?? ''; ?>"
                                            aria-describedby="iconPhone">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="txtMail" class="font-weight-semibold">Correo Institucional <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconMail"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtMail" name="txtMail" class="form-control"
                                            placeholder="Ej: contacto@institucion.edu.pe" required value="<?= $systemInfo['c_mail'] ?? ''; ?>"
                                            aria-describedby="iconMail">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Guardar datos institucionales
                        </button>
                    </div>
                </section>

                <section id="system-api" class="card shadow-sm border-0 mb-4 system-section">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="fa fa-cloud mr-2"></i>Conexión con Servicios Externos</h4>
                            <p class="mb-0 text-muted">Credenciales de acceso para consultas RENIEC y SUNAT.</p>
                        </div>
                        <span class="badge badge-pill badge-primary mt-3 mt-md-0">Paso 3</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fa fa-shield mr-2"></i>
                            <span>Guarde esta información en un lugar seguro. Se recomienda actualizar las claves con
                                frecuencia y revocar accesos que no utilice.</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtUserAPi" class="font-weight-semibold">Usuario API <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconUserApi"><i class="fa fa-user-secret" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtUserAPi" name="txtUserAPi" class="form-control"
                                            placeholder="Usuario proporcionado por el servicio" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_user_api_reniec_sunat'] ?? '') : getSystemName(); ?>"
                                            aria-describedby="iconUserApi">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtPasswordApi" class="font-weight-semibold">Contraseña API <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="cursor: pointer;">
                                            <span class="input-group-text" id="iconPasswordApi"
                                                onclick="(txtPasswordApi.type=='password')?(txtPasswordApi.type='text'):(txtPasswordApi.type='password')"><i class="fa fa-eye"
                                                    aria-hidden="true"></i></span>
                                        </div>
                                        <input type="password" id="txtPasswordApi" name="txtPasswordApi" class="form-control"
                                            placeholder="Contraseña del servicio API" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_password_api_reniec_sunat'] ?? '') : getSystemName(); ?>"
                                            aria-describedby="iconPasswordApi">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="txtKeyApi" class="font-weight-semibold">Llave de Acceso API <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="cursor: pointer;">
                                            <span class="input-group-text" id="iconKeyApi"
                                                onclick="(txtKeyApi.type=='password')?(txtKeyApi.type='text'):(txtKeyApi.type='password')"><i class="fa fa-key"
                                                    aria-hidden="true"></i></span>
                                        </div>
                                        <input type="password" id="txtKeyApi" name="txtKeyApi" class="form-control"
                                            placeholder="Token o llave de autenticación" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_key_api_reniec_sunat'] ?? '') : getSystemName(); ?>"
                                            aria-describedby="iconKeyApi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Guardar credenciales API
                        </button>
                    </div>
                </section>

                <section id="system-email" class="card shadow-sm border-0 mb-4 system-section">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="fa fa-envelope-open mr-2"></i>Configuración de Correo Electrónico</h4>
                            <p class="mb-0 text-muted">Parámetros SMTP para el envío automático de notificaciones.</p>
                        </div>
                        <span class="badge badge-pill badge-primary mt-3 mt-md-0">Paso 4</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtpHost" class="font-weight-semibold">Servidor SMTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconSmtpHost"><i class="fa fa-server"></i></span>
                                        </div>
                                        <input type="text" id="smtpHost" name="smtpHost" class="form-control" placeholder="Ej: smtp.mi-dominio.com" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_email_server_smtp'] ?? '') : ''; ?>"
                                            aria-describedby="iconSmtpHost">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="smtpPort" class="font-weight-semibold">Puerto <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconSmtpPort"><i class="fa fa-plug"></i></span>
                                        </div>
                                        <input type="number" id="smtpPort" name="smtpPort" class="form-control" placeholder="Ej: 465" required min="1" max="9999"
                                            value="<?= $systemInfo['c_email_port'] ?? ''; ?>" aria-describedby="iconSmtpPort">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="smtpEncryption" class="font-weight-semibold">Cifrado <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconSmtpEncryption"><i class="fa fa-lock"></i></span>
                                        </div>
                                        <select id="smtpEncryption" name="smtpEncryption" class="form-control" aria-describedby="iconSmtpEncryption">
                                            <option value="ssl" <?= ($emailEncryption === 'ssl') ? 'selected' : '' ?>>SSL</option>
                                            <option value="tls" <?= ($emailEncryption === 'tls') ? 'selected' : '' ?>>TLS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtpUsername" class="font-weight-semibold">Usuario SMTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconSmtpUsername"><i class="fa fa-user-circle"></i></span>
                                        </div>
                                        <input type="text" id="smtpUsername" name="smtpUsername" class="form-control"
                                            placeholder="Ej: usuario@dominio.com" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_email_user_smtp'] ?? '') : ''; ?>"
                                            aria-describedby="iconSmtpUsername">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtpPassword" class="font-weight-semibold">Contraseña SMTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="cursor: pointer;">
                                            <span class="input-group-text" id="iconSmtpPassword"
                                                onclick="(smtpPassword.type=='password')?(smtpPassword.type='text'):(smtpPassword.type='password')">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                        <input type="password" id="smtpPassword" name="smtpPassword" class="form-control"
                                            placeholder="Contraseña del correo" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_email_password_smtp'] ?? '') : ''; ?>"
                                            aria-describedby="iconSmtpPassword">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromEmail" class="font-weight-semibold">Correo Remitente <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconFromEmail"><i class="fa fa-paper-plane"></i></span>
                                        </div>
                                        <input type="email" id="fromEmail" name="fromEmail" class="form-control"
                                            placeholder="Ej: no-reply@dominio.com" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_email_sender'] ?? '') : ''; ?>"
                                            aria-describedby="iconFromEmail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromName" class="font-weight-semibold">Nombre Remitente <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconFromName"><i class="fa fa-id-badge"></i></span>
                                        </div>
                                        <input type="text" id="fromName" name="fromName" class="form-control"
                                            placeholder="Ej: Soporte Técnico" required
                                            value="<?= $systemInfo ? decryption($systemInfo['c_email_sender_name'] ?? '') : ''; ?>"
                                            aria-describedby="iconFromName">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Guardar configuración de correo
                        </button>
                    </div>
                </section>

                <section id="system-preferences" class="card shadow-sm border-0 mb-4 system-section">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="fa fa-sliders mr-2"></i>Personalización del Sistema</h4>
                            <p class="mb-0 text-muted">Ajuste colores, tiempos de bloqueo y estilo de loader.</p>
                        </div>
                        <span class="badge badge-pill badge-primary mt-3 mt-md-0">Paso 5</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtColorPrimary" class="font-weight-semibold">Color Principal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconColorPrimary"><i class="fa fa-tint" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="color" name="txtColorPrimary" id="txtColorPrimary" class="form-control"
                                            value="<?= $systemInfo['c_color_primary'] ?? '#007bff'; ?>" aria-describedby="iconColorPrimary">
                                    </div>
                                    <small class="form-text text-muted">Color dominante de la interfaz.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtColorSecondary" class="font-weight-semibold">Color Secundario <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconColorSecondary"><i class="fa fa-tint" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="color" name="txtColorSecondary" id="txtColorSecondary" class="form-control"
                                            value="<?= $systemInfo['c_color_secondary'] ?? '#6c757d'; ?>" aria-describedby="iconColorSecondary">
                                    </div>
                                    <small class="form-text text-muted">Color para elementos secundarios.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtDurationLock" class="font-weight-semibold">Tiempo de Inactividad (minutos) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconDutationLock"><i class="fa fa-hourglass-half" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="number" id="txtDurationLock" name="txtDurationLock" class="form-control" required min="0" max="9999"
                                            value="<?= $durationLock > 0 ? $durationLock / 60 : 0; ?>" aria-describedby="iconDutationLock">
                                    </div>
                                    <small class="form-text text-muted">0 = Desactiva el bloqueo automático.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtTextLoader" class="font-weight-semibold">Mensaje de Carga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iconLoaderText"><i class="fa fa-comment" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="txtTextLoader" name="txtTextLoader" class="form-control"
                                            value="<?= $systemInfo ? $systemInfo['c_textLoader'] : 'Espere un momento...'; ?>" aria-describedby="iconLoaderText">
                                    </div>
                                    <small class="form-text text-muted">Texto que aparece durante la carga.</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 class="mb-3 text-center text-primary"><i class="fa fa-circle-o-notch mr-2"></i>Estilo de Indicador de Carga</h5>
                            <p class="text-center text-muted mb-4">Seleccione la animación que se mostrará mientras el sistema procesa información.</p>
                            <div class="row loaders">
                                <?php for ($i = 1; $i <= 13; $i++) {
                                    $label = $loaderLabels[$i] ?? ('Loader ' . $i);
                                ?>
                                    <div class="col-sm-6 col-lg-4 mb-4">
                                        <label class="card text-center p-3 loader-option <?= ($loaderType === $i) ? 'selected' : ''; ?>"
                                            for="loader-<?= $i ?>">
                                            <input type="radio" name="rdLoaderSelect" id="loader-<?= $i ?>" class="loader-radio" hidden value="<?= $i ?>"
                                                <?= ($loaderType === $i) ? 'checked' : ''; ?>>
                                            <?= getLoader($i); ?>
                                            <div class="loader-title"><?= $label ?></div>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Guardar preferencias del sistema
                        </button>
                    </div>
                </section>
            </form>
        </div>
    </div>
</main>
<?= footerAdmin($data) ?>
