(function () {
  "use strict";

  let rolesTable;
  let roleModal;
  let reportModal;

  /**
   * Obtiene o crea una instancia de modal de Bootstrap.
   * @param {HTMLElement|null} element
   * @returns {any}
   */
  function getModalInstance(element) {
    if (!element) return null;
    if (window.bootstrap && bootstrap.Modal) {
      return bootstrap.Modal.getOrCreateInstance(element);
    }
    if (window.$) {
      return $(element);
    }
    return null;
  }

  /**
   * Muestra el modal indicado.
   * @param {HTMLElement|null} element
   */
  function showModal(element) {
    const instance = getModalInstance(element);
    if (!instance) return;
    if (typeof instance.show === "function") {
      instance.show();
    } else if (typeof instance.modal === "function") {
      instance.modal("show");
    }
  }

  /**
   * Oculta el modal indicado.
   * @param {HTMLElement|null} element
   */
  function hideModal(element) {
    const instance = getModalInstance(element);
    if (!instance) return;
    if (typeof instance.hide === "function") {
      instance.hide();
    } else if (typeof instance.modal === "function") {
      instance.modal("hide");
    }
  }

  /**
   * Devuelve el token CSRF almacenado en la tabla.
   * @returns {string}
   */
  function getSecurityToken() {
    const table = document.getElementById("rolesTable");
    if (!table) return "";
    return table.getAttribute("data-token") || "";
  }

  /**
   * Reinicia el formulario y sus textos.
   */
  function resetForm() {
    const form = document.getElementById("roleForm");
    if (!form) return;

    form.reset();
    form.dataset.mode = "create";
    const idField = document.getElementById("roleId");
    if (idField) {
      idField.value = "0";
    }

    toggleStatusField(false);
    updateModalTexts(false);
  }

  /**
   * Muestra u oculta el selector de estado según el modo.
   * @param {boolean} isVisible
   */
  function toggleStatusField(isVisible) {
    const statusGroup = document.getElementById("roleStatusGroup");
    const statusField = document.getElementById("txtRoleAppStatus");

    if (statusGroup) {
      statusGroup.classList.toggle("d-none", !isVisible);
    }

    if (statusField) {
      statusField.disabled = !isVisible;
      statusField.value = "Activo";
    }
  }

  /**
   * Actualiza los textos del modal según el modo.
   * @param {boolean} isEdit
   */
  function updateModalTexts(isEdit) {
    const title = document.getElementById("roleModalLabel");
    const button = document.querySelector("#roleForm button[type='submit']");

    if (title) {
      title.textContent = isEdit ? "Actualizar rol" : "Registrar rol";
    }

    if (button) {
      button.innerHTML = isEdit
        ? '<i class="bi bi-save"></i> Actualizar'
        : '<i class="bi bi-save"></i> Guardar';
    }
  }

  /**
   * Llena el formulario con los datos de un rol.
   * @param {any} role
   */
  function populateForm(role) {
    const form = document.getElementById("roleForm");
    if (!form || !role) return;

    form.dataset.mode = "edit";

    document.getElementById("roleId").value = role.idRoleApp;
    document.getElementById("txtRoleAppName").value = role.name || "";
    document.getElementById("txtRoleAppDescription").value = role.description || "";
    document.getElementById("txtRoleAppStatus").value = role.status || "Activo";

    toggleStatusField(true);
    updateModalTexts(true);
  }

  /**
   * Inicializa la tabla de roles con DataTables.
   */
  function initTable() {
    rolesTable = $("#rolesTable").DataTable({
      ajax: {
        url: `${base_url}/pos/Roles/getRoles`,
        dataSrc: "",
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
  }

  /**
   * Configura los eventos de la tabla (editar, eliminar, reporte).
   */
  function setupTableEvents() {
    const table = document.getElementById("rolesTable");
    if (!table) return;

    table.addEventListener("click", async (event) => {
      const target = event.target;
      const button = target.closest("button");
      if (!button || !rolesTable) return;

      const rowElement = button.closest("tr");
      const rowData = rolesTable.row(rowElement?.classList.contains("child") ? rowElement.previousElementSibling : rowElement).data();
      if (!rowData) return;

      if (button.classList.contains("edit-role")) {
        await loadRole(rowData.idRoleApp);
      }

      if (button.classList.contains("delete-role")) {
        confirmDelete(rowData.idRoleApp, rowData.name, button.dataset.token);
      }

      if (button.classList.contains("report-role")) {
        fillReport(button.dataset);
      }
    });
  }

  /**
   * Llena el modal de reporte con los datos del rol.
   * @param {DOMStringMap|any} data
   */
  function fillReport(data) {
    document.getElementById("reportRoleName").textContent = data.name || "-";
    document.getElementById("reportRoleDescription").textContent = data.description || "Sin descripción";
    document.getElementById("reportRoleStatus").textContent = data.status || "-";
    document.getElementById("reportRoleUpdated").textContent = data.updated || "-";
    showModal(reportModal);
  }

  /**
   * Solicita los datos de un rol y abre el modal en modo edición.
   * @param {number} roleId
   */
  async function loadRole(roleId) {
    try {
      const response = await fetch(`${base_url}/pos/Roles/getRole?id=${roleId}`);
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }
      const data = await response.json();
      if (!data.status) {
        showAlert({
          icon: "error",
          title: data.title || "Ocurrió un error",
          message: data.message || "No fue posible obtener la información del rol.",
        });
        return;
      }

      populateForm(data.data);
      showModal(roleModal);
    } catch (error) {
      console.error("Error cargando rol", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible obtener la información del rol. Inténtalo nuevamente.",
      });
    }
  }

  /**
   * Envía el formulario para crear o actualizar un rol.
   * @param {SubmitEvent} event
   */
  async function handleSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const mode = form.dataset.mode || "create";
    const url = mode === "edit" ? `${base_url}/pos/Roles/updateRole` : `${base_url}/pos/Roles/setRole`;

    try {
      const response = await fetch(url, {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();
      if (!data.status) {
        showAlert({
          icon: data.icon || "error",
          title: data.title || "Ocurrió un error",
          message: data.message || "No fue posible guardar el rol.",
        });
        return;
      }

      showAlert({
        icon: data.icon || "success",
        title: data.title || "Operación exitosa",
        message: data.message || "El rol se guardó correctamente.",
      });

      hideModal(roleModal);
      resetForm();
      rolesTable.ajax.reload(null, false);
    } catch (error) {
      console.error("Error guardando rol", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible guardar el rol, inténtalo nuevamente.",
      });
    }
  }

  /**
   * Confirma la eliminación de un rol.
   * @param {number} roleId
   * @param {string} roleName
   * @param {string} token
   */
  function confirmDelete(roleId, roleName, token) {
    Swal.fire({
      title: "Eliminar rol",
      text: `¿Deseas eliminar el rol "${roleName}"?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      confirmButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        deleteRole(roleId, token);
      }
    });
  }

  /**
   * Envía la solicitud de eliminación al servidor.
   * @param {number} roleId
   * @param {string} token
   */
  async function deleteRole(roleId, token) {
    try {
      const response = await fetch(`${base_url}/pos/Roles/deleteRole`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: roleId, token: token || getSecurityToken() }),
      });

      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();
      if (!data.status) {
        showAlert({
          icon: data.icon || "error",
          title: data.title || "Ocurrió un error",
          message: data.message || "No fue posible eliminar el rol.",
        });
        return;
      }

      showAlert({
        icon: data.icon || "success",
        title: data.title || "Operación exitosa",
        message: data.message || "El rol se eliminó correctamente.",
      });

      rolesTable.ajax.reload(null, false);
    } catch (error) {
      console.error("Error eliminando rol", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible eliminar el rol, inténtalo nuevamente.",
      });
    }
  }

  /**
   * Configura los botones y formularios de la vista.
   */
  function setupEvents() {
    const openModalBtn = document.getElementById("btnOpenRoleModal");
    const form = document.getElementById("roleForm");

    if (openModalBtn) {
      openModalBtn.addEventListener("click", () => {
        resetForm();
        showModal(roleModal);
      });
    }

    if (form) {
      form.addEventListener("submit", handleSubmit);
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    roleModal = document.getElementById("roleModal");
    reportModal = document.getElementById("roleReportModal");

    resetForm();
    initTable();
    setupTableEvents();
    setupEvents();
  });
})();
