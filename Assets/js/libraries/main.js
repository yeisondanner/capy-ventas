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

  // Set initial active toggle
  $("[data-toggle='treeview.'].is-expanded")
    .parent()
    .toggleClass("is-expanded");

  //Activate bootstrip tooltips
  $("[data-toggle='tooltip']").tooltip();
})();
//user activation
if (
  document.getElementById("sidebarToggle") &&
  document.getElementById("sidebar") &&
  document.getElementById("closeSidebar")
) {
  const sidebar = document.getElementById("sidebar");
  const openBtn = document.getElementById("sidebarToggle");
  const closeBtn = document.getElementById("closeSidebar");

  openBtn.addEventListener("click", function () {
    sidebar.classList.add("active");
  });

  closeBtn.addEventListener("click", function () {
    sidebar.classList.remove("active");
  });
}
//creamos una constante del loader
const elementLoader = document.getElementById("loaderOverlay");
document.addEventListener("DOMContentLoaded", function () {
  getNotifications();
  setTimeout(() => {
    showNotificationInfo();
    openModalSeeMoreNotifications();
    elementLoader.classList.add("d-none");
  }, 1000);
  //creamos un intervalo para que se ejecute cada 10 segundos
  setInterval(() => {
    getNotifications();
  }, 10000);
});
//funcion que se encarga cargar el centro de notificaciones
async function getNotifications() {
  //VALIDAMOS QUE EXISTAN LOS ELEMENTOS
  if (
    !document.getElementById("notification-list") &&
    !document.getElementById("notification-count")
  ) {
    return;
  }
  const notifications = document.getElementById("notification-list");
  const notifiacationCount = document.getElementById("notification-count");
  const url = base_url + "/Notification/getNotificationsOfUser";
  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          "Network response was not ok " +
            response.status +
            " " +
            response.statusText
        );
      }
      return response.json();
    })
    .then((data) => {
      let itemsNotification = "";
      let cont = 0;
      data.forEach((element) => {
        itemsNotification += getNotificationItem(element);
        if (element.n_is_read == 0) {
          cont++;
        }
      });
      //mostramos en la parte final  de la lista de notificaciones un enlace para ver todas las notificaciones
      itemsNotification += `<li class="app-notification__footer view-list-notifications text-primary" style="cursor:pointer;" >Ver todas las notificaciones.</li>`;
      notifications.innerHTML = itemsNotification;
      setTimeout(() => {
        //agregamos la funcion que se encargar de mostrar el detalle de la notificacion
        showNotificationInfo();
        //cargamos el elemento de lista de notificaciones
        openModalSeeMoreNotifications();
      }, 500);
      //mostramos un punto rojo en el icono de notificaciones que indica que hay notificaciones no leidas
      if (cont > 0) {
        notifiacationCount.classList.remove("d-none");
        return;
      }
      notifiacationCount.classList.add("d-none");
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
}

//function que encargar de mostrar la prioridad de las notificaciones
function getPriority(priority) {
  let badge = "";
  if (priority == 1) {
    badge = `<span class="badge bage-pill badge-info">
                                        Normal
                                        </span>`;
  } else if (priority == 2) {
    badge = `<span class="badge bage-pill badge-warning">
                                        Moderado
                                        </span>`;
  } else if (priority == 3) {
    badge = `<span class="badge bage-pill badge-danger">
                                        Crítico
                                        </span>`;
  }
  return badge;
}
//funcion que devuelve el item de la notificacion
function getNotificationItem(notification) {
  return `
                            <li>
                                <a class="app-notification__item item-notification" data-info-notification='${JSON.stringify(
                                  notification
                                )}'  href="#">
                                    <span class="app-notification__icon position-relative d-inline-block">
                                        <span class="fa-stack fa-lg">
                                            <i class="fa fa-circle fa-stack-2x text-${
                                              notification.n_color
                                            }">
                                            </i>
                                            <i class="fa ${
                                              notification.n_icon
                                            } fa-stack-1x fa-inverse">
                                            </i>                                            
                                        </span>
                                        ${
                                          notification.n_is_read == 1
                                            ? ""
                                            : `<i class="fa fa-circle fa-xs text-danger position-absolute pulse" style="top: 0; right: 30%;" ></i>`
                                        }
                                    </span>
                                    <div class="w-100">
                                        ${getPriority(notification.n_priority)}
                                        <p class="app-notification__message">${
                                          notification.n_title
                                        }</p>
                                        <p class="app-notification__meta">${
                                          notification.description
                                        }</p>
                                        <p class="text-muted fw-light text-right" style="font-size: 10.8px; font-style: italic;">${
                                          notification.date
                                        }</p>
                                    </div>
                                </a>
                            </li>`;
}
//funcion que se encarga de mostar la informacion de la notificacion al dar click sobre ella
function showNotificationInfo() {
  const itemsNotification = document.querySelectorAll(".item-notification");
  itemsNotification.forEach((item) => {
    item.addEventListener("click", (e) => {
      const notification = JSON.parse(
        item.getAttribute("data-info-notification")
      );
      //mostramos el modalNotificacionLabel dependiendo del tipo de notificacion 'info','success','warning','error','custom'
      const modalNotificacionLabel = document.querySelector(
        "#modalNotificacionLabel"
      );
      const modalNotificacionHeader = document.querySelector(
        "#modalNotificacionHeader"
      );
      //eliminamos todas las clases del modalNotificacionHeader sin necesidad de saber el nombre de la clase
      modalNotificacionHeader.classList.remove(
        ...modalNotificacionHeader.classList
      );
      //agregamos la clase que corresponde al tipo de notificacion
      switch (notification.n_type) {
        case "info":
          //mostramos el titulo de la notificacion
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
          break;
        case "success":
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-check-circle mr-2"></i> Notificación de éxito`;
          break;
        case "warning":
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-exclamation-circle mr-2"></i> Notificación de advertencia`;
          break;
        case "error":
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-exclamation-triangle mr-2"></i> Notificación de error`;
          break;
        case "custom":
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación personalizada`;
          break;
        default:
          modalNotificacionLabel.innerHTML = ` <i class="fa fa-info-circle mr-2"></i> Notificación de información`;
          break;
      }
      //cambiamos el color del encabezado de la notificacion
      modalNotificacionHeader.classList.add(
        `bg-${notification.n_color}`,
        "text-white",
        "modal-header"
      );
      //icono de la notificacion
      const notificacionIcon = document.querySelector("#notificacionIcon");
      notificacionIcon.innerHTML = ` <span class="fa-stack fa-2x text-${notification.n_color}">
                                          <i class="fa fa-circle fa-stack-2x text-${notification.n_color}"></i>
                                          <i class="fa ${notification.n_icon} fa-stack-1x fa-inverse"></i>
                                      </span>`;
      const notificacionHeader = document.querySelector("#notificacionHeader");
      notificacionHeader.innerHTML = ` <h4 class="mb-1 text-dark font-weight-bold">${notification.n_title}</h4>
                                        <span class="badge badge-${notification.n_color}">Tipo: ${notification.n_type}</span>
                                        <small class="text-muted d-block mt-1">Fecha: ${notification.date}</small>`;
      const notificacionDescription = document.querySelector(
        "#notificacionDescription"
      );
      notificacionDescription.innerHTML = notification.n_description;
      const notificationPriority = document.getElementById(
        "notificationPriority"
      );
      const notificationRead = document.getElementById("notificationRead");
      const notificationState = document.getElementById("notificationState");
      const notificationId = document.getElementById("notificationId");
      const notificationLink = document.getElementById("notificationLink");
      //verificamos la prioridad
      if (notification.n_priority == 1) {
        notificationPriority.innerHTML = `<i
                                    class="fa fa-flag text-danger mr-2"></i><strong>Prioridad:</strong> Normal`;
      } else if (notification.n_priority == 2) {
        notificationPriority.innerHTML = `<i
                                    class="fa fa-flag text-warning mr-2"></i><strong>Prioridad:</strong> Moderado`;
      } else if (notification.n_priority == 3) {
        notificationPriority.innerHTML = `<i
                                    class="fa fa-flag text-success mr-2"></i><strong>Prioridad:</strong> Crítico`;
      } else {
        notificationPriority.innerHTML = `<i
                                    class="fa fa-flag text-secondary mr-2"></i><strong>Prioridad:</strong> Normal`;
      }
      //validamos si esta leido o no
      if (notification.n_is_read == 1) {
        notificationRead.innerHTML = `<i
                                    class="fa fa-check-circle text-success mr-2"></i><strong>Leída:</strong> Si`;
      } else {
        notificationRead.innerHTML = `<i
                                    class="fa fa-times-circle text-danger mr-2"></i><strong>Leída:</strong> No`;
      }
      notificationState.innerHTML = `<i
                                    class="fa fa-info-circle text-info mr-2"></i><strong>Estado:</strong> ${notification.n_status}`;
      notificationId.innerHTML = `<i class="fa fa-hashtag text-dark mr-2"></i><strong>ID:</strong> #${notification.idNotification}`;
      if (notification.n_link != null) {
        notificationLink.innerHTML = ` <strong>Enlace:</strong><br>
                        <a href="${notification.n_link}" target="_blank">
                            ${notification.n_link}
                        </a>`;
      }
      if (!item.getAttribute("data-admin")) {
        updateNotificationRead(notification.idNotification);
      }
      //mostramos el modal
      $("#modalNotificacion").modal("show");
    });
  });
}
//function que se encarga de actualizar el estado de leido de la notificacion elegida
function updateNotificationRead(idNotification) {
  const dataForm = new FormData();
  dataForm.append("id", idNotification);
  const dataHeaders = new Headers();
  const config = {
    method: "POST",
    body: dataForm,
    headers: dataHeaders,
    cahe: "no-cache",
  };
  const url = base_url + "/Notification/updateNotificationRead";
  fetch(url, config)
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          `Ocurrio un error al actualizar la notificacion ${response.status}-${response.statusText}`
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
      getNotifications();
      console.log(data);
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
    });
}
let tableNotifications;
//modelo de que abre el modal de ver mas notificaciones
function openModalSeeMoreNotifications() {
  const viewListNotifications = document.querySelectorAll(
    ".view-list-notifications"
  );
  viewListNotifications.forEach((item) => {
    item.addEventListener("click", () => {
      //cargamos la tabla
      loadNotificationsAllUser();
      const modal = document.getElementById("seeMoreNotifications");
      modal.setAttribute("aria-hidden", "true");
      //mostramos el modal
      $("#seeMoreNotifications").modal("show");
    });
  });
}
function loadNotificationsAllUser() {
  tableNotifications = $("#tableNotifications").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Notification/get_notifications_by_user_for_id",
      dataSrc: "",
    },
    columns: [
      { data: "icon" },
      { data: "title" },
      { data: "description" },
      { data: "link" },
      { data: "type" },
      { data: "priority" },
      { data: "read" },
      { data: "date_create" },
      { data: "actions" },
    ],
    columnDefs: [
      {
        targets: 0,
        className: "text-center",
        searchable: false,
        orderable: false,
        render: function (data, type, row, meta) {
          return `<i class="fa ${data}"></i>`;
        },
      },
      {
        targets: 1,
        className: "text-left text-nowrap text-capitalize font-weight-bold",
      },
      {
        targets: 2,
        className: "text-left text-nowrap text-capitalize",
      },
      {
        targets: 3,
        orderable: false,
        className: "text-center",
        searchable: false,
        render: function (data, type, row, meta) {
          //validamos si no esta vacia
          if (data == "") {
            return `<i class="fa fa-times-circle text-danger"></i>`;
          }
          return `<a href="${data}" target="_blank"><i class="fa fa-link text-info"></i></a>`;
        },
      },
      {
        targets: 4,
        className: "text-center",
        searchable: false,
        render: function (data, type, row, meta) {
          return `<span class="badge badge-${row.color}">${data}</span>`;
        },
      },
      {
        targets: 5,
        className: "text-center",
        searchable: false,
        render: function (data, type, row, meta) {
          return getPriority(data);
        },
      },
      {
        targets: 6,
        className: "text-center",
        searchable: false,
        orderable: false,
        render: function (data, type, row, meta) {
          if (data == 0) {
            return `<i class="fa fa-times-circle text-danger"></i>`;
          }
          return `<i class="fa fa-check-circle text-success"></i>`;
        },
      },
      {
        targets: 8,
        className: "text-center",
        searchable: false,
        orderable: false,
      },
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
    responsive: true,
    bProcessing: true,
    destroy: true,
    iDisplayLength: 10,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      showNotificationInfo();
    },
  });
}
