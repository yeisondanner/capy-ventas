import { ApiAccount } from "./functions_account_api.js";
export class Account {
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
}

new Account();
