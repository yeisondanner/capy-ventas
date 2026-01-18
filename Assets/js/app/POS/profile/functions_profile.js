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

    // Cambiar contraseña
    setupChangePassword();
  });

  /**
   * Configura los manejadores de eventos para el modal de edición
   */
  function setupModalEventHandlers() {
    // Actualizar los valores del formulario cuando se muestre el modal
    /*$(modalEditProfile).on('show.bs.modal', function () {
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
    });*/
  }
  /**
   * Actualizar perfil de usuario
   */
  function update_profile() {
    formEditProfile.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Obtener los valores de los campos
      const names = document.getElementById("names").value;
      const lastnames = document.getElementById("lastnames").value;
      const email = document.getElementById("email").value;
      const phone = document.getElementById("phone").value;
      const birthDate = document.getElementById("birthDate").value;
      const username = document.getElementById("username").value;

      // Validar campos obligatorios
      if (!names || !lastnames || !email || !username) {
        showAlert({
          title: "Campos requeridos",
          message: "Nombres, apellidos, usuario y correo son obligatorios.",
          type: "error",
          icon: "error",
          status: false,
        });
        return;
      }

      // Crear objeto FormData personalizado
      const formData = new FormData();
      formData.append("names", names);
      formData.append("lastnames", lastnames);
      formData.append("email", email);
      formData.append("phone", phone);
      formData.append("birthDate", birthDate);
      formData.append("username", username);

      try {
        // Usamos la URL definida en el action del formulario
        const url = formEditProfile.getAttribute("action");

        const response = await fetch(url, {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status) {
          formEditProfile.reset();
          $(modalEditProfile).modal("hide");

          // Actualizar los datos mostrados en la vista sin recargar la página
          updateViewWithData(data.updatedData);

          showAlert({
            title: data.title || "Información actualizada",
            message: data.message || "Perfil actualizado correctamente.",
            type: "success",
            icon: "success",
            status: true,
          });
        } else {
          showAlert({
            title: data.title || "Ocurrió un error",
            message: data.message || "No se pudo actualizar el perfil.",
            type: "error",
            icon: "error",
            status: false,
          });
        }
      } catch (error) {
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
    const fullnameCardElement = document.getElementById("profile-fullname");
    if (fullnameCardElement)
      fullnameCardElement.textContent = updatedData.fullname;

    const usernameCardElement = document.getElementById("profile-username");
    if (usernameCardElement)
      usernameCardElement.textContent = updatedData.username;

    const emailCardElement = document.getElementById("profile-email");
    if (emailCardElement) emailCardElement.textContent = updatedData.email;

    const countryCardElement = document.getElementById("profile-country");
    if (countryCardElement)
      countryCardElement.textContent = updatedData.country || "Sin país";

    const phoneCardElement = document.getElementById("profile-phone");
    if (phoneCardElement)
      phoneCardElement.textContent = updatedData.phone_full || "Sin teléfono";

    const birthDateCardElement = document.getElementById("profile-birthdate");
    if (birthDateCardElement) {
      const birthDateFormatted = updatedData.birthDate
        ? formatDateProfile(updatedData.birthDate, false)
        : "Sin registrar";
      birthDateCardElement.textContent = birthDateFormatted;
    }

    const namesElement = document.getElementById("names");
    if (namesElement)
      namesElement.value = updatedData.fullname.split(" ")[0] || "";

    const lastnamesElement = document.getElementById("lastnames");
    if (lastnamesElement) {
      const nameParts = updatedData.fullname.split(" ");
      lastnamesElement.value = nameParts.slice(1).join(" ") || "";
    }

    const usernameElement = document.getElementById("username");
    if (usernameElement) usernameElement.value = updatedData.username;

    const emailElement = document.getElementById("email");
    if (emailElement) emailElement.value = updatedData.email;

    const phoneElement = document.getElementById("phone");
    if (phoneElement) phoneElement.value = updatedData.phone || "";

    const countryElement = document.getElementById("country");
    if (countryElement) countryElement.value = updatedData.country || "";

    const birthDateElement = document.getElementById("birthDate");
    if (birthDateElement) birthDateElement.value = updatedData.birthDate || "";

    // (Opcional) Si estás devolviendo prefix, puedes rellenarlo:
    const prefixElement = document.getElementById("prefix");
    if (prefixElement && updatedData.prefix !== undefined) {
      prefixElement.value = updatedData.prefix || "";
    }
  }

  /**
   * Función auxiliar para formatear fechas similar a la del PHP
   */
  function formatDateProfile(value, withTime = true) {
    if (!value) return "Sin registrar";

    let date;
    if (typeof value === "string") {
      if (/^\d{4}-\d{2}-\d{2}/.test(value)) {
        const dateWithTime = value + "T00:00:00";
        date = new Date(dateWithTime);
      } else {
        date = new Date(value);
      }
    } else {
      date = new Date(value);
    }

    if (isNaN(date.getTime())) return value;

    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    if (withTime) {
      const hours = String(date.getHours()).padStart(2, "0");
      const minutes = String(date.getMinutes()).padStart(2, "0");
      return `${day}/${month}/${year} ${hours}:${minutes}`;
    } else {
      return `${day}/${month}/${year}`;
    }
  }

  /**
   * Cambiar contraseña con con SweetAlert2
   */
  function setupChangePassword() {
    const btn = document.getElementById("btnChangePassword");
    if (!btn) return;

    btn.addEventListener("click", async () => {
      const result = await Swal.fire({
        title: "Cambiar contraseña",
        html: `
          <div style="display:flex; gap:8px; align-items:center;">
            <input id="swal-current" type="password" class="swal2-input" placeholder="Contraseña actual" style="flex:1; margin:0;">
            <button type="button" class="swal2-styled" data-toggle-pass="swal-current"
              style="margin:0; padding:.4rem .6rem; line-height:1; border-radius:.5rem;">
              <i class="bi bi-eye"></i>
            </button>
          </div>

          <div style="display:flex; gap:8px; align-items:center; margin-top:10px;">
            <input id="swal-new" type="password" class="swal2-input" placeholder="Nueva contraseña" style="flex:1; margin:0;">
            <button type="button" class="swal2-styled" data-toggle-pass="swal-new"
              style="margin:0; padding:.4rem .6rem; line-height:1; border-radius:.5rem;">
              <i class="bi bi-eye"></i>
            </button>
          </div>

          <div style="display:flex; gap:8px; align-items:center; margin-top:10px;">
            <input id="swal-confirm" type="password" class="swal2-input" placeholder="Confirmar nueva contraseña" style="flex:1; margin:0;">
            <button type="button" class="swal2-styled" data-toggle-pass="swal-confirm"
              style="margin:0; padding:.4rem .6rem; line-height:1; border-radius:.5rem;">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Actualizar",
        cancelButtonText: "Cancelar",
        didOpen: () => {
          // activar botones (mostrar-ocultar contraseña)
          const buttons =
            Swal.getPopup().querySelectorAll("[data-toggle-pass]");
          buttons.forEach((btn) => {
            btn.addEventListener("click", () => {
              const inputId = btn.getAttribute("data-toggle-pass");
              const input = document.getElementById(inputId);
              const icon = btn.querySelector("i");
              if (!input) return;

              const isPassword = input.type === "password";
              input.type = isPassword ? "text" : "password";

              if (icon) {
                icon.classList.toggle("bi-eye", !isPassword);
                icon.classList.toggle("bi-eye-slash", isPassword);
              }

              input.focus();
            });
          });

          document.getElementById("swal-current")?.focus();
        },

        preConfirm: () => {
          const current = document.getElementById("swal-current").value.trim();
          const pass1 = document.getElementById("swal-new").value.trim();
          const pass2 = document.getElementById("swal-confirm").value.trim();

          if (!current || !pass1 || !pass2) {
            Swal.showValidationMessage("Completa todos los campos.");
            return false;
          }
          if (pass1.length < 8) {
            Swal.showValidationMessage(
              "La nueva contraseña debe tener al menos 8 caracteres.",
            );
            return false;
          }
          if (pass1 !== pass2) {
            Swal.showValidationMessage("La confirmación no coincide.");
            return false;
          }
          if (pass1 === current) {
            Swal.showValidationMessage(
              "La nueva contraseña no puede ser igual a la actual.",
            );
            return false;
          }
          return { currentPassword: current, newPassword: pass1 };
        },
      });

      if (!result.isConfirmed) return;

      try {
        Swal.fire({
          title: "Actualizando...",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading(),
        });

        const fd = new FormData();
        fd.append("currentPassword", result.value.currentPassword);
        fd.append("newPassword", result.value.newPassword);

        const url = formEditProfile
          .getAttribute("action")
          .replace("/updateProfile", "/updatePassword");

        const response = await fetch(url, { method: "POST", body: fd });

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();

        if (data.status) {
          await Swal.fire({
            title: data.title || "Listo",
            text: data.message || "Contraseña actualizada.",
            icon: "success",
          });
        } else {
          await Swal.fire({
            title: data.title || "Error",
            text: data.message || "No se pudo actualizar la contraseña.",
            icon: "error",
          });
        }
      } catch (error) {
        await Swal.fire({
          title: "Error",
          text: "Ocurrió un error con el servidor: " + error.message,
          icon: "error",
        });
      }
    });
  }
})();
