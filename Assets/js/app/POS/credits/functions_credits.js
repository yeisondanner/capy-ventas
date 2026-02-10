(() => {
  "use strict";
  const filterSearch = document.getElementById("filter-search");
  const filterDateStart = document.getElementById("filter-date-start");
  const filterDateEnd = document.getElementById("filter-date-end");
  const filterBtn = document.getElementById("filter-btn");
  const resetBtn = document.getElementById("reset-btn");
  /**
   * Variable que almacena la tabla de creditos
   */
  let table;
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
              <button class="btn btn-sm btn-outline-secondary btn-report-credit" title="Ver detalles">
                <i class="bi bi-file-earmark-text"></i>
              </button>
            </div>`;
          },
        },
        { data: "fullname" },
        {
          data: "billing_date",
          render: (data, type, row) => {
            return `<span class="text-danger"><i class="bi bi-calendar"></i> ${data}</span>`;
          },
        },
        {
          data: "credit_limit",
          render: (data, type, row) => {
            return `${getcurrency} ${data}`;
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
      drawCallback: function () {},
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
        //depaso colocamos la fecha actual de inicio a la final para evitar problemas con la fecha minima
        filterDateEnd.value = filterDateStart.value;
        table.ajax.reload();
      });
    }
    /**
     * Evento que se ejecuta cuando el usuario cambia la fecha de fin
     */
    if (filterDateEnd) {
      filterDateEnd.addEventListener("input", () => {
        //si la fecha minima esta vacia lazanmos una alerta
        if (filterDateStart.value === "") {
          showAlert({
            title: "Ocurrio un error inesperado",
            message: "Debe seleccionar una fecha de inicio",
            icon: "error",
          });
          filterDateEnd.value = "";
          return;
        }
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
  }
})();
