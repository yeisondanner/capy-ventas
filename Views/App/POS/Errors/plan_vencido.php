<?= headerPos($data) ?>
<main class="app-content">
    <div class="card shadow-sm" style="max-width:720px;">
        <div class="card-body text-center p-4">
            <div class="mb-3">
                <!-- Icono de advertencia (Bootstrap 5 SVG) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-exclamation-triangle-fill text-warning" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                </svg>
            </div>

            <h3 class="card-title mb-2">Plan vencido</h3>

            <p class="card-text mb-3">El negocio no tiene un plan activo o está en plan <strong>Free</strong>. Por favor contacte al dueño del negocio para que pueda renovar el plan.</p>

            <div class="d-flex justify-content-center gap-2">
                <a href="mailto:admin@ejemplo.com" class="btn btn-primary">Contactar al dueño</a>
                <a href="/" class="btn btn-outline-secondary">Volver al inicio</a>
            </div>

            <small class="text-muted d-block mt-3">Si usted es el dueño, inicie sesión en el panel de administración para renovar o actualizar el plan.</small>
        </div>
    </div>
</main>
<?= footerPos($data) ?>