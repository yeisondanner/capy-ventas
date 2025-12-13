import { ApiBox } from "./functions_box_api.js";

export class Box {
  // TODO: Seleccionamos los botones
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
  #divOpenBox = $("#divOpenBox");
  #selectBox = $("#selectBox");
  #cashOpeningAmount = $("#cash_opening_amount");
  #quick_access_base_amount = $("#quick_access_base_amount");
  #quick_access_total_general = $("#quick_access_total_general");
  #quick_access_card_payment_method = $("#quick_access_card_payment_method");
  #quick_access_card_list_movements = $("#quick_access_card_list_movements");
  #quick_access_title_list_movements = $("#quick_access_title_list_movements");

  constructor(base_url) {
    this.apiBox = new ApiBox(base_url);
    this.#init();
    this.#mostrarHoraEnVivo();
    setInterval(this.#mostrarHoraEnVivo, 1000);

    this.#openBox();
  }

  // TODO: Funcion que se usa al cargar la vista
  #init = async () => {
    // ? Verificamos que el usuario no tenga aperturada una caja
    const response = await this.apiBox.get("getuserCheckedBox");
    if (response.status) {
      // * Agregamos el boton de apertura de caja
      this.#divOpenBox.html(`
          <div class="d-flex justify-content-center align-items-center">
                      <button id="btnOpenModalBox" class="btn btn-warning rounded-4 px-2 d-flex align-items-center gap-2 fw-bold">
                          <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
                          <span class="fw-semibold">Abrir Caja</span>
                      </button>
                  </div>
          `);
    } else {
      // * Agregamos el boton de movimientos y gestion de caja
      this.#divOpenBox.html(`
        <div class="d-flex justify-content-center align-items-center">
                    <button id="btnOpenModalGestionBox" class="btn btn-warning rounded-4 px-2 d-flex align-items-center gap-2 fw-bold">
                        <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
                        <span class="fw-semibold">Gestión de Caja</span>
                    </button>
                </div>
        `);
    }
    this.#openModal();
  };

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

      if (response) {
        showAlert({
          title: response.title,
          icon: response.icon,
          message: response.message,
        });

        // ? Si es correcto cerramos el modal
        if (response.status) {
          this.#modalAddBox.modal("hide");

          // ? Limpiamos el forulario de registro
          // this.#selectBox.val();
          this.#cashOpeningAmount.val(0);

          // ? Verificamos nuevamente si ya tiene aperturado su caja
          this.#init();
        }
      }
    });
  };

  // TODO: Funcion para cargar los roles asociados

  // TODO: Funcion para abrir todos los modals
  #openModal = () => {
    // * Open Modal Box
    $("#btnOpenModalBox").on("click", async () => {
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
    $("#btnOpenModalGestionBox").on("click", async () => {
      // ? Traemos los datos necesarios para mostrar en la vista
      const response = await this.apiBox.get("getManagementBox");
      if (!response.status) {
        return showAlert({
          title: response.title,
          icon: response.icon,
          message: response.message,
        });
      }

      // ? Mostramos los datos de gestion
      this.#quick_access_total_general.html(
        this.#convertirASoles(response.total_general)
      );
      this.#quick_access_base_amount.html(
        `Base: ${this.#convertirASoles(response.amount_base)}`
      );
      let html = "";
      response.payment_method.forEach((element) => {
        if (
          typeof response.total_payment_method[element.name] !== "undefined"
        ) {
          html += `<div class="col-4">
                      <div class="card border rounded-4 h-100 bg-body-tertiary">
                          <div class="card-body p-3 text-center">
                              <span class="d-inline-flex align-items-center justify-content-center border bg-white rounded-circle mb-2" style="width: 35px; height: 35px;">
                                  ${element.icon}
                              </span>
                              <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">${
                                element.name
                              }</div>
                              <h6 class="fw-bold mb-0 text-dark">${this.#convertirASoles(
                                response.total_payment_method[element.name]
                              )}</h6>
                          </div>
                      </div>
                  </div>`;
        }
      });
      this.#quick_access_card_payment_method.html(html);

      // ? Renombramos el titulo de los ultimos movimientos
      this.#quick_access_title_list_movements.html(`Últimos <span class="text-primary">${response.movements_limit.length}</span> Movimientos`);

      // ? Mostramos los datos de movimientos
      let card_html = "";
      response.movements_limit.forEach((element) => {
        if (element.type_movement === "Inicio") {
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-key-fill"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 text-dark fw-bold">${element.concept}</h6>
                                    <small class="text-muted">${this.#timeAgoModerno(element.movement_date)}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block text-success fw-bold">+${this.#convertirASoles(element.amount)}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${element.payment_method}</small>
                                </div>
                            </div>
                        </div>`;
        }else if(element.type_movement === "Ingreso"){
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-cart-fill"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${element.concept}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">${this.#timeAgoModerno(element.movement_date)}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block fw-bold text-success">+${this.#convertirASoles(element.amount)}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${element.payment_method}</small>
                                </div>
                            </div>
                        </div>`;
        }else{
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-arrow-up-right"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${element.concept}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">${this.#timeAgoModerno(element.movement_date)}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block fw-bold text-danger">-${this.#convertirASoles(element.amount)}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${element.payment_method}</small>
                                </div>
                            </div>
                        </div>`;
        }
      });
      this.#quick_access_card_list_movements.html(card_html);

      this.#modalGestionBox.modal("show");
    });

    // * Open Modal Arqueo Box
    $("#btnOpenModalArqueoBox").on("click", () => {
      this.#modalArqueoBox.modal("show");
    });
  };

  // TODO: Funcion para convertira a moneda un numero
  #convertirASoles = (valor) => {
    // * Convertimos a número por si viene como string "10.50"
    const numero = Number(valor);

    return new Intl.NumberFormat("es-PE", {
      style: "currency",
      currency: "PEN",
      minimumFractionDigits: 2,
    }).format(numero);
  };

  // TODO: Funcion para cargar el tiempo relativo de la fecha de registro
  #timeAgoModerno = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = (date - now) / 1000; // ? Diferencia en segundos (será negativo)
    const rtf = new Intl.RelativeTimeFormat("es", { numeric: "auto" });

    // ? Lógica rápida para seleccionar la unidad
    if (Math.abs(diff) < 60) return rtf.format(Math.round(diff), "second");
    if (Math.abs(diff) < 3600)
      return rtf.format(Math.round(diff / 60), "minute");
    if (Math.abs(diff) < 86400)
      return rtf.format(Math.round(diff / 3600), "hour");
    return rtf.format(Math.round(diff / 86400), "day");
  };
}

new Box(base_url);
