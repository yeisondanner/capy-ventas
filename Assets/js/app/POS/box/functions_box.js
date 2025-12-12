import { ApiBox } from "./functions_box_api.js";

export class Box {
  #rolesTable = null;
  // TODO: Datos que ingresan al formulario
  #permissions = new Map();

  // TODO: Seleccionamos los botones
  #btnOpenModalBox = $("#btnOpenModalBox");
  #btnOpenModalGestionBox = $("#btnOpenModalGestionBox");
  #btnOpenModalArqueoBox = $("#btnOpenModalArqueoBox");
  #btnOpenBox = $("#btnOpenBox");
  #btnUpdateRole = $("#btnUpdateRole");
  #btnDeleteRole = $("#btnDeleteRole");

  // TODO: Seleccionamos los modals
  #modalAddBox = $("#modalAddBox");
  #modalGestionBox = $("#modalGestionBox");
  #modalArqueoBox = $("#modalArqueoBox");
  #modalUpdateRole = $("#modalUpdateRole");
  #modalDeleteRole = $("#modalDeleteRole");
  #modalReportRole = $("#modalReportRole");

  // TODO: Seleccionamos los html y valores
  #selectBox = $("#selectBox");
  #cashOpeningAmount = $("#cash_opening_amount");

  constructor(base_url) {
    this.apiBox = new ApiBox(base_url);
    // this.#initTable();
    this.#openModal();
    this.#mostrarHoraEnVivo();
    setInterval(this.#mostrarHoraEnVivo, 1000);

    this.#openBox();
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
  #getBoxs = async () => {
    const response = await this.apiBox.get("getBoxs");
    if (!response.status) {
      showAlert({
        icon: response.type,
        title: response.title,
        message: response.message,
      });
    }
    return response;
  };

  // TODO: Funcion para aperturar caja
  #openBox = () => {
    this.#btnOpenBox.click(async () => {
      // ? Validamos la seleccion de caja
      if (!this.#selectBox.val()) {
        return showAlert({
          icon: "warning",
          title: "Validación de campos",
          message: "Seleccione una caja para iniciar turno.",
        });
      }

      // ? Validamos si ingresa el monto inicial de caja
      if (!this.#cashOpeningAmount.val() || this.#cashOpeningAmount.val() < 0) {
        return showAlert({
          icon: "warning",
          title: "Validación de campos",
          message: "El monto ingresado debe ser igual o mayor que 0",
        });
      }

      // ? Registramos la apertura de caja
      const response = await this.apiBox.post("setOpenBox", {
        box_id: this.#selectBox.val(),
        cash_opening_amount: this.#cashOpeningAmount.val(),
      });

      console.log(response);
      
    });
  };

  // TODO: Funcion para cargar los roles asociados

  // TODO: Funcion para abrir todos los modals
  #openModal = () => {
    // * Open Modal Box
    $(this.#btnOpenModalBox).click(async () => {
      const boxs = await this.#getBoxs();
      if (boxs && boxs.status) {
        let html =
          '<option value="" disabled selected>Seleccione una caja...</option>';
        boxs.data.forEach((box, index) => {
          if (box.session === "Activo") {
            html += `<option value="${box.idBox}">Caja ${index + 1} - ${
              box.name
            }</option>`;
          } else if (box.session === "Inactivo") {
            html += `<option class="text-danger fw-bold" disabled value="">Caja ${
              index + 1
            } - ${box.name} (Desabilitado)</option>`;
          } else if (box.session === "Abierta") {
            html += `<option class="text-primary fw-bold" disabled value="">Caja ${
              index + 1
            } - ${box.name} (En uso)</option>`;
          } else {
            html += `<option class="text-warning fw-bold" disabled value="">Caja ${
              index + 1
            } - ${box.name} (En Arqueo)</option>`;
          }
        });
        this.#selectBox.html(html);
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
