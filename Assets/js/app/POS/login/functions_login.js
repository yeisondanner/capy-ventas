(() => {
  "use strict";
  const btnLogin = document.getElementById("btnLogin");
  window.addEventListener("DOMContentLoaded", () => {
    login(); // Funcion que se encarga de enviar los datos del formulario de login
  });
  //funcion para el login
  function login() {
    if (!btnLogin) return;
    const formLogin = document.getElementById("formLogin");
    formLogin.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formdata = new FormData(formLogin);
      const config = {
        method: "POST",
        body: formdata,
        cache: "no-store",
      };
      const url = base_url + "/Login/isLogIn";
      btnLogin.classList.add("disabled");
      const htmlOriginal = btnLogin.innerHTML;
      btnLogin.innerHTML = ` <div class="spinner-border mx-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Validando credenciales...`;
      try {
        const response = await fetch(url, config);
        if (!response.ok) {
          throw new Error(response.statusText + " - " + response.status);
        }
        const data = await response.json();
        showAlert(data);
        if (data.status) {
          setTimeout(() => {
            window.location.href = data.url;
          }, data.timer);
        }
      } catch (error) {
        data = {
          title: "Ocurrio un error inesperado",
          message: "Ocurrio un error con el servidor: " + error.name,
          icon: "error",
          timer: 4000,
        };
        showAlert({
          title: "Ocurrio un error inesperado",
          message: "Ocurrio un error con el servidor: " + error.name,
          icon: "error",
          timer: 4000,
        });
      } finally {
        btnLogin.classList.remove("disabled");
        btnLogin.innerHTML = htmlOriginal;
      }
    });
  }

  // Login Page Flipbox control
  $('.login-content [data-toggle="flip"]').click(function () {
    $(".login-box").toggleClass("flipped");
    return false;
  });
})();
