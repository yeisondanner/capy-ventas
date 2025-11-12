let table;
toastr.options = {
  closeButton: true,
  onclick: null,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "5000",
  progressBar: true,
  onclick: null,
};
window.addEventListener("DOMContentLoaded", (e) => {
  e.preventDefault();
  loadTable();
  setTimeout(() => {
    saveData();
    confirmationDelete();
    deleteData();
    loadDataUpdate();
    updateDate();
    loadReport();
    loadPermissionRol();
    senDataPermission();
  }, 1500);
});

// Función que carga la tabla con los datos
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Roles/getRoles",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "r_name" },
      { data: "r_description" },
      { data: "status" },
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
          columns: [0, 1, 2, 3],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de roles en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [0, 1, 2, 3],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de roles en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [0, 1, 2, 3],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de roles en PDF",
        className: "btn btn-danger",
        orientation: "vertical",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [0, 1, 2, 3],
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
      },
      {
        targets: [2],
        orderable: true,
        className: "text-justify",
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
      url: base_url + "/Assets/js/libraries/Admin/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      confirmationDelete();
      loadDataUpdate();
      loadReport();
      loadPermissionRol();
    },
  });
}
// Función que guarda los datos en la base de datos
async function saveData() {
  const formSave = document.getElementById("formSave");
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
    const url = base_url + "/Roles/setRoles";
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
        if (data.status) {
          //limpiar el formulario
          formSave.reset();
          //ocultar el modal abierto
          $("#modalSave").modal("hide");
          //actualizar la tabla
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        //recargar las funciones
        setTimeout(() => {
          confirmationDelete();
          loadDataUpdate();
          loadReport();
          loadPermissionRol();
          //quitamos el d-none del elementLoader
          elementLoader.classList.add("d-none");
        }, 500);
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
        "¿Está seguro de eliminar el rol <strong>" + name + " </strong>?";
      //Asiganamos los valores obtenidos y los enviamos a traves de un atributo dentro del btn de confirmacion de eliminar
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-idrole", id);
      confirmDelete.setAttribute("data-namerole", name);
      //abrimos el modal de confirmacion
      $("#confirmModalDelete").modal("show");
    });
  });
}
// Función que se encarga de eliminar un registro
async function deleteData() {
  const confirmDelete = document.getElementById("confirmDelete");
  confirmDelete.addEventListener("click", (e) => {
    e.preventDefault();
    //recibimos las variables del atributo del btn de confirmacion de eliminar en sus constantes
    const id = confirmDelete.getAttribute("data-idrole");
    const name = confirmDelete.getAttribute("data-namerole");
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
    const url = base_url + "/Roles/deleteRoles";
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
        if (data.status) {
          //ocultar el modal abierto
          $("#confirmModalDelete").modal("hide");
          //actualizar la tabla
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        ///recargar las funciones
        setTimeout(() => {
          confirmationDelete();
          loadDataUpdate();
          loadReport();
          loadPermissionRol();
          elementLoader.classList.add("d-none");
        }, 500);
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
//funcion que se encarga de mostrar el modal para actualizar los datos del usuario
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      //obtenemos los atributos del btn update y los almacenamos en una constante
      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description");
      const status = item.getAttribute("data-status");
      //asignamos los valores obtenidos a los inputs del modal
      document.getElementById("update_txtId").value = id;
      document.getElementById("update_txtRoleName").value = name;
      document.getElementById("update_txtRoleDescription").value = description;
      document.getElementById("update_txtRoleStatus").value = status;
      setTimeout(() => {
        //quitamos el d-none del elementLoader
        elementLoader.classList.add("d-none");
      }, 500);
      //abrir el modal
      $("#modalUpdate").modal("show");
    });
  });
}
//funcion que actualiza los datos dl usuarios enviandolos al servidor
async function updateDate() {
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
    const url = base_url + "/Roles/updateRole";
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
        if (data.status) {
          //limpiar el formulario
          formUpdate.reset();
          //ocultar el modal abierto
          $("#modalUpdate").modal("hide");
          //actualizar la tabla
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        //recargar las funciones
        setTimeout(() => {
          confirmationDelete();
          loadDataUpdate();
          loadReport();
          loadPermissionRol();
          //quitamos el d-none del elementLoader
          elementLoader.classList.add("d-none");
        }, 500);
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
//Funcion que carga los datos en el reporte del modal del usuario
function loadReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      ///obtenemos los atributos del btn update y los almacenamos en una constante
      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description");
      const dataStatus = item.getAttribute("data-status");
      const dataRegistrationDate = item.getAttribute("data-registrationdate");
      const dataUpdateDate = item.getAttribute("data-updateDate");
      //asignamos los valores obtenidos a los inputs del modal
      document.getElementById("reportTitle").innerHTML = name;
      document.getElementById("reportCode").innerHTML = "#" + id;
      document.getElementById("reportDescription").innerHTML = description;
      document.getElementById("reportEstado").innerHTML = dataStatus;
      document.getElementById("reportRegistrationDate").innerHTML =
        dataRegistrationDate;
      document.getElementById("reportUpdateDate").innerHTML = dataUpdateDate;
      //Consultamos al servidor que opciones tiene activa el rol
      const url = base_url + "/Roles/getOptionsByRoleAdd?id=" + id;
      fetch(url)
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
          if (data.status) {
            const modules = data.modules;
            const datailsModules = document.getElementById("datailsModules");
            //verificamos si el modulo tiene opciones
            //contamos la cantidad de opciones
            if (modules.length > 0) {
              //recorremos el array de modulos
              let html = "";
              modules.forEach((module) => {
                let badge = module.m_status == "Activo" ? "success" : "danger";
                html += `<ul class="list-group mb-2">
                    <li class="list-group-item active">
                       ${module.m_icon}
                        ${module.m_name} 
                        <span class="badge badge-${badge}">${module.m_status}</span>
                    </li>
                    <li class="list-group-item">
                     <ul class="list-group">
                    `;
                //recorremos el array de opciones
                module.interface.forEach((option) => {
                  let badge =
                    option.urd_status == "Activo" ? "success" : "danger";
                  html += `<li class="list-group-item">
                              <ul class="list-group">
                                <li class="list-group-item bg-secondary active">
                                  <i class="fa fa-check"></i>  ${
                                    option.i_name
                                  } <span class="badge badge-${badge}">${
                    option.urd_status
                  }
                                  </span>
                                </li>
                                <li class="list-group-item">
                                 Ruta: <strong>${option.i_url}</strong>
                                </li>
                                <li class="list-group-item">
                                  ¿Es una opción de menú? : <strong>${
                                    option.i_isOption === "1" ? "Si" : "No"
                                  }
                                  </strong>
                                </li>
                                <li class="list-group-item">
                                ¿Es publica? : 
                                <strong>${
                                  option.i_isPublic === "1" ? "Si" : "No"
                                }
                                  </strong>
                                </li>
                                <li class="list-group-item">
                                ¿Se muestra en el menu de navegacion? : 
                                <strong>${
                                  option.i_isListNav === "1" ? "Si" : "No"
                                }
                                  </strong>
                                  </li>
                                  <li class="list-group-item">
                                  Descripción : ${option.i_description}
                                  </li>
                                  <li class="list-group-item">
                                  Estado de la vista : ${option.i_status}
                                  </li>
                              </ul>                  
                            </li>`;
                });

                html += `
                </ul>
                </li>
                </ul>`;
              });
              datailsModules.innerHTML = html;
              setTimeout(() => {
                //quitamos el d-none del elementLoader
                elementLoader.classList.add("d-none");
              }, 500);
              //abrimos el modal
              $("#modalReport").modal("show");
              return true;
            }
            datailsModules.innerHTML = `<p class="text-center text-secondary">No tiene permisos asignados</p>`;
            //quitamos el d-none del elementLoader
            elementLoader.classList.add("d-none");
            //abrimos el modal
            $("#modalReport").modal("show");
          }
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
    });
  });
}
//Funcion que muestra el modal de los permisos pára el rol
async function loadPermissionRol() {
  const dataPermissionItem = document.querySelectorAll(".permission-item");
  dataPermissionItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      //capturamos los atributos del btn
      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description");
      //Los mostramos en el modal
      document.getElementById("permissionTitleRol").innerHTML = name;
      document.getElementById("permissionDescriptionRol").innerHTML =
        description;
      //ahora obtenemos los modulos e interfaces que hay para asignar a este rol al servidor
      const url = base_url + "/Roles/getOptionByRoleAll?id=" + id;
      fetch(url)
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
          if (data.status) {
            const modulesInterfaces =
              document.getElementById("modulesInterfaces");
            const modulesAll = data.modules;
            let html = "";
            modulesAll.forEach((element) => {
              html += `
               <div class="col-md-6">
                                <div class="module-card">
                                    <h5 class="text-center mb-3">${element.m_icon} ${element.m_name}</h5>`;
              element.interface.forEach((interface) => {
                let isOption =
                  interface.i_isOption === "1"
                    ? `<span class="badge badge-primary">Opción</span>`
                    : `<span class="badge badge-info">Vista</span>`;
                let isPublic =
                  interface.i_isPublic === "1"
                    ? `<span class="badge badge-danger">Público</span>`
                    : `<span class="badge badge-success">Privado</span>`;
                let isListNav =
                  interface.i_isListNav === "1"
                    ? `<span class="badge badge-warning">Visible en navegación</span>`
                    : `<span class="badge badge-secondary">Oculto en navegación</span>`;
                html += `<div class="d-flex justify-content-between align-items-center mb-2 border-bottom" >
                                        <span>${interface.i_name}</span>
                                        ${isOption}
                                        ${isPublic}
                                        ${isListNav}
                                        <div class="toggle-flip">
                                            <label>
                                                <input type="checkbox" class="input-interface" data-idRole="${id}" data-idInterface="${
                  interface.idInterface
                }" ${
                  interface.detail
                    ? interface.detail.urd_status === "Activo"
                      ? `checked data-idDetail="${interface.detail.idUserRoleDetail}" data-status="Inactivo"`
                      : `data-idDetail="${interface.detail.idUserRoleDetail}" data-status="Activo"`
                    : ""
                }>
                                                <span class="flip-indecator" data-toggle-on="ON"
                                                    data-toggle-off="OFF"></span>
                                            </label>
                                        </div>
                                    </div>`;
              });

              html += `</div>
                            </div>`;
            });
            modulesInterfaces.innerHTML = html;
            //abrimos el model de permisos
            sendPermission();
            //Agregamos el d-none del elementLoader
            elementLoader.classList.add("d-none");
            $("#modalPermission").modal("show");
          }
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
    });
  });
}
//Funcion que almacena los datos de los permisos
async function sendPermission() {
  const arrInterface = document.querySelectorAll(".input-interface");
  arrInterface.forEach((item) => {
    item.addEventListener("change", () => {
      //quitamos el d-none del elementLoader
      elementLoader.classList.remove("d-none");
      //recolectamos los atributos del input
      const idInterface = item.getAttribute("data-idInterface");
      const idDetail = item.getAttribute("data-idDetail");
      const idRole = item.getAttribute("data-idRole");
      const status = item.getAttribute("data-status");
      //enviamos los datos al servidor
      //creamos un formulario
      const form = new FormData();
      //agregamos los campos al formulario
      form.append("idInterface", idInterface);
      //verificamos si el idDetail es null o no
      if (idDetail != null) {
        form.append("idDetail", idDetail);
      }
      //verificamos si el status es null o no
      if (status != null) {
        form.append("status", status);
        if (status === "Activo") {
          item.setAttribute("data-status", "Inactivo");
        } else {
          item.setAttribute("data-status", "Activo");
        }
      }
      form.append("idRole", idRole);
      //creamos los encabezados
      const header = new Headers();
      //preparamos todo en un array
      const config = {
        method: "POST",
        headers: header,
        node: "no-cache",
        cors: "cors",
        body: form,
      };
      //preparamos la ruta
      const url = base_url + "/Roles/preparePermission";
      //enviamos la solicitud
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
          toastr[data.type](data.message, data.title);
          //quitamos el d-none del elementLoader
          elementLoader.classList.add("d-none");
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
  });
}
//Funcion que registra los permisos en la base de datos
async function senDataPermission() {
  const btnSendPermission = document.getElementById("btn-send-permission");
  btnSendPermission.addEventListener("click", () => {
    //quitamos el d-none del elementLoader
    elementLoader.classList.remove("d-none");
    //enviamos los datos
    const url = base_url + "/Roles/setDataPermission";
    fetch(url)
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
        if (data.status) {
        }
        toastr[data.type](data.message, data.title);
        $("#modalPermission").modal("hide");
        //quitamos el d-none del elementLoader
        elementLoader.classList.add("d-none");
      })
      .catch((error) => {
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
