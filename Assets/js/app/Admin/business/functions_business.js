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
  loadSelects();
  loadTable();
  setTimeout(() => {
    saveData();
    confirmationDelete();
    deleteData();
    loadBusinessReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

window.addEventListener("click", (e) => {
  loadBusinessReport();
  confirmationDelete();
  loadDataUpdate();
});

/**
 * Función que carga los selects de tipo de negocio y usuarios de aplicación
 */
function loadSelects() {
  // Cargar tipos de negocio
  const urlBusinessTypes = base_url + "/Business/getBusinessTypesSelect";
  fetch(urlBusinessTypes)
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
      let selectNew = document.getElementById("slctTypeBusiness");
      let selectUpdate = document.getElementById("update_slctTypeBusiness");
      let option = `<option value="" selected disabled>Seleccione un elemento</option>`;
      data.forEach((item) => {
        option += `<option value="${item.idBusinessType}">${item.name}</option>`;
      });
      if (selectNew) selectNew.innerHTML = option;
      if (selectUpdate) selectUpdate.innerHTML = option;
    })
    .catch((error) => {
      toastr["error"](
        "Error al cargar tipos de negocio: " + error.message,
        "Ocurrió un error inesperado"
      );
    });

  // Cargar usuarios de aplicación
  const urlUserApps = base_url + "/Business/getUserAppsSelect";
  fetch(urlUserApps)
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
      let selectNew = document.getElementById("slctUserApp");
      let selectUpdate = document.getElementById("update_slctUserApp");
      let option = `<option value="" selected disabled>Seleccione un elemento</option>`;
      data.forEach((item) => {
        option += `<option value="${item.idUserApp}">${item.full_name} - ${item.user} (${item.email})</option>`;
      });
      if (selectNew) selectNew.innerHTML = option;
      if (selectUpdate) selectUpdate.innerHTML = option;
    })
    .catch((error) => {
      toastr["error"](
        "Error al cargar usuarios de aplicación: " + error.message,
        "Ocurrió un error inesperado"
      );
    });
}

/**
 * Función que se encarga de listar la tabla de negocios
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Business/getBusinesses",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "business_type_display" },
      { data: "name" },
      { data: "document_number" },
      { data: "email" },
      { data: "phone_full" },
      { data: "city_display" },
      { data: "user_app_display" },
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
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de negocios en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de negocios en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de negocios en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
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
      loadBusinessReport();
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
    const url = base_url + "/Business/setBusiness";
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
          loadSelects(); // Recargar selects por si se agregaron nuevos
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadBusinessReport();
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
      const name = item.getAttribute("data-name");
      const id = item.getAttribute("data-id");
      document.getElementById("txtDelete").innerHTML =
        "¿Está seguro de eliminar el negocio <strong>" +
        name +
        " </strong>?";
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-id", id);
      confirmDelete.setAttribute("data-name", name);
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
    const name = confirmDelete.getAttribute("data-name");
    const token = confirmDelete.getAttribute("data-token");
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
    const url = base_url + "/Business/deleteBusiness";
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
          loadBusinessReport();
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
 * Función que carga los datos en el reporte del modal del negocio
 */
function loadBusinessReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const typebusinessName = item.getAttribute("data-typebusiness-name");
      const name = item.getAttribute("data-name");
      const direction = item.getAttribute("data-direction") || "";
      const city = item.getAttribute("data-city") || "";
      const documentNumber = item.getAttribute("data-document-number");
      const phoneNumber = item.getAttribute("data-phone-number");
      const telephonePrefix = item.getAttribute("data-telephone-prefix");
      const country = item.getAttribute("data-country") || "";
      const email = item.getAttribute("data-email");
      const userappName = item.getAttribute("data-userapp-name");
      const status = item.getAttribute("data-status");
      const registrationDate = item.getAttribute("data-registration-date");
      const updateDate = item.getAttribute("data-update-date");

      const reportName = document.getElementById("reportName");
      const reportTypeBusiness = document.getElementById("reportTypeBusiness");
      const reportNameDetail = document.getElementById("reportNameDetail");
      const reportDocumentNumber = document.getElementById("reportDocumentNumber");
      const reportDirection = document.getElementById("reportDirection");
      const reportCity = document.getElementById("reportCity");
      const reportCountry = document.getElementById("reportCountry");
      const reportPhone = document.getElementById("reportPhone");
      const reportEmail = document.getElementById("reportEmail");
      const reportUserApp = document.getElementById("reportUserApp");
      const reportStatus = document.getElementById("reportStatus");
      const reportRegistrationDate = document.getElementById(
        "reportRegistrationDate"
      );
      const reportUpdateDate = document.getElementById("reportUpdateDate");

      reportName.innerHTML = name;
      reportTypeBusiness.innerHTML = typebusinessName || "-";
      reportNameDetail.innerHTML = name;
      reportDocumentNumber.innerHTML = documentNumber;
      reportDirection.innerHTML = direction || "-";
      reportCity.innerHTML = city || "-";
      reportCountry.innerHTML = country || "-";
      reportPhone.innerHTML = telephonePrefix + " " + phoneNumber;
      reportEmail.innerHTML = email;
      reportUserApp.innerHTML = userappName || "Sin usuario";
      reportStatus.innerHTML =
        status === "Activo"
          ? '<span class="badge badge-success">Activo</span>'
          : '<span class="badge badge-danger">Inactivo</span>';
      reportRegistrationDate.innerHTML = registrationDate;
      reportUpdateDate.innerHTML = updateDate;

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalReport").modal("show");
    });
  });
}

/**
 * Función que se encarga de mostrar el modal para actualizar los datos del negocio
 */
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const typebusinessId = item.getAttribute("data-typebusiness-id");
      const name = item.getAttribute("data-name");
      const direction = item.getAttribute("data-direction") || "";
      const city = item.getAttribute("data-city") || "";
      const documentNumber = item.getAttribute("data-document-number");
      const phoneNumber = item.getAttribute("data-phone-number");
      const country = item.getAttribute("data-country") || "";
      const telephonePrefix = item.getAttribute("data-telephone-prefix");
      const email = item.getAttribute("data-email");
      const status = item.getAttribute("data-status");
      const userappId = item.getAttribute("data-userapp-id");

      const update_txtId = document.getElementById("update_txtId");
      const update_slctTypeBusiness = document.getElementById("update_slctTypeBusiness");
      const update_txtName = document.getElementById("update_txtName");
      const update_txtDirection = document.getElementById("update_txtDirection");
      const update_txtCity = document.getElementById("update_txtCity");
      const update_txtCountry = document.getElementById("update_txtCountry");
      const update_txtDocumentNumber = document.getElementById("update_txtDocumentNumber");
      const update_txtTelephonePrefix = document.getElementById("update_txtTelephonePrefix");
      const update_txtPhoneNumber = document.getElementById("update_txtPhoneNumber");
      const update_txtEmail = document.getElementById("update_txtEmail");
      const update_slctStatus = document.getElementById("update_slctStatus");
      const update_slctUserApp = document.getElementById("update_slctUserApp");

      update_txtId.value = id;
      update_slctTypeBusiness.value = typebusinessId;
      update_txtName.value = name;
      update_txtDirection.value = direction;
      update_txtCity.value = city;
      update_txtCountry.value = country;
      update_txtDocumentNumber.value = documentNumber;
      update_txtTelephonePrefix.value = telephonePrefix;
      update_txtPhoneNumber.value = phoneNumber;
      update_txtEmail.value = email;
      update_slctStatus.value = status;
      update_slctUserApp.value = userappId;

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalUpdate").modal("show");
    });
  });
}

/**
 * Función que actualiza los datos del negocio enviándolos al servidor
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
    const url = base_url + "/Business/updateBusiness";
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
          loadSelects(); // Recargar selects por si se agregaron nuevos
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadBusinessReport();
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
