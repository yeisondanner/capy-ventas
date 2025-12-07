import { ApiAccount } from "./functions_account_api.js";
export class Account {
  #btnSetCode = $("#btnSetCode");
  #btnVerifyCode = $("#btnVerifyCode");


  constructor() {
    this.#sendCodeVerification();
    this.#sendVerificationCode();
  }



  #sendCodeVerification = () => {
    this.#btnSetCode.click(() => {
      // ? Validamos que se envie el correo electronico
      let email = $("#email").val();
      if (!email) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El Correo Electrónico es requerido",
        });
      }

      let accept_terms = $("#accept_terms").is(":checked");
      if (!accept_terms) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "Debes aceptar los términos y condiciones",
        });
      }

      this.ApiAccount.post("sendCodeVerification", {
        email: email,
        accept_terms: accept_terms,
      }).then((response) => {
        if (response.status) {
          $("#setCode").removeClass("d-none");
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
     

      this.ApiAccount.post("validateVerificationCode", {
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
