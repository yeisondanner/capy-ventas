import { ApiBox } from "./functions_box_api.js";

export class Box {
  #rolesTable = null;
  // TODO: Datos que ingresan al formulario
  #permissions = new Map();

  // TODO: Seleccionamos los botones
  #btnOpenModalBox = $("#btnOpenModalBox");
  #btnOpenModalGestionBox = $("#btnOpenModalGestionBox");
  #btnOpenModalArqueoBox = $("#btnOpenModalArqueoBox");
  #btnAddRole = $("#btnAddRole");
  #btnUpdateRole = $("#btnUpdateRole");
  #btnDeleteRole = $("#btnDeleteRole");

  // TODO: Seleccionamos los modals
  #modalAddBox = $("#modalAddBox");
  #modalGestionBox = $("#modalGestionBox");
  #modalArqueoBox = $("#modalArqueoBox");
  #modalUpdateRole = $("#modalUpdateRole");
  #modalDeleteRole = $("#modalDeleteRole");
  #modalReportRole = $("#modalReportRole");

  // TODO: Seleccionamos los html
  #selectBox = $("#selectBox");


  constructor(base_url) {
    this.apiBox = new ApiBox(base_url);
    // this.#initTable();
    this.#openModal();
    this.#mostrarHoraEnVivo();
    setInterval(this.#mostrarHoraEnVivo, 1000);
  }

  // TODO: Funcion para mostrar la hora dinamica
  #mostrarHoraEnVivo = () => {
    const reloj = document.getElementById("reloj");
    const reloj2 = document.getElementById("reloj_2");
    const ahora = new Date();
    const horaTexto = ahora.toLocaleTimeString("es-PE", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: true,
    });

    reloj.innerText = horaTexto;
    reloj2.innerText = horaTexto;
  };

  // TODO: Funcion para traer todas las cajas asociadas al negocio
  #getBoxs = () => {
    this.apiBox.get("getBoxs").then((response) => {
      if(!response.status){
        showAlert({
          icon: response.type,
          title: response.title,
          message: response.message,
        });

        return false;
      }

      return true;
      
    });
    console.log('todo correcto.');
  };

  // TODO: Funcion para cargar los roles asociados

  // TODO: Funcion para abrir todos los modals
  #openModal = () => {
    // * Open Modal Box
    $(this.#btnOpenModalBox).click(() => {
      console.log(this.#getBoxs());
      ;
      if(this.#getBoxs()){
        this.#modalAddBox.modal("show");
      }
    });

    // * Open Modal Gestión Box
    $(this.#btnOpenModalGestionBox).click(() => {
      this.#modalGestionBox.modal("show");
    });

    // * Open Modal Arqueo Box
    $(this.#btnOpenModalArqueoBox).click(() => {
      this.#modalArqueoBox.modal("show");
    });
  };
}

new Box(base_url);
