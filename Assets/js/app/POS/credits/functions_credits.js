(() => {
  "use strict";
  const filterSearch = document.getElementById("filter-search");
  const filterDateStart = document.getElementById("filter-date-start");
  const filterDateEnd = document.getElementById("filter-date-end");
  const filterBtn = document.getElementById("filter-btn");
  const resetBtn = document.getElementById("reset-btn");
  document.addEventListener("DOMContentLoaded", function () {
    loadTable();
  });
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
        { data: "actions" },
        { data: "fullname" },
        {
          data: "credit_limit",
          render: (data, type, row) => {
            return `${getcurrency} ${data}`;
          },
        },
        {
          data: "amount_pending",
          render: (data, type, row) => {
            return `${getcurrency} ${data}`;
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
            columns: [2, 3, 4],
          },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          title: "Reporte de créditos en Excel",
          className: "btn btn-sm btn-outline-success",
          exportOptions: {
            columns: [2, 3, 4],
          },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          title: "Reporte de créditos en CSV",
          className: "btn btn-sm btn-outline-info",
          exportOptions: {
            columns: [2, 3, 4],
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
            columns: [2, 3, 4],
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
})();
