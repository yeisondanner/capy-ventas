<?php
headerPos($data);
$dataBusines = $data['sesion_posbusiness_active'];
$validationDelete = (validate_permission_app(8, "d", false)) ? (int) validate_permission_app(8, "d", false)['delete'] : 0;
?>

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
        <form id="businessForm" class="needs-validation" novalidate autocomplete="off" enctype="multipart/form-data">
            <?= csrf(true, 1); ?>
            <div class="row g-4 align-items-stretch">
                <!-- Columna Izquierda: Identidad y Opciones Avanzadas -->
                <div class="col-lg-4 d-flex flex-column">
                    <!-- Tarjeta de Identidad -->
                    <div class="card-pos mb-4">
                        <div class="card-header-pos">
                            <h5 class="card-title-pos"><i class="bi bi-image"></i> Identidad Visual</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="logo-upload-area mb-3" onclick="document.getElementById('update_logoInput').click()">
                                <img src="<?= $data['logoBusiness'] ?>" id="logoPreview" class="logo-preview-img mb-2" alt="Logo">
                                <div class="text-primary fw-medium small"><i class="bi bi-cloud-upload me-1"></i> Cambiar Logo</div>
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">Click para subir (Max 2MB)</div>
                            </div>
                            <input type="file" class="d-none" id="update_logoInput" name="update_logoInput" accept="image/*">
                        </div>
                    </div>

                    <!-- Tarjeta de Opciones / Danger Zone -->
                    <!-- AÑADIDO: clases flex-grow-1 y d-flex para que ocupe el resto de la altura -->
                    <div class="card-pos flex-grow-1 d-flex flex-column">
                        <div class="card-header-pos">
                            <h5 class="card-title-pos"><i class="bi bi-sliders"></i> Opciones Avanzadas</h5>
                        </div>
                        <!-- AÑADIDO: d-flex flex-column en el body para poder usar mt-auto abajo -->
                        <div class="card-body p-4 d-flex flex-column">

                            <!-- Apertura de Caja -->
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border border-light mb-4">
                                <div>
                                    <label class="form-check-label fw-semibold text-dark d-block" for="update_openBoxSwitch"><i class="bi bi-cash-coin me-1"></i> Apertura de Caja</label>
                                    <small class="text-muted" style="font-size: 0.8rem;">Requerir inicio de turno</small>
                                </div>
                                <div class="form-check form-switch m-0 ">
                                    <input class="form-check-input custom-switch" type="checkbox" id="update_openBoxSwitch" name="update_openBoxSwitch" <?= $dataBusines['openBox'] == 'Si' ? 'checked'  : ''; ?>>
                                </div>
                            </div>

                            <hr class="border-secondary opacity-25 my-4">
                            <!-- Zona de Peligro: Eliminar Negocio -->
                            <!-- AÑADIDO: mt-auto para empujar esto siempre al fondo de la tarjeta -->
                            <?php if ($validationDelete == 1): ?>
                                <div class="mt-auto">
                                    <label class="form-label d-block text-danger small fw-bold text-uppercase mb-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Zona de Peligro
                                    </label>
                                    <div class="danger-zone-bg mb-3">
                                        <p class="small text-muted mb-0" style="line-height: 1.4; font-size: 0.8rem;">
                                            Esta acción eliminará permanentemente el negocio y todos sus datos asociados.
                                        </p>
                                    </div>
                                    <button type="button" id="deleteBusiness" data-id="<?= $dataBusines['idBusiness']; ?>" data-token="<?= csrf(false, 1); ?>" data-name="<?= $dataBusines['business']; ?>" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 fw-medium">
                                        <i class="bi bi-trash3"></i> Eliminar Negocio
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Columna Derecha: Datos del Formulario -->
                <div class="col-lg-8">
                    <div class="card-pos h-100">
                        <div class="card-header-pos d-flex justify-content-between align-items-center">
                            <h5 class="card-title-pos"><i class="bi bi-building-gear"></i> Datos Generales</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Sección 1 -->
                            <div class="section-header mt-0">Información Principal</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="update_name">Nombre Comercial <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                                        <input type="text" class="form-control" name="update_name" id="update_name" required placeholder="Ej. Market Juanita" value="<?= $dataBusines['business']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="update_slctTypeBusiness">Tipo de Negocio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                        <select class="form-select businessType" name="update_slctTypeBusiness" required id="update_slctTypeBusiness">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="update_documentNumber">RUC / NIT / Documento <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                        <input type="text" class="form-control font-monospace" name="update_documentNumber" id="update_documentNumber" required placeholder="00000000000" maxlength="11" value="<?= $dataBusines['document_number']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="update_email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="update_email" id="update_email" required placeholder="admin@negocio.com" value="<?= $dataBusines['email']; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 2 -->
                            <div class="section-header">Ubicación y Contacto</div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="update_country">País <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-globe-americas"></i></span>
                                        <input type="text" class="form-control" name="update_country" id="update_country" placeholder="Perú" value="<?= $dataBusines['country']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="update_telephone">Teléfono <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control text-center bg-white" name="update_telephone_prefix" id="update_telephonePrefix" value="<?= $dataBusines['telephone_prefix']; ?>" style="max-width: 65px;" required>
                                        <input type="tel" class="form-control" name="update_telephone" id="update_telephone" required placeholder="999 000 000" value="<?= $dataBusines['phone_number']; ?>">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label" for="update_city">Ciudad</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-buildings"></i></span>
                                        <input type="text" class="form-control" name="update_city" id="update_city" placeholder="Lima" value="<?= $dataBusines['city']; ?>">
                                    </div>
                                </div>
                                <div class="col-6 col-md-8">
                                    <label class="form-label" for="update_direction">Dirección Fiscal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" class="form-control" name="update_direction" id="update_direction" placeholder="Calle Principal 123" value="<?= $dataBusines['direction']; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 3 -->
                            <div class="section-header">Configuración Fiscal</div>
                            <div class="row g-3 align-items-end">
                                <div class="col-6 col-md-6">
                                    <label class="form-label" for="update_taxname">Nombre Impuesto <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" class="form-control text-uppercase" name="update_taxname" id="update_taxname" value="<?= $dataBusines['taxname']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label" for="update_tax">Tasa (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                        <input type="number" class="form-control text-end fw-bold" name="update_tax" id="update_tax" value="<?= $dataBusines['tax']; ?>" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Acciones -->
                            <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" id="btnUpdateBusiness">
                                    <i class="bi bi-floppy"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<script>
    const typeBusiness = "<?= $dataBusines['idBusinessType']; ?>";
</script>
<?= footerPos($data) ?>