"use strict";

import { ApiBoxmanagement } from "./functions_boxmanagement_api.js";

export class Boxmanagement {
  #api;
  #table;

  constructor() {
    this.#api = new ApiBoxmanagement(base_url);
    this.#init();
  }

  #init = () => {
    this.#table = this.#initTable();

    $("#btnOpenBoxModal").on("click", () => {
      $("#formBox")[0].reset();
      $("#modalBoxLabel").text("Registrar caja");
      $("#modalBox").modal("show");
    });

    this.#register();
    this.#update();
    this.#actions();
  };

  #initTable = () => {
    return $("#table").DataTable({
      processing: true,
      ajax: {
        url: `${base_url}/pos/Boxmanagement/getBoxes`,
        dataSrc: function (json) {
          if (json.url) {
            setTimeout(() => {
              window.location.href = json.url;
            }, 1000);
          }
          // Importante: serverSide espera que los datos vengan en json.data
          return json;
        },
      },
      columns: [
        { data: "cont", className: "text-center" },
        {
          data: "actions",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        { data: "name", className: "text-center" },
        { data: "description", className: "text-center" },
        { data: "registrationDate", className: "text-center" },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary my-2",
          exportOptions: { columns: [0, 2, 3, 4] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success my-2",
          title: "Cajas",
          exportOptions: { columns: [0, 2, 3, 4] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info my-2",
          title: "Cajas",
          exportOptions: { columns: [0, 2, 3, 4] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger my-2",
          orientation: "portrait",
          pageSize: "A4",
          title: "Cajas",
          exportOptions: { columns: [0, 2, 3, 4] },
        },
      ],
      keyTable: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
      autoFill: false,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
      drawCallback: () => {
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => el.classList.add("pagination-sm", "mt-2"));
      },
    });
  };

  #register = () => {
    $("#formBox").on("submit", (e) => {
      e.preventDefault();
      showAlert({ title: "Registrando caja..." }, "loading");

      const formData = new FormData(document.getElementById("formBox"));

      this.#api
        .post("setBox", formData)
        .then((data) => {
          showAlert({
            icon: data.icon || (data.status ? "success" : "error"),
            title: data.title || (data.status ? "Listo" : "Error"),
            message: data.message || "",
          });

          if (data.url)
            setTimeout(() => (window.location.href = data.url), 1000);

          if (data.status) {
            $("#formBox")[0].reset();
            $("#modalBox").modal("hide");
            this.#table.ajax.reload(null, false);
          }
        })
        .catch(() => {
          showAlert({
            icon: "error",
            title: "Ocurrió un error",
            message: "No fue posible registrar la caja.",
          });
        });
    });
  };

  // Actualizar caja
  #update = () => {
    $("#formUpdateBox").on("submit", (e) => {
      e.preventDefault();
      showAlert({ title: "Actualizando caja..." }, "loading");

      const formData = new FormData(document.getElementById("formUpdateBox"));

      this.#api
        .post("updateBox", formData)
        .then((data) => {
          showAlert({
            icon: data.icon || (data.status ? "success" : "error"),
            title: data.title || (data.status ? "Listo" : "Error"),
            message: data.message || "",
          });

          if (data.url)
            setTimeout(() => (window.location.href = data.url), 1000);

          if (data.status) {
            $("#modalUpdateBox").modal("hide");
            this.#table.ajax.reload(null, false);
          }
        })
        .catch(() => {
          showAlert({
            icon: "error",
            title: "Ocurrió un error",
            message: "No fue posible actualizar la caja.",
          });
        });
    });
  };

  // Eliminar caja
  #delete = (id, token) => {
    showAlert({ title: "Procesando..." }, "loading");

    this.#api
      .delete("deleteBox", { id, token })
      .then((data) => {
        showAlert({
          icon: data.icon || (data.status ? "success" : "error"),
          title: data.title || (data.status ? "Listo" : "Error"),
          message: data.message || "",
        });

        if (data.url) setTimeout(() => (window.location.href = data.url), 1000);

        if (data.status) this.#table.ajax.reload(null, false);
      })
      .catch(() => {
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible procesar la eliminación.",
        });
      });
  };

  // Reporte de caja
  #report = (id) => {
    this.#api
      .get("getBox", { id })
      .then((res) => {
        if (!res.status) {
          showAlert({
            icon: "error",
            title: res.title || "Error",
            message: res.message || "No fue posible obtener la caja.",
          });
          if (res.url) setTimeout(() => (window.location.href = res.url), 1000);
          return;
        }
        if (res.url) {
          setTimeout(() => (window.location.href = res.url), 1000);
          return;
        }

        const box = res.data;

        $("#reportBoxName").text(box.name || "-");
        $("#reportBoxDescription").text(box.description || "-");
        $("#reportBoxRegistrationDate").text(box.registrationDate || "-");

        const statusEl = document.getElementById("reportBoxStatus");
        if (statusEl) {
          statusEl.classList.remove("bg-success", "bg-danger", "bg-secondary");
          const st = (box.status || "").toString().trim();

          if (st === "Activo") {
            statusEl.textContent = "Activo";
            statusEl.classList.add("bg-success");
          } else if (st === "Inactivo") {
            statusEl.textContent = "Inactivo";
            statusEl.classList.add("bg-danger");
          } else {
            statusEl.textContent = st || "-";
            statusEl.classList.add("bg-secondary");
          }
        }

        $("#modalBoxReport").modal("show");
      })
      .catch(() => {
        showAlert({
          icon: "error",
          title: "Ocurrió un error",
          message: "No fue posible cargar el reporte.",
        });
      });
  };

  // Acciones de los botones
  #actions = () => {
    document.addEventListener("click", (event) => {
      const reportBtn = event.target.closest(".report-box");
      if (reportBtn) {
        event.preventDefault();
        const id = parseInt(reportBtn.getAttribute("data-id") || "0", 10);
        if (id > 0) this.#report(id);
        return;
      }

      const editBtn = event.target.closest(".edit-box");
      if (editBtn) {
        event.preventDefault();
        const id = parseInt(editBtn.getAttribute("data-id") || "0", 10);
        if (id <= 0) return;

        showAlert({ title: "Cargando caja..." }, "loading-float");

        this.#api
          .get("getBox", { id })
          .then((res) => {
            if (!res.status) {
              showAlert({
                icon: "error",
                title: res.title || "Error",
                message: res.message || "No fue posible obtener la caja.",
              });
              if (res.url)
                setTimeout(() => (window.location.href = res.url), 1000);
              return;
            }

            const box = res.data;
            $("#update_idBox").val(box.idBox);
            $("#update_nameBox").val(box.name);
            $("#update_descriptionBox").val(box.description || "");
            $("#modalUpdateBox").modal("show");
          })
          .catch(() => {
            showAlert({
              icon: "error",
              title: "Ocurrió un error",
              message: "No fue posible cargar la caja.",
            });
          })
          .finally(() => {
            if (window.Swal && Swal.close) Swal.close();
          });

        return;
      }

      const deleteBtn = event.target.closest(".delete-box");
      if (deleteBtn) {
        event.preventDefault();
        const id = parseInt(deleteBtn.getAttribute("data-id") || "0", 10);
        const token = deleteBtn.getAttribute("data-token") || "";
        const name = deleteBtn.getAttribute("data-name") || "esta caja";

        if (!token) {
          return showAlert({
            icon: "error",
            title: "Token ausente",
            message:
              "No fue posible validar la solicitud. Actualiza la página e inténtalo nuevamente.",
          });
        }

        // Confirmar eliminación de la caja
        Swal.fire({
          title: "¿Eliminar caja?",
          html: `Se procesará la eliminación de <strong>${name}</strong>.`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#6c757d",
          confirmButtonText: "Sí, continuar",
          cancelButtonText: "Cancelar",
          focusCancel: true,
        }).then((result) => {
          if (result.isConfirmed) this.#delete(id, token);
        });
      }
    });
  };
}

new Boxmanagement();
