import { ApiRoles } from "./functions_roles_api.js";

export class Roles {
  #rolesTable = null;
  #permissions = new Map();
  // TODO: Seleccionamos los botones
  #btnOpenModalRole = $("#btnOpenModalRole");
  #btnAddRole = $("#btnAddRole");
  // TODO: Seleccionamos los modals
  #modalAddRole = $("#openModalRole");
  // TODO: Seleccionamos los html
  #permissionsHtml = $("#cardPermissions");

  constructor(base_url) {
    this.apiRoles = new ApiRoles(base_url);
    this.#initTable();
    this.#openModal();
    this.#selectedPermision();
    this.#getPermissions();
    this.#setRole();
  }

  // TODO: Funcion para mostrar los roles
  #initTable = () => {
    this.#rolesTable = $("#rolesTable").DataTable({
      ajax: (data, callback, settings) => {
        this.apiRoles
          .get("getRoles")
          .then((response) => {
            callback({
              data: response.data || [],
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

  // TODO: Funcion para abrir todos los modals
  #openModal = () => {
    $(this.#btnOpenModalRole).click(() => {
      $(this.#modalAddRole).modal("show");
    });
  };

  // TODO: Funcion para seleccionar los check
  #selectedPermision = () => {
    $(document).on("click", ".checkPermision", (event) => {
      let Interface = $(event.currentTarget).attr("data-interface");
      let Permission = $(event.currentTarget).attr("data-permision");
      let checkPermision = $("#" + Permission);

      if (!this.#permissions.get(Interface)) {
        this.#permissions.set(Interface, []);
      }

      if (checkPermision.prop("checked")) {
        checkPermision.prop("checked", false);
      } else {
        checkPermision.prop("checked", true);
      }

      if (checkPermision.prop("checked")) {
        if (this.#permissions.get(Interface).indexOf(Permission) === -1) {
          this.#permissions.get(Interface).push(Permission);
        }
      } else {
        let INDEX = this.#permissions.get(Interface).indexOf(Permission);
        if (INDEX !== -1) {
          this.#permissions.get(Interface).splice(INDEX, 1);
        }
      }
      console.log(this.#permissions);
    });
  };

  // TODO: Cargamos todos los permisos todos los permisos
  #getPermissions = () => {
    this.apiRoles.get("getPermissions").then((response) => {
      if (!response.status) {
        return showAlert({
          icon: response.type,
          title: "Error",
          message: response.message,
        });
      }

      this.#permissionsHtml.empty("");
      let html = "";
      response.data.forEach((element) => {
        html += `<div class="d-flex gap-2 flex-column mb-3">
                      <h6 class="fw-normal"><i class="bi bi-file-easel"></i> Interfaz: <strong>${
                        element.interface_name
                      }</strong></h6>
                      <div class="d-flex gap-2">
                          <div style="cursor: pointer;" data-interface="${
                            element.interface_name
                          }" data-permision="create_${
          element.interface_id
        }" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.create == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.create == 1 ? "" : "disabled"
                              } id="create_${
          element.interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.create == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer;">
                              Crear
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.interface_name
                          }" data-permision="read_${
          element.interface_id
        }" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.read == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.read == 1 ? "" : "disabled"
                              } id="read_${
          element.interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.read == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer;">
                              Leer
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.interface_name
                          }" data-permision="update_${
          element.interface_id
        }" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.update == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.update == 1 ? "" : "disabled"
                              } id="update_${
          element.interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.update == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer;">
                              Actualizar
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.interface_name
                          }" data-permision="delete_${
          element.interface_id
        }" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.delete == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.delete == 1 ? "" : "disabled"
                              } id="delete_${
          element.interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.delete == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer;">
                              Eliminar
                              </label>
                          </div>
                      </div>
                  </div>`;
      });
      this.#permissionsHtml.append(html);

      console.log(response.data);
    });
  };

  // TODO: Funcion para registrar un rol con su permiso

  #setRole = () => {
    this.#btnAddRole.click(() => {
      console.log(this.#permissions);
      
      this.apiRoles
        .post("setRole", Object.fromEntries(this.#permissions))
        .then((response) => {
          console.log(response);
        });
    });
  };
}

const classRoles = new Roles(base_url);
