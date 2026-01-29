import { ApiAccount } from "./functions_account_api.js";
export class Account {
  #cardAccount = $("#cardAccount");
  #verificationCode = null;
  #email = null;

  constructor() {
    this.apiAccount = new ApiAccount(base_url);
    this.init();
  }

  init() {
    this.#viewCardOne();
  }
  /**
   * Funcion para enviar el correo electronico
   */
  #bindEventSendCode = () => {
    $("#formSendCode").on("submit", (e) => {
      e.preventDefault();
      // ? Validamos que se envie el correo electronico
      let email = $("#email").val();
      if (!email) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El Correo Electrónico es requerido",
        });
      }

      const formatEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,150}$/;
      if (!formatEmail.test(email)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Correo electrónico inválido.",
        });
      }

      // TODO: Validar el formato de correo electronico
      let accept_terms = $("#accept_terms").is(":checked");

      if (!accept_terms) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Debes aceptar los términos y condiciones",
        });
      }

      showAlert({ message: "Enviando código de verificación." }, "loading");

      this.apiAccount
        .post("sendCodeVerification", {
          email: email,
          accept_terms: accept_terms,
        })
        .then((response) => {
          if (response.status) {
            this.#cardAccount.html(this.#viewCardTwo());
            this.#sendVerificationCode();
            this.#email = email;
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };
  /**
   * Funcion para enviar el codigo de verificacion
   */
  #sendVerificationCode = () => {
    $("#formVerifyCode").on("submit", (e) => {
      e.preventDefault();
      // ? Validamos que se envie el correo electronico
      let code = $("#code").val();

      if (!code) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El código es requerido",
        });
      }

      if (code.length < 6) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El código debe tener mínimo 6 caracteres.",
        });
      }

      showAlert({ message: "Verificando código." }, "loading");

      this.apiAccount
        .post("validateVerificationCode", {
          code: code,
        })
        .then((response) => {
          if (response.status) {
            this.#verificationCode = code;
            this.#cardAccount.html(this.#viewCardTheree());
            this.#setAccount();
            const p1 = document.getElementById("password");
            const p2 = document.getElementById("confirm_password");
            p1.addEventListener("input", () => {
              this.checkPasswords(p1, p2);
            });
            p2.addEventListener("input", () => {
              this.checkPasswords(p1, p2);
            });
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };
  /**
   * Metodo que se encarga de crear la cuenta
   */
  #setAccount = () => {
    $("#formAccount").on("submit", (e) => {
      e.preventDefault();
      let names = $("#names").val();
      let lastname = $("#lastname").val();
      let email = this.#email;
      let date_of_birth = $("#date_of_birth").val();
      let country = $("#country").val();
      let telephone_prefix = $("#telephone_prefix").val();
      let phone_number = $("#phone_number").val();
      let password = $("#password").val();
      let confirm_password = $("#confirm_password").val();
      let username = $("#username").val();

      if (
        names === "" ||
        lastname === "" ||
        email === "" ||
        date_of_birth === "" ||
        country === "" ||
        telephone_prefix === "" ||
        phone_number === "" ||
        password === "" ||
        confirm_password === "" ||
        username === ""
      ) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Los campos son obligatorios",
        });
      }

      // * VALIDACION PARA NOMBRES Y APELLIDOS
      const formatText = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ' .-]{2,80}$/;
      if (!formatText.test(names)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message:
            "Nombres inválidos (Solo letras y espacios, mínimo 2 caracteres y máximo 80).",
        });
      }

      if (!formatText.test(lastname)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message:
            "Apellidos inválidos (Solo letras y espacios, mínimo 2 caracteres y máximo 80).",
        });
      }

      // * VALIDACION PARA EMAIL
      const formatEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,150}$/;
      if (!formatEmail.test(email)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Correo electrónico inválido.",
        });
      }

      // * VALIDACION PARA FECHA DE NACIMIENTO. PD. ESTO SI LO HICE CON CHATGPT, NO SABIA JAJJAA
      const dob = new Date(date_of_birth);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (Number.isNaN(dob.getTime())) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Fecha de nacimiento inválida.",
        });
      }

      let age = today.getFullYear() - dob.getFullYear();
      const m = today.getMonth() - dob.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;

      if (age < 18) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Debes tener al menos 18 años para registrarte.",
        });
      }

      //VALIDACION PARA PAIS
      if (!formatText.test(country)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message:
            "País inválido (Solo letras y espacios, mínimo 2 caracteres).",
        });
      }

      //VALIDACION PARA PREFIJO TELEFONICO
      const formatPrefiij = /^\+?[1-9]\d{1,3}$/;
      if (!formatPrefiij.test(telephone_prefix)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Prefijo telefónico inválido (Ejemplo: +51).",
        });
      }

      //VALIDACION PARA NUMERO DE TELEFONO
      const phoneRegex = /^\d{9}$/;
      if (!phoneRegex.test(phone_number)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Número de teléfono inválido, debe tener 9 dígitos.",
        });
      }

      //VALIDACION PARA CONTRASEÑA
      if (password.length < 6) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "La contraseña debe tener mínimo 6 caracteres.",
        });
      }

      if (password !== confirm_password) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Las contraseñas no coinciden",
        });
      }
      const formatUsername = /^[a-zA-Z0-9_-]{6,10}$/;
      if (!formatUsername.test(username)) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message:
            "El nombre de usuario debe tener mínimo 6 caracteres y máximo 10, solo letras, números y guiones.",
        });
      }

      showAlert({ message: "Creando cuenta, espere." }, "loading");

      this.apiAccount
        .post("setAccount", {
          code: this.#verificationCode,
          names: names,
          lastname: lastname,
          email: email,
          date_of_birth: date_of_birth,
          country: country,
          telephone_prefix: telephone_prefix,
          phone_number: phone_number,
          password: password,
          confirm_password: confirm_password,
          username: username,
        })
        .then((response) => {
          if (response.status) {
            setTimeout(() => {
              window.location.href = "./login";
            }, 3000);
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };

  #viewCardOne = () => {
    const html = `<div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="text-white" style="width: 50px; height: 50px;"><img src="${media_url}/capysm.png" alt="Logo" class="img-fluid rounded-circle"></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-4">
                    <div class="text-center mb-4 mt-lg-5">
                        <div class="login-head">
                            <img src="${media_url}/capymd.png" alt="Logo capy ventas">
                        </div>
                        <h2 class="fw-bold">Regístrate para comenzar</h2>
                    </div>
                    <form id="formSendCode" class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu correo electrónico</label>
                            <div class="text-muted small mb-2">Te enviaremos un código de verificación por <span class="fw-bold">correo electrónico</span></div>

                            <div class="input-group mb-3">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-envelope-at"></i>
                                </span>
                                <input type="email" class="form-control" placeholder="Escribe tu correo electrónico" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="accept_terms" name="accept_terms" required>
                            <label class="form-check-label small text-muted" for="termsCheck">
                                He leído y acepto los <a href="#" class="text-dark fw-bold">Términos y Condiciones</a>, y autorizo expresamente el tratamiento de mis datos personales conforme a la <a href="#" class="text-dark fw-bold">Política de Privacidad</a>.
                            </label>
                        </div>

                        <button id="btnSendCode" type="submit" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Enviar código <i class="bi bi-arrow-right"></i></button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta en <strong>Capy Ventas</strong>?</span>
                        <a href="./login" class="text-primary fw-bold text-decoration-none">Iniciar sesión</a>
                    </div>

                </div>
            </div>`;

    this.#cardAccount.html(html);
    this.#bindEventSendCode();
  };

  #viewCardTwo = () => {
    const html = `<div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="text-white" style="width: 50px; height: 50px;"><img src="${media_url}/capysm.png" alt="Logo" class="img-fluid rounded-circle"></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-4">
                    <div class="text-center mb-4 mt-lg-5">
                        <div class="login-head">
                            <img src="${media_url}/capymd.png" alt="">
                        </div>
                        <h2 class="fw-bold">Verificación del código</h2>
                    </div>
                    <form id="formVerifyCode" class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu código de verificación</label>
                            <div class="text-muted small mb-2">El código de verificación se envío a tu <span class="fw-bold">correo electrónico</span></div>
                            <div class="input-group">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-123"></i>
                                </span>
                                <input id="code" name="code" type="text" class="form-control" placeholder="Escribe tu código de verificación" required>
                            </div>
                            <span class="text-muted small">Revisa el código de verificación en tu <span class="fw-bold">spam</span> o <span class="fw-bold">correo no deseado</span> si no lo encuentras en tu <span class="fw-bold">bandeja de entrada</span></span>
                        </div>
                        <button id="btnVerifyCode" type="submit" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Verificar código <i class="bi bi-arrow-right"></i></button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta en <strong>Capy Ventas</strong>?</span>
                        <a href="./login" class="text-primary fw-bold text-decoration-none">Iniciar sesión</a>
                    </div>

                </div>
            </div>`;
    return html;
  };

  #viewCardTheree = () => {
    const html = `<div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="text-white" style="width: 50px; height: 50px;"><img src="${media_url}/capysm.png" alt="Logo" class="img-fluid rounded-circle"></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-4">
                    <div class="text-center mb-2 mt-lg-2">
                        <div class="login-head">
                            <img src="${media_url}/capymd.png" alt="">
                        </div>
                        <h2 class="fw-bold">Crea tu cuenta</h2>
                    </div> 

                    <form id="formAccount" class="account-form">
                        <h5 class="text-left fw-bold mb-3 text-primary"><i class="bi bi-person-fill"></i> Datos personales</h5>
                        <!-- nombres y apellidos -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombres <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input id="names" type="text" class="form-control" placeholder="Escriba sus nombres" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input id="lastname" type="text" class="form-control" placeholder="Escriba sus apellidos" required>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- email -->
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-envelope-fill"></i>
                                        </span>
                                        <input id="email" type="email" class="form-control" placeholder="Escriba su correo electrónico" value="${
                                          this.#email
                                        }" readonly disabled>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- fecha de nacimiento y pais -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Fecha de Nacimiento <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-calendar-event-fill"></i>
                                        </span>
                                        <input id="date_of_birth" type="date" class="form-control" placeholder="Seleccione su fecha de nacimiento" required>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">País <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-globe-americas-fill"></i>
                                        </span>
                                        <input id="country" type="text" class="form-control" placeholder="Escriba su país" value="Perú" readonly disabled>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- prefijo y numero de telefono -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Prefijo Tel. <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-telephone-fill"></i>
                                        </span>
                                        <input id="telephone_prefix" type="text" class="form-control" placeholder="+51" value="+51" readonly disabled>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Número de Teléfono <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-phone-fill"></i>
                                        </span>
                                        <input id="phone_number" type="number" class="form-control" placeholder="987654321" required>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr>
                        <h5 class="text-left fw-bold mb-3 text-primary"><i class="bi bi-lock-fill"></i> Datos de Acceso</h5>

                        <!-- prefijo y numero de telefono -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Usuario <span class="text-danger">*</span>: </label>

                                    <div class="input-group">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input id="username" name="username" type="text" class="form-control" placeholder="Ingrese un nombre de usuario" required minlength="6" maxlength="10">
                                    </div>
                                    <div class="form-text">El nombre de usuario debe tener entre <b>6</b> y <b>10</b> caracteres y solo <b>puede contener letras, números, guiones bajos y guiones medios.</b></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Contraseña <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-file-lock2-fill"></i>
                                        </span>
                                        <input id="password" type="password" class="form-control" placeholder="Ingrese su contraseña" required>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Repita la Contraseña <span class="text-danger">*</span>: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-file-lock2-fill"></i>
                                        </span>
                                        <input id="confirm_password" type="password" class="form-control" placeholder="Ingrese su contraseña" required>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <!-- Mensaje de Alerta (Bootstrap Alert) -->
                                <div id="msg-container" class="mb-3 d-none">
                                    <div id="msg-alert" class="alert py-2 px-3 small d-flex align-items-center" role="alert">
                                        <span id="msg-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button id="btnCreateAccount" type="submit" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Registrarse <i class="bi bi-arrow-right"></i></button>
                    </form>
                </div>
            </div>`;
    return html;
  };
  /**
   * Función que verifica que las contraseñas coincidan
   * @returns
   */
  checkPasswords(p1, p2) {
    const val1 = p1.value;
    const val2 = p2.value;
    // Elementos del DOM
    const msgContainer = document.getElementById("msg-container");
    const msgAlert = document.getElementById("msg-alert");
    const msgText = document.getElementById("msg-text");
    // Si el segundo campo está vacío, ocultamos el mensaje
    if (val2 === "") {
      msgContainer.classList.add("d-none");
      p2.classList.remove("is-valid", "is-invalid");
      return;
    }
    msgContainer.classList.remove("d-none");
    if (val1 === val2 && val1 !== "") {
      // Coinciden: Usamos clases de éxito de Bootstrap
      msgAlert.className = "alert alert-success py-2 px-3 small mb-0";
      msgText.innerText = "✓ Las contraseñas coinciden.";
      p2.classList.remove("is-invalid");
      p2.classList.add("is-valid");
    } else {
      // No coinciden: Usamos clases de error de Bootstrap
      msgAlert.className = "alert alert-danger py-2 px-3 small mb-0";
      msgText.innerText = "✕ Las contraseñas no coinciden.";
      p2.classList.remove("is-valid");
      p2.classList.add("is-invalid");
    }
  }
}

new Account();
