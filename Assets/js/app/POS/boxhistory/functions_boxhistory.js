import ReadBox from "./read_box.js";
(() => {
  "use strict";
  //obtenemos los elementos del DOM
  const dateContainer = document.getElementById("date-container") ?? null;
  const dateRangeContainer =
    document.getElementById("date-range-container") ?? null;
  const dateToContainer = document.getElementById("date-to-container") ?? null;
  const dateLabel = document.getElementById("date-label") ?? null;
  const filterType = document.getElementById("filter-type") ?? null;
  const minDate = document.getElementById("min-date") ?? null;
  const maxDate = document.getElementById("max-date") ?? null;
  const filterDate = document.getElementById("filter-date") ?? null;
  const resetBtn = document.getElementById("reset-btn") ?? null;
  const filterBtn = document.getElementById("filter-btn") ?? null;
  //creamos un objeto de la clase ReadBox
  const readBox = new ReadBox();
  /**
   * Inicializamos todos los eventos despues de cargard todo el DOM
   */
  document.addEventListener("DOMContentLoaded", () => {
    readBox.loadTable();
    //inicializamos la funcion toggleFilters
    toggleFilters();
    //inicializamos la funcion resetFilters
    resetFiltersBtn();
    //inicializamos la funcion inputEvents
    inputAndBtnEvents();
  });
  /**
   * Funcion que se encarga de mostras/ocultar los filtros de acuerdo al tipo de filtro seleccionado
   */
  function toggleFilters() {
    //validamos que existan los elementos necesarios
    if (
      !filterType ||
      !minDate ||
      !maxDate ||
      !filterDate ||
      !dateLabel ||
      !dateContainer ||
      !dateRangeContainer ||
      !dateToContainer
    )
      return;
    // Mostrar u ocultar campos de rango personalizado según selección y actualizar comportamiento del campo de fecha
    filterType.addEventListener("change", function () {
      //obtenemos el valor del filtro seleccionado
      const filterTypeValue = this.value;
      if (filterTypeValue === "custom") {
        dateRangeContainer.style.display = "block";
        dateToContainer.style.display = "block";
        dateContainer.style.display = "none";
        // Limpiar los campos de fecha cuando se cambia de rango personalizado a otro tipo
        minDate.value = "";
        maxDate.value = "";
      } else {
        dateRangeContainer.style.display = "none";
        dateToContainer.style.display = "none";
        dateContainer.style.display = "block";

        // Limpiar los campos de fecha personalizados
        minDate.value = "";
        maxDate.value = "";

        // Actualizar la etiqueta del campo de fecha según el tipo de filtro
        switch (filterTypeValue) {
          case "daily":
            dateLabel.textContent = "Fecha:";
            filterDate.type = "date";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("daily");
            break;
          case "weekly":
            dateLabel.textContent = "Semana:";
            filterDate.type = "week";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("weekly");
            break;
          case "monthly":
            dateLabel.textContent = "Mes:";
            filterDate.type = "month";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("monthly");
            break;
          case "yearly":
            dateLabel.textContent = "Año:";
            filterDate.type = "number";
            filterDate.min = "1970";
            filterDate.max = new Date().getFullYear() + 10;
            filterDate.step = "1";
            filterDate.value = setDefaultDateValue("yearly");
            break;
          case "all":
            dateContainer.style.display = "none";
            dateRangeContainer.style.display = "none";
            dateToContainer.style.display = "none";
            break;
        }
      }
      //ejecutamos la funcion loadTable
      readBox.loadTable();
    });
  }
  /**
   * MEtodo que se encarga de detectar el evento clic al boton de resetear los filtros
   */
  function resetFiltersBtn() {
    resetBtn.addEventListener("click", resetFilters);
  }
  /**
   * Metodo que se encarga de limpiar los filtros
   */
  function resetFilters() {
    minDate.value = "";
    maxDate.value = "";
    filterType.value = "daily";
    filterDate.type = "date";
    filterDate.min = null;
    filterDate.max = null;
    filterDate.step = null;
    filterDate.value = "daily";
    dateLabel.textContent = "Fecha:";
    dateRangeContainer.style.display = "none";
    dateToContainer.style.display = "none";
    dateContainer.style.display = "block";
    readBox.loadTable();
  }
  // Función para inicializar el campo de fecha con valores predeterminados según el tipo de filtro
  function setDefaultDateValue(filterType) {
    const now = new Date();
    // Para evitar problemas de zona horaria, usamos la fecha local en lugar de ISO
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const todayStr = `${year}-${month}-${day}`;

    switch (filterType) {
      case "daily":
        return todayStr;
      case "weekly":
        const weekNum = getWeekNumber(now);
        const weekYear = now.getFullYear();
        return `${weekYear}-W${weekNum.toString().padStart(2, "0")}`;
      case "monthly":
        return (
          now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0")
        );
      case "yearly":
        return now.getFullYear().toString();
      default:
        return todayStr;
    }
  }
  // Función para obtener el número de semana
  function getWeekNumber(d) {
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    var yearStart = new Date(Date.UTC(d.getFullYear(), 0, 1));
    var weekNo = Math.ceil(
      ((d - yearStart) / 86400000 + yearStart.getUTCDay() + 1) / 7,
    );
    return weekNo;
  }
  //Metodo que se encarga de de activarse de los eventos del input
  function inputAndBtnEvents() {
    filterDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    minDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    maxDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    filterBtn.addEventListener("click", function () {
      readBox.loadTable();
    });
  }

  // Variables globales para las instancias de las gráficas
  let financialChartInstance = null;
  let movementsChartInstance = null;

  /**
   * Función para cargar el reporte de la caja cerrada
   */
  function loadReport() {
    $("#table").on("click", ".report-item", function () {
      const idBoxSession = $(this).data("id");

      $.ajax({
        url: base_url + "/pos/Boxhistory/getBoxSession",
        type: "POST",
        dataType: "json",
        data: { idBoxSession: idBoxSession },
        success: function (res) {
          if (!res.status) {
            showAlert(
              {
                title: res.title,
                message: res.message || "No se pudo cargar el comprobante",
                icon: res.icon,
              },
              "float",
            );
            if (res.url) {
              setTimeout(() => {
                window.location.href = res.url;
              }, 1000);
            }
            return;
          }

          const d = res.data;

          // Llenar datos del modal
          $("#logo_business").attr("src", d.logo_url);
          $("#name_business").text(d.name_business);
          $("#direction_business").text(d.direction_business);
          $("#document_business").text(d.document_business);

          $("#box_name").text(d.box_name);
          $("#user_fullname").text(d.fullname);
          $("#opening_date").text(d.opening_date);
          $("#closing_date").text(d.closing_date);

          // Formatear montos
          $("#initial_amount").text(getcurrency + " " + d.initial_amount);
          $("#expected_amount").text(getcurrency + " " + d.expected_amount);
          $("#counted_amount").text(getcurrency + " " + d.counted_amount);

          // Diferencia con color
          const diff = parseFloat(d.difference);
          const diffText = getcurrency + " " + d.difference;
          const diffEl = $("#difference_amount");

          diffEl.text(diffText);
          diffEl.removeClass("text-success text-danger text-primary");

          if (diff > 0) {
            diffEl.addClass("text-success");
          } else if (diff < 0) {
            diffEl.addClass("text-danger");
          } else {
            diffEl.addClass("text-primary");
          }

          $("#session_notes").text(d.notes || "Sin notas.");

          // === GRAFICAS ===

          // 1. Gráfica Financiera (Bar)
          const ctxFinancial = document
            .getElementById("financialChart")
            .getContext("2d");
          if (financialChartInstance) {
            financialChartInstance.destroy();
          }

          financialChartInstance = new Chart(ctxFinancial, {
            type: "bar",
            data: {
              labels: ["Inicial", "Contado", "Esperado"],
              datasets: [
                {
                  label: "Montos",
                  data: [
                    parseFloat(d.initial_amount),
                    parseFloat(d.counted_amount),
                    parseFloat(d.expected_amount),
                  ],
                  backgroundColor: [
                    "rgba(108, 117, 125, 0.7)", // Secondary
                    "rgba(25, 135, 84, 0.7)", // Success
                    "rgba(13, 202, 240, 0.7)", // Info
                  ],
                  borderColor: [
                    "rgba(108, 117, 125, 1)",
                    "rgba(25, 135, 84, 1)",
                    "rgba(13, 202, 240, 1)",
                  ],
                  borderWidth: 1,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                title: { display: true, text: "Resumen Financiero" },
              },
              scales: {
                y: { beginAtZero: true },
              },
            },
          });

          // 2. Gráfica de Movimientos (Doughnut)
          const ctxMovements = document
            .getElementById("movementsChart")
            .getContext("2d");
          if (movementsChartInstance) {
            movementsChartInstance.destroy();
          }

          let totalIngresos = 0;
          let totalEgresos = 0;

          if (d.movements_history && d.movements_history.length > 0) {
            d.movements_history.forEach((m) => {
              const amt = parseFloat(m.amount);
              if (m.type_movement.toLowerCase().includes("ingreso")) {
                totalIngresos += amt;
              } else {
                totalEgresos += amt;
              }
            });

            document.getElementById("movementsChartContainer").style.display =
              "block";

            movementsChartInstance = new Chart(ctxMovements, {
              type: "doughnut",
              data: {
                labels: ["Ingresos", "Egresos"],
                datasets: [
                  {
                    data: [totalIngresos, totalEgresos],
                    backgroundColor: [
                      "rgba(25, 135, 84, 0.7)", // Success
                      "rgba(220, 53, 69, 0.7)", // Danger
                    ],
                    hoverOffset: 4,
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  title: { display: true, text: "Movimientos" },
                },
              },
            });
          } else {
            document.getElementById("movementsChartContainer").style.display =
              "none";
          }

          // Renderizar Historial de Arqueos (New UI)
          const countsContainer = $("#counts_history_container");
          countsContainer.empty();

          if (d.counts_history && d.counts_history.length > 0) {
            d.counts_history.forEach((count) => {
              const diffVal = parseFloat(count.difference);
              let diffColor = "text-secondary";
              let diffIcon = "bi-dash-circle";
              if (diffVal > 0) {
                diffColor = "text-success";
                diffIcon = "bi-plus-circle";
              }
              if (diffVal < 0) {
                diffColor = "text-danger";
                diffIcon = "bi-exclamation-circle";
              }

              // Details HTML (Badges)
              let detailsHtml = "";
              if (count.details && count.details.length > 0) {
                detailsHtml =
                  '<div class="mt-2 pt-2 border-top border-light small">';
                detailsHtml += '<div class="d-flex flex-wrap gap-2">';
                count.details.forEach((det) => {
                  detailsHtml += `<span class="badge bg-white text-dark border border-light shadow-sm fw-normal">
                          ${det.quantity} x ${det.label} <span class="text-muted ms-1">(${getcurrency} ${det.total})</span>
                       </span>`;
                });
                detailsHtml += "</div></div>";
              }

              const html = `
                  <div class="card border mb-0 shadow-sm">
                      <div class="card-body p-3">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                              <div class="d-flex align-items-center">
                                  <div class="rounded-circle bg-light p-2 me-2 text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                      <i class="bi bi-wallet2"></i>
                                  </div>
                                  <div>
                                      <h6 class="mb-0 fw-bold text-uppercase small">${count.type}</h6>
                                      <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>${count.date_time}</small>
                                  </div>
                              </div>
                              <div class="text-end">
                                  <span class="d-block fw-bold ${diffColor}"><i class="bi ${diffIcon} me-1"></i>${getcurrency} ${count.difference}</span>
                                  <small class="text-muted" style="font-size: 0.7rem;">Diferencia</small>
                              </div>
                          </div>
                          
                          <div class="d-flex justify-content-between small bg-light p-2 rounded">
                              <span>Contado: <strong>${getcurrency} ${count.counted_amount}</strong></span>
                              <span class="text-muted">|</span>
                              <span>Esperado: <strong>${getcurrency} ${count.expected_amount}</strong></span>
                          </div>

                          ${detailsHtml}
                      </div>
                  </div>
              `;
              countsContainer.append(html);
            });
          } else {
            countsContainer.append(
              '<div class="text-center text-muted small p-3">No hay historial disponible</div>',
            );
          }

          // Renderizar Movimientos (New UI)
          const moveContainer = $("#movements_history_container");
          const moveGeneralContainer = $("#movements_general_container"); // Elemento padre para ocultar
          moveContainer.empty();

          if (d.movements_history && d.movements_history.length > 0) {
            moveGeneralContainer.show();
            d.movements_history.forEach((mov) => {
              let icon = "bi-arrow-right-circle";
              let color = "text-primary";

              if (mov.type_movement.toLowerCase().includes("ingreso")) {
                icon = "bi-arrow-down-circle";
                color = "text-success";
              } else if (
                mov.type_movement.toLowerCase().includes("egreso") ||
                mov.type_movement.toLowerCase().includes("gasto")
              ) {
                icon = "bi-arrow-up-circle";
                color = "text-danger";
              }

              moveContainer.append(`
                <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm">
                     <div class="me-3 fs-4 ${color}">
                        <i class="bi ${icon}"></i>
                     </div>
                     <div class="flex-grow-1">
                        <h6 class="mb-0 small fw-bold">${mov.type_movement}</h6>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">${
                          mov.concept || "--"
                        }</small>
                     </div>
                     <div class="text-end">
                        <span class="fw-bold small ${color}">${getcurrency} ${
                          mov.amount
                        }</span>
                        <div class="text-muted" style="font-size: 0.7rem;">${
                          mov.created_at
                        }</div>
                     </div>
                </div>
              `);
            });
          } else {
            moveGeneralContainer.hide();
          }

          // Mostrar modal
          const modalEl = document.getElementById("boxSessionModal");
          const modalBox = bootstrap.Modal.getOrCreateInstance(modalEl);
          modalBox.show();
        },
        error: function () {
          alert("Error de comunicación con el servidor");
        },
      });
    });
  }

  /**
   * Función para descargar el reporte como PNG
   */
  function downloadPNG() {
    $("#download-png").click(() => {
      html2canvas(document.getElementById("voucherContainer"), {
        scale: 2,
        useCORS: true,
      })
        .then((canvas) => {
          const imgData = canvas.toDataURL("image/png");
          const link = document.createElement("a");
          link.href = imgData;
          link.download = "Reporte_Cierre_Caja.png";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error al exportar PNG:", error);
        });
    });
  }

  // Inicializar las nuevas funciones
  loadReport();
  downloadPNG();
})();
