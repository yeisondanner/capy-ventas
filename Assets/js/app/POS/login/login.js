export class Login {
  /**
   * Encapsula la funcionalidad del login como privadas
   */
  #btnLogin = document.getElementById("btnLogin");
  #formLogin = document.getElementById("formLogin");
  #txtUser = document.getElementById("txtUser");
  #txtPassword = document.getElementById("txtPassword");
  #togglePassword = document.getElementById("togglePassword");
  #iconoPassword = document.getElementById("iconoPassword");
  constructor() {}
  /**
   * Metodo que se encarga de enviar los datos del formulario de login
   * @returns
   */
  login() {
    if (!this.#btnLogin) return;
    if (!this.#formLogin) return;
    this.#formLogin.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formdata = new FormData(this.#formLogin);
      const config = {
        method: "POST",
        body: formdata,
        cache: "no-store",
      };
      const url = base_url + "/Login/isLogIn";
      this.#disableInputs();
      const htmlOriginal = this.#btnLogin.innerHTML;
      this.#btnLogin.innerHTML = ` <div class="spinner-border mx-2" role="status">
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
            // window.location.href = data.url;
          }, data.timer);
          return;
        }
        this.#formLogin.reset();
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
        this.#formLogin.reset();
      } finally {
        this.#enableInputs();
        this.#btnLogin.innerHTML = htmlOriginal;
      }
    });
  }
  /**
   * Metodo que bloquea los campos del formulario de login
   */
  #disableInputs() {
    //adicionamos la clase disabled a los elementos
    this.#btnLogin.classList.add("disabled");
    this.#formLogin.classList.add("disabled");
    this.#txtUser.classList.add("disabled");
    this.#txtPassword.classList.add("disabled");
    this.#togglePassword.classList.add("disabled");
    //bloqueamos los elementos para evitar interacciones mientras se procesa la solicitud
    this.#btnLogin.setAttribute("disabled", "disabled");
    this.#txtUser.setAttribute("disabled", "disabled");
    this.#txtPassword.setAttribute("disabled", "disabled");
    this.#togglePassword.setAttribute("disabled", "disabled");
  }
  /**
   * Metodo que habilita los campos del formulario de login
   */
  #enableInputs() {
    //removemos la clase disabled a los elementos
    this.#btnLogin.classList.remove("disabled");
    this.#formLogin.classList.remove("disabled");
    this.#txtUser.classList.remove("disabled");
    this.#txtPassword.classList.remove("disabled");
    this.#togglePassword.classList.remove("disabled");
    //habilitamos los elementos para permitir interacciones
    this.#btnLogin.removeAttribute("disabled");
    this.#txtUser.removeAttribute("disabled");
    this.#txtPassword.removeAttribute("disabled");
    this.#togglePassword.removeAttribute("disabled");
  }
  /**
   * Metodo que permite mostrar u ocultar la contraseña en el campo correspondiente
   * @returns
   */
  togglePasswordVisibility() {
    if (!this.#togglePassword) return;
    this.#togglePassword.addEventListener("click", () => {
      const type =
        this.#txtPassword.getAttribute("type") === "password"
          ? "text"
          : "password";
      this.#txtPassword.setAttribute("type", type);
      // Cambiar el ícono según el estado
      if (type === "text") {
        this.#iconoPassword.classList.remove("bi-eye-fill");
        this.#iconoPassword.classList.add("bi-eye-slash");
      } else {
        this.#iconoPassword.classList.remove("bi-eye-slash");
        this.#iconoPassword.classList.add("bi-eye-fill");
      }
    });
  }
}
