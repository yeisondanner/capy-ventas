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
  });
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
        const data = await response.json();
        if (data.status) {
          formEditProfile.reset();
          $(modalEditProfile).modal("hide");
        }
        showAlert(data);
      } catch (error) {
        showAlert({
          title: "Ocurrio un error inesperado",
          message: "Ocurrio un error con el servidor: " + error.name,
          icon: "error",
          timer: 4000,
        });
      }
    });
  }
})();
