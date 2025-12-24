import { ApiResetpassword } from "./functions_resetpassword_api.js";
export class Resetpassword {
  #cardAccount = $("#cardAccount");
  #verificationCode = null;
  #verificationMail = null;

  constructor() {
    this.ApiResetpassword = new ApiResetpassword(base_url);
    this.init();
  }

  init() {
    this.#viewCardOne();
  }

  #bindEventSendCode = () => {
    $("#btnSendCode").on("click", () => {
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

      this.ApiResetpassword.post("sendCodeVerification", {
        email: email,
        accept_terms: accept_terms,
      }).then((response) => {
        if (response.status) {
          this.#verificationMail = email;
          this.#cardAccount.html(this.#viewCardTwo());
          this.#sendVerificationCode();
        }
        showAlert({
          icon: response.type,
          title: response.title,
          message: response.message,
        });
      });
    });
  };

  #sendVerificationCode = () => {
    $("#btnVerifyCode").on("click", () => {
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

      this.ApiResetpassword.post("validateVerificationCode", {
        code: code,
      }).then((response) => {
        if (response.status) {
          this.#verificationCode = code;
          this.#cardAccount.html(this.#viewCardTheree());
          this.#setUpdatePassword();
        }
        showAlert({
          icon: response.type,
          title: response.title,
          message: response.message,
        });
      });
    });
  };

  #setUpdatePassword = () => {
    $("#btnCreateAccount").on("click", () => {
      const password = $("#password").val();
      const confirmPassword = $("#confirmPassword").val();

      const email = this.#verificationMail;
      const code = this.#verificationCode;

      if (!email || !code) {
        return showAlert({
          icon: "warning",
          title: "Validación de datos",
          message:
            "No se encontró el correo o el código verificado. Vuelve a iniciar el proceso.",
        });
      }

      if (password === "" || confirmPassword === "") {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Los campos son obligatorios",
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

      if (password !== confirmPassword) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Las contraseñas no coinciden",
        });
      }

      showAlert({ message: "Actualizando contraseña, espere." }, "loading");

      this.ApiResetpassword
        .post("updatePassword", {
          email: email,
          code: code,
          password: password,
          confirmPassword: confirmPassword,
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
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
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
                                    <span>Cambia tu contraseña</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al ser parte de <span class="text-primary">Capy Ventas</span> podrás:</p>
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
                            <img src="${media_url}/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Recuperar contraseña</h2>
                    </div>
                    <form id="formAccount" class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu correo electrónico</label>
                            <div class="text-muted small mb-2">Te enviaremos un código de verificación por <span class="fw-bold">correo electrónico</span></div>

                            <div class="input-group mb-3">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-envelope-at"></i>
                                </span>
                                <input type="email" class="form-control" placeholder="Escribe tu correo electrónico" id="email">
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="accept_terms">
                            <label class="form-check-label small text-muted" for="termsCheck">
                                He leído y acepto los <a href="#" class="text-dark fw-bold">Términos y Condiciones</a>, y autorizo expresamente el tratamiento de mis datos personales conforme a la <a href="#" class="text-dark fw-bold">Política de Privacidad</a>.
                            </label>
                        </div>

                        <button id="btnSendCode" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Enviar código</button>
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
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
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
                                    <span>Cambia tu contraseña</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al ser parte de <span class="text-primary">Capy Ventas</span> podrás:</p>
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
                            <img src="${media_url}/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Verificación del código</h2>
                    </div>
                    <form class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu código de verificación</label>
                            <div class="text-muted small mb-2">El código de verificación se envío a tu <span class="fw-bold">correo electrónico</span></div>

                            <div class="input-group mb-3">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-envelope-check-fill"></i>
                                </span>
                                <input id="code" type="input" class="form-control" placeholder="Escribe tu código de verificación">
                            </div>
                        </div>

                        <!-- <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsCheck">
                            <label class="form-check-label small text-muted" for="termsCheck">
                                He leído y acepto los <a href="#" class="text-dark fw-bold">Términos y Condiciones</a>, y autorizo expresamente el tratamiento de mis datos personales conforme a la <a href="#" class="text-dark fw-bold">Política de Privacidad</a>.
                            </label>
                        </div> -->

                        <button id="btnVerifyCode" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Verificar código</button>
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
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
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
                                    <span>Cambia tu contraseña</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al ser parte de <span class="text-primary">Capy Ventas</span> podrás:</p>
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
                            <img src="${media_url}/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Cambia tu contraseña</h2>
                    </div>

                    <form class="account-form">
                       
                        <!-- Contraseña -->
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-12">
                                    <label class="form-label fw-bold">Nueva contraseña: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-file-lock2-fill"></i>
                                        </span>
                                        <input id="password" type="password" class="form-control" placeholder="Escribe tu contraseña">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-12">
                                    <label class="form-label fw-bold">Repite contraseña: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-file-lock2-fill"></i>
                                        </span>
                                        <input id="confirmPassword" type="password" class="form-control" placeholder="Escribe tu contraseña">
                                    </div>
                                </div>

                            </div>

                        </div>


                        <button id="btnCreateAccount" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Actualizar contraseña</button>
                    </form>

                    

                </div>
            </div>`;
    return html;
  };
}

new Resetpassword();
