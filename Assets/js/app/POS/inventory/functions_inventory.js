let productsTable;
let modalCreate;
let modalUpdate;
let modalDelete;
let cachedCategories = [];
let cachedMeasurements = [];

const rootUrl = base_url.replace(/\/?pos$/, '');

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
  modalDelete = document.getElementById("modalDeleteProduct");
}

/**
 * Carga las categorías y unidades de medida desde el servidor.
 */
async function loadSelectors() {
  try {
    const [categoriesResponse, measurementsResponse] = await Promise.all([
      fetch(`${base_url}/inventory/getCategories`),
      fetch(`${base_url}/inventory/getMeasurements`),
    ]);

    if (!categoriesResponse.ok) {
      throw new Error(`Categorías: ${categoriesResponse.status}`);
    }
    if (!measurementsResponse.ok) {
      throw new Error(`Unidades: ${measurementsResponse.status}`);
    }

    const categoriesJson = await categoriesResponse.json();
    const measurementsJson = await measurementsResponse.json();

    if (!categoriesJson.status) {
      throw new Error(categoriesJson.message || "No fue posible cargar las categorías");
    }
    if (!measurementsJson.status) {
      throw new Error(measurementsJson.message || "No fue posible cargar las unidades de medida");
    }

    cachedCategories = categoriesJson.data.map((item) => ({
      id: item.idCategory,
      name: item.name,
    }));
    cachedMeasurements = measurementsJson.data.map((item) => ({
      id: item.idMeasurement,
      name: item.name,
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
      message: "No fue posible cargar las listas de apoyo. Actualiza la página e inténtalo nuevamente.",
    });
  }
}

/**
 * Configura la tabla de productos con DataTables.
 */
function initTable() {
  productsTable = $("#table").DataTable({
    ajax: {
      url: `${base_url}/inventory/getProducts`,
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "actions", orderable: false, searchable: false },
      { data: "name" },
      { data: "category" },
      { data: "measurement" },
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
      document.querySelectorAll(".dataTables_paginate > .pagination").forEach((el) => {
        el.classList.add("pagination-sm");
      });
    },
  });
}

/**
 * Reinicia los formularios y muestra el modal de registro.
 */
function openCreateModal() {
  const form = document.getElementById("formSaveProduct");
  if (!form) return;

  form.reset();
  populateSelect(
    document.getElementById("txtProductCategory"),
    cachedCategories,
    "Selecciona una categoría"
  );
  populateSelect(
    document.getElementById("txtProductMeasurement"),
    cachedMeasurements,
    "Selecciona una unidad"
  );

  if (!cachedCategories.length || !cachedMeasurements.length) {
    showAlert({
      icon: "warning",
      title: "Datos incompletos",
      message: "Antes de registrar un producto debes contar con categorías y unidades disponibles.",
    });
    return;
  }

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
      const response = await fetch(`${base_url}/inventory/setProduct`, {
        method: "POST",
        body: formData,
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
        form.reset();
        populateSelect(
          document.getElementById("txtProductCategory"),
          cachedCategories,
          "Selecciona una categoría"
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
        message: "No fue posible registrar el producto. Inténtalo nuevamente.",
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
      const response = await fetch(`${base_url}/inventory/updateProduct`, {
        method: "POST",
        body: formData,
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
        hideModal(modalUpdate);
        productsTable.ajax.reload(null, false);
      }
    } catch (error) {
      console.error("Error al actualizar producto", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No fue posible actualizar el producto. Inténtalo nuevamente.",
      });
    }
  });
}

/**
 * Envía la solicitud de eliminación del producto seleccionado.
 */
function handleDelete() {
  const confirmButton = document.getElementById("confirmDeleteProduct");
  if (!confirmButton) return;

  confirmButton.addEventListener("click", async () => {
    const id = confirmButton.getAttribute("data-id");
    const token = confirmButton.getAttribute("data-token");

    if (!id) {
      showAlert({
        icon: "warning",
        title: "Producto no seleccionado",
        message: "Selecciona un producto antes de confirmar la eliminación.",
      });
      return;
    }

    try {
      const response = await fetch(`${base_url}/inventory/deleteProduct`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, token }),
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
        confirmButton.removeAttribute("data-id");
        hideModal(modalDelete);
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
    const response = await fetch(`${base_url}/inventory/getProduct?id=${productId}`);
    if (!response.ok) {
      throw new Error(`Error ${response.status}`);
    }

    const data = await response.json();
    if (!data.status) {
      showAlert({
        icon: "error",
        title: data.title || "Ocurrió un error",
        message: data.message || "No fue posible obtener la información del producto.",
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
      document.getElementById("update_txtProductMeasurement"),
      cachedMeasurements,
      "Selecciona una unidad"
    );

    document.getElementById("update_txtProductId").value = product.idProduct;
    document.getElementById("update_txtProductName").value = product.name;
    document.getElementById("update_txtProductCategory").value = `${product.category_id}`;
    document.getElementById("update_txtProductMeasurement").value = `${product.measurement_id}`;
    document.getElementById("update_txtProductStatus").value = product.status;
    document.getElementById("update_txtProductStock").value = product.stock;
    document.getElementById("update_txtProductPurchasePrice").value = product.purchase_price;
    document.getElementById("update_txtProductSalesPrice").value = product.sales_price;
    document.getElementById("update_txtProductDescription").value = product.description || "";

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
 * Configura los listeners de la tabla para acciones de edición y eliminación.
 */
function registerTableActions() {
  document.addEventListener("click", (event) => {
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
      const id = deleteButton.getAttribute("data-id");
      const name = deleteButton.getAttribute("data-name") || "";
      const confirmButton = document.getElementById("confirmDeleteProduct");
      const deleteText = document.getElementById("txtDeleteProduct");

      if (confirmButton) {
        confirmButton.setAttribute("data-id", id || "");
      }
      if (deleteText) {
        deleteText.innerHTML = name ? `<strong>${name}</strong>` : "";
      }

      showModal(modalDelete);
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
  handleDelete();

  const openButton = document.getElementById("btnOpenProductModal");
  if (openButton) {
    openButton.addEventListener("click", openCreateModal);
  }
});
