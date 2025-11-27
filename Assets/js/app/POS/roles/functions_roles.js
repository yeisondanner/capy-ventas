import { ApiRoles } from "./functions_roles_api.js";

export class Roles {
  constructor() {
    this.apiRoles = new ApiRoles(base_url);
    this.initTable();
  }

  initTable = () => {
    rolesTable = $("#rolesTable").DataTable({
      ajax: (data, callback, settings) => {
        apiRoles
          .get("getRoles")
          .then((response) => {
            callback({
              data: response,
            });
          })
          .catch((error) => {
            console.error("Error al cargar los roles:", error);
            callback({ data: [] });
          });
      },
      columns: [
        { data: "cont" },
        { data: "actions", orderable: false, searchable: false },
        { data: "name" },
        { data: "description" },
        { data: "status", orderable: false },
        { data: "updated_at" },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-secondary",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-success",
          title: "Roles",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-info text-white",
          title: "Roles",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Roles",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
      ],
      columnDefs: [
        { targets: [0, 1, 4, 5], className: "text-center" },
        { targets: 3, className: "text-wrap" },
      ],
      responsive: true,
      destroy: true,
      colReorder: true,
      stateSave: false,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
      drawCallback: () => {
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => el.classList.add("pagination-sm"));
      },
    });
  };
}
