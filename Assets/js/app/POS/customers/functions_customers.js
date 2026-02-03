(function () {
  "use strict";

  let customersTable;
  let customerModalElement;
  let editCustomerModalElement;
  let detailModalElement;

  const PROTECTED_CUSTOMER_NAME = "Sin cliente";
  const PROTECTED_CUSTOMER_KEY = normalizeCustomerName(PROTECTED_CUSTOMER_NAME);

  /**
   * Normaliza un nombre de cliente eliminando tildes y espacios duplicados.
   * @param {string} value Texto a normalizar.
   * @returns {string}
   */
  function normalizeCustomerName(value) {
    if (!value) return "";

    let normalized = value.toString().trim();
    if (!normalized) return "";

    if (typeof normalized.normalize === "function") {
      normalized = normalized.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    normalized = normalized.toLowerCase();

    return normalized.replace(/\s+/g, " ");
  }

  /**
   * Indica si el nombre corresponde al cliente protegido del sistema.
   * @param {string} value Nombre a evaluar.
   * @returns {boolean}
   */
  function isProtectedCustomerName(value) {
    if (!value) return false;
    return normalizeCustomerName(value) === PROTECTED_CUSTOMER_KEY;
  }

  /**
   * Obtiene o crea una instancia de modal de Bootstrap.
   * @param {HTMLElement|null} element Elemento del modal.
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
   * Oculta un modal compatible con Bootstrap 4/5.
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
   * Obtiene el token CSRF almacenado en la tabla.
   * @returns {string}
   */
  function getSecurityToken() {
    const table = document.getElementById("customerTable");
    if (!table) return "";
    return table.getAttribute("data-token") || "";
  }

  /**
   * Resetea el formulario de creación.
   */
  function resetCreateForm() {
    const form = document.getElementById("formCustomer");
    if (!form) return;

    form.reset();
    const idField = document.getElementById("customerId");
    if (idField) {
      idField.value = "0";
    }
  }

  /**
   * Rellena el formulario de EDICIÓN con los datos del cliente.
   * @param {any} customer Datos del cliente.
   */
  function populateEditForm(customer) {
    const form = document.getElementById("formEditCustomer");
    if (!form || !customer) return;

    // Campos con sufijo 'Edit' para el modal de edición
    const idField = document.getElementById("customerIdEdit");
    if (idField) {
      idField.value = customer.idCustomer || customer.id || 0;
    }

    const documentTypeField = document.getElementById(
      "txtCustomerDocumentTypeEdit",
    );
    if (documentTypeField) {
      documentTypeField.value = String(
        customer.documenttype_id || customer.document_type_id || "",
      );
    }

    const documentField = document.getElementById("txtCustomerDocumentEdit");
    if (documentField) {
      documentField.value = customer.document_raw || "";
    }

    const nameField = document.getElementById("txtCustomerNameEdit");
    if (nameField) {
      nameField.value = customer.fullname_raw || "";
    }

    const phoneField = document.getElementById("txtCustomerPhoneEdit");
    if (phoneField) {
      phoneField.value = customer.phone_raw || "";
    }

    const emailField = document.getElementById("txtCustomerEmailEdit");
    if (emailField) {
      emailField.value = customer.email_raw || "";
    }

    const addressField = document.getElementById("txtCustomerAddressEdit");
    if (addressField) {
      addressField.value = customer.direction_raw || "";
    }

    const creditLimitField = document.getElementById("txtCustomerCreditLimit");
    if (creditLimitField) {
      creditLimitField.value = customer.credit_limit || "50.00";
    }

    const defaultInterestField = document.getElementById(
      "txtCustomerDefaultInterest",
    );
    if (defaultInterestField) {
      defaultInterestField.value = customer.default_interest_rate || "0.00";
    }

    const currentInterestField = document.getElementById(
      "txtCustomerCurrentInterest",
    );
    if (currentInterestField) {
      currentInterestField.value = customer.current_interest_rate || "0.00";
    }

    const billingDateField = document.getElementById("txtCustomerBillingDate");
    if (billingDateField) {
      billingDateField.value = customer.billing_date || "";
    }
  }

  /**
   * Extrae los datos de la fila asociada al botón clicado.
   * @param {HTMLElement} element Elemento que detonó el evento.
   * @returns {any}
   */
  function getRowDataFromElement(element) {
    if (!element || !customersTable) return null;

    const rowElement = element.closest("tr");
    if (!rowElement) return null;

    let data = customersTable.row(rowElement).data();

    if (!data && rowElement.classList.contains("child")) {
      const previous = rowElement.previousElementSibling;
      if (previous) {
        data = customersTable.row(previous).data();
      }
    }

    return data || null;
  }

  /**
  /**
   * Muestra el modal con el detalle del cliente seleccionado (Estilo Reporte).
   * @param {any} customer Datos del cliente (se usa el ID para fetch completo).
   */
  async function showCustomerDetail(customer) {
    if (!customer) return;

    const customerId = customer.idCustomer || customer.id || 0;
    if (!customerId) return;

    try {
      const formData = new FormData();
      formData.append("customerId", customerId);

      const response = await fetch(`${base_url}/pos/Customers/getCustomer`, {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const res = await response.json();

      if (!res.status || !res.data) {
        showAlert({
          icon: "error",
          title: "Error",
          message: res.message || "No se pudo cargar el detalle del cliente",
        });
        return;
      }

      const d = res.data;

      // Header Negocio
      const logoImg = document.getElementById("report_logo");
      if (logoImg) {
        if (d.logo) {
          logoImg.src = d.logo;
          logoImg.style.display = "inline-block";
        } else {
          logoImg.style.display = "none";
        }
      }

      document.getElementById("report_business_name").textContent =
        d.name_bussines || "--";
      document.getElementById("report_business_address").textContent =
        d.direction_bussines || "--";
      document.getElementById("report_business_document").textContent =
        d.document_bussines || "--";

      // Detalles Cliente
      document.getElementById("report_customer_name").textContent =
        d.fullname || "--";
      document.getElementById("report_customer_doctype").textContent =
        d.document_type || "--";
      document.getElementById("report_customer_document").textContent =
        d.document_number || "--";
      document.getElementById("report_customer_phone").textContent =
        d.phone_number || "--";
      document.getElementById("report_customer_email").textContent =
        d.email || "--";
      document.getElementById("report_customer_address").textContent =
        d.direction || "--";

      // Estado Badge
      const statusEl = document.getElementById("report_customer_status");
      if (statusEl) {
        let statusBadgeClass = "badge bg-secondary";
        if (d.status === "Activo") {
          statusBadgeClass = "badge bg-success text-white";
        } else if (d.status === "Inactivo") {
          statusBadgeClass = "badge bg-danger text-white";
        }

        statusEl.textContent = d.status || "-";
        statusEl.className = statusBadgeClass + " border";
      }

      showModal(detailModalElement);
    } catch (error) {
      console.error("Error cargando detalle cliente", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible cargar el detalle del cliente.",
      });
    }
  }

  /**
   * Genera captura completa clonando el nodo en el body para evitar recorte por scroll
   * @param {string} elementId ID del elemento a capturar
   * @param {string} filename Nombre del archivo a descargar
   */
  const exportToPng = (elementId, filename) => {
    const originalElement = document.getElementById(elementId);
    if (!originalElement) return;

    // 1. Clonar el elemento
    const clone = originalElement.cloneNode(true);

    // 2. Estilizar el clon para que se muestre completo
    Object.assign(clone.style, {
      position: "fixed",
      top: "-9999px",
      left: "-9999px",
      width: originalElement.offsetWidth + "px", // Mismo ancho que el original
      height: "auto", // Altura automática para mostrar todo el contenido
      zIndex: "-1",
      overflow: "visible", // Asegurar que no haya scroll oculto
      backgroundColor: "#ffffff", // Asegurar fondo blanco
    });

    // 3. Insertar el clon en el documento
    document.body.appendChild(clone);

    // 4. Generar el canvas desde el clon
    // Asegurarse de tener html2canvas cargado en la vista (generalmente en footer_pos o header_pos)
    if (typeof html2canvas === "undefined") {
      console.error("html2canvas no está definido.");
      document.body.removeChild(clone);
      return;
    }

    html2canvas(clone, {
      scale: 2, // Mejor resolución
      useCORS: true,
      scrollY: -window.scrollY, // Ajuste para evitar desplazamiento
      backgroundColor: "#ffffff",
    })
      .then((canvas) => {
        const imgData = canvas.toDataURL("image/png");
        const link = document.createElement("a");
        link.href = imgData;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      })
      .catch((err) => {
        console.error("Error exporting PNG:", err);
      })
      .finally(() => {
        // 5. Eliminar el clon
        if (document.body.contains(clone)) {
          document.body.removeChild(clone);
        }
      });
  };

  function registerExportButton() {
    const btn = document.getElementById("btnDownloadCustomerPng");
    if (btn) {
      // Clonar para eliminar listeners previos si se reinicializa
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);

      newBtn.addEventListener("click", () => {
        const name =
          document.getElementById("report_customer_name")?.textContent ||
          "Cliente";
        // Sanitize filename
        const cleanName = name.replace(/[^a-z0-9]/gi, "_").toLowerCase();
        exportToPng(
          "customerReportContainer",
          `Ficha_Cliente_${cleanName}.png`,
        );
      });
    }
  }

  /**
   * Envía la información para CREAR un nuevo cliente via modal #modalCustomer
   */
  async function submitCreateCustomer(event) {
    event.preventDefault();

    const form = event.currentTarget;
    if (!form) return;

    const token = getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message: "No se encontró el token de seguridad.",
      });
      return;
    }

    const formData = new FormData(form);
    // Endpoint para crear
    const endpoint = `${base_url}/pos/Customers/setCustomer`;

    if (!formData.get("token")) {
      formData.append("token", token);
    }

    try {
      const response = await fetch(endpoint, {
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
        resetCreateForm();
        hideModal(customerModalElement);
        if (customersTable) {
          customersTable.ajax.reload(null, false);
        }
      }
    } catch (error) {
      console.error("Error creando cliente", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible registrar el cliente.",
      });
    }
  }

  /**
   * Envía la información para EDITAR un cliente via modal #modalEditCustomer
   */
  async function submitEditCustomer(event) {
    event.preventDefault();

    const form = event.currentTarget;
    if (!form) return;

    const token = getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message: "No se encontró el token de seguridad.",
      });
      return;
    }

    const formData = new FormData(form);
    // Endpoint para actualizar
    const endpoint = `${base_url}/pos/Customers/updateCustomer`;

    if (!formData.get("token")) {
      formData.append("token", token);
    }

    try {
      const response = await fetch(endpoint, {
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
        form.reset(); // Reset del form de edición
        hideModal(editCustomerModalElement);
        if (customersTable) {
          customersTable.ajax.reload(null, false);
        }
      }
    } catch (error) {
      console.error("Error actualizando cliente", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible actualizar el cliente.",
      });
    }
  }

  /**
   * Solicita confirmación para eliminar un cliente.
   * @param {any} customer Datos del cliente.
   * @param {HTMLElement} button Botón que originó la acción.
   */
  function confirmDeleteCustomer(customer, button) {
    if (!customer || !button) return;

    if (
      isProtectedCustomerName(customer.fullname_raw || customer.fullname || "")
    ) {
      showAlert({
        icon: "info",
        title: "Acción no permitida",
        message: "El cliente predeterminado no puede eliminarse.",
      });
      return;
    }

    const token = button.getAttribute("data-token") || getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message:
          "No fue posible validar la solicitud. Actualiza la página e inténtalo nuevamente.",
      });
      return;
    }

    const customerName =
      customer.fullname_raw || customer.fullname || "este cliente";

    Swal.fire({
      title: "¿Eliminar cliente?",
      html: `Se eliminará definitivamente <strong>${customerName}</strong>. Esta acción no se puede deshacer.`,
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
          `${base_url}/pos/Customers/deleteCustomer`,
          {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              id: customer.idCustomer || customer.id || 0,
              token,
            }),
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
        if (data.status && customersTable) {
          customersTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error eliminando cliente", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible eliminar el cliente. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Registra los eventos del formulario y de la tabla de clientes.
   */
  /**
   * Registra los eventos del formulario y de la tabla de clientes.
   */
  function registerEvents() {
    const createForm = document.getElementById("formCustomer");
    if (createForm) {
      createForm.addEventListener("submit", submitCreateCustomer);
    }

    const editForm = document.getElementById("formEditCustomer");
    if (editForm) {
      editForm.addEventListener("submit", submitEditCustomer);
    }

    const openButton = document.getElementById("btnOpenCustomerModal");
    if (openButton) {
      openButton.addEventListener("click", () => {
        resetCreateForm();
        showModal(customerModalElement);
      });
    }

    const table = document.getElementById("customerTable");
    if (table) {
      table.addEventListener("click", (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
          return;
        }

        const viewButton = target.closest(".view-customer");
        if (viewButton) {
          event.preventDefault();
          const data = getRowDataFromElement(viewButton);
          if (data) {
            showCustomerDetail(data);
          }
          return;
        }

        const editButton = target.closest(".edit-customer");
        if (editButton) {
          event.preventDefault();
          const data = getRowDataFromElement(editButton);
          if (data) {
            populateEditForm(data);
            showModal(editCustomerModalElement);
          }
          return;
        }

        const deleteButton = target.closest(".delete-customer");
        if (deleteButton) {
          event.preventDefault();
          const data = getRowDataFromElement(deleteButton);
          if (data) {
            confirmDeleteCustomer(data, deleteButton);
          }
        }
      });
    }
  }

  /**
   * Inicializa la tabla de clientes con DataTables.
   */
  function initCustomersTable() {
    customersTable = $("#customerTable").DataTable({
      processing: true,
      ajax: {
        url: `${base_url}/pos/Customers/getCustomers`,
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
        { data: "actions", orderable: false, searchable: false },
        { data: "fullname" },
        {
          data: "document_type",
          className: "text-center",
        },
        { data: "document_number" },
        {
          data: "phone_number",
          render: function (data, type, row) {
            return (
              `<i class="bi bi-telephone text-success"></i> ${data}` || "-"
            );
          },
        },
        {
          data: "email",
          render: function (data, type, row) {
            return `<i class="bi bi-envelope text-primary"></i> ${data}` || "-";
          },
        },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success",
          title: "Clientes",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info",
          title: "Clientes",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Clientes",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
      ],
      columnDefs: [
        { targets: 0, className: "text-center" },
        { targets: 1, className: "text-center" },
        { targets: [4, 5, 6], className: "text-nowrap" },
      ],
      keyTable: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
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

  document.addEventListener("DOMContentLoaded", () => {
    customerModalElement = document.getElementById("modalCustomer");
    editCustomerModalElement = document.getElementById("modalEditCustomer");
    detailModalElement = document.getElementById("modalCustomerDetail");

    registerEvents();
    initCustomersTable();
    registerExportButton();
  });
})();
