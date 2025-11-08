window.addEventListener("DOMContentLoaded", () => {
  flippedFormLogin();
  rememberUser();
  login(); // Funcion que se encarga de enviar los datos del formulario de login
  setRemember();
  toggleInputPassword();
  resetPasswordUser();
});

function flippedFormLogin() {
  // Login Page Flipbox control
  $('.login-content [data-toggle="flip"]').click(function () {
    $(".login-box").toggleClass("flipped");
    return false;
  });
}
function login() {
  const formLogin = document.getElementById("formLogin");
  formLogin.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formLogin);
    const headers = new Headers();
    const config = {
      method: "POST",
      headers: headers,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/login/isLogIn"; // URL to send the form data
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
          formLogin.reset();
          toastr[data.type](data.message, data.title);
          elementLoader.classList.add("d-none");
          return false;
        }
        toastr[data.type](data.message, data.title);
        formLogin.reset();
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
//Funcion que se encarga de verificar si el usuario quiere recordar el usuario
function setRemember() {
  const chbxRemember = document.getElementById("chbxRemember");
  const txtUser = document.getElementById("txtUser");
  chbxRemember.addEventListener("change", () => {
    if (document.getElementById("chbxRemember").checked) {
      //Validamos que campo no este vacio
      if (txtUser.value === "") {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "No se puede recordar el usuario cuando el campo usuario esta vacio",
          "Ocurrio un error inesperado"
        );
        document.getElementById("chbxRemember").checked = false;
        return false;
      }
      toastr.options = {
        closeButton: true,
        timeOut: 3000,
        onclick: null,
      };
      toastr["info"](
        "El usuario sera recordado a partir de ahora en adelante",
        "Mensaje de informacion"
      );
      localStorage.setItem("usuario", txtUser.value); // Guarda si se marca
    } else {
      toastr.options = {
        closeButton: true,
        timeOut: 3000,
        onclick: null,
      };
      toastr["info"]("El usuario no sera recordado", "Atencion");
      localStorage.removeItem("usuario"); // Borra si no se marca
    }
  });
}
//Funcion que se encarga de recordar el usuario
function rememberUser() {
  const txtUser = document.getElementById("txtUser");
  const user = localStorage.getItem("usuario");
  if (user !== null) {
    txtUser.value = user;
    document.getElementById("chbxRemember").checked = true;
  }
}
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
/**
 * Metodo que se encarga de reiniciar la contraseña
 */
function resetPasswordUser() {
  const formReset = document.getElementById("formReset"); // Formulario de reseteo
  formReset.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formReset);
    const headers = new Headers();
    const config = {
      method: "POST",
      headers: headers,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/login/resetPassword"; // URL to send the form data
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
          formLogin.reset();
          toastr[data.type](data.message, data.title);
          setTimeout(() => {
            elementLoader.classList.add("d-none");
            //window.location.href = data.redirection;
          }, 1000);
          return false;
        }
        toastr[data.type](data.message, data.title);
        formLogin.reset();
        setTimeout(() => {
          elementLoader.classList.add("d-none");
          //window.location.href = data.redirection;
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
