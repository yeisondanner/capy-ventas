(function () {
  "use strict";

  var treeviewMenu = $(".app-menu");

  // Toggle Sidebar
  $('[data-toggle="sidebar"]').click(function (event) {
    event.preventDefault();
    $(".app").toggleClass("sidenav-toggled");
  });

  // Activate sidebar treeview toggle
  $("[data-toggle='treeview']").click(function (event) {
    event.preventDefault();
    if (!$(this).parent().hasClass("is-expanded")) {
      treeviewMenu
        .find("[data-toggle='treeview']")
        .parent()
        .removeClass("is-expanded");
    }
    $(this).parent().toggleClass("is-expanded");
  });
})();
const formAddBusiness = document.getElementById("formAddBusiness");
const btnAddBusiness = document.getElementById("btnAddBusiness");
/**
 * Creamos una funcion de tipos de alertas con sweetalert2
 */
function showAlert(data = {}, type = "float") {
  switch (type) {
    case "float":
      Swal.fire({
        icon: data.icon ?? "success",
        title: data.title ?? "Satisfactorio",
        text: data.message ?? "Conexión exitosa",
        html: data.html ?? "",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: data.timer ?? 2500,
        timerProgressBar: true,
      });
      break;

    default:
      break;
  }
}

/**
 * Inicializa la gestión de negocios en el panel lateral.
 */
function initBusinessManagement() {
  const dropdownList = document.getElementById("businessListDropdown");
  const form = document.getElementById("formAddBusiness");
  const tokenInput = document.getElementById("businessToken");

  if (!dropdownList && !form) return;

  const token = tokenInput ? tokenInput.value : "";

  loadBusinessTypes();
  loadUserBusinesses();

  /*if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      createBusiness(form, token);
    });
  }*/

  if (dropdownList) {
    dropdownList.addEventListener("click", function (event) {
      const item = event.target.closest("[data-business-id]");
      if (!item) return;
      event.preventDefault();
      const businessId = item.getAttribute("data-business-id");
      setActiveBusiness(businessId, token);
    });
  }
}

/**
 * Carga los negocios del usuario y los pinta en el selector.
 */
function loadUserBusinesses() {
  const dropdownList = document.getElementById("businessListDropdown");
  if (!dropdownList) return;

  fetch(`${base_url}/pos/Business/getBusinesses`)
    .then((response) => response.json())
    .then((result) => {
      if (!result?.status) {
        renderBusinessFallback(dropdownList);
        return;
      }
      renderBusinessList(dropdownList, result.data || []);
    })
    .catch(() => renderBusinessFallback(dropdownList));
}

/**
 * Muestra un mensaje de vacío o error en la lista de negocios.
 * @param {HTMLElement} dropdownList
 */
function renderBusinessFallback(dropdownList) {
  dropdownList.innerHTML = `
    <li class="px-3 py-2 text-muted small">No encontramos negocios registrados.</li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addBusinessModal">Agregar negocio</a></li>
  `;
}

/**
 * Dibuja la lista de negocios en el dropdown.
 * @param {HTMLElement} dropdownList
 * @param {Array} businesses
 */
function renderBusinessList(dropdownList, businesses) {
  if (!dropdownList) return;

  if (!Array.isArray(businesses) || businesses.length === 0) {
    renderBusinessFallback(dropdownList);
    return;
  }

  dropdownList.innerHTML = "";

  businesses.forEach((business) => {
    const isActive = Boolean(business.is_active);
    const iconClass = isActive ? "bi bi-check-lg me-2" : "me-2";
    const activeClass = isActive ? "active" : "";
    const category = business.category
      ? `<small class="text-muted d-block">${business.category}</small>`
      : "";

    const item = document.createElement("li");
    item.innerHTML = `
      <a class="dropdown-item d-flex align-items-start ${activeClass}" href="#" data-business-id="${business.idBusiness}">
        <i class="${iconClass}"></i>
        <div>
          <div class="fw-semibold">${business.business}</div>
          ${category}
        </div>
      </a>
    `;

    dropdownList.appendChild(item);
  });

  const divider = document.createElement("li");
  divider.innerHTML = '<hr class="dropdown-divider">';
  dropdownList.appendChild(divider);

  const createItem = document.createElement("li");
  createItem.innerHTML =
    '<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addBusinessModal"><i class="bi bi-plus-circle me-2"></i>Agregar nuevo negocio</a>';
  dropdownList.appendChild(createItem);
}

/**
 * Registra un nuevo negocio mediante petición asíncrona.
 *
 */
function createBusiness() {
  if (!formAddBusiness) return;
  formAddBusiness.addEventListener("submit", async (e) => {
    e.preventDefault();
    const htmlAddBusiness = btnAddBusiness.innerHTML;
    btnAddBusiness.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btnAddBusiness.disabled = true;
    btnAddBusiness.classList.add("disabled");
    const formdata = new FormData(formAddBusiness);
    const config = {
      method: "POST",
      body: formdata,
    };
    const url = base_url + "/pos/Business/create";
    try {
      const response = await fetch(url, config);
      if (!response.ok) {
        throw new Error("No se pudo registrar el negocio");
      }
      const data = await response.json();
      if (data.status) {
        resetBusinessForm(formAddBusiness);
        closeModal("addBusinessModal");
        updateActiveBusinessUI(data?.data);
        loadUserBusinesses();
      }
      showAlert(data);
    } catch (error) {
      showAlert({
        icon: "error",
        title: "Error",
        message: "No fue posible registrar el negocio. Inténtalo más tarde.",
        html: `<pre>${error}</pre>`,
      });
    } finally {
      btnAddBusiness.innerHTML = htmlAddBusiness;
      btnAddBusiness.disabled = false;
      btnAddBusiness.classList.remove("disabled");
    }
  });
}

/**
 * Establece un negocio como activo para la sesión.
 * @param {string|number} businessId
 * @param {string} token
 */
function setActiveBusiness(businessId, token) {
  if (!businessId) return;

  const formData = new FormData();
  formData.append("businessId", businessId);
  formData.append("token", token || "");

  fetch(`${base_url}/pos/Business/setActiveBusiness`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((result) => {
      if (!result?.status) {
        showAlert({
          icon: "error",
          title: "No se pudo cambiar",
          message: result?.message || "Intenta nuevamente.",
        });
        return;
      }

      showAlert(result);

      updateActiveBusinessUI(result?.data);
      loadUserBusinesses();
    })
    .catch(() => {
      showAlert({
        icon: "error",
        title: "Error",
        message: "No pudimos actualizar el negocio activo.",
      });
    });
}

/**
 * Restablece el formulario del modal de negocios.
 * @param {HTMLFormElement} form
 */
function resetBusinessForm(form) {
  if (!form) return;
  form.reset();
  const prefixField = document.getElementById("businessTelephonePrefix");
  if (prefixField) {
    prefixField.value = prefixField.getAttribute("value") || "+51";
  }
}

/**
 * Actualiza la información mostrada del negocio activo sin recargar la página.
 * Actualiza el nombre, categoria e imagen
 * @param {Object} business
 */
function updateActiveBusinessUI(business) {
  if (!business) return;

  const nameElement = document.getElementById("currentBusinessName");
  const currentBusinessAvatar = document.getElementById(
    "currentBusinessAvatar"
  );
  const categoryElement = document.getElementById("currentBusinessCategory");
  const dropdownList = document.getElementById("businessListDropdown");

  if (nameElement) {
    nameElement.textContent = business.business || "Negocio";
    currentBusinessAvatar.src =
      generate_profile + business.business || "Negocio";
    currentBusinessAvatar.alt = business.business || "Negocio";
    currentBusinessAvatar.title = business.business || "Negocio";
  }

  if (categoryElement) {
    categoryElement.textContent = business.category || "Propietario";
  }

  if (dropdownList) {
    dropdownList.querySelectorAll("[data-business-id]").forEach((item) => {
      const businessId = item.getAttribute("data-business-id");
      const icon = item.querySelector("i");
      const isActive =
        `${businessId}` === `${business.idBusiness || business.id || ""}`;

      item.classList.toggle("active", isActive);
      if (icon) {
        icon.className = isActive ? "bi bi-check-lg me-2" : "me-2";
      }
    });
  }
}

/**
 * Cierra un modal de Bootstrap 4/5 de forma segura.
 * @param {string} modalId
 */
function closeModal(modalId) {
  if (!modalId) return;
  const modalElement = document.getElementById(modalId);
  if (!modalElement) return;

  if (window.bootstrap && bootstrap.Modal) {
    const instance =
      bootstrap.Modal.getInstance(modalElement) ||
      new bootstrap.Modal(modalElement);
    if (instance?.hide) instance.hide();
  } else if (window.$) {
    $(modalElement).modal("hide");
  }
}

/**
 * Obtiene los tipos de negocio disponibles.
 */
function loadBusinessTypes() {
  const select = document.getElementById("businessType");
  if (!select) return;

  fetch(`${base_url}/pos/Business/getBusinessTypes`)
    .then((response) => response.json())
    .then((result) => {
      if (!result?.status || !Array.isArray(result.data)) return;

      select.innerHTML =
        '<option value="" disabled selected>Selecciona un tipo de negocio</option>';

      result.data.forEach((type) => {
        const option = document.createElement("option");
        option.value = type.idBusinessType;
        option.textContent = type.name;
        select.appendChild(option);
      });
    })
    .catch(() => {});
}

document.addEventListener("DOMContentLoaded", () => {
  createBusiness();
  initBusinessManagement();
});
