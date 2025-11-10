window.addEventListener("DOMContentLoaded", () => {
  login(); // Funcion que se encarga de enviar los datos del formulario de login
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
          throw new Error(
            `HTTP ${response.status} ${response.statusText} :: ${raw.slice(
              0,
              200
            )}`
          );
        }
        if (!contentType.includes("application/json")) {
          console.error("Respuesta no JSON del servidor:", raw);
          throw new Error(
            "El servidor no devolvió JSON (Content-Type inválido)"
          );
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
            toastr[data.type || "error"](
              data.message || "Acción no permitida",
              data.title || "Atención"
            );
          } else {
            alert(
              `${data.title || "Atención"}\n${
                data.message || "Acción no permitida"
              }`
            );
          }
          return;
        }

        if (typeof toastr !== "undefined") {
          toastr[data.type || "success"](
            data.message || "Operación exitosa",
            data.title || "OK"
          );
        } else {
          alert(
            `${data.title || "OK"}\n${data.message || "Operación exitosa"}`
          );
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

// Login Page Flipbox control
$('.login-content [data-toggle="flip"]').click(function () {
  $(".login-box").toggleClass("flipped");
  return false;
});

Swal.fire({
  icon: "success",
  title: "Modificado",
  text: "El usuario se modificó correctamente.",
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
});
