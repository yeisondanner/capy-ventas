(function () {
  "use strict";
  // Formulario de edición de perfil (solo 6 campos)
  const formEditProfile = document.getElementById("formEditProfile");
  // Tooltip del badge de suscripción
  const billingBadge = document.querySelector(".tile .badge.bg-info");
  //modal del edit
  const modalEditProfile = document.getElementById("modalEditProfile");
  document.addEventListener("DOMContentLoaded", () => {
    if (billingBadge) {
      billingBadge.title = "Estado de la suscripción y forma de renovación";
    }
    // Llamada a la función para actualizar el perfil
    update_profile();

    // Agregar evento para actualizar los valores del formulario cuando se abra el modal
    setupModalEventHandlers();
  });

  /**
   * Configura los manejadores de eventos para el modal de edición
   */
  function setupModalEventHandlers() {
    // Actualizar los valores del formulario cuando se muestre el modal
    $(modalEditProfile).on('show.bs.modal', function () {
      // Actualizar los valores del formulario con los datos mostrados actualmente en la página
      document.getElementById('fullname').value = document.getElementById('profile-fullname').textContent;
      document.getElementById('username').value = document.getElementById('profile-username').textContent;
      document.getElementById('email').value = document.getElementById('profile-email').textContent;
      document.getElementById('phone').value = document.getElementById('profile-phone').textContent !== 'Sin teléfono' ? document.getElementById('profile-phone').textContent : '';
      document.getElementById('country').value = document.getElementById('profile-country').textContent !== 'Sin país' ? document.getElementById('profile-country').textContent : '';

      // Para la fecha de nacimiento, necesitamos extraer solo la fecha sin el formato adicional
      const birthDateText = document.getElementById('profile-birthdate').textContent;
      if (birthDateText && birthDateText !== 'Sin registrar') {
        // Intentar convertir el formato DD/MM/AAAA a AAAA-MM-DD para el input date
        const dateParts = birthDateText.split(' ')[0].split('/'); // Tomar solo la parte de la fecha si hay hora
        if (dateParts.length === 3) {
          document.getElementById('birthDate').value = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
        }
      } else {
        document.getElementById('birthDate').value = '';
      }
    });
  }
  /**
   * Actualizar perfil de usuario
   */
  function update_profile() {
    formEditProfile.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(formEditProfile);

      try {
        // Usamos la URL definida en el action del formulario
        const url = formEditProfile.getAttribute("action");

        const response = await fetch(url, {
          method: "POST",
          body: formData,
        });

        // Verificar si la respuesta es exitosa antes de procesarla
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status) {
          formEditProfile.reset();
          $(modalEditProfile).modal("hide");

          // Actualizar los datos mostrados en la vista sin recargar la página
          updateViewWithData(data.updatedData);

          // Mostrar mensaje de éxito
          showAlert({
            title: data.title || 'Información actualizada',
            message: data.message || 'Perfil actualizado correctamente.',
            type: 'success',
            icon: 'success',
            status: true
          });
        } else {
          // Mostrar mensaje de error
          showAlert({
            title: data.title || 'Ocurrió un error',
            message: data.message || 'No se pudo actualizar el perfil.',
            type: 'error',
            icon: 'error',
            status: false
          });
        }
      } catch (error) {
        // Manejar errores de red u otros errores
        showAlert({
          title: "Ocurrió un error inesperado",
          message: "Ocurrió un error con el servidor: " + error.message,
          icon: "error",
          type: "error",
          status: false,
          timer: 4000,
        });
      }
    });
  }

  /**
   * Actualiza los elementos visuales en la página con los nuevos datos
   */
  function updateViewWithData(updatedData) {
    // Actualizar el nombre completo en la tarjeta de perfil
    const fullnameCardElement = document.getElementById('profile-fullname');
    if (fullnameCardElement) {
      fullnameCardElement.textContent = updatedData.fullname;
    }

    // Actualizar el nombre de usuario en la tarjeta de perfil
    const usernameCardElement = document.getElementById('profile-username');
    if (usernameCardElement) {
      usernameCardElement.textContent = updatedData.username;
    }

    // Actualizar el correo electrónico en la tarjeta de perfil
    const emailCardElement = document.getElementById('profile-email');
    if (emailCardElement) {
      emailCardElement.textContent = updatedData.email;
    }

    // Actualizar el país en la tarjeta de perfil
    const countryCardElement = document.getElementById('profile-country');
    if (countryCardElement) {
      countryCardElement.textContent = updatedData.country || 'Sin país';
    }

    // Actualizar el teléfono en la tarjeta de perfil
    const phoneCardElement = document.getElementById('profile-phone');
    if (phoneCardElement) {
      phoneCardElement.textContent = updatedData.phone || 'Sin teléfono';
    }

    // Actualizar la fecha de nacimiento en la tarjeta de perfil
    const birthDateCardElement = document.getElementById('profile-birthdate');
    if (birthDateCardElement) {
      // Formatear la fecha de nacimiento al formato DD/MM/AAAA como en la vista PHP
      const birthDateFormatted = updatedData.birthDate ? formatDateProfile(updatedData.birthDate, false) : 'Sin registrar';
      birthDateCardElement.textContent = birthDateFormatted;
    }

    // Actualizar también los campos del formulario de edición
    const fullnameElement = document.getElementById('fullname');
    if (fullnameElement) {
      fullnameElement.value = updatedData.fullname;
    }

    const usernameElement = document.getElementById('username');
    if (usernameElement) {
      usernameElement.value = updatedData.username;
    }

    const emailElement = document.getElementById('email');
    if (emailElement) {
      emailElement.value = updatedData.email;
    }

    const phoneElement = document.getElementById('phone');
    if (phoneElement) {
      phoneElement.value = updatedData.phone || '';
    }

    const countryElement = document.getElementById('country');
    if (countryElement) {
      countryElement.value = updatedData.country || '';
    }

    const birthDateElement = document.getElementById('birthDate');
    if (birthDateElement) {
      birthDateElement.value = updatedData.birthDate || '';
    }
  }

  /**
   * Función auxiliar para formatear fechas similar a la del PHP
   */
  function formatDateProfile(value, withTime = true) {
    if (!value) {
      return 'Sin registrar';
    }

    // Intentar parsear la fecha en diferentes formatos
    let date;
    if (typeof value === 'string') {
      // Si el valor es una cadena en formato YYYY-MM-DD
      if (/^\d{4}-\d{2}-\d{2}/.test(value)) {
        // Agregar tiempo si no está presente para evitar problemas con la zona horaria
        const dateWithTime = value + 'T00:00:00';
        date = new Date(dateWithTime);
      } else {
        // Si ya tiene formato DD/MM/AAAA o similar
        date = new Date(value);
      }
    } else {
      date = new Date(value);
    }

    if (isNaN(date.getTime())) {
      return value; // Si no se puede parsear, devolver el valor original
    }

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    if (withTime) {
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day}/${month}/${year} ${hours}:${minutes}`;
    } else {
      return `${day}/${month}/${year}`;
    }
  }

})();
