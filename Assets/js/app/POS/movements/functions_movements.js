(() => {
  "use strict";
  let table;

  window.addEventListener("DOMContentLoaded", (e) => {
    loadTable();
    loadReport();
    dowloadPNG();

    // Mostrar u ocultar campos de rango personalizado según selección y actualizar comportamiento del campo de fecha
    document
      .getElementById("filter-type")
      .addEventListener("change", function () {
        const filterType = this.value;
        const dateContainer = document.getElementById("date-container");
        const dateRangeContainer = document.getElementById(
          "date-range-container"
        );
        const dateToContainer = document.getElementById("date-to-container");
        const dateLabel = document.getElementById("date-label");

        if (filterType === "custom") {
          dateRangeContainer.style.display = "block";
          dateToContainer.style.display = "block";
          dateContainer.style.display = "none";
          // Limpiar los campos de fecha cuando se cambia de rango personalizado a otro tipo
          document.getElementById("min-date").value = "";
          document.getElementById("max-date").value = "";
        } else {
          dateRangeContainer.style.display = "none";
          dateToContainer.style.display = "none";
          dateContainer.style.display = "block";

          // Limpiar los campos de fecha personalizados
          document.getElementById("min-date").value = "";
          document.getElementById("max-date").value = "";

          // Actualizar la etiqueta del campo de fecha según el tipo de filtro
          switch (filterType) {
            case "daily":
              dateLabel.textContent = "Fecha:";
              document.getElementById("filter-date").type = "date";
              document.getElementById("filter-date").min = null;
              document.getElementById("filter-date").max = null;
              document.getElementById("filter-date").step = null;
              document.getElementById("filter-date").value =
                setDefaultDateValue("daily");
              break;
            case "weekly":
              dateLabel.textContent = "Semana:";
              document.getElementById("filter-date").type = "week";
              document.getElementById("filter-date").min = null;
              document.getElementById("filter-date").max = null;
              document.getElementById("filter-date").step = null;
              document.getElementById("filter-date").value =
                setDefaultDateValue("weekly");
              break;
            case "monthly":
              dateLabel.textContent = "Mes:";
              document.getElementById("filter-date").type = "month";
              document.getElementById("filter-date").min = null;
              document.getElementById("filter-date").max = null;
              document.getElementById("filter-date").step = null;
              document.getElementById("filter-date").value =
                setDefaultDateValue("monthly");
              break;
            case "yearly":
              dateLabel.textContent = "Año:";
              document.getElementById("filter-date").type = "number";
              document.getElementById("filter-date").min = "1970";
              document.getElementById("filter-date").max =
                new Date().getFullYear() + 10;
              document.getElementById("filter-date").step = "1";
              document.getElementById("filter-date").value =
                setDefaultDateValue("yearly");
              break;
          }
        }

        // Actualizar la tabla y los totales financieros cuando se cambia el tipo de filtro
        table.ajax.reload();
        loadTotals();
      });

    // Event listeners para los campos de fecha para recarga automática
    document.getElementById("min-date").addEventListener("change", function () {
      table.ajax.reload();
      loadTotals();
    });

    document.getElementById("max-date").addEventListener("change", function () {
      table.ajax.reload();
      loadTotals();
    });

    // Event listener para el campo de búsqueda por concepto
    document
      .getElementById("search-concept")
      .addEventListener("input", function () {
        // Usar setTimeout para evitar demasiadas solicitudes mientras se escribe
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function () {
          table.ajax.reload();
          loadTotals();
        }, 500); // 500ms de delay después de dejar de escribir
      });

    // Event listener para el campo de fecha dinámico
    document
      .getElementById("filter-date")
      .addEventListener("change", function () {
        // Forzar la actualización inmediata
        table.ajax.reload();
        loadTotals();
      });

    // Event listeners para filtros
    document
      .getElementById("filter-btn")
      .addEventListener("click", function () {
        table.ajax.reload();
        // Actualizar los totales financieros después de aplicar el filtro
        loadTotals();
      });

    // Función para reiniciar todos los filtros a sus valores predeterminados
    function resetFilters() {
      document.getElementById("min-date").value = "";
      document.getElementById("max-date").value = "";
      document.getElementById("search-concept").value = ""; // Limpiar campo de búsqueda
      document.getElementById("filter-type").value = "daily"; // Valor por defecto

      // Configurar el campo de fecha para el tipo diario por defecto
      document.getElementById("filter-date").type = "date";
      document.getElementById("filter-date").min = null;
      document.getElementById("filter-date").max = null;
      document.getElementById("filter-date").step = null;
      document.getElementById("filter-date").value =
        setDefaultDateValue("daily");
      document.getElementById("date-label").textContent = "Fecha:";

      // Ocultar campos de rango personalizado y mostrar campo de fecha único
      document.getElementById("date-range-container").style.display = "none";
      document.getElementById("date-to-container").style.display = "none";
      document.getElementById("date-container").style.display = "block";

      // Actualizar la tabla y los totales
      table.ajax.reload();
      loadTotals();
    }

    document.getElementById("reset-btn").addEventListener("click", function () {
      resetFilters();
    });
    //cargamos el boton de ingresos
    loadBtnIncomeTable();
  });

  // Función para obtener el número de semana
  function getWeekNumber(d) {
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    var yearStart = new Date(Date.UTC(d.getFullYear(), 0, 1));
    var weekNo = Math.ceil(
      ((d - yearStart) / 86400000 + yearStart.getUTCDay() + 1) / 7
    );
    return weekNo;
  }

  // Función para obtener la fecha de inicio y fin de una semana específica (lunes a domingo)
  function getStartAndEndOfWeek(year, week) {
    // Cálculo según la ISO 8601 (semana comienza en lunes)
    const simple = new Date(year, 0, 1 + (week - 1) * 7);
    const dow = simple.getDay();
    const nearestThursday = new Date(simple);
    nearestThursday.setDate(simple.getDate() + (4 - dow)); // Jueves de esta semana

    const jan4 = new Date(nearestThursday.getFullYear(), 0, 4);
    const jan4Dow = jan4.getDay();
    const firstMonday = new Date(jan4);
    firstMonday.setDate(4 - (jan4Dow <= 0 ? (jan4Dow + 6) % 7 : jan4Dow - 1));

    const start = new Date(firstMonday);
    start.setDate(firstMonday.getDate() + (week - 1) * 7);

    const end = new Date(start);
    end.setDate(start.getDate() + 6);

    return {
      start:
        start.getFullYear() +
        "-" +
        String(start.getMonth() + 1).padStart(2, "0") +
        "-" +
        String(start.getDate()).padStart(2, "0"),
      end:
        end.getFullYear() +
        "-" +
        String(end.getMonth() + 1).padStart(2, "0") +
        "-" +
        String(end.getDate()).padStart(2, "0"),
    };
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

  // Función para calcular las fechas de inicio y fin según el tipo de filtro y el valor del campo
  function calculateDateRange(filterType, filterValue) {
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
              0
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

  // Función que carga los totales dinámicos
  function loadTotals() {
    // Obtener los parámetros de filtro
    const filterType = document.getElementById("filter-type").value;
    const searchConcept = document.getElementById("search-concept").value;

    // Obtener el valor del campo de fecha según el tipo de filtro
    let filterValue;
    if (filterType === "custom") {
      filterValue = null; // No se usa el campo de fecha único para custom
    } else {
      filterValue = document.getElementById("filter-date").value;
    }

    // Calcular fechas usando la función centralizada
    const { minDate, maxDate } = calculateDateRange(filterType, filterValue);

    $.ajax({
      url: base_url + "/pos/Movements/getTotals",
      type: "GET",
      data: {
        minDate: minDate,
        maxDate: maxDate,
        filterType: filterType,
        searchConcept: searchConcept,
      },
      //ponemos un load
      beforeSend: function () {
        showAlert(
          {
            title: "Cargando",
            message: "Cargando totales...",
            icon: "info",
          },
          "loading"
        );
      },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          const totals = res.totals;

          $("#balance").text(totals.balance);
          $("#totalSales").text(totals.total_sales);
          $("#totalExpenses").text(totals.total_expenses);
        }
      },
      error: function () {
        showAlert(
          {
            title: "Error",
            message: "Error al cargar los totales",
            icon: "error",
          },
          "float"
        );
      },
      complete: function () {
        Swal.close();
        showAlert(
          {
            title: "Listo",
            message: "Información cargada correctamente",
            icon: "success",
          },
          "float"
        );
      },
    });
  }

  // Función que carga la tabla con los datos
  function loadTable() {
    table = $("#table").DataTable({
      aProcessing: true,
      aServerSide: true,
      ajax: {
        url: base_url + "/pos/Movements/getMovements",
        data: function (d) {
          d.filterType = document.getElementById("filter-type").value;
          d.searchConcept = document.getElementById("search-concept").value;
          // Obtener el valor del campo de fecha según el tipo de filtro
          let filterValue;
          if (d.filterType === "custom") {
            filterValue = null; // No se usa el campo de fecha único para custom
          } else {
            filterValue = document.getElementById("filter-date").value;
          }
          // Calcular fechas usando la función centralizada
          const { minDate, maxDate } = calculateDateRange(
            d.filterType,
            filterValue
          );
          d.minDate = minDate;
          d.maxDate = maxDate;
        },
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        { data: "actions" },
        { data: "voucher_name" },
        {
          data: "amount",
          render: function (data) {
            return getcurrency + " " + data;
          },
        },
        { data: "name" },
        { data: "fullname" },
        { data: "date_time" },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard-check'></i> Copiar",
          titleAttr: "Copiar",
          className: "btn btn-sm btn-outline-secondary",
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          title: "Reporte de categorias en Excel",
          className: "btn btn-sm btn-outline-success",
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          title: "Reporte de categorias en CSV",
          className: "btn btn-sm btn-outline-info",
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-file-earmark-pdf'></i> PDF",
          title: "Reporte de categorias en PDF",
          className: "btn btn-sm btn-outline-danger",
          orientation: "landscape",
          pageSize: "LEGAL",
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
        {
          targets: [6],
          searchable: false,
          className: "text-center",
        },
      ],
      responsive: true,
      processing: true,
      colReorder: true,
      stateSave: true,
      destroy: true,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: base_url + "/Assets/js/libraries/POS/Spanish-datatables.json",
      },
      // Callback que se ejecuta después de que se carguen los datos
      drawCallback: function () {
        // Actualizar los totales después de cargar la tabla
        // Solo si no se está inicializando por primera vez
        if (table.page.info().recordsTotal > 0) {
          loadTotals();
        }
      },
    });
  }

  //FUNCION PARA CARGAR EL REPORTE DEL COMPROBANTE
  function loadReport() {
    $("#table").on("click", ".report-item", function () {
      const idVoucher = $(this).data("idvoucher");
      $.ajax({
        url: base_url + "/pos/Movements/getVoucher",
        type: "POST",
        dataType: "json",
        data: { idVoucherHeader: idVoucher },
        success: function (res) {
          if (!res.status) {
            alert(res.msg || "No se pudo cargar el comprobante");
            return;
          }

          const h = res.header;
          const d = res.details;

          // === Cabecera ===
          $("#name_bussines").text(h.name_bussines);
          $("#direction_bussines").text(h.direction_bussines);
          $("#document_bussines").text(h.document_bussines);
          $("#date_time").text(h.date_time);
          $("#name_customer").text(h.name_customer);
          $("#direction_customer").text(h.direction_customer);
          $("#fullname").text(h.fullname);
          document.getElementById("logo_voucher").src = h.logo;

          // Totales
          $("#percentage_discount").text(h.percentage_discount);
          $("#total_amount").text("S/ " + Number(h.amount).toFixed(2));

          // Calculamos subtotal y descuento a partir del detalle
          let subtotal = 0;
          d.forEach((item) => {
            subtotal += Number(item.sales_price_product) * item.stock_product;
          });

          const descuento =
            (subtotal * Number(h.percentage_discount || 0)) / 100;

          $("#subtotal_amount").text("S/ " + subtotal.toFixed(2));
          $("#discount_amount").text("S/ " + descuento.toFixed(2));

          // === Detalle ===
          const $tbody = $("#tbodyVoucherDetails");
          $tbody.empty();

          d.forEach((item) => {
            $tbody.append(`
            <tr>
              <td>${item.stock_product}</td>
              <td>${item.name_product} (${item.unit_of_measurement})</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product
              ).toFixed(2)}</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product * item.stock_product
              ).toFixed(2)}</td>
|            </tr>
          `);
          });

          const modalEl = document.getElementById("voucherModal");
          // Usar getOrCreateInstance para evitar múltiples instancias
          const modalVoucher = bootstrap.Modal.getOrCreateInstance(modalEl);
          modalVoucher.show();
        },
        error: function () {
          alert("Error de comunicación con el servidor");
        },
      });
    });
  }
  /**
   * Metodo que se encarga de descargar el comprobante en formato PNG
   */
  const dowloadPNG = () => {
    $("#download-png").click(() => {
      // console.log("descargar png");
      html2canvas(document.getElementById("voucherContainer"), {
        scale: 2, // más resolución
        useCORS: true, // por si usas imágenes externas
      })
        .then((canvas) => {
          // Convertir el canvas a dataURL (PNG)
          const imgData = canvas.toDataURL("image/png");

          // Crear un enlace "fantasma" para descargar
          const link = document.createElement("a");
          link.href = imgData;
          link.download = "Comprobante.png";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error al exportar PNG:", error);
        });
    });
  };
  /**
   * Metodo que se encarga de cargar los registros de movimientos de ingresos o egresos
   */
  function loadBtnIncomeTable() {
    if (document.querySelectorAll(".btn-movement").length === 0) return;
    const dataBtnIncome = document.querySelectorAll(".btn-movement");
    dataBtnIncome.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        //obtenemos el atributo data-type
        const type = btn.getAttribute("data-type");
        //traducimos el tipo
        const typeTranslate = type === "income" ? "ingresos" : "egresos";
        showAlert(
          {
            title: "Mostrando registros de " + typeTranslate,
            message: "Cargando registros de " + typeTranslate + "...",
            icon: "info",
          },
          "float"
        );
      });
    });
  }
})();
