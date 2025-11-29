import { ApiAccount } from "./functions_account_api.js";
export class Account {
  constructor() {
    this.#funcionPrueba();
  }

  #funcionPrueba() {
    console.log("Estamos en el registro de cuenta");
  }
}

new Account();
