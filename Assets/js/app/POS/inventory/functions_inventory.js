(function () {
  "use strict";
  //variables de los botones
  const btnOpenProductModal =
    document.getElementById("btnOpenProductModal") ?? null;
  //elementos de formularios
  const formSaveProduct = document.getElementById("formSaveProduct") ?? null;
  //elementos slct
  const slctProductCategory =
    document.getElementById("slctProductCategory") ?? null;
  const slctProductSupplier =
    document.getElementById("slctProductSupplier") ?? null;
  const slctProductMeasurement =
    document.getElementById("slctProductMeasurement") ?? null;
  //Elemento de imagenes de preview
  const flInput = document.getElementById("flInput") ?? null;
  //inicializamos la tabla
  let productsTable;
  //inicializamos el id del product
  let idProduct = null;
  /**
   * Variables de los endpoints
   */
  const ENDPOINT_GET_PRODUCTS = `${base_url}/pos/Inventory/getProducts`;
  const ENDPOINT_GET_CATEGORIES = `${base_url}/pos/Inventory/getCategories`;
  const ENDPOINT_GET_MEASUREMENTS = `${base_url}/pos/Inventory/getMeasurements`;
  const ENDPOINT_GET_SUPPLIERS = `${base_url}/pos/Inventory/getSuppliers`;
  const ENDPOINT_SAVE_PRODUCT = `${base_url}/pos/Inventory/setProduct`;
  document.addEventListener("DOMContentLoaded", function () {
    loadTable();
    //inicializamos los eventos
    initEvents();
  });
  /**
   * Metodo que incializa todas las funciones del modulo
   * de registro de productos y categorias
   */
  async function initEvents() {
    /**
     * Inicializamos el select de categorias
     */
    if (slctProductCategory) {
      slctProductCategory.innerHTML = await getAndRenderOptionsSelect(
        ENDPOINT_GET_CATEGORIES,
        {
          method: "GET",
        }
      );
    }
    /**
     * Inicializamos el select de proveedores
     */
    if (slctProductSupplier) {
      slctProductSupplier.innerHTML = await getAndRenderOptionsSelect(
        ENDPOINT_GET_SUPPLIERS,
        {
          method: "GET",
        }
      );
    }
    /**
     * Inicializamos el select de medidas
     */
    if (slctProductMeasurement) {
      slctProductMeasurement.innerHTML = await getAndRenderOptionsSelect(
        ENDPOINT_GET_MEASUREMENTS,
        {
          method: "GET",
        }
      );
    }
    /**
     * Inicializamos el input de imagen
     */
    if (flInput) {
      flInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            renderPreviewImage(e.target.result, "logoPreview");
          };
          reader.readAsDataURL(file);
        }
      });
    }
    /**
     * Inicializamos el modal de productos
     */
    if (btnOpenProductModal) {
      btnOpenProductModal.addEventListener("click", function () {
        $("#modalProduct").modal("show");
      });
    }
    if (formSaveProduct) {
      formSaveProduct.addEventListener("submit", async function (e) {
        e.preventDefault();
        const form = new FormData(formSaveProduct);
        const config = {
          method: "POST",
          body: form,
        };
        const data = await sendData(ENDPOINT_SAVE_PRODUCT, config);
        if (!data.status) {
          showAlert(data);
          return;
        }
      });
    }
  }

  /**
   * Configura la tabla de productos con DataTables.
   * @returns {void}
   */
  function loadTable() {
    productsTable = $("#table").DataTable({
      responsive: true,
      processing: true,
      ajax: {
        url: ENDPOINT_GET_PRODUCTS,
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
        {
          data: "",
          className: "dtr-control",
          orderable: false,
          searchable: false,
          width: "10px",
          defaultContent: `<i class="bi bi-plus text-primary h3"></i>`,
        },
        { data: "cont" },
        { data: "actions", orderable: false, searchable: false },
        {
          data: "bar_code",
          className: "text-center",
          render: (data, type, row) => {
            if (data == "Sin código") {
              return `-`;
            }
            return `<span class="bg-primary text-white rounded-2 px-2"><i class="bi bi-upc"></i> ${data}</span>`;
          },
        },
        { data: "name", className: "text-center" },
        {
          data: "expiration_date",
          className: "text-end",
          render: (data, type, row) => {
            if (data != "-") {
              const totalDays = row.days_expiration.total_dias;
              if (totalDays <= 15) {
                let badgeClass = "bg-info text-dark";
                let icon = "bi-info-circle";
                let textDays = `${totalDays} ${totalDays === 1 ? "día" : "días"}`;

                if (totalDays < 0) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                  const pastDays = Math.abs(totalDays);
                  textDays = `Vencido hace ${pastDays} ${pastDays === 1 ? "día" : "días"}`;
                } else if (totalDays === 0) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                  textDays = "Vence hoy";
                } else if (totalDays <= 5) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                } else if (totalDays <= 10) {
                  badgeClass = "bg-warning text-dark";
                  icon = "bi-exclamation-triangle";
                }

                return `
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <span class="fw-bold mb-1 small">${data}</span>
                    <span class="badge rounded-pill ${badgeClass}" title="Próximo a vencer">
                      <i class="bi ${icon} me-1"></i>${textDays}
                    </span>
                  </div>
                `;
              }
            }
            return data;
          },
        },
        { data: "category", className: "text-center" },
        { data: "supplier", className: "text-center" },
        {
          data: "stock_mesurement",
          className: "text-center",
          render: function (data, type, row) {
            if (row.stock <= 5) {
              return (
                '<span class="badge badge-danger bg-danger">' + data + "</span>"
              );
            } else if (row.stock <= 10) {
              return (
                '<span class="badge badge-warning bg-warning">' +
                data +
                "</span>"
              );
            } else if (row.stock <= 15) {
              return (
                '<span class="badge badge-info bg-info">' + data + "</span>"
              );
            } else {
              return (
                '<span class="badge badge-success bg-success">' +
                data +
                "</span>"
              );
            }
          },
        },
        { data: "sales_price", className: "text-center" },
        { data: "purchase_price", className: "text-center" },
        { data: "gain", className: "text-center" },
        {
          data: "is_public",
          className: "text-center",
          render: function (data, type, row) {
            return data === "Si"
              ? '<span class="badge badge-success bg-success" title="Actualmente el producto es visible en el catalgo"><i class="bi bi-check-circle"></i> Sí</span>'
              : '<span class="badge badge-secondary bg-secondary" title="Actualmente el producto no es visible en el catalgo"><i class="bi bi-slash-circle"></i> No</span>';
          },
        },
      ],
      createdRow: function (row, data, dataIndex) {
        if (data.expiration_date != "-" || data.stock != null) {
          const totalDays = data.days_expiration.total_dias;
          if (totalDays <= 5 || data.stock <= 5) {
            $(row).addClass("table-danger");
          } else if (totalDays <= 10 || data.stock <= 10) {
            $(row).addClass("table-warning");
          } else if (totalDays <= 15 || data.stock <= 15) {
            $(row).addClass("table-info");
          }
        }
      },
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary my-2",
          exportOptions: { columns: [1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success my-2",
          title: "Productos",
          exportOptions: { columns: [1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info my-2",
          title: "Productos",
          exportOptions: { columns: [1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger my-2",
          orientation: "portrait",
          pageSize: "A4",
          title: "Productos",
          exportOptions: { columns: [1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] },
        },
      ],
      keyTable: true,
      destroy: true,
      colReorder: true,
      stateSave: true,
      autoFill: false,
      searching: true,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
      drawCallback: () => {
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => {
            el.classList.add("pagination-sm", "mt-2");
          });
      },
    });
  }
  /**
   * Creamos el metodo que se encarga de enviar todo al backend asi como tambien obtener los datos
   * @param {string} urlEndpoint - URL del endpoint
   * @param {Object} config - Configuración de la solicitud
   * @returns {Promise<Object>} - Resultado de la solicitud
   */
  async function sendData(urlEndpoint, config) {
    showAlert(
      {
        title: "Procesando...",
        text: "Por favor espere un momento",
        icon: "info",
      },
      "loading"
    );
    try {
      const response = await fetch(urlEndpoint, config);
      if (!response.ok) {
        throw new Error(
          "Ocurrio un error al procesar la solicitud " +
            response.status +
            " " +
            response.statusText
        );
      }
      const result = await response.json();
      return result;
    } catch (error) {
      showAlert({
        title: "Error inesperado",
        text: error,
        icon: "error",
      });
    } finally {
      swal.close();
    }
  }
  /**
   * Metodo que se encarga de obtner las opciones de los selects
   * asi mismo las normaliza para que cualquier select realice la peticion
   * @param {string} urlEndpoint - Endpoint de la peticion
   * @param {object} config - Configuracion de la peticion
   * @returns {string} - Opciones del select
   */
  async function getAndRenderOptionsSelect(urlEndpoint, config) {
    const data = await sendData(urlEndpoint, config);
    if (!data.status) {
      showAlert({
        title: data.title,
        text: data.text,
        icon: data.icon,
      });
      return false;
    }
    let htmlOptions = "";
    data.data.forEach((element) => {
      htmlOptions += `<option value="${element.id}">${element.name}</option>`;
    });
    return htmlOptions;
  }
  /**
   * Metodo que se encarga de renderizar una imagen para previzualizarla
   * le pasamos la url de la imagen y donde se va a renderizar
   * @param {string} url - URL de la imagen
   * @param {string} element - Elemento donde se va a renderizar
   */
  function renderPreviewImage(url, element) {
    const img = document.getElementById(element);
    img.src = url;
  }
})();
