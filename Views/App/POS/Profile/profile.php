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
                <div class="tile-title-w-btn d-flex align-items-center">
                    <div>
                        <h3 class="tile-title mb-0">Datos del usuario</h3>
                        <small class="text-muted">Información básica de tu cuenta</small>
                    </div>
                    <span class="badge bg-<?= ($user['status'] ?? '') === 'Activo' ? 'success' : 'secondary' ?>">
                        <?= htmlspecialchars($user['status'] ?? 'Desconocido', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="avatar-wrapper">
                        <img src="<?= GENERAR_PERFIL . $avatarName; ?>" class="rounded-circle shadow-sm profile-avatar" alt="Avatar">
                    </div>
                    <div>
                        <h5 class="mb-1"><?= htmlspecialchars($user['fullname'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="mb-0 text-muted">Usuario: <strong><?= htmlspecialchars($user['user'] ?? 'Sin usuario', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p class="mb-0 text-muted">Correo: <strong><?= htmlspecialchars($user['email'] ?? 'Sin correo', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-1 text-muted">Teléfono</p>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['phone'] ?? 'Sin teléfono', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">País</p>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['country'] ?? 'Sin país', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Nacimiento</p>
                        <p class="mb-0 fw-semibold"><?= formatDateProfile($user['birthDate'] ?? null, false); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Registro</p>
                        <p class="mb-0 fw-semibold"><?= formatDateProfile($user['registeredAt'] ?? null); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Última actualización</p>
                        <p class="mb-0 fw-semibold"><?= formatDateProfile($user['updatedAt'] ?? null); ?></p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Vigencia del plan</p>
                        <p class="mb-0 fw-semibold"><?= formatDateProfile($user['planExpiresAt'] ?? null); ?></p>
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
        <?php
        dep($invoices);
        ?>
        <div class="table-responsive mt-3">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Periodo</th>
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
                                    <span class="badge bg-<?= ($invoice['status'] ?? '') === 'active' ? 'success' : (($invoice['status'] ?? '') === 'expired' ? 'secondary' : 'warning'); ?>">
                                        <?= htmlspecialchars($invoice['status'], ENT_QUOTES, 'UTF-8'); ?>
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
                <h3 class="tile-title mb-0">Negocios vinculados</h3>
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
<?= footerPos($data) ?>