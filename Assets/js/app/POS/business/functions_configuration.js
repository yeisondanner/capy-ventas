// Preview de imagen
document
  .getElementById("logoInput")
  .addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("logoPreview").src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });

// Script para confirmar eliminación
function confirmDelete() {
  // En un sistema real, aquí podrías abrir un Modal de Bootstrap
  if (
    confirm(
      "¡ATENCIÓN! \n\n¿Estás seguro de que deseas eliminar este negocio?\nEsta acción es irreversible y borrará todo el historial."
    )
  ) {
    // Simulación de llamada al backend
    alert("Procesando eliminación del negocio...");
  }
}

// Validación
(function () {
  "use strict";
  var forms = document.querySelectorAll(".needs-validation");
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        } else {
          event.preventDefault();
          const btn = form.querySelector('button[type="submit"]');
          const originalText = btn.innerHTML;
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

          // Simular petición
          setTimeout(() => {
            btn.className =
              "btn btn-success d-flex align-items-center gap-2 shadow-sm";
            btn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Guardado!';

            setTimeout(() => {
              btn.disabled = false;
              btn.className =
                "btn btn-primary d-flex align-items-center gap-2 shadow-sm";
              btn.innerHTML = originalText;
            }, 2000);
          }, 1000);
        }
        form.classList.add("was-validated");
      },
      false
    );
  });
})();
