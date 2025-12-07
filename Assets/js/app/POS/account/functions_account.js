import { ApiAccount } from "./functions_account_api.js";
export class Account {
  #btnSetCode = $("#btnSetCode");
  #btnVerifyCode = $("#btnVerifyCode");
  #codeEncripted = null;

  constructor() {
    this.#funcionPrueba();
    this.#funcionChange();
  }

  #funcionPrueba = () => {
    console.log("Estamos en el registro de cuenta");
  };

  #funcionChange = () => {
    // referencias a los pasos para luego mostrar/ocultar los pasos
    const step1 = document.getElementById("step-1");
    const step2 = document.getElementById("step-2");
    const step3 = document.getElementById("step-3");

    // Botones en donde se hara clic para avanzar de paso
    const btnStep1 = document.getElementById("btn-step-1");
    const btnStep2 = document.getElementById("btn-step-2");

    // Función para mostrar un paso y ocultar los demás
    const showStep = (stepToShow) => {
      [step1, step2, step3].forEach((step) => {
        if (!step) return;
        step.classList.add("d-none");
      });

      if (stepToShow) stepToShow.classList.remove("d-none");
    };

    // Eventos
    if (btnStep1) {
      btnStep1.addEventListener("click", (e) => {
        e.preventDefault();
        showStep(step2);
      });
    }

    if (btnStep2) {
      btnStep2.addEventListener("click", (e) => {
        e.preventDefault();
        showStep(step3);
      });
    }
  };

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
      let codeEncripted = this.#codeEncripted;

      this.ApiAccount.post("verifyCode", {
        code: code,
        codeEncripted: codeEncripted,
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
