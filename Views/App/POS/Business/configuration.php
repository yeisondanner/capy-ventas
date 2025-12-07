<?php
headerPos($data);
$dataBusines = $data['sesion_posbusiness_active'];
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
        <?= csrf(true, 1); ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="update_businessType" class="form-label">Tipo de negocio <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <select class="form-select businessType" id="update_businessType" name="update_businessType" required>
                        <option value="" selected disabled>Selecciona un tipo de negocio</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label for="update_businessName" class="form-label">Nombre del negocio <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shop"></i></span>
                    <input type="text" class="form-control" id="update_businessName" name="update_businessName" maxlength="255" required placeholder="Ingresa el nombre comercial">
                </div>
            </div>
            <div class="col-md-6">
                <label for="update_businessDocument" class="form-label">Número de documento <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                    <input type="text" class="form-control" id="update_businessDocument" name="update_businessDocument" maxlength="11" required placeholder="RUC o documento">
                </div>
            </div>
            <div class="col-md-6">
                <label for="update_businessEmail" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="update_businessEmail" name="update_businessEmail" maxlength="255" required placeholder="correo@ejemplo.com">
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="businessTelephonePrefix" class="form-label">Prefijo telefónico <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone-plus"></i></span>
                    <input type="text" class="form-control" id="businessTelephonePrefix" name="businessTelephonePrefix" maxlength="7" required placeholder="+51" value="+51">
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <label for="businessCountry" class="form-label">País</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control" id="businessCountry" value="PERU" onkeyup="this.value = this.value.toUpperCase()" name="businessCountry" maxlength="100" placeholder="País del negocio">
                </div>
            </div>
            <div class="col-md-12 col-lg-8">
                <label for="businessPhone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" class="form-control" id="businessPhone" name="businessPhone" maxlength="11" required placeholder="Número de contacto">
                </div>
            </div>
            <div class="col-md-6">
                <label for="businessCity" class="form-label">Ciudad</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo"></i></span>
                    <input type="text" class="form-control" id="businessCity" onkeyup="this.value = this.value.toUpperCase()" name="businessCity" maxlength="250" placeholder="Ciudad o provincia">
                </div>
            </div>
            <div class="col-md-6">
                <label for="businessDirection" class="form-label">Dirección</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-signpost-2"></i></span>
                    <input type="text" class="form-control" id="businessDirection" onkeyup="this.value = this.value.toUpperCase()" name="businessDirection" placeholder="Dirección comercial">
                </div>
            </div>
        </div>
        <p class="text-muted small mt-3 mb-0">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
    </div>
</main>
<?= footerPos($data) ?>