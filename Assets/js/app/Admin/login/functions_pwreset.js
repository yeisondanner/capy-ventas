document.addEventListener("DOMContentLoaded", () => {
  setTimeout(() => {
    toggleInputPassword();
    sendData();
  }, 1000);
});
//Funcion que se encarga de mostrar y ocultar la contraseña
function toggleInputPassword() {
  const toggleBtn = document.getElementById("togglePassword");
  const inputPass = document.getElementById("txtPassword");
  const iconPass = document.getElementById("iconoPassword");

  toggleBtn.addEventListener("click", function () {
    const isPassword = inputPass.type === "password";
    inputPass.type = isPassword ? "text" : "password";
    iconPass.classList.toggle("fa-eye");
    iconPass.classList.toggle("fa-eye-slash");

    // Selecciona el texto si se muestra
    if (inputPass.type === "text") {
      inputPass.focus();
      inputPass.select();
      inputPass.setSelectionRange(0, inputPass.value.length);
    }
  });
}
//Metodo que se encarga de registrar la nueva contraseña
function sendData() {
  const formPassword = document.getElementById("formPassword");
  formPassword.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formPassword);
    const headers = new Headers();
    const config = {
      method: "POST",
      headers: headers,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/login/updatePassword"; // URL to send the form data
    elementLoader.classList.remove("d-none");
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Error en la solicitud " +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        toastr.options = {
          closeButton: true,
          onclick: null,
          showDuration: "300",
          hideDuration: "1000",
          timeOut: "5000",
          progressBar: true,
          onclick: null,
        };
        if (!data.status) {
          //limpiar el formulario
          formPassword.reset();
          toastr[data.type](data.message, data.title);
          setTimeout(() => {
            elementLoader.classList.add("d-none");
            // window.location.href = data.redirection;
          }, 1000);
          return false;
        }
        toastr[data.type](data.message, data.title);
        formPassword.reset();
        setTimeout(() => {
          elementLoader.classList.add("d-none");
          window.location.href = data.redirection;
        }, 1000);
        return true;
      })
      .catch((error) => {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrio un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
