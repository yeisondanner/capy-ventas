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
      "#formSupplier button[type='submit']",
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
   * Muestra el modal con el detalle del proveedor seleccionado (Estilo Reporte).
   * @param {any} supplier Datos del proveedor.
   */
  async function showSupplierDetail(supplier) {
    if (!supplier) return;

    const supplierId = supplier.idSupplier || supplier.id || 0;
    if (!supplierId) return;

    try {
      const formData = new FormData();
      formData.append("supplierId", supplierId);

      const response = await fetch(`${base_url}/pos/Suppliers/getSupplier`, {
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
          message: res.message || "No se pudo cargar el detalle del proveedor",
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

      // Detalles Proveedor
      document.getElementById("report_supplier_name").textContent =
        d.company_name || "--";
      document.getElementById("report_supplier_document").textContent =
        d.document_number || "--";
      document.getElementById("report_supplier_phone").textContent =
        d.phone_number || "--";
      document.getElementById("report_supplier_email").textContent =
        d.email || "--";
      document.getElementById("report_supplier_address").textContent =
        d.direction || "--";

      // Estado Badge
      const statusEl = document.getElementById("report_supplier_status");
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
      console.error("Error cargando detalle proveedor", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible cargar el detalle del proveedor.",
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
      width: originalElement.offsetWidth + "px",
      height: "auto",
      zIndex: "-1",
      overflow: "visible",
      backgroundColor: "#ffffff",
    });

    // 3. Insertar el clon en el documento
    document.body.appendChild(clone);

    // 4. Generar el canvas
    if (typeof html2canvas === "undefined") {
      console.error("html2canvas no está definido.");
      document.body.removeChild(clone);
      return;
    }

    html2canvas(clone, {
      scale: 2,
      useCORS: true,
      scrollY: -window.scrollY,
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
        if (document.body.contains(clone)) {
          document.body.removeChild(clone);
        }
      });
  };

  function registerExportButton() {
    const btn = document.getElementById("btnDownloadSupplierPng");
    if (btn) {
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);

      newBtn.addEventListener("click", () => {
        const name =
          document.getElementById("report_supplier_name")?.textContent ||
          "Proveedor";
        const cleanName = name.replace(/[^a-z0-9]/gi, "_").toLowerCase();
        exportToPng(
          "supplierReportContainer",
          `Ficha_Proveedor_${cleanName}.png`,
        );
      });
    }
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
      if (data.url) {
        setTimeout(() => {
          window.location.href = data.url;
        }, 1000);
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
        supplier.company_raw || supplier.company_name || "",
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
      processing: true,
      ajax: {
        url: `${base_url}/pos/Suppliers/getSuppliers`,
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
        { data: "company_name" },
        { data: "document_number" },
        {
          data: "phone_number",
          render: function (data, type, row) {
            return `<a href="tel:${data}" class="text-decoration-none text-success" title="Llamar a ${row.company_name}"> <i class="bi bi-telephone text-success"></i> ${data}</a>`;
          },
        },
        {
          data: "email",
          render: function (data, type, row) {
            return `<a href="mailto:${data}" class="text-decoration-none text-primary" title="Enviar correo a ${row.company_name}"> <i class="bi bi-envelope text-primary"></i> ${data}</a>`;
          },
        },
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
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info",
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Proveedores",
          exportOptions: { columns: [0, 2, 3, 4, 5] },
        },
      ],
      columnDefs: [
        { targets: 0, className: "text-center" },
        { targets: 1, className: "text-center" },
        { targets: [3, 4, 5], className: "text-nowrap" },
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
    supplierModalElement = document.getElementById("modalSupplier");
    detailModalElement = document.getElementById("modalSupplierDetail");

    registerEvents();
    initSuppliersTable();
    registerExportButton();
  });
})();
