(() => {
  "use strict";
  const filterSearch = document.getElementById("filter-search");
  const filterDateStart = document.getElementById("filter-date-start");
  const filterDateEnd = document.getElementById("filter-date-end");
  const filterBtn = document.getElementById("filter-btn");
  const resetBtn = document.getElementById("reset-btn");
  //elementos del modal de reporte
  const detailCustomerName = document.getElementById("detailCustomerName");
  const checkAllCredits = document.getElementById("checkAllCredits");
  const detailCustomerDocument = document.getElementById(
    "detailCustomerDocument",
  );
  const btnPaySelectedCredits = document.getElementById(
    "btn-pay-selected-credits",
  );
  const detailCustomerStatus = document.getElementById("detailCustomerStatus");
  const detailCustomerCode = document.getElementById("detailCustomerCode");
  const detailCustomerPhone = document.getElementById("detailCustomerPhone");
  const detailCustomerDirection = document.getElementById(
    "detailCustomerDirection",
  );
  const detailCustomerBillingDay = document.getElementById(
    "detailCustomerBillingDay",
  );
  const detailCustomerCreditLimitFinancing = document.getElementById(
    "detailCustomerCreditLimitFinancing",
  );
  const detailCustomerMonthlyInterest = document.getElementById(
    "detailCustomerMonthlyInterest",
  );
  const detailCustomerMonthlyInterestFinancing = document.getElementById(
    "detailCustomerMonthlyInterestFinancing",
  );
  const detailCustomerCreditLimit = document.getElementById(
    "detailCustomerCreditLimit",
  );
  const detailCustomerPercentConsu = document.getElementById(
    "detailCustomerPercentConsu",
  );
  const detailCustomerIndicadorPercent = document.getElementById(
    "detailCustomerIndicadorPercent",
  );
  const detailCustomerAmountDisp = document.getElementById(
    "detailCustomerAmountDisp",
  );
  const modalFilterDateStart = document.getElementById(
    "modal-filter-date-start",
  );
  const modalFilterDateEnd = document.getElementById("modal-filter-date-end");
  const modalFilterBtn = document.getElementById("modal-filter-btn");
  const modalFilterReset = document.getElementById("modal-filter-reset");
  const modalFilterSaleType = document.getElementById("modal-filter-sale-type");
  const modalFilterPaymentStatus = document.getElementById(
    "modal-filter-payment-status",
  );
  //elementos del modal de reporte de creditos
  const detailCustomerTotalPurchased = document.getElementById(
    "detailCustomerTotalPurchased",
  );
  const detailCustomerTotalPaid = document.getElementById(
    "detailCustomerTotalPaid",
  );
  const detailCustomerTotalDebt = document.getElementById(
    "detailCustomerTotalDebt",
  );
  //elemento del template de pago de creditos seleccionados
  const templatePaymentCreditSelected = document.getElementById(
    "template-payment-credit-selected",
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
          modalFilterPaymentStatus.value ?? "All",
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
          modalFilterPaymentStatus.value ?? "All",
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
          modalFilterPaymentStatus.value ?? "All",
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
          modalFilterPaymentStatus.value ?? "All",
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
          modalFilterPaymentStatus.value ?? "All",
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
          modalFilterPaymentStatus.value ?? "All",
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
            modalFilterPaymentStatus.value ?? "All",
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
    paymentStatus,
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
      "loading",
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
      btnReportItemCredit();
      checkAllCreditsAction();
      checkCreditSelected();
      paySelectedCredits();
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
    detailCustomerPercentConsu.textContent = `${data.customer.percent_consu}% Uso`;
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
      let inputCheck = "";
      if (sale.payment_status === "Pendiente") {
        btnActions = `<button class="btn btn-sm btn-dark shadow-sm btn-payment" data-id="${sale.idVoucherHeader}"><i class="bi bi-wallet"></i></button>`;
        inputCheck = `<input type="checkbox" name="" id="" class="form-check-input form-check-input-sm select-credit" 
                                              data-id="${sale.idVoucherHeader}" 
                                              data-amount="${sale.amount}" 
                                              data-month_overdue="${sale.month_overdue}" 
                                              data-amount_current_interest_rate="${sale.amount_current_interest_rate}" 
                                              data-amount_default_interest_rate="${sale.amount_default_interest_rate}" 
                                              data-payment_deadline="${sale.payment_deadline}"
                                              data-days_overdue="${sale.days_overdue}" 
                        >`;
      } else {
        btnActions = `<button class="btn btn-sm btn-light border btn-view" data-id="${sale.idVoucherHeader}"><i class="bi bi-file-earmark-text"></i></button>`;
        inputCheck = "";
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
      <td class="text-center">
          ${inputCheck}
      </td>
      <td class="text-end pe-4">${btnActions}</td>
      `;
      customerSalesBody.appendChild(row);
    });
  }
  /**
   * Recorremos los botones de pago y mostramos el modal de pago
   * Esto te permite pagar el credito individualmente
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
          "loading",
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
              await getPaymentMethods("swalMethodPaymentSelect");

              // Lógica para el botón de desglose
              desglosePaymentVoucherName();
              //obtenemmos el select de metodo de pago
              const swalMethodPaymentSelect = document.getElementById(
                "swalMethodPaymentSelect",
              );
              if (!swalMethodPaymentSelect) return;
              //obtenemos el tipo de metodo de pago
              typeMethodPayment(swalMethodPaymentSelect.value);
              //agregamos el evento para obtener el tipo de metodo de pago
              swalMethodPaymentSelect.addEventListener("change", () => {
                typeMethodPayment(swalMethodPaymentSelect.value);
              });
            },
            //preconfirmamos el envio de la informacion al back
            preConfirm: async (e) => {
              const methodPay =
                document.getElementById("swalMethodPaymentSelect") ?? 0;
              const amountCashReceived =
                document.getElementById("swalCashReceived") ?? 0;
              const detailPay =
                document.getElementById("swalDetailPayment") ?? "";
              return await sendPaymentIndividualCredit(
                idVoucher,
                methodPay.value,
                amountCashReceived.value,
                detailPay.value,
              );
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
                  modalFilterPaymentStatus.value ?? "All",
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
   * Recorremos los botones de ver voucher de creditos o pagos al contado
   * Aqui se genera el modal para mostrar el voucher
   */
  function btnReportItemCredit() {
    const btnReportCredit = document.querySelectorAll(".btn-view");
    btnReportCredit.forEach((btn) => {
      btn.addEventListener("click", async () => {
        const idVoucher = btn.dataset.id;

        showAlert(
          {
            title: "Cargando comprobante",
            message: "Por favor espere...",
            icon: "info",
          },
          "loading",
        );

        const formdata = new FormData();
        formdata.append("idVoucherHeader", idVoucher);

        try {
          const response = await fetch(`${base_url}/pos/Credits/getVoucher`, {
            method: "POST",
            body: formdata,
          });
          const res = await response.json();
          swal.close();

          if (!res.status) {
            showAlert({
              title: res.title,
              message: res.message || "No se pudo cargar el comprobante",
              icon: res.icon,
            });
            if (res.url) {
              setTimeout(() => {
                window.location.href = res.url;
              }, 1000);
            }
            return;
          }

          Swal.fire({
            target: document.getElementById("creditsReportModal"),
            html: renderHtmlVoucherPayment(res.header, res.details),
            showCancelButton: true,
            cancelButtonText: '<i class="bi bi-x-lg"></i> Cerrar',
            showConfirmButton: true,
            confirmButtonText: `<i class="bi bi-card-image"></i> Exportar PNG`,
            buttonsStyling: false,
            customClass: {
              confirmButton: "btn btn-outline-warning me-2",
              cancelButton: "btn btn-secondary",
            },
            preConfirm: () => {
              exportToPng(
                "voucherContainer",
                `Comprobante_Venta_CV-${String(res.header.id).padStart(8, "0")}.png`,
              );
              return false; // Evita que se cierre el modal
            },
          });
        } catch (error) {
          showAlert({
            title: "Ocurrio un error inesperado",
            message: `Error al cargar el comprobante - ${error.message}`,
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
   * Metodo para renderizar el html del comprobante de pago
   * @param {*} h Datos de la cabecera
   * @param {*} d Datos del detalle
   * @returns
   */
  function renderHtmlVoucherPayment(h, d) {
    const id_voucher = String(h.id).padStart(8, "0");
    const voucher_code = `CV-${id_voucher}`;

    let subtotal = 0;
    let detailsHtml = "";
    d.forEach((item) => {
      subtotal += Number(item.sales_price_product) * item.stock_product;
      detailsHtml += `
        <tr>
          <td>${item.stock_product}</td>
          <td>${item.name_product} (${item.unit_of_measurement})</td>
          <td class="text-end">${getcurrency} ${Number(item.sales_price_product)}</td>
          <td class="text-end">${getcurrency} ${Number(item.sales_price_product * item.stock_product).toFixed(2)}</td>
        </tr>
      `;
    });

    const descuento = (subtotal * Number(h.percentage_discount || 0)) / 100;

    return `
           <div id="voucherContainer" class="receipt-container report-card-movements p-4 border rounded shadow-sm bg-white text-start">
                    <!-- Header -->
                    <div class="row align-items-center mb-4 border-bottom pb-3">
                        <div class="col-3 text-center">
                            <img id="logo_voucher" src="${h.logo}" alt="Logo" class="img-fluid"
                                style="max-height: 80px; filter: grayscale(100%);">
                        </div>
                        <div class="col-9 text-end">
                            <h4 class="fw-bold text-uppercase mb-1" id="name_bussines">${h.name_bussines}</h4>
                            <p class="mb-0 text-muted small" id="direction_bussines">${h.direction_bussines}</p>
                            <p class="mb-0 text-muted small">RUC: <span id="document_bussines">${h.document_bussines}</span></p>
                        </div>
                    </div>

                    <!-- Title & Date -->
                    <div class="row mb-4">
                        <div class="col-12 text-center border border-dark">
                            <h5 class="fw-bold text-decoration-underline text-uppercase mb-0 py-1">Comprobante de Venta</h5>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-6 border-start border-top border-dark bg-light p-2">
                            <label class="small text-uppercase text-muted fw-bold">Codigo de Venta:</label>
                            <div class="fw-bold small" id="voucher_code">${voucher_code}</div>
                        </div>
                        <div class="col-3 border-top border-dark bg-light p-2">
                            <label class="small text-uppercase text-muted fw-bold">Estado:</label>
                            <div class="fw-bold small" id="voucher_state">${h.status}</div>
                        </div>
                        <div class="col-3 border-end border-top border-dark bg-light p-2">
                            <label class="small text-uppercase text-muted fw-bold">Tipo Venta:</label>
                            <div class="fw-bold small" id="voucher_type">${h.sale_type}</div>
                        </div>
                        <div class="col-6 border-start border-bottom border-dark bg-light p-2">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Emisión/pago:</label>
                            <div class="fw-bold small" id="date_time">${h.date_time}</div>
                        </div>
                        <div class="col-6 border-end border-bottom border-dark bg-light p-2">
                            <label class="small text-uppercase text-muted fw-bold">Fecha de Vencimiento:</label>
                            <div class="fw-bold small" id="voucher_expiration_date">${h.payment_deadline}</div>
                        </div>
                        <div class="col-12 text-end mt-2">
                            <label class="small text-uppercase text-muted fw-bold">Vendedor:</label>
                            <div class="fw-bold small" id="fullname">${h.fullname}</div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="small text-uppercase text-muted fw-bold">Cliente:</label>
                            <div class="border-bottom border-dark pb-1 fs-5" id="name_customer">${h.name_customer}</div>
                            <div class="small text-muted" id="direction_customer">${h.direction_customer}</div>
                        </div>
                    </div>

                    <!-- Product Details Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered border-dark table-sm mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 10%;">Cant.</th>
                                    <th style="width: 50%;">Descripción</th>
                                    <th style="width: 20%;">P. Unit</th>
                                    <th style="width: 20%;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVoucherDetails">
                                ${detailsHtml}
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="row justify-content-end">
                        <div class="col-8">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-end fw-bold small py-0">Subtotal:</td>
                                        <td class="text-end small py-0" style="width: 120px;"><span
                                                id="subtotal_amount">${getcurrency} ${subtotal.toFixed(2)}</span></td>
                                    </tr>
                                    <tr class="border-top border-dark">
                                        <td class="text-end fw-bold small py-0">Descuento (<span
                                                id="percentage_discount">${h.percentage_discount}</span>%):</td>
                                        <td class="text-end text-danger small py-0"><span id="discount_amount">${getcurrency} ${descuento.toFixed(2)}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0"><span id="tax_name">${h.tax_name}</span> (<span
                                                id="tax_percentage">${h.tax_percentage}</span>%):</td>
                                        <td class="text-end small py-0"><span id="tax_amount">${getcurrency} ${h.tax_amount}</span></td>
                                    </tr>
                                    <!--Inpuestos -->
                                    <tr class="border-top border-dark">
                                        <td class="text-end fw-bold small py-0" title="Impuesto de financiamiento">
                                            <span>Imp. Finac.</span> (<span id="input_finac_percentage">${h.current_interest_rate}</span>%):
                                        </td>
                                        <td class="text-end small py-0"><span id="input_finac_amount">${getcurrency} ${h.amount_current_interest_rate}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold small py-0" title="Impuesto por mora Mensual">
                                            <span>Imp. Mor. Mens.</span> (<span id="input_mora_percentage">${h.default_interest_rate}</span>%):
                                        </td>
                                        <td class="text-end small py-0"><span id="input_mora_amount">${getcurrency} ${h.amount_default_interest_rate}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-2 border border-2 border-dark rounded bg-light mt-2 text-end">
                                <label class="small text-uppercase text-muted fw-bold d-block">Total a Pagar</label>
                                <span class="fs-4 fw-bold text-dark" id="total_amount">S/ ${Number(h.amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    <!-- System Footer -->
                    <div class="row mt-4">
                        <div class="col-12 text-center d-flex align-items-center justify-content-center">
                            <img src="${base_url}/Assets/capysm.png" alt="Logo"
                                style="height: 20px; width: auto; margin-right: 5px; opacity: 0.8;">
                            <small class="text-muted fst-italic">Generado por Capy Ventas</small>
                        </div>
                    </div>

                </div>
    `;
  }

  /**
   * Metodo que se encarga de descargar el comprobante en formato PNG
   */
  const exportToPng = (elementId, filename) => {
    const originalElement = document.getElementById(elementId);
    if (!originalElement) return;

    // 1. Clonar el elemento
    const clone = originalElement.cloneNode(true);

    // 2. Estilizar el clon para que se muestre completo
    Object.assign(clone.style, {
      position: "fixed",
      top: "-9999px",
      left: "-9999px",
      width: originalElement.offsetWidth + "px", // Mismo ancho que el original
      height: "auto", // Altura automática para mostrar todo el contenido
      zIndex: "-1",
      overflow: "visible", // Asegurar que no haya scroll oculto
    });

    // 3. Insertar el clon en el documento
    document.body.appendChild(clone);

    // 4. Generar el canvas desde el clon
    html2canvas(clone, {
      scale: 2, // Mejor resolución
      useCORS: true,
      scrollY: -window.scrollY, // Ajuste para evitar desplazamiento
    })
      .then((canvas) => {
        const imgData = canvas.toDataURL("image/png");
        const link = document.createElement("a");
        link.href = imgData;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      })
      .catch((err) => {
        console.error("Error exporting PNG:", err);
      })
      .finally(() => {
        // 5. Eliminar el clon
        document.body.removeChild(clone);
      });
  };
  /**
   * Metodo para obtener los metodos de pago para el swal
   * @returns
   */
  async function getPaymentMethods(selectId) {
    //obtenemmos el select de metodo de pago
    const swalMethodPaymentSelect = document.getElementById(selectId);
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
    }
  }
  /**
   * Metodo para obtener el tipo de metodo de pago
   * @returns
   */
  function typeMethodPayment(methodPayment) {
    const swalReceivesContainer = document.getElementById(
      "swalReceivesContainer",
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
      "containerDetailPayment",
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
            "bg-danger",
          );
          btnToggleDetail.classList.add("text-secondary", "border-secondary");
        } else {
          btnToggleDetail.innerHTML =
            '<i class="bi bi-x-circle me-2"></i>Cancelar nombre del pago';
          btnToggleDetail.classList.remove(
            "text-secondary",
            "border-secondary",
          );
          btnToggleDetail.classList.add(
            "text-danger",
            "border-danger",
            "bg-danger",
            "bg-opacity-10",
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
   * @param {number} idvoucher - id del voucher
   * @param {number} methodPay - metodo de pago
   * @param {number} amountCashReceived - cantidad de dinero recibido
   * @param {string} detailPay - detalle del pago
   * @returns {void}
   */
  async function sendPaymentIndividualCredit(
    idvoucher,
    methodPay,
    amountCashReceived,
    detailPay,
  ) {
    const formdata = new FormData();
    formdata.append("idvoucher", idvoucher); //mostramos una alerta que el metodo de pago no a sid seleccionado
    if (methodPay == 0) {
      return Swal.showValidationMessage(
        "Debe seleccionar un metodo de pago para continuar",
      );
    }
    formdata.append("paymentMethod", methodPay);
    //pasamos el vuelto
    formdata.append("amountReceived", amountCashReceived);
    formdata.append("detailPayment", detailPay);
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
  /**
   * Metodo que se encarga de seleccionar todos los creditos
   */
  function checkAllCreditsAction() {
    checkAllCredits.addEventListener("input", () => {
      const checkboxes = document.querySelectorAll(".select-credit");
      let htmlBtnPaySelectedCredits = `<i
                        class="bi bi-wallet2 me-1"></i> Pagar
                    Todo`;
      //verificamos si el input esta en cehcked o no para poder determinar el comportamiento del boton de pagar
      if (checkAllCredits.checked) {
        btnPaySelectedCredits.disabled = false;
        btnPaySelectedCredits.style.cursor = "pointer";
        htmlBtnPaySelectedCredits = `<i
                        class="bi bi-wallet2 me-1"></i> Pagar
                    Todo`;
      } else {
        btnPaySelectedCredits.disabled = true;
        btnPaySelectedCredits.style.cursor = "not-allowed";
        htmlBtnPaySelectedCredits = `<i
                        class="bi bi-ban me-1"></i> No disponible`;
      }
      btnPaySelectedCredits.innerHTML = htmlBtnPaySelectedCredits;
      //validamos si existen creditos pendientes
      if (checkboxes.length > 0) {
        checkboxes.forEach((checkbox) => {
          checkbox.checked = checkAllCredits.checked;
        });
      } else {
        btnPaySelectedCredits.disabled = true;
        btnPaySelectedCredits.style.cursor = "not-allowed";
        showAlert({
          title: "Acción no valida",
          message:
            "No se encontraron creditos pendientes para realizar esta acción",
          icon: "info",
        });
      }
    });
  }
  /**
   * Metodo que se encarga de seleccionar los creditos individualmente
   */
  function checkCreditSelected() {
    const checkboxes = document.querySelectorAll(".select-credit");
    const btnPay = btnPaySelectedCredits; // Referencia global o usar getElementById
    const checkAll = checkAllCredits;

    checkboxes.forEach((check) => {
      check.addEventListener("change", () => {
        //Gestionar el estado del "Seleccionar todos"
        if (!check.checked && checkAll) {
          checkAll.checked = false;
        }

        //Obtener todos los marcados para calcular el total
        const checkedInputs = document.querySelectorAll(
          ".select-credit:checked",
        );
        let totalAmount = 0;
        //recorremos los checkboxes marcados para obtener el total
        checkedInputs.forEach((input) => {
          // Convertimos el string a número. Usamos 0 si el valor no es válido.
          totalAmount += parseFloat(input.dataset.amount) || 0;
        });

        //Formatear el monto (2 decimales) para no perder el formato de moneda
        const formattedTotal = totalAmount.toLocaleString("es-PE", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });

        //Actualizar el botón según el conteo de checkboxes marcados
        if (checkedInputs.length > 0) {
          btnPay.disabled = false;
          btnPay.style.cursor = "pointer";
          btnPay.innerHTML = `<i class="bi bi-wallet2 me-1"></i> Pagar Seleccionados (${getcurrency} ${formattedTotal})`;
        } else {
          btnPay.disabled = true;
          btnPay.style.cursor = "not-allowed";
          btnPay.innerHTML = `<i class="bi bi-ban me-1"></i> No disponible`;
        }
      });
    });
  }
  /**
   * Metodo que se encarga de pagar los creditos seleccionados
   */
  function paySelectedCredits() {
    btnPaySelectedCredits.addEventListener("click", () => {
      Swal.fire({
        target: document.getElementById("creditsReportModal"),
        html: renderHtmlPaymentSelectedCredits(),
        showCancelButton: true,
        cancelButtonText: "<i class='bi bi-x-lg'></i> Cancelar",
        showConfirmButton: true,
        confirmButtonText: "<i class='bi bi-wallet'></i> Pagar",
        didOpen: () => {
          renderHtmlTableDetailLiquidation();
          getPaymentMethods("selectMethodPaymentAll");
        },
        buttonsStyling: false,
        customClass: {
          popup: "w-50",
          confirmButton: "btn btn-primary me-2",
          cancelButton: "btn btn-secondary",
        },
      });
    });
  }
  /**
   * Metodo que se encarga de renderizar el html para el pago de los creditos seleccionados
   * @returns {string} - El html para el pago de los creditos seleccionados
   */
  function renderHtmlPaymentSelectedCredits() {
    return `
        <div class="text-start px-2">
              <div class="border-bottom mb-3 pb-3">
                  <div class="d-flex align-items-start">
                      <!-- Icono a la izquierda -->
                      <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill shadow-sm me-3">
                          <i class="bi bi-receipt-cutoff fs-3"></i>
                      </div>
                      
                      <!-- Títulos a la izquierda -->
                      <div class="d-flex flex-column align-items-start">
                          <h5 class="fw-bold mb-1 text-dark">Registrar Pago de Créditos</h5>
                          <!-- Badge sutil para el número de Voucher -->
                          <span class="text-muted" style="font-size: 0.8rem;">
                              <i class="bi bi-info-circle me-1"></i>Aquí se podra realizar el pago de los creditos seleccionados anteriormente en el modal de reportes de creditos
                          </span>
                      </div>
                  </div>
              </div>
              <!-- SECCIÓN 1: DETALLE DE DEUDA CON FINANCIAMIENTO -->
              <div class="mb-3">
                  <label class="form-label fw-bold small text-uppercase text-muted tracking-widest mb-2">
                      <i class="bi bi-receipt me-1"></i> Detalle de Liquidación
                  </label>
                  <div class="table-responsive border rounded-3 bg-white shadow-sm">
                      <table class="table table-sm table-hover mb-0 align-middle" style="font-size: 0.8rem;">
                          <thead class="bg-light border-bottom text-muted" style="font-size: 0.7rem;">
                              <tr title="Detalle de liquidación" class="table-info">
                                  <th class="ps-3 py-2">VENCIMIENTO</th>
                                  <th class="py-2 text-center">CAPITAL</th>
                                  <th class="py-2 text-center" >MORA (1%) MENSUAL</th>
                                  <th class="py-2 text-center">FINAN. (0%)</th>
                                  <th class="text-end pe-3 py-2">SUBTOTAL</th>
                              </tr>
                          </thead>
                          <tbody id="detailLiquidation">                              
                          </tbody>
                          <tfoot class="border-top bg-light">
                              <tr class="fw-bold text-dark">
                                  <td colspan="4" class="ps-3 py-2 text-uppercase" style="font-size: 0.7rem;">Monto Total a
                                      Cobrar</td>
                                  <td class="text-end pe-3 py-2 h5 mb-0 text-primary" id="totalAmount">${getcurrency} 16.00</td>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>

              <!-- SECCIÓN 2: FORMULARIO DE PAGO -->
              <div class="row g-3">
                  <div class="col-md-6">
                      <div class="mb-3">
                          <label class="form-label fw-bold small text-secondary">MÉTODO DE PAGO</label>
                          <div class="input-group input-group-sm">
                              <span class="input-group-text bg-white border-end-0" id="icon-metodo">
                                  <i class="bi bi-cash-coin text-primary"></i>
                              </span>
                              <select class="form-select ps-0 fw-bold" id="selectMethodPaymentAll">
                                  
                              </select>
                          </div>
                      </div>
                      <div class="mb-0 text-start">
                          <label class="form-label fw-bold small text-secondary">NOMBRE O DESCRIPCIÓN BREVE</label>
                          <input type="text" class="form-control form-control-sm bg-light" id="descriptionPaymentAll" placeholder="Opcional...">
                      </div>
                  </div>

                  <!-- CALCULADORA ESTÁNDAR -->
                  <div class="col-md-6">
                      <div class="p-2 border rounded bg-white shadow-sm">
                          <label class="form-label fw-bold" style="font-size: 0.65rem; color: #6c757d;">RECIBIDO
                              (OPCIONAL)</label>
                          <div class="input-group input-group-sm mb-2">
                              <span class="input-group-text bg-light border-end-0">S/</span>
                              <input type="number" class="form-control ps-1 fw-bold" id="recibidoPaymentAll"
                                  placeholder="0.00">
                              <button class="btn btn-outline-secondary" type="button" onclick="setMontoExacto(16.00)">
                                  <i class="bi bi-check2"></i>
                              </button>
                          </div>
                          <div class="d-flex justify-content-between align-items-center pt-1 border-top">
                              <span class="fw-bold text-muted" style="font-size: 0.7rem;">VUELTO:</span>
                              <div class="d-flex align-items-center">
                                  <span id="vueltoBadgeSwal" class="badge bg-light text-secondary me-2"
                                      style="font-size: 0.6rem;">INFO</span>
                                  <span class="fw-bold h5 mb-0 text-dark" id="vueltoTextSwal">S/ 0.00</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- SECCIÓN 3: IMPACTO EN CUENTA -->
              <div class="mt-3 pt-3 border-top">
                  <div class="row align-items-center">
                      <div class="col-7">
                          <div class="small text-muted mb-1" style="font-size: 0.7rem;">Línea de crédito post-pago:</div>
                          <div class="fw-bold" style="font-size: 0.85rem;">
                              <span class="text-secondary">S/ 27.70</span>
                              <i class="bi bi-arrow-right mx-1 text-primary"></i>
                              <span class="text-success">S/ 11.70</span>
                          </div>
                      </div>
                      <div class="col-5 text-end">
                          <div class="form-check form-switch d-inline-block">
                              <input class="form-check-input" type="checkbox" id="printTicket" checked>
                              <label class="form-check-label small fw-bold text-secondary" for="printTicket"
                                  style="font-size: 0.7rem;">Ticket</label>
                          </div>
                      </div>
                  </div>
              </div>
          </div>`;
  }
  /**
   * Renderiza la tabla de detalle de liquidación
   */
  function renderHtmlTableDetailLiquidation() {
    const detailContainer = document.getElementById("detailLiquidation");
    const totalDisplay = document.getElementById("totalAmount");
    const checkedElements = document.querySelectorAll(".select-credit:checked");

    // Configuramos un formateador de moneda una sola vez
    const formatter = new Intl.NumberFormat("es-PE", {
      style: "currency",
      currency: "PEN", // Cambia a tu moneda local o usa la variable getcurrency
    });

    let total = 0;

    const htmlRows = Array.from(checkedElements)
      .map((element) => {
        // 1. Desestructuración y conversión inmediata
        const {
          month_overdue,
          amount_default_interest_rate: defInt,
          amount_current_interest_rate: curInt,
          amount,
          days_overdue,
          payment_deadline,
        } = element.dataset;

        const nMonthOverdue = parseFloat(month_overdue) || 0;
        const nAmount = parseFloat(amount) || 0;
        const nCurInt = parseFloat(curInt) || 0;
        const nDefInt = parseFloat(defInt) || 0;

        // 2. Cálculos
        const amountMoraMes = nMonthOverdue * nDefInt;
        const capital = nAmount - (amountMoraMes + nCurInt);
        total += nAmount;

        // 3. Lógica de Estilos (Clases y Badges)
        const isOverdue = parseInt(days_overdue) < 0;
        const classDate = isOverdue
          ? "ps-3 py-2 border-start border-danger border-4 fw-bold text-danger"
          : "ps-3 py-2 fw-bold text-dark";

        const badgeOverdue =
          isOverdue && nMonthOverdue > 0
            ? `<br><span class="badge bg-danger small">${nMonthOverdue} mes${nMonthOverdue > 1 ? "es" : ""} vencido</span>`
            : "";

        // 4. Retornamos el string de la fila
        return `
      <tr>
        <td class="${classDate}">${payment_deadline} ${badgeOverdue}</td>
        <td class="text-secondary text-center">${formatter.format(capital)}</td>
        <td class="text-muted text-center">${formatter.format(amountMoraMes)}</td>
        <td class="text-muted text-center">${formatter.format(nCurInt)}</td>
        <td class="text-end pe-3 fw-bold">${formatter.format(nAmount)}</td>
      </tr>`;
      })
      .join("");

    // 5. Una sola inserción al DOM (más rápido)
    detailContainer.innerHTML = htmlRows;
    totalDisplay.textContent = formatter.format(total);
  }
})();
