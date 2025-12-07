import { ApiAccount } from "./functions_account_api.js";
export class Account {
  #btnSendCode = $("#btnSendCode");
  #btnVerifyCode = $("#btnVerifyCode");

  constructor() {
    this.apiAccount = new ApiAccount(base_url);
    this.#sendCodeVerification();
    // this.#sendVerificationCode();
  }

  #sendCodeVerification = () => {
    this.#btnSendCode.click(() => {
      // ? Validamos que se envie el correo electronico
      let email = $("#email").val();
      if (!email) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El Correo Electrónico es requerido",
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

      this.apiAccount.post("sendCodeVerification", {
        email: email,
        accept_terms: accept_terms
      }).then((response) => {
        if (response.status) {
          $("#setCode").addClass("d-none");
          $("#verifyCode").removeClass("d-none");
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
    this.#btnVerifyCode.click(() => {
      // ? Validamos que se envie el correo electronico
      let code = $("#code").val();
      if (!code) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El código es requerido",
        });
      }

      this.apiAccount.post("validateVerificationCode", {
        code: code,
      }).then((response) => {
        if (response.status) {
          $("#verifyCode").removeClass("d-none");
        }
        showAlert({
          icon: response.type,
          title: response.title,
          message: response.message,
        });
      });
    });
  };
}

new Account();
