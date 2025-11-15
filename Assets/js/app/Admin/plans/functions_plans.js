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
    loadPlanReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

window.addEventListener("click", (e) => {
  loadPlanReport();
  confirmationDelete();
  loadDataUpdate();
});

/**
 * Funciรณn que se encarga de listar la tabla de planes
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Plans/getPlans",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "name" },
      { data: "description" },
      { data: "base_price_formatted" },
      { data: "billing_period_text" },
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
          columns: [1, 2, 3, 4, 5],
        },
      },
      {
        extend: "excelHtml5",
        text: "<i class='fa fa-file-excel-o'></i> Excel",
        title: "Reporte de planes en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4, 5],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de planes en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4, 5],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de planes en PDF",
        className: "btn btn-danger",
        orientation: "landscape",
        pageSize: "LEGAL",
        exportOptions: {
          columns: [1, 2, 3, 4, 5],
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
        targets: [3, 4, 5],
        className: "text-center",
      },
      {
        targets: [6],
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
      loadPlanReport();
      loadDataUpdate();
    },
  });
}

/**
 * Funciรณn que se encarga de guardar un nuevo registro
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
    const url = base_url + "/Plans/setPlan";
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
          loadPlanReport();
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
          "Ocurriรณ un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}

/**
 * Funciรณn que permite eliminar un registro
 */
function confirmationDelete() {
  const arrBtnDeleteItem = document.querySelectorAll(".delete-item");
  arrBtnDeleteItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      const name = item.getAttribute("data-name");
      const id = item.getAttribute("data-id");
      document.getElementById("txtDelete").innerHTML =
        "ยฟEstรก seguro de eliminar el plan <strong>" +
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
 * Funciรณn que se encarga de eliminar un registro
 */
async function deleteData() {
  const confirmDelete = document.getElementById("confirmDelete");
  confirmDelete.addEventListener("click", (e) => {
    e.preventDefault();
    const id = confirmDelete.getAttribute("data-id");
    const name = confirmDelete.getAttribute("data-name");
    const token = confirmDelete.getAttribute("data-token");
    const arrValues = {
      idPlan: id,
      name: name,
      token: token,
    };
    const header = { "Content-Type": "application/json" };
    const config = {
      method: "DELETE",
      headers: header,
      body: JSON.stringify(arrValues),
    };
    const url = base_url + "/Plans/deletePlan";
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
          loadPlanReport();
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
          "Ocurriรณ un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}

/**
 * Funciรณn que carga los datos en el reporte del modal del plan
 */
function loadPlanReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const name = item.getAttribute("data-name");
      const description = item.getAttribute("data-description") || "";
      const basePrice = item.getAttribute("data-base-price-formatted");
      const billingPeriod = item.getAttribute("data-billing-period-text");
      const status = item.getAttribute("data-status-text");
      const isActive = item.getAttribute("data-is-active");

      const reportName = document.getElementById("reportName");
      const reportNameDetail = document.getElementById("reportNameDetail");
      const reportDescription = document.getElementById("reportDescription");
      const reportBasePrice = document.getElementById("reportBasePrice");
      const reportBillingPeriod = document.getElementById("reportBillingPeriod");
      const reportStatus = document.getElementById("reportStatus");

      reportName.innerHTML = name;
      reportNameDetail.innerHTML = name;
      reportDescription.innerHTML = description || "-";
      reportBasePrice.innerHTML = "$ " + basePrice;
      reportBillingPeriod.innerHTML = billingPeriod;
      reportStatus.innerHTML =
        isActive == "1"
          ? '<span class="badge badge-success">Activo</span>'
          : '<span class="badge badge-danger">Inactivo</span>';

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalReport").modal("show");
    });
  });
}

/**
 * Funciรณn que se encarga de mostrar el modal para actualizar los datos del plan
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
      const basePrice = item.getAttribute("data-base-price");
      const billingPeriod = item.getAttribute("data-billing-period");
      const isActive = item.getAttribute("data-is-active");

      const update_idPlan = document.getElementById("update_idPlan");
      const update_txtName = document.getElementById("update_txtName");
      const update_txtDescription = document.getElementById(
        "update_txtDescription"
      );
      const update_txtBasePrice = document.getElementById("update_txtBasePrice");
      const update_slctBillingPeriod = document.getElementById(
        "update_slctBillingPeriod"
      );
      const update_slctIsActive = document.getElementById("update_slctIsActive");

      update_idPlan.value = id;
      update_txtName.value = name;
      update_txtDescription.value = description;
      update_txtBasePrice.value = basePrice;
      update_slctBillingPeriod.value = billingPeriod;
      update_slctIsActive.value = isActive;

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalUpdate").modal("show");
    });
  });
}

/**
 * Funciรณn que actualiza los datos del plan enviรกndolos al servidor
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
    const url = base_url + "/Plans/updatePlan";
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
          loadPlanReport();
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
          "Ocurriรณ un error inesperado"
        );
        elementLoader.classList.add("d-none");
      });
  });
}
