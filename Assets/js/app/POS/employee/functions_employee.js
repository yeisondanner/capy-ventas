(function () {
  "use strict";
  let employeesTable;
  let modalCreate;
  let modalUpdate;
  let modalReport;
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
   * Genera una función que limita la frecuencia de ejecución.
   * @param {Function} fn Función a ejecutar.
   * @param {number} delay Milisegundos de espera.
   * @returns {Function}
   */
  function debounce(fn, delay = 300) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn(...args), delay);
    };
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
   * Limpia los campos de información de usuario según el prefijo indicado.
   * @param {"create"|"update"} prefix
   */
  function resetUserInfo(prefix) {
    const currentPrefix = prefix === "update" ? "update_" : "";
    const displayPrefix = prefix === "update" ? "update_" : "";
    const userInput = document.getElementById(
      `${currentPrefix}txtEmployeeUserappId`,
    );
    if (userInput) {
      userInput.value = "";
    }

    const defaultName = "Sin usuario seleccionado";
    const defaultEmail = "-";
    const defaultUser = "No asignado";
    const defaultNote =
      prefix === "update"
        ? "Busca un usuario activo para actualizar la asignación."
        : "Busca un usuario para mostrar sus datos antes de guardar.";

    const fullNameElement = document.getElementById(
      `${displayPrefix}displayEmployeeFullName`,
    );
    const emailElement = document.getElementById(
      `${displayPrefix}displayEmployeeEmail`,
    );
    const userElement = document.getElementById(
      `${displayPrefix}displayEmployeeUser`,
    );
    const noteElement = document.getElementById(
      `${displayPrefix}displayEmployeeNote`,
    );

    if (fullNameElement) fullNameElement.textContent = defaultName;
    if (emailElement) emailElement.textContent = defaultEmail;
    if (userElement) userElement.textContent = defaultUser;
    if (noteElement) noteElement.textContent = defaultNote;
  }

  /**
   * Establece los datos del usuario encontrado en el formulario indicado.
   * @param {"create"|"update"} prefix
   * @param {{idUserApp:number,names:string,lastname:string,email:string,user:string}} user
   */
  function fillUserInfo(prefix, user) {
    const currentPrefix = prefix === "update" ? "update_" : "";
    const displayPrefix = prefix === "update" ? "update_" : "";

    const idInput = document.getElementById(
      `${currentPrefix}txtEmployeeUserappId`,
    );
    const searchInput = document.getElementById(
      `${currentPrefix}txtEmployeeUserSearch`,
    );

    const fullName = `${user.names || ""} ${user.lastname || ""}`.trim();

    if (idInput) idInput.value = user.idUserApp || "";
    if (searchInput && user.user) {
      searchInput.value = user.user;
    }

    const fullNameElement = document.getElementById(
      `${displayPrefix}displayEmployeeFullName`,
    );
    const emailElement = document.getElementById(
      `${displayPrefix}displayEmployeeEmail`,
    );
    const userElement = document.getElementById(
      `${displayPrefix}displayEmployeeUser`,
    );
    const noteElement = document.getElementById(
      `${displayPrefix}displayEmployeeNote`,
    );

    if (fullNameElement)
      fullNameElement.textContent = fullName || "Sin nombre registrado";
    if (emailElement) emailElement.textContent = user.email || "-";
    if (userElement) userElement.textContent = user.user || "No asignado";
    if (noteElement)
      noteElement.textContent = "Datos obtenidos del usuario seleccionado.";
  }

  /**
   * Pinta las sugerencias en el datalist correspondiente.
   * @param {"create"|"update"} prefix
   * @param {Array<{user:string,email:string,full_name:string}>} suggestions
   */
  function renderSuggestions(prefix, suggestions) {
    const datalistId =
      prefix === "update"
        ? "employeeUserSuggestionsUpdate"
        : "employeeUserSuggestions";
    const datalist = document.getElementById(datalistId);

    if (!datalist) return;

    datalist.innerHTML = "";

    suggestions.forEach((item) => {
      const option = document.createElement("option");
      option.value = item.user || item.email || "";
      option.label = [item.user || item.email || "", item.full_name]
        .filter(Boolean)
        .join(" – ");
      datalist.appendChild(option);
    });
  }

  /**
   * Obtiene sugerencias predictivas de usuarios según el texto ingresado.
   * @param {"create"|"update"} prefix
   * @param {number|null} excludeEmployeeId
   */
  const fetchUserSuggestions = debounce(
    async (prefix, excludeEmployeeId = null) => {
      const currentPrefix = prefix === "update" ? "update_" : "";
      const input = document.getElementById(
        `${currentPrefix}txtEmployeeUserSearch`,
      );
      if (!input) return;

      const query = input.value.trim();
      if (query.length < 2) {
        renderSuggestions(prefix, []);
        return;
      }

      try {
        const params = new URLSearchParams({ q: query });
        if (excludeEmployeeId) {
          params.append("exclude_employee_id", String(excludeEmployeeId));
        }

        const response = await fetch(
          `${base_url}/pos/Employee/suggestUserApps?${params.toString()}`,
        );

        const data = await response.json();

        if (data.status) {
          renderSuggestions(prefix, data.data || []);
        } else {
          renderSuggestions(prefix, []);
        }
      } catch (error) {
        console.error("Error obteniendo sugerencias", error);
        renderSuggestions(prefix, []);
      }
    },
    350,
  );

  /**
   * Consulta al backend un usuario por identificador y lo muestra en el formulario.
   * @param {"create"|"update"} prefix
   * @param {number|null} excludeEmployeeId
   */
  async function searchUser(prefix, excludeEmployeeId = null) {
    const currentPrefix = prefix === "update" ? "update_" : "";
    const input = document.getElementById(
      `${currentPrefix}txtEmployeeUserSearch`,
    );

    if (!input) return;

    const identifier = input.value.trim();
    if (!identifier) {
      showAlert({
        icon: "warning",
        title: "Dato requerido",
        message: "Ingresa un usuario o correo para realizar la búsqueda.",
      });
      return;
    }

    try {
      const params = new URLSearchParams({ identifier });
      if (excludeEmployeeId) {
        params.append("exclude_employee_id", String(excludeEmployeeId));
      }

      const response = await fetch(
        `${base_url}/pos/Employee/findUserApp?${params.toString()}`,
      );

      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();

      if (!data.status) {
        resetUserInfo(prefix);
        showAlert({
          icon: data.icon || "error",
          title: data.title || "Ocurrió un error",
          message:
            data.message || "No fue posible encontrar el usuario indicado.",
        });
        return;
      }

      fillUserInfo(prefix, data.data);
      showAlert({
        icon: "success",
        title: "Usuario encontrado",
        message: "El usuario está disponible para asignarlo como empleado.",
      });
    } catch (error) {
      console.error("Error buscando usuario", error);
      resetUserInfo(prefix);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message:
          "No fue posible buscar el usuario. Verifica el dato ingresado e inténtalo nuevamente.",
      });
    }
  }

  /**
   * Configura la búsqueda predictiva en el campo indicado.
   * @param {"create"|"update"} prefix
   */
  function setupPredictiveSearch(prefix) {
    const currentPrefix = prefix === "update" ? "update_" : "";
    const input = document.getElementById(
      `${currentPrefix}txtEmployeeUserSearch`,
    );

    if (!input) return;

    const handleSuggestions = () => {
      const employeeIdField = document.getElementById("update_txtEmployeeId");
      const excludeEmployeeId =
        prefix === "update" && employeeIdField
          ? Number(employeeIdField.value || 0)
          : null;

      fetchUserSuggestions(prefix, excludeEmployeeId || null);
    };

    input.addEventListener("input", handleSuggestions);

    input.addEventListener("change", () => {
      const employeeIdField = document.getElementById("update_txtEmployeeId");
      const excludeEmployeeId =
        prefix === "update" && employeeIdField
          ? Number(employeeIdField.value || 0)
          : null;

      if (input.value.trim()) {
        searchUser(prefix, excludeEmployeeId || null);
      } else {
        resetUserInfo(prefix);
      }
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
   * Carga los roles desde el servidor.
   */
  async function loadSelectors() {
    try {
      const roleAppsResponse = await fetch(
        `${base_url}/pos/Employee/getRoleApps`,
      );

      if (!roleAppsResponse.ok) {
        throw new Error(`Roles: ${roleAppsResponse.status}`);
      }

      const roleAppsJson = await roleAppsResponse.json();

      if (!roleAppsJson.status) {
        throw new Error(
          roleAppsJson.message ||
            "No fue posible cargar los roles de aplicación",
        );
      }

      cachedRoleApps = roleAppsJson.data.map((item) => ({
        id: item.idRoleApp,
        name: item.name,
      }));

      populateSelect(
        document.getElementById("txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol",
      );
      populateSelect(
        document.getElementById("update_txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol",
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
          className: "btn btn-sm btn-outline-secondary",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success",
          title: "Empleados",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info",
          title: "Empleados",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger",
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
      processing: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
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
    populateSelect(
      document.getElementById("txtEmployeeRolapp"),
      cachedRoleApps,
      "Selecciona un rol",
    );
    resetUserInfo("create");

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
      const userId = formData.get("txtEmployeeUserappId");

      if (!userId) {
        showAlert({
          icon: "warning",
          title: "Usuario requerido",
          message:
            "Busca y selecciona un usuario antes de registrar al empleado.",
        });
        return;
      }
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
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1000);
        }

        if (data.status) {
          form.reset();
          populateSelect(
            document.getElementById("txtEmployeeRolapp"),
            cachedRoleApps,
            "Selecciona un rol",
          );
          resetUserInfo("create");
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
      const userId = formData.get("update_txtEmployeeUserappId");

      if (!userId) {
        showAlert({
          icon: "warning",
          title: "Usuario requerido",
          message:
            "Busca y selecciona un usuario disponible antes de actualizar.",
        });
        return;
      }
      try {
        const response = await fetch(
          `${base_url}/pos/Employee/updateEmployee`,
          {
            method: "POST",
            body: formData,
          },
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
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1000);
        }

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
          },
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
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1000);
        }

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
        `${base_url}/pos/Employee/getEmployee?id=${employeeId}`,
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
      resetUserInfo("update");

      await loadSelectors();

      populateSelect(
        document.getElementById("update_txtEmployeeRolapp"),
        cachedRoleApps,
        "Selecciona un rol",
      );

      document.getElementById("update_txtEmployeeId").value =
        employee.idEmployee;
      document.getElementById("update_txtEmployeeUserappId").value =
        employee.userapp_id || "";
      document.getElementById("update_txtEmployeeUserSearch").value =
        employee.user_app_user || employee.person_email || "";
      document.getElementById("update_txtEmployeeRolapp").value =
        employee.rolapp_id;
      document.getElementById("update_txtEmployeeStatus").value =
        employee.status;
      fillUserInfo("update", {
        idUserApp: employee.userapp_id,
        names: employee.names,
        lastname: employee.lastname,
        email: employee.person_email,
        user: employee.user_app_user,
      });

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
        `${base_url}/pos/Employee/getEmployee?id=${employeeId}`,
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

      document.getElementById("reportEmployeeName").textContent =
        employee.full_name || "Sin usuario asignado";
      document.getElementById("reportEmployeeUserApp").textContent =
        employee.user_app_user || "-";
      document.getElementById("reportEmployeeEmail").textContent =
        employee.person_email || "-";
      document.getElementById("reportEmployeeRole").textContent =
        employee.role_app_name || "-";
      document.getElementById("reportEmployeeRoleDescription").textContent =
        employee.role_app_description || "Sin descripción registrada.";

      const statusBadgeElement = document.getElementById(
        "reportEmployeeStatusBadge",
      );
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
          10,
        );
        loadEmployeeForEdition(id);
        return;
      }

      const reportButton = event.target.closest(".report-employee");
      if (reportButton) {
        event.preventDefault();
        const id = Number.parseInt(
          reportButton.getAttribute("data-id") || "0",
          10,
        );
        loadEmployeeForReport(id);
        return;
      }

      const deleteButton = event.target.closest(".delete-employee");
      if (deleteButton) {
        event.preventDefault();
        const id = Number.parseInt(
          deleteButton.getAttribute("data-id") || "0",
          10,
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
    setupPredictiveSearch("create");
    setupPredictiveSearch("update");

    const btnOpenEmployeeModal = document.getElementById(
      "btnOpenEmployeeModal",
    );
    if (btnOpenEmployeeModal) {
      btnOpenEmployeeModal.addEventListener("click", () => {
        openCreateModal();
      });
    }

    const btnSearchCreate = document.getElementById("btnSearchEmployeeUser");
    if (btnSearchCreate) {
      btnSearchCreate.addEventListener("click", () => searchUser("create"));
    }

    const btnSearchUpdate = document.getElementById(
      "btnSearchEmployeeUserUpdate",
    );
    if (btnSearchUpdate) {
      btnSearchUpdate.addEventListener("click", () => {
        const employeeIdField = document.getElementById("update_txtEmployeeId");
        const employeeId = employeeIdField
          ? Number(employeeIdField.value || 0)
          : null;
        searchUser("update", employeeId);
      });
    }
  });
})();
