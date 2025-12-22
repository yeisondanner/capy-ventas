import { ApiBox } from "./functions_box_api.js";

export class Box {
  // ==========================================
  // 1. ESTADO Y DATOS (State)
  // ==========================================
  #datosSesionCaja = {}; // Datos generales de la sesión
  #mapaConteoEfectivo = new Map(); // Mapa para el conteo físico
  #totalEfectivoSistema = 0; // Calculado: Base + Ventas - Gastos
  #totalEfectivoContado = 0; // Input del usuario

  // ==========================================
  // 2. ELEMENTOS DEL DOM (Selectores)
  // ==========================================
  // Botones Principales
  #btnOpenBox = $("#btnOpenBox");
  // Usamos selector dinámico para cierre por si se renderiza después en el DOM

  // Modales
  #modalAddBox = $("#modalAddBox");
  #modalGestionBox = $("#modalGestionBox");
  #modalArqueoBox = $("#modalArqueoBox");
  #modalCloseBox = $("#modalCloseBox");

  // Vistas Generales
  #divOpenBox = $("#divOpenBox");
  #selectBox = $("#selectBox");
  #inputMontoApertura = $("#cash_opening_amount");

  // Vista: Gestión de Caja
  #lblBaseAmount = $("#quick_access_base_amount");
  #lblTotalGeneral = $("#quick_access_total_general");
  #containerMetodosPago = $("#quick_access_card_payment_method");
  #containerListaMovimientos = $("#quick_access_card_list_movements");
  #lblTituloMovimientos = $("#quick_access_title_list_movements");
  #lblTituloGestionBoxName = $("#gestion_box_name");

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
  #lblTituloArqueoBoxName = $("#arqueo_box_name");

  // Vista: Cierre de Caja (Dashboard Final)
  #lblCloseBoxTotalSales = $("#close_box_total_sales");
  #lblCloseBoxTotalTransactions = $("#close_box_total_transactions");
  #lblCloseBoxTotalPaymentMethod = $("#close_box_total_payment_method");

  #lblCloseBoxBase = $("#close_box_base");
  #lblCloseBoxIncome = $("#close_box_income");
  #lblCloseBoxExpenses = $("#close_box_expenses");
  #lblCloseBoxExpected = $("#close_box_expected");
  #lblTituloCloseBoxName = $("#close_box_name");

  // Balance Sistema vs Arqueo
  #lblCloseBoxSistema = $("#close_box_sistema");
  #lblCloseBoxContado = $("#close_box_contado");
  #lblCloseBoxDifference = $("#close_box_difference");

  #containerCloseBoxStatus = $("#close_box_status_container");

  // Acciones Finales
  #inputCloseBoxNotes = $("#close_box_notes");
  #btnFinalizarCierre = $("#btnFinalizarCierre");

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

  #verificarEstadoCaja = async () => {
    const response = await this.apiBox.get("getuserCheckedBox");
    const htmlBoton = response.status
      ? this.#generarBotonAperturaHtml()
      : this.#generarBotonGestionHtml();

    this.#divOpenBox.html(htmlBoton);
    this.#activarListenersDinamicos();
  };

  #configurarEventosGlobales = () => {
    this.#containerArqueoInputsDinero.on(
      "input",
      "input[type='number']",
      this.#handleInputConteoDinero
    );
  };

  #activarListenersDinamicos = () => {
    // Botones principales
    $("#btnOpenModalBox").on("click", this.#handleClickAbrirModalSeleccion);
    $("#btnOpenModalGestionBox").on(
      "click",
      this.#handleClickAbrirModalGestion
    );

    // Botones dentro de Gestión
    $("#btnOpenModalArqueoBox").on("click", this.#handleClickAbrirModalArqueo);
    $("#btnLimpiarArqueo").on("click", () => this.#limpiarArqueo());

    // Botón para Cierre Definitivo (Delegado)
    $("body").on(
      "click",
      "#btnOpenModalCloseBox",
      this.#handleClickAbrirModalCierre
    );

    // Lógicas de Guardado
    this.#setupFormularioApertura();
    this.#handleClickRegistrarArqueoCaja();
    this.#setupEventoCierreDefinitivo();
  };

  // ==========================================
  // 4. MANEJADORES DE EVENTOS (Handlers)
  // ==========================================

  #handleClickAbrirModalSeleccion = async () => {
    const boxs = await this.#getBoxs();
    if (boxs && boxs.status) {
      this.#renderOpcionesDeCaja(boxs.data);
      this.#modalAddBox.modal("show");
    }
  };

  #handleClickAbrirModalGestion = async () => {
    const response = await this.apiBox.get("getManagementBox");
    if (!response.status) return this.#mostrarAlerta(response);
    this.#datosSesionCaja = response;
    this.#renderVistaGestion();
    this.#modalGestionBox.modal("show");
  };

  #handleClickAbrirModalArqueo = async () => {
    this.#resetearFormularioArqueo();
    this.#renderResumenEsperadoArqueo();

    const response = await this.apiBox.get("getCurrencyDenominations");
    if (!response.status) return this.#mostrarAlerta(response);

    this.#renderInputsDenominaciones(response.data);
    this.#modalArqueoBox.modal("show");
  };

  #handleInputConteoDinero = (e) => {
    const input = $(e.currentTarget);
    const cantidad = parseFloat(input.val()) || 0;
    const idDenomination = input.data("id");

    if (this.#mapaConteoEfectivo.has(idDenomination)) {
      const data = this.#mapaConteoEfectivo.get(idDenomination);
      this.#mapaConteoEfectivo.set(idDenomination, {
        ...data,
        cantidad: cantidad,
        total_amount: cantidad * data.value_currency,
      });

      this.#totalEfectivoContado = this.#calcularTotalUsuario();
      this.#actualizarUIArqueo();
    }
  };

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
        this.#verificarEstadoCaja();
      }
    });
  };

  #handleClickRegistrarArqueoCaja = () => {
    $("#setArqueoCaja").on("click", async () => {
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

      const notes = $("#quick_access_arqueo_justificacion").val() || null;
      const params = {
        conteo_efectivo: detallesArray,
        notes: notes,
        type: "Auditoria",
      };

      const response = await this.apiBox.post("setBoxCashCount", params);

      if (response.status) {
        this.#resetearFormularioArqueo();
        this.#modalArqueoBox.modal("hide");
      }
      return this.#mostrarAlerta(response);
    });
  };

  #handleClickAbrirModalCierre = async () => {
    if (
      !this.#datosSesionCaja ||
      Object.keys(this.#datosSesionCaja).length === 0
    ) {
      const responseMgmt = await this.apiBox.get("getManagementBox");
      if (responseMgmt.status) {
        this.#datosSesionCaja = responseMgmt;
      } else {
        return this.#mostrarAlerta(responseMgmt);
      }
    }
    const data = this.#datosSesionCaja;
    const responseArqueo = await this.apiBox.get("getLastCashCount");

    let montoContado = 0;
    let existeArqueoEnBd = false;

    // Priorizamos datos de BD
    if (responseArqueo && responseArqueo.status && responseArqueo.data) {
      existeArqueoEnBd = true;
      montoContado = parseFloat(responseArqueo.data.counted_amount) || 0;
    } else {
      montoContado = this.#totalEfectivoContado;
    }

    // --- CÁLCULOS MATEMÁTICOS ---
    const totalVentas = parseFloat(data.total_general) || 0;
    const totalTransacciones =
      data.total_transacciones ||
      (data.movements_limit ? data.movements_limit.length : 0);

    const efectivoVentas = parseFloat(data.total_payment_method.Efectivo) || 0;
    const baseInicial = parseFloat(data.amount_base) || 0;

    // Ingresos Efectivo = Ventas Efectivo (La base ya es inicial, no es ingreso por venta)
    const egresosCaja = parseFloat(data.total_efectivo_egreso) || 0;
    const ingresosCaja = efectivoVentas + egresosCaja;

    // Total Sistema = Base + Ingresos - Egresos
    const totalEsperadoSistema = baseInicial + ingresosCaja - egresosCaja;

    const diferencia = montoContado - totalEsperadoSistema;

    // --- RENDERIZADO ---
    this.#lblCloseBoxTotalSales.html(this.#formatoMoneda(totalVentas));
    this.#lblCloseBoxTotalTransactions.html(totalTransacciones);

    // Desglose Métodos de Pago
    let htmlPaymentMethod = "";
    if (data.total_payment_method) {
      Object.entries(data.total_payment_method).forEach(([tipo, monto]) => {
        const colorClass =
          tipo === "Efectivo"
            ? "text-success"
            : tipo === "Yape" || tipo === "Plin"
            ? "text-primary"
            : "text-info";
        htmlPaymentMethod += `
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="${colorClass} fw-bold"><i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>${tipo}</span>
                    <span class="fw-bold">${this.#formatoMoneda(monto)}</span>
                </div>`;
      });
    }
    this.#lblCloseBoxTotalPaymentMethod.html(htmlPaymentMethod);

    // Balance Efectivo
    this.#lblCloseBoxBase.html(this.#formatoMoneda(baseInicial));
    this.#lblCloseBoxIncome.html(`+${this.#formatoMoneda(ingresosCaja)}`);
    this.#lblCloseBoxExpenses.html(`-${this.#formatoMoneda(egresosCaja)}`);
    this.#lblCloseBoxExpected.html(this.#formatoMoneda(totalEsperadoSistema));

    // Comparativa Sistema vs Arqueo
    this.#lblCloseBoxSistema.html(this.#formatoMoneda(totalEsperadoSistema));
    this.#lblCloseBoxContado.html(this.#formatoMoneda(montoContado));

    const signoDiff = diferencia > 0 ? "+" : "";
    this.#lblCloseBoxDifference.html(
      `${signoDiff}${this.#formatoMoneda(diferencia)}`
    );

    if (diferencia < 0)
      this.#lblCloseBoxDifference
        .removeClass("text-success")
        .addClass("text-danger");
    else
      this.#lblCloseBoxDifference
        .removeClass("text-danger")
        .addClass("text-success");

    // Estado del Cuadre
    let htmlStatus = "";
    if (!existeArqueoEnBd && montoContado === 0 && totalEsperadoSistema > 0) {
      htmlStatus = `<div class="alert alert-warning py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-exclamation-circle-fill fs-5"></i><div><strong>Advertencia:</strong> No se ha realizado conteo físico (Arqueo).</div></div>`;
    } else if (Math.abs(diferencia) < 0.1) {
      htmlStatus = `<div class="alert alert-success py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-check-circle-fill fs-5"></i><div><strong>Cuadre Correcto</strong><br>El arqueo coincide con el sistema.</div></div>`;
    } else if (diferencia > 0) {
      htmlStatus = `<div class="alert alert-primary py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-graph-up-arrow fs-5"></i><div><strong>Dinero Sobrante</strong><br>Excedente: +${this.#formatoMoneda(
        diferencia
      )}</div></div>`;
    } else {
      htmlStatus = `<div class="alert alert-danger py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-exclamation-triangle-fill fs-5"></i><div><strong>Descuadre (Faltante)</strong><br>Diferencia: ${this.#formatoMoneda(
        diferencia
      )}</div></div>`;
    }

    this.#containerCloseBoxStatus.html(htmlStatus);
    this.#modalCloseBox.modal("show");
  };

  #setupEventoCierreDefinitivo = async () => {
    this.#btnFinalizarCierre.off("click").on("click", async () => {
      const responseAlert = await Swal.fire({
        title: "¿Cerrar turno?",
        text: "¿Está seguro de finalizar el turno? Esta acción es irreversible.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, continuar",
      });

      if (!responseAlert.isConfirmed) return;

      const notes = this.#inputCloseBoxNotes.val() ?? null;
      const response = await this.apiBox.post("setCloseBoxSession", { notes });

      if (response.status) {
        setTimeout(() => {
          this.#modalCloseBox.modal("hide");
          window.location.reload();
        }, 2000);
      }

      this.#mostrarAlerta(response);
    });
  };

  // ==========================================
  // 5. UTILIDADES DE RENDERIZADO (Helpers)
  // ==========================================

  #renderOpcionesDeCaja = (listaCajas) => {
    let html =
      '<option value="" disabled selected>Seleccione una caja...</option>';
    listaCajas.forEach((box, index) => {
      const num = index + 1;
      let clase =
        box.session === "Activo"
          ? ""
          : box.session === "Abierta"
          ? "text-primary fw-bold"
          : "text-danger";
      let disabled = box.session === "Activo" ? "" : "disabled";
      let extra =
        box.session === "Abierta"
          ? "(En uso)"
          : box.session === "Activo"
          ? ""
          : "(No disponible)";

      html += `<option class="${clase}" ${disabled} value="${box.idBox}">Caja ${num} - ${box.name} ${extra}</option>`;
    });
    this.#selectBox.html(html);
  };

  // CORREGIDO: Lógica de suma de Base solo a Efectivo
  #renderVistaGestion = () => {
    // Mostramos el nombre de la caja en session
    this.#lblTituloGestionBoxName.html(this.#datosSesionCaja.name_box);
    this.#lblTituloArqueoBoxName.html(this.#datosSesionCaja.name_box);
    this.#lblTituloCloseBoxName.html(this.#datosSesionCaja.name_box);

    // Calculamos montos
    let amount_base = parseFloat(this.#datosSesionCaja.amount_base) || 0;

    this.#lblTotalGeneral.html(
      this.#formatoMoneda(this.#datosSesionCaja.total_general + amount_base)
    );
    this.#lblBaseAmount.html(
      `Base: ${this.#formatoMoneda(this.#datosSesionCaja.amount_base)}`
    );

    let htmlMetodos = "";

    this.#datosSesionCaja.payment_method.forEach((el) => {
      if (this.#datosSesionCaja.total_payment_method[el.name] !== undefined) {
        // Obtenemos el total de ventas por ese método
        let totalToShow = parseFloat(
          this.#datosSesionCaja.total_payment_method[el.name]
        );

        // SOLO si es Efectivo, le sumamos la base para mostrar en la tarjeta
        if (el.name === "Efectivo") {
          totalToShow += amount_base;
        }

        htmlMetodos += this.#crearCardMetodoPago(el, totalToShow);
      }
    });
    this.#containerMetodosPago.html(htmlMetodos);

    const moves = this.#datosSesionCaja.movements_limit || [];
    this.#lblTituloMovimientos.html(
      `Últimos <span class="text-primary">${moves.length}</span> Movimientos`
    );
    this.#containerListaMovimientos.html(
      moves.map((mov) => this.#crearItemMovimiento(mov)).join("")
    );
  };

  // CORREGIDO: Lógica de suma de Base para el Arqueo
  #renderResumenEsperadoArqueo() {
    const data = this.#datosSesionCaja;
    console.log(data);

    const ventaEfectivo = parseFloat(data.total_payment_method.Efectivo) || 0;
    const base = parseFloat(data.amount_base) || 0;

    // Total Sistema = Ventas Efectivo + Base
    const totalEfectivoEsperado = ventaEfectivo + base;

    this.#lblArqueoTotalEfectivo.html(
      this.#formatoMoneda(totalEfectivoEsperado)
    );
    this.#lblArqueoTotalGeneral.html(this.#formatoMoneda(data.total_general));

    // IMPORTANTE: Actualizamos la variable global para que el cálculo de diferencia sea correcto
    this.#totalEfectivoSistema = totalEfectivoEsperado;

    // Mostramos el monto inicial
    let htmlTarjetas = `
          <div class="flex-fill p-2 rounded-4 bg-primary-subtle border border-primary text-center">
            <small class="d-block text-primary fw-bold mb-1" style="font-size: 0.7rem;">Monto Inicial</small>
            <span class="fw-bold text-primary">${this.#formatoMoneda(
              base
            )}</span>
          </div>`;

    // Renderizado de Tarjetas Informativas (Yape, Visa, etc)

    htmlTarjetas += data.payment_method
      .filter((el) => data.total_payment_method[el.name] !== undefined)
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

    denominaciones.forEach((el) => {
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

    let htmlFinal = "";
    Object.keys(grupos).forEach((tipo) => {
      const config = styleConfig[tipo] || styleConfig["default"];
      htmlFinal += `
          <div class="d-flex align-items-center mb-3">
            <h6 class="fw-bold ${config.text} mb-0 me-3" style="min-width: 65px;"><i class="bi ${config.icon} me-2"></i>${tipo}</h6>
            <div class="flex-grow-1 border-bottom"></div>
          </div>
          <div class="row g-2 mb-4">`;

      htmlFinal += grupos[tipo]
        .map((el) => {
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
              <input id="currency_${el.idDenomination}" data-id="${
            el.idDenomination
          }" type="number" class="form-control border-start-0 bg-light" placeholder="0" min="0" value="${val}">
            </div>
          </div>`;
        })
        .join("");
      htmlFinal += `</div>`;
    });

    this.#containerArqueoInputsDinero.html(htmlFinal);
    if (this.#totalEfectivoContado > 0) this.#actualizarUIArqueo();
  }

  #actualizarUIArqueo() {
    this.#lblArqueoTotalContado.html(
      this.#formatoMoneda(this.#totalEfectivoContado)
    );
    const diferencia = this.#totalEfectivoSistema - this.#totalEfectivoContado;
    let uiState = this.#determinarEstadoArqueo(
      this.#totalEfectivoContado,
      diferencia
    );

    const alertHtml = uiState.showMsg
      ? `<div class="alert alert-${uiState.theme} d-flex align-items-center gap-2 p-2 rounded-4 mb-0" role="alert"><i class="bi ${uiState.icon}"></i><strong>${uiState.msg}</strong></div>`
      : "";
    this.#containerArqueoMensaje.html(alertHtml);

    const diffHtml = `<p class="mb-0 fw-bold small text-muted">Diferencia:</p><div class="card rounded-4 border-${
      uiState.theme
    } bg-${uiState.theme}-subtle"><h5 class="mb-0 px-3 py-1 text-${
      uiState.theme
    } fw-bold">${this.#formatoMoneda(uiState.amount)}</h5></div>`;
    this.#containerArqueoDiferencia.html(diffHtml);

    const desglose = this.#calcularDesglosePorTipo();
    let htmlDesglose = "";
    Object.entries(desglose).forEach(([tipo, datos]) => {
      htmlDesglose += `<div class="text-center w-50 border-end"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">${tipo}</small><div class="fw-bold text-dark">${this.#formatoMoneda(
        datos.total
      )}</div></div>`;
    });
    this.#containerDesgloseFinal.html(htmlDesglose);
  }

  // ==========================================
  // 6. FUNCIONES AUXILIARES
  // ==========================================

  #resetearFormularioArqueo = () => {
    this.#totalEfectivoContado = 0;
    this.#mapaConteoEfectivo.forEach((d) => {
      d.cantidad = 0;
      d.total_amount = 0;
    });
    this.#actualizarUIArqueo();
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

  // ==========================================
  // 7. HTML HELPERS
  // ==========================================

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

  // CORREGIDO: Ya no recibe 'base' porque la lógica se hace fuera
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

    return `
    <div class="list-group-item px-3 py-3 border-bottom-0">
        <div class="d-flex align-items-center gap-3">
            <div class="${
              config.bg
            } rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
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
