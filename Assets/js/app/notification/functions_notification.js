let table;
/**
 * Funcion que se encarga de inicializar las funciones de la pagina
 */
window.addEventListener("DOMContentLoaded", function () {
  // Cargamos la tabla de datos
  loadTable();
  setTimeout(() => {
    // Initialize select2 de la lista de usuarios
    $("#slctUsers").select2({
      placeholder: "Seleccione los destinatarios o destinatario",
      minimumResultsForSearch: Infinity,
      language: "es",
      allowClear: true,
      width: "95%",
      height: "100%",
      multiple: true,
      tags: false,
      templateResult: formatUser,
      templateSelection: formatUserSelection,
      ajax: {
        url: base_url + "/Users/get_users_active_for_notification",
        dataType: "json",
        delay: 250,
        processResults: function (data) {
          return {
            results: data,
          };
        },
        cache: true,
      },
    });
    //inicializamos el allUsers
    allUsers();
    //inicializamos em metodo save
    save();
    //eliminamos el registro
    deleteData();
    //eliminamos la notificacion
    confirmationDelete();
    //cargamos la tabla de notificaciones
    showNotificationInfo();
    loadDataUpdate();
    updateDate();
    buttonCloseModal();
  }, 1500);
});

function formatUser(user) {
  if (user.loading) return "Cargando...";
  return $("<span>" + user.text + "</span>");
}

function formatUserSelection(user) {
  return user.text || user.id;
}
/**
 * Funcion que al momento de hacer click en el button allUsers
 * este desactiva el select2 de la lista de usuarios indicando que ya se seleccionaron todos los usuarios
 * y se cambia el color del mismo boton a success y el icono a check
 */
function allUsers() {
  let allUsers = document.getElementById("allUsers");
  let slctUsers = document.getElementById("slctUsers");
  allUsers.addEventListener("click", () => {
    if (slctUsers.disabled && allUsers.classList.contains("ok")) {
      slctUsers.disabled = false;
      allUsers.classList.add("btn-primary");
      allUsers.classList.remove("btn-success", "ok");
      allUsers.innerHTML = '<i class="fa fa-users"></i>';
    } else {
      slctUsers.disabled = true;
      allUsers.classList.add("btn-success", "ok");
      allUsers.classList.remove("btn-primary");
      allUsers.innerHTML = '<i class="fa fa-check"></i>';
    }
  });
}
/**
 * Cambio de colores de la previsualizacion de la notificacion
 */
function chageColorPreviewNotification(color) {
  previewNotification.classList.remove(...previewNotification.classList);
  previewNotification.classList.add(
    "text-white",
    "p-3",
    "rounded-top",
    "bg-" + color
  );
  previewIconContainer.classList.remove(...previewIconContainer.classList);
  previewIconContainer.classList.add("fa-stack", "fa-2x", "text-" + color);
  previewIconCircle.classList.remove(...previewIconCircle.classList);
  previewIconCircle.classList.add(
    "fa",
    "fa-circle",
    "fa-stack-2x",
    "text-" + color
  );
  previewType.classList.remove(...previewType.classList);
  previewType.classList.add("badge", "badge-" + color);
}
function updateChageColorPreviewNotification(color) {
  updatePreviewNotification.classList.remove(
    ...updatePreviewNotification.classList
  );
  updatePreviewNotification.classList.add(
    "text-white",
    "p-3",
    "rounded-top",
    "bg-" + color
  );
  updatePreviewIconContainer.classList.remove(
    ...updatePreviewIconContainer.classList
  );
  updatePreviewIconContainer.classList.add(
    "fa-stack",
    "fa-2x",
    "text-" + color
  );
  updatePreviewIconCircle.classList.remove(
    ...updatePreviewIconCircle.classList
  );
  updatePreviewIconCircle.classList.add(
    "fa",
    "fa-circle",
    "fa-stack-2x",
    "text-" + color
  );
  updatePreviewType.classList.remove(...updatePreviewType.classList);
  updatePreviewType.classList.add("badge", "badge-" + color);
}
/**
 * Funcion que se encarga de cambiar el titulo de l header de acuerdo al tipo de la
 * notificacion
 */
function changeTitleHeader(type) {
  previewType.textContent = `Tipo: ${type}`;
  switch (type) {
    case "info":
      //mostramos el titulo de la notificacion
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
      break;
    case "success":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-check-circle mr-2"></i> Notificación de éxito`;
      break;
    case "warning":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-exclamation-circle mr-2"></i> Notificación de advertencia`;
      break;
    case "error":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-exclamation-triangle mr-2"></i> Notificación de error`;
      break;
    case "custom":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación personalizada`;
      break;
    default:
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
      break;
  }
}
function updateChangeTitleHeader(type) {
  updatePreviewType.textContent = `Tipo: ${type}`;
  switch (type) {
    case "info":
      //mostramos el titulo de la notificacion
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
      break;
    case "success":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-check-circle mr-2"></i> Notificación de éxito`;
      break;
    case "warning":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-exclamation-circle mr-2"></i> Notificación de advertencia`;
      break;
    case "error":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-exclamation-triangle mr-2"></i> Notificación de error`;
      break;
    case "custom":
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación personalizada`;
      break;
    default:
      previewTitlePrincipal.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
      break;
  }
}
/**
 * Funcione que envia el formulario al backend
 * y poder registrar la notificacion en la base de datos
 * mediante fetch
 */
function save() {
  const formSave = document.getElementById("formSave");
  formSave.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(formSave);
    const headers = new Headers();
    const config = {
      method: "POST",
      headers: headers,
      body: formData,
      mode: "cors",
      cache: "default",
    };
    const url = base_url + "/Notification/setNotification";
    elementLoader.classList.remove("d-none");
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new Error(response.status + " " + response.statusText);
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
        //actualizar la tabla
        table.ajax.reload(null, false);
        //limpiar el formulario
        formSave.reset();
        //metodo de confirmacion de eliminacion
        confirmationDelete();
        loadDataUpdate();
        //ocultar el modal abierto
        $("#modalSave").modal("hide");
        //quitamos el d-none del elementLoader
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
// Función que carga la tabla con los datos
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Notification/get_notifications",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "u_fullname" },
      { data: "idNotification" },
      { data: "n_title" },
      { data: "description" },
      { data: "n_link" },
      { data: "n_icon" },
      { data: "n_type" },
      { data: "n_priority" },
      { data: "n_is_read" },
      { data: "n_notification_email" },
      { data: "n_status" },
      { data: "n_created_at" },
      { data: "actions" },
    ],
    dom: "lBfrtip",
    buttons: [
      {
        extend: "copyHtml5",
        text: "<i class='fa fa-copy'></i> Copiar",
        titleAttr: "Copiar",
        className: "btn btn-secondary",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de notificaciones en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de notificaciones en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de notificaciones en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        },
      },
    ],
    columnDefs: [
      {
        targets: [0],
        orderable: true,
        className: "text-center",
        searchable: false,
      },
      {
        targets: [1],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          //devolvemos en un card el nombre del usuario con su foto
          return `<div class="d-flex align-items-center">
                  <img src="${row.u_profile}" alt="${row.u_fullname}" style="width: 50px; height: 50px; border-radius: 50%;">
                  <div>
                    <p style="font-weight: bold; font-size: 12px;">${row.u_fullname}</p>
                    <p class="text-muted" style="font-size: 10px;">${row.u_email}</p>
                  </div>
                </div>`;
        },
      },
      {
        targets: [2],
        orderable: true,
        className: "text-center",
        searchable: true,
      },
      {
        targets: [3],
        orderable: true,
        className: "text-center",
        searchable: true,
      },
      {
        targets: [4],
        className: "text-justify",
      },
      {
        targets: [5],
        className: "text-center",
        render: function (data, type, row) {
          return `<a href="${row.n_link}" target="_blank" class="text-${row.n_color}">${row.n_link}</a>`;
        },
      },
      {
        targets: [6],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          return `<i class="fa ${row.n_icon} text-${row.n_color}"></i>`;
        },
      },
      {
        targets: [7],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          return `<span class="badge badge-${row.n_color}">${row.n_type}</span>`;
        },
      },
      {
        targets: [8],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          if (data == 1) {
            return `<span class="badge badge-${row.n_color}">Baja</span>`;
          } else if (data == 2) {
            return `<span class="badge badge-${row.n_color}">Media</span>`;
          } else if (data == 3) {
            return `<span class="badge badge-${row.n_color}">Alta</span>`;
          }
        },
      },
      {
        targets: [9],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          if (data == 1) {
            return `<span class="badge badge-success"><i class="fa fa-check"></i> Leido</span>`;
          } else if (data == 0) {
            return `<span class="badge badge-danger"><i class="fa fa-times"></i> No leido</span>`;
          }
        },
      },
      {
        targets: [10],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          //verificamos si esta si se agrega un check y icono de correo y si esta no se agrega una x
          if (data == "Si") {
            return `<span class="badge badge-success"><i class="fa fa-check "></i> <i class="fa fa-envelope"></i></span>`;
          } else if (data == "No") {
            return `<span class="badge badge-danger"><i class="fa fa-times "></i> </span>`;
          }
        },
      },
      {
        targets: [11],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          if (data == "activo") {
            return `<span class="badge badge-success">Activo</span>`;
          } else if (data == "inactivo") {
            return `<span class="badge badge-danger">Inactivo</span>`;
          }
        },
      },
      {
        targets: [12],
        orderable: true,
        className: "text-center",
        searchable: true,
        render: function (data, type, row) {
          return `<i class="fa fa-calendar text-info"></i> ${data}`;
        },
      },
      {
        targets: [13],
        orderable: false,
        className: "text-center",
        searchable: false,
      },
    ],
    responsive: "true",
    bProcessing: true,
    destroy: true,
    iDisplayLength: 10,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      confirmationDelete();
      showNotificationInfo();
      loadDataUpdate();
    },
  });
}
// Función que confirma la eliminación
function confirmationDelete() {
  const arrBtnDeleteItem = document.querySelectorAll(".delete-item");
  arrBtnDeleteItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      //obtenemos los atributos del btn delete y los almacenamos en una constante
      const name = item.getAttribute("data-name");
      const id = item.getAttribute("data-id");
      //Preguntamos en el modal si esta seguro de eliminar elar el registro
      document.getElementById("txtDelete").innerHTML =
        "¿Está seguro de eliminar la notificacion <strong>" +
        name +
        " </strong>?";
      //Asiganamos los valores obtenidos y los enviamos a traves de un atributo dentro del btn de confirmacion de eliminar
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-id", id);
      confirmDelete.setAttribute("data-name", name);

      //abrimos el modal de confirmacion
      $("#confirmModalDelete").modal("show");
    });
  });
}
// Función que se encarga de eliminar un registro
function deleteData() {
  const confirmDelete = document.getElementById("confirmDelete");
  confirmDelete.addEventListener("click", (e) => {
    e.preventDefault();
    //recibimos las variables del atributo del btn de confirmacion de eliminar en sus constantes
    const id = confirmDelete.getAttribute("data-id");
    const name = confirmDelete.getAttribute("data-name");
    const token = confirmDelete.getAttribute("data-token");
    //creamos un array con los valores recuperados
    const arrValues = {
      id: id,
      name: name,
      token: token,
    };
    const header = { "Content-Type": "application/json" };
    const config = {
      method: "DELETE",
      headers: header,
      body: JSON.stringify(arrValues),
    };
    //La ruta donde se apunta del controlador
    const url = base_url + "/Notification/deleteNotification";
    //quitamos el d-none del elementLoader
    elementLoader.classList.remove("d-none");
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
          return false;
        }
        //ocultar el modal abierto
        $("#confirmModalDelete").modal("hide");
        //actualizar la tabla
        table.ajax.reload(null, false);
        toastr[data.type](data.message, data.title);
        confirmationDelete();
        loadDataUpdate();
        ///recargar las funciones
        setTimeout(() => {
          elementLoader.classList.add("d-none");
        }, 500);
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
//funcion que se encarga de mostrar el modal para actualizar los datos del usuario
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      data = JSON.parse(item.getAttribute("data-data"));
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      updateImgUser.src = data.u_profile;
      updateNameUser.innerHTML = data.u_fullname;
      updateEmailUser.innerHTML = data.u_email;
      updateTxtTitle.value = data.n_title;
      updateTxtDescription.value = data.n_description;
      updateTxtLink.value = data.n_link;
      updateSlctIcon.value = data.n_icon;
      updateSlctType.value = data.n_type;
      updateSlctColor.value = data.n_color;
      updateSlctPriority.value = data.n_priority;
      updatePreviewStatus.value = data.n_status;
      //hacemos que estos input que se active los eventos change y keyup
      const eventChange = new Event("change");
      const eventKeyup = new Event("keyup");
      updateTxtTitle.dispatchEvent(eventKeyup);
      updateTxtDescription.dispatchEvent(eventKeyup);
      updateTxtLink.dispatchEvent(eventKeyup);
      updateSlctIcon.dispatchEvent(eventChange);
      updateSlctType.dispatchEvent(eventChange);
      updateSlctColor.dispatchEvent(eventChange);
      updateSlctPriority.dispatchEvent(eventChange);
      updatePreviewStatus.dispatchEvent(eventChange);

      setTimeout(() => {
        //quitamos el d-none del elementLoader
        elementLoader.classList.add("d-none");
      }, 500);
      //creamos el input oculto para enviar el id
      const inputId = document.createElement("input");
      inputId.setAttribute("type", "hidden");
      inputId.setAttribute("name", "updateTxtIdNotification");
      //agregamos el valor del id al input
      inputId.setAttribute("id", "updateTxtIdNotification");
      inputId.setAttribute("value", data.idNotification);
      //agregamos el input al formulario
      const form = document.getElementById("formUpdate");
      //validamos que el input solo se agregue una vez
      if (form.querySelector("#updateTxtIdNotification")) {
        return false;
      }
      form.appendChild(inputId);
      //abrir el modal
      $("#modalUpdate").modal("show");
    });
  });
}
//funcion que actualiza los datos dl usuarios enviandolos al servidor
function updateDate() {
  const formUpdate = document.getElementById("formUpdate");
  formUpdate.addEventListener("submit", (e) => {
    //enviamos el formulario por metodo PUT con todo archivo de imagen
    e.preventDefault();
    const formData = new FormData(formUpdate);
    const header = new Headers();
    const config = {
      method: "POST",
      headers: header,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/Notification/updateNotification";
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
          return false;
        }
        //limpiar el formulario
        formUpdate.reset();
        //ocultar el modal abierto
        $("#modalUpdate").modal("hide");
        //actualizar la tabla
        table.ajax.reload(null, false);
        toastr[data.type](data.message, data.title);
        //recargar las funciones
        setTimeout(() => {
          confirmationDelete();
          loadDataUpdate();
          //eliminamos este input updateTxtIdNotification
          const inputId = document.getElementById("updateTxtIdNotification");
          formUpdate.removeChild(inputId);
          //quitamos el d-none del elementLoader
          elementLoader.classList.add("d-none");
        }, 500);
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
//button que se encarga de cerrar el modal de confirmacion de eliminar
function buttonCloseModal() {
  const btnclosemodal = document.querySelectorAll(".btn-close-modal");
  btnclosemodal.forEach((btn) => {
    btn.addEventListener("click", () => {
      const inputId = document.getElementById("updateTxtIdNotification");
      formUpdate.removeChild(inputId);
      $("#modalUpdate").modal("hide");
    });
  });
}
