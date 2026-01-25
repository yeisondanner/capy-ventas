(() => {
  "use strict";
  let table;

  window.addEventListener("DOMContentLoaded", (e) => {
    loadTable();
    loadReportVoucher();
    loadReportExpense();
    dowloadPNG();

    // Mostrar u ocultar campos de rango personalizado según selección y actualizar comportamiento del campo de fecha
    document
      .getElementById("filter-type")
      .addEventListener("change", function () {
        const filterType = this.value;
        const dateContainer = document.getElementById("date-container");
        const dateRangeContainer = document.getElementById(
          "date-range-container",
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
    loadBtnMovementsTable();
  });

  // Función para obtener el número de semana
  function getWeekNumber(d) {
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    var yearStart = new Date(Date.UTC(d.getFullYear(), 0, 1));
    var weekNo = Math.ceil(
      ((d - yearStart) / 86400000 + yearStart.getUTCDay() + 1) / 7,
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
      dataType: "json",
      success: function (res) {
        if (res.status) {
          const totals = res.totals;

          $("#balance").text(totals.balance);
          $("#totalSales").text(totals.total_sales);
          $("#totalExpenses").text(totals.total_expenses);
        }
        if (res.url) {
          setTimeout(() => {
            window.location.href = res.url;
          }, 1000);
        }
      },
      error: function () {
        showAlert(
          {
            title: "Error",
            message: "Error al cargar los totales",
            icon: "error",
          },
          "float",
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
          "float",
        );
      },
    });
  }

  // Función que carga la tabla con los datos
  function loadTable() {
    table = $("#table").DataTable({
      processing: true,
      ajax: {
        url: base_url + "/pos/Movements/getMovements",
        data: function (d) {
          const type = document.querySelector(
            'input[name="movementType"]:checked',
          ).value;
          d.type = type;
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
            filterValue,
          );
          d.minDate = minDate;
          d.maxDate = maxDate;
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
        { data: "actions" },
        { data: "name" },
        {
          data: "amount",
          render: function (data) {
            return getcurrency + " " + data;
          },
        },
        { data: "method_payment" },
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
        // Actualizar los totales después de cargar la tabla
        // Solo si no se está inicializando por primera vez
        if (table.page.info().recordsTotal > 0) {
          loadTotals();
        }
      },
    });
  }

  //FUNCION PARA CARGAR EL REPORTE DEL COMPROBANTE
  function loadReportVoucher() {
    $("#table").on("click", ".report-item-income", function () {
      const idVoucher = $(this).data("id");
      $.ajax({
        url: base_url + "/pos/Movements/getVoucher",
        type: "POST",
        dataType: "json",
        data: { idVoucherHeader: idVoucher },
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
          //hacemos que el id se muestre con ceros a la izquierda
          const id_voucher = String(h.id).padStart(8, "0");
          //CV = Comprobante de Venta
          const voucher_code = `CV-${id_voucher}`;
          $("#voucher_code").text(voucher_code);

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
          $("#discount_amount").text("- S/ " + descuento.toFixed(2));
          $("#tax_name").text(h.tax_name);
          $("#tax_percentage").text(Number(h.tax_percentage).toFixed(2));
          $("#tax_amount").text(getcurrency + Number(h.tax_amount).toFixed(2));

          // === Detalle ===
          const $tbody = $("#tbodyVoucherDetails");
          $tbody.empty();

          d.forEach((item) => {
            $tbody.append(`
            <tr>
              <td>${item.stock_product}</td>
              <td>${item.name_product} (${item.unit_of_measurement})</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product,
              ).toFixed(2)}</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product * item.stock_product,
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
  //funcion que encarga de cargar el reporte de los gastos
  function loadReportExpense() {
    $("#table").on("click", ".report-item-expense", function () {
      const idExpense = $(this).data("id");

      $.ajax({
        url: base_url + "/pos/Movements/getExpense",
        type: "POST",
        dataType: "json",
        data: { idExpense: idExpense },
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

          $("#name_business_expense").text(
            d.name_bussines || "NOMBRE DEL NEGOCIO",
          );
          $("#direction_business_expense").text(
            d.direction_bussines || "Dirección no registrada",
          );
          $("#document_business_expense").text(
            d.document_bussines || "00000000000",
          );
          $("#expense_date").text(d.expense_date);
          $("#expense_fullname").text(d.fullname);
          $("#expense_name").text(d.name_expense);
          $("#expense_description").text(d.description || "Sin descripción");
          $("#expense_category").text(d.category_name);
          $("#expense_supplier").text(d.supplier_name || "--");
          $("#expense_voucher_reference").text(d.voucher_reference || "--");
          //hacemos que el id se muestre con ceros a la izquierda
          const id_expense = String(d.id).padStart(8, "0");
          //CG = Comprobante de Gasto
          const expense_code = `CG-${id_expense}`;
          $("#expense_code").text(expense_code);
          let statusBadge = "badge bg-secondary";
          if (d.status === "pagado")
            statusBadge = "badge bg-success text-white";
          else if (d.status === "anulado")
            statusBadge = "badge bg-danger text-white";
          else if (d.status === "pendiente")
            statusBadge = "badge bg-warning text-dark";

          $("#expense_status")
            .text(d.status.toUpperCase())
            .attr("class", statusBadge + " border");

          $("#expense_payment_method").text(d.payment_method);
          $("#expense_total_amount").text(d.amount_formatted);

          if (d.logo) {
            document.getElementById("logo_expense").src = d.logo;
          }

          const modalEl = document.getElementById("expenseModal");
          const modalExpense = bootstrap.Modal.getOrCreateInstance(modalEl);
          modalExpense.show();
        },
        error: function () {
          showAlert(
            {
              icon: "error",
              title: "Error",
              message: "Error de comunicación con el servidor",
              position: "bottom",
            },
            "float",
          );
        },
      });
    });
  }
  /**
   * Metodo que se encarga de descargar el comprobante en formato PNG
   */
  /* Generar captura completa clonando el nodo en el body para evitar recorte por scroll */
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

  const dowloadPNG = () => {
    // Comprobante de Venta (Ingresos)
    const btnDownloadPng = document.getElementById("download-png");
    if (btnDownloadPng) {
      // Remover listeners anteriores para evitar múltiples descargas
      const newBtn = btnDownloadPng.cloneNode(true);
      btnDownloadPng.parentNode.replaceChild(newBtn, btnDownloadPng);

      newBtn.addEventListener("click", () => {
        exportToPng("voucherContainer", "Comprobante_Venta.png");
      });
    }

    // Comprobante de Gasto (Egresos)
    const btnDownloadPngExpense = document.getElementById(
      "download-expense-png",
    );
    if (btnDownloadPngExpense) {
      // Remover listeners anteriores
      const newBtnExpense = btnDownloadPngExpense.cloneNode(true);
      btnDownloadPngExpense.parentNode.replaceChild(
        newBtnExpense,
        btnDownloadPngExpense,
      );

      newBtnExpense.addEventListener("click", () => {
        exportToPng("expenseContainer", "Comprobante_Egreso.png");
      });
    }
  };
  /**
   * Metodo que se encarga de cargar los registros de movimientos de ingresos o egresos
   */
  function loadBtnMovementsTable() {
    if (document.querySelectorAll(".btn-movement").length === 0) return;
    const dataBtnIncome = document.querySelectorAll(".btn-movement");
    dataBtnIncome.forEach((btn) => {
      btn.addEventListener("input", (e) => {
        e.preventDefault();
        //obtenemos el atributo data-type
        const type = btn.getAttribute("value");
        //traducimos el tipo
        const typeTranslate = type === "income" ? "ingresos" : "egresos";
        showAlert(
          {
            title: "Mostrando registros de " + typeTranslate,
            message: "Cargando registros de " + typeTranslate + "...",
            icon: "info",
          },
          "float",
        );
        table.ajax.reload();
        loadTotals();
      });
    });
  }
})();
