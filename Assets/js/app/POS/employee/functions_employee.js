(function () {
  "use strict";
  let employeesTable;
  let modalCreate;
  let modalUpdate;
  let modalReport;
  let cachedUserApps = [];
  let cachedRoleApps = [];

  const rootUrl = base_url;

  /**
   * Obtiene o crea la instancia de un modal de Bootstrap.
   * @param {HTMLElement} element Elemento del modal.
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
   * Muestra un modal compatible con Bootstrap 4/5.
   * @param {HTMLElement} element
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
   * Oculta un modal compatible con Bootstrap 4/5.
   * @param {HTMLElement} element
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
   * Muestra una alerta usando SweetAlert2.
   * @param {Object} options Opciones de la alerta.
   */
  function showAlert(options) {
    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: options.icon || "info",
        title: options.title || "",
        text: options.message || "",
        confirmButtonText: "Aceptar",
      });
    } else if (typeof toastr !== "undefined") {
      toastr[options.icon === "error" ? "error" : options.icon === "success" ? "success" : "info"](
        options.message || "",
        options.title || ""
      );
    }
  }

  /**
   * Rellena un elemento select con opciones.
   * @param {HTMLElement} select Elemento select.
   * @param {Array} items Lista de elementos.
   * @param {string} placeholder Texto del placeholder.
   */
  function populateSelect(select, items, placeholder) {
    if (!select) return;
    select.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
    items.forEach((item) => {
      const opt = document.createElement("option");
      opt.value = item.id;
      opt.textContent = item.name || item.label || item.text;
      select.appendChild(opt);
    });
  }

  /**
   * Inicializa las referencias de los modales.
   */
  function initModals() {
    modalCreate = document.getElementById("modalEmployee");
    modalUpdate = document.getElementById("modalUpdateEmployee");
    modalReport = document.getElementById("modalEmployeeReport");
  }

  /**
   * Carga los usuarios de aplicación y roles desde el servidor.
   * @param {number|null} excludeEmployeeId ID del empleado a excluir (para actualizaciones).
   */
  async function loadSelectors(excludeEmployeeId = null) {
    try {
      let userAppsUrl = `${base_url}/pos/Employee/getUserApps`;
      if (excludeEmployeeId) {
        userAppsUrl += `?exclude_employee_id=${excludeEmployeeId}`;
      }
      
      const [userAppsResponse, roleAppsResponse] = await Promise.all([
        fetch(userAppsUrl),
        fetch(`${base_url}/pos/Employee/getRoleApps`),
      ]);

      if (!userAppsResponse.ok) {
        throw new Error(`Usuarios: ${userAppsResponse.status}`);
      }
      if (!roleAppsResponse.ok) {
        throw new Error(`Roles: ${roleAppsResponse.status}`);
      }

      const userAppsJson = await userAppsResponse.json();
      const roleAppsJson = await roleAppsResponse.json();

      if (!userAppsJson.status) {
        throw new Error(
          userAppsJson.message || "No fue posible cargar los usuarios de aplicación"
        );
      }
      if (!roleAppsJson.status) {
        throw new Error(
          roleAppsJson.message || "No fue posible cargar los roles de aplicación"
        );
      }

      cachedUserApps = userAppsJson.data.map((item) => ({
        id: item.idUserApp,
        idUserApp: item.idUserApp,
        name: `${item.full_name} - ${item.user} (${item.email})`,
        full_name: item.full_name,
        user: item.user,
        email: item.email,
      }));
      cachedRoleApps = roleAppsJson.data.map((item) => ({
        id: item.idRoleApp,
        name: item.name,
      }));

      // Agregar opción "Sin usuario asignado" al inicio
      const userAppsWithNone = [
        { id: "", idUserApp: null, name: "Sin usuario asignado" },
        ...cachedUserApps
      ];
      
      populateSelect(
        document.getElementById("txtEmployeeUserapp"),
        userAppsWithNone,
        "Sin usuario asignado"
      );
      populateSelect(
        document.getElementById("update_txtEmployeeUserapp"),
        userAppsWithNone,
        "Sin usuario asignado"
      );

      populateSelect(
        document.getElementById("txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol"
      );
      populateSelect(
        document.getElementById("update_txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol"
      );
    } catch (error) {
      console.error("Error cargando selectores", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message:
          "No fue posible cargar las listas de apoyo. Actualiza la página e inténtalo nuevamente.",
      });
    }
  }

  /**
   * Configura la tabla de empleados con DataTables.
   */
  function initTable() {
    employeesTable = $("#table").DataTable({
      ajax: {
        url: `${base_url}/pos/Employee/getEmployees`,
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        { data: "actions", orderable: false, searchable: false },
        { data: "full_name" },
        { data: "user_app_display" },
        { data: "role_app_name" },
        { data: "status", orderable: false },
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
          title: "Empleados",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-info text-white",
          title: "Empleados",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Empleados",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
      ],
      columnDefs: [
        { targets: 0, className: "text-center" },
        { targets: 1, className: "text-center" },
        { targets: 5, className: "text-center" },
      ],
      responsive: true,
      destroy: true,
      colReorder: true,
      stateSave: false,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${rootUrl}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
      drawCallback: () => {
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => {
            el.classList.add("pagination-sm");
          });
      },
    });
  }

  /**
   * Reinicia los formularios y muestra el modal de registro.
   */
  async function openCreateModal() {
    const form = document.getElementById("formSaveEmployee");
    if (!form) return;

    if (!cachedRoleApps.length) {
      await loadSelectors();
    }

    if (!cachedRoleApps.length) {
      showAlert({
        icon: "warning",
        title: "Datos incompletos",
        message:
          "Antes de registrar un empleado debes contar con roles disponibles.",
      });
      return;
    }

    form.reset();
    // Agregar opción "Sin usuario asignado" al inicio
    const userAppsWithNone = [
      { id: "", idUserApp: null, name: "Sin usuario asignado" },
      ...cachedUserApps
    ];
    
    populateSelect(
      document.getElementById("txtEmployeeUserapp"),
      userAppsWithNone,
      "Sin usuario asignado"
    );
    populateSelect(
      document.getElementById("txtEmployeeRolapp"),
      cachedRoleApps,
      "Selecciona un rol"
    );

    showModal(modalCreate);
  }

  /**
   * Envía el formulario de creación de empleados.
   */
  function handleCreate() {
    const form = document.getElementById("formSaveEmployee");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      try {
        const response = await fetch(`${base_url}/pos/Employee/setEmployee`, {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }

        const data = await response.json();
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title:
            data.title ||
            (data.status ? "Operación exitosa" : "Ocurrió un error"),
          message: data.message || "",
        });

        if (data.status) {
          form.reset();
          // Agregar opción "Sin usuario asignado" al inicio
          const userAppsWithNone = [
            { id: "", idUserApp: null, name: "Sin usuario asignado" },
            ...cachedUserApps
          ];
          
          populateSelect(
            document.getElementById("txtEmployeeUserapp"),
            userAppsWithNone,
            "Sin usuario asignado"
          );
          populateSelect(
            document.getElementById("txtEmployeeRolapp"),
            cachedRoleApps,
            "Selecciona un rol"
          );
          hideModal(modalCreate);
          employeesTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al registrar empleado", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible registrar el empleado. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Atiende el envío del formulario de actualización de empleados.
   */
  function handleUpdate() {
    const form = document.getElementById("formUpdateEmployee");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      try {
        const response = await fetch(
          `${base_url}/pos/Employee/updateEmployee`,
          {
            method: "POST",
            body: formData,
          }
        );

        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }

        const data = await response.json();
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title:
            data.title ||
            (data.status ? "Operación exitosa" : "Ocurrió un error"),
          message: data.message || "",
        });

        if (data.status) {
          hideModal(modalUpdate);
          employeesTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al actualizar empleado", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible actualizar el empleado. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Obtiene el token CSRF disponible en la vista.
   *
   * @returns {string}
   */
  function getSecurityToken() {
    const table = document.getElementById("table");
    if (table && table.dataset.token) {
      return table.dataset.token;
    }

    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
      return metaToken.getAttribute("content") || "";
    }

    return "";
  }

  /**
   * Confirma con SweetAlert la eliminación del empleado seleccionado.
   *
   * @param {number} employeeId Identificador del empleado.
   * @param {string} employeeName Nombre del empleado.
   * @param {string | null} employeeToken Token CSRF asociado al botón.
   */
  function confirmDeleteEmployee(employeeId, employeeName, employeeToken) {
    if (!Number.isInteger(employeeId) || employeeId <= 0) {
      showAlert({
        icon: "warning",
        title: "Empleado inválido",
        message: "No fue posible identificar el empleado seleccionado.",
      });
      return;
    }

    const token = employeeToken || getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message:
          "No fue posible validar la solicitud de eliminación. Actualiza la página e inténtalo nuevamente.",
      });
      return;
    }

    const safeName = employeeName
      ? `<strong>${employeeName}</strong>`
      : "este empleado";

    Swal.fire({
      title: "¿Eliminar empleado?",
      html: `Se eliminará definitivamente ${safeName}. Esta acción no se puede deshacer.`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      focusCancel: true,
    }).then(async (result) => {
      if (!result.isConfirmed) {
        return;
      }

      try {
        const response = await fetch(
          `${base_url}/pos/Employee/deleteEmployee`,
          {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: employeeId, token }),
          }
        );

        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }

        const data = await response.json();
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title:
            data.title ||
            (data.status ? "Operación exitosa" : "Ocurrió un error"),
          message: data.message || "",
        });

        if (data.status) {
          employeesTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al eliminar empleado", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible eliminar el empleado. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Obtiene la información de un empleado y prepara el formulario de edición.
   * @param {number} employeeId
   */
  async function loadEmployeeForEdition(employeeId) {
    try {
      const response = await fetch(
        `${base_url}/pos/Employee/getEmployee?id=${employeeId}`
      );
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();
      if (!data.status) {
        showAlert({
          icon: "error",
          title: data.title || "Ocurrió un error",
          message:
            data.message ||
            "No fue posible obtener la información del empleado.",
        });
        return;
      }

      const form = document.getElementById("formUpdateEmployee");
      if (!form) return;

      const employee = data.data;
      form.reset();

      // Recargar selectores excluyendo el empleado actual para que su usuario esté disponible
      await loadSelectors(employee.idEmployee);

      // Preparar lista de usuarios con opción "Sin usuario asignado"
      let userAppsForUpdate = [
        { id: "", idUserApp: null, name: "Sin usuario asignado" }
      ];

      // Si el empleado tiene usuario, asegurar que esté en la lista
      if (employee.userapp_id) {
        const currentUserAppExists = cachedUserApps.some(
          (item) => item.idUserApp === employee.userapp_id
        );
        if (!currentUserAppExists && employee.user_app_user) {
          cachedUserApps.push({
            id: employee.userapp_id,
            idUserApp: employee.userapp_id,
            name: `${employee.full_name} - ${employee.user_app_user} (${employee.person_email})`,
            full_name: employee.full_name,
            user: employee.user_app_user,
            email: employee.person_email,
          });
        }
      }

      userAppsForUpdate = [...userAppsForUpdate, ...cachedUserApps];

      populateSelect(
        document.getElementById("update_txtEmployeeUserapp"),
        userAppsForUpdate,
        "Sin usuario asignado"
      );
      populateSelect(
        document.getElementById("update_txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol"
      );

      document.getElementById("update_txtEmployeeId").value = employee.idEmployee;
      document.getElementById("update_txtEmployeeUserapp").value = employee.userapp_id || "";
      document.getElementById("update_txtEmployeeRolapp").value = employee.rolapp_id;
      document.getElementById("update_txtEmployeeStatus").value = employee.status;

      showModal(modalUpdate);
    } catch (error) {
      console.error("Error cargando empleado", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message:
          "No fue posible obtener la información del empleado. Inténtalo nuevamente.",
      });
    }
  }

  /**
   * Carga la información del empleado en el modal de reporte.
   * @param {number} employeeId
   */
  async function loadEmployeeForReport(employeeId) {
    try {
      const response = await fetch(
        `${base_url}/pos/Employee/getEmployee?id=${employeeId}`
      );
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();
      if (!data.status) {
        showAlert({
          icon: "error",
          title: data.title || "Ocurrió un error",
          message:
            data.message ||
            "No fue posible obtener la información del empleado.",
        });
        return;
      }

      const employee = data.data;

      document.getElementById("reportEmployeeName").textContent = employee.full_name || "Sin usuario asignado";
      document.getElementById("reportEmployeeUserApp").textContent = employee.user_app_user || "-";
      document.getElementById("reportEmployeeEmail").textContent = employee.person_email || "-";
      document.getElementById("reportEmployeeRole").textContent = employee.role_app_name || "-";
      document.getElementById("reportEmployeeRoleDescription").textContent = employee.role_app_description || "Sin descripción registrada.";

      const statusBadgeElement = document.getElementById("reportEmployeeStatusBadge");
      const statusElement = document.getElementById("reportEmployeeStatus");
      
      if (statusBadgeElement) {
        if (employee.status === "Activo") {
          statusBadgeElement.textContent = "Activo";
          statusBadgeElement.classList.remove("text-danger", "text-muted");
          statusBadgeElement.classList.add("text-success");
        } else {
          statusBadgeElement.textContent = "Inactivo";
          statusBadgeElement.classList.remove("text-success", "text-muted");
          statusBadgeElement.classList.add("text-danger");
        }
      }
      
      if (statusElement) {
        statusElement.textContent = employee.status || "-";
      }

      showModal(modalReport);
    } catch (error) {
      console.error("Error cargando reporte de empleado", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message:
          "No fue posible obtener la información del empleado. Inténtalo nuevamente.",
      });
    }
  }

  /**
   * Registra los listeners para las acciones de la tabla.
   */
  function registerTableActions() {
    const table = document.getElementById("table");
    if (!table) return;

    table.addEventListener("click", (event) => {
      const editButton = event.target.closest(".edit-employee");
      if (editButton) {
        event.preventDefault();
        const id = Number.parseInt(
          editButton.getAttribute("data-id") || "0",
          10
        );
        loadEmployeeForEdition(id);
        return;
      }

      const reportButton = event.target.closest(".report-employee");
      if (reportButton) {
        event.preventDefault();
        const id = Number.parseInt(
          reportButton.getAttribute("data-id") || "0",
          10
        );
        loadEmployeeForReport(id);
        return;
      }

      const deleteButton = event.target.closest(".delete-employee");
      if (deleteButton) {
        event.preventDefault();
        const id = Number.parseInt(
          deleteButton.getAttribute("data-id") || "0",
          10
        );
        const name = deleteButton.getAttribute("data-full-name") || "";
        const token = deleteButton.getAttribute("data-token") || "";
        confirmDeleteEmployee(id, name, token);
      }
    });
  }

  /**
   * Inicializa la aplicación cuando el DOM está listo.
   */
  document.addEventListener("DOMContentLoaded", async () => {
    initModals();
    await loadSelectors();
    initTable();
    registerTableActions();
    handleCreate();
    handleUpdate();

    const btnOpenEmployeeModal = document.getElementById("btnOpenEmployeeModal");
    if (btnOpenEmployeeModal) {
      btnOpenEmployeeModal.addEventListener("click", () => {
        openCreateModal();
      });
    }
  });
})();
