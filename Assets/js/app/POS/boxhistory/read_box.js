export default class ReadBox {
  //encapsulamos los atributos de busqueda
  #filterType = document.getElementById("filter-type") ?? null;
  #minDate = document.getElementById("min-date") ?? null;
  #maxDate = document.getElementById("max-date") ?? null;
  #filterDate = document.getElementById("filter-date") ?? null;
  constructor() {
    this.table = $("#table");
  }
  /**
   * Metodo que se encarga de cargar la tabla
   */
  loadTable() {
    //obtenemos los valores de los filtros
    const filterType = this.#filterType.value;
    const minDate = this.#minDate.value;
    const maxDate = this.#maxDate.value;
    const filterDate = this.#filterDate.value;
    //cargamos la tabla
    this.table.DataTable({
      responsive: true,
      ajax: {
        url: `${base_url}/pos/Boxhistory/loadBoxHistory`,
        data: function (d) {
          d.filterType = filterType;
          d.minDate = minDate;
          d.maxDate = maxDate;
          d.filterDate = filterDate;
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
          data: null,
          className: "text-center",
          render: function (data, type, row) {
            return `<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <button type="button" title="Ver reporte" class="btn btn-sm btn-outline-info report-item" data-id="${row.idBoxSessions}"><i class="bi bi-file-text"></i></button>
                    </div>`;
          },
        },
        {
          data: "fullname",
          render: (data, type, row) => {
            return `<i class="bi bi-person-circle text-primary"></i> ${data}`;
          },
        },
        {
          data: "initial_amount",
          className: "text-end",
          render: (data, type, row) => {
            return `${getcurrency} ${data}`;
          },
        },
        {
          data: "expected_amount",
          className: "text-end",
          render: (data, type, row) => {
            return `${getcurrency} ${data}`;
          },
        },
        {
          data: "difference",
          className: "text-end",
          render: (data, type, row) => {
            //validamos si la diferencia es positiva o negativa le agregamos un icono
            if (row.difference > 0) {
              return `<span class="text-success" title="${row.notes}"><i class="bi bi-arrow-up"></i> ${getcurrency} ${data}</span>`;
            } else if (row.difference < 0) {
              return `<span class="text-danger" title="${row.notes}"><i class="bi bi-arrow-down"></i> ${getcurrency} ${data}</span>`;
            } else if (row.difference == 0) {
              return `<span class="text-primary" title="${row.notes}"><i class="bi bi-arrow-left-right"></i> ${getcurrency} ${data}</span>`;
            }
          },
        },
        {
          data: "closing_date",
          className: "text-center",
          render: (data, type, row) => {
            return `<span  class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Esta fecha es la fecha en que se cerró la caja"><i class="bi bi-info-circle text-primary"></i> ${data}</span>`;
          },
        },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary my-2",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success my-2",
          title: "Historial de cierre de cajas",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info my-2",
          title: "Historial de cierre de cajas",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger my-2",
          orientation: "portrait",
          pageSize: "A4",
          title: "Historial de cierre de cajas",
          exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
        },
      ],
      keyTable: true,
      processing: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
      drawCallBack: function () {},
    });
  }
  // Función para calcular las fechas de inicio y fin según el tipo de filtro y el valor del campo
  calculateDateRange(filterType, filterValue) {
    let minDate, maxDate;

    if (filterType === "custom") {
      // Para rango personalizado, se usan los campos separados (esto se manejará fuera de esta función)
      minDate = document.getElementById("min-date").value;
      maxDate = document.getElementById("max-date").value;
    } else {
      // Para otros tipos de filtro, usar el campo de fecha único
      switch (filterType) {
        case "daily":
          minDate = maxDate = filterValue || setDefaultDateValue("daily");
          break;
        case "weekly":
          // Convertir el valor de semana a fechas (formato YYYY-WXX)
          if (filterValue) {
            const [year, week] = filterValue.split("-W");
            const dates = getStartAndEndOfWeek(parseInt(year), parseInt(week));
            minDate = dates.start;
            maxDate = dates.end;
          } else {
            // Si no hay valor, usar semana actual
            const today = new Date();
            const weekNum = getWeekNumber(today);
            const dates = getStartAndEndOfWeek(today.getFullYear(), weekNum);
            minDate = dates.start;
            maxDate = dates.end;
          }
          break;
        case "monthly":
          if (filterValue) {
            // El formato es YYYY-MM
            const [year, month] = filterValue.split("-");
            const startDate = year + "-" + month + "-01";
            // Calcular último día del mes
            const endDate = new Date(year, month, 0).getDate();
            minDate = startDate;
            maxDate = year + "-" + month + "-" + endDate;
          } else {
            // Si no hay valor, usar mes actual
            const today = new Date();
            const startDate =
              today.getFullYear() +
              "-" +
              String(today.getMonth() + 1).padStart(2, "0") +
              "-01";
            const endDate = new Date(
              today.getFullYear(),
              today.getMonth() + 1,
              0,
            ).getDate();
            minDate = startDate;
            maxDate =
              today.getFullYear() +
              "-" +
              String(today.getMonth() + 1).padStart(2, "0") +
              "-" +
              endDate;
          }
          break;
        case "yearly":
          if (filterValue) {
            minDate = filterValue + "-01-01";
            maxDate = filterValue + "-12-31";
          } else {
            // Si no hay valor, usar año actual
            const year = new Date().getFullYear();
            minDate = year + "-01-01";
            maxDate = year + "-12-31";
          }
          break;
      }
    }

    return { minDate, maxDate };
  }
}
