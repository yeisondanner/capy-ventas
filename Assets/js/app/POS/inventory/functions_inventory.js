(function () {
  "use strict";
  //variables de los botones
  const btnOpenProductModal =
    document.getElementById("btnOpenProductModal") ?? null;
  //variable para abrir el modal de generacion de codigos de barras masivos
  const btnGenerateAllBarcodes =
    document.getElementById("btnGenerateAllBarcodes") ?? null;
  //Variable para abrir el modal de categorias
  const btnOpenCategoryModal =
    document.getElementById("btnOpenCategoryModal") ?? null;
  //Boton para generar codigo de barras
  const btnGenerateCode = document.getElementById("btnGenerateCode") ?? null;
  //elementos de formularios
  const formSaveProduct = document.getElementById("formSaveProduct") ?? null;
  const formUpdateProduct =
    document.getElementById("formUpdateProduct") ?? null;
  //Elemento de imagenes de preview
  const flInput = document.getElementById("flInput") ?? null;
  const update_flInput = document.getElementById("update_flInput") ?? null;
  const update_btnGenerateCode =
    document.getElementById("update_btnGenerateCode") ?? null;
  //formulario de registro de categorias
  const formCreateCategory =
    document.getElementById("formCreateCategory") ?? null;
  //input de codigo de producto
  const txtProductCode = document.getElementById("txtProductCode") ?? null;
  //input de formato de codigo de barras
  const slctBarcodeFormat =
    document.getElementById("slctBarcodeFormat") ?? null;
  //input de formato de codigo de barras
  const update_slctBarcodeFormat =
    document.getElementById("update_slctBarcodeFormat") ?? null;
  //input de codigo de producto
  const update_txtProductCode =
    document.getElementById("update_txtProductCode") ?? null;
  //Variables para el modal de generacion de codigos de barras masivos
  const print_slctProduct =
    document.getElementById("print_slctProduct") ?? null;
  const btnDeleteAllProducts =
    document.getElementById("btnDeleteAllProducts") ?? null;
  const print_btnAddProduct =
    document.getElementById("print_btnAddProduct") ?? null;
  const btnDownloadBarcodesPdf =
    document.getElementById("btnDownloadBarcodesPdf") ?? null;
  /**
   * Elementos para el procesamiento en segundo plano
   * para la generacion de codigos de barras masivos
   */
  const canvas = document.getElementById("worker-canvas");
  const ctx = canvas.getContext("2d");
  //inicializamos la tabla
  let productsTable;
  let categorysTable;
  //inicializamos el id del product y de la imagen
  let idProduct = null;
  let idImage = null;
  let nameImage = null;
  /**
   * Variables de los selects
   */
  let categorys;
  let suppliers;
  let measurements;
  let productsAll = [];
  let productsInQueue = [];
  const { jsPDF } = window.jspdf;
  /**
   * Variables de los endpoints
   */
  const ENDPOINT_GET_PRODUCTS = `${base_url}/pos/Inventory/getProducts`;
  const ENDPOINT_GET_CATEGORIES = `${base_url}/pos/Inventory/getCategories`;
  const ENDPOINT_GET_MEASUREMENTS = `${base_url}/pos/Inventory/getMeasurements`;
  const ENDPOINT_GET_SUPPLIERS = `${base_url}/pos/Inventory/getSuppliers`;
  const ENDPOINT_SAVE_PRODUCT = `${base_url}/pos/Inventory/setProduct`;
  const ENDPOINT_DELETE_PRODUCT = `${base_url}/pos/Inventory/deleteProduct`;
  const ENDPOINT_GET_PRODUCT = `${base_url}/pos/Inventory/getProduct?id=`;
  const ENDPOINT_UPDATE_PRODUCT = `${base_url}/pos/Inventory/updateProduct`;
  const DEFAULT_IMAGE = `${base_url}/Loadfile/iconproducts?f=product.png`;
  const ENDPOINT_DELETE_IMAGE = `${base_url}/pos/Inventory/deletePhotoImage`;
  const ENDPOINT_GENERATE_PRODUCT_CODE = `${base_url}/pos/Inventory/generateProductCode`;
  const ENDPOINT_GET_CATEGORY_LIST = `${base_url}/pos/Inventory/getCategoryList`;
  const ENDPOINT_SAVE_CATEGORY = `${base_url}/pos/Inventory/setCategory`;
  const ENDPOINT_UPDATE_CATEGORY = `${base_url}/pos/Inventory/updateCategory`;
  const ENDPOINT_DELETE_CATEGORY = `${base_url}/pos/Inventory/deleteCategory`;
  const ENDPOINT_SET_PRODUCT_IN_QUEUE = `${base_url}/pos/Inventory/setProductInQueue`;
  const ENDPOINT_GET_PRODUCTS_IN_QUEUE = `${base_url}/pos/Inventory/getProductsInQueue`;
  const ENDPOINT_UPDATE_PRODUCT_IN_QUEUE = `${base_url}/pos/Inventory/updateItemInQueue`;
  const ENDPOINT_DELETE_PRODUCT_IN_QUEUE = `${base_url}/pos/Inventory/deleteItemInQueue`;
  const ENDPOINT_DELETE_ALL_PRODUCTS_IN_QUEUE = `${base_url}/pos/Inventory/deleteAllProductsInQueue`;
  /**
   * Variable de protegida
   */
  const PROTECTED_CATEGORY = "Sin categoría";
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
     * Inicializamos los selects
     */
    await loadSelects();
    /**
     * Inicializamos el modal de productos
     */
    if (btnOpenProductModal) {
      btnOpenProductModal.addEventListener("click", async function () {
        $("#slctProductCategory").html(categorys);
        $("#slctProductSupplier").html(suppliers);
        $("#slctProductMeasurement").html(measurements);
        $("#modalProduct").modal("show");
      });
    }
    /**
     * Inicializamos el modal de generacion de codigos de barras masivos
     */
    if (btnGenerateAllBarcodes) {
      btnGenerateAllBarcodes.addEventListener("click", async function () {
        /**
         * Inicializamos el select de productos para generar codigos de barras masivos
         */
        if (print_slctProduct) {
          const data = await sendData(ENDPOINT_GET_PRODUCTS, {}, true);
          if (data.status) {
            showAlert({
              title: data.title,
              message: data.message,
              icon: data.icon,
              timer: data.timer,
            });
          }
          if (data.url) {
            setTimeout(() => {
              window.location.href = data.url;
            }, 1000);
            return;
          }
          print_slctProduct.innerHTML =
            "<option value='' disabled selected>Sin productos disponibles</option>";
          if (data.length > 0) {
            productsAll = data;
            let products =
              "<option value='all' selected>Todos los productos</option>";
            data.forEach(function (item) {
              //si el producto tiene codigo de barras
              if (item.bar_code_origin !== null) {
                products += `<option value="${item.idProduct}">${item.name}</option>`;
              }
            });
            print_slctProduct.innerHTML = products;
          }
        }
        /**
         * Cargar la tabla de productos en cola
         */
        await loadtTableProductsInQueue();
        /**
         * Inicializamos el item que actualizará
         */
        updateItemInQueue();
        /**
         * Inicializamos el item que eliminará
         */
        deleteItemQueue();
        $("#modalGenerateAllBarcodes").modal("show");
      });
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
     * Inicializamos el evento para generar el codigo de barras
     */
    if (btnGenerateCode) {
      btnGenerateCode.addEventListener("click", async function () {
        const data = await sendData(ENDPOINT_GENERATE_PRODUCT_CODE, {});
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          //limpiamos el input de codigo de barras
          $("#txtProductCode").val("");
          //establecemos el codigo de barras
          $("#txtProductCode").val(data.code);
          //aceptamos el foco en el input de codigo de barras
          $("#txtProductCode").focus();
          //renderizamos la previsualización del código de barras
          renderBarcodePreview(data.code, "barcode", "barcodeEmptyState");
        }
      });
    }
    /**
     * Evento de escritura en el input de código del modal de creación.
     * Renderiza en tiempo real la previsualización del código de barras.
     */
    if (txtProductCode) {
      txtProductCode.addEventListener("input", function () {
        renderBarcodePreview(
          this.value.trim(),
          "barcode",
          "barcodeEmptyState",
          "slctBarcodeFormat",
        );
      });
    }
    /**
     * Evento de cambio en el selector de formato del código de barras.
     * Re-renderiza la previsualización con el formato recién seleccionado
     * y el código actualmente ingresado en el campo.
     */
    if (slctBarcodeFormat) {
      slctBarcodeFormat.addEventListener("change", function () {
        const currentCode =
          document.getElementById("txtProductCode")?.value.trim() ?? "";
        renderBarcodePreview(
          currentCode,
          "barcode",
          "barcodeEmptyState",
          "slctBarcodeFormat",
        );
      });
    }
    /**
     * Metodo que se encarga de enviar el formulario de registro de productos
     * y procesarlo por completo
     */
    if (formSaveProduct) {
      formSaveProduct.addEventListener("submit", async function (e) {
        e.preventDefault();
        const form = new FormData(formSaveProduct);
        const config = {
          method: "POST",
          body: form,
        };
        const data = await sendData(ENDPOINT_SAVE_PRODUCT, config);
        //mostramos la alerta de exito
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          //limpiamos el formulario
          formSaveProduct.reset();
          //establecemos la imagen por default
          const defaultImage = `${base_url}/Loadfile/iconproducts?f=product.png`;
          //limpiamos la imagen
          renderPreviewImage(defaultImage, "logoPreview");
          //limpiamos la previsualización del código de barras
          renderBarcodePreview("", "barcode", "barcodeEmptyState");
          //actualizamos la tabla
          productsTable.ajax.reload(null, false);
          //cerramos el modal
          $("#modalProduct").modal("hide");
        }
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1500);
        }
      });
    }
    /**
     * Metodo que se encarga de enviar los datos del fomulario del productos
     * para actualizar la informacion
     */
    if (formUpdateProduct) {
      formUpdateProduct.addEventListener("submit", async (e) => {
        e.preventDefault();
        const form = new FormData(formUpdateProduct);
        //adicionamos el id del producto
        form.append("update_txtProductId", idProduct);
        const config = {
          method: "POST",
          body: form,
        };
        const data = await sendData(ENDPOINT_UPDATE_PRODUCT, config);
        //mostramos la alerta de exito
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          //limpiamos el formulario
          formUpdateProduct.reset();
          //limpiamos la imagen
          renderPreviewImage(DEFAULT_IMAGE, "update_logoPreview");
          //actualizamos la tabla
          productsTable.ajax.reload(null, false);
          //cerramos el modal
          $("#modalUpdateProduct").modal("hide");
        }
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1500);
        }
      });
    }
    /**
     * Metodo que se encarga de abrir el modal de categorias
     * y cargar la tabla de categorias y todo el CRUD completo
     */
    if (btnOpenCategoryModal) {
      btnOpenCategoryModal.addEventListener("click", function () {
        loadTableCategorys();
        $("#modalCategory").modal("show");
      });
    }
    /**
     * Metodo que se encarga de registrar una nueva categoria
     */
    if (formCreateCategory) {
      formCreateCategory.addEventListener("submit", async (e) => {
        e.preventDefault();
        const form = new FormData(formCreateCategory);
        const config = {
          method: "POST",
          body: form,
        };
        const data = await sendData(ENDPOINT_SAVE_CATEGORY, config);
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          formCreateCategory.reset();
          categorysTable.ajax.reload(null, false);
        }
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1500);
        }
      });
    }
    /**
     * Metodo que se encarga de obtener la imagen del producto
     */
    if (update_flInput) {
      update_flInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
          renderPreviewImage(e.target.result, "update_logoPreview");
        };
        reader.readAsDataURL(file);
      });
    }
    /**
     * Inicializamos el evento para generar el codigo de barras
     */
    if (update_btnGenerateCode) {
      update_btnGenerateCode.addEventListener("click", async function () {
        const data = await sendData(ENDPOINT_GENERATE_PRODUCT_CODE, {});
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          //limpiamos el input de codigo de barras
          $("#update_txtProductCode").val("");
          //establecemos el codigo de barras
          $("#update_txtProductCode").val(data.code);
          //aceptamos el foco en el input de codigo de barras
          $("#update_txtProductCode").focus();
          renderBarcodePreview(
            data.code,
            "update_barcode",
            "update_barcodeEmptyState",
            "update_slctBarcodeFormat",
          );
        }
      });
    }
    /**
     * 
     * Evento que se ejecuta cuando cambia el formato del código de barras
     */
    if (update_slctBarcodeFormat) {
      update_slctBarcodeFormat.addEventListener("change", function () {
        renderBarcodePreview(
          $("#update_txtProductCode").val(),
          "update_barcode",
          "update_barcodeEmptyState",
          "update_slctBarcodeFormat",
        );
      });
    }
    /**
     * Evento que se ejecuta cuando cambia el codigo de barras
     */
    if (update_txtProductCode) {
      update_txtProductCode.addEventListener("input", function () {
        renderBarcodePreview(
          this.value,
          "update_barcode",
          "update_barcodeEmptyState",
          "update_slctBarcodeFormat",
        );
      });
    }
    /**
     * Evento de agregar a la lista de impresion
     * de codigos de barras
     */
    if (print_btnAddProduct) {
      print_btnAddProduct.addEventListener("click", async function () {
        const formdata = new FormData();
        formdata.append("product", print_slctProduct.value);
        formdata.append("quantity", print_txtQuantity.value);
        const config = {
          method: "POST",
          body: formdata,
        };
        const data = await sendData(ENDPOINT_SET_PRODUCT_IN_QUEUE, config);
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          await loadtTableProductsInQueue();
          updateItemInQueue();
          deleteItemQueue();
        }
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1500);
        }
      });
    }
    /**
     * Evento que se encarga de eliminar todos los productos de la cola de impresion
     */
    if (btnDeleteAllProducts) {
      btnDeleteAllProducts.addEventListener("click", async function () {
        const data = await sendData(
          ENDPOINT_DELETE_ALL_PRODUCTS_IN_QUEUE,
          {
            method: "POST",
          },
          false,
        );
        showAlert({
          title: data.title,
          message: data.message,
          icon: data.icon,
          timer: data.timer,
        });
        if (data.status) {
          await loadtTableProductsInQueue();
          updateItemInQueue();
          deleteItemQueue();
          badgeBarcodeCount.textContent = "0 productos";
          //limpiamos la variable productsAll
          productsAll = [];
        }
        if (data.url) {
          setTimeout(() => {
            window.location.href = data.url;
          }, 1500);
        }
      });
    }
    /**
     * Evento que se encarga de descagar el pdf de los codigo masivos
     */
    if (btnDownloadBarcodesPdf) {
      btnDownloadBarcodesPdf.addEventListener("click", async () => {
        try {
          //verificamos que la cola no este vacia
          if (productsInQueue.length === 0) {
            showAlert({
              title: "Ocurrio un error",
              message:
                "No se pudo generar el pdf, intente nuevamente porque la cola esta vacia",
              icon: "error",
              timer: 1500,
            });
            return;
          }
          showAlert(
            {
              title: "GENERANDO CODIGOS DE BARRAS",
              message:
                "Por favor espere, se esta generando los codigos de barras y empaquetando en un pdf...",
            },
            "loading",
          );
          //Estandarizamos las medidas de la etiqueta (50x25mm)
          const standardWidth = 50;
          const standardHeight = 25;
          //preparamos la configuracion de la hoja del pdf
          const configPdf = {
            orientation: "1",
            unit: "mm",
            format: [standardWidth, standardHeight],
          };
          //creamos una instalacia de la libreria JsPdf
          const doc = new jsPDF(configPdf);
          //controlamos la primera pagina
          let firstPage = true;
          console.log(productsInQueue);
          for (const item of productsInQueue) {
            const imgData = await generateImageTagsBarcode(
              item.name,
              item.code,
              item.format,
              standardWidth,
              standardHeight,
            );
            for (let index = 0; index < item.quantity; index++) {
              if (!firstPage) {
                doc.addPage([standardWidth, standardHeight], "1");
              }
              doc.addImage(imgData, "PNG", 0, 0, standardWidth, standardHeight);
              firstPage = false;
            }
          }
          doc.save(`Codigos_barras_imprimir_${new Date().getTime()}.pdf`);
          Swal.close();
        } catch (error) {
          Swal.close();
          showAlert({
            title: "Ocurrio un error",
            message:
              "No se pudo generar el pdf, intente nuevamente porque la cola esta vacia",
            icon: "error",
            timer: 1500,
          });
        }
      });
    }
  }
  /**
   * Metodo que se encarga de generar los codigos de barras
   */
  function generateImageTagsBarcode(
    name,
    code,
    format = "CODE128",
    mmWidth = 50,
    mmHeight = 25,
  ) {
    //retornamos una promesa
    return new Promise((resolve) => {
      //convertimos las medidas de milimetros a pixeles
      const pxWidth = mmWidth * 10;
      const pxHeight = mmHeight * 10;
      //establecemos las medidas del canvas
      canvas.width = pxWidth;
      canvas.height = pxHeight;
      //limpiamos el canvas
      ctx.fillStyle = "white";
      //pintamos el fondo del canvas
      ctx.fillRect(0, 0, pxWidth, pxHeight);
      //establecemos el color de la tinta
      ctx.fillStyle = "black";
      //establecemos la alineacion del texto
      ctx.textAlign = "center";
      //establecemos el tipo de letra
      ctx.font = `bold ${pxHeight * 0.05}px sans-serif`;
      //agregamos el nombre del producto
      ctx.fillText(name.toUpperCase(), pxWidth / 2, pxHeight * 0.25);
      //obtenemos el elemento svg
      const tempSVG = document.getElementById("hidden-barcode");
      //generamos el codigo de barras
      JsBarcode(tempSVG, code, {
        format: format,
        displayValue: true,
        margin: 0,
        font: "monospace",
        textAlign: "center",
      });
      //convertimos el svg a string
      const xml = new XMLSerializer().serializeToString(tempSVG);
      //convertimos el svg a base64
      const svg64 = btoa(xml);
      //creamos una imagen
      const img = new Image();
      img.onload = function () {
        //calculamos el ancho y el alto del codigo de barras
        const bcWidth = img.width * 1;
        const bcHeight = img.height * 0.65;
        /**
         * Dibujamos el codigo de barras en el canvas
         * @param {Image} img - Imagen del codigo de barras
         * @param {number} x - Posicion en el eje x
         * @param {number} y - Posicion en el eje y
         * @param {number} width - Ancho del codigo de barras
         * @param {number} height - Alto del codigo de barras
         */
        ctx.drawImage(
          img,
          (pxWidth - bcWidth) / 2,
          pxHeight * 0.32,
          bcWidth,
          bcHeight,
        );
        //retornamos el canvas como data url
        resolve(canvas.toDataURL("image/png"));
      };
      //establecemos la fuente de la imagen
      img.src = `data:image/svg+xml;base64,${svg64}`;
    });
  }
  /**
   * Metodo que se encarga de cargar la tabla de productos en cola
   * para imprimir codigos de barras
   * @returns {void}
   */
  async function loadtTableProductsInQueue() {
    const data = await sendData(ENDPOINT_GET_PRODUCTS_IN_QUEUE, {}, false);
    if (data.status) {
      productsInQueue = data.data;
    }
    if (productsInQueue.length > 0) {
      $("#barcodeEmptyResult").addClass("d-none");
      $("#barcodeResultContainer").removeClass("d-none");
      $("#badgeBarcodeCount").text(
        `${productsInQueue.length} ${productsInQueue.length > 1 ? "productos" : "producto"}`,
      );
      $("#tbodyPrintQueue").html("");
      productsInQueue.forEach(function (item) {
        const updateButton =
          item.update === 1
            ? ` <input type="number"
                                                data-id="${item.id}"
                                                class="form-control form-control-sm text-center mx-auto print-update-item-queue"
                                                value="${item.quantity}" min="1" max="1000"
                                                style="width:80px;">`
            : "";
        const deleteButton =
          item.delete === 1
            ? ` <button type="button"
                                                data-id="${item.id}"
                                                data-name="${item.name}"
                                                class="btn btn-sm btn-outline-danger border-0 print-delete-item-queue"
                                                title="Quitar producto">
                                                <i class="bi bi-trash3"></i>
                                            </button>`
            : "";
        $("#tbodyPrintQueue").append(`         
                                    <tr>
                                        <!-- Nombre del producto -->
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:32px; height:32px;">
                                                    <i class="bi bi-box-seam" style="font-size:0.8rem;"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-medium text-dark small">${item.name}</div>
                                                    <div class="text-muted" style="font-size:0.72rem;">
                                                        <span class="font-monospace">${item.code}</span>
                                                        &middot;
                                                        <span>${item.format}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Cantidad de etiquetas a imprimir -->
                                        <td class="text-center">
                                           ${updateButton}
                                        </td>
                                        <!-- Acción: quitar de la lista -->
                                        <td class="text-center">
                                           ${deleteButton}
                                        </td>
                                    </tr>
            
        `);
      });
      return;
    }
    $("#barcodeEmptyResult").removeClass("d-none");
    $("#barcodeResultContainer").addClass("d-none");
  }
  /**
   *Metodo que se encarga de actualizar la cantidad de un producto en la cola de impresion
   * @returns {void}
   */
  function updateItemInQueue() {
    if (productsInQueue.length > 0) {
      const printUpdateItemQueue = document.querySelectorAll(
        ".print-update-item-queue",
      );
      printUpdateItemQueue.forEach(function (item) {
        item.addEventListener("change", async function () {
          const formdata = new FormData();
          formdata.append("product", item.dataset.id);
          formdata.append("quantity", item.value);
          const config = {
            method: "POST",
            body: formdata,
          };
          const data = await sendData(
            ENDPOINT_UPDATE_PRODUCT_IN_QUEUE,
            config,
            false,
          );
          if (data.status) {
            await loadtTableProductsInQueue();
            updateItemInQueue();
            deleteItemQueue();
          }
        });
      });
    }
  }
  /**
   * Metodo que se encarga de eliminar un producto de la cola de impresion
   * @returns {void}
   */
  function deleteItemQueue() {
    if (productsInQueue.length > 0) {
      const printDeleteQueue = document.querySelectorAll(
        ".print-delete-item-queue",
      );
      printDeleteQueue.forEach(function (item) {
        item.addEventListener("click", async function () {
          const formdata = new FormData();
          formdata.append("product", item.dataset.id);
          const config = {
            method: "POST",
            body: formdata,
          };
          const data = await sendData(
            ENDPOINT_DELETE_PRODUCT_IN_QUEUE,
            config,
            false,
          );
          if (data.status) {
            await loadtTableProductsInQueue();
            updateItemInQueue();
            deleteItemQueue();
          }
        });
      });
    }
  }
  /**
   * Metodo que se encarga de cargar la tabla de categorias
   * @returns {void}
   */
  function loadTableCategorys() {
    categorysTable = $("#tableCategorys").DataTable({
      responsive: true,
      processing: true,
      ajax: {
        url: ENDPOINT_GET_CATEGORY_LIST,
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
          defaultContent: `<i class="bi bi-info-circle text-primary h3"></i>`,
        },
        {
          data: "name",
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: "text-center",
          render: function (data, type, row) {
            if (row.name === PROTECTED_CATEGORY) return "";

            if (row.update === 0 && row.delete === 0) {
              return '<span class="badge bg-light text-muted border"><i class="bi bi-slash-circle"></i> Sin permisos</span>';
            }

            let buttons = "";

            if (row.update === 1) {
              buttons += `<button type="button" class="btn btn-outline-primary text-primary edit-category" data-id="${row.idCategory}" data-name="${row.name}"><i class="bi bi-pencil-square"></i></button>`;
            }
            if (row.delete === 1) {
              buttons += `<button type="button" class="btn btn-outline-danger text-danger delete-category" data-id="${row.idCategory}" data-name="${row.name}"><i class="bi bi-trash"></i></button>`;
            }

            return `<div class="btn-group btn-group-sm" role="group">${buttons}</div>`;
          },
        },
      ],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary my-2",
          title: "Listado de categorias",
          exportOptions: { columns: [1] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success my-2",
          title: "Listado de categorias",
          exportOptions: { columns: [1] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info my-2",
          title: "Listado de categorias",
          exportOptions: { columns: [1] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger my-2",
          orientation: "portrait",
          pageSize: "A4",
          title: "Listado de categorias",
          exportOptions: { columns: [1] },
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
        //agregamos estilos a la paginacion
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => {
            el.classList.add("pagination-sm", "mt-2");
          });
        //botones de editar y eliminar
        const btnEditCategory = document.querySelectorAll(".edit-category");
        const btnDeleteCategory = document.querySelectorAll(".delete-category");
        /**
         * Metodo que se encarga de editar una categoria
         */
        if (btnEditCategory.length > 0) {
          btnEditCategory.forEach((el) => {
            el.addEventListener("click", function () {
              const idCategory = this.getAttribute("data-id");
              const categoryName = this.getAttribute("data-name") || "";
              Swal.fire({
                target: "#modalCategory",
                title: "Editar categoría",
                html: `
                <div class="text-start mt-3">
                  <form id="formUpdateCategory" autocomplete="off">
                    <label for="update_txtCategoryName" class="form-label text-muted small mb-1">Nombre de la categoría <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0 text-primary">
                        <i class="bi bi-collection"></i>
                      </span>
                      <input type="text" class="form-control border-start-0 ps-0 shadow-none" 
                        id="update_txtCategoryName" 
                        name="update_txtCategoryName"
                        value="${categoryName}"
                        maxlength="255" required placeholder="Ej. Bebidas calientes">                                
                    </div>
                  </form>
                </div>
              `,
                footer:
                  "<span class='text-danger'>*</span> Campos obligatorios",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#0d6efd",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "<i class='bi bi-save'></i> Guardar cambios",
                cancelButtonText: "<i class='bi bi-x-lg'></i> Cancelar",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                  const input = $("#update_txtCategoryName");
                  input.focus();
                  input.select();
                },
                preConfirm: () => {
                  const newCategoryName = $("#update_txtCategoryName").val();
                  //validamos que el nombre de la categoría no esté vacío
                  if (!newCategoryName.trim()) {
                    Swal.showValidationMessage(
                      "El nombre de la categoría es obligatorio",
                    );
                    const input = $("#update_txtCategoryName");
                    input.focus();
                    input.select();
                    return false;
                  }
                  //preparamos los datos para enviar al backend
                  const formdata = new FormData();
                  formdata.append("categoryId", idCategory);
                  formdata.append("txtCategoryName", newCategoryName.trim());
                  const config = {
                    method: "POST",
                    body: formdata,
                  };
                  return sendData(ENDPOINT_UPDATE_CATEGORY, config);
                },
              }).then((result) => {
                if (result.isConfirmed) {
                  const data = result.value;
                  showAlert({
                    title: data.title,
                    message: data.message,
                    icon: data.icon,
                    timer: data.timer,
                  });
                  if (data.status) {
                    formCreateCategory.reset();
                    categorysTable.ajax.reload(null, false);
                  }
                  if (data.url) {
                    setTimeout(() => {
                      window.location.href = data.url;
                    }, 1500);
                  }
                }
              });
            });
          });
        }

        /**
         * Metodo que se encarga de eliminar una categoria
         */
        if (btnDeleteCategory.length > 0) {
          btnDeleteCategory.forEach((el) => {
            el.addEventListener("click", function () {
              const idCategory = this.getAttribute("data-id");
              const categoryName = this.getAttribute("data-name");
              Swal.fire({
                title: "¿Está seguro de eliminar la categoría?",
                html: `La categoría <strong>"${categoryName}"</strong> será eliminada`,
                footer: `<div style="border: 1px dashed #dc3545"><span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Esta acción no se puede deshacer</span></div>`,
                icon: "warning",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "<i class='bi bi-trash'></i> Si, eliminar",
                cancelButtonText: "<i class='bi bi-x-lg'></i> No, cancelar",
                //hacemos que no se cierre el modal
                allowOutsideClick: false,
                allowEscapeKey: false,
                //preConfirm es una funcion que se ejecuta antes de que se cierre el modal
                preConfirm: async () => {
                  //preparamos los datos para enviar al backend
                  const formData = new FormData();
                  formData.append("id", idCategory);
                  formData.append("name", categoryName);
                  const config = {
                    method: "POST",
                    body: formData,
                  };
                  //enviamos los datos al backend
                  const data = await sendData(ENDPOINT_DELETE_CATEGORY, config);
                  return data;
                },
              }).then((result) => {
                if (result.isConfirmed) {
                  //obtenemos los valores que devuelve el backend
                  const data = result.value;
                  showAlert({
                    title: data.title,
                    message: data.message,
                    icon: data.icon,
                    timer: data.timer,
                  });
                  if (data.status) {
                    //recargamos la tabla
                    categorysTable.ajax.reload(null, false);
                  }
                  //validamos si existe una url para redirigir
                  if (data.url) {
                    setTimeout(() => {
                      window.location.href = data.url;
                    }, 1000);
                  }
                }
              });
            });
          });
        }
      },
    });
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
          defaultContent: `<i class="bi bi-info-circle text-primary h3"></i>`,
        },
        { data: "cont" },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: (data, type, row) => {
            let buttons = "";

            if (row.update === 1) {
              buttons += `<button type="button" class="btn btn-outline-primary text-primary edit-product" data-id="${row.idProduct}" data-name="${row.name}"><i class="bi bi-pencil-square"></i></button>`;
            }
            if (row.delete === 1) {
              buttons += `<button type="button" class="btn btn-outline-danger text-danger delete-product" data-id="${row.idProduct}" data-name="${row.name}"><i class="bi bi-trash"></i></button>`;
            }

            return `<div class="btn-group btn-group-sm" role="group">
                      <button class="btn btn-outline-secondary report-product" data-id="${row.idProduct}" data-name="${row.name}" title="Ver reporte del producto"><i class="bi bi-file-earmark-text"></i></button>
                      ${buttons}
                    </div>`;
          },
        },
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
                let badgeClass = "bg-info message-dark";
                let icon = "bi-info-circle";
                let messageDays = `${totalDays} ${totalDays === 1 ? "día" : "días"}`;

                if (totalDays < 0) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                  const pastDays = Math.abs(totalDays);
                  messageDays = `Vencido hace ${pastDays} ${pastDays === 1 ? "día" : "días"}`;
                } else if (totalDays === 0) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                  messageDays = "Vence hoy";
                } else if (totalDays <= 5) {
                  badgeClass = "bg-danger";
                  icon = "bi-exclamation-octagon";
                } else if (totalDays <= 10) {
                  badgeClass = "bg-warning message-dark";
                  icon = "bi-exclamation-triangle";
                }

                return `
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <span class="fw-bold mb-1 small">${data}</span>
                    <span class="badge rounded-pill ${badgeClass}" title="Próximo a vencer">
                      <i class="bi ${icon} me-1"></i>${messageDays}
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
          orientation: "landscape",
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
        //agregamos estilos a la paginacion
        document
          .querySelectorAll(".dataTables_paginate > .pagination")
          .forEach((el) => {
            el.classList.add("pagination-sm", "mt-2");
          });

        const btnDeleteProduct = document.querySelectorAll(".delete-product");
        const btnEditProduct = document.querySelectorAll(".edit-product");
        const btnReportProduct = document.querySelectorAll(".report-product");
        /**
         * Metodo que se encarga de eliminar un producto
         */
        if (btnDeleteProduct.length > 0) {
          btnDeleteProduct.forEach((el) => {
            el.addEventListener("click", function () {
              idProduct = this.getAttribute("data-id");
              const nameProduct = this.getAttribute("data-name");
              Swal.fire({
                title: "¿Está seguro de eliminar el producto?",
                html: `El producto <strong>"${nameProduct}"</strong> será eliminado`,
                footer: `<div style="border: 1px dashed #dc3545"><span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Esta acción no se puede deshacer</span></div>`,
                icon: "warning",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "<i class='bi bi-trash'></i> Si, eliminar",
                cancelButtonText: "<i class='bi bi-x-lg'></i> No, cancelar",
                //hacemos que no se cierre el modal
                allowOutsideClick: false,
                allowEscapeKey: false,
                //preConfirm es una funcion que se ejecuta antes de que se cierre el modal
                preConfirm: async () => {
                  //preparamos los datos para enviar al backend
                  const formData = new FormData();
                  formData.append("id", idProduct);
                  formData.append("name", nameProduct);
                  const config = {
                    method: "POST",
                    body: formData,
                  };
                  //enviamos los datos al backend
                  const data = await sendData(ENDPOINT_DELETE_PRODUCT, config);
                  return data;
                },
              }).then((result) => {
                if (result.isConfirmed) {
                  //obtenemos los valores que devuelve el backend
                  const data = result.value;
                  showAlert({
                    title: data.title,
                    message: data.message,
                    icon: data.icon,
                    timer: data.timer,
                  });
                  if (data.status) {
                    //recargamos la tabla
                    productsTable.ajax.reload(null, false);
                  }
                  //validamos si existe una url para redirigir
                  if (data.url) {
                    setTimeout(() => {
                      window.location.href = data.url;
                    }, 1000);
                  }
                }
              });
            });
          });
        }
        /**
         * Metodo que se encarga de editar un producto
         */
        if (btnEditProduct.length > 0) {
          btnEditProduct.forEach((edit) => {
            edit.addEventListener("click", async (e) => {
              e.preventDefault();
              idProduct = edit.getAttribute("data-id");
              const urlEndpoint = ENDPOINT_GET_PRODUCT + idProduct;
              /**
               * Metodo que se encarga de obtener los datos del producto
               */
              const data = await sendData(urlEndpoint, {
                method: "GET",
              });
              const prodInf = data.data;
              /**
               * cargamos los selects con los datos obtenidos
               */
              $("#update_slctProductCategory").html(categorys);
              $("#update_slctProductSupplier").html(suppliers);
              $("#update_slctProductMeasurement").html(measurements);
              /**
               * cargamos los inputs con los datos obtenidos
               */
              $("#update_slctBarcodeFormat").val(prodInf.barcode_format);
              $("#update_txtProductCode").val(prodInf.bar_code);
              $("#update_txtProductName").val(prodInf.name);
              $("#update_slctProductCategory").val(prodInf.category_id);
              $("#update_slctProductSupplier").val(prodInf.supplier_id);
              $("#update_slctProductMeasurement").val(prodInf.measurement_id);
              $("#update_txtProductDateExpirated").val(prodInf.expiration_date);
              $("#update_txtProductStock").val(prodInf.stock);
              $("#update_txtProductPurchasePrice").val(prodInf.purchase_price);
              $("#update_txtProductSalesPrice").val(prodInf.sales_price);
              $("#update_txtProductDescription").val(prodInf.description);
              $("#update_logoPreview").attr("src", prodInf.image_main_url);
              $("#listImagesContainer").html(renderAllImages(prodInf.images));
              renderBarcodePreview(
                prodInf.bar_code,
                "update_barcode",
                "update_barcodeEmptyState",
                "update_slctBarcodeFormat",
              );
              deleteImage();
              if (prodInf.is_public == "Si") {
                $("#update_chkProductStatus").prop("checked", true);
              } else if (prodInf.is_public == "No") {
                $("#update_chkProductStatus").prop("checked", false);
              }

              // Se elimina el padding generado por animaciones previas (como SweetAlert) para evitar el encogimiento
              setTimeout(() => {
                //document.body.style.paddingRight = ''; temporalmente se comento para ver como se comporta y si no ocurre el problema
                $("#modalUpdateProduct").modal("show");
              }, 200);
            });
          });
        }
        /**
         * Metodo que se encarga de mostrar el reporte del producto
         */
        if (btnReportProduct.length > 0) {
          btnReportProduct.forEach((btn) => {
            btn.addEventListener("click", (e) => {
              idProduct = btn.getAttribute("data-id");
              renderProductReport(idProduct);
              $("#modalProductReport").modal("show");
            });
          });
        }
      },
    });
  }
  /**
   * Metodo que se encarga de renderizar el reporte del producto
   * @param {*} idProduct
   */
  async function renderProductReport(idProduct) {
    const config = {
      method: "GET",
    };
    const data = await sendData(ENDPOINT_GET_PRODUCT + idProduct, config);
    const info = data.data;
    //cargamos la imagen principal
    $("#reportImageMain").attr("src", info.image_main_url);
    //cargamos la informacion principal del producto
    $("#reportProductName").text(info.name);
    $("#reportProductStatus").text(info.status);
    $("#reportProductStatus").addClass(
      info.status == "Activo"
        ? "badge bg-success text-white"
        : "badge bg-danger text-white",
    );
    $("#reportProductCategory").text(info.category_name);
    //generamos el codigo de barras con dimensiones nativas más pequeñas
    //si en caso el codigo de barras llega vacio que limpie
    $("#reportProductBarcode").empty();
    $("#reportProductCode").removeClass("d-none");
    $("#reportProductBarcode").addClass("d-none");
    if (info.bar_code) {
      const format = info.barcode_format || "CODE128";
      $("#reportProductCode").addClass("d-none");
      $("#reportProductBarcode").removeClass("d-none");
      JsBarcode("#reportProductBarcode", info.bar_code, {
        format: format,
        width: 1.5,
        height: 40,
        fontSize: 14,
        margin: 0,
        valid: function (valid) {
          if (!valid) {
            showError(
              `"${info.bar_code}" no es válido para el formato ${format}.`,
            );
          }
        },
      });
    }
    $("#reportProductCode").text(info.bar_code ? info.bar_code : "-");
    $("#reportProductSupplier").text(info.supplier_name);
    $("#reportProductMeasurement").text(info.measurement_name);
    $("#reportProductDescription").text(info.description);
    $("#reportProductExpirationDate").text(info.expiration_date);
    $("#reportProductIsPublic").text(info.is_public);
    //llenamos la informacion de los kpis
    $("#reportProductStock").text(info.stock_text);
    $("#reportProductPurchase").text(info.purchase_price_text);
    $("#reportProductSale").text(info.sales_price_text);
    //llenamos la descripcion
    $("#reportProductDescription").text(info.description);
    /**
     * Renderizamos las imagenes del producto
     */
    const images = info.images;
    renderImagesReport(images);
    /**
     * Renderizamos el historial del producto
     */
    loatTableHistoryProduct(info);
  }
  /**
   * Metodo que se encarga de cargar la tabla del historial del producto
   * @param {*} info
   */
  function loatTableHistoryProduct(info) {
    const product_history = info.product_history || [];
    //inicializamos el data tables
    if ($.fn.DataTable.isDataTable("#reportTableHistoryProduct")) {
      $("#reportTableHistoryProduct").DataTable().clear().destroy();
    }
    $("#reportTableHistoryProduct").DataTable({
      responsive: true,
      data: product_history,
      columns: [
        {
          data: "",
          className: "dtr-control",
          orderable: false,
          searchable: false,
          width: "10px",
          defaultContent: `<i class="bi bi-info-circle text-primary h3"></i>`,
        },
        {
          data: "bar_code",
          className: "text-center",
          render: function (data, type, row) {
            if (!data || data === "Sin código") {
              return `<span class="text-muted fst-italic small">Sin código</span>`;
            }
            return `
              <div class="lh-sm">
                <div class="fw-bold"><i class="bi bi-upc text-primary me-1"></i>${data}</div>
                <div class="text-muted" style="font-size:0.7rem;">${row.bar_code_format}</div>
              </div>`;
          },
        },
        { data: "name_product" },
        { data: "stock_product_text" },
        { data: "purchase_price_text" },
        { data: "sales_price_text" },
        { data: "category" },
        { data: "expiration_date_product" },
        { data: "fullname_user" },
        { data: "registration_date_product" },
      ],
      columnDefs: [],
      dom: "lBfrtip",
      buttons: [
        {
          extend: "copyHtml5",
          text: "<i class='bi bi-clipboard'></i> Copiar",
          className: "btn btn-sm btn-outline-secondary my-2",
          footer: true,
          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
        },
        {
          extend: "excelHtml5",
          text: "<i class='bi bi-file-earmark-excel'></i> Excel",
          className: "btn btn-sm btn-outline-success my-2",
          title: "Historial del Producto " + info.name,
          //hacemos que tambien se exporte el total
          footer: true,
          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
        },
        {
          extend: "csvHtml5",
          text: "<i class='bi bi-filetype-csv'></i> CSV",
          className: "btn btn-sm btn-outline-info my-2",
          title: "Historial del Producto " + info.name,
          footer: true,
          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
        },
        {
          extend: "pdfHtml5",
          text: "<i class='bi bi-filetype-pdf'></i> PDF",
          className: "btn btn-sm btn-outline-danger my-2",
          orientation: "landscape",
          pageSize: "A4",
          title: "Historial del Producto " + info.name,
          footer: true,
          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] },
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
      footerCallback: function (row, data, start, end, display) {
        let api = this.api();

        // Sumamos solo las filas que están visibles (después de cualquier filtro)
        let totalStock = api
          .rows({ search: "applied" })
          .data()
          .toArray()
          .reduce(
            (sum, item) => sum + (parseFloat(item.stock_product) || 0),
            0,
          );

        $("#totalStockFooter").text(
          totalStock.toFixed(2) + " " + info.measurement_name,
        );
      },
      language: {
        url: `${base_url}/Assets/js/libraries/POS/Spanish-datatables.json`,
      },
    });
  }
  /**
   * Metodo que se encarga de renderizar las imagenes del producto
   * @param {*} images
   */
  function renderImagesReport(images) {
    const listReportImages = document.getElementById("listReportImages");
    listReportImages.innerHTML = "";
    //recorremos todas las imagenes para mostrar
    images.forEach((item) => {
      const divcard = document.createElement("div");
      divcard.classList.add("col-xl-2", "col-lg-3", "col-md-4", "col-6");
      divcard.innerHTML = `<div class="ratio ratio-1x1">
                             <img src="${base_url}/Loadfile/iconproducts?f=${item.name}" class="rounded border object-fit-cover" alt="Vista 1">
                          </div>`;
      listReportImages.appendChild(divcard);
    });
  }
  /**
   * Metodo que se encarga de renderizar todas las imágenes del producto
   * @param {*} images
   * @returns
   */
  function renderAllImages(images) {
    if (images.length == 0) {
      return `
        <div class="col-12 p-2">
            <div class="d-flex flex-column align-items-center justify-content-center text-muted p-4 rounded-3 bg-light w-100 border border-2 border-secondary" style="border-style: dashed; --bs-border-opacity: .3;">
                <i class="bi bi-images text-secondary mb-2 fs-1"></i>
                <h6 class="fw-bold text-secondary mb-1">No se encontraron imágenes</h6>
                <p class="small mb-0 text-center">Este producto aún no cuenta con imágenes en su galería.</p>
            </div>
        </div>
      `;
    }
    let html = "";
    // Iteramos sobre las imágenes
    images.forEach((image) => {
      const urlImage = image.url;
      const nameImage = image.name;
      const idImage = image.idProduct_file;
      html += `
                    <div class="col-6 col-md-5 col-lg-4 col-xl-3 p-2 image-item">
                        <div class="border rounded-3 bg-white shadow-sm position-relative h-100">
                            <!-- Skeleton / Spinner -->
                            <div class="d-flex justify-content-center align-items-center bg-light rounded-3 position-absolute w-100 h-100 top-0 start-0 z-1">
                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <!-- Imagen de producto -->
                            <img src="${urlImage}" class="img-fluid rounded-3 p-1 w-100 position-relative z-2 opacity-0" alt="${nameImage}" loading="lazy" style="object-fit: contain; aspect-ratio: 1/1; transition: opacity 0.3s ease;" onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('d-none');">
                            <!-- Botón eliminar -->
                            <button type="button" data-id=${idImage} data-name=${nameImage} class="btn-delete-image btn btn-danger position-absolute top-0 start-100 translate-middle rounded-circle shadow-sm border border-2 border-white d-flex align-items-center justify-content-center p-0 z-3" style="width: 28px; height: 28px; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Eliminar imagen">
                                <i class="bi bi-x-lg" style="font-size: 12px; -webkit-text-stroke: 0.5px;"></i>
                            </button>
                        </div>
                    </div>
      `;
    });
    return html;
  }
  /**
   * Metodo que se encarga de eliminar una imagen del producto
   * @returns {void}
   */
  function deleteImage() {
    const btnsDeletes = document.querySelectorAll(".btn-delete-image");
    btnsDeletes.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        //obtenemos los datos del boton
        idImage = btn.dataset.id;
        nameImage = btn.dataset.name;
        Swal.fire({
          title: "¿Está seguro de eliminar la imagen?",
          html: `La imagen <strong>"${nameImage}"</strong> será eliminada`,
          footer: `<div style="border: 1px dashed #dc3545"><span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Esta acción no se puede deshacer</span></div>`,
          icon: "warning",
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: "#dc3545",
          cancelButtonColor: "#6c757d",
          confirmButtonText: "<i class='bi bi-trash'></i> Si, eliminar",
          cancelButtonText: "<i class='bi bi-x-lg'></i> No, cancelar",
          //hacemos que no se cierre el modal
          allowOutsideClick: false,
          allowEscapeKey: false,
          //preConfirm es una funcion que se ejecuta antes de que se cierre el modal
          preConfirm: async () => {
            //preparamos los datos para enviar al backend
            const form = new FormData();
            form.append("id", idImage);
            form.append("name", nameImage);
            const config = {
              method: "POST",
              body: form,
            };
            //enviamos los datos al backend
            const data = await sendData(ENDPOINT_DELETE_IMAGE, config);
            return data;
          },
        }).then((result) => {
          if (result.isConfirmed) {
            const data = result.value;
            showAlert({
              title: data.title,
              message: data.message,
              icon: data.icon,
              timer: data.timer,
            });
            if (data.status) {
              //quitamos el elemento del dom
              const image = btn.closest(".image-item");
              image.remove();
            }
            //validamos si existe una url para redirigir
            if (data.url) {
              setTimeout(() => {
                window.location.href = data.url;
              }, 1000);
            }
          }
        });
      });
    });
  }
  /**
   * Creamos el metodo que se encarga de enviar todo al backend asi como tambien obtener los datos
   * @param {string} urlEndpoint - URL del endpoint
   * @param {Object} config - Configuración de la solicitud
   * @returns {Promise<Object>} - Resultado de la solicitud
   */
  async function sendData(urlEndpoint, config, showLoading = true) {
    if (showLoading) {
      showAlert(
        {
          title: "Procesando...",
          message: "Por favor espere un momento",
          icon: "info",
        },
        "loading",
      );
    }
    try {
      const response = await fetch(urlEndpoint, config);
      if (!response.ok) {
        throw new Error(
          "Ocurrio un error al procesar la solicitud " +
            response.status +
            " " +
            response.statusText,
        );
      }
      const result = await response.json();
      return result;
    } catch (error) {
      showAlert({
        title: "Error inesperado",
        message: error,
        icon: "error",
      });
    } finally {
      if (showLoading) {
        swal.close();
      }
    }
  }
  /**
   * Metodo que se encarga de obtner las opciones de los selects
   * asi mismo las normaliza para que cualquier select realice la peticion
   * @param {string} urlEndpoint - Endpoint de la peticion
   * @param {object} config - Configuracion de la peticion
   * @returns {string} - Opciones del select
   */
  async function getAndRenderOptionsSelect(
    urlEndpoint,
    config,
    showLoading = true,
  ) {
    const data = await sendData(urlEndpoint, config, showLoading);
    if (!data.status) {
      showAlert({
        title: data.title,
        message: data.message,
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
  /**
   * Metodo que se encarga de cargar los selects de crear y actualizar producto
   * @returns {Promise<void>}
   */
  async function loadSelects() {
    showAlert(
      {
        title: "Espere un momento",
        message:
          "Estamos procesando su solicitud de visualización de su inventario...",
        icon: "info",
      },
      "loading",
    );
    categorys = await getAndRenderOptionsSelect(
      ENDPOINT_GET_CATEGORIES,
      {
        method: "GET",
      },
      false,
    );
    suppliers = await getAndRenderOptionsSelect(
      ENDPOINT_GET_SUPPLIERS,
      {
        method: "GET",
      },
      false,
    );
    measurements = await getAndRenderOptionsSelect(
      ENDPOINT_GET_MEASUREMENTS,
      {
        method: "GET",
      },
      false,
    );
    swal.close();
  }
  /**
   * Renderiza la previsualización del código de barras en el modal de creación de producto.
   * Si el código está vacío o no es válido para el formato, muestra el estado vacío.
   * @param {string} code  - Valor del código de barras a renderizar.
   * @param {string} svgId - ID del elemento SVG donde se renderiza el barcode.
   * @param {string} emptyStateId - ID del contenedor del estado vacío.
   * @param {string} formatId - ID del select que contiene el formato del código de barras.
   * @returns {void}
   */
  function renderBarcodePreview(code, svgId, emptyStateId, formatId) {
    const svgEl = document.getElementById(svgId);
    const emptyEl = document.getElementById(emptyStateId);
    const formatEl = document.getElementById(formatId);

    // Si no hay código, mostramos el estado vacío y ocultamos el SVG
    if (!code || code.trim() === "") {
      if (svgEl) {
        svgEl.classList.add("d-none");
        // Limpiamos el SVG para evitar residuos visuales
        while (svgEl.firstChild) svgEl.removeChild(svgEl.firstChild);
        svgEl.removeAttribute("style");
      }
      if (emptyEl) emptyEl.classList.remove("d-none");
      return;
    }

    // Obtenemos el formato actualmente seleccionado (por defecto CODE128)
    const format = formatEl ? formatEl.value : "CODE128";

    try {
      JsBarcode(`#${svgId}`, code, {
        format: format,
        width: 1.8,
        height: 50,
        fontSize: 13,
        margin: 4,
        displayValue: true,
        valid: function (valid) {
          if (!valid) {
            // Código no válido para el formato: mostramos estado vacío
            if (svgEl) svgEl.classList.add("d-none");
            if (emptyEl) {
              emptyEl.innerHTML = `<i class="bi bi-exclamation-triangle fs-3 d-block mb-1 text-warning"></i>
                                   <span class="text-warning">Código no válido para el formato <strong>${format}</strong></span>`;
              emptyEl.classList.remove("d-none");
            }
          } else {
            // Código válido: ocultamos el estado vacío y mostramos el barcode
            if (svgEl) svgEl.classList.remove("d-none");
            if (emptyEl) {
              // Restauramos el mensaje original del empty state por si se usó antes
              emptyEl.innerHTML = `<i class="bi bi-upc fs-3 d-block mb-1 opacity-50"></i>
                                   Ingresa o genera un código para previsualizar`;
              emptyEl.classList.add("d-none");
            }
          }
        },
      });
    } catch (e) {
      // En caso de excepción (formato desconocido, etc.) mostramos estado vacío
      if (svgEl) svgEl.classList.add("d-none");
      if (emptyEl) emptyEl.classList.remove("d-none");
    }
  }
})();
