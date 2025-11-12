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
    loadBusinessTypeReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

window.addEventListener("click", (e) => {
  loadBusinessTypeReport();
  confirmationDelete();
  loadDataUpdate();
});

/**
 * Función que se encarga de listar la tabla de tipos de negocio
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/BusinessType/getBusinessTypes",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "name" },
      { data: "description_display" },
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
          columns: [1, 2, 3],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de tipos de negocio en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de tipos de negocio en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de tipos de negocio en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3],
        },
      },
    ],
    columnDefs: [
      {
        targets: [0],
        className: "text-center",
      },
      {
        targets: [1, 2],
        className: "text-left",
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
    bDestroy: true,
    iDisplayLength: 10,
    order: [[0, "asc"]],
    language: {
      url: base_url + "/Assets/js/libraries/Admin/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
      confirmationDelete();
      loadBusinessTypeReport();
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
    const url = base_url + "/BusinessType/setBusinessType";
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
          loadBusinessTypeReport();
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
        "¿Está seguro de eliminar el tipo de negocio <strong>" +
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
    const url = base_url + "/BusinessType/deleteBusinessType";
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
          loadBusinessTypeReport();
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
 * Función que carga los datos en el reporte del modal del tipo de negocio
 */
function loadBusinessTypeReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description") || "";
      const status = item.getAttribute("data-status");
      const registrationDate = item.getAttribute("data-registration-date");
      const updateDate = item.getAttribute("data-update-date");

      const reportName = document.getElementById("reportName");
      const reportNameDetail = document.getElementById("reportNameDetail");
      const reportDescription = document.getElementById("reportDescription");
      const reportStatus = document.getElementById("reportStatus");
      const reportRegistrationDate = document.getElementById(
        "reportRegistrationDate"
      );
      const reportUpdateDate = document.getElementById("reportUpdateDate");

      reportName.innerHTML = name;
      reportNameDetail.innerHTML = name;
      reportDescription.innerHTML = description || "-";
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
 * Función que se encarga de mostrar el modal para actualizar los datos del tipo de negocio
 */
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description") || "";
      const status = item.getAttribute("data-status");

      const update_txtId = document.getElementById("update_txtId");
      const update_txtName = document.getElementById("update_txtName");
      const update_txtDescription = document.getElementById(
        "update_txtDescription"
      );
      const update_slctStatus = document.getElementById("update_slctStatus");

      update_txtId.value = id;
      update_txtName.value = name;
      update_txtDescription.value = description;
      update_slctStatus.value = status;

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalUpdate").modal("show");
    });
  });
}

/**
 * Función que actualiza los datos del tipo de negocio enviándolos al servidor
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
    const url = base_url + "/BusinessType/updateBusinessType";
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
          loadBusinessTypeReport();
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
