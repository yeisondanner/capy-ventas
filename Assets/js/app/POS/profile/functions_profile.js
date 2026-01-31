(function () {
  "use strict";
  // Formulario de edición de perfil (solo 6 campos)
  const formEditProfile = document.getElementById("formEditProfile");
  // Tooltip del badge de suscripción
  const billingBadge = document.querySelector(".tile .badge.bg-info");
  //modal del edit
  const modalEditProfile = document.getElementById("modalEditProfile");

  // Estado de verificación de contraseña
  let passwordVerified = false;
  let storedCurrentPassword = ""; // almacena la contraseña actual verificada

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
    
  }

  /**
   * Muestra un mensaje inline relacionado con la contraseña
   */
  function showPassMsg(type, text) {
    const container = document.getElementById("msg-container-pass");
    const alertEl = document.getElementById("msg-alert-pass");
    const textEl = document.getElementById("msg-text-pass");

    if (!container || !alertEl || !textEl) return;

    container.classList.remove("d-none");
    alertEl.className = `alert alert-${type} py-2 px-3 small mb-0`;
    textEl.innerText = text;
  }

  function hidePassMsg() {
    const container = document.getElementById("msg-container-pass");
    if (container) container.classList.add("d-none");
  }

  function resetPasswordBlock() {
    passwordVerified = false;
    storedCurrentPassword = "";
    hidePassMsg();

    const current = document.getElementById("currentPassword");
    const p1 = document.getElementById("newPassword");
    const p2 = document.getElementById("confirmNewPassword");

    if (current) {
      current.value = "";
      current.readOnly = false;
      current.classList.remove("is-valid", "is-invalid");
    }
    if (p1) {
      p1.value = "";
      p1.disabled = true;
      p1.classList.remove("is-valid", "is-invalid");
    }
    if (p2) {
      p2.value = "";
      p2.disabled = true;
      p2.classList.remove("is-valid", "is-invalid");
    }

    // Limpiar mensaje de confirmación de contraseña
    const wrap = document.getElementById("matchPassWrap");
    const alertEl = document.getElementById("matchPassAlert");
    const textEl = document.getElementById("matchPassText");
    if (wrap) wrap.classList.add("d-none");
    if (alertEl)
      alertEl.className =
        "alert py-2 px-3 mb-0 small rounded-3 d-flex align-items-center";
    if (textEl) textEl.innerText = "";
  }

  /**
   * Verifica si las contraseñas coinciden y actualiza el estado visual
   */
  function checkPasswords(p1, p2) {
    const val1 = (p1.value || "").trim();
    const val2 = (p2.value || "").trim();

    const wrap = document.getElementById("matchPassWrap");
    const alertEl = document.getElementById("matchPassAlert");
    const iconEl = document.getElementById("matchPassIcon");
    const textEl = document.getElementById("matchPassText");

    const hide = () => {
      if (wrap) wrap.classList.add("d-none");
    };

    const show = (type, iconClass, text) => {
      if (!wrap || !alertEl || !iconEl || !textEl) return;
      wrap.classList.remove("d-none");
      alertEl.className = `alert alert-${type} py-2 px-3 mb-0 small rounded-3 d-flex align-items-center`;
      iconEl.className = `bi me-2 ${iconClass}`;
      textEl.innerText = text;
    };

    // Si no escribió confirmación
    if (val2 === "") {
      p2.classList.remove("is-valid", "is-invalid");
      hide();
      return;
    }

    // Si no escribió nueva contraseña
    if (val1 === "") {
      p2.classList.remove("is-valid");
      p2.classList.add("is-invalid");
      show(
        "warning",
        "bi-exclamation-triangle-fill",
        "Primero escribe la nueva contraseña.",
      );
      return;
    }

    // Comparar contraseñas
    if (val1 === val2) {
      p2.classList.remove("is-invalid");
      p2.classList.add("is-valid");
      show("success", "bi-check-circle-fill", "Las contraseñas coinciden.");
    } else {
      p2.classList.remove("is-valid");
      p2.classList.add("is-invalid");
      show("danger", "bi-x-circle-fill", "Las contraseñas no coinciden.");
    }
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

      // Manejo de cambio de contraseña
      const newPasswordEl = document.getElementById("newPassword");
      const confirmNewPasswordEl =
        document.getElementById("confirmNewPassword");

      const newPass = newPasswordEl ? newPasswordEl.value.trim() : "";
      const confirmPass = confirmNewPasswordEl
        ? confirmNewPasswordEl.value.trim()
        : "";

      const wantsChangePassword =
        passwordVerified && (newPass !== "" || confirmPass !== "");

      // Si no ha verificado la contraseña pero intenta cambiarla
      if (!passwordVerified && (newPass !== "" || confirmPass !== "")) {
        showPassMsg("warning", "Primero verifica tu contraseña actual.");
        return;
      }

      // Validaciones de la nueva contraseña
      if (wantsChangePassword) {
        if (newPass.length < 8) {
          showPassMsg(
            "warning",
            "La nueva contraseña debe tener al menos 8 caracteres.",
          );
          return;
        }
        if (newPass !== confirmPass) {
          showPassMsg("danger", "Las contraseñas no coinciden.");
          return;
        }
        if (storedCurrentPassword && newPass === storedCurrentPassword) {
          showPassMsg(
            "warning",
            "La nueva contraseña no puede ser igual a la actual.",
          );
          return;
        }

        try {
          // Crear objeto FormData para el cambio de contraseña
          const fdPass = new FormData();
          fdPass.append("currentPassword", storedCurrentPassword);
          fdPass.append("newPassword", newPass);

          const urlPass = formEditProfile
            .getAttribute("action")
            .replace("/updateProfile", "/updatePassword");

          const respPass = await fetch(urlPass, {
            method: "POST",
            body: fdPass,
          });

          if (!respPass.ok) {
            throw new Error(`HTTP error! status: ${respPass.status}`);
          }

          const dataPass = await respPass.json();

          if (!dataPass.status) {
            showPassMsg(
              "danger",
              `${dataPass.message || "No se pudo actualizar la contraseña."}`,
            );
            return;
          }

          // Contraseña actualizada correctamente
          showPassMsg("success", "✓ Contraseña actualizada correctamente.");
        } catch (error) {
          showPassMsg(
            "danger",
            "Ocurrió un error con el servidor: " + error.message,
          );
          return;
        }
      }

      // Crear objeto FormData para el perfil
      const formData = new FormData();
      formData.append("names", names);
      formData.append("lastnames", lastnames);
      formData.append("email", email);
      formData.append("phone", phone);
      formData.append("birthDate", birthDate);
      formData.append("username", username);

      try {
        // Enviar la solicitud al servidor
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

          // Actualizar la vista con los nuevos datos sin recargar la página
          updateViewWithData(data.updatedData);

          showAlert({
            title: data.title || "Información actualizada",
            message: data.message || "Perfil actualizado correctamente.",
            type: "success",
            icon: "success",
            status: true,
          });

          // Resetear bloque de cambio de contraseña
          resetPasswordBlock();
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
   * Actualiza los datos mostrados en la vista del perfil sin recargar la página
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

    const prefixElement = document.getElementById("prefix");
    if (prefixElement && updatedData.prefix !== undefined) {
      prefixElement.value = updatedData.prefix || "";
    }
  }

  /**
    * Formatea una fecha en formato DD/MM/YYYY HH:MM o DD/MM/YYYY
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
    * Configura la funcionalidad de cambio de contraseña
   */
  function setupChangePassword() {
    const btnVerify = document.getElementById("btnVerifyPassword");
    const btnCancel = document.getElementById("btnCancelPasswordChange");

    const current = document.getElementById("currentPassword");
    const p1 = document.getElementById("newPassword");
    const p2 = document.getElementById("confirmNewPassword");

    if (!btnVerify || !btnCancel || !current || !p1 || !p2) return;

    // Reiniciar bloque al abrir modal
    $(modalEditProfile).on("shown.bs.modal", function () {
      resetPasswordBlock();
    });

    // Verificar contraseña actual
    btnVerify.addEventListener("click", async (e) => {
      e.preventDefault();
      hidePassMsg();

      const currentPassword = current.value.trim();

      if (!currentPassword) {
        current.classList.add("is-invalid");
        showPassMsg(
          "warning",
          "✕ Ingresa tu contraseña actual para verificar.",
        );
        return;
      }

      current.classList.remove("is-invalid");

      try {
        const fd = new FormData();
        fd.append("currentPassword", currentPassword);

        const url = formEditProfile
          .getAttribute("action")
          .replace("/updateProfile", "/verifyPassword");

        const response = await fetch(url, { method: "POST", body: fd });

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();

        if (!data.status) {
          passwordVerified = false;
          storedCurrentPassword = "";
          current.classList.add("is-invalid");
          showPassMsg(
            "danger",
            `${data.message || "Contraseña incorrecta."}`,
          );
          return;
        }

        // Contraseña verificada OK
        passwordVerified = true;
        storedCurrentPassword = currentPassword;

        current.classList.remove("is-invalid");
        current.classList.add("is-valid");
        current.readOnly = true; // bloquear campo

        p1.disabled = false;
        p2.disabled = false;
        p1.focus();

        showPassMsg(
          "success",
          "Contraseña verificada. Ahora puedes escribir la nueva contraseña.",
        );
      } catch (error) {
        showPassMsg(
          "danger",
          "Ocurrió un error con el servidor: " + error.message,
        );
      }
    });

    // Cancelar cambio de contraseña
    btnCancel.addEventListener("click", (e) => {
      e.preventDefault();
      resetPasswordBlock();
    });

    // Verificar coincidencia de nuevas contraseñas en tiempo real
    p1.addEventListener("input", () => checkPasswords(p1, p2));
    p2.addEventListener("input", () => checkPasswords(p1, p2));
  }
})();
