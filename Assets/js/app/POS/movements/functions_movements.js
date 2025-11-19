let table;
window.addEventListener("DOMContentLoaded", (e) => {
  loadTable();
  setTimeout(() => {
    
  }, 1000);
});
window.addEventListener("click", (e) => {
//   loadDataUpdate();
//   confirmationDelete();
//   loadReport();
});
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
      
    ],

    responsive: "true",
    processing: true,
    destroy: true,
    iDisplayLength: 10,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/POS/Spanish-datatables.json",
    },
  });
}