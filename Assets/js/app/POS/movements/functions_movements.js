(() => {
  "use strict";
  let table;

  window.addEventListener("DOMContentLoaded", (e) => {
    loadTable();
  });

  // Función que carga los totales dinámicos
  function loadTotals() {
    $.ajax({
      url: base_url + "/pos/Movements/getTotals",
      type: "GET",
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
        console.error("Error al cargar los totales");
      },
    });
  }

  // Función que carga la tabla con los datos
  function loadTable() {
    table = $("#table").DataTable({
      aProcessing: true,
      aServerSide: true,
      ajax: {
        url: "" + base_url + "/pos/Movements/getMovements",
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        { data: "actions" },
        { data: "voucher_name" },
        { data: "amount" },
        { data: "name" },
        { data: "date_time" },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='fa fa-copy'></i> Copiar",
          titleAttr: "Copiar",
          className: "btn btn-secondary",
        },
        {
          extend: "excelHtml5",
          text: "<i class='fa fa-table'></i> Excel",
          title: "Reporte de categorias en Excel",
          className: "btn btn-success",
        },
        {
          extend: "csvHtml5",
          text: "<i class='fa fa-file-text'></i> CSV",
          title: "Reporte de categorias en CSV",
          className: "btn btn-info",
        },
        {
          extend: "pdfHtml5",
          text: "<i class='fa fa-file-pdf'></i> PDF",
          title: "Reporte de categorias en PDF",
          className: "btn btn-danger",
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
      ],

      responsive: true,
      processing: true,
      colReorder: true,
      stateSave: false,
      destroy: true,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: base_url + "/Assets/js/libraries/POS/Spanish-datatables.json",
      },
      // Callback que se ejecuta después de que se carguen los datos
      drawCallback: function () {
        // Actualizar los totales después de cargar la tabla
        loadTotals();
        loadReport();
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

          // Totales
          $("#percentage_discount").text(h.percentage_discount);
          $("#total_amount").text("S/ " + Number(h.amount).toFixed(2));

          // Calculamos subtotal y descuento a partir del detalle
          let subtotal = 0;
          d.forEach((item) => {
            subtotal += Number(item.sales_price_product);
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
              <td>1.00</td>
              <td>${item.name_product} (${item.unit_of_measurement})</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product
              ).toFixed(2)}</td>
              <td class="text-end">S/ ${Number(
                item.sales_price_product
              ).toFixed(2)}</td>
            </tr>
          `);
          });

          const modalEl = document.getElementById("voucherModal");
          const modalVoucher = new bootstrap.Modal(modalEl);
          modalVoucher.show();
        },
        error: function () {
          alert("Error de comunicación con el servidor");
        },
      });
    });
  }
})();
