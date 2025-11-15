let table; // Instancia de DataTables

// Configuración de Toastr
toastr.options = {
  closeButton: true,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "5000",
  progressBar: true,
};

/**
 * Carga la lista de planes en los select
 */
function loadPlans() {
  // Cargar planes para el formulario de registro
  fetch(`${base_url}/Discounts/getPlans`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((response) => {
      const selectSave = document.querySelector("#slctPlanId");
      const selectUpdate = document.querySelector("#update_slctPlanId");

      if (selectSave) {
        selectSave.innerHTML = '<option value="">Todos los planes</option>';

        if (response.status && response.data) {
          response.data.forEach((plan) => {
            const option = document.createElement("option");
            option.value = plan.idPlan;
            option.textContent = plan.name;
            selectSave.appendChild(option);
          });
        }
      }

      if (selectUpdate) {
        selectUpdate.innerHTML = '<option value="">Todos los planes</option>';

        if (response.status && response.data) {
          response.data.forEach((plan) => {
            const option = document.createElement("option");
            option.value = plan.idPlan;
            option.textContent = plan.name;
            selectUpdate.appendChild(option);
          });
        }
      }
    })
    .catch((error) => {
      console.error("Error al cargar planes:", error);
    });
}

/**
 * Carga la tabla de descuentos con DataTables
 */
function loadTable() {
  table = $("#table").DataTable({
    aProcessing: true,
    aServerSide: true,
    ajax: {
      url: base_url + "/Discounts/getDiscounts",
      dataSrc: "",
    },
    columns: [
      { data: "0" },
      { data: "1" },
      { data: "2" },
      { data: "3" },
      { data: "4" },
      { data: "5" },
      { data: "6" },
      { data: "7" },
      { data: "8" },
    ],
    dom: "Bfrtip",
    buttons: [
      {
        extend: "copy",
        text: '<i class="fa fa-copy"></i> Copiar',
        titleAttr: "Copiar",
        className: "btn btn-secondary btn-sm",
      },
      {
        extend: "excelHtml5",
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        titleAttr: "Exportar a Excel",
        className: "btn btn-success btn-sm",
        title: "Descuentos",
      },
      {
        extend: "csvHtml5",
        text: '<i class="fa fa-file-text-o"></i> CSV',
        titleAttr: "Exportar a CSV",
        className: "btn btn-primary btn-sm",
        title: "Descuentos",
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fa fa-file-pdf-o"></i> PDF',
        titleAttr: "Exportar a PDF",
        className: "btn btn-danger btn-sm",
        title: "Descuentos",
        orientation: "landscape",
        pageSize: "LEGAL",
      },
    ],
    columnDefs: [
      {
        targets: 0,
        className: "text-center",
      },
      {
        targets: [1, 2, 3, 4, 5, 6],
        className: "text-left",
      },
      {
        targets: 7,
        className: "text-center",
        render: function (data, type, row) {
          if (data.includes("Activo")) {
            return '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
          } else if (data.includes("Inactivo")) {
            return '<span class="badge badge-danger"><i class="fa fa-times"></i> Inactivo</span>';
          }
          return data;
        },
      },
      {
        targets: 8,
        className: "text-center",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return data;
        },
      },
    ],
    language: {
      url: base_url + "/Assets/js/libraries/Admin/Spanish-datatables.json",
    },
    fnDrawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-sm");
    },
  });
}

/**
 * Configura el envío del formulario de registro
 */
function saveData() {
  const formSave = document.querySelector("#formSave");

  if (formSave) {
    formSave.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(formSave);

      fetch(`${base_url}/Discounts/setDiscount`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((response) => {
          if (response.status) {
            formSave.reset();
            $("#modalSave").modal("hide");
            table.ajax.reload();

            toastr[response.type](response.message, response.title);
          } else {
            toastr[response.type](response.message, response.title);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          toastr.error("Error en la solicitud", "Error");
        });
    });
  }
}

/**
 * Configura el modal de confirmación de eliminación
 */
function confirmationDelete() {
  const buttonsDelete = document.querySelectorAll(".delete-item");

  buttonsDelete.forEach((button) => {
    button.addEventListener("click", function () {
      const code = this.getAttribute("data-code");
      document.querySelector(
        "#txtDelete"
      ).innerHTML = `¿Está seguro de eliminar el descuento con código <strong>${code}</strong>?`;
      document
        .querySelector("#confirmDelete")
        .setAttribute("data-id", this.getAttribute("data-id"));
    });
  });
}

/**
 * Ejecuta la eliminación del descuento
 */
function deleteData() {
  const confirmDelete = document.querySelector("#confirmDelete");

  if (confirmDelete) {
    confirmDelete.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const token = this.getAttribute("data-token");

      const data = {
        id: id,
        code: document
          .querySelector("#txtDelete")
          .textContent.match(/código <strong>(.*?)<\/strong>/)[1],
        token: token,
      };

      const elementLoader = document.querySelector(".elementLoader");
      elementLoader.classList.remove("d-none");

      fetch(`${base_url}/Discounts/deleteDiscount`, {
        method: "DELETE",
        body: JSON.stringify(data),
        headers: {
          "Content-Type": "application/json",
        },
      })
        .then((response) => response.json())
        .then((response) => {
          if (response.status) {
            $("#confirmModalDelete").modal("hide");
            table.ajax.reload();
            toastr[response.type](response.message, response.title);
          } else {
            toastr[response.type](response.message, response.title);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          toastr.error("Error en la solicitud", "Error");
        })
        .finally(() => {
          elementLoader.classList.add("d-none");
        });
    });
  }
}

/**
 * Carga los datos en el modal de reporte
 */
function loadDiscountReport() {
  const buttonsReport = document.querySelectorAll(".report-item");

  buttonsReport.forEach((button) => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const code = this.getAttribute("data-code");
      const type = this.getAttribute("data-type");
      const value = this.getAttribute("data-value");
      const startDate = this.getAttribute("data-start-date");
      const endDate = this.getAttribute("data-end-date");
      const appliesToPlanId = this.getAttribute("data-applies-to-plan-id");
      const planName = this.getAttribute("data-plan-name");
      const maxUses = this.getAttribute("data-max-uses");
      const isRecurring = this.getAttribute("data-is-recurring");
      const status = this.getAttribute("data-status");

      // Formatear tipo de descuento
      const typeText = type === "percentage" ? "Porcentaje" : "Monto Fijo";

      // Formatear es recurrente
      const isRecurringText = isRecurring == "1" ? "Sí" : "No";

      // Formatear fecha
      const startDateFormatted = new Date(startDate).toLocaleString("es-ES");
      const endDateFormatted = new Date(endDate).toLocaleString("es-ES");

      const statusBadge =
        status === "Activo"
          ? '<span class="badge badge-success">Activo</span>'
          : '<span class="badge badge-danger">Inactivo</span>';

      // Formatear max uses
      const maxUsesText = maxUses ? maxUses : "Ilimitado";

      document.querySelector("#reportCode").textContent = code;
      document.querySelector("#reportCodeDetail").textContent = code;
      document.querySelector("#reportType").textContent = typeText;
      document.querySelector("#reportValue").textContent = value;
      document.querySelector("#reportPlanName").textContent = planName;
      document.querySelector("#reportStartDate").textContent =
        startDateFormatted;
      document.querySelector("#reportEndDate").textContent = endDateFormatted;
      document.querySelector("#reportMaxUses").textContent = maxUsesText;
      document.querySelector("#reportIsRecurring").innerHTML = isRecurringText;
      document.querySelector("#reportStatus").innerHTML = statusBadge;

      // Fecha de registro y actualización (para este ejemplo, se usa la fecha de inicio/finalización)
      document.querySelector("#reportRegistrationDate").textContent =
        startDateFormatted;
      document.querySelector("#reportUpdateDate").textContent =
        endDateFormatted;

      const elementLoader = document.querySelector(".elementLoader");
      elementLoader.classList.remove("d-none");

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
    });
  });
}

/**
 * Carga los datos en el formulario de actualización
 */
function loadDataUpdate() {
  const buttonsUpdate = document.querySelectorAll(".update-item");

  buttonsUpdate.forEach((button) => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const code = this.getAttribute("data-code");
      const type = this.getAttribute("data-type");
      const value = this.getAttribute("data-value");
      const startDate = this.getAttribute("data-start-date");
      const endDate = this.getAttribute("data-end-date");
      const appliesToPlanId = this.getAttribute("data-applies-to-plan-id");
      const planName = this.getAttribute("data-plan-name");
      const maxUses = this.getAttribute("data-max-uses");
      const isRecurring = this.getAttribute("data-is-recurring");
      const status = this.getAttribute("data-status");

      // Convertir fechas al formato esperado por datetime-local (YYYY-MM-DDTHH:mm)
      const startDateFormatted = startDate
        ? new Date(startDate).toISOString().slice(0, 16)
        : "";
      const endDateFormatted = endDate
        ? new Date(endDate).toISOString().slice(0, 16)
        : "";

      document.querySelector("#update_idDiscount").value = id;
      document.querySelector("#update_txtCode").value = code;
      document.querySelector("#update_slctType").value = type;
      document.querySelector("#update_txtValue").value = value;
      document.querySelector("#update_slctPlanId").value = appliesToPlanId
        ? appliesToPlanId
        : "";
      document.querySelector("#update_txtStartDate").value = startDateFormatted;
      document.querySelector("#update_txtEndDate").value = endDateFormatted;
      document.querySelector("#update_txtMaxUses").value = maxUses
        ? maxUses
        : "";
      document.querySelector("#update_chkIsRecurring").checked =
        isRecurring == "1";
      document.querySelector("#update_slctStatus").value = status;

      const elementLoader = document.querySelector(".elementLoader");
      elementLoader.classList.remove("d-none");

      setTimeout(() => {
        elementLoader.classList.add("d-none");
      }, 500);
    });
  });
}

/**
 * Configura el envío del formulario de actualización
 */
function updateData() {
  const formUpdate = document.querySelector("#formUpdate");
  const elementLoader = document.querySelector(".elementLoader");

  if (formUpdate) {
    formUpdate.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(formUpdate);
      elementLoader.classList.remove("d-none");

      fetch(`${base_url}/Discounts/setDiscount`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((response) => {
          if (response.status) {
            formUpdate.reset();
            $("#modalUpdate").modal("hide");
            table.ajax.reload();

            toastr[response.type](response.message, response.title);
          } else {
            toastr[response.type](response.message, response.title);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          toastr.error("Error en la solicitud", "Error");
        })
        .finally(() => {
          elementLoader.classList.add("d-none");
        });
    });
  }
}

/**
 * Eventos principales
 */
document.addEventListener("DOMContentLoaded", function (e) {
  e.preventDefault();
  loadPlans(); // Cargar planes primero
  loadTable();
  setTimeout(() => {
    saveData();
    confirmationDelete();
    deleteData();
    loadDiscountReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});

/**
 * Eventos dinámicos para elementos que se cargan después
 */
document.addEventListener("click", function (e) {
  loadDiscountReport();
  confirmationDelete();
  loadDataUpdate();
});
