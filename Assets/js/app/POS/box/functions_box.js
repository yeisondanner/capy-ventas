import { ApiBox } from "./functions_box_api.js";

export class Box {
  // TODO: Informacion de movements de BOX
  #arrayMovements = [];
  #arrayCountEfectivo = new Map();
  #countEfectivoTotal = 0;
  #sisEfectivoTotal = 0;

  // TODO: Seleccionamos los botones
  #btnOpenBox = $("#btnOpenBox");

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

  // TODO: Captura de elementos para arqueo
  #quick_access_arqueo_total_efectivo = $(
    "#quick_access_arqueo_total_efectivo"
  );
  #quick_access_arqueo_total_general = $("#quick_access_arqueo_total_general");
  #quick_access_arqueo_total_payment_method = $(
    "#quick_access_arqueo_total_payment_method"
  );
  #quick_access_arqueo_currency_denominations = $(
    "#quick_access_arqueo_currency_denominations"
  );
  #quick_access_arqueo_count_efectivo = $(
    "#quick_access_arqueo_count_efectivo"
  );
  #quick_access_arqueo_message = $("#quick_access_arqueo_message");
  #quick_access_arqueo_diference = $("#quick_access_arqueo_diference");
  #quick_access_desgloce_efectivo = $("#quick_access_desgloce_efectivo");

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
                      <button id="btnOpenModalBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
                          <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
                          <span class="fw-semibold">Abrir Caja</span>
                      </button>
                  </div>
          `);
    } else {
      // * Agregamos el boton de movimientos y gestion de caja
      this.#divOpenBox.html(`
        <div class="d-flex justify-content-center align-items-center">
                    <button id="btnOpenModalGestionBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
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

  // TODO: Funcion para abrir todos los modals (Y Configurar eventos)
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

      // ? Cargamos los datos de movimientos de caja
      this.#arrayMovements = {
        amount_base: response.amount_base,
        total_general: response.total_general,
        movements_limit: response.movements_limit,
        payment_method: response.payment_method,
        total_payment_method: response.total_payment_method,
      };

      // ? Mostramos los datos de gestion
      this.#quick_access_total_general.html(
        this.#convertirASoles(this.#arrayMovements.total_general)
      );
      this.#quick_access_base_amount.html(
        `Base: ${this.#convertirASoles(this.#arrayMovements.amount_base)}`
      );
      let html = "";
      this.#arrayMovements.payment_method.forEach((element) => {
        if (
          typeof this.#arrayMovements.total_payment_method[element.name] !==
          "undefined"
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
                                this.#arrayMovements.total_payment_method[
                                  element.name
                                ]
                              )}</h6>
                          </div>
                      </div>
                  </div>`;
        }
      });
      this.#quick_access_card_payment_method.html(html);

      // ? Renombramos el titulo de los ultimos movimientos
      this.#quick_access_title_list_movements.html(
        `Últimos <span class="text-primary">${
          this.#arrayMovements.movements_limit.length
        }</span> Movimientos`
      );

      // ? Mostramos los datos de movimientos
      let card_html = "";
      this.#arrayMovements.movements_limit.forEach((element) => {
        if (element.type_movement === "Inicio") {
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-key-fill"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 text-dark fw-bold">${
                                      element.concept
                                    }</h6>
                                    <small class="text-muted">${this.#timeAgoModerno(
                                      element.movement_date
                                    )}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block text-success fw-bold">+${this.#convertirASoles(
                                      element.amount
                                    )}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${
                                      element.payment_method
                                    }</small>
                                </div>
                            </div>
                        </div>`;
        } else if (element.type_movement === "Ingreso") {
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-cart-fill"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${
                                      element.concept
                                    }</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">${this.#timeAgoModerno(
                                      element.movement_date
                                    )}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block fw-bold text-success">+${this.#convertirASoles(
                                      element.amount
                                    )}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${
                                      element.payment_method
                                    }</small>
                                </div>
                            </div>
                        </div>`;
        } else {
          card_html += `<div class="list-group-item px-3 py-3 border-bottom-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-arrow-up-right"></i>
                                </div>
                                <div class="flex-fill lh-1">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${
                                      element.concept
                                    }</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">${this.#timeAgoModerno(
                                      element.movement_date
                                    )}</small>
                                </div>
                                <div class="text-end lh-1">
                                    <span class="d-block fw-bold text-danger">-${this.#convertirASoles(
                                      element.amount
                                    )}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${
                                      element.payment_method
                                    }</small>
                                </div>
                            </div>
                        </div>`;
        }
      });
      this.#quick_access_card_list_movements.html(card_html);

      this.#arrayMovements = this.#arrayMovements;

      this.#modalGestionBox.modal("show");
    });

    // * Open Modal Arqueo Box
    $("#btnOpenModalArqueoBox").on("click", async () => {
      // ? 1. Cargar datos del resumen (Totales y Tarjetas)
      this.#renderResumenArqueo();

      // ? 2. Obtener denominaciones del servidor
      const response = await this.apiBox.get("getCurrencyDenominations");

      if (!response.status) {
        return showAlert({
          title: response.title,
          icon: response.icon,
          message: response.message,
        });
      }

      // ? 3. Procesar y Renderizar las denominaciones (Billetes/Monedas)
      this.#procesarDenominaciones(response.data);

      // ? 4. Limpiamos los datos del formulario
      this.#clearForm();

      // ? 4. Mostrar el modal
      this.#modalArqueoBox.modal("show");
    });

    // ! ================================================================
    // ! LÓGICA AGREGADA: EVENTO DELEGADO PARA ACTUALIZAR MAP
    // ! ================================================================
    this.#quick_access_arqueo_currency_denominations.on(
      "input",
      "input[type='number']",
      (e) => {
        const input = $(e.currentTarget);
        const cantidad = parseFloat(input.val()) || 0;
        const idDenomination = input.data("id");

        if (this.#arrayCountEfectivo.has(idDenomination)) {
          const data = this.#arrayCountEfectivo.get(idDenomination);
          const nuevoTotal = cantidad * data.value_currency;

          // ? 1. Actualizamos el Map
          this.#arrayCountEfectivo.set(idDenomination, {
            ...data,
            cantidad: cantidad,
            total_amount: nuevoTotal,
          });

          // ? 2. Calculamos totales y diferencias
          this.#sisEfectivoTotal = this.#calcularTotalContado();
          const diferencia = this.#countEfectivoTotal - this.#sisEfectivoTotal; // ? Sistema - Real

          // ? 3. Actualizamos el display del total contado
          this.#quick_access_arqueo_count_efectivo.html(
            this.#convertirASoles(this.#sisEfectivoTotal)
          );

          // ? 4. Definimos el estado de la UI (Tema, Icono, Mensaje)
          let ui = {
            theme: "body",
            icon: "",
            msg: "",
            showMsg: false,
            amount: 0,
          };

          if (this.#sisEfectivoTotal === 0) {
            // ? Estado: Inicio / Vacío
            ui.theme = "body";
            ui.amount = 0;
          } else if (diferencia === 0) {
            // ? Estado: Cuadre Perfecto
            ui = {
              theme: "success",
              icon: "bi-check2-circle",
              msg: "Cuadre perfecto",
              showMsg: true,
              amount: 0,
            };
          } else if (diferencia < 0) {
            // ? Estado: Sobrante (Azul)
            ui = {
              theme: "primary",
              icon: "bi-plus-circle-dotted",
              msg: "Monto sobrante a favor",
              showMsg: true,
              amount: Math.abs(diferencia),
            };
          } else {
            // ? Estado: Faltante (Rojo)
            ui = {
              theme: "danger",
              icon: "bi-exclamation-triangle-fill",
              msg: "Descuadre detectado",
              showMsg: true,
              amount: diferencia,
            };
          }

          // ? 5. Renderizamos Mensaje (Alerta)
          const alertHtml = ui.showMsg
            ? `<div class="alert alert-${ui.theme} d-flex align-items-center gap-2 p-2 rounded-4 mb-0" role="alert">
            <i class="bi ${ui.icon}"></i>
            <strong>${ui.msg}</strong>
          </div>`
            : "";
          this.#quick_access_arqueo_message.html(alertHtml);

          // ? 6. Renderizamos Diferencia (Card)
          // ? Nota: Usamos las variables ui.theme para cambiar los colores dinámicamente
          const diffHtml = `
            <p class="mb-0 fw-bold small text-muted">Diferencia:</p>
            <div class="card rounded-4 border-${ui.theme} bg-${
            ui.theme
          }-subtle">
              <h5 class="mb-0 px-3 py-1 text-${ui.theme} fw-bold">
                ${this.#convertirASoles(ui.amount)}
              </h5>
            </div>
          `;
          this.#quick_access_arqueo_diference.html(diffHtml);

          // ? 7. Mostramos los datos de billetes y monedas
          const resultados = this.#getDesgloseEfectivo();
          let html_desgloce = "";
          Object.entries(resultados).forEach(([tipo, datos]) => {
            html_desgloce += `<div class="text-center w-50 border-end">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">${tipo}</small>
                                        <div class="fw-bold text-dark">${this.#convertirASoles(
                                          datos.total
                                        )}</div>
                                    </div>`;
          });
          this.#quick_access_desgloce_efectivo.html(html_desgloce);
        }
      }
    );
  };

  // TODO: Funcion para limpiar datos
  #clearForm = () => {
    // ? 1. Cargamos por default el mensaje si existe diferencia
    this.#quick_access_arqueo_diference
      .html(`<p class="mb-0 fw-bold small text-muted">Diferencia:</p>
            <div class="card rounded-4 border-body bg-body-subtle">
                <h5 id="" class="mb-0 px-3 py-1 text-body fw-bold">${this.#convertirASoles(
                  0
                )}</h5>
            </div>`);

    // ? 2. Mensaje por default
    this.#quick_access_arqueo_message.html("");

    // ? 3. Efectivo total del sistema
    this.#sisEfectivoTotal = 0;

    // ? 4. Desplazamos el total de display contado
    this.#quick_access_arqueo_count_efectivo.html(
      this.#convertirASoles(this.#sisEfectivoTotal)
    );

    // ? 5. Resetamos el map del dinero
    this.#arrayCountEfectivo.forEach((data) => {
      data.cantidad = 0;
      data.total_amount = 0;
    });

    // ? 6. Limpiamos el desgloce del efectivo
    this.#quick_access_desgloce_efectivo.html("");
  };

  // TODO: Calcular el total de efectivo contado en tiempo real
  #calcularTotalContado = () => {
    let total = 0;
    this.#arrayCountEfectivo.forEach((data) => {
      total += data.total_amount;
    });
    return total;
  };

  // TODO: Renderizacion de resumen de arqueo
  #renderResumenArqueo() {
    const data = this.#arrayMovements;

    // ? Renderizar Totales Simples
    this.#quick_access_arqueo_total_efectivo.html(
      this.#convertirASoles(data.total_payment_method.Efectivo)
    );
    this.#quick_access_arqueo_total_general.html(
      this.#convertirASoles(data.total_general)
    );

    // ? Guardamos el efectivo en un variable
    this.#countEfectivoTotal = data.total_payment_method.Efectivo;

    // ? Renderizar Métodos de Pago (Visa, Yape, etc.)
    const tarjetasHtml = data.payment_method
      .filter(
        (el) =>
          el.name !== "Efectivo" &&
          data.total_payment_method[el.name] !== undefined
      )
      .map(
        (el) => `
      <div class="flex-fill p-2 rounded-4 bg-body-tertiary border text-center">
        <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem;">${
          el.name
        }</small>
        <span class="fw-bold text-dark">${this.#convertirASoles(
          data.total_payment_method[el.name]
        )}</span>
      </div>
    `
      )
      .join("");

    this.#quick_access_arqueo_total_payment_method.html(tarjetasHtml);
  }

  // TODO: Procesar denominaciones de dinero
  #procesarDenominaciones(denominaciones) {
    // ? Configuración de estilos por tipo
    const styleConfig = {
      Billete: {
        icon: "bi-cash",
        text: "text-success",
        bg: "bg-success-subtle",
      },
      Moneda: {
        icon: "bi-coin",
        text: "text-warning",
        bg: "bg-warning-subtle",
      },
      default: { icon: "bi-coin", text: "text-body", bg: "bg-body-subtle" }, // ? Para "Otros"
    };

    // ? Agrupamos los datos por Tipo para renderizar ordenadamente
    const grupos = {};

    denominaciones.forEach((el) => {
      // ? Lógica original: Guardar en el Map de conteo
      this.#arrayCountEfectivo.set(el.idDenomination, {
        type: el.type,
        value_currency: parseFloat(el.value),
        cantidad: 0,
        total_amount: 0,
      });

      // Agrupar
      if (!grupos[el.type]) grupos[el.type] = [];
      grupos[el.type].push(el);
    });

    // Generar HTML
    let htmlFinal = "";

    Object.keys(grupos).forEach((tipo) => {
      const config = styleConfig[tipo] || styleConfig["default"];
      const items = grupos[tipo];

      // Header del grupo
      htmlFinal += `
      <div class="d-flex align-items-center mb-3">
        <h6 class="fw-bold ${config.text} mb-0 me-3" style="min-width: 65px;">
          <i class="bi ${config.icon} me-2"></i>${tipo}
        </h6>
        <div class="flex-grow-1 border-bottom"></div>
      </div>
      <div class="row g-2 mb-4">
    `;

      // Items del grupo
      const itemsHtml = items
        .map(
          (el) => `
      <div class="col-6 item-box">
        <div class="input-group">
          <span class="input-group-text ${config.text} ${
            config.bg
          } fw-bold border-end-0" style="width: 85px;">
            ${this.#convertirASoles(el.value)}
          </span>
          <input 
            id="currency_${el.idDenomination}" 
            data-id="${el.idDenomination}" 
            type="number" 
            class="form-control border-start-0 bg-light" 
            placeholder="0" 
            min="0">
        </div>
      </div>
    `
        )
        .join("");

      htmlFinal += itemsHtml + `</div>`; // Cierre del row
    });

    this.#quick_access_arqueo_currency_denominations.html(htmlFinal);
  }

  // TODO: Obtener totales separados por Billetes y Monedas
  #getDesgloseEfectivo = () => {
    let desglose = {
      billetes: { total: 0, cantidad_fisica: 0 },
      monedas: { total: 0, cantidad_fisica: 0 },
      otros: { total: 0, cantidad_fisica: 0 },
    };

    this.#arrayCountEfectivo.forEach((data) => {
      // ? Sumamos según el tipo guardado
      if (data.type === "Billete") {
        desglose.billetes.total += data.total_amount;
        desglose.billetes.cantidad_fisica += data.cantidad;
      } else if (data.type === "Moneda") {
        desglose.monedas.total += data.total_amount;
        desglose.monedas.cantidad_fisica += data.cantidad;
      } else {
        desglose.otros.total += data.total_amount;
        desglose.otros.cantidad_fisica += data.cantidad;
      }
    });

    return desglose;
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
