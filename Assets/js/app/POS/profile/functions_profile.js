'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const billingBadge = document.querySelector('.tile .badge.bg-info');
    if (billingBadge) {
        billingBadge.title = 'Estado de la suscripción y forma de renovación';
    }
});
