(function () {
  "use strict";
  /**
   * Elemento del DOM  que se utilizaran en el archivo
   */
  const formAddBusiness = document.getElementById("formAddBusiness");
  const btnAddBusiness = document.getElementById("btnAddBusiness");
  const dropdownList = document.getElementById("businessListDropdown");

  var treeviewMenu = $(".app-menu");

  // Toggle Sidebar
  $('[data-toggle="sidebar"]').click(function (event) {
    event.preventDefault();
    $(".app").toggleClass("sidenav-toggled");
    document.getElementById("cardBusiness").classList.toggle("d-none");
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
      const owner = business.is_owner
        ? `<small class="text-white d-block badge bg-success"><i class="bi bi-person-fill me-1"></i> Dueño</small>`
        : `<small class="text-white d-block badge bg-info"><i class="bi bi-people-fill"></i> Empleado</small>`;
      const item = document.createElement("li");
      item.innerHTML = `
      <a class="dropdown-item d-flex align-items-start ${activeClass}" href="#" data-business-id="${business.idBusiness}" data-owner="${business.is_owner}">
        <i class="${iconClass}"></i>
        <div>
          <div class="fw-semibold">${business.business}</div>
          ${category}
          ${owner}
        </div>
      </a>
    `;

      dropdownList.appendChild(item);
    });
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
   */
  async function setActiveBusiness(businessId, owner) {
    if (!businessId) return;
    if (!owner) return;
    const formData = new FormData();
    formData.append("businessId", businessId);
    formData.append("owner", owner);
    const url = base_url + "/pos/Business/setActiveBusiness";
    const config = {
      method: "POST",
      body: formData,
    };
    showAlert(
      {
        icon: "loading",
        title: "Cambiando negocio...",
        html: "Se esta cambiando de negocio por favor espere un momento",
      },
      "loading"
    );
    try {
      const response = await fetch(url, config);
      if (!response.ok) {
        throw new Error("No se pudo establecer el negocio activo");
      }
      const result = await response.json();
      if (result.data) {
        updateActiveBusinessUI(result?.data);
        loadUserBusinesses();
      }
      showAlert(result);
      if (result.reload) {
        setTimeout(() => {
          window.location.reload();
        }, result.timer);
      }
    } catch (error) {
      showAlert({
        icon: "error",
        title: "Error",
        message:
          "No fue posible establecer el negocio activo. Inténtalo más tarde.",
        html: `<pre>${error}</pre>`,
      });
    }
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
  /**
   * Metodo que carga los negocios cuando el usuario
   * hace clic sobre el negocio
   */
  function activeBusiness() {
    if (dropdownList) {
      dropdownList.addEventListener("click", function (event) {
        const item = event.target.closest("[data-business-id]");
        if (!item) return;
        const owner = item.getAttribute("data-owner");
        event.preventDefault();
        const businessId = item.getAttribute("data-business-id");
        setActiveBusiness(businessId, owner);
      });
    }
  }
  document.addEventListener("DOMContentLoaded", () => {
    loadBusinessTypes();
    loadUserBusinesses();
    createBusiness();
    activeBusiness();
    closeAllModals();
    // Esto es ideal para botones tipo "Cerrar sesión" o al cambiar de ruta en una SPA.
  });
})();

/**
 * Creamos una funcion de tipos de alertas con sweetalert2
 */
function showAlert(data = {}, type = "float") {
  switch (type) {
    // 1. Toast flotante (tu modo original)
    case "float":
      Swal.fire({
        icon: data.icon ?? "success",
        title: data.title ?? "Satisfactorio",
        text: data.message ?? "Conexión exitosa",
        html: data.html ?? "",
        toast: true,
        position: data.position ?? "top-end",
        showConfirmButton: false,
        timer: data.timer ?? 2500,
        timerProgressBar: true,
      });
      break;

    // 2. Modal centrado normal
    case "modal":
      Swal.fire({
        icon: data.icon ?? "info",
        title: data.title ?? "",
        text: data.message ?? "",
        html: data.html ?? "",
        showConfirmButton: data.showConfirmButton ?? true,
        showCancelButton: data.showCancelButton ?? false,
      });
      break;

    // 3. Confirmación (devuelve promesa)
    case "confirm":
      return Swal.fire({
        title: data.title ?? "¿Estás seguro?",
        text: data.message ?? "Esta acción no se puede deshacer",
        icon: data.icon ?? "warning",
        showCancelButton: true,
        confirmButtonText: data.confirmText ?? "Sí",
        cancelButtonText: data.cancelText ?? "Cancelar",
        reverseButtons: true,
      });

    // 4. Loading / Espera
    case "loading":
      Swal.fire({
        icon: data.icon ?? "info",
        title: data.title ?? "Cargando...",
        html: data.message ?? "Por favor espera",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
      break;

    // 5. Snackbar abajo
    case "bottom":
      Swal.fire({
        icon: data.icon ?? "info",
        text: data.message ?? "",
        toast: true,
        position: "bottom",
        showConfirmButton: false,
        timer: data.timer ?? 3000,
      });
      break;

    // 6. Input (para pedir un dato)
    case "input":
      return Swal.fire({
        title: data.title ?? "Ingresa un valor",
        input: data.inputType ?? "text",
        inputLabel: data.inputLabel ?? "",
        inputPlaceholder: data.inputPlaceholder ?? "",
        showCancelButton: true,
      });

    default:
      console.warn("Tipo de alerta no reconocido:", type);
      break;
  }
}
/**
 * Busca todos los modales visibles (clase .show) y los cierra.
 */
function closeAllModals() {
  // Selecciona todos los div con clase 'modal' y 'show' (abiertos)
  const openModals = document.querySelectorAll(".modal.show");

  openModals.forEach((modalElement) => {
    closeModal(modalElement);
  });

  // Limpieza de seguridad extra por si quedan fondos grises
  setTimeout(removeBackdrop, 500);
}

/**
 * Cierra un modal específico.
 * @param {string|HTMLElement} target - Puede ser el ID del modal o el elemento DOM.
 */
function closeModal(target) {
  let modalElement;

  // 1. Determinar si recibimos un ID o el elemento directo
  if (typeof target === "string") {
    modalElement = document.getElementById(target);
  } else if (target instanceof HTMLElement) {
    modalElement = target;
  }

  if (!modalElement) return;
  // Si el elemento que tiene el foco (el botón presionado) está dentro del modal,
  // le quitamos el foco inmediatamente.
  if (modalElement.contains(document.activeElement)) {
    document.activeElement.blur();
  }
  // 2. Lógica para Bootstrap 5 (Vanilla JS)
  if (window.bootstrap && bootstrap.Modal) {
    // Solo obtenemos la instancia existente. No creamos una nueva para cerrar.
    const instance = bootstrap.Modal.getInstance(modalElement);
    if (instance) {
      instance.hide();
    } else {
      // Fallback: Si no hay instancia, forzamos cierre visual
      forceClose(modalElement);
    }
  }
  // 3. Lógica para jQuery (Bootstrap 4)
  else if (window.$) {
    $(modalElement).modal("hide");
  } else {
    // 4. Fallback final si no hay librerías cargadas
    forceClose(modalElement);
  }
}

/**
 * Helper para forzar el cierre visual manipulando clases CSS
 */
function forceClose(element) {
  element.classList.remove("show");
  element.style.display = "none";
  element.setAttribute("aria-hidden", "true");
}

/**
 * Elimina el fondo gris oscuro (backdrop) si se queda pegado.
 */
function removeBackdrop() {
  const backdrops = document.querySelectorAll(".modal-backdrop");
  backdrops.forEach((backdrop) => backdrop.remove());
  document.body.classList.remove("modal-open");
  document.body.style.overflow = "";
}
