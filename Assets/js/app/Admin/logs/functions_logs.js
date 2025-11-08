let table;
let logTrendChart;
window.addEventListener("load", () => {
  initializeLogAnalytics();
  loadTable();
  setTimeout(() => {
    loadReport();
    filterTable();
    clearFilters();
  }, 500);
});
/**
 * Inicializa la sección analítica de logs cargando los años disponibles y los datos del año por defecto.
 *
 * @returns {void}
 */
function initializeLogAnalytics() {
  const yearSelect = document.getElementById("logs-year-filter");
  if (!yearSelect) {
    return;
  }

  loadAvailableYears()
    .then((years) => {
      populateYearOptions(yearSelect, years);
      const defaultYear = parseInt(yearSelect.value, 10) || new Date().getFullYear();
      yearSelect.addEventListener("change", () => {
        const selectedYear = parseInt(yearSelect.value, 10);
        loadLogSummary(Number.isNaN(selectedYear) ? new Date().getFullYear() : selectedYear);
      });
      loadLogSummary(defaultYear);
    })
    .catch((error) => {
      console.error("Error al inicializar el resumen de logs:", error);
      if (typeof toastr !== "undefined") {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "No se pudo cargar la información estadística de los logs.",
          "Ocurrió un error inesperado"
        );
      }
      renderTrendChart({ labels: [], series: [] });
    });
}
/**
 * Obtiene de la API los años disponibles para el filtrado de la gráfica.
 *
 * @returns {Promise<number[]>} Promesa con el listado de años disponibles.
 */
function loadAvailableYears() {
  return fetch(`${base_url}/Logs/getLogYears`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (!Array.isArray(data)) {
        return [new Date().getFullYear()];
      }
      const sanitized = data
        .map((year) => parseInt(year, 10))
        .filter((year) => !Number.isNaN(year));
      return sanitized.length > 0 ? sanitized : [new Date().getFullYear()];
    });
}
/**
 * Rellena el elemento select con los años disponibles obtenidos del servidor.
 *
 * @param {HTMLSelectElement} select Elemento select que se actualizará.
 * @param {number[]} years Colección de años disponibles.
 * @returns {void}
 */
function populateYearOptions(select, years) {
  const uniqueYears = [...new Set(years)].sort((a, b) => b - a);
  select.innerHTML = "";
  uniqueYears.forEach((year) => {
    const option = document.createElement("option");
    option.value = year;
    option.textContent = year;
    select.appendChild(option);
  });
  const defaultYear = uniqueYears[0] || new Date().getFullYear();
  select.value = defaultYear;
}
/**
 * Solicita el resumen estadístico del año seleccionado y actualiza la interfaz.
 *
 * @param {number} year Año del cual se desea obtener la información.
 * @returns {Promise<void>} Promesa que concluye cuando se procesa la respuesta.
 */
function loadLogSummary(year) {
  const selectedYear = Number.isNaN(year) ? new Date().getFullYear() : year;
  const chartContainer = document.getElementById("logsTrendChart");
  const emptyMessage = document.getElementById("logsTrendEmpty");
  elementLoader?.classList.remove("d-none");
  return fetch(`${base_url}/Logs/getLogSummary?year=${selectedYear}`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      const totals = Array.isArray(data?.totals) ? data.totals : [];
      updateSummaryCards(totals);
      renderTrendChart(data?.monthly || { labels: [], series: [] });
      if (emptyMessage) {
        emptyMessage.textContent = "No existen registros para el año seleccionado.";
      }
    })
    .catch((error) => {
      console.error("Error al cargar el resumen estadístico de logs:", error);
      if (typeof toastr !== "undefined") {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "No se pudo cargar el resumen estadístico de los logs.",
          "Ocurrió un error inesperado"
        );
      }
      if (chartContainer) {
        chartContainer.classList.add("d-none");
      }
      if (emptyMessage) {
        emptyMessage.classList.remove("d-none");
        emptyMessage.textContent = "No fue posible cargar la información del gráfico.";
      }
    })
    .finally(() => {
      elementLoader?.classList.add("d-none");
    });
}
/**
 * Actualiza los contadores del resumen por tipo de log en la interfaz.
 *
 * @param {Array<{idTypeLog: string|number, total: string|number}>} totals Totales agrupados por tipo.
 * @returns {void}
 */
function updateSummaryCards(totals) {
  const counters = document.querySelectorAll(".log-summary-total");
  counters.forEach((counter) => {
    const typeId = parseInt(counter.getAttribute("data-type"), 10);
    const match = totals.find((item) => parseInt(item.idTypeLog, 10) === typeId);
    const total = match ? Number(match.total) || 0 : 0;
    counter.textContent = total.toLocaleString("es-PE");
  });
}
/**
 * Renderiza la gráfica de tendencia mensual utilizando Chart.js si la librería está disponible.
 *
 * @param {{labels: string[], series: Array<{id: number, name: string, data: number[]}>}} monthly Información mensual a graficar.
 * @returns {void}
 */
function renderTrendChart(monthly) {
  const canvas = document.getElementById("logsTrendChart");
  const emptyMessage = document.getElementById("logsTrendEmpty");
  if (!canvas) {
    return;
  }

  const labels = Array.isArray(monthly?.labels) ? monthly.labels : [];
  const series = Array.isArray(monthly?.series) ? monthly.series : [];

  if (typeof Chart === "undefined") {
    console.error("Chart.js no está disponible en el contexto actual.");
    canvas.classList.add("d-none");
    if (emptyMessage) {
      emptyMessage.classList.remove("d-none");
      emptyMessage.textContent =
        "No fue posible renderizar la gráfica porque la librería Chart.js no está disponible.";
    }
    return;
  }

  if (logTrendChart) {
    logTrendChart.destroy();
    logTrendChart = null;
  }

  const hasData = series.some((dataset) =>
    Array.isArray(dataset.data) && dataset.data.some((value) => Number(value) > 0)
  );

  if (!hasData) {
    canvas.classList.add("d-none");
    if (emptyMessage) {
      emptyMessage.classList.remove("d-none");
      emptyMessage.textContent = "No existen registros para el año seleccionado.";
    }
    return;
  }

  canvas.classList.remove("d-none");
  if (emptyMessage) {
    emptyMessage.classList.add("d-none");
  }

  const ctx = canvas.getContext("2d");

  const datasets = series.map((dataset) => {
    const palette = getLogTypePalette(dataset.id);
    const values = Array.isArray(dataset.data)
      ? dataset.data.map((value) => Number(value) || 0)
      : [];
    return {
      label: dataset.name,
      data: values,
      fillColor: palette.background,
      strokeColor: palette.border,
      pointColor: palette.border,
      pointStrokeColor: "#fff",
      pointHighlightFill: "#fff",
      pointHighlightStroke: palette.border,
      bezierCurve: true,
      bezierCurveTension: 0.3,
    };
  });

  const chartData = {
    labels,
    datasets,
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    datasetStrokeWidth: 3,
    pointDotRadius: 4,
    pointHitDetectionRadius: 18,
    scaleGridLineColor: "rgba(0, 0, 0, 0.05)",
    scaleFontColor: "#495057",
    multiTooltipTemplate: "<%= datasetLabel %>: <%= value %>",
  };

  logTrendChart = new Chart(ctx).Line(chartData, chartOptions);
}
/**
 * Devuelve la paleta de colores asociada al tipo de log.
 *
 * @param {number} typeId Identificador del tipo de log.
 * @returns {{background: string, border: string}} Colores a utilizar para el dataset.
 */
function getLogTypePalette(typeId) {
  const palettes = {
    1: {
      background: "rgba(220, 53, 69, 0.15)",
      border: "rgba(220, 53, 69, 0.85)",
    },
    2: {
      background: "rgba(40, 167, 69, 0.15)",
      border: "rgba(40, 167, 69, 0.85)",
    },
    3: {
      background: "rgba(23, 162, 184, 0.15)",
      border: "rgba(23, 162, 184, 0.85)",
    },
  };
  return palettes[typeId] || {
    background: "rgba(0, 123, 255, 0.12)",
    border: "rgba(0, 123, 255, 0.85)",
  };
}
//Funcion que se encarga de listar la tabla
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Logs/getLogs",
      data: function (d) {
        // Se agrega el parámetro del filtro al objeto que se envía al servidor
        d.filterType = $("#filter-type").val();
        d.minData = $("#min-datetime").val();
        d.maxData = $("#max-datetime").val();
      },
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "l_title" },
      { data: "tl_name" },
      { data: "u_fullname" },
      { data: "l_registrationDate" }, // Fecha con hora
      { data: "actions" },
    ],
    dom: "lBfrtip",
    buttons: [
      {
        extend: "copyHtml5",
        text: "<i class='fa fa-copy'></i> Copiar",
        titleAttr: "Copiar",
        className: "btn btn-secondary",
        exportOptions: {
          columns: [1, 2, 3, 4],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de logs en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de logs en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de logs en PDF",
        className: "btn btn-danger",
        orientation: "vertical",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3, 4],
        },
      },
    ],
    columnDefs: [
      {
        targets: [0, 5],
        className: "text-center",
        searchable: false,
      },
      {
        targets: [1, 3, 4],
        className: "text-left",
      },
      {
        targets: [2],
        className: "text-center",
        //hacemos que camvie el color del texto dependiendo del tipo de log con badge
        render: function (data, type, row) {
          if (row.tl_name == "Error") {
            return `<span class="badge badge-danger">${data}</span>`;
          } else if (row.tl_name == "Correcto") {
            return `<span class="badge badge-success">${data}</span>`;
          } else if (row.tl_name == "Información") {
            return `<span class="badge badge-info">${data}</span>`;
          } else {
            return data;
          }
        },
      },
    ],
    responsive: true,
    bProcessing: true,
    bDestroy: true,
    iDisplayLength: 100,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/Spanish-datatables.json",
    },
    //hacemos que se recarguen funciones externas al modificar cualquier dato de la tabla o accion
    fnDrawCallback: function () {
      loadReport();
    },
  });
}
//Funcion que carga los datos en el reporte del modal del usuario
function loadReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      e.preventDefault();
      //creamos las constantes que capturar los datos de los atributos del boton
      const idLog = item.getAttribute("data-id");
      const title = item.getAttribute("data-title");
      const description = item.getAttribute("data-description");
      const registrationDate = item.getAttribute("data-registrationdate");
      const updateDate = item.getAttribute("data-updatedate");
      const type = item.getAttribute("data-type");
      const fullname = item.getAttribute("data-fullname");
      const email = item.getAttribute("data-email");
      const user = item.getAttribute("data-user");
      //obtene los elementos del modal donde se cargaran los datos
      const reportTitle = document.getElementById("reportTitle");
      const reportCode = document.getElementById("reportCode");
      const reportType = document.getElementById("reportType");
      const reportDescription = document.getElementById("reportDescription");
      const reportFullname = document.getElementById("reportFullname");
      const reportUser = document.getElementById("reportUser");
      const reportEmail = document.getElementById("reportEmail");
      const reportRegistrationDate = document.getElementById(
        "reportRegistrationDate"
      );
      const reportUpdateDate = document.getElementById("reportUpdateDate");
      //asignamos los valores a los elementos del modal
      reportTitle.innerHTML = title;
      reportCode.innerHTML = "#" + idLog;
      reportType.innerHTML = type;
      reportDescription.innerHTML = description
        .replaceAll("|", '"')
        .replaceAll("¬", "'");
      reportFullname.innerHTML = fullname;
      reportUser.innerHTML = user;
      reportEmail.innerHTML = email;
      reportRegistrationDate.innerHTML = registrationDate;
      reportUpdateDate.innerHTML = updateDate;
      setTimeout(() => {
        //add el d-none del elementLoader
        elementLoader.classList.add("d-none");
      }, 500);
      //abrimos el modal
      $("#modalReport").modal("show");
    });
  });
}
//Function que filtra los datos de la tabla
function filterTable() {
  const filterBtn = document.getElementById("filter-btn");
  filterBtn.addEventListener("click", () => {
    //obtenemos los valores de los inputs de la fechas
    const minDate = document.getElementById("min-datetime").value;
    const maxDate = document.getElementById("max-datetime").value;
    //validamos los campos vacios
    if (minDate == "" || maxDate == "") {
      toastr.options = {
        closeButton: true,
        timeOut: 0,
        onclick: null,
      };
      toastr["error"](
        "Debe llenar los campos de fecha",
        "Ocurrio un error inesperado"
      );
      return false;
    }
    //validamos que la fecha maxima sea mayor a la fecha minima
    if (minDate > maxDate) {
      toastr.options = {
        closeButton: true,
        timeOut: 0,
        onclick: null,
      };
      toastr["error"](
        "La fecha minima no debe ser mayor que la fecha maxima",
        "Ocurrio un error inesperado"
      );
      return false;
    }
    table.ajax.reload();
  });
}
//Funcion que limpiar los campos de los filtros
function clearFilters() {
  const clearBtn = document.getElementById("reset-btn");
  clearBtn.addEventListener("click", () => {
    document.getElementById("min-datetime").value = "";
    document.getElementById("max-datetime").value = "";
    document.getElementById("filter-type").value = "0";
    table.ajax.reload();
    toastr.options = {
      closeButton: true,
      timeOut: 0,
      onclick: null,
    };
    toastr["success"]("Filtros limpiados correctamente", "Filtros limpiados");
  });
}
