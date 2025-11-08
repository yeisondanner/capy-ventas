window.addEventListener("DOMContentLoaded", () => {
  setTimeout(() => {
    decorateLoaderOptions();
    initializeNavigation();
    saveData();
  }, 500);
});

/**
 * Activa los efectos visuales del selector de loaders y mantiene la tarjeta seleccionada.
 * @returns {void}
 */
function decorateLoaderOptions() {
  const radios = document.querySelectorAll(".loader-radio");
  const labels = document.querySelectorAll(".loader-option");

  if (!radios.length || !labels.length) {
    return;
  }

  radios.forEach((radio) => {
    radio.addEventListener("change", () => {
      labels.forEach((label) => label.classList.remove("selected"));
      if (radio.checked) {
        const selectedCard = radio.closest(".loader-option");
        if (selectedCard) {
          selectedCard.classList.add("selected");
        }
      }
    });
  });
}

/**
 * Gestiona el estado activo del menú lateral y lo sincroniza con el desplazamiento de la página.
 * @returns {void}
 */
function initializeNavigation() {
  const navLinks = document.querySelectorAll(".system-nav__link");
  const sections = document.querySelectorAll(".system-section");

  if (!navLinks.length || !sections.length) {
    return;
  }

  const activateLink = (sectionId) => {
    if (!sectionId) {
      return;
    }
    navLinks.forEach((link) => {
      const linkTarget = link.getAttribute("href");
      const isActive = linkTarget && linkTarget.replace("#", "") === sectionId;
      link.classList.toggle("active", Boolean(isActive));
    });
  };

  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      const targetId = link.getAttribute("href");
      activateLink(targetId ? targetId.replace("#", "") : "");
    });
  });

  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            activateLink(entry.target.id);
          }
        });
      },
      {
        rootMargin: "-30% 0px -30% 0px",
        threshold: [0.25, 0.6],
      }
    );

    sections.forEach((section) => observer.observe(section));
  }
}

/**
 * Envía la configuración del sistema al servidor y muestra el resultado con notificaciones Toastr.
 * @returns {void}
 */
function saveData() {
  const formSave = document.getElementById("formSave");
  if (!formSave) {
    return;
  }
  formSave.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formSave);
    const header = new Headers();
    const config = {
      method: "POST",
      headers: header,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/System/setInfoGeneral";
    //quitamos el d-none del elementLoader
    elementLoader.classList.remove("d-none");
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Error en la solicitud " +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        toastr.options = {
          closeButton: true,
          onclick: null,
          showDuration: "300",
          hideDuration: "1000",
          timeOut: "5000",
          progressBar: true,
          onclick: null,
        };
        if (!data.status) {
          toastr[data.type](data.message, data.title);
          elementLoader.classList.add("d-none");
          return false;
        }
        toastr[data.type](data.message, data.title);
        elementLoader.classList.add("d-none");
        return true;
      })
      .catch((error) => {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrio un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
/**
 * Muestra una vista previa del logotipo y actualiza la etiqueta del input de archivos.
 * @param {Event} event - Evento change del input file para el logotipo del sistema.
 * @returns {void}
 */
function previewLogo(event) {
  const { target } = event;
  if (!target || !target.files || !target.files.length) {
    return;
  }

  const [file] = target.files;
  const reader = new FileReader();
  reader.onload = function () {
    const output = document.getElementById("logoPreview");
    if (output) {
      output.src = reader.result;
      output.classList.remove("d-none");
    }
  };
  reader.readAsDataURL(file);

  const label = target.nextElementSibling;
  if (label && label.classList.contains("custom-file-label")) {
    label.textContent = file.name;
  }
}
