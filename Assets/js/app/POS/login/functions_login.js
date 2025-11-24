import { Login } from "./login.js";
(() => {
  "use strict";
  //obtenemos el objeto de la clase Login
  const loginInstance = new Login();
  window.addEventListener("DOMContentLoaded", () => {
    loginInstance.login(); // Funcion que se encarga de enviar los datos del formulario de login
    loginInstance.togglePasswordVisibility();
  });
  // Login Page Flipbox control
  $('.login-content [data-toggle="flip"]').click(function () {
    $(".login-box").toggleClass("flipped");
    return false;
  });
})();
