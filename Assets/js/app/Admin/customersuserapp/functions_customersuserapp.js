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
    loadPeopleReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

window.addEventListener("click", (e) => {
  loadPeopleReport();
  confirmationDelete();
  loadDataUpdate();
});

/**
 * Función que se encarga de listar la tabla de personas
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Customersuserapp/getPeople",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "names" },
      { data: "lastname" },
      { data: "email" },
      { data: "date_of_birth_formatted" },
      { data: "country" },
      { data: "phone_full" },
      { data: "user_app" },
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
          columns: [1, 2, 3, 4, 5, 6, 7],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de clientes en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de clientes en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de clientes en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7],
        },
      },
    ],
    columnDefs: [
      {
        targets: [0],
        className: "text-center",
      },
      {
        targets: [1, 2, 3, 4, 5, 6, 7],
        className: "text-left",
      },
      {
        targets: [7],
        render: function (data, type, row) {
          if (data === "Sin usuario" || !data) {
            return '<span class="badge badge-secondary">Sin usuario</span>';
          }
          return data;
        },
      },
      {
        targets: [9],
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
      url: base_url + "/Assets/js/libraries/Admin/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      confirmationDelete();
      loadPeopleReport();
      loadDataUpdate();
    },
  });
}

/**
 * Función que se encarga de guardar un nuevo registro
 */
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
    const url = base_url + "/Customersuserapp/setPeople";
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
          formSave.reset();
          $("#modalSave").modal("hide");
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadPeopleReport();
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

/**
 * Función que permite eliminar un registro
 */
function confirmationDelete() {
  const arrBtnDeleteItem = document.querySelectorAll(".delete-item");
  arrBtnDeleteItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      const fullname = item.getAttribute("data-fullname");
      const id = item.getAttribute("data-id");
      const userAppId = item.getAttribute("data-user-app-id") || "";
      document.getElementById("txtDelete").innerHTML =
        "¿Está seguro de eliminar el cliente <strong>" +
        fullname +
        " </strong>?";
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-id", id);
      confirmDelete.setAttribute("data-fullname", fullname);
      confirmDelete.setAttribute("data-user-app-id", userAppId);
      $("#confirmModalDelete").modal("show");
    });
  });
}

/**
 * Función que se encarga de eliminar un registro
 */
async function deleteData() {
  const confirmDelete = document.getElementById("confirmDelete");
  confirmDelete.addEventListener("click", (e) => {
    e.preventDefault();
    const id = confirmDelete.getAttribute("data-id");
    const fullname = confirmDelete.getAttribute("data-fullname");
    const token = confirmDelete.getAttribute("data-token");
    const userAppId = confirmDelete.getAttribute("data-user-app-id") || "";
    const arrValues = {
      id: id,
      fullname: fullname,
      token: token,
      user_app_id: userAppId,
    };
    const header = { "Content-Type": "application/json" };
    const config = {
      method: "DELETE",
      headers: header,
      body: JSON.stringify(arrValues),
    };
    const url = base_url + "/Customersuserapp/deletePeople";
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
          $("#confirmModalDelete").modal("hide");
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadPeopleReport();
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

/**
 * Función que carga los datos en el reporte del modal de la persona
 */
function loadPeopleReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const names = item.getAttribute("data-names");
      const lastname = item.getAttribute("data-lastname");
      const email = item.getAttribute("data-email");
      const dateOfBirth = item.getAttribute("data-date-of-birth");
      const country = item.getAttribute("data-country");
      const telephonePrefix = item.getAttribute("data-telephone-prefix");
      const phoneNumber = item.getAttribute("data-phone-number");
      const status = item.getAttribute("data-status");
      const registrationDate = item.getAttribute("data-registration-date");
      const updateDate = item.getAttribute("data-update-date");
      const user = item.getAttribute("data-user");
      const userPassword = item.getAttribute("data-user-password");
      const userStatus = item.getAttribute("data-user-status");
      const hasUser = item.getAttribute("data-has-user");

      const reportFullName = document.getElementById("reportFullName");
      const reportNames = document.getElementById("reportNames");
      const reportLastname = document.getElementById("reportLastname");
      const reportEmail = document.getElementById("reportEmail");
      const reportDateOfBirth = document.getElementById("reportDateOfBirth");
      const reportCountry = document.getElementById("reportCountry");
      const reportPhone = document.getElementById("reportPhone");
      const reportStatus = document.getElementById("reportStatus");
      const reportRegistrationDate = document.getElementById(
        "reportRegistrationDate"
      );
      const reportUpdateDate = document.getElementById("reportUpdateDate");
      const reportUser = document.getElementById("reportUser");
      const reportPassword = document.getElementById("reportPassword");
      const reportUserStatus = document.getElementById("reportUserStatus");

      reportFullName.innerHTML = names + " " + lastname;
      reportNames.innerHTML = names;
      reportLastname.innerHTML = lastname;
      reportEmail.innerHTML = email;
      reportDateOfBirth.innerHTML = dateOfBirth;
      reportCountry.innerHTML = country;
      reportPhone.innerHTML = telephonePrefix + " " + phoneNumber;
      reportStatus.innerHTML =
        status === "Activo"
          ? '<span class="badge badge-success">Activo</span>'
          : '<span class="badge badge-danger">Inactivo</span>';
      reportRegistrationDate.innerHTML = registrationDate;
      reportUpdateDate.innerHTML = updateDate;

      // Datos del usuario de la app
      if (hasUser === "1" && user && user !== "Sin usuario") {
        reportUser.innerHTML = user;
        reportPassword.innerHTML = userPassword || "-";
        reportUserStatus.innerHTML =
          userStatus === "Activo"
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>';
      } else {
        reportUser.innerHTML =
          '<span class="badge badge-secondary">Sin usuario</span>';
        reportPassword.innerHTML = "-";
        reportUserStatus.innerHTML = "-";
      }

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalReport").modal("show");
    });
  });
}

/**
 * Función que se encarga de mostrar el modal para actualizar los datos de la persona
 */
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const names = item.getAttribute("data-names");
      const lastname = item.getAttribute("data-lastname");
      const email = item.getAttribute("data-email");
      const dateOfBirth = item.getAttribute("data-date-of-birth");
      const country = item.getAttribute("data-country");
      const telephonePrefix = item.getAttribute("data-telephone-prefix");
      const phoneNumber = item.getAttribute("data-phone-number");
      const status = item.getAttribute("data-status");
      const userAppId = item.getAttribute("data-user-app-id") || "";
      const user = item.getAttribute("data-user") || "";
      const userPassword = item.getAttribute("data-user-password") || "";
      const userStatus = item.getAttribute("data-user-status") || "Activo";
      const hasUser = item.getAttribute("data-has-user") || "0";

      const update_txtId = document.getElementById("update_txtId");
      const update_txtNames = document.getElementById("update_txtNames");
      const update_txtLastname = document.getElementById("update_txtLastname");
      const update_txtEmail = document.getElementById("update_txtEmail");
      const update_txtDateOfBirth = document.getElementById(
        "update_txtDateOfBirth"
      );
      const update_txtCountry = document.getElementById("update_txtCountry");
      const update_txtTelephonePrefix = document.getElementById(
        "update_txtTelephonePrefix"
      );
      const update_txtPhoneNumber = document.getElementById(
        "update_txtPhoneNumber"
      );
      const update_slctStatus = document.getElementById("update_slctStatus");
      const update_txtUserAppId = document.getElementById("update_txtUserAppId");
      const update_txtUser = document.getElementById("update_txtUser");
      const update_txtPassword = document.getElementById("update_txtPassword");
      const update_slctUserStatus = document.getElementById(
        "update_slctUserStatus"
      );

      update_txtId.value = id;
      update_txtNames.value = names;
      update_txtLastname.value = lastname;
      update_txtEmail.value = email;
      update_txtDateOfBirth.value = dateOfBirth;
      update_txtCountry.value = country;
      update_txtTelephonePrefix.value = telephonePrefix;
      update_txtPhoneNumber.value = phoneNumber;
      update_slctStatus.value = status;

      // Datos del usuario de la app
      update_txtUserAppId.value = userAppId;
      if (hasUser === "1" && user && user !== "Sin usuario") {
        update_txtUser.value = user;
        update_txtPassword.value = userPassword;
        update_slctUserStatus.value = userStatus;
      } else {
        update_txtUser.value = "";
        update_txtPassword.value = "";
        update_slctUserStatus.value = "Activo";
      }

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalUpdate").modal("show");
    });
  });
}

/**
 * Función que actualiza los datos de la persona enviándolos al servidor
 */
async function updateData() {
  const formUpdate = document.getElementById("formUpdate");
  formUpdate.addEventListener("submit", (e) => {
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
    const url = base_url + "/Customersuserapp/updatePeople";
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
          formUpdate.reset();
          $("#modalUpdate").modal("hide");
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadPeopleReport();
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
