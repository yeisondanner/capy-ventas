(function () {
  "use strict";
  let productsTable;
  let modalCreate;
  let modalUpdate;
  let modalCategory;
  let modalReport;
  let categoryList = [];
  let cachedCategories = [];
  let cachedMeasurements = [];
  let cachedSuppliers = [];

  const rootUrl = base_url;
  const PROTECTED_CATEGORY_NAME = "Sin Categoría";
  const PROTECTED_CATEGORY_KEY = normalizeCategoryName(PROTECTED_CATEGORY_NAME);

  /**
   * Normaliza un nombre de categoría eliminando tildes y espacios duplicados.
   * @param {string} value Texto a normalizar.
   * @returns {string}
   */
  function normalizeCategoryName(value) {
    if (!value) return "";

    let normalized = value.toString().trim();
    if (!normalized) return "";

    if (typeof normalized.normalize === "function") {
      normalized = normalized
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    }

    normalized = normalized.toLowerCase();

    return normalized.replace(/\s+/g, " ");
  }

  /**
   * Indica si el nombre corresponde a la categoría protegida por defecto.
   * @param {string} value Nombre a evaluar.
   * @returns {boolean}
   */
  function isProtectedCategoryName(value) {
    if (!value) return false;
    return normalizeCategoryName(value) === PROTECTED_CATEGORY_KEY;
  }

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
   * Actualiza el contenido de un elemento dentro del modal de reporte.
   *
   * @param {string} elementId Identificador del elemento a actualizar.
   * @param {string} value Texto que se mostrará.
   */
  function setReportField(elementId, value) {
    const element = document.getElementById(elementId);
    if (!element) return;
    element.textContent = value;
  }

  /**
   * Configura el estado visual del producto dentro del modal de reporte.
   *
   * @param {string} status Estado del producto (Activo/Inactivo).
   */
  function renderReportStatus(status) {
    const statusElement = document.getElementById("reportProductStatus");
    if (!statusElement) return;

    statusElement.classList.remove("text-success", "text-danger", "text-muted");

    if (status === "Activo") {
      statusElement.textContent = "Activo";
      statusElement.classList.add("text-success");
    } else if (status === "Inactivo") {
      statusElement.textContent = "Inactivo";
      statusElement.classList.add("text-danger");
    } else {
      statusElement.textContent = status || "Estado desconocido";
      statusElement.classList.add("text-muted");
    }
  }

  /**
   * Rellena los datos del modal de reporte con la información del producto.
   *
   * @param {any} product Objeto que contiene la información del producto.
   */
  function renderProductReport(product) {
    if (!product) return;

    setReportField("reportProductName", product.name || "Producto sin nombre");
    setReportField(
      "reportProductCategory",
      product.category_name || "Sin categoría asignada"
    );
    setReportField(
      "reportProductSupplier",
      product.supplier_name || "Sin proveedor asignado"
    );
    setReportField(
      "reportProductMeasurement",
      product.measurement_name || "Sin unidad registrada"
    );
    const currency =
      typeof product.currency_symbol === "string" ? product.currency_symbol : "";
    const stockText =
      product.stock_text ||
      `${Number(product.stock || 0).toFixed(2)}${
        product.measurement_name ? ` ${product.measurement_name}` : ""
      }`;
    const purchaseText =
      product.purchase_price_text ||
      `${currency ? `${currency} ` : ""}${Number(
        product.purchase_price || 0
      ).toFixed(2)}`;
    const saleText =
      product.sales_price_text ||
      `${currency ? `${currency} ` : ""}${Number(
        product.sales_price || 0
      ).toFixed(2)}`;

    setReportField("reportProductStock", stockText);
    setReportField("reportProductPurchase", purchaseText);
    setReportField("reportProductSale", saleText);

    const description =
      typeof product.description === "string" && product.description.trim()
        ? product.description
        : "Sin descripción registrada.";
    setReportField("reportProductDescription", description);

    renderReportStatus(product.status || "");
  }

  /**
   * Llena un elemento select con las opciones proporcionadas.
   * @param {HTMLSelectElement} select
   * @param {Array} data
   * @param {string} placeholder
   */
  function populateSelect(select, data, placeholder) {
    if (!select) return;
    select.innerHTML = "";
    const option = document.createElement("option");
    option.value = "";
    option.textContent = placeholder;
    option.disabled = true;
    option.selected = true;
    select.appendChild(option);

    data.forEach((item) => {
      const opt = document.createElement("option");
      opt.value = item.id;
      opt.textContent = item.name;
      select.appendChild(opt);
    });
  }

  /**
   * Inicializa las referencias de los modales.
   */
  function initModals() {
    modalCreate = document.getElementById("modalProduct");
    modalUpdate = document.getElementById("modalUpdateProduct");
    modalCategory = document.getElementById("modalCategory");
    modalReport = document.getElementById("modalProductReport");
  }

  /**
   * Carga las categorías, proveedores y unidades de medida desde el servidor.
   */
  async function loadSelectors() {
    try {
      const [categoriesResponse, measurementsResponse, suppliersResponse] =
        await Promise.all([
          fetch(`${base_url}/pos/Inventory/getCategories`),
          fetch(`${base_url}/pos/Inventory/getMeasurements`),
          fetch(`${base_url}/pos/Inventory/getSuppliers`),
        ]);

      if (!categoriesResponse.ok) {
        throw new Error(`Categorías: ${categoriesResponse.status}`);
      }
      if (!measurementsResponse.ok) {
        throw new Error(`Unidades: ${measurementsResponse.status}`);
      }
      if (!suppliersResponse.ok) {
        throw new Error(`Proveedores: ${suppliersResponse.status}`);
      }

      const categoriesJson = await categoriesResponse.json();
      const measurementsJson = await measurementsResponse.json();
      const suppliersJson = await suppliersResponse.json();

      if (!categoriesJson.status) {
        throw new Error(
          categoriesJson.message || "No fue posible cargar las categorías"
        );
      }
      if (!measurementsJson.status) {
        throw new Error(
          measurementsJson.message ||
            "No fue posible cargar las unidades de medida"
        );
      }
      if (!suppliersJson.status) {
        throw new Error(
          suppliersJson.message || "No fue posible cargar los proveedores"
        );
      }

      cachedCategories = categoriesJson.data.map((item) => ({
        id: item.idCategory,
        name: item.name,
      }));
      cachedMeasurements = measurementsJson.data.map((item) => ({
        id: item.idMeasurement,
        name: item.name,
      }));
      cachedSuppliers = suppliersJson.data.map((item) => ({
        id: item.idSupplier,
        name: item.company_name,
      }));

      populateSelect(
        document.getElementById("txtProductCategory"),
        cachedCategories,
        "Selecciona una categoría"
      );
      populateSelect(
        document.getElementById("update_txtProductCategory"),
        cachedCategories,
        "Selecciona una categoría"
      );

      populateSelect(
        document.getElementById("txtProductSupplier"),
        cachedSuppliers,
        "Selecciona un proveedor"
      );
      populateSelect(
        document.getElementById("update_txtProductSupplier"),
        cachedSuppliers,
        "Selecciona un proveedor"
      );

      populateSelect(
        document.getElementById("txtProductMeasurement"),
        cachedMeasurements,
        "Selecciona una unidad"
      );
      populateSelect(
        document.getElementById("update_txtProductMeasurement"),
        cachedMeasurements,
        "Selecciona una unidad"
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
   * Muestra las categorías disponibles dentro del listado del modal.
   * @param {Array<{idCategory:number,name:string,status:string}>} categories
   */
  function renderCategoryList(categories) {
    const list = document.getElementById("categoryList");
    if (!list) return;

    list.innerHTML = "";

    if (!Array.isArray(categories) || !categories.length) {
      const emptyItem = document.createElement("li");
      emptyItem.className = "list-group-item text-center text-muted";
      emptyItem.textContent = "No hay categorías registradas.";
      list.appendChild(emptyItem);
      return;
    }

    categories.forEach((category) => {
      const item = document.createElement("li");
      item.className =
        "list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2";

      const infoWrapper = document.createElement("div");
      infoWrapper.className = "d-flex flex-column flex-grow-1";

      const nameRow = document.createElement("div");
      nameRow.className = "d-flex align-items-center gap-2";

      const nameText = document.createElement("span");
      nameText.className = "fw-semibold";
      nameText.textContent = category.name;

      const statusBadge = document.createElement("span");
      const isActive = category.status === "Activo";
      const isProtected = isProtectedCategoryName(category.name);
      statusBadge.className = `badge ${isActive ? "bg-success" : "bg-secondary"}`;
      statusBadge.textContent = category.status;

      nameRow.appendChild(nameText);
      nameRow.appendChild(statusBadge);
      infoWrapper.appendChild(nameRow);

      item.appendChild(infoWrapper);

      if (!isProtected) {
        const actionGroup = document.createElement("div");
        actionGroup.className = "btn-group btn-group-sm";

        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.className = "btn btn-outline-primary text-primary edit-category";
        editButton.setAttribute("data-id", `${category.idCategory}`);
        editButton.innerHTML = '<i class="bi bi-pencil-square"></i>';

        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className =
          "btn btn-outline-danger text-danger delete-category";
        deleteButton.setAttribute("data-id", `${category.idCategory}`);
        deleteButton.setAttribute("data-name", category.name);
        deleteButton.innerHTML = '<i class="bi bi-trash"></i>';

        actionGroup.appendChild(editButton);
        actionGroup.appendChild(deleteButton);

        item.appendChild(actionGroup);
      }

      list.appendChild(item);
    });
  }

  /**
   * Obtiene el listado de categorías para el mantenimiento.
   * @param {boolean} showError Mensaje de error en caso de fallo.
   * @returns {Promise<Array<{idCategory:number,name:string,status:string}>>}
   */
  async function refreshCategoryList(showError = true) {
    try {
      const response = await fetch(`${base_url}/pos/Inventory/getCategoryList`);
      if (!response.ok) {
        throw new Error(`Error ${response.status}`);
      }

      const data = await response.json();
      if (!data.status) {
        if (showError) {
          showAlert({
            icon: "error",
            title: data.title || "Ocurrió un error",
            message:
              data.message ||
              "No fue posible cargar las categorías registradas. Actualiza la página e inténtalo nuevamente.",
          });
        }
        categoryList = [];
        renderCategoryList(categoryList);
        return [];
      }

      categoryList = Array.isArray(data.data) ? data.data : [];
      renderCategoryList(categoryList);
      return categoryList;
    } catch (error) {
      console.error("Error cargando categorías", error);
      if (showError) {
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible cargar las categorías registradas. Actualiza la página e inténtalo nuevamente.",
        });
      }
      categoryList = [];
      renderCategoryList(categoryList);
      return [];
    }
  }

  /**
   * Abre el modal de categorías y carga el listado disponible.
   */
  async function openCategoryModal() {
    const list = document.getElementById("categoryList");
    if (list) {
      list.innerHTML = "";
      const loadingItem = document.createElement("li");
      loadingItem.className = "list-group-item text-center text-muted";
      loadingItem.textContent = "Cargando categorías...";
      list.appendChild(loadingItem);
    }

    showModal(modalCategory);
    await refreshCategoryList(true);
  }

  /**
   * Gestiona el registro de nuevas categorías desde el formulario del modal.
   */
  function handleCreateCategory() {
    const form = document.getElementById("formCreateCategory");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      const nameValue = (formData.get("txtCategoryName") || "").toString().trim();
      if (!nameValue) {
        showAlert({
          icon: "warning",
          title: "Nombre requerido",
          message: "Debes ingresar el nombre de la categoría.",
        });
        return;
      }

      formData.set("txtCategoryName", nameValue);

      try {
        const response = await fetch(`${base_url}/pos/Inventory/setCategory`, {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }

        const data = await response.json();
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title: data.title || (data.status ? "Categoría registrada" : "Ocurrió un error"),
          message: data.message || "",
        });

        if (data.status) {
          form.reset();
          await refreshCategoryList(false);
          await loadSelectors();
        }
      } catch (error) {
        console.error("Error registrando categoría", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible registrar la categoría. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Solicita la edición del nombre de una categoría utilizando SweetAlert.
   * @param {number} categoryId Identificador de la categoría.
   */
  function promptCategoryEdition(categoryId) {
    if (!Number.isInteger(categoryId) || categoryId <= 0) {
      showAlert({
        icon: "warning",
        title: "Categoría inválida",
        message: "No fue posible identificar la categoría seleccionada.",
      });
      return;
    }

    const currentCategory = categoryList.find(
      (item) => Number.parseInt(item.idCategory, 10) === categoryId
    );

    if (!currentCategory) {
      showAlert({
        icon: "warning",
        title: "Categoría no encontrada",
        message: "La categoría seleccionada no se encuentra en el listado actual.",
      });
      return;
    }

    if (isProtectedCategoryName(currentCategory.name)) {
      showAlert({
        icon: "info",
        title: "Acción no permitida",
        message: "La categoría predeterminada no puede modificarse.",
      });
      return;
    }

    Swal.fire({
      title: "Actualizar categoría",
      input: "text",
      inputLabel: "Nombre",
      inputValue: currentCategory.name,
      inputAttributes: {
        maxlength: "255",
        autocapitalize: "words",
      },
      showCancelButton: true,
      confirmButtonText: "Guardar",
      cancelButtonText: "Cancelar",
      focusCancel: true,
      preConfirm: async (value) => {
        const newName = (value || "").trim();
        if (!newName) {
          Swal.showValidationMessage("Debes ingresar un nombre para la categoría.");
          return false;
        }

        const token = getSecurityToken();
        if (!token) {
          Swal.showValidationMessage(
            "No se encontró el token de seguridad. Actualiza la página e inténtalo nuevamente."
          );
          return false;
        }

        const formData = new FormData();
        formData.append("token", token);
        formData.append("categoryId", `${categoryId}`);
        formData.append("txtCategoryName", newName);

        try {
          const response = await fetch(`${base_url}/pos/Inventory/updateCategory`, {
            method: "POST",
            body: formData,
          });

          if (!response.ok) {
            throw new Error(`Error ${response.status}`);
          }

          const data = await response.json();
          if (!data.status) {
            Swal.showValidationMessage(
              data.message || "No fue posible actualizar la categoría."
            );
            return false;
          }

          return data;
        } catch (error) {
          console.error("Error actualizando categoría", error);
          Swal.showValidationMessage(
            "No fue posible actualizar la categoría. Inténtalo nuevamente."
          );
          return false;
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    }).then(async (result) => {
      if (result.isConfirmed && result.value) {
        showAlert({
          icon: result.value.icon || (result.value.status ? "success" : "error"),
          title:
            result.value.title ||
            (result.value.status ? "Categoría actualizada" : "Ocurrió un error"),
          message:
            result.value.message ||
            (result.value.status
              ? "Los cambios fueron guardados correctamente."
              : "No fue posible actualizar la categoría."),
        });
        await refreshCategoryList(false);
        await loadSelectors();
      }
    });
  }

  /**
   * Confirma con el usuario la eliminación de una categoría.
   * @param {number} categoryId Identificador de la categoría.
   * @param {string} categoryName Nombre de la categoría.
   */
  function confirmDeleteCategory(categoryId, categoryName) {
    if (!Number.isInteger(categoryId) || categoryId <= 0) {
      showAlert({
        icon: "warning",
        title: "Categoría inválida",
        message: "No fue posible identificar la categoría seleccionada.",
      });
      return;
    }

    const currentCategory = categoryList.find(
      (item) => Number.parseInt(item.idCategory, 10) === categoryId
    );

    if (
      (currentCategory && isProtectedCategoryName(currentCategory.name)) ||
      (!currentCategory && isProtectedCategoryName(categoryName))
    ) {
      showAlert({
        icon: "info",
        title: "Acción no permitida",
        message: "La categoría predeterminada no puede eliminarse.",
      });
      return;
    }

    const token = getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message:
          "No fue posible validar la solicitud de eliminación. Actualiza la página e inténtalo nuevamente.",
      });
      return;
    }

    const safeName = categoryName
      ? `<strong>${categoryName}</strong>`
      : "esta categoría";

    Swal.fire({
      title: "¿Eliminar categoría?",
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
        const response = await fetch(`${base_url}/pos/Inventory/deleteCategory`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: categoryId, token }),
        });

        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }

        const data = await response.json();
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title: data.title || (data.status ? "Operación exitosa" : "Ocurrió un error"),
          message: data.message || "",
        });

        if (data.status) {
          await refreshCategoryList(false);
          await loadSelectors();
        }
      } catch (error) {
        console.error("Error eliminando categoría", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible eliminar la categoría. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Registra los listeners para las acciones del listado de categorías.
   */
  function registerCategoryActions() {
    const list = document.getElementById("categoryList");
    if (!list) return;

    list.addEventListener("click", (event) => {
      const editButton = event.target.closest(".edit-category");
      if (editButton) {
        event.preventDefault();
        const id = Number.parseInt(editButton.getAttribute("data-id") || "0", 10);
        promptCategoryEdition(id);
        return;
      }

      const deleteButton = event.target.closest(".delete-category");
      if (deleteButton) {
        event.preventDefault();
        const id = Number.parseInt(deleteButton.getAttribute("data-id") || "0", 10);
        const name = deleteButton.getAttribute("data-name") || "";
        confirmDeleteCategory(id, name);
      }
    });
  }

  /**
   * Configura la tabla de productos con DataTables.
   */
  function initTable() {
    productsTable = $("#table").DataTable({
      ajax: {
        url: `${base_url}/pos/Inventory/getProducts`,
        dataSrc: "",
      },
      columns: [
        { data: "cont" },
        { data: "actions", orderable: false, searchable: false },
        { data: "name" },
        { data: "category" },
        { data: "supplier" },
        { data: "stock" },
        { data: "sales_price" },
        { data: "purchase_price" },
        { data: "status", orderable: false },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-secondary",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-success",
          title: "Productos",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-info text-white",
          title: "Productos",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-danger",
          orientation: "portrait",
          pageSize: "A4",
          title: "Productos",
          exportOptions: { columns: [0, 2, 3, 4, 5, 6, 7, 8] },
        },
      ],
      columnDefs: [
        { targets: 0, className: "text-center" },
        { targets: 1, className: "text-center" },
        { targets: [5, 6, 7], className: "text-end" },
        { targets: 8, className: "text-center" },
      ],
      responsive: true,
      destroy: true,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${rootUrl}/Assets/js/libraries/Admin/Spanish-datatables.json`,
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
    const form = document.getElementById("formSaveProduct");
    if (!form) return;

    if (
      !cachedCategories.length ||
      !cachedSuppliers.length ||
      !cachedMeasurements.length
    ) {
      await loadSelectors();
    }

    if (
      !cachedCategories.length ||
      !cachedSuppliers.length ||
      !cachedMeasurements.length
    ) {
      showAlert({
        icon: "warning",
        title: "Datos incompletos",
        message:
          "Antes de registrar un producto debes contar con categorías, proveedores y unidades disponibles.",
      });
      return;
    }

    form.reset();
    populateSelect(
      document.getElementById("txtProductCategory"),
      cachedCategories,
      "Selecciona una categoría"
    );
    populateSelect(
      document.getElementById("txtProductSupplier"),
      cachedSuppliers,
      "Selecciona un proveedor"
    );
    populateSelect(
      document.getElementById("txtProductMeasurement"),
      cachedMeasurements,
      "Selecciona una unidad"
    );

    showModal(modalCreate);
  }

  /**
   * Envía el formulario de creación de productos.
   */
  function handleCreate() {
    const form = document.getElementById("formSaveProduct");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      try {
        const response = await fetch(`${base_url}/pos/Inventory/setProduct`, {
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
          populateSelect(
            document.getElementById("txtProductCategory"),
            cachedCategories,
            "Selecciona una categoría"
          );
          populateSelect(
            document.getElementById("txtProductSupplier"),
            cachedSuppliers,
            "Selecciona un proveedor"
          );
          populateSelect(
            document.getElementById("txtProductMeasurement"),
            cachedMeasurements,
            "Selecciona una unidad"
          );
          hideModal(modalCreate);
          productsTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al registrar producto", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible registrar el producto. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Atiende el envío del formulario de actualización de productos.
   */
  function handleUpdate() {
    const form = document.getElementById("formUpdateProduct");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      try {
        const response = await fetch(`${base_url}/pos/Inventory/updateProduct`, {
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
          hideModal(modalUpdate);
          productsTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al actualizar producto", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message:
            "No fue posible actualizar el producto. Inténtalo nuevamente.",
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
   * Confirma con SweetAlert la eliminación del producto seleccionado.
   *
   * @param {number} productId Identificador del producto.
   * @param {string} productName Nombre del producto.
   * @param {string | null} productToken Token CSRF asociado al botón.
   */
  function confirmDeleteProduct(productId, productName, productToken) {
    if (!Number.isInteger(productId) || productId <= 0) {
      showAlert({
        icon: "warning",
        title: "Producto inválido",
        message: "No fue posible identificar el producto seleccionado.",
      });
      return;
    }

    const token = productToken || getSecurityToken();
    if (!token) {
      showAlert({
        icon: "error",
        title: "Token ausente",
        message:
          "No fue posible validar la solicitud de eliminación. Actualiza la página e inténtalo nuevamente.",
      });
      return;
    }

    const safeName = productName
      ? `<strong>${productName}</strong>`
      : "este producto";

    Swal.fire({
      title: "¿Eliminar producto?",
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
        const response = await fetch(`${base_url}/pos/Inventory/deleteProduct`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: productId, token }),
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
          productsTable.ajax.reload(null, false);
        }
      } catch (error) {
        console.error("Error al eliminar producto", error);
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible eliminar el producto. Inténtalo nuevamente.",
        });
      }
    });
  }

  /**
   * Obtiene la información de un producto y prepara el formulario de edición.
   * @param {number} productId
   */
  async function loadProductForEdition(productId) {
    try {
      const response = await fetch(
        `${base_url}/pos/Inventory/getProduct?id=${productId}`
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
            "No fue posible obtener la información del producto.",
        });
        return;
      }

      const form = document.getElementById("formUpdateProduct");
      if (!form) return;

      const product = data.data;
      form.reset();

      populateSelect(
        document.getElementById("update_txtProductCategory"),
        cachedCategories,
        "Selecciona una categoría"
      );
      populateSelect(
        document.getElementById("update_txtProductSupplier"),
        cachedSuppliers,
        "Selecciona un proveedor"
      );
      populateSelect(
        document.getElementById("update_txtProductMeasurement"),
        cachedMeasurements,
        "Selecciona una unidad"
      );

      document.getElementById("update_txtProductId").value = product.idProduct;
      document.getElementById("update_txtProductName").value = product.name;
      document.getElementById(
        "update_txtProductCategory"
      ).value = `${product.category_id}`;
      document.getElementById(
        "update_txtProductSupplier"
      ).value = `${product.supplier_id}`;
      document.getElementById(
        "update_txtProductMeasurement"
      ).value = `${product.measurement_id}`;
      document.getElementById("update_txtProductStatus").value = product.status;
      document.getElementById("update_txtProductStock").value = product.stock;
      document.getElementById("update_txtProductPurchasePrice").value =
        product.purchase_price;
      document.getElementById("update_txtProductSalesPrice").value =
        product.sales_price;
      document.getElementById("update_txtProductDescription").value =
        product.description || "";

      showModal(modalUpdate);
    } catch (error) {
      console.error("Error obteniendo producto", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible cargar la información del producto.",
      });
    }
  }

  /**
   * Obtiene los datos del producto seleccionado y muestra el modal de reporte.
   *
   * @param {number} productId Identificador del producto.
   */
  async function openProductReport(productId) {
    if (!Number.isInteger(productId) || productId <= 0) {
      showAlert({
        icon: "warning",
        title: "Producto inválido",
        message: "No fue posible identificar el producto seleccionado.",
      });
      return;
    }

    try {
      const response = await fetch(
        `${base_url}/pos/Inventory/getProduct?id=${productId}`
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
            data.message || "No fue posible obtener la información del producto.",
        });
        return;
      }

      renderProductReport(data.data);
      showModal(modalReport);
    } catch (error) {
      console.error("Error obteniendo reporte del producto", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible cargar el reporte del producto.",
      });
    }
  }

  /**
   * Configura los listeners de la tabla para acciones de reporte, edición y eliminación.
   */
  function registerTableActions() {
    document.addEventListener("click", (event) => {
      const reportButton = event.target.closest(".report-product");
      if (reportButton) {
        event.preventDefault();
        const id = parseInt(reportButton.getAttribute("data-id") || "0", 10);
        if (Number.isInteger(id) && id > 0) {
          openProductReport(id);
        }
        return;
      }

      const editButton = event.target.closest(".edit-product");
      if (editButton) {
        event.preventDefault();
        const id = parseInt(editButton.getAttribute("data-id"), 10);
        if (Number.isInteger(id) && id > 0) {
          loadProductForEdition(id);
        }
        return;
      }

      const deleteButton = event.target.closest(".delete-product");
      if (deleteButton) {
        event.preventDefault();
        const id = parseInt(deleteButton.getAttribute("data-id") || "0", 10);
        const name = deleteButton.getAttribute("data-name") || "";
        const token = deleteButton.getAttribute("data-token") || null;
        confirmDeleteProduct(id, name, token);
      }
    });
  }

  window.addEventListener("DOMContentLoaded", async () => {
    initModals();
    await loadSelectors();
    initTable();
    registerTableActions();
    handleCreate();
    handleUpdate();
    handleCreateCategory();
    registerCategoryActions();

    const openButton = document.getElementById("btnOpenProductModal");
    if (openButton) {
      openButton.addEventListener("click", openCreateModal);
    }

    const openCategoryButton = document.getElementById("btnOpenCategoryModal");
    if (openCategoryButton) {
      openCategoryButton.addEventListener("click", openCategoryModal);
    }
  });
})();
