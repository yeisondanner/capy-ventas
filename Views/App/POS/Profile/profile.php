<?= headerPos($data) ?>
<?php
// Función auxiliar para evitar avisos al formatear fechas
function formatDateProfile(?string $value, bool $withTime = true): string
{
    if (empty($value)) {
        return 'Sin registrar';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return $withTime ? date('d/m/Y H:i', $timestamp) : date('d/m/Y', $timestamp);
}

$user         = $data['user'] ?? [];

$subscription = $data['subscription'] ?? [];
$invoices     = $data['invoices'] ?? [];
$businesses   = $data['businesses'] ?? [];
$avatarName   = urlencode($user['fullname'] ?? 'Usuario');
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="bi bi-person-circle"></i> Perfil</h1>
            <p>Consulta tu identidad, facturación y plan activo.</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/pos/dashboard">Inicio</a></li>
            <li class="breadcrumb-item active">Perfil</li>
        </ul>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="tile profile-card h-100">
                <div class="tile-title-w-btn d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="tile-title mb-0">Datos del usuario</h3>
                        <small class="text-muted">Información básica de tu cuenta</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= ($user['status'] ?? '') === 'Activo' ? 'success' : 'secondary' ?>">
                            <?= htmlspecialchars($user['status'] ?? 'Desconocido', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="avatar-wrapper">
                        <img src="<?= GENERAR_PERFIL . $avatarName; ?>" class="rounded-circle shadow-sm profile-avatar" alt="Avatar">
                    </div>
                    <div>
                        <h5 class="mb-1" id="profile-fullname"><?= htmlspecialchars($user['fullname'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="mb-0 text-muted">Usuario: <strong id="profile-username"><?= htmlspecialchars($user['user'] ?? 'Sin usuario', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p class="mb-0 text-muted">Correo: <strong id="profile-email"><?= htmlspecialchars($user['email'] ?? 'Sin correo', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-6">
                        <p class="mb-1 text-muted">País</p>
                        <p class="mb-0 fw-semibold" id="profile-country"><?= htmlspecialchars($user['country'] ?? 'Sin país', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Teléfono</p>
                        <p class="mb-0 fw-semibold" id="profile-phone"><?= htmlspecialchars($user['phone_full'] ?? 'Sin teléfono', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Nacimiento</p>
                        <p class="mb-0 fw-semibold" id="profile-birthdate"><?= formatDateProfile($user['birthDate'] ?? null, false); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Fecha de expiración plan</p>
                        <p class="mb-0 fw-semibold"><?= formatDateProfile($user['planExpiresAt'] ?? null, false); ?></p>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditProfile">
                            <i class="bi bi-pencil-square"></i> Editar Perfil
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="tile h-100">
                <div class="tile-title-w-btn d-flex align-items-center">
                    <div>
                        <h3 class="tile-title mb-0">Plan y facturación</h3>
                        <small class="text-muted">Resumen de tu suscripción</small>
                    </div>
                    <span class="badge bg-info text-dark text-uppercase"><?= htmlspecialchars($subscription['status'] ?? 'sin datos', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="info-box border border-primary">
                            <p class="text-muted mb-1">Plan</p>
                            <h5 class="mb-0"><?= htmlspecialchars($subscription['plan'] ?? 'Sin plan', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <small class="text-muted text-uppercase"><?= htmlspecialchars($subscription['billingPeriod'] ?? 'sin periodo', ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box border border-warning">
                            <p class="text-muted mb-1">Monto por ciclo</p>
                            <h5 class="mb-0">
                                <?php if (!empty($subscription['price'])): ?>
                                    <?= getCurrency(); ?> <?= number_format((float) $subscription['price'], 2); ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin costo</span>
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted">Renovación <?= !empty($subscription['autoRenew']) ? 'automática' : 'manual'; ?></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box border border-success">
                            <p class="text-muted mb-1">Descuento</p>
                            <h5 class="mb-0"><?= htmlspecialchars($subscription['discount'] ?? 'Sin descuento', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <small class="text-muted">Aplicado al ciclo</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Inicio</p>
                        <p class="fw-semibold mb-0 text-primary"><i class="bi bi-calendar4-event"></i> <?= formatDateProfile($subscription['startDate'] ?? null); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Próxima facturación</p>
                        <p class="fw-semibold mb-0 text-info"><i class="bi bi-credit-card-2-back"></i> <?= formatDateProfile($subscription['nextBilling'] ?? null); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Fin de ciclo</p>
                        <p class="fw-semibold mb-0 text-danger"><i class="bi bi-calendar4-event"></i> <?= formatDateProfile($subscription['endDate'] ?? null); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de la sección de suscripción -->
    <!-- Inicio de la sección de historial de facturación -->
    <div class="tile mt-4">
        <div class="tile-title-w-btn d-flex align-items-center">
            <div>
                <h3 class="tile-title mb-0"><i class="bi bi-file-text"></i> Historial de Facturación</h3>
                <small class="text-muted">Registro de tus suscripciones y renovaciones</small>
            </div>
            <span class="badge bg-secondary"><?= count($invoices); ?> registros</span>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Boleta</th>
                        <th>Plan</th>
                        <th>Periodo</td>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay historial de facturación disponible.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($invoice['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($invoice['plan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($invoice['billingPeriod'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= formatDateProfile($invoice['startDate'] ?? null); ?></td>
                                <td><?= formatDateProfile($invoice['endDate'] ?? null); ?></td>
                                <td>
                                    <?= getCurrency(); ?> <?= number_format((float) $invoice['subtotal'], 2); ?>
                                </td>
                                <td> <?= getCurrency(); ?> <?= htmlspecialchars($invoice['discount'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?= getCurrency(); ?> <?= number_format((float) $invoice['total'], 2); ?>
                                </td>
                                <td>
                                    <span class="badge <?= $invoice['status'][1] ?>">
                                        <i class="bi <?= $invoice['status'][2] ?>"></i>
                                        <?= htmlspecialchars($invoice['status'][0], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Fin de la sección de historial de facturación -->

    <div class="tile mt-4">
        <div class="tile-title-w-btn d-flex align-items-center">
            <div>
                <h3 class="tile-title mb-0"> <i class="bi bi-building"></i> Dueño de negocios</h3>
                <small class="text-muted">Contexto comercial de tu cuenta</small>
            </div>
            <span class="badge bg-secondary"><?= count($businesses); ?> en total</span>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Categoría</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($businesses)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Sin negocios asociados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($businesses as $business): ?>
                            <tr>
                                <td><?= htmlspecialchars($business['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($business['document'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($business['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($business['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <span class="badge bg-<?= ($business['status'] ?? '') === 'Activo' ? 'success' : 'secondary'; ?>">
                                        <?= htmlspecialchars($business['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?= formatDateProfile($business['registered'] ?? null); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- GEMINASO, DISEÑO DE EDITAR PERIL :) -->
<div class="modal fade" id="modalEditProfile" tabindex="-1" aria-labelledby="modalEditProfileLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <!-- Encabezado -->
            <div class="modal-header bg-success text-white py-3 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-bounding-box fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold" id="modalEditProfileLabel">Mi Perfil</h5>
                        <p class="mb-0 text-white-50 small">Actualiza tus datos y privacidad</p>
                    </div>
                </div>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditProfile" method="post" action="<?= base_url() ?>/pos/profile/updateProfile" autocomplete="off">
                <div class="modal-body p-4 bg-light bg-opacity-25">
                    <!-- Navegación-->
                    <div class="d-flex justify-content-center mb-4">
                        <ul class="nav nav-pills bg-white rounded-pill p-1 shadow-sm border" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-content" type="button" role="tab">
                                    <i class="bi bi-person me-2"></i>Datos Personales
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-content" type="button" role="tab">
                                    <i class="bi bi-shield-lock me-2"></i>Seguridad
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="profileTabsContent">
                        <!-- DATOS PERSONALES -->
                        <div class="tab-pane fade show active" id="personal-content" role="tabpanel">
                            <div class="row g-4">
                                <!-- Tarjeta (en responsive arriba, en lg a la derecha) -->
                                <div class="col-lg-4">
                                    <div class="card border-0 text-white h-100 rounded-4 shadow-sm"
                                        style="background: linear-gradient(160deg, #198754 0%, #0f5132 100%);">
                                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                                            <div class="bg-white bg-opacity-25 rounded-circle p-3 mb-3">
                                                <i class="bi bi-person-badge fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold">Tus Datos</h5>
                                            <p class="small text-white-50 mb-0">Mantén tu información actualizada para una mejor experiencia.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Formulario (en responsive abajo, en lg a la izquierda) -->
                                <div class="col-lg-8">
                                    <div class="bg-white p-4 rounded-4 shadow-sm border border-light h-100">
                                        <h6 class="text-success fw-bold text-uppercase small mb-3 ls-1">Información Básica</h6>

                                        <div class="row g-3">
                                            <!-- Nombres -->
                                            <div class="col-md-6">
                                                <label for="names" class="form-label fw-semibold text-dark small ps-2">Nombres</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-person-fill"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="names"
                                                        name="names"
                                                        placeholder="Ej: Juan Carlos"
                                                        value="<?= htmlspecialchars(explode(' ', $user['fullname'] ?? '')[0] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            <!-- Apellidos -->
                                            <div class="col-md-6">
                                                <label for="lastnames" class="form-label fw-semibold text-dark small ps-2">Apellidos</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-person-vcard-fill"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="lastnames"
                                                        name="lastnames"
                                                        placeholder="Ej: Pérez Díaz"
                                                        value="<?= htmlspecialchars(isset($user['fullname']) ? implode(' ', array_slice(explode(' ', $user['fullname']), 1)) : '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            <!-- Email -->
                                            <div class="col-12">
                                                <label for="email" class="form-label fw-semibold text-dark small ps-2">Correo Electrónico</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-envelope-fill"></i></span>
                                                    <input
                                                        type="email"
                                                        class="form-control"
                                                        id="email"
                                                        name="email"
                                                        placeholder="correo@dominio.com"
                                                        value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <hr class="text-muted opacity-25 my-2">
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-6">
                                                <label for="birthDate" class="form-label fw-semibold text-dark small ps-2">Fecha de Nacimiento</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-calendar-event-fill"></i></span>
                                                    <input
                                                        type="date"
                                                        class="form-control"
                                                        id="birthDate"
                                                        name="birthDate"
                                                        value="<?= !empty($user['birthDate']) ? date('Y-m-d', strtotime($user['birthDate'])) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-6">
                                                <label for="country" class="form-label fw-semibold text-dark small ps-2">País</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-geo-alt-fill"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="country"
                                                        name="country"
                                                        value="<?= htmlspecialchars($user['country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        disabled>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-6">
                                                <label for="prefix" class="form-label fw-semibold text-dark small ps-2">Prefijo</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-telephone-plus-fill"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control text-center"
                                                        id="prefix"
                                                        name="prefix"
                                                        value="<?= htmlspecialchars($user['prefix'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        disabled>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 col-lg-6">
                                                <label for="phone" class="form-label fw-semibold text-dark small ps-2">Número de Teléfono</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text ps-3"><i class="bi bi-phone-fill"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control fw-bold"
                                                        id="phone"
                                                        name="phone"
                                                        placeholder="Ej: 987654321"
                                                        value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- SEGURIDAD -->
                        <div class="tab-pane fade" id="account-content" role="tabpanel">
                            <div class="row g-4">
                                <!-- Tarjeta de Info  -->
                                <div class="col-lg-4">
                                    <div class="card border-0 text-white h-100 rounded-4 shadow-sm" style="background: linear-gradient(160deg, #198754 0%, #0f5132 100%);">
                                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                                            <div class="bg-white bg-opacity-25 rounded-circle p-3 mb-3">
                                                <i class="bi bi-shield-check fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold">Zona Segura</h5>
                                            <p class="small text-white-50 mb-0">Protege tu cuenta con una contraseña segura.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Formulario de Cambio-->
                                <div class="col-lg-8">
                                    <div class="bg-white p-4 rounded-4 shadow-sm border border-light h-100">
                                        <!-- Alerta Mensajes -->
                                        <div id="msg-container-pass" class="d-none mb-3">
                                            <div id="msg-alert-pass" class="alert border-0 rounded-3 py-2 px-3 d-flex align-items-center" role="alert">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                <span id="msg-text-pass" class="small fw-semibold"></span>
                                            </div>
                                        </div>
                                        <!-- USUARIO -->
                                        <div class="mb-4">
                                            <label for="username" class="form-label fw-bold text-dark small">Usuario</label>
                                            <div class="input-group custom-input-group">
                                                <span class="input-group-text ps-3"><i class="bi bi-person-circle"></i></span>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="username"
                                                    name="username"
                                                    value="<?= htmlspecialchars($user['user'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    placeholder="Nombre de usuario">
                                            </div>
                                        </div>
                                        <hr class="text-muted opacity-25 my-4">
                                        <!-- Paso 1 -->
                                        <div class="mb-4">
                                            <label for="currentPassword" class="form-label fw-bold text-dark small">1. Validar Contraseña Actual</label>
                                            <div class="input-group custom-input-group">
                                                <span class="input-group-text ps-3"><i class="bi bi-key-fill"></i></span>
                                                <input
                                                    type="password"
                                                    class="form-control"
                                                    id="currentPassword"
                                                    name="currentPassword"
                                                    placeholder="••••••••">
                                                <button class="btn btn-light text-success fw-bold px-3" type="button" id="btnVerifyPassword">VALIDAR</button>
                                                <button class="btn btn-light text-danger px-3 d-none" type="button" id="btnCancelPasswordChange">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Paso 2 -->
                                        <div id="newPasswordSection" style="transition: all 0.3s ease;">
                                            <label class="form-label fw-bold text-dark small">2. Crear Nueva Contraseña</label>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <div class="input-group custom-input-group bg-light">
                                                        <span class="input-group-text ps-3"><i class="bi bi-lock-fill"></i></span>
                                                        <input
                                                            type="password"
                                                            class="form-control bg-light"
                                                            id="newPassword"
                                                            name="newPassword"
                                                            placeholder="Nueva Clave"
                                                            disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group custom-input-group bg-light">
                                                        <span class="input-group-text ps-3"><i class="bi bi-check-circle-fill"></i></span>
                                                        <input
                                                            type="password"
                                                            class="form-control bg-light"
                                                            id="confirmNewPassword"
                                                            name="confirmNewPassword"
                                                            placeholder="Confirmar"
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Mensaje de coincidencia-->
                                            <div id="matchPassWrap" class="mt-2 d-none">
                                                <div id="matchPassAlert" class="alert py-2 px-3 mb-0 small rounded-3 d-flex align-items-center" role="alert">
                                                    <i id="matchPassIcon" class="bi me-2"></i>
                                                    <span id="matchPassText" class="fw-semibold"></span>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <small class="text-muted" style="font-size: 0.75rem;">Mínimo 8 caracteres</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-light bg-opacity-25 rounded-bottom-4 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-floppy2-fill me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>




<?= footerPos($data) ?>