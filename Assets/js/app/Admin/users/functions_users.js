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
    loadSelect();
    saveData();
    confirmationDelete();
    deleteData();
    loadUserReport();
    loadDataUpdate();
    updateDate();
    loadImagePreview();
    getPeopleReniec();
    sicronizarCampos();
  }, 1500);
});
window.addEventListener("click", (e) => {
  loadUserReport();
  confirmationDelete();
  loadDataUpdate();
});
//Funcion que se encarga de listar la tabla
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Users/getUsers",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "u_fullname" },
      { data: "u_dni" },
      { data: "u_user" },
      { data: "u_email" },
      { data: "r_name" },
      { data: "u_space_limit" },
      { data: "u_personal_folder_name" },
      { data: "u_online" },
      { data: "u_login_attempts" },
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
          columns: [1, 2, 3, 4, 5, 6],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de usuarios en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de usuarios en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de usuarios en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6],
        },
      },
    ],
    columnDefs: [
      {
        targets: [0, 6],
        className: "text-center",
      },
      {
        targets: [1, 2, 3, 4, 5],
        className: "text-left",
      },
      {
        targets: [6],
        className: "text-center",
        render: function (data, type, row) {
          //validamos si es cero colocamos ilimitado
          if (data == 0) {
            return "Ilimitado";
          }
          return data + " GB";
        },
      },
      {
        targets: [8],
        render: function (data, type, row) {
          //hacemos que aparecas un punto verde si esta 1 y si no aparece un punto gris
          if (data == 1) {
            return '<i class="fa fa-circle" title="El usuario se encuentra activo actualmente" style="color: green;"></i>';
          } else {
            return '<i class="fa fa-circle" style="color: gray;"></i>';
          }
        },
      },
      {
        targets: [7],
        className: "text-center",
        render: function (data, type, row) {
          //le damos mas estilos al resultado de la ruta
          return (
            '<span class="badge badge-info" title="' +
            data +
            '">./' +
            data +
            "</span>"
          );
        },
      },
      {
        targets: [9],
        className: "text-center",
        render: function (data, type, row) {
          //cuando tienes 3 intentos que aparesca en verde un badge y s cuando 2 en amarillo cuando tiene 1 en naranja y cuando tiene 0 diga bloqueado
          data = -data + 3;
          if (data == 3) {
            return '<span class="badge badge-success">' + data + "</span>";
          } else if (data == 2) {
            return '<span class="badge badge-warning">' + data + "</span>";
          } else if (data == 1) {
            return '<span class="badge badge-danger">' + data + "</span>";
          } else {
            return '<span class="badge badge-danger">Bloqueado</span>';
          }
        },
      },
      {
        targets: [11, 8],
        orderable: false,
        className: "text-center",
        searchable: false,
      },
    ],
    responsive: "true",
    bProcessing: true,
    bDestroy: true,
    iDisplayLength: 10,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      confirmationDelete();
      loadUserReport();
      loadDataUpdate();
    },
  });
}
//funcion que se encarga de listar el select de tipo de rol
function loadSelect() {
  const url = base_url + "/Roles/getRolesSelect";
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
      let selectNew = document.getElementById("slctRole");
      let selectUpdate = document.getElementById("update_slctRole");
      option = `<option value="" selected disabled>Seleccione un elemento</option>`;
      data.forEach((item) => {
        option += `<option value="${item.idRole}">${item.r_name}</option>`;
      });
      selectNew.innerHTML = option;
      selectUpdate.innerHTML = option;
    })
    .catch((error) => {
      //carga de mensaje de error
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
        "Ocurrió un error inesperado"
      );
    });
}
//funcion que se encarga de guarda en la tabla
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
    const url = base_url + "/Users/setUser";
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
          loadUserReport();
          loadDataUpdate();
          elementLoader.classList.add("d-none");
        }, 500);
        return false;
      })
      .catch((error) => {
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrió un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
//Funcion que carga la imagen para previsualizar
function loadImagePreview() {
  const flPhotoNew = document.getElementById("flPhoto");
  const flPhotoUpdate = document.getElementById("update_flPhoto");
  const imgNewProfile = document.querySelector("#imgNewProfile");
  const imgUpdateProfile = document.querySelector("#imgUpdateProfile");
  flPhotoNew.addEventListener("change", () => {
    if (flPhoto.files[0]) {
      imgNewProfile.src = URL.createObjectURL(flPhoto.files[0]);
    }
  });
  flPhotoUpdate.addEventListener("change", () => {
    if (flPhotoUpdate.files[0]) {
      imgUpdateProfile.src = URL.createObjectURL(flPhotoUpdate.files[0]);
    }
  });
}
// Función que permite eliminar un registro
function confirmationDelete() {
  const arrBtnDeleteItem = document.querySelectorAll(".delete-item");
  arrBtnDeleteItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      const fullname = item.getAttribute("data-fullname");
      const id = item.getAttribute("data-id");
      const img = item.getAttribute("data-img");
      document.getElementById("txtDelete").innerHTML =
        "¿Está seguro de eliminar el usuario <strong>" +
        fullname +
        " </strong>?";
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-id", id);
      confirmDelete.setAttribute("data-fullname", fullname);
      confirmDelete.setAttribute("data-img", img);
      //abrimos el modal de confirmacion
      $("#confirmModalDelete").modal("show");
    });
  });
}
//funcion que se encarga de eliminar un registro
async function deleteData() {
  const confirmDelete = document.getElementById("confirmDelete");
  confirmDelete.addEventListener("click", (e) => {
    e.preventDefault();
    const id = confirmDelete.getAttribute("data-id");
    const fullname = confirmDelete.getAttribute("data-fullname");
    const token = confirmDelete.getAttribute("data-token");
    const img = confirmDelete.getAttribute("data-img");
    //creamos un objeto con los valores
    const arrValues = {
      id: id,
      fullname: fullname,
      token: token,
      img: img,
    };
    const header = { "Content-Type": "application/json" };
    const config = {
      method: "DELETE",
      headers: header,
      body: JSON.stringify(arrValues),
    };
    const url = base_url + "/Users/deleteUser";
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
          //ocultar el modal abierto
          $("#confirmModalDelete").modal("hide");
          //actualizar la tabla
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        ///recargar las funciones
        setTimeout(() => {
          confirmationDelete();
          loadUserReport();
          loadDataUpdate();
          elementLoader.classList.add("d-none");
        }, 500);
        return false;
      })
      .catch((error) => {
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrió un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
//Funcion que carga los datos en el reporte del modal del usuario
function loadUserReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      //Obtenemos los valores de los atributos del boton
      const id = item.getAttribute("data-id");
      const fullname = item.getAttribute("data-fullname");
      const email = item.getAttribute("data-email");
      const user = item.getAttribute("data-user");
      const password = item.getAttribute("data-password");
      const rol = item.getAttribute("data-rol");
      const profile = item.getAttribute("data-profile");
      const registrationDate = item.getAttribute("data-registrationDate");
      const status = item.getAttribute("data-status");
      const updateDate = item.getAttribute("data-updateDate");
      const dni = item.getAttribute("data-dni");
      const gender = item.getAttribute("data-gender");
      const attemp = item.getAttribute("data-attemp");
      const token = item.getAttribute("data-token");
      const url = item.getAttribute("data-url");
      const statuslink = item.getAttribute("data-status-link");
      const datafoldername = item.getAttribute("data-folder-name");
      const datastorageused = item.getAttribute("data-storage-used");
      const datastorageavailable = item.getAttribute("data-storage-available");
      //cargamos los elementos donde se cargaran los datos
      const reportFullName = document.getElementById("reportFullName");
      const reportPhotoProfile = document.getElementById("reportPhotoProfile");
      const reportDNI = document.getElementById("reportDNI");
      const reportGender = document.getElementById("reportGender");
      const reportUser = document.getElementById("reportUser");
      const reportEmail = document.getElementById("reportEmail");
      const reportRole = document.getElementById("reportRole");
      const reportRegistrationDate = document.getElementById(
        "reportRegistrationDate"
      );
      const reportUpdateDate = document.getElementById("reportUpdateDate");
      const reportPassword = document.getElementById("reportPassword");
      const reportAttemp = document.getElementById("reportAttemp");
      const reportToken = document.getElementById("reportToken");
      const reportUrl = document.getElementById("reportUrl");
      const reportStatusLink = document.getElementById("reportStatusLink");
      const reportStorageFolder = document.getElementById(
        "reportStorageFolder"
      );
      const reportStorageUsed = document.getElementById("reportStorageUsed");

      //asignamos los valores
      reportFullName.innerHTML = fullname;
      reportPhotoProfile.src = profile;
      reportDNI.innerHTML = dni;
      reportGender.innerHTML = gender;
      reportUser.innerHTML = user;
      reportPassword.innerHTML = password;
      reportEmail.innerHTML = email;
      reportRole.innerHTML = rol;
      reportRegistrationDate.innerHTML = registrationDate;
      reportUpdateDate.innerHTML = updateDate;
      reportAttemp.innerHTML = attemp;
      //validamos si el token esta vacio entonces ponemos un badge de color rojo no iniciado
      reportToken.innerHTML = token;
      //hacemos que esto se copie al portapapeles
      reportUrl.addEventListener("click", () => {
        navigator.clipboard.writeText(url);
      });
      reportUrl.innerHTML = url;
      if (token == "") {
        reportToken.innerHTML = `<span class="badge badge-danger">No iniciado</span>`;
        reportUrl.innerHTML = `<span class="badge badge-danger">No iniciado</span>`;
      }
      //atraves de un badge mostramos la ruta de la carpeta personal
      reportStorageFolder.innerHTML = `<span class="badge badge-info">./${datafoldername}</span>`;
      //mostramos la informacion del  (luego colocar en una funcion para hacer el calculo)
      reportStorageUsed.innerHTML = `<small class="text-muted">${datastorageused} GB de ${datastorageavailable} GB</small>
                                <div class="progress">
                                    <div class="progress-bar w-0" role="progressbar" aria-valuenow="${datastorageused}"
                                        aria-valuemin="0" aria-valuemax="${datastorageavailable}"></div>
                                </div>`;
      reportStatusLink.innerHTML = statuslink;
      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      //abrimos el modal
      $("#modalReport").modal("show");
    });
  });
}
//funcion que se encarga de mostrar el modal para actualizar los datos del usuario
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");
      //obtenemos los valores de los atributos del
      const id = item.getAttribute("data-id");
      const fullname = item.getAttribute("data-fullname");
      const dni = item.getAttribute("data-dni");
      const gender = item.getAttribute("data-gender");
      const email = item.getAttribute("data-email");
      const user = item.getAttribute("data-user");
      const password = item.getAttribute("data-password");
      const rol = item.getAttribute("data-rol");
      const rolId = item.getAttribute("data-rolid");
      const profile = item.getAttribute("data-profile");
      const dataprofileupdate = item.getAttribute("data-profile-update");
      const registrationDate = item.getAttribute("data-registrationDate");
      const status = item.getAttribute("data-status");
      const updateDate = item.getAttribute("data-updateDate");
      // Preparamos los elementos del formularios para actualizar
      const update_txtId = document.getElementById("update_txtId");
      const update_txtFullName = document.getElementById("update_txtFullName");
      const update_txtDNI = document.getElementById("update_txtDNI");
      document.getElementById(`update_${gender}`).checked = true;
      const update_txtUser = document.getElementById("update_txtUser");
      const update_txtEmail = document.getElementById("update_txtEmail");
      const update_slctRole = document.getElementById("update_slctRole");
      const imgUpdateProfile = document.getElementById("imgUpdateProfile");
      const update_txtPassword = document.getElementById("update_txtPassword");
      const update_txtFotoActual = document.getElementById(
        "update_txtFotoActual"
      );
      const update_slctStatus = document.getElementById("update_slctStatus");
      //asginamos los valores
      update_txtId.value = id;
      update_txtFullName.value = fullname;
      update_txtDNI.value = dni;
      update_txtUser.value = user;
      update_txtEmail.value = email;
      //validacion de usuarios root no permite el cambio de rol
      if (id == 1 && rolId == 1) {
        update_slctRole.disabled = true;
        update_slctRole.required = false;
      } else {
        update_slctRole.disabled = false;
        update_slctRole.value = rolId;
      }
      update_txtPassword.value = password;
      imgUpdateProfile.src = profile;
      update_txtFotoActual.value = dataprofileupdate;
      update_slctStatus.value = status;
      setTimeout(() => {
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
    const url = base_url + "/Users/updateUser";
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
          loadUserReport();
          loadDataUpdate();
          elementLoader.classList.add("d-none");
        }, 500);
        return false;
      })
      .catch((error) => {
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrió un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
//Funcion que consulta la API de reniec para obtener los datos de la persona
async function getPeopleReniec() {
  const btnSearchApi = document.getElementById("btnSearchApi");
  btnSearchApi.addEventListener("click", () => {
    const txtDNIRUC = document.getElementById("txtDNI").value;
    //validacion de campos vacios
    if (txtDNIRUC === "") {
      toastr["error"](
        "Para consultar los datos de la persona, necesita que el DNI este lleno",
        "Consulta no completada"
      );
      return false;
    }
    //consulta al servidor
    const url = base_url + "/Apireniecsunat/getpeople?data=" + txtDNIRUC;
    //mostramos el loader
    elementLoader.classList.remove("d-none");
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
        if (!data.status) {
          toastr[data.type](data.message, data.title);
          //Quitamos el loader
          elementLoader.classList.add("d-none");
          return false;
        }
        if (data.info.message) {
          toastr["error"](
            data.info.message,
            "Ourrio un error inesperado al consultar la API"
          );
          //Quitamos el loader
          elementLoader.classList.add("d-none");
          return false;
        }
        document.getElementById("txtFullName").value = data.info.nombreCompleto;
        //Quitamos el loader
        elementLoader.classList.add("d-none");
      })
      .catch((error) => {
        //Quitamos el loader
        elementLoader.classList.add("d-none");
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
//funcion que sincroniza campos
async function sicronizarCampos() {
  const rngStorage = document.getElementById("rngStorage");
  const numStorage = document.getElementById("numStorage");
  const lblStorage = document.getElementById("lblStorage");

  // Función para actualizar
  function updateStorage(value) {
    rngStorage.value = value;
    numStorage.value = value;
    lblStorage.textContent = value + " GB";
  }

  // Eventos
  rngStorage.addEventListener("input", () => updateStorage(rngStorage.value));
  numStorage.addEventListener("input", () => updateStorage(numStorage.value));

  // Valor inicial
  updateStorage(2);
}
