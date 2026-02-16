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
        btnActions = `<button class="btn btn-sm btn-dark px-3 rounded-pill shadow-sm">Pagar</button>`;
      } else {
        btnActions = `<button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></button>`;
      }
      row.innerHTML = `
      <td class="${dateClass}">${sale.date}</td>
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
})();
