(function () {
  "use strict";

  let suppliersTable;
  let supplierModalElement;
  let detailModalElement;

  const PROTECTED_SUPPLIER_NAME = "Sin Proveedor";
  const PROTECTED_SUPPLIER_KEY = normalizeSupplierName(PROTECTED_SUPPLIER_NAME);

  /**
   * Normaliza un nombre de proveedor eliminando tildes y espacios duplicados.
   * @param {string} value Texto a normalizar.
   * @returns {string}
   */
  function normalizeSupplierName(value) {
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
   * Indica si el nombre corresponde al proveedor protegido del sistema.
   * @param {string} value Nombre a evaluar.
   * @returns {boolean}
   */
  function isProtectedSupplierName(value) {
    if (!value) return false;
    return normalizeSupplierName(value) === PROTECTED_SUPPLIER_KEY;
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
    const table = document.getElementById("supplierTable");
    if (!table) return "";
    return table.getAttribute("data-token") || "";
  }

  /**
   * Reinicia el formulario de proveedores.
   */
  function resetSupplierForm() {
    const form = document.getElementById("formSupplier");
    if (!form) return;

    form.reset();
    form.dataset.mode = "create";

    const idField = document.getElementById("supplierId");
    if (idField) {
      idField.value = "0";
    }

    updateModalTexts(false);
  }

  /**
   * Actualiza los textos del modal según el modo seleccionado.
   * @param {boolean} isEdit Indica si el modal está en modo edición.
   */
  function updateModalTexts(isEdit) {
    const title = document.getElementById("modalSupplierLabel");
    const submitButton = document.querySelector(
      "#formSupplier button[type='submit']"
    );

    if (title) {
      title.textContent = isEdit
        ? "Actualizar proveedor"
        : "Registrar proveedor";
    }

    if (submitButton) {
      submitButton.innerHTML = isEdit
        ? '<i class="bi bi-save"></i> Actualizar'
        : '<i class="bi bi-save"></i> Guardar';
    }
  }

  /**
   * Rellena el formulario con los datos del proveedor.
   * @param {any} supplier Datos del proveedor.
   */
  function populateSupplierForm(supplier) {
    const form = document.getElementById("formSupplier");
    if (!form || !supplier) return;

    form.dataset.mode = "edit";

    const idField = document.getElementById("supplierId");
    if (idField) {
      idField.value = supplier.idSupplier || supplier.id || 0;
    }

    const documentField = document.getElementById("txtSupplierDocument");
    if (documentField) {
      documentField.value = supplier.document_raw || "";
    }

    const nameField = document.getElementById("txtSupplierName");
    if (nameField) {
      nameField.value = supplier.company_raw || "";
    }

    const phoneField = document.getElementById("txtSupplierPhone");
    if (phoneField) {
      phoneField.value = supplier.phone_raw || "";
    }

    const emailField = document.getElementById("txtSupplierEmail");
    if (emailField) {
      emailField.value = supplier.email_raw || "";
    }

    const addressField = document.getElementById("txtSupplierAddress");
    if (addressField) {
      addressField.value = supplier.direction_raw || "";
    }

    updateModalTexts(true);
  }

  /**
   * Extrae los datos de la fila asociada al botón clicado.
   * @param {HTMLElement} element Elemento que detonó el evento.
   * @returns {any}
   */
  function getRowDataFromElement(element) {
    if (!element || !suppliersTable) return null;

    const rowElement = element.closest("tr");
    if (!rowElement) return null;

    let data = suppliersTable.row(rowElement).data();

    if (!data && rowElement.classList.contains("child")) {
      const previous = rowElement.previousElementSibling;
      if (previous) {
        data = suppliersTable.row(previous).data();
      }
    }

    return data || null;
  }

  /**
   * Muestra el modal con el detalle del proveedor seleccionado.
   * @param {any} supplier Datos del proveedor.
   */
  function showSupplierDetail(supplier) {
    if (!supplier) return;

    const fields = {
      detailSupplierName: supplier.company_raw || supplier.company_name || "-",
      detailSupplierDocument: supplier.document_raw || "Sin documento",
      detailSupplierPhone: supplier.phone_raw || "Sin teléfono",
      detailSupplierEmail: supplier.email_raw || "Sin correo",
      detailSupplierAddress: supplier.direction_raw || "Sin dirección",
      detailSupplierStatus: supplier.status_text || "-",
    };

    Object.entries(fields).forEach(([id, value]) => {
      const element = document.getElementById(id);
      if (element) {
        element.textContent = value;
      }
    });

    showModal(detailModalElement);
  }

  /**
   * Envía la información del formulario para registrar o actualizar un proveedor.
   * @param {SubmitEvent} event Evento submit del formulario.
   */
  async function submitSupplier(event) {
    event.preventDefault();

    const form = event.currentTarget;
    if (!form) return;

    const token = getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message:
          "No se encontró el token de seguridad. Actualiza la página e inténtalo nuevamente.",
      });
      return;
    }

    const formData = new FormData(form);
    const supplierId = Number.parseInt(formData.get("supplierId") || "0", 10);
    const isEdit = supplierId > 0;

    const endpoint = isEdit
      ? `${base_url}/pos/Suppliers/updateSupplier`
      : `${base_url}/pos/Suppliers/setSupplier`;

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
        resetSupplierForm();
        hideModal(supplierModalElement);
        if (suppliersTable) {
          suppliersTable.ajax.reload(null, false);
        }
      }
    } catch (error) {
      console.error("Error guardando proveedor", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message:
          "No fue posible guardar la información del proveedor. Inténtalo nuevamente.",
      });
    }
  }

  /**
   * Solicita confirmación para eliminar o desactivar un proveedor.
   * @param {any} supplier Datos del proveedor.
   * @param {HTMLElement} button Botón que originó la acción.
   */
  function confirmDeleteSupplier(supplier, button) {
    if (!supplier || !button) return;

    if (
      isProtectedSupplierName(
        supplier.company_raw || supplier.company_name || ""
      )
    ) {
      showAlert({
        icon: "info",
        title: "Acción no permitida",
        message: "El proveedor predeterminado no puede eliminarse.",
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

    const supplierName =
      supplier.company_raw || supplier.company_name || "este proveedor";

    Swal.fire({
      title: "¿Eliminar proveedor?",
      html: `Se eliminará definitivamente <strong>${supplierName}</strong>. Esta acción no se puede deshacer.`,
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
          `${base_url}/pos/Suppliers/deleteSupplier`,
          {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              id: supplier.idSupplier || supplier.id || 0,
              token,
            }),
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

        if (data.status && suppliersTable) {
          suppliersTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error eliminando proveedor", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible eliminar el proveedor. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Registra los eventos del formulario y de la tabla de proveedores.
   */
  function registerEvents() {
    const form = document.getElementById("formSupplier");
    if (form) {
      form.addEventListener("submit", submitSupplier);
    }

    const openButton = document.getElementById("btnOpenSupplierModal");
    if (openButton) {
      openButton.addEventListener("click", () => {
        resetSupplierForm();
        showModal(supplierModalElement);
      });
    }

    const table = document.getElementById("supplierTable");
    if (table) {
      table.addEventListener("click", (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
          return;
        }

        const viewButton = target.closest(".view-supplier");
        if (viewButton) {
          event.preventDefault();
          const data = getRowDataFromElement(viewButton);
          if (data) {
            showSupplierDetail(data);
          }
          return;
        }

        const editButton = target.closest(".edit-supplier");
        if (editButton) {
          event.preventDefault();
          const data = getRowDataFromElement(editButton);
          if (data) {
            populateSupplierForm(data);
            showModal(supplierModalElement);
          }
          return;
        }

        const deleteButton = target.closest(".delete-supplier");
        if (deleteButton) {
          event.preventDefault();
          const data = getRowDataFromElement(deleteButton);
          if (data) {
            confirmDeleteSupplier(data, deleteButton);
          }
        }
      });
    }
  }

  /**
   * Inicializa la tabla de proveedores con DataTables.
   */
  function initSuppliersTable() {
    suppliersTable = $("#supplierTable").DataTable({
      ajax: {
        url: `${base_url}/pos/Suppliers/getSuppliers`,
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        { data: "actions", orderable: false, searchable: false },
        { data: "company_name" },
        { data: "document_number" },
        { data: "phone_number" },
        { data: "email" },
        { data: "status", orderable: false },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-secondary",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-success",
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-info text-white",
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6] },
        },
      ],
      columnDefs: [
        { targets: 0, className: "text-center" },
        { targets: 1, className: "text-center" },
        { targets: [3, 4, 5], className: "text-nowrap" },
        { targets: 6, className: "text-center" },
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
          .forEach((el) => {
            el.classList.add("pagination-sm");
          });
      },
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    supplierModalElement = document.getElementById("modalSupplier");
    detailModalElement = document.getElementById("modalSupplierDetail");

    registerEvents();
    initSuppliersTable();
  });
})();
