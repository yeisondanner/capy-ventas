import { ApiBox } from "./functions_box_api.js";

export class Box {
  // ==========================================
  // 1. ESTADO Y DATOS (State)
  // ==========================================
  #datosSesionCaja = {};
  #mapaConteoEfectivo = new Map();
  #totalEfectivoSistema = 0;
  #totalEfectivoContado = 0;
  #chartInstance = null;
  #canvasGraphic = $("#graphic_sales_hour");
  #statusRegisterHeader = null;
  #statusExpenseHeader = null;
  #valueTax = null;

  // ==========================================
  // 2. ELEMENTOS DEL DOM (Selectores)
  // ==========================================
  #btnOpenBox = $("#btnOpenBox");

  // Modales
  #modalAddBox = $("#modalAddBox");
  #modalGestionBox = $("#modalGestionBox");
  #modalArqueoBox = $("#modalArqueoBox");
  #modalCloseBox = $("#modalCloseBox");
  #modalMovementBox = $("#modalMovementBox"); // NUEVO: Modal de Movimientos
  #modalRetireMovementBox = $("#modalRetireMovementBox"); // NUEVO: Retirar movimientos Movimientos

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

  // Vista: Arqueo
  #lblArqueoTotalEfectivo = $("#quick_access_arqueo_total_efectivo");
  #lblArqueoTotalGeneral = $("#quick_access_arqueo_total_general");
  #containerArqueoTarjetas = $("#quick_access_arqueo_total_payment_method");
  #containerArqueoInputsDinero = $(
    "#quick_access_arqueo_currency_denominations",
  );
  #lblArqueoTotalContado = $("#quick_access_arqueo_count_efectivo");
  #containerArqueoMensaje = $("#quick_access_arqueo_message");
  #containerArqueoDiferencia = $("#quick_access_arqueo_diference");
  #containerDesgloseFinal = $("#quick_access_desgloce_efectivo");
  #lblTituloArqueoBoxName = $("#arqueo_box_name");

  // Vista: Cierre
  #lblCloseBoxTotalSales = $("#close_box_total_sales");
  #lblCloseBoxTotalTransactions = $("#close_box_total_transactions");
  #lblCloseBoxTotalPaymentMethod = $("#close_box_total_payment_method");
  #lblCloseBoxBase = $("#close_box_base");
  #lblCloseBoxIncome = $("#close_box_income");
  #lblCloseBoxExpenses = $("#close_box_expenses");
  #lblCloseBoxExpected = $("#close_box_expected");
  #lblTituloCloseBoxName = $("#close_box_name");
  #lblCloseBoxSistema = $("#close_box_sistema");
  #lblCloseBoxContado = $("#close_box_contado");
  #lblCloseBoxDifference = $("#close_box_difference");
  #containerCloseBoxStatus = $("#close_box_status_container");

  // Vista: Movimientos (Ingreso/Retiro) -> NUEVO
  #btnTypeIngreso = $("#btnTypeIngreso");
  #btnTypeRetiro = $("#btnTypeRetiro");
  #inputMovementType = $("#movement_type");
  #inputMovementAmount = $("#movement_amount");
  #inputRetireAmount = $("#retire_amount");
  #inputRetireDate = $("#retire_date");
  #inputMovementDescription = $("#movement_description");
  #inputRetireDescription = $("#retire_description");
  #inputRetireName = $("#retire_name");
  #btnSaveMovement = $("#btnSaveMovement");
  #btnSaveRetireCash = $("#btnSaveRetireCash");
  #iconMovementWrapper = $("#movement_icon_wrapper");
  #selectMovementCustomer = $("#movement_customer");
  #selectRetireSupplier = $("#retire_supplier");
  #selectRetireExpenseCategory = $("#retire_expense_category");
  #selectMovementPaymentMethod = $("#movement_payment_method");
  #selectRetirePaymentMethod = $("#retire_payment_method");
  #labelTaxName = $("#label_tax_name");
  #spanTax = $("#span_tax");
  #inputTotal = $("#movement_total");
  #inputCheckTax = $("#movement_check_tax");
  #inputTax = $("#movement_tax");

  // Acciones Finales
  #inputCloseBoxNotes = $("#close_box_notes");
  #btnFinalizarCierre = $("#btnFinalizarCierre");

  constructor(base_url) {
    this.apiBox = new ApiBox(base_url);
    this.#verificarEstadoCaja();
    this.#iniciarReloj();
    this.#configurarEventosEstaticos();
  }

  // ==========================================
  // 3. INICIALIZACIÓN Y CONFIGURACIÓN
  // ==========================================

  #verificarEstadoCaja = async () => {
    //limpiamos el contenedor antes de la consulta
    this.#divOpenBox.html("");
    const response = await this.apiBox.get("getuserCheckedBox");
    let htmlBoton = "";
    //const htmlBoton = response.status
    /*  ? this.#generarBotonAperturaHtml()
      : this.#generarBotonGestionHtml();*/
    //si el negocio requiere aperturar caja es pro entonces requiresbox es true
    if (response.requiresbox) {
      //si el negocio es true, el usuario puede abrir una caja
      //si el negocio es false, el usuario ya tiene abierta una caja
      htmlBoton = response.status
        ? this.#generarBotonAperturaHtml()
        : this.#generarBotonGestionHtml();
    } else {
      //si el negocio es free no muestra nada
      htmlBoton = "";
    }

    this.#divOpenBox.html(htmlBoton);
    this.#activarListenersDinamicos();
  };

  // Configura eventos de elementos que SIEMPRE existen en el HTML (Modales base)
  #configurarEventosEstaticos = () => {
    // Input de dinero (Arqueo)
    this.#containerArqueoInputsDinero.on(
      "input",
      "input[type='number']",
      this.#handleInputConteoDinero,
    );

    // Botones Arqueo
    $("#btnLimpiarArqueo")
      .off("click")
      .on("click", () => this.#limpiarArqueo());
    $("#setArqueoCaja")
      .off("click")
      .on("click", this.#handleClickRegistrarArqueoCaja);

    // Botón Finalizar Cierre
    this.#btnFinalizarCierre
      .off("click")
      .on("click", this.#handleClickFinalizarCierre);

    // --- NUEVO: Eventos del Modal de Movimientos ---
    this.#btnTypeIngreso
      .off("click")
      .on("click", () => this.#cambiarTipoMovimiento("Ingreso"));
    this.#btnSaveMovement
      .off("click")
      .on("click", this.#handleClickGuardarMovimiento);
    this.#btnSaveRetireCash
      .off("click")
      .on("click", this.#handleClickGuardarRetiro);
  };

  // Configura eventos de elementos DINÁMICOS (Botones generados por JS o dentro de vistas cargadas)
  #activarListenersDinamicos = () => {
    // Botones principales
    $("#btnOpenModalBox")
      .off("click")
      .on("click", this.#handleClickAbrirModalSeleccion);
    $("#btnOpenModalGestionBox")
      .off("click")
      .on("click", this.#handleClickAbrirModalGestion);

    // Botones dentro de Gestión (Delegados al body porque el modal se renderiza/abre después)
    $("body")
      .off("click", "#btnOpenModalArqueoBox")
      .on("click", "#btnOpenModalArqueoBox", this.#handleClickAbrirModalArqueo);
    $("body")
      .off("click", "#btnOpenModalCloseBox")
      .on("click", "#btnOpenModalCloseBox", this.#handleClickAbrirModalCierre);

    // --- NUEVO: Botón Azul "Ingreso"
    $("body")
      .off("click", "#btnOpenModalMovement")
      .on("click", "#btnOpenModalMovement", async (e) => {
        const reset = $(e.currentTarget).attr("data-header");
        this.#resetearModalMovimiento(reset);
        // Consultamos la data para mostrar en venta rapida
        const response = await this.apiBox.get("getDataQuickSale");
        if (!response.status) {
          return this.#mostrarAlerta({
            icon: response.icon,
            title: response.title,
            message: response.message,
          });
        }
        this.#renderQuickSale(response);
        this.#handleCheckTax();
        this.#modalMovementBox.modal("show");
      });

    // --- NUEVO: Botón Rojo "Retiro"
    $("body")
      .off("click", "#btnOpenModalRetireCash")
      .on("click", "#btnOpenModalRetireCash", async (e) => {
        const reset = $(e.currentTarget).attr("data-header");
        this.#resetearModalRetireMovimiento(reset);
        // Consultamos la data para mostrar en venta rapida
        const response = await this.apiBox.get("getDataRetireCash");
        if (!response.status) {
          return this.#mostrarAlerta({
            icon: response.icon,
            title: response.title,
            message: response.message,
          });
        }
        this.#renderRetireCash(response);
        this.#modalRetireMovementBox.modal("show");
      });

    // Formulario Apertura
    this.#btnOpenBox.off("click").on("click", this.#handleClickGuardarApertura);
  };

  // ==========================================
  // 4. LÓGICA DE MOVIMIENTOS (NUEVO)
  // ==========================================

  #cambiarTipoMovimiento = (tipo) => {
    this.#inputMovementType.val(tipo);

    if (tipo === "Ingreso") {
      // Visual: Botones Switch
      this.#btnTypeIngreso
        .addClass("btn-primary shadow-sm text-white")
        .removeClass("text-muted btn-transparent");

      // Visual: Input y Botón Guardar (Verde)
      this.#btnSaveMovement
        .removeClass("btn-danger")
        .addClass("btn-success")
        .html('<i class="bi bi-check2-circle me-2"></i> Registrar Ingreso');

      // Visual: Icono
      this.#iconMovementWrapper
        .removeClass("bg-danger-subtle text-danger")
        .addClass("bg-success-subtle text-success");
    } else {
      // Visual: Botones Switch
      this.#btnTypeIngreso
        .removeClass("btn-primary shadow-sm text-white")
        .addClass("text-muted btn-transparent");

      // Visual: Input y Botón Guardar (Rojo)
      this.#btnSaveMovement
        .removeClass("btn-success")
        .addClass("btn-danger")
        .html('<i class="bi bi-dash-circle me-2"></i> Registrar Retiro');

      // Visual: Icono
      this.#iconMovementWrapper
        .removeClass("bg-success-subtle text-success")
        .addClass("bg-danger-subtle text-danger");
    }
  };

  #resetearModalMovimiento = (reload) => {
    this.#statusRegisterHeader = reload;

    // 1. Limpiar inputs principales
    this.#inputMovementAmount.val("");
    this.#inputMovementDescription.val("");

    // 2. Resetear lógica de Impuestos
    this.#inputCheckTax.prop("checked", false); // Desmarcar check
    this.#inputTax.val(""); // Limpiar input impuesto
    this.#inputTotal.val(this.#formatoMoneda(0)); // Limpiar input total

    // 3. Restaurar estilos visuales (volver a gris "desactivado")
    // Agregamos el gris y quitamos el blanco
    this.#spanTax.addClass("bg-dark-subtle").removeClass("bg-white");
    this.#inputTax.addClass("bg-dark-subtle").removeClass("bg-white");

    // 4. Reset a Ingreso por defecto
    this.#cambiarTipoMovimiento("Ingreso");
  };

  #resetearModalRetireMovimiento = (reload) => {
    this.#statusExpenseHeader = reload;

    // 1. Limpiar inputs principales
    this.#inputMovementAmount.val("");
    this.#inputMovementDescription.val("");

    // 2. Resetear lógica de Impuestos
    this.#inputCheckTax.prop("checked", false); // Desmarcar check
    this.#inputTax.val(""); // Limpiar input impuesto
    this.#inputTotal.val(this.#formatoMoneda(0)); // Limpiar input total

    // 3. Restaurar estilos visuales (volver a gris "desactivado")
    // Agregamos el gris y quitamos el blanco
    this.#spanTax.addClass("bg-dark-subtle").removeClass("bg-white");
    this.#inputTax.addClass("bg-dark-subtle").removeClass("bg-white");

    // 4. Reset a Ingreso por defecto
    this.#cambiarTipoMovimiento("Ingreso");
  };

  #handleClickGuardarMovimiento = async () => {
    const amount = this.#inputMovementAmount.val();
    let description = this.#inputMovementDescription.val();
    const type = this.#inputMovementType.val(); // "Ingreso" o "Retiro"
    const customer = this.#selectMovementCustomer.val(); // "Ingreso" o "Retiro"
    const payment_method = this.#selectMovementPaymentMethod.val(); // "Ingreso" o "Retiro"
    const status_movement_header = this.#statusRegisterHeader;

    if (!amount || amount <= 0)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Monto inválido",
        message: "Ingrese un monto mayor a 0.",
      });

    if (!description) description = "Venta rápida";
    if (!customer)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione el cliente.",
      });

    if (!payment_method)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione el metodo de pago.",
      });

    const params = {
      amount: amount,
      description: description,
      type_movement: type,
      customer: customer,
      payment_method: payment_method,
      status_movement_header: status_movement_header,
      check_tax: this.#inputCheckTax.prop("checked"),
    };

    // Llamada al Backend
    const response = await this.apiBox.post("setBoxMovement", params);

    if (response.status) {
      this.#modalMovementBox.modal("hide");
      if (response.status_movement_header === 1) {
        this.#handleClickAbrirModalGestion(); // Recargar gestión para ver el nuevo saldo
      }
    }
    this.#mostrarAlerta(response);
  };

  #handleClickGuardarRetiro = async () => {
    const amount = this.#inputRetireAmount.val();
    const date = this.#inputRetireDate.val();
    let description = this.#inputRetireDescription.val();
    let retire_name = this.#inputRetireName.val();
    const supplier = this.#selectRetireSupplier.val();
    const expense_category = this.#selectRetireExpenseCategory.val();
    const payment_method = this.#selectRetirePaymentMethod.val();
    const status_expense_header = this.#statusExpenseHeader;

    if (!supplier)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione el proveedor.",
      });

    if (!expense_category)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione la categoría de gasto.",
      });

    if (!amount || amount <= 0)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Monto inválido",
        message: "Ingrese un monto mayor a 0.",
      });

    if (!date)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione la fecha.",
      });

    if (!payment_method)
      return this.#mostrarAlerta({
        icon: "warning",
        title: "Faltan datos",
        message: "Seleccione el metodo de pago.",
      });

    if (!description) description = null;
    if (!retire_name) retire_name = "Gasto sin nombre"; // falta agregar la fecha

    const params = {
      amount: amount,
      description: description,
      expense_name: retire_name,
      date: date,
      supplier: supplier,
      payment_method: payment_method,
      expense_category: expense_category,
      status_expense_header: status_expense_header,
    };

    // Llamada al Backend
    const response = await this.apiBox.post("setExpense", params);

    console.log(response);

    if (response.status) {
      //LIMPIAMOS los campos
      this.#inputRetireAmount.val("");
      this.#inputRetireDate.val("");
      this.#inputRetireDescription.val("");
      this.#inputRetireName.val("");

      this.#selectRetireSupplier.val("").trigger("change");
      this.#selectRetireExpenseCategory.val("").trigger("change");
      this.#selectRetirePaymentMethod.val("").trigger("change");

      //cierra el modal
      this.#modalRetireMovementBox.modal("hide");

      if (response.status_expense_header === 1) {
        this.#handleClickAbrirModalGestion(); // Recargar gestión para ver el nuevo saldo
      }
    }
    this.#mostrarAlerta(response);
  };

  // ==========================================
  // 5. MANEJADORES DE EVENTOS EXISTENTES
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

  #handleClickGuardarApertura = async () => {
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
  };

  #handleClickRegistrarArqueoCaja = async () => {
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

    if (responseArqueo && responseArqueo.status && responseArqueo.data) {
      existeArqueoEnBd = true;
      montoContado = parseFloat(responseArqueo.data.counted_amount) || 0;
    } else {
      montoContado = this.#totalEfectivoContado;
    }

    const totalVentas = parseFloat(data.total_general) || 0;
    const totalTransacciones =
      data.total_transacciones ||
      (data.movements_limit ? data.movements_limit.length : 0);
    const efectivoVentas = parseFloat(data.total_payment_method.Efectivo) || 0;
    const baseInicial = parseFloat(data.amount_base) || 0;
    const egresosCaja = parseFloat(data.total_efectivo_egreso) || 0;

    // Ingresos reales a caja (solo efectivo + base ya está en sistema)
    const ingresosCaja = efectivoVentas + egresosCaja;
    // Si tu backend suma Ingresos extras a 'total_general', revisa esta lógica.
    // Por ahora asumimos Ingresos Caja = Ventas Efectivo. Si sumas ingresos manuales, agrégalos aquí.

    const totalEsperadoSistema = baseInicial + ingresosCaja - egresosCaja;
    const diferencia = montoContado - totalEsperadoSistema;

    // Renderizado
    this.#lblCloseBoxTotalSales.html(this.#formatoMoneda(totalVentas));
    this.#lblCloseBoxTotalTransactions.html(totalTransacciones);

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

    this.#lblCloseBoxBase.html(this.#formatoMoneda(baseInicial));
    this.#lblCloseBoxIncome.html(`+${this.#formatoMoneda(ingresosCaja)}`);
    this.#lblCloseBoxExpenses.html(`-${this.#formatoMoneda(egresosCaja)}`);
    this.#lblCloseBoxExpected.html(this.#formatoMoneda(totalEsperadoSistema));

    this.#lblCloseBoxSistema.html(this.#formatoMoneda(totalEsperadoSistema));
    this.#lblCloseBoxContado.html(this.#formatoMoneda(montoContado));

    const signoDiff = diferencia > 0 ? "+" : "";
    this.#lblCloseBoxDifference.html(
      `${signoDiff}${this.#formatoMoneda(diferencia)}`,
    );
    if (diferencia < 0)
      this.#lblCloseBoxDifference
        .removeClass("text-success")
        .addClass("text-danger");
    else
      this.#lblCloseBoxDifference
        .removeClass("text-danger")
        .addClass("text-success");

    let htmlStatus = "";
    if (!existeArqueoEnBd && montoContado === 0 && totalEsperadoSistema > 0) {
      htmlStatus = `<div class="alert alert-warning py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-exclamation-circle-fill fs-5"></i><div><strong>Advertencia:</strong> No se ha realizado conteo físico (Arqueo).</div></div>`;
    } else if (Math.abs(diferencia) < 0.1) {
      htmlStatus = `<div class="alert alert-success py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-check-circle-fill fs-5"></i><div><strong>Cuadre Correcto</strong><br>El arqueo coincide con el sistema.</div></div>`;
    } else if (diferencia > 0) {
      htmlStatus = `<div class="alert alert-primary py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-graph-up-arrow fs-5"></i><div><strong>Dinero Sobrante</strong><br>Excedente: +${this.#formatoMoneda(
        diferencia,
      )}</div></div>`;
    } else {
      htmlStatus = `<div class="alert alert-danger py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small"><i class="bi bi-exclamation-triangle-fill fs-5"></i><div><strong>Descuadre (Faltante)</strong><br>Diferencia: ${this.#formatoMoneda(
        diferencia,
      )}</div></div>`;
    }

    this.#containerCloseBoxStatus.html(htmlStatus);
    this.#modalCloseBox.modal("show");
  };

  #handleClickFinalizarCierre = async () => {
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
    const params = { idBoxSession: this.#selectBox.val() || 1, notes: notes };

    const response = await this.apiBox.post("setCloseBoxSession", params);

    if (response.status) {
      setTimeout(() => {
        this.#modalCloseBox.modal("hide");
        window.location.reload();
      }, 2000);
    }
    this.#mostrarAlerta(response);
  };

  #handleCheckTax = () => {
    // 1. Creamos una función reutilizable para calcular y pintar
    const recalcular = () => {
      // Leemos el estado actual (NO usamos click, solo leemos)
      const isChecked = this.#inputCheckTax.prop("checked");
      const amount = +this.#inputMovementAmount.val() || 0; // El || 0 evita NaN si borran todo

      if (isChecked) {
        const interes = (amount * this.#valueTax) / 100;
        const total = interes + amount;

        // Estilos: Activo (Blanco)
        this.#spanTax.removeClass("bg-dark-subtle").addClass("bg-white");
        this.#inputTax.removeClass("bg-dark-subtle").addClass("bg-white");

        this.#inputTax.val(this.#formatoMoneda(interes));
        this.#inputTotal.val(this.#formatoMoneda(total));
      } else {
        // Estilos: Inactivo (Gris)
        this.#spanTax.addClass("bg-dark-subtle").removeClass("bg-white");
        this.#inputTax.addClass("bg-dark-subtle").removeClass("bg-white");

        this.#inputTax.val(this.#formatoMoneda(0));
        this.#inputTotal.val(this.#formatoMoneda(amount));
      }
    };

    // ---------------------------------------------------------
    // 2. Asignamos los eventos
    // ---------------------------------------------------------

    // Evento A: Cuando el usuario marca/desmarca el check
    this.#inputCheckTax.on("click", () => {
      recalcular();
    });

    // Evento B: Cuando el usuario escribe números
    // Usamos arrow function (e) => para no perder el 'this' de la clase
    this.#inputMovementAmount.on("input", (e) => {
      recalcular();
    });
  };

  // ==========================================
  // 6. UTILIDADES DE RENDERIZADO (Helpers)
  // ==========================================

  #renderOpcionesDeCaja = (listaCajas) => {
    let html =
      '<option value="" disabled selected>Seleccione una caja...</option>';
    listaCajas.forEach((box, index) => {
      const num = index + 1;
      let clase = box.session ? "text-primary fw-bold" : "";
      let disabled = !box.session ? "" : "disabled";
      let extra = box.session ? "(En uso)" : "";
      html += `<option class="${clase}" ${disabled} value="${box.idBox}">Caja ${num} - ${box.name} ${extra}</option>`;
    });
    this.#selectBox.html(html);
  };

  #renderVistaGestion = () => {
    this.#lblTituloGestionBoxName.html(this.#datosSesionCaja.name_box);
    this.#lblTituloArqueoBoxName.html(this.#datosSesionCaja.name_box);
    this.#lblTituloCloseBoxName.html(this.#datosSesionCaja.name_box);

    let amount_base = parseFloat(this.#datosSesionCaja.amount_base) || 0;
    this.#lblTotalGeneral.html(
      this.#formatoMoneda(this.#datosSesionCaja.total_general + amount_base),
    );
    this.#lblBaseAmount.html(
      `Base: ${this.#formatoMoneda(this.#datosSesionCaja.amount_base)}`,
    );

    let htmlMetodos = "";
    this.#datosSesionCaja.payment_method.forEach((el) => {
      if (this.#datosSesionCaja.total_payment_method[el.name] !== undefined) {
        let totalToShow = parseFloat(
          this.#datosSesionCaja.total_payment_method[el.name],
        );
        if (el.name === "Efectivo") totalToShow += amount_base;
        htmlMetodos += this.#crearCardMetodoPago(el, totalToShow);
      }
    });
    this.#containerMetodosPago.html(htmlMetodos);

    const moves = this.#datosSesionCaja.movements_limit || [];
    this.#lblTituloMovimientos.html(
      `Últimos <span class="text-primary">${moves.length}</span> Movimientos`,
    );
    this.#containerListaMovimientos.html(
      moves.map((mov) => this.#crearItemMovimiento(mov)).join(""),
    );
    // NUEVO: Llamar a la gráfica
    // Pasamos el objeto 'chart_data' que enviamos desde PHP
    this.#renderGraphicSales(this.#datosSesionCaja.chart_data);
  };

  #renderResumenEsperadoArqueo() {
    const data = this.#datosSesionCaja;
    const ventaEfectivo = parseFloat(data.total_payment_method.Efectivo) || 0;
    const base = parseFloat(data.amount_base) || 0;
    const totalEfectivoEsperado = ventaEfectivo + base;

    this.#lblArqueoTotalEfectivo.html(
      this.#formatoMoneda(totalEfectivoEsperado),
    );
    this.#lblArqueoTotalGeneral.html(this.#formatoMoneda(data.total_general));
    this.#totalEfectivoSistema = totalEfectivoEsperado;

    let htmlTarjetas = `
          <div class="flex-fill p-2 rounded-4 bg-primary-subtle border border-primary text-center">
            <small class="d-block text-primary fw-bold mb-1" style="font-size: 0.7rem;">Monto Inicial</small>
            <span class="fw-bold text-primary">${this.#formatoMoneda(
              base,
            )}</span>
          </div>`;

    htmlTarjetas += data.payment_method
      .filter((el) => data.total_payment_method[el.name] !== undefined)
      .map(
        (el) => `
          <div class="flex-fill p-2 rounded-4 bg-body-tertiary border text-center">
            <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem;">${
              el.name
            }</small>
            <span class="fw-bold text-dark">${this.#formatoMoneda(
              data.total_payment_method[el.name],
            )}</span>
          </div>`,
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
      htmlFinal += `<div class="d-flex align-items-center mb-3"><h6 class="fw-bold ${config.text} mb-0 me-3" style="min-width: 65px;"><i class="bi ${config.icon} me-2"></i>${tipo}</h6><div class="flex-grow-1 border-bottom"></div></div><div class="row g-2 mb-4">`;
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

  #renderGraphicSales = (chartData) => {
    // Si no existe el canvas en el DOM, salimos
    if (this.#canvasGraphic.length === 0) return;

    // Destruir gráfica anterior si existe para evitar superposiciones
    if (this.#chartInstance) {
      this.#chartInstance.destroy();
    }

    const ctx = this.#canvasGraphic.get(0).getContext("2d");

    // --- MEJORA VISUAL 1: Gradiente más suave ---
    // Creamos un degradado verde que se desvanece hacia abajo
    const gradient = ctx.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, "rgba(25, 135, 84, 0.5)"); // Verde "Success" semitransparente arriba
    gradient.addColorStop(1, "rgba(25, 135, 84, 0.05)"); // Casi transparente abajo

    // Fuente estándar bonita (tipo Bootstrap)
    const fontStack = "'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";

    this.#chartInstance = new Chart(ctx, {
      type: "line",
      data: {
        labels: chartData && chartData.labels ? chartData.labels : [],
        datasets: [
          {
            data: chartData && chartData.values ? chartData.values : [],
            label: "Ventas Totales", // Etiqueta interna
            borderColor: "#198754", // Color de la línea (Verde Bootstrap Success)
            backgroundColor: gradient, // El degradado que creamos arriba
            borderWidth: 3, // Línea un poco más gruesa
            // --- MEJORA VISUAL 2: Puntos más estilizados ---
            pointBackgroundColor: "#fff", // Punto blanco por dentro
            pointBorderColor: "#198754", // Borde verde
            pointBorderWidth: 2,
            pointRadius: 5, // Puntos normales más grandes
            pointHoverRadius: 8, // Puntos muy grandes al pasar el mouse
            fill: true, // Rellenar el área debajo
            tension: 0.3, // Curvatura suave de la línea (0 es recta, 1 es muy curva)
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: "index",
          intersect: false,
        },
        plugins: {
          legend: { display: false }, // Ocultamos la leyenda por defecto (ya tenemos título)
          // --- MEJORA VISUAL 3: Título Principal ---
          title: {
            display: true,
            text: "Ingreso de Ventas por Hora (Turno Actual)",
            align: "start", // Alineado a la izquierda
            color: "#343a40", // Color gris oscuro
            font: {
              size: 16,
              family: fontStack,
              weight: "bold",
            },
            padding: { bottom: 20 },
          },
          // --- MEJORA VISUAL 4: Tooltip Profesional ---
          tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.95)", // Fondo blanco casi opaco
            titleColor: "#000", // Texto negro
            bodyColor: "#000",
            borderColor: "#198754", // Borde verde
            borderWidth: 1,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
            callbacks: {
              // Título del tooltip con un icono de reloj
              title: (tooltipItems) => {
                return "🕒 Hora: " + tooltipItems[0].label;
              },
              // Etiqueta del valor con formato de moneda claro
              label: (context) => {
                const valor = Number(context.parsed.y).toFixed(2);
                return `  Ventas: S/ ${valor}`;
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            // --- MEJORA VISUAL 5: Título y Formato Eje Y ---
            title: {
              display: true,
              text: "Monto Vendido (S/)",
              color: "#6c757d",
              font: { family: fontStack, weight: "bold", size: 11 },
            },
            grid: {
              borderDash: [5, 5], // Líneas de cuadrícula punteadas
              color: "#e9ecef",
            },
            ticks: {
              color: "#6c757d",
              font: { family: fontStack, size: 11 },
              // Agregar "S/" a los números del eje Y
              callback: function (value) {
                return "S/ " + value;
              },
            },
          },
          x: {
            // --- MEJORA VISUAL 6: Título Eje X ---
            title: {
              display: true,
              text: "Hora del día (Formato 24h)",
              color: "#6c757d",
              font: { family: fontStack, weight: "bold", size: 11 },
              padding: { top: 10 },
            },
            grid: { display: false }, // Sin líneas verticales para limpieza
            ticks: {
              color: "#6c757d",
              font: { family: fontStack, size: 11 },
            },
          },
        },
      },
    });
  };

  #renderQuickSale = ({ customers, payment_method, tax_business }) => {
    let html_customers = "<option disabled>Seleccionar</option>";
    customers.forEach((element) => {
      html_customers += `<option ${
        element.document_number === "Sin cliente" ? "selected" : ""
      } value="${element.idCustomer}">${element.fullname}</option>`;
    });

    this.#selectMovementCustomer.html(html_customers);

    let html_payment_method = "<option disabled>Seleccionar</option>";
    payment_method.forEach((element) => {
      html_payment_method += `<option ${
        element.name === "Efectivo" ? "selected" : ""
      } value="${element.idPaymentMethod}">${element.name}</option>`;
    });

    this.#selectMovementPaymentMethod.html(html_payment_method);

    this.#labelTaxName.html(tax_business.taxname);
    this.#spanTax.html(tax_business.tax + `%`);
    this.#valueTax = tax_business.tax;

    console.log(customers);
    console.log(payment_method);
    console.log(tax_business);
  };

  #renderRetireCash = ({ category_expences, payment_method, supplier }) => {
    let html_supplier = "<option disabled>Seleccionar</option>";
    supplier.forEach((element) => {
      html_supplier += `<option ${
        element.document_number === "00000000000" ? "selected" : ""
      } value="${element.idSupplier}">${element.company_name}</option>`;
    });

    this.#selectRetireSupplier.html(html_supplier);

    let html_category = "<option disabled selected>Seleccionar</option>";
    category_expences.forEach((element) => {
      html_category += `<option value="${element.idExpenseCategory}">${element.name}</option>`;
    });

    this.#selectRetireExpenseCategory.html(html_category);

    let html_payment_method = "<option disabled>Seleccionar</option>";
    payment_method.forEach((element) => {
      html_payment_method += `<option ${
        element.name === "Efectivo" ? "selected" : ""
      } value="${element.idPaymentMethod}">${element.name}</option>`;
    });

    this.#selectRetirePaymentMethod.html(html_payment_method);

    console.log(category_expences);
    console.log(payment_method);
    console.log(supplier);
  };

  #actualizarUIArqueo() {
    this.#lblArqueoTotalContado.html(
      this.#formatoMoneda(this.#totalEfectivoContado),
    );
    const diferencia = this.#totalEfectivoSistema - this.#totalEfectivoContado;
    let uiState = this.#determinarEstadoArqueo(
      this.#totalEfectivoContado,
      diferencia,
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
        datos.total,
      )}</div></div>`;
    });
    this.#containerDesgloseFinal.html(htmlDesglose);
  }

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

  #generarBotonAperturaHtml = () => `
    <div class="d-flex justify-content-center align-items-center">
        <button title="Abrir Caja" id="btnOpenModalBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
            <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
            <span class="d-none d-sm-block fw-semibold">Abrir Caja</span>
        </button>
    </div>`;

  #generarBotonGestionHtml = () => `
    <div class="d-flex justify-content-center align-items-center">
        <button title="Gestión de Caja" id="btnOpenModalGestionBox" class="btn btn-warning px-2 py-1 d-flex align-items-center gap-2 fw-bold">
            <img style="width: 22px;" src="${media_url}/icons/POS/open-box.png" alt="">
            <span class="d-none d-sm-block fw-semibold">Gestión de Caja</span>
        </button>
    </div>`;

  #crearCardMetodoPago = (el, total) => `
    <div class="col-6 col-sm-4">
        <div class="card border rounded-4 h-100 bg-body-tertiary">
            <div class="card-body p-3 text-center">
                <span class="d-inline-flex align-items-center justify-content-center border bg-white rounded-circle mb-2" style="width: 35px; height: 35px;">${
                  el.icon
                }</span>
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">${
                  el.name
                }</div>
                <h6 class="fw-bold mb-0 text-dark">${this.#formatoMoneda(
                  total,
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
                  element.movement_date,
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
