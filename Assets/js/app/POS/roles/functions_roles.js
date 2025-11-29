import { ApiRoles } from "./functions_roles_api.js";

export class Roles {
  #rolesTable = null;
  // TODO: Datos que ingresan al formulario
  #permissions = new Map();

  // TODO: Seleccionamos los botones
  #btnOpenModalAddRole = $("#btnOpenModalAddRole");
  #btnAddRole = $("#btnAddRole");
  #btnUpdateRole = $("#btnUpdateRole");
  #btnDeleteRole = $("#btnDeleteRole");

  // TODO: Seleccionamos los modals
  #modalAddRole = $("#modalAddRole");
  #modalUpdateRole = $("#modalUpdateRole");
  #modalDeleteRole = $("#modalDeleteRole");
  #modalReportRole = $("#modalReportRole");

  // TODO: Seleccionamos los html
  #permissionsHtml = $("#cardPermissions");
  #permissionsUpdateHtml = $("#cardPermissionsUpdate");
  #permissionsReportHtml = $("#cardPermissionsReport");

  constructor(base_url) {
    this.apiRoles = new ApiRoles(base_url);
    this.#initTable();
    this.#openModal();
    this.#selectedPermision();
    this.#setRole();
    this.#updateRole();
    this.#deleteRole();
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
    // * Open moda add role
    $(this.#btnOpenModalAddRole).click(() => {
      this.#cleanForm();
      this.#getPermissions();
      this.#modalAddRole.modal("show");
    });

    // * Open modal update role
    $(document).on("click", ".update_role", (event) => {
      this.#cleanForm();
      let role_id = $(event.currentTarget).attr("data-id");
      this.apiRoles
        .get("getRole", {
          id: role_id,
        })
        .then((response) => {
          if (response.status) {
            // ? cargamos los datos
            $("#modalUpdateRoleLabel").text(
              `Actualizar Rol #${response.data.role.idRoleApp}`
            );
            $("#txtNameUpdate").val(response.data.role.name);
            $("#selectStatusUpdate").val(response.data.role.status);
            $("#txtDescriptionUpdate").val(response.data.role.description);
            this.#btnUpdateRole.attr(
              "data-role-id",
              response.data.role.idRoleApp
            );
            this.#loadPermissionsUpdate(
              response.data.permissions_interface,
              response.data.permissions_app
            );
            this.#modalUpdateRole.modal("show");
          }
        });
    });

    // * Open modal delete role
    $(document).on("click", ".delete_role", (event) => {
      const id = $(event.currentTarget).attr("data-id");
      const name = $(event.currentTarget).attr("data-name");
      let description = $(event.currentTarget).attr("data-description");

      if (!description) {
        description = `<i class="text-muted">Sin descripcion</i>`;
      }

      $(".card_information_role")
        .html(`<p class="mb-2">¿Estas seguro de eliminar el rol <strong class="text-primary">${name}</strong>?</p>
                    <p class="mb-2"><strong><i class="bi bi-exclamation-diamond"></i> Nota</strong>: Se eliminaran permisos asociados a este rol.</p>
                    <p class="mb-0" style="text-align: justify;"><strong>Descripcion:</strong> ${description}</p>`);

      $("#modalDeleteRoleLabel").text(`Eliminar Rol #${id}`);
      this.#btnDeleteRole.attr("data-id", id);
      this.#modalDeleteRole.modal("show");
    });

    // * Open modal report
    $(document).on("click", ".report_role", (event) => {
      const id = $(event.currentTarget).attr("data-id");
      const name = $(event.currentTarget).attr("data-name");
      let description = $(event.currentTarget).attr("data-description");
      const status = $(event.currentTarget).attr("data-status");
      const date = $(event.currentTarget).attr("data-updated");
      if (!description) {
        description = "Sin descripción";
      }

      // ? Asiganamos los valores para mostrar en la vista
      $("#modalReportRoleLabel").text(`Información del Rol #${id}`);
      $("#txtNameReport").val(name);
      $("#txtDescriptionReport").val(description);
      $("#txtStatusReport").val(status);
      $("#txtDateReport").text(date);

      // ? Consultamos los permisos para mostrar en la vista
      this.apiRoles.get("getPermissionsApp", { id: id }).then((response) => {
        if (!response.status) {
          return showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        }
        this.#permissionsReportHtml.empty("");
        let html = "";
        response.data.forEach((element, index) => {
          console.log(element);
          
          html += `<div class="d-flex flex-column">
                      <h6 class="fw-normal"><i class="bi bi-file-easel"></i> Interfaz: <strong>${element.interface_name}</strong></h6>
                      <div class="d-flex gap-2 flex-wrap">`;
          if (element.create === 1) {
            html += `<div class="flex-fill">
                              <div class="p-2 border rounded-2 shadow-sm bg-light"><i class="bi bi-check-all"></i> Crear</div>
                          </div>`;
          }
          if (element.read === 1) {
            html += `<div class="flex-fill">
                              <div class="p-2 border rounded-2 shadow-sm bg-light"><i class="bi bi-check-all"></i> Leer</div>
                          </div>`;
          }
          if (element.update === 1) {
            html += `<div class="flex-fill">
                              <div class="p-2 border rounded-2 shadow-sm bg-light"><i class="bi bi-check-all"></i> Actualizar</div>
                          </div>`;
          }
          if (element.delete === 1) {
            html += `<div class="flex-fill">
                              <div class="p-2 border rounded-2 shadow-sm bg-light"><i class="bi bi-check-all"></i> Eliminar</div>
                          </div>`;
          }
          html += `</div></div>`;
          if (response.data[index + 1]) {
            html += `<hr>`;
          }
        });
        this.#permissionsReportHtml.append(html);
        this.#modalReportRole.modal("show");
      });
    });
  };

  // TODO: Funcion para seleccionar los check
  #selectedPermision = () => {
    $(document).on("click", ".checkPermision", (event) => {
      let Interface = $(event.currentTarget).attr("data-interface");
      let Permission = $(event.currentTarget).attr("data-permision");
      let checkPermision = $("#" + Permission + "_" + Interface);

      if (!$(event.target).is("input[type='checkbox']")) {
        checkPermision.prop("checked", !checkPermision.prop("checked"));
      }

      if (!this.#permissions.get(Interface)) {
        this.#permissions.set(Interface, []);
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
    });

    $(document).on("change", ".checkAllPermissions", (event) => {
      const isChecked = $(event.target).prop("checked");

      const allInputs = $(
        ".checkPermision input[type='checkbox']:not(:disabled)"
      );

      allInputs.prop("checked", isChecked);

      allInputs.each((index, element) => {
        let parent = $(element).closest(".checkPermision");
        let Interface = parent.attr("data-interface");
        let Permission = parent.attr("data-permision");

        if (!this.#permissions.get(Interface)) {
          this.#permissions.set(Interface, []);
        }

        let currentPermissions = this.#permissions.get(Interface);

        if (isChecked) {
          if (currentPermissions.indexOf(Permission) === -1) {
            currentPermissions.push(Permission);
          }
        } else {
          let idx = currentPermissions.indexOf(Permission);
          if (idx !== -1) {
            currentPermissions.splice(idx, 1);
          }
        }
      });
    });
  };

  // TODO: Cargamos los permisos para actualizar
  #loadPermissionsUpdate = (permissionsInterface, permissionsApp) => {
    this.#permissionsHtml.empty("");
    this.#permissionsUpdateHtml.empty("");
    this.#permissions.clear();
    let html = "";
    permissionsInterface.forEach((element, index) => {
      let perApp = permissionsApp.find(
        (item) => item.plan_interface_id === element.plan_interface_id
      );

      if (perApp) {
        const planInterfaceId = perApp.plan_interface_id.toString();
        if (!this.#permissions.get(planInterfaceId)) {
          this.#permissions.set(planInterfaceId, []);
        }
        perApp.create == 1
          ? this.#permissions.get(planInterfaceId).push("create")
          : false;
        perApp.read == 1
          ? this.#permissions.get(planInterfaceId).push("read")
          : false;
        perApp.update == 1
          ? this.#permissions.get(planInterfaceId).push("update")
          : false;
        perApp.delete == 1
          ? this.#permissions.get(planInterfaceId).push("delete")
          : false;
      }

      html += `<div class="d-flex gap-2 flex-column mb-3">
                      <h6 class="fw-normal"><i class="bi bi-file-easel"></i> Interfaz: <strong>${
                        element.interface_name
                      }</strong></h6>
                      <div class="d-flex gap-2 flex-wrap">
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="create" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
        element.create == 1 ? true : "pe-none user-select-none"
      }">
                              <input ${
                                perApp && perApp.create == 1 ? "checked" : ""
                              } ${
        element.create == 1 ? "" : "disabled"
      } id="create_${element.plan_interface_id}" type="checkbox" value="">
                              <label class="${
                                element.create == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Crear
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="read" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
        element.read == 1 ? true : "pe-none user-select-none"
      }">
                              <input ${
                                perApp && perApp.read == 1 ? "checked" : ""
                              } ${
        element.read == 1 ? "" : "disabled"
      } id="read_${element.plan_interface_id}" type="checkbox" value="">
                              <label class="${
                                element.read == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Leer
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="update" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
        element.update == 1 ? true : "pe-none user-select-none"
      }">
                              <input ${
                                perApp && perApp.update == 1 ? "checked" : ""
                              } ${
        element.update == 1 ? "" : "disabled"
      } id="update_${element.plan_interface_id}" type="checkbox" value="">
                              <label class="${
                                element.update == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Actualizar
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="delete" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
        element.delete == 1 ? true : "pe-none user-select-none"
      }">
                              <input ${
                                perApp && perApp.delete == 1 ? "checked" : ""
                              } ${
        element.delete == 1 ? "" : "disabled"
      } id="delete_${element.plan_interface_id}" type="checkbox" value="">
                              <label class="${
                                element.delete == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Eliminar
                              </label>
                          </div>
                      </div>
                  </div>`;
      if (permissionsInterface[index + 1]) {
        html += `<hr>`;
      }
    });
    return this.#permissionsUpdateHtml.append(html);
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
      this.#permissionsUpdateHtml.empty("");
      let html = "";
      response.data.forEach((element, index) => {
        html += `<div class="d-flex gap-2 flex-column mb-3">
                      <h6 class="fw-normal"><i class="bi bi-file-easel"></i> Interfaz: <strong>${
                        element.interface_name
                      }</strong></h6>
                      <div class="d-flex gap-2 flex-wrap">
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="create" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.create == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.create == 1 ? "" : "disabled"
                              } id="create_${
          element.plan_interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.create == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Crear
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="read" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.read == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.read == 1 ? "" : "disabled"
                              } id="read_${
          element.plan_interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.read == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Leer
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="update" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.update == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.update == 1 ? "" : "disabled"
                              } id="update_${
          element.plan_interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.update == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Actualizar
                              </label>
                          </div>
                          <div style="cursor: pointer;" data-interface="${
                            element.plan_interface_id
                          }" data-permision="delete" class="form-check flex-fill d-flex rounded-2 p-2 gap-1 shadow-sm border checkPermision ${
          element.delete == 1 ? true : "pe-none user-select-none"
        }">
                              <input ${
                                element.delete == 1 ? "" : "disabled"
                              } id="delete_${
          element.plan_interface_id
        }" type="checkbox" value="">
                              <label class="${
                                element.delete == 1
                                  ? true
                                  : "text-decoration-line-through text-danger"
                              }" style="cursor: pointer; pointer-events: none;">
                              Eliminar
                              </label>
                          </div>
                      </div>
                  </div>`;
        if (response.data[index + 1]) {
          html += `<hr>`;
        }
      });
      this.#permissionsHtml.append(html);
    });
  };

  // TODO: Funcion para registrar un rol con su permiso
  #setRole = () => {
    this.#btnAddRole.click(() => {
      // ? Validamos que se envie el nombre
      let name = $("#txtName").val();
      if (!name) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El nombre es requerido",
        });
      }

      // ? Validamos que existas al menos un permiso
      // console.log(this.#permissions.size);
      // falta implementar

      // ? Datos opcionales
      let description = $("#txtDescription").val();
      !description ? null : description;

      this.apiRoles
        .post("setRole", {
          name: name,
          description: description,
          permissions: Object.fromEntries(this.#permissions),
        })
        .then((response) => {
          if (response.status) {
            this.#rolesTable.ajax.reload();
            this.#cleanForm();
            this.#modalAddRole.modal("hide");
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };

  // TODO: Funcion para actualizar un rol con sus permisos
  #updateRole = () => {
    this.#btnUpdateRole.click((event) => {
      // ? Validamos que se envie el nombre
      let name = $("#txtNameUpdate").val();
      if (!name) {
        return showAlert({
          icon: "warning",
          title: "Validacion de datos",
          message: "El nombre es requerido",
        });
      }

      // ? Validamos que existas al menos un permiso
      // console.log(this.#permissions.size);
      // falta implementar

      // ? Cargamos el id del role
      let role_id = $(event.currentTarget).attr("data-role-id");

      let status = $("#selectStatusUpdate").val();

      // ? Datos opcionales
      let description = $("#txtDescriptionUpdate").val();
      !description ? null : description;

      this.apiRoles
        .post("updateRole", {
          id: role_id,
          name: name,
          description: description,
          status: status, // ? Actualizar luego
          permissions: Object.fromEntries(this.#permissions),
        })
        .then((response) => {
          if (response.status) {
            this.#rolesTable.ajax.reload();
            this.#cleanForm();
            this.#modalUpdateRole.modal("hide");
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };

  // TODO: Funcion para eliminar un rol con sus permisos
  #deleteRole = () => {
    this.#btnDeleteRole.click((event) => {
      const id = $(event.currentTarget).attr("data-id");
      this.apiRoles
        .post("deleteRole", {
          id: id,
        })
        .then((response) => {
          if (response.status) {
            this.#rolesTable.ajax.reload();
            this.#cleanForm();
            this.#modalDeleteRole.modal("hide");
          }
          showAlert({
            icon: response.type,
            title: response.title,
            message: response.message,
          });
        });
    });
  };
  // TODO: Funcion para limpiar los formularios de registro y actualizar
  #cleanForm = () => {
    $("#txtName").val(null);
    $("#txtdDescription").val(null);
    $("#txtNameUpdate").val(null);
    $("#txtdDescriptionUpdate").val(null);
    $(".checkAllPermissions").prop("checked", false);
    this.#permissions.clear();
  };
}

const classRoles = new Roles(base_url);
