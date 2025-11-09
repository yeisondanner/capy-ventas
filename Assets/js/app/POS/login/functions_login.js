window.addEventListener("DOMContentLoaded", () => {
  rememberUser();
  login(); // Funcion que se encarga de enviar los datos del formulario de login
  setRemember();
  toggleInputPassword();
});


function login() {
  const formLogin = document.getElementById("formLogin");

  formLogin.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(formLogin);

    const config = {
      method: "POST",
      body: formData,
      cache: "no-cache",
      mode: "cors",
      credentials: "same-origin", 
    };

    const url = base_url + "/login/isLogIn";

    fetch(url, config)
      .then(async (response) => {
        const contentType = response.headers.get("content-type") || "";
        const raw = await response.text();

        if (!response.ok) {
          throw new Error(`HTTP ${response.status} ${response.statusText} :: ${raw.slice(0, 200)}`);
        }
        if (!contentType.includes("application/json")) {
          console.error("Respuesta no JSON del servidor:", raw);
          throw new Error("El servidor no devolvió JSON (Content-Type inválido)");
        }

        return JSON.parse(raw);
      })
      .then((data) => {
        if (typeof toastr !== "undefined") {
          toastr.options = {
            closeButton: true,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            progressBar: true,
          };
        }

        if (!data.status) {
          formLogin.reset();
          if (typeof toastr !== "undefined") {
            toastr[data.type || "error"](data.message || "Acción no permitida", data.title || "Atención");
          } else {
            alert(`${data.title || "Atención"}\n${data.message || "Acción no permitida"}`);
          }
          return;
        }

        if (typeof toastr !== "undefined") {
          toastr[data.type || "success"](data.message || "Operación exitosa", data.title || "OK");
        } else {
          alert(`${data.title || "OK"}\n${data.message || "Operación exitosa"}`);
        }

        formLogin.reset();
        setTimeout(() => {
          window.location.href = data.redirection;
        }, 1000);
      })
      .catch((error) => {
        console.error("Error en la solicitud:", error);
        // Mensaje simple (sin Toastr si no está cargado)
        if (typeof toastr !== "undefined") {
          toastr.error(error.message || "Error en la solicitud", "Error");
        } else {
          alert(`Error en la solicitud al servidor: ${error.message}`);
        }
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
    iconPass.classList.toggle("bi-eye-fill");
    iconPass.classList.toggle("bi-eye-slash-fill");

    // Selecciona el texto si se muestra
    if (inputPass.type === "text") {
      inputPass.focus();
      inputPass.select();
      inputPass.setSelectionRange(0, inputPass.value.length);
    }
  });
}
