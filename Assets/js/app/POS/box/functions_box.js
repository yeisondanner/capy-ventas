import { ApiBox } from "./functions_box_api.js";

export class Box {
  // ==========================================
  // 1. ESTADO Y DATOS (State)
  // ==========================================
  #datosSesionCaja = []; // Antes: #arrayMovements
  #mapaConteoEfectivo = new Map(); // Antes: #arrayCountEfectivo
  #totalEfectivoSistema = 0; // Lo que dice el sistema que debe haber
  #totalEfectivoContado = 0; // Lo que el usuario contó en el Arqueo

  // ==========================================
  // 2. ELEMENTOS DEL DOM (Selectores)
  // ==========================================
  // Botones
  #btnOpenBox = $("#btnOpenBox");

  // Modales
  #modalAddBox = $("#modalAddBox");
  #modalGestionBox = $("#modalGestionBox");
  #modalArqueoBox = $("#modalArqueoBox");
  #modalCloseBox = $("#modalCloseBox");

  // Contenedores y Vistas Principales
  #divOpenBox = $("#divOpenBox");
  #selectBox = $("#selectBox");
  #inputMontoApertura = $("#cash_opening_amount");

  // Vista: Gestión de Caja
  #lblBaseAmount = $("#quick_access_base_amount");
  #lblTotalGeneral = $("#quick_access_total_general");
  #containerMetodosPago = $("#quick_access_card_payment_method");
  #containerListaMovimientos = $("#quick_access_card_list_movements");
  #lblTituloMovimientos = $("#quick_access_title_list_movements");

  // Vista: Arqueo (Conteo Físico)
  #lblArqueoTotalEfectivo = $("#quick_access_arqueo_total_efectivo");
  #lblArqueoTotalGeneral = $("#quick_access_arqueo_total_general");
  #containerArqueoTarjetas = $("#quick_access_arqueo_total_payment_method");
  #containerArqueoInputsDinero = $(
    "#quick_access_arqueo_currency_denominations"
  );
  #lblArqueoTotalContado = $("#quick_access_arqueo_count_efectivo");
  #containerArqueoMensaje = $("#quick_access_arqueo_message");
  #containerArqueoDiferencia = $("#quick_access_arqueo_diference");
  #containerDesgloseFinal = $("#quick_access_desgloce_efectivo");

  // Vista: Cierre de Caja (Dashboard Final)
  #lblCloseBoxTotalSales = $("#close_box_total_sales");
  #lblCloseBoxTotalTransactions = $("#close_box_total_transactions");
  #lblCloseBoxTotalPaymentMethod = $("#close_box_total_payment_method");

  #lblCloseBoxBase = $("#close_box_base");
  #lblCloseBoxIncome = $("#close_box_income");
  #lblCloseBoxExpenses = $("#close_box_expenses");
  #lblCloseBoxExpected = $("#close_box_expected");

  #containerCloseBoxStatus = $("#close_box_status_container");
  #containerCloseBoxPending = $("#close_box_pending_list");
  #containerCloseBoxAlerts = $("#close_box_alerts_list");

  #inputCloseBoxNotes = $("#close_box_notes");
  #btnFinalizarCierre = $("#btnFinalizarCierre");
  #btnOpenModalCloseBox = $("#btnOpenModalCloseBox"); // Botón que abre el modal final

  constructor(base_url) {
    this.apiBox = new ApiBox(base_url);

    // Inicialización
    this.#verificarEstadoCaja();
    this.#iniciarReloj();
    this.#configurarEventosGlobales();
  }

  // ==========================================
  // 3. INICIALIZACIÓN Y CONFIGURACIÓN
  // ==========================================

  // Verifica si hay caja abierta al cargar la página
  #verificarEstadoCaja = async () => {
    const response = await this.apiBox.get("getuserCheckedBox");

    // Renderiza el botón principal según el estado
    const htmlBoton = response.status
      ? this.#generarBotonAperturaHtml()
      : this.#generarBotonGestionHtml();

    this.#divOpenBox.html(htmlBoton);

    // Una vez renderizado el botón, activamos sus listeners específicos
    this.#activarListenersDinamicos();
  };

  #configurarEventosGlobales = () => {
    // Evento para el conteo de dinero en tiempo real (Delegación de eventos)
    this.#containerArqueoInputsDinero.on(
      "input",
      "input[type='number']",
      this.#handleInputConteoDinero
    );
  };

  #activarListenersDinamicos = () => {
    // Evento: Clic en "Abrir Caja"
    $("#btnOpenModalBox").on("click", this.#handleClickAbrirModalSeleccion);

    // Evento: Clic en "Gestión de Caja"
    $("#btnOpenModalGestionBox").on(
      "click",
      this.#handleClickAbrirModalGestion
    );

    // Evento: Clic en "Arqueo de Caja" (Dentro de Gestión)
    $("#btnOpenModalArqueoBox").on("click", this.#handleClickAbrirModalArqueo);

    // Evento: Botón Limpiar dentro del Arqueo
    $("#btnLimpiarArqueo").on("click", () => {
      this.#limpiarArqueo();
    });

    // Configurar lógica del formulario de apertura
    this.#setupFormularioApertura();

    // Lógica para guardar arqueo de caja
    this.#handleClickRegistrarArqueoCaja();

    // Evento: Clic en "Cerrar Caja" (Abre el Dashboard Final)
    // Asegúrate de que este ID exista en tu botón de Gestión de Caja
    this.#btnOpenModalCloseBox.on("click", this.#handleClickAbrirModalCierre);

    // Configurar el botón rojo "Finalizar Cierre"
    this.#setupEventoCierreDefinitivo();
  };

  // ==========================================
  // 4. MANEJADORES DE EVENTOS (Handlers)
  // ==========================================

  // Abre el modal para seleccionar caja e iniciar turno
  #handleClickAbrirModalSeleccion = async () => {
    const boxs = await this.#getBoxs();
    if (boxs && boxs.status) {
      this.#renderOpcionesDeCaja(boxs.data);
      this.#modalAddBox.modal("show");
    }
  };

  // Abre el modal principal con los movimientos y totales
  #handleClickAbrirModalGestion = async () => {
    const response = await this.apiBox.get("getManagementBox");
    if (!response.status) {
      return this.#mostrarAlerta(response);
    }

    // Guardamos los datos de la sesión
    this.#datosSesionCaja = {
      amount_base: response.amount_base,
      total_general: response.total_general,
      movements_limit: response.movements_limit,
      payment_method: response.payment_method,
      total_payment_method: response.total_payment_method,
      total_transacciones: response.total_transacciones,
      total_efectivo_egreso: response.total_efectivo_egreso,
    };

    this.#renderVistaGestion();
    this.#modalGestionBox.modal("show");
  };

  // Abre el modal de conteo de dinero (Arqueo)
  #handleClickAbrirModalArqueo = async () => {
    // 1. Renderizar resumen (Totales esperados)
    this.#renderResumenEsperadoArqueo();

    // 2. Obtener billetes y monedas disponibles
    const response = await this.apiBox.get("getCurrencyDenominations");
    if (!response.status) return this.#mostrarAlerta(response);

    // 3. Inicializar el Map y renderizar inputs
    this.#renderInputsDenominaciones(response.data);

    // 4. Limpiar formulario para empezar de cero (Opcional: Si quieres guardar estado, quita esto)
    // this.#resetearFormularioArqueo();

    // 5. Mostrar
    this.#modalArqueoBox.modal("show");
  };

  // Lógica principal: Cuando el usuario escribe una cantidad en los inputs
  #handleInputConteoDinero = (e) => {
    const input = $(e.currentTarget);
    const cantidad = parseFloat(input.val()) || 0;
    const idDenomination = input.data("id");

    if (this.#mapaConteoEfectivo.has(idDenomination)) {
      const data = this.#mapaConteoEfectivo.get(idDenomination);

      // 1. Actualizar Map
      this.#mapaConteoEfectivo.set(idDenomination, {
        ...data,
        cantidad: cantidad,
        total_amount: cantidad * data.value_currency,
      });

      // 2. Calcular Totales
      this.#totalEfectivoContado = this.#calcularTotalUsuario();

      // 3. Actualizar UI (Totales, Diferencias, Alertas)
      this.#actualizarUIArqueo();
    }
  };

  // Configura el botón de "Guardar Apertura"
  #setupFormularioApertura = () => {
    this.#btnOpenBox.off("click").on("click", async () => {
      const boxId = this.#selectBox.val();
      const monto = this.#inputMontoApertura.val();

      if (!boxId)
        return this.#mostrarAlerta({
          icon: "warning",
          title: "Validación",
          message: "Seleccione una caja.",
        });
      if (!monto || monto < 0)
        return this.#mostrarAlerta({
          icon: "warning",
          title: "Validación",
          message: "Monto inválido.",
        });

      const response = await this.apiBox.post("setOpenBox", {
        box_id: boxId,
        cash_opening_amount: monto,
      });

      this.#mostrarAlerta(response);
      if (response.status) {
        this.#modalAddBox.modal("hide");
        this.#inputMontoApertura.val(0);
        this.#verificarEstadoCaja(); // Reiniciar vista
      }
    });
  };

  // Registrar un arqueo de caja
  #handleClickRegistrarArqueoCaja = () => {
    $("#setArqueoCaja").on("click", async () => {
      // Validamos que haya almenos un efectivo insertado (Opcional: permitir arqueo en 0)
      // if (this.#totalEfectivoContado === 0) {
      //   return showAlert({ title: "Validación", icon: "warning", message: "Por favor seleccione almenos un efectivo." });
      // }

      // Convertimos el Map a un Array legible para el enpoint
      const detallesArray = [];
      this.#mapaConteoEfectivo.forEach((data, key) => {
        if (data.cantidad > 0) {
          detallesArray.push({
            denomination_id: key,
            cantidad: data.cantidad,
            total: data.total_amount,
          });
        }
      });

      // validamos si existe alguna justificacion
      const notes = $("#quick_access_arqueo_justificacion").val() || null;

      const params = {
        conteo_efectivo: detallesArray,
        notes: notes,
        type: "Auditoria",
      };

      const response = await this.apiBox.post("setBoxCashCount", params);

      if (response.status) {
        // Limpiamos el formulario visualmente
        this.#resetearFormularioArqueo();
        this.#modalArqueoBox.modal("hide");
      }

      return this.#mostrarAlerta({
        title: response.title,
        icon: response.icon,
        message: response.message,
      });
    });
  };

  // TODO: Abrir Modal de Cierre Definitivo (Dashboard)
  #handleClickAbrirModalCierre = async () => {

    // consultamos el ultimo arqueo de caja realizado
    const response = await this.apiBox.get("");


    const data = this.#datosSesionCaja;

    // --- 1. CÁLCULOS MATEMÁTICOS ---
    const totalVentas = parseFloat(data.total_general) || 0;
    const totalTransacciones = data.total_transacciones;

    // Desglose Ventas
    const efectivoVentas = parseFloat(data.total_payment_method.Efectivo) || 0;
    // Sumar todos los digitales
    const digitalVentas = totalVentas - efectivoVentas;

    // Balance Efectivo
    const baseInicial = parseFloat(data.amount_base) || 0;
    // Ingresos a Caja (Base + Ventas Efectivo) - Egresos
    // *Si tienes retiros de caja, réstalos aquí*
    const ingresosCaja = efectivoVentas;
    const egresosCaja = parseFloat(data.total_efectivo_egreso) || 0 ;
    const totalEsperadoSistema = baseInicial + ingresosCaja - egresosCaja;

    // Comparación con el ÚLTIMO ARQUEO REALIZADO
    const ultimoArqueoContado = this.#totalEfectivoContado;
    const diferencia = ultimoArqueoContado - totalEsperadoSistema;

    // --- 2. RENDERIZADO EN EL DOM ---

    // A. Resumen Turno
    this.#lblCloseBoxTotalSales.html(this.#formatoMoneda(totalVentas));
    this.#lblCloseBoxTotalTransactions.html(totalTransacciones);

    let htmlPaymentMethod = "";
    Object.entries(data.total_payment_method).forEach(([tipo, datos]) => {
      htmlPaymentMethod += `<div class="d-flex justify-content-between align-items-center small">
                                        <span class="${
                                          tipo === "Efectivo"
                                            ? "text-success"
                                            : "text-muted"
                                        } fw-bold"><i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>${tipo}</span>
                                        <span class="fw-bold" id="close_box_breakdown_cash">${this.#formatoMoneda(
                                          datos
                                        )}</span>
                                    </div>`;
    });
    this.#lblCloseBoxTotalPaymentMethod.html(htmlPaymentMethod);

    // B. Balance Efectivo
    this.#lblCloseBoxBase.html(this.#formatoMoneda(baseInicial));
    this.#lblCloseBoxIncome.html(`+${this.#formatoMoneda(ingresosCaja)}`);
    this.#lblCloseBoxExpenses.html(`-${this.#formatoMoneda(egresosCaja)}`);
    this.#lblCloseBoxExpected.html(this.#formatoMoneda(totalEsperadoSistema));

    // C. Estado del Cuadre (Visual)
    let htmlStatus = "";

    // Caso: No hizo arqueo
    if (this.#totalEfectivoContado === 0 && totalEsperadoSistema > 0) {
      htmlStatus = `
        <div class="alert alert-warning py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <div><strong>Advertencia:</strong> No se ha realizado conteo físico (Arqueo) o es cero.</div>
        </div>`;
    }
    // Caso: Cuadre Perfecto (Margen < 0.10)
    else if (Math.abs(diferencia) < 0.1) {
      htmlStatus = `
        <div class="alert alert-success py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><strong>Cuadre Correcto</strong><br>El arqueo coincide con el sistema.</div>
        </div>`;
    }
    // Caso: Descuadre
    else {
      const color = diferencia > 0 ? "primary" : "danger";
      const texto = diferencia > 0 ? "Sobrante" : "Faltante";
      const signo = diferencia > 0 ? "+" : "";

      htmlStatus = `
        <div class="alert alert-${color} py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>Descuadre (${texto})</strong><br>
                Diferencia: ${signo}${this.#formatoMoneda(diferencia)}
            </div>
        </div>`;
    }

    this.#containerCloseBoxStatus.html(htmlStatus);

    // 3. Mostrar Modal
    this.#modalCloseBox.modal("show");
  };

  // Evento para el botón rojo de Finalizar
  #setupEventoCierreDefinitivo = () => {
    this.#btnFinalizarCierre.on("click", async () => {
      // Confirmación simple del navegador (Opcional)
      if (
        !confirm(
          "¿Está seguro de finalizar el turno? Esta acción es irreversible."
        )
      )
        return;

      const notes = this.#inputCloseBoxNotes.val();

      // Aquí llamas a tu API de cierre definitivo
      // Asegúrate que tu endpoint "closeSession" reciba 'idBoxSession' y 'notes'
      const params = {
        idBoxSession: this.#selectBox.val() || 1, // Obtener ID real de la sesión activa
        notes: notes,
      };

      const response = await this.apiBox.post("closeSession", params);

      if (response.status) {
        this.#modalCloseBox.modal("hide");
        window.location.reload(); // Recargar para volver al login o estado cerrado
      } else {
        this.#mostrarAlerta(response);
      }
    });
  };

  // ==========================================
  // 5. LÓGICA DE RENDERIZADO (UI Helpers)
  // ==========================================

  #renderOpcionesDeCaja = (listaCajas) => {
    let html =
      '<option value="" disabled selected>Seleccione una caja...</option>';
    listaCajas.forEach((box, index) => {
      const num = index + 1;
      let clase = "";
      let textoExtra = "";
      let disabled = "disabled";

      if (box.session === "Activo") {
        clase = "";
        disabled = "";
      } else if (box.session === "Inactivo") {
        clase = "text-danger fw-bold";
        textoExtra = "(Deshabilitado)";
      } else if (box.session === "Abierta") {
        clase = "text-primary fw-bold";
        textoExtra = "(En uso)";
      } else {
        clase = "text-warning fw-bold";
        textoExtra = "(En Arqueo)";
      }

      html += `<option class="${clase}" ${disabled} value="${box.idBox}">Caja ${num} - ${box.name} ${textoExtra}</option>`;
    });
    this.#selectBox.html(html);
  };

  #renderVistaGestion = () => {
    // Totales Cabecera
    this.#lblTotalGeneral.html(
      this.#formatoMoneda(this.#datosSesionCaja.total_general)
    );
    this.#lblBaseAmount.html(
      `Base: ${this.#formatoMoneda(this.#datosSesionCaja.amount_base)}`
    );

    // Tarjetas Superiores (Metodos de pago)
    let htmlMetodos = "";
    this.#datosSesionCaja.payment_method.forEach((el) => {
      if (this.#datosSesionCaja.total_payment_method[el.name] !== undefined) {
        htmlMetodos += this.#crearCardMetodoPago(
          el,
          this.#datosSesionCaja.total_payment_method[el.name]
        );
      }
    });
    this.#containerMetodosPago.html(htmlMetodos);

    // Lista de Movimientos
    this.#lblTituloMovimientos.html(
      `Últimos <span class="text-primary">${
        this.#datosSesionCaja.movements_limit.length
      }</span> Movimientos`
    );

    let htmlMovimientos = this.#datosSesionCaja.movements_limit
      .map((mov) => this.#crearItemMovimiento(mov))
      .join("");

    this.#containerListaMovimientos.html(htmlMovimientos);
  };

  #renderResumenEsperadoArqueo() {
    const data = this.#datosSesionCaja;

    // Mostrar Totales del Sistema
    this.#lblArqueoTotalEfectivo.html(
      this.#formatoMoneda(data.total_payment_method.Efectivo)
    );
    this.#lblArqueoTotalGeneral.html(this.#formatoMoneda(data.total_general));

    // Guardar referencia del total esperado en efectivo
    this.#totalEfectivoSistema =
      parseFloat(data.total_payment_method.Efectivo) || 0;

    // Renderizar tarjetas (Visa, Yape, etc.) para referencia visual
    const htmlTarjetas = data.payment_method
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
            <span class="fw-bold text-dark">${this.#formatoMoneda(
              data.total_payment_method[el.name]
            )}</span>
          </div>`
      )
      .join("");

    this.#containerArqueoTarjetas.html(htmlTarjetas);
  }

  #renderInputsDenominaciones(denominaciones) {
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
      default: { icon: "bi-coin", text: "text-body", bg: "bg-body-subtle" },
    };

    const grupos = {};

    // 1. Inicializar Mapa y Agrupar
    denominaciones.forEach((el) => {
      // Si ya existe en el mapa (porque el usuario ya contó), respetamos su valor. Si no, 0.
      const valorPrevio = this.#mapaConteoEfectivo.get(el.idDenomination);

      this.#mapaConteoEfectivo.set(el.idDenomination, {
        type: el.type,
        value_currency: parseFloat(el.value),
        cantidad: valorPrevio ? valorPrevio.cantidad : 0,
        total_amount: valorPrevio ? valorPrevio.total_amount : 0,
      });

      if (!grupos[el.type]) grupos[el.type] = [];
      grupos[el.type].push(el);
    });

    // 2. Generar HTML
    let htmlFinal = "";
    Object.keys(grupos).forEach((tipo) => {
      const config = styleConfig[tipo] || styleConfig["default"];

      // Header
      htmlFinal += `
          <div class="d-flex align-items-center mb-3">
            <h6 class="fw-bold ${config.text} mb-0 me-3" style="min-width: 65px;"><i class="bi ${config.icon} me-2"></i>${tipo}</h6>
            <div class="flex-grow-1 border-bottom"></div>
          </div>
          <div class="row g-2 mb-4">`;

      // Inputs
      htmlFinal += grupos[tipo]
        .map((el) => {
          // Recuperamos valor previo para ponerlo en el input si se vuelve a abrir el modal
          const dataMap = this.#mapaConteoEfectivo.get(el.idDenomination);
          const val = dataMap.cantidad > 0 ? dataMap.cantidad : "";

          return `
          <div class="col-6 item-box">
            <div class="input-group">
              <span class="input-group-text ${config.text} ${
            config.bg
          } fw-bold border-end-0" style="width: 85px;">
                ${this.#formatoMoneda(el.value)}
              </span>
              <input id="currency_${el.idDenomination}" 
                     data-id="${el.idDenomination}" 
                     type="number" 
                     class="form-control border-start-0 bg-light" 
                     placeholder="0" 
                     min="0"
                     value="${val}">
            </div>
          </div>`;
        })
        .join("");

      htmlFinal += `</div>`;
    });

    this.#containerArqueoInputsDinero.html(htmlFinal);

    // Si había datos previos, actualizamos los totales visuales
    if (this.#totalEfectivoContado > 0) this.#actualizarUIArqueo();
  }

  // Actualiza toda la parte derecha del modal de arqueo (Alertas, Totales, Desglose)
  #actualizarUIArqueo() {
    // A. Mostrar Total Contado
    this.#lblArqueoTotalContado.html(
      this.#formatoMoneda(this.#totalEfectivoContado)
    );

    // B. Calcular Estado (Sobra/Falta)
    const diferencia = this.#totalEfectivoSistema - this.#totalEfectivoContado;
    let uiState = this.#determinarEstadoArqueo(
      this.#totalEfectivoContado,
      diferencia
    );

    // C. Renderizar Alerta
    const alertHtml = uiState.showMsg
      ? `<div class="alert alert-${uiState.theme} d-flex align-items-center gap-2 p-2 rounded-4 mb-0" role="alert">
            <i class="bi ${uiState.icon}"></i><strong>${uiState.msg}</strong>
           </div>`
      : "";
    this.#containerArqueoMensaje.html(alertHtml);

    // D. Renderizar Diferencia
    const diffHtml = `
        <p class="mb-0 fw-bold small text-muted">Diferencia:</p>
        <div class="card rounded-4 border-${uiState.theme} bg-${
      uiState.theme
    }-subtle">
            <h5 class="mb-0 px-3 py-1 text-${
              uiState.theme
            } fw-bold">${this.#formatoMoneda(uiState.amount)}</h5>
        </div>`;
    this.#containerArqueoDiferencia.html(diffHtml);

    // E. Renderizar Desglose (Billetes vs Monedas)
    const desglose = this.#calcularDesglosePorTipo();
    let htmlDesglose = "";
    Object.entries(desglose).forEach(([tipo, datos]) => {
      htmlDesglose += `
            <div class="text-center w-50 border-end">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">${tipo}</small>
                <div class="fw-bold text-dark">${this.#formatoMoneda(
                  datos.total
                )}</div>
            </div>`;
    });
    this.#containerDesgloseFinal.html(htmlDesglose);
  }

  // ==========================================
  // 6. UTILIDADES Y CALCULOS (Helpers)
  // ==========================================

  #resetearFormularioArqueo = () => {
    // Resetea valores visuales y de lógica
    this.#totalEfectivoContado = 0;
    this.#mapaConteoEfectivo.forEach((d) => {
      d.cantidad = 0;
      d.total_amount = 0;
    });
    this.#actualizarUIArqueo(); // Esto limpiará alertas y textos automáticamente
  };

  #limpiarArqueo = () => {
    this.#resetearFormularioArqueo();
    this.#containerArqueoInputsDinero.find("input[type='number']").val("");
  };

  #calcularTotalUsuario = () => {
    let total = 0;
    this.#mapaConteoEfectivo.forEach((data) => (total += data.total_amount));
    return total;
  };

  #determinarEstadoArqueo(totalContado, diferencia) {
    if (totalContado === 0) return { theme: "body", amount: 0, showMsg: false };
    if (diferencia === 0)
      return {
        theme: "success",
        icon: "bi-check2-circle",
        msg: "Cuadre perfecto",
        showMsg: true,
        amount: 0,
      };
    if (diferencia < 0)
      return {
        theme: "primary",
        icon: "bi-plus-circle-dotted",
        msg: "Monto sobrante a favor",
        showMsg: true,
        amount: Math.abs(diferencia),
      };
    return {
      theme: "danger",
      icon: "bi-exclamation-triangle-fill",
      msg: "Descuadre detectado",
      showMsg: true,
      amount: diferencia,
    };
  }

  #calcularDesglosePorTipo = () => {
    let desglose = {
      billetes: { total: 0 },
      monedas: { total: 0 },
      otros: { total: 0 },
    };
    this.#mapaConteoEfectivo.forEach((data) => {
      const key =
        data.type === "Billete"
          ? "billetes"
          : data.type === "Moneda"
          ? "monedas"
          : "otros";
      desglose[key].total += data.total_amount;
    });
    return desglose;
  };

  #formatoMoneda = (valor) => {
    return new Intl.NumberFormat("es-PE", {
      style: "currency",
      currency: "PEN",
      minimumFractionDigits: 2,
    }).format(Number(valor));
  };

  #timeAgoModerno = (dateString) => {
    const diff = (new Date(dateString) - new Date()) / 1000;
    const rtf = new Intl.RelativeTimeFormat("es", { numeric: "auto" });
    if (Math.abs(diff) < 60) return rtf.format(Math.round(diff), "second");
    if (Math.abs(diff) < 3600)
      return rtf.format(Math.round(diff / 60), "minute");
    if (Math.abs(diff) < 86400)
      return rtf.format(Math.round(diff / 3600), "hour");
    return rtf.format(Math.round(diff / 86400), "day");
  };

  #iniciarReloj = () => {
    const tick = () => {
      const t = new Date().toLocaleTimeString("es-PE", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
      });
      // Validamos existencia antes de asignar (Evita el error null)
      const r1 = document.getElementById("reloj");
      if (r1) r1.innerText = t;

      const r2 = document.getElementById("reloj_2");
      if (r2) r2.innerText = t;

      const r3 = document.getElementById("reloj_3");
      if (r3) r3.innerText = t;
    };
    tick();
    setInterval(tick, 1000);
  };

  #getBoxs = async () => {
    const r = await this.apiBox.get("getBoxs");
    if (!r.status) this.#mostrarAlerta(r);
    return r;
  };

  #mostrarAlerta = ({ icon, title, message }) => {
    if (typeof showAlert === "function") showAlert({ icon, title, message });
    else console.warn("showAlert no está definido", message);
  };

  // HTML Helpers
  #generarBotonAperturaHtml = () => `
    <div class="d-flex justify-content-center align-items-center">
        <button id="btnOpenModalBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
            <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
            <span class="fw-semibold">Abrir Caja</span>
        </button>
    </div>`;

  #generarBotonGestionHtml = () => `
    <div class="d-flex justify-content-center align-items-center">
        <button id="btnOpenModalGestionBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
            <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
            <span class="fw-semibold">Gestión de Caja</span>
        </button>
    </div>`;

  #crearCardMetodoPago = (el, total) => `
    <div class="col-4">
        <div class="card border rounded-4 h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="d-inline-flex align-items-center justify-content-center border bg-white rounded-circle mb-2" style="width: 35px; height: 35px;">${
                  el.icon
                }</span>
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">${
                  el.name
                }</div>
                <h6 class="fw-bold mb-0 text-dark">${this.#formatoMoneda(
                  total
                )}</h6>
            </div>
        </div>
    </div>`;

  #crearItemMovimiento = (element) => {
    let config = {
      icon: "bi-arrow-up-right",
      color: "danger",
      sign: "-",
      bg: "bg-danger-subtle",
    };
    if (element.type_movement === "Inicio")
      config = {
        icon: "bi-key-fill",
        color: "success",
        sign: "+",
        bg: "bg-info-subtle text-info",
      };
    else if (element.type_movement === "Ingreso")
      config = {
        icon: "bi-cart-fill",
        color: "success",
        sign: "+",
        bg: "bg-success-subtle text-success",
      };

    const iconClass =
      element.type_movement === "Inicio"
        ? "bg-info-subtle text-info"
        : element.type_movement === "Ingreso"
        ? "bg-success-subtle text-success"
        : "bg-danger-subtle text-danger";

    return `
    <div class="list-group-item px-3 py-3 border-bottom-0">
        <div class="d-flex align-items-center gap-3">
            <div class="${iconClass} rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi ${config.icon}"></i>
            </div>
            <div class="flex-fill lh-1">
                <h6 class="mb-1 text-dark fw-bold">${element.concept}</h6>
                <small class="text-muted">${this.#timeAgoModerno(
                  element.movement_date
                )}</small>
            </div>
            <div class="text-end lh-1">
                <span class="d-block text-${config.color} fw-bold">${
      config.sign
    }${this.#formatoMoneda(element.amount)}</span>
                <small class="text-muted" style="font-size: 0.75rem;">${
                  element.payment_method
                }</small>
            </div>
        </div>
    </div>`;
  };
}

new Box(base_url);
