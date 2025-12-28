export default class ReadBox {
  constructor() {
    this.table = $("#table");
  }
  /**
   * Metodo que se encarga de cargar la tabla
   */
  loadTable() {
    this.table.DataTable({
      ajax: {
        url: `${base_url}/pos/Boxhistory/loadBoxHistory`,
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        {
          data: null,
          className: "text-center",
          render: function (data, type, row) {
            return `<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-sm btn-outline-info"><i class="bi bi-file-text"></i></button>
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
              return `<span class="text-success"><i class="bi bi-arrow-up"></i> ${getcurrency} ${data}</span>`;
            } else {
              return `<span class="text-danger"><i class="bi bi-arrow-down"></i> ${getcurrency} ${data}</span>`;
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
      responsive: true,
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
}
