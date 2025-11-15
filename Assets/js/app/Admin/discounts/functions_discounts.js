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
  setupTypeChangeHandlers();
  setTimeout(() => {
    saveData();
    confirmationDelete();
    deleteData();
    loadDiscountReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

window.addEventListener("click", (e) => {
  loadDiscountReport();
  confirmationDelete();
  loadDataUpdate();
});

/**
 * Función que carga los selects de planes
 */
function loadSelects() {
  // Cargar planes activos
  const urlPlans = base_url + "/Discounts/getPlansSelect";
  fetch(urlPlans)
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
      let selectNew = document.getElementById("slctAppliesToPlanId");
      let selectUpdate = document.getElementById("update_slctAppliesToPlanId");
      let option = `<option value="" selected>Todos los planes</option>`;
      data.forEach((item) => {
        option += `<option value="${item.idPlan}">${item.name}</option>`;
      });
      if (selectNew) selectNew.innerHTML = option;
      if (selectUpdate) selectUpdate.innerHTML = option;
    })
    .catch((error) => {
      toastr["error"](
        "Error al cargar planes: " + error.message,
        "Ocurrió un error inesperado"
      );
    });
}

/**
 * Función que configura los manejadores de cambio de tipo de descuento
 */
function setupTypeChangeHandlers() {
  // Para el formulario de registro
  const slctType = document.getElementById("slctType");
  const valuePrefix = document.getElementById("valuePrefix");
  const valueHelp = document.getElementById("valueHelp");
  const txtValue = document.getElementById("txtValue");

  if (slctType && valuePrefix && valueHelp && txtValue) {
    slctType.addEventListener("change", function () {
      if (this.value === "percentage") {
        valuePrefix.querySelector("span").textContent = "%";
        valueHelp.textContent = "Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.";
        txtValue.setAttribute("max", "100");
      } else if (this.value === "fixed") {
        valuePrefix.querySelector("span").textContent = "$";
        valueHelp.textContent = "Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.";
        txtValue.removeAttribute("max");
      }
    });
  }

  // Para el formulario de actualización
  const updateSlctType = document.getElementById("update_slctType");
  const updateValuePrefix = document.getElementById("updateValuePrefix");
  const updateValueHelp = document.getElementById("updateValueHelp");
  const updateTxtValue = document.getElementById("update_txtValue");

  if (updateSlctType && updateValuePrefix && updateValueHelp && updateTxtValue) {
    updateSlctType.addEventListener("change", function () {
      if (this.value === "percentage") {
        updateValuePrefix.querySelector("span").textContent = "%";
        updateValueHelp.textContent = "Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.";
        updateTxtValue.setAttribute("max", "100");
      } else if (this.value === "fixed") {
        updateValuePrefix.querySelector("span").textContent = "$";
        updateValueHelp.textContent = "Para porcentaje: 0-100. Para monto fijo: mayor o igual a 0.";
        updateTxtValue.removeAttribute("max");
      }
    });
  }
}

/**
 * Función que se encarga de listar la tabla de descuentos
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: "" + base_url + "/Discounts/getDiscounts",
      dataSrc: "",
    },
    columns: [
      { data: "cont" },
      { data: "code" },
      { data: "type_text" },
      { data: "value_formatted" },
      { data: "start_date_formatted" },
      { data: "end_date_formatted" },
      { data: "plan_name_display" },
      { data: "max_uses_display" },
      { data: "is_recurring_text" },
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
        title: "Reporte de descuentos en Excel",
        className: "btn btn-success",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      {
        extend: "csvHtml5",
        text: "<i class='fa fa-file-text'></i> CSV",
        title: "Reporte de descuentos en CSV",
        className: "btn btn-info",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='fa fa-file-pdf-o'></i> PDF",
        title: "Reporte de descuentos en PDF",
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
        targets: [1, 2, 3, 6],
        className: "text-left",
      },
      {
        targets: [4, 5, 7, 8],
        className: "text-center",
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
      loadDiscountReport();
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
    const url = base_url + "/Discounts/setDiscount";
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
          // Resetear el prefijo del valor
          const valuePrefix = document.getElementById("valuePrefix");
          if (valuePrefix) {
            valuePrefix.querySelector("span").textContent = "%";
          }
          table.ajax.reload(null, false);
        }
        toastr[data.type](data.message, data.title);
        setTimeout(() => {
          confirmationDelete();
          loadDiscountReport();
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
      const code = item.getAttribute("data-code");
      const id = item.getAttribute("data-id");
      document.getElementById("txtDelete").innerHTML =
        "¿Está seguro de eliminar el descuento <strong>" +
        code +
        " </strong>?";
      const confirmDelete = document.getElementById("confirmDelete");
      confirmDelete.setAttribute("data-id", id);
      confirmDelete.setAttribute("data-code", code);
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
    const code = confirmDelete.getAttribute("data-code");
    const token = confirmDelete.getAttribute("data-token");
    const arrValues = {
      idDiscount: id,
      code: code,
      token: token,
    };
    const header = { "Content-Type": "application/json" };
    const config = {
      method: "DELETE",
      headers: header,
      body: JSON.stringify(arrValues),
    };
    const url = base_url + "/Discounts/deleteDiscount";
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
          loadDiscountReport();
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
 * Función que carga los datos en el reporte del modal del descuento
 */
function loadDiscountReport() {
  const btnReportItem = document.querySelectorAll(".report-item");
  btnReportItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const code = item.getAttribute("data-code");
      const type = item.getAttribute("data-type-text");
      const value = item.getAttribute("data-value-formatted");
      const startDate = item.getAttribute("data-start-date-formatted");
      const endDate = item.getAttribute("data-end-date-formatted");
      const planName = item.getAttribute("data-plan-name");
      const maxUses = item.getAttribute("data-max-uses-display");
      const isRecurring = item.getAttribute("data-is-recurring-text");

      const reportCode = document.getElementById("reportCode");
      const reportCodeDetail = document.getElementById("reportCodeDetail");
      const reportType = document.getElementById("reportType");
      const reportValue = document.getElementById("reportValue");
      const reportStartDate = document.getElementById("reportStartDate");
      const reportEndDate = document.getElementById("reportEndDate");
      const reportPlanName = document.getElementById("reportPlanName");
      const reportMaxUses = document.getElementById("reportMaxUses");
      const reportIsRecurring = document.getElementById("reportIsRecurring");

      reportCode.innerHTML = code;
      reportCodeDetail.innerHTML = code;
      reportType.innerHTML = type;
      reportValue.innerHTML = value;
      reportStartDate.innerHTML = startDate || "-";
      reportEndDate.innerHTML = endDate || "-";
      reportPlanName.innerHTML = planName || "Todos los planes";
      reportMaxUses.innerHTML = maxUses || "Ilimitado";
      reportIsRecurring.innerHTML = isRecurring;

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalReport").modal("show");
    });
  });
}

/**
 * Función que se encarga de mostrar el modal para actualizar los datos del descuento
 */
function loadDataUpdate() {
  const btnUpdateItem = document.querySelectorAll(".update-item");
  btnUpdateItem.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      elementLoader.classList.remove("d-none");

      const id = item.getAttribute("data-id");
      const code = item.getAttribute("data-code");
      const type = item.getAttribute("data-type");
      const value = item.getAttribute("data-value");
      const startDate = item.getAttribute("data-start-date");
      const endDate = item.getAttribute("data-end-date");
      const appliesToPlanId = item.getAttribute("data-applies-to-plan-id");
      const maxUses = item.getAttribute("data-max-uses");
      const isRecurring = item.getAttribute("data-is-recurring");

      // Convertir fechas al formato datetime-local (YYYY-MM-DDTHH:mm)
      let startDateLocal = "";
      if (startDate) {
        const startDateObj = new Date(startDate);
        startDateLocal =
          startDateObj.getFullYear() +
          "-" +
          String(startDateObj.getMonth() + 1).padStart(2, "0") +
          "-" +
          String(startDateObj.getDate()).padStart(2, "0") +
          "T" +
          String(startDateObj.getHours()).padStart(2, "0") +
          ":" +
          String(startDateObj.getMinutes()).padStart(2, "0");
      }

      let endDateLocal = "";
      if (endDate) {
        const endDateObj = new Date(endDate);
        endDateLocal =
          endDateObj.getFullYear() +
          "-" +
          String(endDateObj.getMonth() + 1).padStart(2, "0") +
          "-" +
          String(endDateObj.getDate()).padStart(2, "0") +
          "T" +
          String(endDateObj.getHours()).padStart(2, "0") +
          ":" +
          String(endDateObj.getMinutes()).padStart(2, "0");
      }

      const update_idDiscount = document.getElementById("update_idDiscount");
      const update_txtCode = document.getElementById("update_txtCode");
      const update_slctType = document.getElementById("update_slctType");
      const update_txtValue = document.getElementById("update_txtValue");
      const update_txtStartDate = document.getElementById("update_txtStartDate");
      const update_txtEndDate = document.getElementById("update_txtEndDate");
      const update_slctAppliesToPlanId = document.getElementById(
        "update_slctAppliesToPlanId"
      );
      const update_txtMaxUses = document.getElementById("update_txtMaxUses");
      const update_slctIsRecurring = document.getElementById(
        "update_slctIsRecurring"
      );

      update_idDiscount.value = id;
      update_txtCode.value = code;
      update_slctType.value = type;
      update_txtValue.value = value;
      update_txtStartDate.value = startDateLocal;
      update_txtEndDate.value = endDateLocal;
      update_slctAppliesToPlanId.value = appliesToPlanId || "";
      update_txtMaxUses.value = maxUses || "";
      update_slctIsRecurring.value = isRecurring;

      // Actualizar el prefijo del valor según el tipo
      const updateValuePrefix = document.getElementById("updateValuePrefix");
      if (updateValuePrefix) {
        if (type === "percentage") {
          updateValuePrefix.querySelector("span").textContent = "%";
          update_txtValue.setAttribute("max", "100");
        } else {
          updateValuePrefix.querySelector("span").textContent = "$";
          update_txtValue.removeAttribute("max");
        }
      }

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
      $("#modalUpdate").modal("show");
    });
  });
}

/**
 * Función que actualiza los datos del descuento enviándolos al servidor
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
    const url = base_url + "/Discounts/updateDiscount";
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
          loadDiscountReport();
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
