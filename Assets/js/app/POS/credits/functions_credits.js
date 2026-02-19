(() => {
  "use strict";
  const filterSearch = document.getElementById("filter-search");
  const filterDateStart = document.getElementById("filter-date-start");
  const filterDateEnd = document.getElementById("filter-date-end");
  const filterBtn = document.getElementById("filter-btn");
  const resetBtn = document.getElementById("reset-btn");
  //elementos del modal de reporte
  const detailCustomerName = document.getElementById("detailCustomerName");
  const detailCustomerDocument = document.getElementById(
    "detailCustomerDocument"
  );
  const detailCustomerStatus = document.getElementById("detailCustomerStatus");
  const detailCustomerCode = document.getElementById("detailCustomerCode");
  const detailCustomerPhone = document.getElementById("detailCustomerPhone");
  const detailCustomerDirection = document.getElementById(
    "detailCustomerDirection"
  );
  const detailCustomerBillingDay = document.getElementById(
    "detailCustomerBillingDay"
  );
  const detailCustomerCreditLimitFinancing = document.getElementById(
    "detailCustomerCreditLimitFinancing"
  );
  const detailCustomerMonthlyInterest = document.getElementById(
    "detailCustomerMonthlyInterest"
  );
  const detailCustomerMonthlyInterestFinancing = document.getElementById(
    "detailCustomerMonthlyInterestFinancing"
  );
  const detailCustomerCreditLimit = document.getElementById(
    "detailCustomerCreditLimit"
  );
  const detailCustomerPercentConsu = document.getElementById(
    "detailCustomerPercentConsu"
  );
  const detailCustomerIndicadorPercent = document.getElementById(
    "detailCustomerIndicadorPercent"
  );
  const detailCustomerAmountDisp = document.getElementById(
    "detailCustomerAmountDisp"
  );
  const modalFilterDateStart = document.getElementById(
    "modal-filter-date-start"
  );
  const modalFilterDateEnd = document.getElementById("modal-filter-date-end");
  const modalFilterBtn = document.getElementById("modal-filter-btn");
  const modalFilterReset = document.getElementById("modal-filter-reset");
  const modalFilterSaleType = document.getElementById("modal-filter-sale-type");
  const modalFilterPaymentStatus = document.getElementById(
    "modal-filter-payment-status"
  );
  //elementos del modal de reporte de creditos
  const detailCustomerTotalPurchased = document.getElementById(
    "detailCustomerTotalPurchased"
  );
  const detailCustomerTotalPaid = document.getElementById(
    "detailCustomerTotalPaid"
  );
  const detailCustomerTotalDebt = document.getElementById(
    "detailCustomerTotalDebt"
  );
  //cuerpo de la tabla de creditos
  const customerSalesBody = document.getElementById("customerSalesBody");
  /**
   * Variable que almacena la tabla de creditos
   */
  let table;
  /**
   * Variable que obtiene el id el cliente
   */
  let idCustomer;
  /**
   * Obtenemos el total del credito individual a pagar
   */
  let totalIndividualCredit;
  /**
   * obtenemos el id del voucher
   */
  let idVoucher;
  /**
   * Evento que se ejecuta cuando el DOM esta cargado
   */
  document.addEventListener("DOMContentLoaded", function () {
    //definimos la fecha minima de la fecha de fin
    filterDateEnd.min = filterDateStart.value;
    // Carga la tabla de creditos
    loadTable();
    // Carga los eventos de los filtros
    inputsEventsFilters();
  });
  /**
   * Carga la tabla de creditos
   */
  function loadTable() {
    table = $("#table").DataTable({
      processing: true,
      ajax: {
        url: base_url + "/pos/credits/getAllCreditsFilters",
        data: function (d) {
          d.startDate = filterDateStart.value;
          d.endDate = filterDateEnd.value;
          d.search = filterSearch.value;
        },
        dataSrc: function (json) {
          if (json.url) {
            setTimeout(() => {
              window.location.href = json.url;
            }, 1000);
          }
          // Importante: serverSide espera que los datos vengan en json.data
          return json;
        },
      },
      columns: [
        { data: "cont" },
        {
          data: "actions",
          render: (data, type, row) => {
            return `<div class="button-group">
              <button class="btn btn-sm btn-outline-secondary btn-report-credit" data-id="${row.idCustomer}" title="Ver detalles">
                <i class="bi bi-file-earmark-text"></i>
              </button>
            </div>`;
          },
        },
        { data: "fullname" },
        {
          data: "billing_date",
          render: (data, type, row) => {
            return `<span class="text-danger"><i class="bi bi-calendar"></i> ${data ?? "Sin fecha <i class='bi bi-slash-circle'></i>"}</span>`;
          },
        },
        {
          data: "credit_limit",
          render: (data, type, row) => {
            return `${getcurrency} ${data == 0 ? "Sin límite" : data}`;
          },
        },
        {
          data: "amount_pending",
          render: (data, type, row) => {
            const percentage = data / row.credit_limit;
            if (data <= 0) {
              return `<span class="text-success"> <i class="bi bi-check-circle"></i> ${getcurrency} ${data}</span>`;
            } else if (percentage > 0 && percentage <= 0.25) {
              return `<span class="text-info"> <i class="bi bi-exclamation-circle"></i> ${getcurrency} ${data}</span>`;
            } else if (percentage > 0.25 && percentage <= 0.5) {
              return `<span class="text-warning"> <i class="bi bi-exclamation-circle"></i> ${getcurrency} ${data}</span>`;
            } else if (percentage > 0.5) {
              return `<span class="text-danger"> <i class="bi bi-exclamation-triangle"></i> ${getcurrency} ${data}</span>`;
            }
          },
        },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard-check'></i> Copiar",
          titleAttr: "Copiar",
          className: "btn btn-sm btn-outline-secondary",
          exportOptions: {
            columns: [2, 3, 4, 5],
          },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          title: "Reporte de créditos en Excel",
          className: "btn btn-sm btn-outline-success",
          exportOptions: {
            columns: [2, 3, 4, 5],
          },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          title: "Reporte de créditos en CSV",
          className: "btn btn-sm btn-outline-info",
          exportOptions: {
            columns: [2, 3, 4, 5],
          },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-file-earmark-pdf'></i> PDF",
          title: "Reporte de créditos en PDF",
          className: "btn btn-sm btn-outline-danger",
          orientation: "landscape",
          pageSize: "LEGAL",
          exportOptions: {
            columns: [2, 3, 4, 5],
          },
        },
      ],
      columnDefs: [
        {
          targets: [0],
          visible: true,
          searchable: false,
        },
        {
          targets: [1],
          className: "text-center",
        },
        {
          targets: [2],
          className: "text-center",
        },
        {
          targets: [3],
          searchable: false,
          className: "text-center",
        },
        {
          targets: [4],
          searchable: false,
          className: "text-center",
        },
        {
          targets: [5],
          searchable: false,
          className: "text-center",
        },
      ],
      keyTable: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: base_url + "/Assets/js/libraries/POS/Spanish-datatables.json",
      },
      // Callback que se ejecuta después de que se carguen los datos
      drawCallback: function () {
        getCreditsReport();
      },
    });
  }
  /**
   * Filtra la tabla de creditos
   */
  function inputsEventsFilters() {
    /**
     * Evento que se ejecuta cuando el usuario escribe en el input de busqueda
     */
    if (filterSearch) {
      filterSearch.addEventListener("input", () => {
        table.ajax.reload();
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia la fecha de inicio
     */
    if (filterDateStart) {
      filterDateStart.addEventListener("input", () => {
        //definimos la fecha minima de la fecha de fin
        filterDateEnd.min = filterDateStart.value;
        table.ajax.reload();
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia la fecha de fin
     */
    if (filterDateEnd) {
      filterDateEnd.addEventListener("input", () => {
        //hacemos que el maximo de la fecha de inicio sea la fecha de fin
        filterDateStart.max = filterDateEnd.value;
        table.ajax.reload();
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario hace clic en el boton de filtrar
     */
    if (filterBtn) {
      filterBtn.addEventListener("click", () => {
        table.ajax.reload();
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario hace clic en el boton de limpiar
     */
    if (resetBtn) {
      resetBtn.addEventListener("click", () => {
        filterSearch.value = "";
        filterDateStart.value = "";
        filterDateEnd.value = "";
        table.ajax.reload();
      });
    }
    /**
     * eventos de filtros del modal de rporte de creditos
     */
    if (modalFilterDateStart) {
      modalFilterDateStart.addEventListener("input", () => {
        //establecemos la fecha minima de la fecha de fin
        modalFilterDateEnd.min = modalFilterDateStart.value;
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia el tipo de venta
     */
    if (modalFilterSaleType) {
      modalFilterSaleType.addEventListener("change", () => {
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia el estado de pago
     */
    if (modalFilterPaymentStatus) {
      modalFilterPaymentStatus.addEventListener("change", () => {
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia la fecha de fin
     */
    if (modalFilterDateEnd) {
      modalFilterDateEnd.addEventListener("input", () => {
        //si en caso se seleccionas una fecha establecemos la fecha maxima de la fecha de inicio
        modalFilterDateStart.max = modalFilterDateEnd.value;
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario hace clic en el boton de filtrar
     */
    if (modalFilterBtn) {
      modalFilterBtn.addEventListener("click", () => {
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario hace clic en el boton de limpiar
     */
    if (modalFilterReset) {
      modalFilterReset.addEventListener("click", () => {
        modalFilterDateStart.value = "";
        modalFilterDateEnd.value = "";
        getInformationDetailCredist(
          idCustomer,
          modalFilterDateStart.value,
          modalFilterDateEnd.value,
          modalFilterSaleType.value ?? "All",
          modalFilterPaymentStatus.value ?? "All"
        );
      });
    }
  }
  /**
   * Metodo que se encarga de obtener la información de los
   * creditos
   */
  function getCreditsReport() {
    const btnReportCredit = document.querySelectorAll(".btn-report-credit");
    if (btnReportCredit) {
      btnReportCredit.forEach((btn) => {
        btn.addEventListener("click", async () => {
          idCustomer = btn.getAttribute("data-id");
          $("#creditsReportModal").modal("show");
          await getInformationDetailCredist(
            idCustomer,
            modalFilterDateStart.value,
            modalFilterDateEnd.value,
            modalFilterSaleType.value ?? "All",
            modalFilterPaymentStatus.value ?? "All"
          );
        });
      });
    }
  }
  /**
   * Metodo que se encarga de obtener la información de los creditos del cliente
   * El metodo pide la información del cliente y los creditos del cliente
   * @param {*} idCustomer
   * @param {*} startDate
   * @param {*} endDate
   * @param {*} saleType
   * @param {*} paymentStatus
   * @returns
   */
  async function getInformationDetailCredist(
    idCustomer,
    startDate,
    endDate,
    saleType,
    paymentStatus
  ) {
    //return; //paramos temporalmente
    const formdata = new FormData();
    formdata.append("idCustomer", idCustomer);
    formdata.append("startDate", startDate);
    formdata.append("endDate", endDate);
    formdata.append("saleType", saleType);
    formdata.append("paymentStatus", paymentStatus);
    const config = {
      body: formdata,
      method: "POST",
    };
    const endpoint = `${base_url}/pos/Credits/getInfoCustomerAndCredits`;
    showAlert(
      {
        title: "Obteniendo información del cliente",
        message: "Por favor espere...",
        icon: "info",
      },
      "loading"
    );
    try {
      const response = await fetch(endpoint, config);
      const data = await response.json();
      if (!data.status) {
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
        });
        //si hay url redirigimos
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, data.timer);
        }
        return;
      }
      renderCustomerCredits(data);
      renderKPISCustomerCredits(data);
      renderBodyTableCustomerCredits(data);
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: "Por favor recargue la pagina",
        icon: "error",
      });
    } finally {
      btnPaymentItemCredit();
      swal.close();
    }
  }
  /**
   * Metodo que se encarga de renderizar la información del cliente
   * @param {*} data
   */
  function renderCustomerCredits(data) {
    /**
     * Mostramos la información del cliente
     */
    detailCustomerName.textContent = data.customer.fullname;
    detailCustomerDocument.textContent = data.customer.document_number;
    detailCustomerStatus.textContent = data.customer.status;
    /**
     * Cambiamos el color del badge segun el estado del cliente
     */
    if (data.customer.status === "Activo") {
      detailCustomerStatus.classList.remove("bg-danger");
      detailCustomerStatus.classList.add("bg-success");
    } else {
      detailCustomerStatus.classList.remove("bg-success");
      detailCustomerStatus.classList.add("bg-danger");
    }
    detailCustomerCode.textContent = `ID: #${data.customer.idCustomer}`;
    detailCustomerPhone.textContent = data.customer.phone_number;
    detailCustomerDirection.textContent = data.customer.direction;
    detailCustomerBillingDay.textContent = `Día ${data.customer.day_billing} del mes`;
    detailCustomerCreditLimit.textContent = `Total: ${getcurrency} ${data.customer.credit_limit > 0 ? data.customer.credit_limit : "Ilimitado"}`;
    detailCustomerPercentConsu.textContent = `${data.customer.percent_consu != 0 ? data.customer.percent_consu : "Ilimitado"}% Uso`;
    detailCustomerIndicadorPercent.style.width = `${data.customer.percent_consu != 0 ? data.customer.percent_consu : "100"}%`;
    detailCustomerAmountDisp.textContent = `${getcurrency} ${data.customer.amount_disp > 0 ? data.customer.amount_disp : 0}`;
    detailCustomerCreditLimitFinancing.textContent = `${getcurrency} ${data.customer.credit_limit > 0 ? data.customer.credit_limit : "Ilimitado"}`;
    detailCustomerMonthlyInterest.textContent = `${parseFloat(data.customer.default_interest_rate).toFixed(2)}%`;
    detailCustomerMonthlyInterestFinancing.textContent = `${parseFloat(data.customer.current_interest_rate).toFixed(2)}%`;
    /**
     * Fin de la información del cliente
     */
  }
  /**
   * Metodo que se encarga de renderizar los kpis de los creditos del cliente
   * @param {*} data
   */
  function renderKPISCustomerCredits(data) {
    detailCustomerTotalPurchased.textContent = `${getcurrency} ${data.kpis.total_ventas}`;
    detailCustomerTotalPaid.textContent = `${getcurrency} ${data.kpis.total_pagado}`;
    detailCustomerTotalDebt.textContent = `${getcurrency} ${data.kpis.total_pendiente}`;
  }
  /**
   * Metodo que se encarga de renderizar el cuerpo de la tabla de creditos del cliente
   * @param {*} data
   * @returns
   */
  function renderBodyTableCustomerCredits(data) {
    customerSalesBody.innerHTML = "";
    if (data.customerSales.length === 0) {
      customerSalesBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-receipt fs-1 mb-3"></i>
                            <h6 class="fw-bold">No se encontraron movimientos</h6>
                            <p class="mb-0 small">Intenta ajustar los filtros de fecha o estado para ver resultados.</p>
                        </div>
                    </td>
                </tr>
            `;
      return;
    }
    data.customerSales.forEach((sale) => {
      //estilos del tipo de venta
      let saleTypeClass = "";
      if (sale.sale_type === "Contado") {
        saleTypeClass =
          "badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10";
      } else if (sale.sale_type === "Credito") {
        saleTypeClass =
          "badge bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-10";
      } else {
        saleTypeClass =
          "badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-10";
      }
      //estilos del estado de la venta
      let salePaymentStatusClass = "";
      let rowClass = "";
      let dateClass = "ps-4 text-nowrap";
      if (sale.payment_status === "Pagado") {
        salePaymentStatusClass =
          "badge rounded-pill bg-success-subtle text-success";
      } else if (sale.payment_status === "Pendiente") {
        salePaymentStatusClass = "badge rounded-pill bg-danger text-white";
        rowClass = [
          "table-danger",
          "border-start",
          "border-4",
          "border-danger",
        ];
        dateClass = "ps-4 text-nowrap fw-bold text-danger";
      } else {
        salePaymentStatusClass =
          "badge rounded-pill bg-warning-subtle text-warning";
      }
      const row = document.createElement("tr");
      row.classList.add(...rowClass);
      //cambiamos el tipo de boton
      let btnActions = "";
      if (sale.payment_status === "Pendiente") {
        btnActions = `<button class="btn btn-sm btn-dark shadow-sm btn-payment" data-id="${sale.idVoucherHeader}"><i class="bi bi-wallet"></i></button>`;
      } else {
        btnActions = `<button class="btn btn-sm btn-light border btn-view"><i class="bi bi-file-earmark-text"></i></button>`;
      }
      //validamos si los dias han sido vnecido
      let date_status = "";
      if (sale.days_overdue < 0) {
        date_status = `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10" style="font-size: 0.65em;">${sale.date_status}</span>`;
      } else if (sale.days_overdue >= 0 && sale.days_overdue < 5) {
        date_status = `<span class="badge bg-warning text-dark border border-warning" style="font-size: 0.65em;">${sale.date_status}</span>`;
      } else if (sale.days_overdue >= 5) {
        date_status = `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" style="font-size: 0.65em;">${sale.date_status}</span>`;
      }
      row.innerHTML = `
      <td class="${dateClass}">
                  <div class="fw-medium">${sale.date}</div>
                  ${date_status}
      </td>
      <td><div class="fw-medium">${sale.voucher_name}</div>
          <span class="${saleTypeClass}" style="font-size: 0.65em;">
            ${sale.sale_type}
          </span>
      </td>
      <td class="text-end tabular-nums">${getcurrency} ${sale.amount}</td>
      <td class="text-center">
          <span class="${salePaymentStatusClass}">${sale.payment_status}</span>
      </td>
      <td class="text-end pe-4">${btnActions}</td>
      `;
      customerSalesBody.appendChild(row);
    });
  }
  /**
   * Recorremos los botones de pago y mostramos el modal de pago
   */
  function btnPaymentItemCredit() {
    const btnPayment = document.querySelectorAll(".btn-payment");
    btnPayment.forEach((btn) => {
      btn.addEventListener("click", async () => {
        idVoucher = btn.dataset.id;
        const formdata = new FormData();
        formdata.append("idVoucher", idVoucher);
        const endpoint = `${base_url}/pos/Credits/getInfoCreditToPay`;
        const config = {
          method: "POST",
          body: formdata,
        };
        //mostramos un loading

        showAlert(
          {
            title: "Obteniendo informacion del credito",
            message: "Por favor espere...Calculando intereses....",
            icon: "info",
          },
          "loading"
        );
        try {
          const response = await fetch(endpoint, config);
          const data = await response.json();
          //validamos la respuesta del estado para mostrar el error o redirigir
          if (!data.status) {
            showAlert({
              title: data.title,
              message: data.message,
              icon: data.icon,
              timer: data.timer,
            });
            if (data.url) {
              setTimeout(() => {
                window.location.href = data.url;
              }, data.timer);
            }
            return;
          }
          //guardamos el total del credito individual a pagar
          totalIndividualCredit = data.infoCredit.amount_total;
          //Mostramos un sweet alert para mostrar el detalle del pago del credito seleccionado
          Swal.fire({
            target: document.getElementById("creditsReportModal"),
            html: renderHtmlCreditPayment(data.infoCredit),
            showCancelButton: true,
            cancelButtonText: "<i class='bi bi-x-lg'></i> Cancelar",
            showConfirmButton: true,
            confirmButtonText: "<i class='bi bi-wallet'></i> Pagar",
            // Importante: Desactivar estilos por defecto para usar Bootstrap
            buttonsStyling: false,
            // Forma recomendada de añadir clases CSS
            customClass: {
              confirmButton: "btn btn-primary me-2", // me-2 añade un margen si están pegados
              cancelButton: "btn btn-secondary",
            },
            // Opcional: Evitar que se cierre al hacer clic fuera si es un proceso de pago
            allowOutsideClick: false,
            didOpen: async () => {
              await getPaymentMethods();
              // Lógica para el botón de desglose
              desglosePaymentVoucherName();
            },
            //preconfirmamos el envio de la informacion al back
            preConfirm: async (e) => {
              return await sendPaymentIndividualCredit();
            },
          }).then(async (result) => {
            if (result.isConfirmed) {
              const data = result.value;
              if (data.status) {
                //recargamos la tabla de creditos
                await getInformationDetailCredist(
                  idCustomer,
                  modalFilterDateStart.value,
                  modalFilterDateEnd.value,
                  modalFilterSaleType.value ?? "All",
                  modalFilterPaymentStatus.value ?? "All"
                );
              }
              showAlert({
                title: data.title,
                message: data.message,
                icon: data.icon,
                timer: data.timer,
              });
            }
          });
        } catch (error) {
          showAlert({
            title: "Ocurrio un error inesperado",
            message: `Error al obtener la informacion del credito - ${error.message}`,
            icon: "error",
          });
        }
      });
    });
  }
  /**
   * Renderiza el html para el modal de pago del credito
   * @param {*} data
   * @returns
   */
  function renderHtmlCreditPayment(data) {
    return `
        <!-- Cabecera Alineada a la Izquierda (Flexbox) -->
        <div class="p-3 border-bottom">
            <div class="d-flex align-items-start">
                <!-- Icono a la izquierda -->
                <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill shadow-sm me-3">
                    <i class="bi bi-receipt-cutoff fs-3"></i>
                </div>
                
                <!-- Títulos a la izquierda -->
                <div class="d-flex flex-column align-items-start">
                    <h5 class="fw-bold mb-1 text-dark">Registrar Cobro</h5>
                    <!-- Badge sutil para el número de Voucher -->
                    <span class="fw-bold text-primary border border-primary border-opacity-10 rounded-pill px-3 py-1 bg-primary bg-opacity-10" style="font-size: 0.8rem;">
                        <i class="bi bi-ticket-perforated me-1"></i>CV-${String(data.idVoucherHeader).padStart(8, "0")}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-4">
            
            <!-- 1. DETALLE DEL CRÉDITO (Cuadrícula Informativa) -->
            <div class="bg-light border rounded-3 p-3 mb-4">
                
                <!-- Grid de Datos Clave -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-white border rounded h-100">
                            <small class="d-block text-dark text-uppercase fw-bold" style="font-size: 0.7rem;">Tipo Venta</small>
                            <span class="fw-bold text-primary" style="font-size: 0.8rem;">
                                <i class="bi bi-tag-fill me-1"></i>${data.sale_type}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-white border rounded h-100">
                            <small class="d-block text-dark text-uppercase fw-bold" style="font-size: 0.7rem;">Fecha Registro</small>
                            <span class="fw-bold text-secondary" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar-check me-1"></i> ${data.date_time}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-white border rounded h-100">
                            <small class="d-block text-dark text-uppercase fw-bold" style="font-size: 0.7rem;" title="Fecha de vencimiento del crédito, se establece al momento de crear el crédito con la fecha que en ese momento tiene el usuario">Vencimiento</small>
                            <span class="fw-bold text-danger" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar-x me-1"></i>${data.payment_deadline}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded h-100">
                            <small class="d-block text-danger text-uppercase fw-bold" style="font-size: 0.7rem;">Meses Vencidos</small>
                            <span class="fw-bold text-danger fs-5 d-flex flex-column align-items-center" style="font-size: 0.8rem;">
                            <small class="fw-normal text-muted" style="font-size: 0.675rem;">${data.total_dias} dias</small>
                                <span>${data.month_overdue} <small class="fs-6 fw-normal">meses</small></span>                                
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Desglose Económico -->
                <div class="border-top pt-2 bg-white px-2 rounded-2 border-light">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Capital Base</span>
                        <span class="fw-medium">${getcurrency} ${data.amount}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-primary">Financiamiento (${data.current_interest_rate}%)</span>
                        <span class="fw-medium text-primary">+ ${getcurrency} ${data.amount_current_interest_rate}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-danger">Mora (${data.default_interest_rate}% mensual)</span>
                        <span class="fw-medium text-danger">+ ${getcurrency} ${data.amount_total_overdue}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                    <span class="small text-dark fw-bold text-uppercase">Total a Pagar</span>
                    <span class="fs-3 fw-bold text-success">${getcurrency} ${data.amount_total}</span>
                </div>
            </div>

            <!-- 2. Formulario de Cobro -->
            <div class="row g-3 text-start">
                
                <!-- A. Método -->
                <div class="col-12">
                    <label class="form-label small fw-bold text-primary text-uppercase mb-1">
                        <i class="bi bi-credit-card me-1"></i>1. Método de Pago
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border text-muted fw-bold"><i class="bi bi-credit-card me-1"></i></span>
                        <select id="swalMethodPaymentSelect" class="form-select">
                           --
                        </select>
                    </div>
                </div>

                <!-- B. Recibido -->
                <div id="swalReceivesContainer">                  
                </div>

                <!-- C. Detalle Adicional (Desglose) -->
                <div class="col-12 mt-3">
                     <button type="button" id="btnToggleDetail" class="btn btn-outline-secondary border-dashed w-100 fw-medium d-flex align-items-center justify-content-center py-2" style="border-style: dashed; border-width: 2px;" title="Agregar una nota o desglose al pago">
                        <i class="bi bi-journal-plus me-2"></i>Agregar nombre del pago
                     </button>
                </div>

                <!-- Contenedor Oculto para Nombre del Pago -->
                <div id="containerDetailPayment" class="col-12 d-none animation-fade-in mt-2">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body p-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1 ms-1">
                                <i class="bi bi-pen me-1"></i>Nombre del Pago
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-primary">
                                    <i class="bi bi-bookmark-fill"></i>
                                </span>
                                <input id="swalDetailPayment" type="text" class="form-control" placeholder="Escribe aquí el nombre del pago...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
  }
  /**
   * Metodo para obtener los metodos de pago para el swal
   * @returns
   */
  async function getPaymentMethods() {
    //obtenemmos el select de metodo de pago
    const swalMethodPaymentSelect = document.getElementById(
      "swalMethodPaymentSelect"
    );
    if (!swalMethodPaymentSelect) return;
    //limpiamos el select
    swalMethodPaymentSelect.innerHTML = "";
    const enpointMethodPayment = `${base_url}/pos/Credits/getPaymentMethods`;
    //mostramos en el select un mensaje preparanado metodos de pagos
    swalMethodPaymentSelect.innerHTML = `
        <option value="">Preparando metodos de pagos...</option>
    `;
    //agregamos un loading al select
    try {
      const response = await fetch(enpointMethodPayment);
      const data = await response.json();
      if (!data.status) {
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, data.timer);
        }
        return;
      }
      //limpiamos el select
      swalMethodPaymentSelect.innerHTML = "";
      data.paymentMethods.forEach((paymentMethod) => {
        swalMethodPaymentSelect.innerHTML += `
                    <option value="${paymentMethod.id}">${paymentMethod.name}</option>
                  `;
      });
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: `Error al obtener la informacion de los metodos de pagos - ${error.message}`,
        icon: "error",
      });
    } finally {
      //obtenemos el tipo de metodo de pago
      typeMethodPayment(swalMethodPaymentSelect.value);
      //agregamos el evento para obtener el tipo de metodo de pago
      swalMethodPaymentSelect.addEventListener("change", () => {
        typeMethodPayment(swalMethodPaymentSelect.value);
      });
    }
  }
  /**
   * Metodo para obtener el tipo de metodo de pago
   * @returns
   */
  function typeMethodPayment(methodPayment) {
    const swalReceivesContainer = document.getElementById(
      "swalReceivesContainer"
    );
    if (!swalReceivesContainer) return;

    swalReceivesContainer.innerHTML = "";
    if (methodPayment == 1) {
      swalReceivesContainer.innerHTML = `
                  <div class="col-12">
                      <label class="form-label small fw-bold text-primary text-uppercase mb-1">
                          <i class="bi bi-cash-stack me-1"></i>2. Efectivo Recibido
                      </label>
                      <div class="input-group">
                          <span class="input-group-text bg-white border text-muted fw-bold">${getcurrency}</span>
                          <input id="swalCashReceived" type="number" step="0.01" min="${totalIndividualCredit}" max="99999999.99" class="form-control" placeholder="0.00">
                      </div>
                  </div>
                  <!-- C. Vuelto -->
                  <div class="col-12 mt-3">
                      <div class="p-3 rounded-3 border text-center position-relative bg-light bg-opacity-10" style="transition: all 0.3s ease-in-out;" id="vuelto-container">
                          <span class="position-absolute top-0 start-50 translate-middle badge bg-secondary rounded-pill px-3 border border-white shadow-sm">3. VUELTO</span>
                          <div id="swalVueltoDisplay" class="fs-2 fw-bold text-muted mt-2">
                              ${getcurrency} 0.00
                          </div>
                          <small class="text-muted d-block pb-1" id="vuelto-label">Esperando ingreso...</small>
                      </div>
                  </div>
      `;
      //esperamos que se renderice
      const swalCashReceived = document.getElementById("swalCashReceived");
      if (swalCashReceived) {
        swalCashReceived.addEventListener("input", calculateReturn);
      }
    } else {
      //mostramos un mensaje en html que no es necesario hacer el calculo
      swalReceivesContainer.innerHTML = `
                  <div class="col-12">
                      <div class="alert alert-info text-center" role="alert">
                          <i class="bi bi-info-circle-fill me-2"></i>
                          No es necesario hacer el calculo de vuelto para este metodo de pago.
                      </div>
                  </div>
      `;
    }
  }
  /**
   * Metodo para calcular el vuelto
   * @returns
   */
  function calculateReturn() {
    const swalCashReceived = document.getElementById("swalCashReceived");
    const swalVueltoDisplay = document.getElementById("swalVueltoDisplay");
    const vueltoContainer = document.getElementById("vuelto-container");
    const vueltoLabel = document.getElementById("vuelto-label");

    if (
      !swalCashReceived ||
      !swalVueltoDisplay ||
      !vueltoContainer ||
      !vueltoLabel
    )
      return;
    const cashReceived = swalCashReceived.value;
    const vueltoString = (cashReceived - totalIndividualCredit).toFixed(2);
    const vuelto = parseFloat(vueltoString);
    swalVueltoDisplay.textContent = `${getcurrency} ${vuelto}`;
    if (cashReceived <= 0) {
      vueltoContainer.classList.remove("bg-light", "bg-danger", "bg-warning");
      vueltoContainer.classList.add("bg-light");
      swalVueltoDisplay.textContent = `${getcurrency} 0.00`;
      vueltoLabel.textContent = "Esperando ingreso...";
      return;
    }
    if (vuelto > 0) {
      vueltoContainer.classList.remove("bg-light", "bg-danger", "bg-warning");
      vueltoContainer.classList.add("bg-success");
      vueltoLabel.textContent = "Vuelto";
    } else if (vuelto === 0) {
      vueltoContainer.classList.remove("bg-light", "bg-danger", "bg-success");
      vueltoContainer.classList.add("bg-warning");
      vueltoLabel.textContent = "Vuelto exacto";
    } else {
      vueltoContainer.classList.remove("bg-light", "bg-success", "bg-warning");
      vueltoContainer.classList.add("bg-danger");
      vueltoLabel.textContent = "Falta recibir";
    }
  }
  /**
   * Metodo que se encarga de mostrar la interaccion con el desglose del nombre del pago
   * @returns {void}
   */
  function desglosePaymentVoucherName() {
    const btnToggleDetail = document.getElementById("btnToggleDetail");
    const containerDetailPayment = document.getElementById(
      "containerDetailPayment"
    );
    const swalDetailPayment = document.getElementById("swalDetailPayment");
    if (btnToggleDetail && containerDetailPayment) {
      btnToggleDetail.addEventListener("click", () => {
        //limpiamos el input
        swalDetailPayment.value = "";
        containerDetailPayment.classList.toggle("d-none");
        if (containerDetailPayment.classList.contains("d-none")) {
          btnToggleDetail.innerHTML =
            '<i class="bi bi-journal-plus me-2"></i>Agregar nombre del pago';
          btnToggleDetail.classList.remove(
            "text-danger",
            "border-danger",
            "bg-danger"
          );
          btnToggleDetail.classList.add("text-secondary", "border-secondary");
        } else {
          btnToggleDetail.innerHTML =
            '<i class="bi bi-x-circle me-2"></i>Cancelar nombre del pago';
          btnToggleDetail.classList.remove(
            "text-secondary",
            "border-secondary"
          );
          btnToggleDetail.classList.add(
            "text-danger",
            "border-danger",
            "bg-danger",
            "bg-opacity-10"
          );
          // Enfocar el input cuando se muestra
          setTimeout(() => {
            const inputDetail = document.getElementById("swalDetailPayment");
            if (inputDetail) inputDetail.focus();
          }, 100);
        }
      });
    }
  }
  /**
   * Metodo que se encarga de enviar la informacion al back para realizar el
   * pago indvidual, esto es la funcion del preconfirm
   * @returns {void}
   */
  async function sendPaymentIndividualCredit() {
    const formdata = new FormData();
    formdata.append("idvoucher", idVoucher);
    const swalMethodPaymentSelect =
      document.getElementById("swalMethodPaymentSelect") ?? 0;
    const swalCashReceived = document.getElementById("swalCashReceived") ?? 0;
    const swalDetailPayment =
      document.getElementById("swalDetailPayment") ?? "";
    //mostramos una alerta que el metodo de pago no a sid seleccionado
    if (swalMethodPaymentSelect.value == 0) {
      return Swal.showValidationMessage(
        "Debe seleccionar un metodo de pago para continuar"
      );
    }
    formdata.append("paymentMethod", swalMethodPaymentSelect.value ?? 0);
    //pasamos el vuelto
    formdata.append("amountReceived", swalCashReceived.value ?? 0);
    formdata.append("detailPayment", swalDetailPayment.value ?? "");
    const config = {
      method: "POST",
      body: formdata,
    };
    const enpointPayment = `${base_url}/pos/Credits/setPaymentCreditIndividually`;
    Swal.showLoading();
    try {
      const response = await fetch(enpointPayment, config);
      const data = await response.json();
      return data;
    } catch (error) {
      return {
        status: false,
        title: "Ocurrio un error inesperado",
        message: `Error al obtener la informacion del credito - ${error.message}`,
        icon: "error",
      };
    }
  }
})();
