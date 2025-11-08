const container_files = document.getElementById("container_files");
let modalViewFile = null;
let viewerStage = null;
let viewerContainer = null;
let viewerMessage = null;
let downloadButton = null;
let modalFileName = null;
let modalFileIcon = null;
let modalFileDetails = null;
let viewerMetaBadge = null;
let viewerMetaInfo = null;
let viewerMetaType = null;
let viewerMetaLocation = null;
let viewerMetaSize = null;
let viewerMetaRegistered = null;
let viewerMetaUpdated = null;
const defaultViewerMessage = "Selecciona un archivo para visualizarlo.";
const loadingViewerMessage = "Cargando vista previa...";
const viewerActiveClass = "viewer-stage--active";
//menu toggle
document.getElementById("menu-toggle").addEventListener("click", function () {
  document.getElementById("sidebarClust").classList.toggle("active");
});
document.getElementById("menu-close").addEventListener("click", closeMenu);
function closeMenu() {
  document.getElementById("sidebarClust").classList.toggle("active");
}
//inicializamos las alertas
toastr.options = {
  closeButton: true,
  onclick: null,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "2000",
  progressBar: true,
  onclick: null,
};
//carga el DOM
document.addEventListener("DOMContentLoaded", function () {
  initializeViewerElements();
  loadFiles();
  setTimeout(function () {
    // Código a ejecutar después de 1 segundo
    saveFolder();
    selectItem();
    deleteFiles();
    close_form_seleccion();
    updateFiles();
    openFolderDoubleClick();
    redirectfolder();
    uploadFiles();
    selectFile();
  }, 1000);
});
/**
 * Inicializa las referencias del visor para asegurar que existan tras la carga del DOM.
 */
function initializeViewerElements() {
  modalViewFile = document.getElementById("modalViewFile");
  viewerStage = document.getElementById("fileViewerStage");
  viewerContainer = document.getElementById("fileViewerContainer");
  viewerMessage = document.getElementById("fileViewerMessage");
  downloadButton = document.getElementById("btnDownloadFile");
  modalFileName = document.getElementById("modalFileName");
  modalFileIcon = document.getElementById("modalFileIcon");
  modalFileDetails = document.getElementById("modalFileDetails");
  viewerMetaBadge = document.getElementById("modalFileBadge");
  viewerMetaInfo = document.getElementById("modalFileInfo");
  viewerMetaType = document.getElementById("modalFileType");
  viewerMetaLocation = document.getElementById("viewerMetaLocation");
  viewerMetaSize = document.getElementById("viewerMetaSize");
  viewerMetaRegistered = document.getElementById("viewerMetaRegistered");
  viewerMetaUpdated = document.getElementById("viewerMetaUpdated");
  resetViewerMetadata();
  showViewerMessage(defaultViewerMessage, false);
}
/**
 * Actualiza el texto auxiliar mostrado bajo el título del visor.
 * @param {string} text - Mensaje descriptivo para el usuario.
 */
function updateViewerSubtitle(text) {
  if (!modalFileDetails) {
    return;
  }
  modalFileDetails.textContent =
    text || "Selecciona un archivo para iniciar la vista previa.";
}
/**
 * Convierte un tamaño en bytes a una etiqueta legible (KB, MB, GB, etc.).
 *
 * @param {number|string} bytesValue - Tamaño del archivo en bytes.
 * @returns {string} Cadena con el tamaño formateado o "-" si no aplica.
 */
function formatFileSizeLabel(bytesValue) {
  const parsedValue = Number(bytesValue);
  if (!Number.isFinite(parsedValue) || parsedValue < 0) {
    return "-";
  }
  const units = ["B", "KB", "MB", "GB", "TB", "PB"];
  let value = parsedValue;
  let unitIndex = 0;
  while (value >= 1024 && unitIndex < units.length - 1) {
    value /= 1024;
    unitIndex += 1;
  }
  const decimals = unitIndex === 0 ? 0 : value < 10 ? 2 : 1;
  const formatted = value
    .toFixed(decimals)
    .replace(/\.0+$|\.([0-9]*[1-9])0+$/, ".$1")
    .replace(".", ",");
  return `${formatted} ${units[unitIndex]}`;
}
/**
 * Normaliza una fecha/hora recibida desde el servidor a un formato legible.
 *
 * @param {string} dateValue - Fecha en formato ISO o timestamp MySQL.
 * @returns {string} Fecha formateada (dd/mm/aaaa hh:mm) o "-" si no es válida.
 */
function formatDateTimeLabel(dateValue) {
  if (!dateValue) {
    return "-";
  }
  const normalized = String(dateValue).replace(" ", "T");
  const date = new Date(normalized);
  if (Number.isNaN(date.getTime())) {
    return "-";
  }
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${day}/${month}/${year} ${hours}:${minutes}`;
}
/**
 * Acondiciona la ubicación recibida de la API para mostrarla en el visor.
 *
 * @param {string} pathValue - Ruta relativa almacenada para el archivo.
 * @returns {string} Ruta normalizada o "-" si no se cuenta con la información.
 */
function sanitizeViewerLocation(pathValue) {
  if (!pathValue) {
    return "-";
  }
  const normalized = String(pathValue)
    .replace(/\\+/g, "/")
    .replace(/\s+/g, " ")
    .trim();
  const trimmed = normalized.replace(/^\/+/, "").replace(/\/+$/, "");
  return trimmed || "-";
}
/**
 * Obtiene el texto actual de un elemento de metadatos del visor.
 *
 * @param {HTMLElement|null} element - Elemento a evaluar.
 * @returns {string} Texto del elemento o "-" si no existe.
 */
function getViewerMetadataText(element) {
  if (!element || !element.textContent) {
    return "-";
  }
  return element.textContent;
}
/**
 * Escapa caracteres especiales para utilizarlos dentro de atributos HTML.
 *
 * @param {string} value - Texto a escapar.
 * @returns {string} Cadena segura para un atributo HTML.
 */
function escapeAttribute(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;");
}
/**
 * Refresca los elementos visuales del panel informativo del visor.
 * @param {Object} options - Conjunto de datos descriptivos del archivo.
 * @param {string} [options.filename] - Nombre del archivo a mostrar.
 * @param {string} [options.extension] - Extensión del archivo sin punto.
 * @param {string} [options.render] - Tipo de representación disponible para la vista previa.
 */
function updateViewerMetadata({
  filename = "",
  extension = "",
  render = "pending",
  location = "",
  sizeLabel = "",
  registered = "",
  updated = "",
} = {}) {
  const iconData = getFileIconByExtension(extension || "");
  if (modalFileIcon) {
    const iconClasses = [
      "fa",
      iconData.icon,
      "fa-2x",
      "mr-3",
      iconData.textClass || "text-light",
    ];
    modalFileIcon.className = iconClasses.join(" ");
  }
  if (viewerMetaBadge) {
    const badgeText = extension ? extension.toUpperCase() : "SIN ARCHIVO";
    const badgeClasses = [
      "badge",
      "badge-pill",
      iconData.badgeClass || "badge-secondary",
    ];
    viewerMetaBadge.className = badgeClasses.join(" ");
    viewerMetaBadge.textContent = badgeText;
  }
  if (viewerMetaInfo) {
    viewerMetaInfo.textContent =
      filename || "Selecciona un archivo para iniciar la vista previa.";
  }
  if (viewerMetaLocation) {
    viewerMetaLocation.textContent = location || "-";
  }
  if (viewerMetaSize) {
    viewerMetaSize.textContent = sizeLabel || "-";
  }
  if (viewerMetaRegistered) {
    viewerMetaRegistered.textContent = registered || "-";
  }
  if (viewerMetaUpdated) {
    viewerMetaUpdated.textContent = updated || "-";
  }
  if (viewerMetaType) {
    const renderDescriptions = {
      idle: "Selecciona un archivo para iniciar la vista previa.",
      image: "Vista previa como imagen disponible.",
      pdf: "Visor PDF integrado habilitado.",
      text: "Vista previa de texto plano disponible.",
      download: "La vista previa no está disponible para este formato.",
      pending: "Consultando la información del archivo seleccionado...",
    };
    viewerMetaType.textContent =
      renderDescriptions[render] || renderDescriptions.download;
  }
}
/**
 * Restaura el visor a su estado inicial sin archivo seleccionado.
 */
function resetViewerMetadata() {
  updateViewerMetadata({
    filename: "Ningún archivo seleccionado",
    extension: "",
    render: "idle",
    location: "-",
    sizeLabel: "-",
    registered: "-",
    updated: "-",
  });
  updateViewerSubtitle("Selecciona un archivo para iniciar la vista previa.");
}
/**
 * Prepara la interfaz del visor con la información disponible en la tarjeta seleccionada.
 * @param {HTMLElement} card - Tarjeta asociada al archivo seleccionado.
 */
function prefillViewerMetadata(card) {
  if (!card) {
    resetViewerMetadata();
    setViewerTitle("Visor de archivos");
    return;
  }
  const name = card.getAttribute("data-name") || "Archivo";
  const extension = card.getAttribute("data-extension") || "";
  const fullName = extension ? `${name}.${extension}` : name;
  const cardLocation = card.getAttribute("data-path") || "-";
  setViewerTitle(fullName || "Visor de archivos");
  updateViewerMetadata({
    filename: fullName,
    extension,
    render: "pending",
    location: card.getAttribute("data-path") || "-",
    sizeLabel: card.getAttribute("data-size") || "-",
    registered: card.getAttribute("data-created") || "-",
    updated: card.getAttribute("data-updated") || "-",
  });
  const subtitleText =
    cardLocation && cardLocation !== "-"
      ? `Ubicación: ${cardLocation}`
      : "Consultando la información del archivo seleccionado...";
  updateViewerSubtitle(subtitleText);
}
/**
 * Crea un bloque de alerta cuando no hay elementos que mostrar.
 * @param {string} message - Texto del mensaje de alerta.
 * @returns {HTMLElement} - Elemento <div> con la alerta.
 */
function createEmptyAlert(message) {
  const div = document.createElement("div");
  div.classList.add("col-12");

  const alert = document.createElement("div");
  alert.classList.add("alert", "alert-info");
  alert.setAttribute("role", "alert");
  alert.innerText = message;

  div.append(alert);
  return div;
}

/**
 * Renderiza la sección de carpetas.
 * @param {Array} folders - Lista de carpetas a mostrar.
 * @returns {HTMLElement} - Contenedor con las carpetas renderizadas.
 */
function renderFolders(folders) {
  const container = document.createElement("div");
  container.id = "folder_container";

  if (!folders || folders.length === 0) {
    container.append(createEmptyAlert("No hay carpetas disponibles"));
    return container;
  }

  const pfolder = document.createElement("p");
  pfolder.innerText = "Carpetas";
  const hrfolder = document.createElement("hr");

  const divfolders = document.createElement("div");
  divfolders.classList.add("row");
  const fragment = document.createDocumentFragment();

  folders.forEach((f) => {
    const div = document.createElement("div");
    div.className = "col-md-2 col-sm-6 col-6 mb-4";
    div.innerHTML = `
      <div class="card shadow-sm h-100 text-center cursor-pointer card-item card-folder"
          data-id="${f.idFolder}"
          data-code="${f.full_name_encryption}"
          data-name="${f.f_name}"
          data-type="folder"
          data-short-name="${f.name_short}"
          title="Nombre completo [${f.fullnamefolder}]">
          <div class="card-body">
            <div class="position-relative">
              <i class="fa fa-folder fa-3x text-warning mb-2"></i>
              ${f.iconUser}
            </div>
            <h6 class="mb-0">${f.name_short}</h6>
          </div>
      </div>`;
    fragment.appendChild(div);
  });

  divfolders.append(fragment);
  container.append(pfolder, hrfolder, divfolders);

  return container;
}

/**
 * Renderiza la sección de archivos.
 * @param {Array} files - Lista de archivos a mostrar.
 * @returns {HTMLElement} - Contenedor con los archivos renderizados.
 */
function renderFiles(files) {
  const container = document.createElement("div");
  container.id = "file_container";

  const pfile = document.createElement("p");
  pfile.innerText = "Archivos";
  const hrfile = document.createElement("hr");
  container.append(pfile, hrfile);

  if (!files || files.length === 0) {
    container.append(createEmptyAlert("No hay archivos disponibles"));
    return container;
  }

  const fragment = document.createDocumentFragment();
  const divrow = document.createElement("div");
  divrow.classList.add("row");

  files.forEach((file) => {
    const divcontainer = document.createElement("div");
    divcontainer.classList.add("col-md-2", "col-sm-6", "mb-4", "col-6");
    const sizeLabel = formatFileSizeLabel(file.f_size);
    const createdLabel = formatDateTimeLabel(file.f_registrationDate);
    const updatedLabel = formatDateTimeLabel(file.f_updateDate);
    const locationLabel = sanitizeViewerLocation(
      file.f_path || file.folder_path || ""
    );

    divcontainer.innerHTML = `
      <div class="card cursor-pointer shadow-sm h-100 text-center card-item" title="Archivo ${
        file.f_extension ? file.f_extension.toUpperCase() : "sin extensión"
      }"
        data-id="${file.idFile}"
        data-code="${file.full_name_encryption}"
        data-name="${file.f_name}"
        data-type="file"
        data-extension="${(file.f_extension || "").toLowerCase()}"
        data-short-name="${file.name_short}"
        data-size="${escapeAttribute(sizeLabel)}"
        data-created="${escapeAttribute(createdLabel)}"
        data-updated="${escapeAttribute(updatedLabel)}"
        data-path="${escapeAttribute(locationLabel)}">
        <div class="card-body" title="Nombre completo [${file.f_name}]">
          <i class="fa ${
            getFileIconByExtension(file.f_extension).icon
          } fa-3x mb-2" style="color: ${
      getFileIconByExtension(file.f_extension).color
    };"></i>
                <h6 class="mb-0">${file.name_short}.${
      file.f_extension || "file"
    }</h6>
        </div>
      </div>`;
    divrow.appendChild(divcontainer);
  });
  fragment.appendChild(divrow);
  container.append(fragment);
  return container;
}

/**
 * Carga y renderiza la lista de carpetas y archivos desde el servidor.
 * Utiliza async/await para manejar la solicitud y actualizar el DOM dinámicamente.
 */
async function loadFiles() {
  // Contenedor principal donde se mostrarán las carpetas y archivos
  const container_files = document.getElementById("container_files");

  // Formulario de selección que debe ocultarse al cargar archivos
  const formSelecction = document.getElementById("formSelecction");

  // URL de la API que devuelve las carpetas y archivos
  const url = `${base_url}/Clust/getFiles`;

  // Ocultar el formulario cambiando clases CSS
  formSelecction.classList.remove("d-flex"); // Quita la clase que lo muestra en flex
  formSelecction.classList.add("d-none"); // Añade la clase que lo oculta

  try {
    // Realizar la petición al servidor
    const response = await fetch(url);

    // Convertir la respuesta en JSON
    const data = await response.json();

    // Validar si la respuesta indica error
    if (!data.status) {
      // Mostrar notificación con toastr según el tipo de error
      toastr[data.type](data.message, data.title);

      // Recargar la página después de 0.5 segundos
      //setTimeout(() => window.location.reload(), 500);
      return; // Salir de la función
    }

    // Extraer datos del objeto de respuesta (desestructuración)
    const { folders, files, breadcrumbTrail, arraybreadcrumb } = data;

    // Cargar el breadcrumb con la ruta actual
    const breadcrumbData =
      Array.isArray(breadcrumbTrail) && breadcrumbTrail.length
        ? breadcrumbTrail
        : arraybreadcrumb || [];
    loadBreadcrumb(breadcrumbData);

    // Limpiar el contenedor para evitar duplicaciones
    container_files.innerHTML = "";

    // Si no hay carpetas ni archivos, mostrar un mensaje de alerta y salir
    if ((!folders || folders.length === 0) && (!files || files.length === 0)) {
      container_files.append(
        createEmptyAlert("No hay carpetas ni archivos disponibles")
      );
      return;
    }

    // Renderizar las carpetas y archivos
    const folder_container = renderFolders(folders); // Construye el HTML de carpetas
    const file_container = renderFiles(files); // Construye el HTML de archivos

    // Agregar ambos contenedores al DOM
    container_files.append(folder_container, file_container);
  } catch (error) {
    // Manejo de errores inesperados (red, servidor, etc.)
    toastr.error(
      `Error en la solicitud al servidor: ${error.message} - ${error.name}`,
      "Ocurrió un error inesperado"
    );

    // Ocultar el loader si existe en la página
    elementLoader?.classList.add("d-none");
  }
}

/**
 * Renderiza el breadcrumb de la carpeta seleccionada.
 * @param {Array} breadcrumbItems - Lista de rutas en el breadcrumb.
 */
function loadBreadcrumb(breadcrumbItems) {
  const elementBreadcrumb = document.getElementById("breadcrumb");
  elementBreadcrumb.innerHTML = "";
  const fragment = document.createDocumentFragment();

  const normalizedItems = Array.isArray(breadcrumbItems)
    ? breadcrumbItems
        .map((item) => {
          if (item && typeof item === "object") {
            return {
              id: item.id ? String(item.id) : "",
              name: item.name || "",
            };
          }
          const name = typeof item === "string" ? item : "";
          return { id: "", name };
        })
        .filter((item) => item.name !== "")
    : [];

  if (normalizedItems.length === 0) {
    const li = document.createElement("li");
    li.classList.add("breadcrumb-item", "active");
    li.setAttribute("aria-current", "page");
    li.innerHTML = '<i class="fa fa-folder-open-o"></i> Carpeta actual';
    fragment.appendChild(li);
    elementBreadcrumb.appendChild(fragment);
    return;
  }

  normalizedItems.forEach((item, index) => {
    const li = document.createElement("li");
    li.classList.add("breadcrumb-item");
    const isLast = index === normalizedItems.length - 1;

    if (isLast) {
      li.classList.add("active");
      li.setAttribute("aria-current", "page");
      li.innerHTML = `<i class="fa fa-folder-open-o"></i> ${item.name}`;
    } else {
      const a = document.createElement("a");
      a.classList.add("redirect-item");
      a.dataset.name = item.name;
      if (item.id) {
        a.dataset.id = item.id;
      }
      a.href = "#";
      a.innerHTML = `<i class="fa fa-folder-open-o"></i> ${item.name}`;
      li.appendChild(a);
    }

    fragment.appendChild(li);
  });

  elementBreadcrumb.appendChild(fragment);
}

/**
 * =========================================================================
 * Metodo que se encarga de insertar un nuevo folder
 * =========================================================================
 */
function saveFolder() {
  const formSave = document.getElementById("formSave");
  formSave.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formSave);
    //creamos los encabezados
    const header = new Headers();
    //preparamos todo en un array
    const config = {
      method: "POST",
      headers: header,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/Clust/createFolder";
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Error en la solicitud" +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        //recargamos el listado de files
        loadFiles();
        if (data.status) {
          //ocultamos el modal
          $("#modalCarpeta").modal("hide");
          //limpiamos el formulario
          formSave.reset();
        }
        toastr[data.type](data.message, data.title);
        //esperamos un segundo de carga
        setTimeout(() => {
          selectItem();
          openFolderDoubleClick();
          redirectfolder();
          //quitamos el d-none del elementLoader
          elementLoader.classList.add("d-none");
        }, 1000);
        return true;
      })
      .catch((error) => {
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrio un error inesperado"
        );
      });
  });
}
/**
 * Metodo que se encarga de seleccionar un card para preparar lo botones de eliminar y actualizar nombre
 */
function selectItem() {
  const arrCardItem = document.querySelectorAll(".card-item");
  arrCardItem.forEach((item) => {
    item.addEventListener("click", () => {
      const idfolder = item.getAttribute("data-id");
      const shortname = item.getAttribute("data-short-name");
      const code = item.getAttribute("data-code");
      const full_name = item.getAttribute("data-name");
      const type = item.getAttribute("data-type");
      //recorremos todos los elementos
      arrCardItem.forEach((i) => {
        //verificamos que tenga la clase
        if (i.classList.contains("bg-primary", "text-white")) {
          i.classList.remove("bg-primary", "text-white");
        }
      });
      //mostramos el elemento de seleccion para eliminar y actualizar la carpeta o archivo
      const formSelecction = document.getElementById("formSelecction");
      const update_txtName = document.getElementById("update_txtName");
      if (formSelecction.classList.contains("d-none")) {
        formSelecction.classList.remove("d-none");
      }
      formSelecction.classList.add("d-flex");
      //mostramos que elemento se selecciono
      update_txtName.placeholder = shortname + " seleccionada";
      //cargamos la informacion necesaria al boton eliminar
      const btnDeleteFiles = document.getElementById("btnDeleteFiles");
      //asignamos los atributos necesarios
      //asignamos el id
      btnDeleteFiles.setAttribute("data-id", idfolder);
      //asignamos el nombre actual
      btnDeleteFiles.setAttribute("data-name", full_name);
      //preparamos el tipo de elemento
      btnDeleteFiles.setAttribute("data-type", type);
      //preparamos los datos para actualizar el nombre de la carpeta
      const btnUpdateFiles = document.getElementById("btnUpdateFiles");
      //asignamos los atributos necesarios
      //asignamos el id
      btnUpdateFiles.setAttribute("data-id", idfolder);
      //asignamos el nombre actual
      btnUpdateFiles.setAttribute("data-name", full_name);
      //preparamos el tipo de elemento
      btnUpdateFiles.setAttribute("data-type", type);
      //activamos la clase
      item.classList.add("bg-primary", "text-white", "transition");
    });
  });
}
/**
 * Metodo que se encarga de ocultar el formulario de seleccion
 */
function close_form_seleccion() {
  const btn_close_form_selecction = document.getElementById(
    "btn_close_form_selecction"
  );
  btn_close_form_selecction.addEventListener("click", () => {
    const formSelecction = document.getElementById("formSelecction");
    if (formSelecction.classList.contains("d-flex")) {
      formSelecction.classList.remove("d-flex");
    }
    formSelecction.classList.add("d-none");
    formSelecction.reset();
    toastr.info("Cerrado correctamente");
  });
}
/**
 * Metodo que se encarga de eliminar el archivo seleccionado
 */
function deleteFiles() {
  const btnDeleteFiles = document.getElementById("btnDeleteFiles");
  btnDeleteFiles.addEventListener("click", () => {
    elementLoader.classList.remove("d-none");
    const id = btnDeleteFiles.getAttribute("data-id");
    const name = btnDeleteFiles.getAttribute("data-name");
    const token = btnDeleteFiles.getAttribute("data-token");
    const type = btnDeleteFiles.getAttribute("data-type");
    const form = new FormData();
    form.append("id", id);
    form.append("name", name);
    form.append("token", token);
    form.append("type", type);
    const header = new Headers();
    const config = {
      method: "POST",
      node: "no-cache",
      cors: "cors",
      header: header,
      body: form,
    };
    const url = base_url + "/Clust/deleteFolderAndFiles";
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new error(
            "Ocurrio un error inesperado" +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        if (data.status) {
          //refrescamos la carga de carpetas
          loadFiles();
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          selectItem();
          openFolderDoubleClick();
          redirectfolder();
        }, 1000);
        elementLoader.classList.add("d-none");
      })
      .catch((error) => {
        toastr.error(
          "Ocurrio un error inesperado",
          "Error en el servidor " + error.message + " - " + error.name
        );
        elementLoader.classList.add("d-none");
      });
  });
}
/**
 * envio de datos para actualizar el nombnre de la carpeta u archivo
 */
async function updateFiles() {
  const formSelecction = document.getElementById("formSelecction");
  formSelecction.addEventListener("submit", (e) => {
    e.preventDefault();
    elementLoader.classList.remove("d-none");
    //obtemos los atributos del boton actualizar
    const btnUpdateFiles = document.getElementById("btnUpdateFiles");
    const id = btnUpdateFiles.getAttribute("data-id");
    const name = btnUpdateFiles.getAttribute("data-name");
    const type = btnUpdateFiles.getAttribute("data-type");
    const form = new FormData(formSelecction);
    //pasamos los nuevos inputs
    form.append("id", id);
    form.append("name", name);
    form.append("type", type);
    const header = new Headers();
    const config = {
      method: "POST",
      header: header,
      node: "no-cache",
      cors: "cors",
      body: form,
    };
    const url = base_url + "/Clust/updateFolderAndFiles";
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new error(
            "Error en el servidor " +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        if (data.status) {
          formSelecction.reset();
        }
        toastr[data.type](data.message, data.title);
        loadFiles();
        setTimeout(() => {
          selectItem();
          openFolderDoubleClick();
        }, 1000);
        elementLoader.classList.add("d-none");
      })
      .catch((error) => {
        toastr.error(
          "Ocurrio un error grave",
          "Error en  " + error.message + " - " + error.name
        );
        elementLoader.classList.add("d-none");
      });
  });
}
/**
 * Metodo que se encarga de abrir con doble click una carpeta
 */
function openFolderDoubleClick() {
  const arrFolders = document.querySelectorAll(".card-item");
  arrFolders.forEach((folder) => {
    folder.addEventListener("dblclick", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");
      const id = folder.getAttribute("data-id");
      //pasamos el codigo encriptado
      const name = folder.getAttribute("data-code");
      const type = folder.getAttribute("data-type");
      if (type === "folder") {
        const form = new FormData();
        form.append("id", id);
        form.append("name", name);
        const header = new Headers();
        const config = {
          method: "POST",
          headers: header,
          cors: "cors",
          node: "no-cache",
          body: form,
        };
        const url = base_url + "/Clust/open_folder";
        fetch(url, config)
          .then((response) => {
            if (!response.ok) {
              throw new error(
                "Ocurrio un error inesperado " +
                  response.status +
                  " - " +
                  response.statusText
              );
            }
            return response.json();
          })
          .then((data) => {
            if (data.status) {
              loadFiles();
            }
            setTimeout(() => {
              selectItem();
              openFolderDoubleClick();
              redirectfolder();
            }, 500);

            elementLoader.classList.add("d-none");
          })
          .catch((error) => {
            toastr.error(
              "Ocurrio un error inesperado en el servidor: " + error.message
            );
            elementLoader.classList.add("d-none");
          });
      } else if (type === "file") {
        openFileModal(folder);
      }
    });
  });
}
/**
 * Metodo que se encarga de redireccionar por medio de la breadcrumb
 */
async function redirectfolder() {
  const redirectitem = document.querySelectorAll(".redirect-item");
  redirectitem.forEach((folder) => {
    folder.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");
      const name = folder.getAttribute("data-name");
      const folderId = folder.getAttribute("data-id") || "";
      const form = new FormData();
      form.append("name", name);
      if (folderId) {
        form.append("id", folderId);
      }
      const header = new Headers();
      const config = {
        method: "POST",
        headers: header,
        cors: "cors",
        node: "no-cache",
        body: form,
      };
      const url = base_url + "/Clust/open_folder_for_breadcrumb";
      fetch(url, config)
        .then((response) => {
          if (!response.ok) {
            throw new error(
              "Ocurrio un error inesperado " +
                response.status +
                " - " +
                response.statusText
            );
          }
          return response.json();
        })
        .then((data) => {
          if (data.status) {
            loadFiles();
          }
          setTimeout(() => {
            selectItem();
            openFolderDoubleClick();
            redirectfolder();
          }, 1000);
          elementLoader.classList.add("d-none");
        })
        .catch((error) => {
          toastr.error(
            "Ocurrio un error inesperado en el servidor: " + error.message
          );
          elementLoader.classList.add("d-none");
        });
    });
  });
}
/**
 * metodo que se encarga de subir los archivos al servidor
 */
async function uploadFiles() {
  //obtenemos el formulario
  const formUpload = document.getElementById("formUpload");
  formUpload.addEventListener("submit", async (e) => {
    e.preventDefault();
    elementLoader.classList.remove("d-none");
    const form = new FormData(formUpload);
    const header = new Headers();
    const config = {
      method: "POST",
      headers: header,
      node: "no-cache",
      cors: "cors",
      body: form,
    };
    const url = base_url + "/Clust/upload_files";
    try {
      //realizamos la peticion
      const response = await fetch(url, config);
      const data = await response.json();
      if (data.status) {
        formUpload.reset();
        //ocultamos el modal
        $("#modalSubir").modal("hide");
      }
      //recargamos el listado de files
      loadFiles();
      toastr[data.type](data.message, data.title);
      //esperamos un segundo de carga
      setTimeout(() => {
        selectItem();
        openFolderDoubleClick();
        redirectfolder();
      }, 1000);
      elementLoader.classList.add("d-none");
    } catch (error) {
      toastr.error(
        "Ocurrio un error inesperado en el servidor: " + error.message
      );
      elementLoader.classList.add("d-none");
    }
  });
}
/**
 * Actualiza el título mostrado en el modal del visor de archivos.
 * @param {string} title - Texto que se mostrará como título del modal.
 */
function setViewerTitle(title) {
  if (!modalFileName) {
    return;
  }
  modalFileName.textContent = title || "Visor de archivos";
}
/**
 * Configura el botón de descarga según la disponibilidad del archivo.
 * @param {string} url - Ruta absoluta al recurso de descarga.
 * @param {string} filename - Nombre sugerido para el archivo descargado.
 * @param {boolean} enabled - Indicador para habilitar o deshabilitar el botón.
 */
function updateDownloadButton(url, filename = "", enabled = false) {
  if (!downloadButton) {
    return;
  }
  if (!enabled || !url) {
    downloadButton.classList.add("disabled");
    downloadButton.setAttribute("aria-disabled", "true");
    downloadButton.href = "#";
    downloadButton.removeAttribute("download");
    return;
  }
  downloadButton.classList.remove("disabled");
  downloadButton.setAttribute("aria-disabled", "false");
  downloadButton.href = url;
  if (filename) {
    downloadButton.setAttribute("download", filename);
  } else {
    downloadButton.removeAttribute("download");
  }
}
/**
 * Elimina cualquier elemento previamente renderizado dentro del visor.
 */
function clearViewerContent() {
  if (!viewerContainer) {
    return;
  }
  const previews = viewerContainer.querySelectorAll(
    '[data-viewer-element="true"]'
  );
  previews.forEach((element) => element.remove());
  setViewerActiveState(false);
}

/**
 * Controla el estado visual del área de vista previa según exista contenido renderizado.
 * @param {boolean} isActive - Define si el visor debe mostrarse en modo expandido.
 */
function setViewerActiveState(isActive) {
  if (!viewerStage) {
    return;
  }
  viewerStage.classList.toggle(viewerActiveClass, Boolean(isActive));
}
/**
 * Muestra un mensaje dentro del visor, opcionalmente con un indicador de carga.
 * @param {string} message - Texto a mostrar al usuario.
 * @param {boolean|Object} [options=false] - Define si se muestra un spinner de carga o los ajustes visuales.
 * @param {boolean} [options.isLoading=false] - Indica si se muestra un indicador de carga.
 * @param {"info"|"warning"|"error"|"success"} [options.status="info"] - Tipo de estado a representar en el mensaje.
 */
function showViewerMessage(message, options = {}) {
  if (!viewerMessage) {
    return;
  }
  if (typeof options === "boolean") {
    options = { isLoading: options };
  }
  const { isLoading = false, status = "info" } = options;
  setViewerActiveState(false);
  viewerMessage.classList.remove("d-none");
  viewerMessage.innerHTML = "";
  if (isLoading) {
    const wrapper = document.createElement("div");
    wrapper.classList.add(
      "d-flex",
      "flex-column",
      "align-items-center",
      "justify-content-center",
      "text-center",
      "w-100"
    );
    const spinner = document.createElement("div");
    spinner.classList.add("spinner-border", "text-primary", "mb-3");
    spinner.setAttribute("role", "status");
    const srOnly = document.createElement("span");
    srOnly.classList.add("sr-only");
    srOnly.textContent = "Cargando...";
    spinner.appendChild(srOnly);
    const text = document.createElement("span");
    text.classList.add("text-muted");
    text.textContent = message;
    wrapper.appendChild(spinner);
    wrapper.appendChild(text);
    viewerMessage.appendChild(wrapper);
    return;
  }
  const statusMap = {
    info: "info",
    warning: "warning",
    error: "danger",
    success: "success",
  };
  const alert = document.createElement("div");
  alert.classList.add("alert", `alert-${statusMap[status] || "info"}`);
  alert.setAttribute("role", "alert");
  alert.textContent = message;
  viewerMessage.appendChild(alert);
}
/**
 * Oculta el mensaje del visor y limpia su contenido.
 */
function hideViewerMessage() {
  if (!viewerMessage) {
    return;
  }
  viewerMessage.classList.add("d-none");
  viewerMessage.innerHTML = "";
  setViewerActiveState(true);
}
/**
 * Abre el modal del visor y solicita la información necesaria al servidor.
 * @param {HTMLElement} card - Elemento que representa el archivo seleccionado.
 */
function openFileModal(card) {
  if (!card) {
    return;
  }
  elementLoader?.classList.remove("d-none");
  if (modalViewFile) {
    $(modalViewFile).modal("show");
  }
  clearViewerContent();
  showViewerMessage(loadingViewerMessage, true);
  updateDownloadButton("#", "", false);
  prefillViewerMetadata(card);
  showFileResource(card);
}
/**
 * Consulta al servidor la información de un archivo para construir la vista previa.
 * @param {HTMLElement} card - Elemento HTML que contiene los atributos del archivo.
 */
async function showFileResource(card) {
  if (!card) {
    elementLoader?.classList.add("d-none");
    return;
  }
  const id = card.getAttribute("data-id") || "";
  const code = card.getAttribute("data-code") || "";
  const cardName = card.getAttribute("data-name") || "Archivo";
  const cardExtension = card.getAttribute("data-extension") || "";
  const fallbackFilename = cardExtension
    ? `${cardName}.${cardExtension}`
    : cardName;
  if (!id || !code) {
    clearViewerContent();
    showViewerMessage(
      "No se pudo identificar el archivo seleccionado. Intenta nuevamente.",
      { status: "error" }
    );
    resetViewerMetadata();
    updateViewerSubtitle(
      "No se pudo identificar el archivo seleccionado. Intenta nuevamente."
    );
    elementLoader?.classList.add("d-none");
    return;
  }
  const form = new FormData();
  form.append("id", id);
  form.append("code", code);
  try {
    const response = await fetch(`${base_url}/Clust/getFileResource`, {
      method: "POST",
      body: form,
    });
    if (!response.ok) {
      throw new Error(`HTTP ${response.status} - ${response.statusText}`);
    }
    const data = await response.json();
    if (!data.status) {
      updateViewerSubtitle(
        data.message || "No fue posible obtener la información del archivo."
      );
      updateViewerMetadata({
        filename: fallbackFilename,
        extension: cardExtension,
        render: "download",
        location: card.getAttribute("data-path") || "-",
        sizeLabel: card.getAttribute("data-size") || "-",
        registered: card.getAttribute("data-created") || "-",
        updated: card.getAttribute("data-updated") || "-",
      });
      toastr[data.type || "error"](
        data.message || "No se pudo obtener la información del archivo.",
        data.title || "Ocurrió un error inesperado"
      );
      if (modalViewFile) {
        $(modalViewFile).modal("hide");
      }
      return;
    }
    setViewerTitle(data.filename || "Visor de archivos");
    updateDownloadButton(
      data.download || "#",
      data.filename || "",
      Boolean(data.download)
    );
    const metadataPayload = {
      filename: data.filename || fallbackFilename,
      extension: data.extension || cardExtension,
      render: data.preview ? data.render : "download",
      location:
        data.location || data.path || card.getAttribute("data-path") || "-",
      sizeLabel:
        data.size_readable ||
        data.sizeLabel ||
        card.getAttribute("data-size") ||
        "-",
      registered:
        data.registered_at ||
        data.registered ||
        card.getAttribute("data-created") ||
        "-",
      updated:
        data.updated_at ||
        data.updated ||
        card.getAttribute("data-updated") ||
        "-",
    };
    updateViewerMetadata(metadataPayload);
    if (card) {
      card.setAttribute("data-path", metadataPayload.location || "-");
      card.setAttribute("data-size", metadataPayload.sizeLabel || "-");
      card.setAttribute("data-created", metadataPayload.registered || "-");
      card.setAttribute("data-updated", metadataPayload.updated || "-");
    }
    const subtitleText =
      metadataPayload.location && metadataPayload.location !== "-"
        ? `Ubicación: ${metadataPayload.location}`
        : data.preview
        ? "Vista previa generada correctamente."
        : "La vista previa no está disponible para este formato.";
    updateViewerSubtitle(subtitleText);
    if (!data.preview) {
      clearViewerContent();
      showViewerMessage(
        "Este tipo de archivo no cuenta con vista previa disponible. Utiliza el botón Descargar para obtenerlo.",
        { status: "warning" }
      );
      return;
    }
    await renderPreview(data);
  } catch (error) {
    updateViewerSubtitle("No fue posible mostrar el archivo seleccionado.");
    updateViewerMetadata({
      filename: fallbackFilename,
      extension: cardExtension,
      render: "download",
      location: card.getAttribute("data-path") || "-",
      sizeLabel: card.getAttribute("data-size") || "-",
      registered: card.getAttribute("data-created") || "-",
      updated: card.getAttribute("data-updated") || "-",
    });
    toastr.error(
      `Ocurrió un error inesperado al intentar visualizar el archivo: ${error.message}`,
      "Error al cargar el archivo"
    );
    if (modalViewFile) {
      $(modalViewFile).modal("hide");
    }
  } finally {
    elementLoader?.classList.add("d-none");
  }
}
/**
 * Renderiza el contenido del archivo en el visor según el tipo de representación.
 * @param {Object} fileData - Información devuelta por el backend para construir la vista.
 */
async function renderPreview(fileData) {
  if (!viewerContainer) {
    return;
  }
  const viewerUrl = fileData.viewer || "";
  clearViewerContent();
  if (!viewerUrl) {
    showViewerMessage(
      "No se recibió una ruta válida para la vista previa. Descarga el archivo para revisarlo.",
      { status: "error" }
    );
    updateViewerSubtitle("No se recibió una ruta válida para la vista previa.");
    return;
  }
  switch (fileData.render) {
    case "image": {
      const image = document.createElement("img");
      const fallbackMessage =
        "No se pudo cargar la vista previa de la imagen. Descarga el archivo para revisarlo.";
      const fallbackSubtitle =
        "No fue posible renderizar la vista previa de la imagen.";
      const metadata = {
        filename: fileData.filename || "Archivo",
        extension: fileData.extension || "",
      };
      image.setAttribute("data-viewer-element", "true");
      image.alt = metadata.filename;
      image.loading = "lazy";
      image.classList.add(
        "w-100",
        "h-100",
        "viewer-preview",
        "viewer-preview-image"
      );
      // La clase "viewer-preview-image" aplica el ajuste documentado en style_clust.css.
      image.addEventListener(
        "load",
        () => {
          hideViewerMessage();
          updateViewerSubtitle("Mostrando vista previa como imagen.");
        },
        { once: true }
      );
      image.addEventListener(
        "error",
        () => {
          image.remove();
          showViewerMessage(fallbackMessage, { status: "warning" });
          updateViewerSubtitle(fallbackSubtitle);
          updateViewerMetadata({
            filename: metadata.filename,
            extension: metadata.extension,
            render: "download",
            location: getViewerMetadataText(viewerMetaLocation),
            sizeLabel: getViewerMetadataText(viewerMetaSize),
            registered: getViewerMetadataText(viewerMetaRegistered),
            updated: getViewerMetadataText(viewerMetaUpdated),
          });
          toastr.warning(
            "No fue posible cargar la vista previa de la imagen seleccionada.",
            "Vista previa no disponible"
          );
        },
        { once: true }
      );
      viewerContainer.appendChild(image);
      image.src = viewerUrl;
      break;
    }
    case "pdf": {
      hideViewerMessage();
      const object = document.createElement("object");
      object.setAttribute("data-viewer-element", "true");
      object.type = "application/pdf";
      object.data = viewerUrl;
      object.classList.add("viewer-preview", "viewer-preview-document");
      object.setAttribute(
        "aria-label",
        fileData.filename
          ? `Vista previa de ${fileData.filename}`
          : "Documento PDF"
      );
      const fallback = document.createElement("div");
      fallback.classList.add("p-4", "text-center", "text-light");
      const paragraph = document.createElement("p");
      paragraph.classList.add("mb-2");
      paragraph.textContent =
        "Tu navegador no puede mostrar la vista previa del PDF.";
      const link = document.createElement("a");
      link.classList.add("text-info", "font-weight-bold");
      link.href = fileData.download || viewerUrl;
      link.target = "_blank";
      link.rel = "noopener";
      link.textContent = "Descargar documento";
      fallback.appendChild(paragraph);
      fallback.appendChild(link);
      object.appendChild(fallback);
      viewerContainer.appendChild(object);
      updateViewerSubtitle("Mostrando documento PDF integrado.");
      break;
    }
    case "text": {
      try {
        const response = await fetch(viewerUrl);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status} - ${response.statusText}`);
        }
        const content = await response.text();
        hideViewerMessage();
        const pre = document.createElement("pre");
        pre.setAttribute("data-viewer-element", "true");
        pre.classList.add(
          "m-0",
          "text-monospace",
          "h-100",
          "overflow-auto",
          "viewer-preview",
          "viewer-preview-text"
        );
        pre.textContent = content;
        viewerContainer.appendChild(pre);
        updateViewerSubtitle("Mostrando contenido del archivo de texto.");
      } catch (error) {
        clearViewerContent();
        showViewerMessage(
          "No se pudo cargar la vista previa del archivo de texto. Descarga el archivo para revisarlo.",
          { status: "warning" }
        );
        updateViewerSubtitle(
          "No fue posible renderizar la vista previa de texto."
        );
        updateViewerMetadata({
          filename: fileData.filename || "Archivo de texto",
          extension: fileData.extension || "",
          render: "download",
          location: getViewerMetadataText(viewerMetaLocation),
          sizeLabel: getViewerMetadataText(viewerMetaSize),
          registered: getViewerMetadataText(viewerMetaRegistered),
          updated: getViewerMetadataText(viewerMetaUpdated),
        });
        toastr.warning(
          `No fue posible mostrar el contenido del archivo: ${error.message}`,
          "Vista previa no disponible"
        );
      }
      break;
    }
    default: {
      showViewerMessage(
        "Este tipo de archivo no cuenta con vista previa disponible. Utiliza el botón Descargar para obtenerlo.",
        { status: "warning" }
      );
      updateViewerSubtitle(
        "La vista previa no está disponible para este formato."
      );
    }
  }
}
/**
 * Obtiene la configuración visual del ícono asociado a una extensión de archivo.
 * @param {string} ext - Extensión del archivo sin el punto inicial.
 * @returns {{icon: string, badgeClass: string, textClass: string}} Configuración visual sugerida.
 */
function getFileIconByExtension(ext) {
  if (!ext) {
    return {
      icon: "fa-file-o",
      badgeClass: "badge-secondary",
      textClass: "text-light",
    };
  }
  const normalized = String(ext).toLowerCase();
  switch (normalized) {
    case "jpg":
    case "jpeg":
    case "png":
    case "gif":
    case "bmp":
      return {
        icon: "fa-file-image-o",
        badgeClass: "badge-warning",
        textClass: "text-warning",
      };
    case "pdf":
      return {
        icon: "fa-file-pdf-o",
        badgeClass: "badge-danger",
        textClass: "text-danger",
      };
    case "doc":
    case "docx":
      return {
        icon: "fa-file-word-o",
        badgeClass: "badge-primary",
        textClass: "text-white",
      };
    case "xls":
    case "xlsx":
      return {
        icon: "fa-file-excel-o",
        badgeClass: "badge-success",
        textClass: "text-success",
      };
    case "ppt":
    case "pptx":
      return {
        icon: "fa-file-powerpoint-o",
        badgeClass: "badge-warning",
        textClass: "text-warning",
      };
    case "txt":
      return {
        icon: "fa-file-text-o",
        badgeClass: "badge-secondary",
        textClass: "text-light",
      };
    case "zip":
    case "rar":
    case "7z":
      return {
        icon: "fa-file-archive-o",
        badgeClass: "badge-dark",
        textClass: "text-light",
      };
    case "mp3":
    case "wav":
    case "ogg":
      return {
        icon: "fa-file-audio-o",
        badgeClass: "badge-info",
        textClass: "text-light",
      };
    case "mp4":
    case "avi":
    case "mov":
      return {
        icon: "fa-file-video-o",
        badgeClass: "badge-info",
        textClass: "text-light",
      };
    default:
      return {
        icon: "fa-file-o",
        badgeClass: "badge-secondary",
        textClass: "text-light",
      };
  }
}
/**
 * Metodo que se encarga de cargar el nombre del archivo para modificarlo de acuerdo al archivo seleccionado
 */

function selectFile() {
  const inputFiles = document.getElementById("inputFiles");
  const inputName = document.getElementById("inputName");

  // Quita solo la última extensión
  const sinExtension = (filename) => filename.replace(/\.[^.]+$/, "");

  // Solo caracteres permitidos: letras, números, espacio, guion y guion bajo
  const limpiar = (text) => text.replace(/[^a-zA-Z0-9 _-]/g, "");

  inputFiles.addEventListener("change", () => {
    if (!inputFiles.files || inputFiles.files.length === 0) {
      inputName.value = "";
      return;
    }

    // Procesa cada archivo
    const nombres = Array.from(inputFiles.files).map((f) =>
      limpiar(sinExtension(f.name))
    );

    // Une nombres filtrados
    inputName.value = nombres.join(", ");
  });
}
if (viewerMessage) {
  showViewerMessage(defaultViewerMessage);
}
if (downloadButton) {
  updateDownloadButton("#", "", false);
}
if (modalViewFile) {
  $(modalViewFile).on("hidden.bs.modal", () => {
    setViewerTitle("Visor de archivos");
    clearViewerContent();
    showViewerMessage(defaultViewerMessage);
    updateDownloadButton("#", "", false);
    resetViewerMetadata();
  });
}
