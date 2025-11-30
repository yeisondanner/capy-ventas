'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // Tooltip del badge de suscripción
    const billingBadge = document.querySelector('.tile .badge.bg-info');
    if (billingBadge) {
        billingBadge.title = 'Estado de la suscripción y forma de renovación';
    }

    // Formulario de edición de perfil (solo 6 campos)
   'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // Tooltip del badge de suscripción
    const billingBadge = document.querySelector('.tile .badge.bg-info');
    if (billingBadge) {
        billingBadge.title = 'Estado de la suscripción y forma de renovación';
    }

    // Formulario de edición de perfil (solo 6 campos)
    const formEditProfile = document.getElementById('formEditProfile');
    if (formEditProfile) {
        formEditProfile.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(formEditProfile);

            try {
                // Usamos la URL definida en el action del formulario
                const url = formEditProfile.getAttribute('action');

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status) {
                    if (typeof showAlert !== 'undefined') {
                        showAlert.fire('Éxito', data.msg, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(data.msg);
                        window.location.reload();
                    }
                } else {
                    if (typeof showAlert !== 'undefined') {
                        showAlert.fire('Aviso', data.msg, 'warning');
                    } else {
                        alert(data.msg || 'No se pudo actualizar el perfil.');
                    }
                }
            } catch (error) {
                console.error('Error al actualizar el perfil:', error);
                if (typeof showAlert !== 'undefined') {
                    showAlert.fire('Error', 'Ocurrió un error al actualizar el perfil.', 'error');
                } else {
                    alert('Ocurrió un error al actualizar el perfil.');
                }
            }
        });
    }
});


            try {
                const url = base_url + '/pos/profile/updateProfile';

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status) {
                    if (typeof showAlert !== 'undefined') {
                        showAlert.fire('Éxito', data.msg, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(data.msg);
                        window.location.reload();
                    }
                } else {
                    if (typeof showAlert !== 'undefined') {
                        showAlert.fire('Aviso', data.msg, 'warning');
                    } else {
                        alert(data.msg || 'No se pudo actualizar el perfil.');
                    }
                }
            } catch (error) {
                console.error('Error al actualizar el perfil:', error);
                if (typeof showAlert !== 'undefined') {
                    showAlert.fire('Error', 'Ocurrió un error al actualizar el perfil.', 'error');
                } else {
                    alert('Ocurrió un error al actualizar el perfil.');
                }
            }
        });
    }
});
