(function () {
  "use strict";
  /**
   * Variables de los elementos necesarios
   */
  const listProducts = document.getElementById("listProducts");
  const listCart = document.getElementById("listCart");
  const basketSubtotal = document.getElementById("basketSubtotal");
  // Referencias a los 3 pasos principales
  const step1 = document.getElementById("step1");
  const step2 = document.getElementById("step2");
  const step3 = document.getElementById("step3");

  // Botones de navegación entre pasos en móvil
  const btnToStep2 = document.getElementById("btnToStep2");
  const btnBackToStep1 = document.getElementById("btnBackToStep1");
  const btnToStep3 = document.getElementById("btnToStep3");
  const btnBackToStep2 = document.getElementById("btnBackToStep2");
  const btnDesktopToStep3 = document.getElementById("btnDesktopToStep3");
  const btnDesktopBackToStep2 = document.getElementById("btnDesktopBackToStep2");
  const btnEmptyCart = document.getElementById("btnEmptyCart");

  // Totales y descuentos
  const inputDescuentoMonto = document.getElementById("descuentoMonto");
  const inputDescuentoPorc = document.getElementById("descuentoPorc");
  const lblSubtotal = document.getElementById("lblSubtotal");
  const lblTotal = document.getElementById("lblTotal");

  // Modal de cobro
  const btnFinalizarVenta = document.getElementById("btnFinalizarVenta");
  const spanResumenTotal = document.getElementById("resumenTotalVenta");

  let actualizarDesdeMonto = null;
  let actualizarDesdePorcentaje = null;

  /**
   * Metodo que inicializa todas las funciones de la vista
   */
  function init() {
    // Control de flujo en escritorio (canasta -> pago)
    let desktopStep = 2;

    // Helper para saber si estamos en un dispositivo pequeño (celular)
    function isMobile() {
      return window.innerWidth <= 576;
    }

    /**
     * Muestra u oculta las tarjetas laterales (canasta/pago) en escritorio.
     * Mantiene el estado actual para reusarlo si el usuario redimensiona la ventana.
     *
     * @param {number} stepNumber Paso a mostrar en escritorio (2 = canasta, 3 = pago)
     */
    function showDesktopStep(stepNumber) {
      if (!step2 || !step3 || isMobile()) return;

      desktopStep = stepNumber;
      if (stepNumber === 2) {
        step2.classList.remove("desktop-hidden");
        step3.classList.add("desktop-hidden");
        playStepAnimation(step2);
      } else {
        step2.classList.add("desktop-hidden");
        step3.classList.remove("desktop-hidden");
        playStepAnimation(step3);
      }
    }

    // Muestra solo el paso n en móvil. En PC se muestran todos.
    function showStep(n) {
      if (!isMobile()) {
        // En PC no ocultamos ningún paso, así que quitamos la clase de "activo".
        step1.classList.remove("active-step");
        step2.classList.remove("active-step");
        step3.classList.remove("active-step");
        return;
      }

      const steps = [step1, step2, step3];
      steps.forEach(function (step, index) {
        if (!step) return;
        // Si coincide el índice, es el paso activo
        if (index === n - 1) {
          step.classList.add("active-step");
          playStepAnimation(step);
        } else {
          step.classList.remove("active-step");
        }
      });
    }

    // Estado inicial de los pasos
    if (step1 && step2 && step3) {
      if (isMobile()) {
        // En móvil empezamos en el paso 1 (productos) o conservamos el paso de pago si estaba activo en escritorio
        showStep(desktopStep === 3 ? 3 : 1);
      } else {
        // En escritorio solo mostramos productos + canasta por defecto
        showDesktopStep(desktopStep);
      }

      // Al cambiar el tamaño de la ventana, reajustamos la vista
      window.addEventListener("resize", function () {
        if (!isMobile()) {
          // En PC quitamos el control de active-step y respetamos el flujo lateral
          step1.classList.remove("active-step");
          step2.classList.remove("active-step");
          step3.classList.remove("active-step");
          showDesktopStep(desktopStep);
        } else {
          // En móvil se vuelven a mostrar todos los pasos y se aplica el activo correspondiente
          step2.classList.remove("desktop-hidden");
          step3.classList.remove("desktop-hidden");

          if (
            step1.classList.contains("active-step") ||
            step2.classList.contains("active-step") ||
            step3.classList.contains("active-step")
          ) {
            return;
          }

          showStep(desktopStep === 3 ? 3 : 1);
        }
      });
    }

    // Navegar de Paso 1 -> Paso 2 (móvil)
    if (btnToStep2) {
      btnToStep2.addEventListener("click", function () {
        showStep(2);
        desktopStep = 2;
        if (isMobile() && step2) {
          step2.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }

    // Navegar de Paso 2 -> Paso 3 (móvil)
    if (btnToStep3) {
      btnToStep3.addEventListener("click", function () {
        showStep(3);
        desktopStep = 3;
        if (isMobile() && step3) {
          step3.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }

    // Volver de Paso 2 -> Paso 1 (móvil)
    if (btnBackToStep1) {
      btnBackToStep1.addEventListener("click", function () {
        showStep(1);
        desktopStep = 2;
        if (isMobile() && step1) {
          step1.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }

    // Volver de Paso 3 -> Paso 2 (móvil)
    if (btnBackToStep2) {
      btnBackToStep2.addEventListener("click", function () {
        showStep(2);
        desktopStep = 2;
        if (isMobile() && step2) {
          step2.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }

    // Navegación en escritorio entre canasta y pago
    if (btnDesktopToStep3) {
      btnDesktopToStep3.addEventListener("click", function () {
        showDesktopStep(3);
        if (!isMobile() && step3) {
          step3.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }

    if (btnDesktopBackToStep2) {
      btnDesktopBackToStep2.addEventListener("click", function () {
        showDesktopStep(2);
        if (!isMobile() && step2) {
          step2.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }
    // --- Colocar la fecha actual en el input de fecha de venta ---
    const inputFechaVenta = document.getElementById("fechaVenta");
    if (inputFechaVenta) {
      const hoy = new Date();
      const yyyy = hoy.getFullYear();
      const mm = String(hoy.getMonth() + 1).padStart(2, "0");
      const dd = String(hoy.getDate()).padStart(2, "0");
      // Usamos formato YYYY-MM-DD compatible con inputs type="date"
      inputFechaVenta.value = yyyy + "-" + mm + "-" + dd;
    }

    // --- Descuento: recalcular total a pagar (monto fijo y porcentaje) ---
    actualizarDesdeMonto = function () {
      if (!lblSubtotal || !lblTotal || !inputDescuentoMonto || !inputDescuentoPorc) return;

      const subtotal = parseFloat(lblSubtotal.dataset.valor) || 0;
      let monto = parseFloat(inputDescuentoMonto.value) || 0;

      // Evitar negativos y que el descuento supere al subtotal
      if (monto < 0) monto = 0;
      if (monto > subtotal) monto = subtotal;

      // Calculamos el porcentaje equivalente
      const porcentaje = subtotal > 0 ? (monto / subtotal) * 100 : 0;

      // Formateamos con 2 decimales
      inputDescuentoMonto.value = monto.toFixed(2);
      inputDescuentoPorc.value = porcentaje.toFixed(2);

      // Total nunca menor que cero
      const total = Math.max(subtotal - monto, 0);
      lblTotal.textContent = "S/ " + total.toFixed(2);
    };

    actualizarDesdePorcentaje = function () {
      if (!lblSubtotal || !lblTotal || !inputDescuentoMonto || !inputDescuentoPorc) return;

      const subtotal = parseFloat(lblSubtotal.dataset.valor) || 0;
      let porcentaje = parseFloat(inputDescuentoPorc.value) || 0;

      // Limitar porcentaje entre 0 y 100
      if (porcentaje < 0) porcentaje = 0;
      if (porcentaje > 100) porcentaje = 100;

      // Monto equivalente al porcentaje
      const monto = subtotal * (porcentaje / 100);

      inputDescuentoPorc.value = porcentaje.toFixed(2);
      inputDescuentoMonto.value = monto.toFixed(2);

      const total = Math.max(subtotal - monto, 0);
      lblTotal.textContent = "S/ " + total.toFixed(2);
    };

    // Escuchamos cambios en el input de monto
    if (inputDescuentoMonto) {
      inputDescuentoMonto.addEventListener("input", actualizarDesdeMonto);
    }

    // Escuchamos cambios en el input de porcentaje
    if (inputDescuentoPorc) {
      inputDescuentoPorc.addEventListener("input", actualizarDesdePorcentaje);
    }

    // Inicializamos el total al cargar la página
    if (lblSubtotal && lblTotal) {
      actualizarDesdeMonto();
    }

    // --- Configuración de los modales de Bootstrap ---
    const modalCobroEl = document.getElementById("modalCobro");
    const modalPostVentaEl = document.getElementById("modalPostVenta");

    let modalCobro = null;
    let modalPostVenta = null;

    // Creamos instancias de los modales si Bootstrap está disponible
    if (modalCobroEl && typeof bootstrap !== "undefined") {
      modalCobro = new bootstrap.Modal(modalCobroEl);
    }

    if (modalPostVentaEl && typeof bootstrap !== "undefined") {
      modalPostVenta = new bootstrap.Modal(modalPostVentaEl);
    }

    // Referencias a elementos dentro del modal de cobro
    const spanModalTotal = document.getElementById("modalTotal");
    const inputMontoPaga = document.getElementById("montoPaga");
    const spanVuelto = document.getElementById("montoVuelto");

    // Recalcula el vuelto cuando cambia el monto con el que paga el cliente
    function actualizarVuelto() {
      if (!spanModalTotal || !inputMontoPaga || !spanVuelto) return;
      const total = parseFloat(spanModalTotal.textContent) || 0;
      const paga = parseFloat(inputMontoPaga.value) || 0;
      const vuelto = Math.max(paga - total, 0);
      spanVuelto.textContent = vuelto.toFixed(2);
    }

    if (inputMontoPaga) {
      inputMontoPaga.addEventListener("input", actualizarVuelto);
    }

    // Todos los botones que abren el proceso de cobro (móvil y PC)
    const botonesCobrar = document.querySelectorAll(".btn-cobrar");
    botonesCobrar.forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (!modalCobro || !spanModalTotal || !lblTotal) return;

        // Tomamos el total actual (texto tipo "S/ 209.70") y lo convertimos a número
        const totalTexto = lblTotal.textContent.replace("S/", "").trim();
        const total = parseFloat(totalTexto) || 0;
        spanModalTotal.textContent = total.toFixed(2);

        // Limpiamos inputs del modal de cobro
        if (inputMontoPaga) {
          inputMontoPaga.value = "";
        }
        if (spanVuelto) {
          spanVuelto.textContent = "0.00";
        }

        // Mostramos el modal de cobro
        modalCobro.show();
      });
    });

    if (btnFinalizarVenta) {
      btnFinalizarVenta.addEventListener("click", function () {
        // Cerramos el modal de cobro si existe
        const modalCobroInstance = modalCobro;
        if (modalCobroInstance) {
          modalCobroInstance.hide();
        }

        // Copiamos el total que se veía en el modal de cobro al resumen final
        if (spanModalTotal && spanResumenTotal) {
          const total = parseFloat(spanModalTotal.textContent) || 0;
          spanResumenTotal.textContent = total.toFixed(2);
        }

        // Mostramos el modal post-venta tipo voucher
        if (modalPostVenta) {
          modalPostVenta.show();
        }
      });
    }
  }
  // Esperamos a que todo el DOM esté cargado antes de manipular elementos
  document.addEventListener("DOMContentLoaded", function () {
    init();
    bindCartActions();
    bindEmptyCart();
    //cargamos los productos
    getProducts();
    //cargamos la canasta
    getCart();
  });
  /**
   * Metodo que se encarga de obtener los productos asociados
   */
  async function getProducts() {
    const url = base_url + "/pos/Sales/getProducts";
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(response.statusText + " - " + response.status);
      }
      const data = await response.json();
      if (listProducts) {
        if (data.status) {
          const products = data.products;
          listProducts.innerHTML = "";
          products.forEach((product) => {
            const divCardProduct = renderProductCard(product);
            listProducts.appendChild(divCardProduct);
          });
        }
      }
      //ejecutamos las acciones que correspondes a esta accion
      badgeColor();
      addCart();
    } catch (error) {
      console.error("Error guardando proveedor", error);
      showAlert({
        icon: "error",
        title: "Ocurrió un error",
        message: "No es posible obtener los productos. Inténtalo nuevamente",
        html: `<pre>${error}</pre>`,
      });
    }
  }
  /**
   * Metodo que se encarga de obtener los productos cargados a la canasta
   * @returns
   */
  async function getCart() {
    if (!listCart) return;
    listCart.innerHTML = "";
    const url = base_url + "/pos/Sales/getCart";
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(response.statusText + " - " + response.status);
      }
      const data = await response.json();
      if (!data.status) {
        renderEmptyCart();
        updateTotals(0);
        syncProductCardSelection([]);
        return;
      }
      const cartProducts = data.cart || [];
      if (cartProducts.length === 0) {
        renderEmptyCart();
        updateTotals(0);
        syncProductCardSelection([]);
        return;
      }
      cartProducts.forEach((product) => {
        const divProduct = renderProductCart(product);
        listCart.appendChild(divProduct);
      });
      lockPriceInputs();
      updateTotals(parseFloat(data.subtotal) || 0);
      syncProductCardSelection(cartProducts);
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: "Ocurrio un error con el servidor: " + error.name,
        html: `<pre>${error}</pre>`,
        icon: "error",
        timer: 4000,
      });
    }
  }
  //obtenemos el carda de los productos
  function renderProductCard(product) {
    const divCardProduct = document.createElement("div");
    const buttonProduct = document.createElement("button");
    const spanCounter = document.createElement("span");
    const divImg = document.createElement("div");
    const spanPrice = document.createElement("span");
    const spanName = document.createElement("span");
    const spanStock = document.createElement("span");
    //asignacion de clases
    divCardProduct.classList.add("col-6", "col-md-4", "col-xl-3");
    buttonProduct.classList.add("product-card");
    spanCounter.classList.add("product-counter-badge");
    divImg.classList.add("product-thumb");
    spanPrice.classList.add("product-price", "text-dark");
    spanName.classList.add("product-name");
    spanStock.classList.add("product-stock-badge", "badge");
    const stock = parseFloat(product.stock);
    //asignacion de atributos
    buttonProduct.dataset.selected = "0";
    buttonProduct.dataset.idproduct = product.idproduct;
    buttonProduct.dataset.idsupplier = product.idsupplier;
    buttonProduct.dataset.idmeasurement = product.idmeasurement;
    buttonProduct.dataset.idcategory = product.idcategory;
    buttonProduct.dataset.price = product.price;
    buttonProduct.dataset.product = product.product;
    buttonProduct.dataset.stock = product.stock;
    buttonProduct.dataset.supplier = product.supplier;
    buttonProduct.dataset.category = product.category;
    buttonProduct.dataset.measurement = product.measurement;
    //asignacion de valores
    spanCounter.textContent = "0";
    divImg.innerHTML = `<img class="emoji" src="${base_url}/Storage/Products/product.png" alt="${product.product}">`;
    spanPrice.textContent = getcurrency + product.price;
    spanName.textContent = product.product;
    spanStock.dataset.stock = stock;
    spanStock.innerHTML = `<i class="bi bi-info-circle"></i> ${stock} disponibles`;
    //unificamos para el card completo
    buttonProduct.appendChild(spanCounter);
    buttonProduct.appendChild(divImg);
    buttonProduct.appendChild(spanPrice);
    buttonProduct.appendChild(spanName);
    buttonProduct.appendChild(spanStock);
    divCardProduct.appendChild(buttonProduct);
    return divCardProduct;
  }
  //funcion que encarga de renderizar los productos del carrito
  function renderProductCart(product) {
    const quantity = Math.max(1, parseInt(product.selected, 10) || 1);
    const price = parseFloat(product.price) || 0;
    const amount = (quantity * price).toFixed(2);
    //creamos los elementos necesarios
    const divProduct = document.createElement("div");
    const divHeader = document.createElement("div");
    const divInfo = document.createElement("div");
    const divIcon = document.createElement("div");
    const divNameStock = document.createElement("div");
    const spanName = document.createElement("span");
    const spanStock = document.createElement("span");
    const btnDelete = document.createElement("button");
    const divControls = document.createElement("div");
    const divPriceLine = document.createElement("div");
    const divControlLeft = document.createElement("div");
    const divControlRight = document.createElement("div");
    const inputGroupQty = document.createElement("div");
    const inputGroupPrice = document.createElement("div");
    const btnMinus = document.createElement("button");
    const btnPlus = document.createElement("button");
    const inputQty = document.createElement("input");
    const spanPrefix = document.createElement("span");
    const inputPrice = document.createElement("input");
    //asignamos las clases
    divProduct.classList.add("basket-item");
    divHeader.classList.add("basket-header");
    divInfo.classList.add("basket-info");
    divIcon.classList.add("basket-icon");
    spanName.classList.add("basket-name");
    if (product.stock <= 0) {
      spanStock.classList.add("basket-stock", "text-danger");
    } else {
      spanStock.classList.add("basket-stock", "text-muted");
    }
    btnDelete.classList.add(
      "btn",
      "btn-outline-danger",
      "btn-sm",
      "rounded-circle",
      "btn-delete-cart"
    );
    divControls.classList.add("basket-controls");
    divPriceLine.classList.add("basket-price-line", "text-muted", "mt-1");
    divControlLeft.classList.add("basket-half");
    divControlRight.classList.add("basket-half");
    inputGroupQty.classList.add("input-group", "input-group-sm");
    inputGroupPrice.classList.add("input-group", "input-group-sm");
    btnMinus.classList.add("btn", "btn-outline-secondary", "btn-cart-decrease");
    btnPlus.classList.add("btn", "btn-outline-secondary", "btn-cart-increase");
    inputQty.classList.add("form-control", "text-center", "cart-quantity-input");
    spanPrefix.classList.add("input-group-text");
    inputPrice.classList.add("form-control", "text-end", "cart-price-input");
    //datos auxiliares
    divProduct.dataset.idproduct = product.idproduct;
    divProduct.dataset.stock = product.stock;
    divProduct.dataset.price = price.toFixed(2);
    //llenamos la data
    divIcon.innerHTML = `<i class="bi bi-bag"></i>`;
    spanName.textContent = product.product;
    spanStock.textContent = `${parseFloat(product.stock)} Disponibles`;
    btnDelete.innerHTML = `<i class="bi bi-trash"></i>`;
    btnMinus.innerHTML = `<i class="bi bi-dash"></i>`;
    btnPlus.innerHTML = `<i class="bi bi-plus"></i>`;
    inputQty.type = "number";
    inputQty.value = quantity;
    inputQty.min = "1";
    inputQty.readOnly = false;
    spanPrefix.textContent = "S/";
    inputPrice.type = "text";
    inputPrice.value = amount;
    inputPrice.readOnly = true;
    inputPrice.setAttribute("aria-label", "Precio total del producto en canasta");
    inputPrice.setAttribute("tabindex", "-1");
    divPriceLine.innerHTML = `Precio por <span class="fw-semibold">${quantity}</span> ${product.measurement}: <span class="fw-semibold">${getcurrency} ${amount}</span>`;
    //renderizamos la informacion
    divNameStock.appendChild(spanName);
    divNameStock.appendChild(spanStock);
    divInfo.appendChild(divIcon);
    divInfo.appendChild(divNameStock);
    divHeader.appendChild(divInfo);
    divHeader.appendChild(btnDelete);

    inputGroupQty.appendChild(btnMinus);
    inputGroupQty.appendChild(inputQty);
    inputGroupQty.appendChild(btnPlus);

    inputGroupPrice.appendChild(spanPrefix);
    inputGroupPrice.appendChild(inputPrice);

    divControlLeft.appendChild(inputGroupQty);
    divControlRight.appendChild(inputGroupPrice);

    divControls.appendChild(divControlLeft);
    divControls.appendChild(divControlRight);

    divProduct.appendChild(divHeader);
    divProduct.appendChild(divControls);
    divProduct.appendChild(divPriceLine);

    return divProduct;
  }
  //funcion que se encarga de colorear los badges del stock
  function badgeColor() {
    //prevenimos errores cuando no se encuentra un elemento de este tipo
    if (document.querySelectorAll(".product-stock-badge").length === 0) return;
    // --- Colorear badges de stock según la cantidad disponible ---
    const stockBadges = document.querySelectorAll(".product-stock-badge");
    stockBadges.forEach(function (badge) {
      const stock = parseInt(badge.getAttribute("data-stock"), 10);
      // Quitamos clases posibles antes de aplicar las nuevas
      badge.classList.remove(
        "bg-success",
        "bg-warning",
        "bg-danger",
        "text-white",
        "text-dark"
      );
      if (isNaN(stock)) return;

      if (stock > 10) {
        // Mucho stock -> verde
        badge.classList.add("bg-success", "text-white");
      } else if (stock > 0) {
        // Poco stock -> amarillo
        badge.classList.add("bg-warning", "text-dark");
      } else {
        // Sin stock -> rojo
        badge.classList.add("bg-danger", "text-white");
      }
    });
  }
  //Guncion que se encarga de dar la accion cuando se agrega un producto al carrito o canasta
  function addCart() {
    if (document.querySelectorAll(".product-card").length === 0) return;
    // --- Indicador de cantidad seleccionada en las tarjetas de producto ---
    const productCards = document.querySelectorAll(".product-card");
    productCards.forEach(function (card) {
      const counter = card.querySelector(".product-counter-badge");
      function updateCounter(value) {
        const safeValue = Math.max(0, value);
        card.dataset.selected = String(safeValue);
        card.classList.toggle("has-selection", safeValue > 0);
        if (counter) {
          counter.textContent = safeValue;
          counter.setAttribute(
            "aria-label",
            "Productos seleccionados: " + safeValue
          );
        }
      }
      const initial = parseInt(card.dataset.selected || "0", 10);
      updateCounter(Number.isNaN(initial) ? 0 : initial);
      card.addEventListener("click", async function (event) {
        event.preventDefault();
        const current = parseInt(card.dataset.selected || "0", 10) || 0;
        updateCounter(current + 1);
        //preparamos toda la data para enviar al back
        const formdata = new FormData();
        formdata.append("idproduct", card.dataset.idproduct);
        formdata.append("idsupplier", card.dataset.idsupplier);
        formdata.append("idmeasurement", card.dataset.idmeasurement);
        formdata.append("idcategory", card.dataset.idcategory);
        formdata.append("price", card.dataset.price);
        formdata.append("product", card.dataset.product);
        formdata.append("stock", card.dataset.stock);
        formdata.append("supplier", card.dataset.supplier);
        formdata.append("category", card.dataset.category);
        formdata.append("selected", card.dataset.selected);
        formdata.append("measurement", card.dataset.measurement);
        const url = base_url + "/pos/Sales/addCart";
        const config = {
          method: "POST",
          body: formdata,
        };
        try {
          const response = await fetch(url, config);
          if (!response.ok) {
            throw new Error(response.statusText + " - " + response.status);
          }
          const data = await response.json();
          if (data.status) {
            playStepAnimation(card);
            //actualizamos los datos del cart
            getCart();
          }
          showAlert({
            icon: data.icon,
            title: data.title,
            message: data.message,
          });
        } catch (error) {
          showAlert({
            title: "Ocurrio un error inesperado",
            message: "Ocurrio un error con el servidor: " + error.name,
            icon: "error",
            timer: 4000,
          });
        }
      });
    });
  }
  /**
   * Reproduce una animación suave al mostrar un paso.
   * Remueve y añade la clase para permitir repetición en cambios consecutivos.
   *
   * @param {HTMLElement|null} stepElement Elemento raíz del paso.
   */
  function playStepAnimation(stepElement) {
    if (!stepElement) return;
    stepElement.classList.remove("pos-step-animate");
    void stepElement.offsetHeight; // Reflujo para reiniciar la animación
    stepElement.classList.add("pos-step-animate");
  }

  /**
   * Bloquea los campos de precio de la canasta para evitar modificaciones manuales.
   */
  function lockPriceInputs() {
    if (!listCart) return;
    const priceInputs = listCart.querySelectorAll(".cart-price-input");
    priceInputs.forEach((input) => {
      input.readOnly = true;
      input.setAttribute("tabindex", "-1");
      input.classList.add("bg-light");
    });
  }

  /**
   * Renderiza un estado vacio para la canasta.
   */
  function renderEmptyCart() {
    if (!listCart) return;
    listCart.innerHTML = `
      <div class="p-3 text-center text-muted">
        <i class="bi bi-basket fs-3 d-block mb-2"></i>
        <span>Tu canasta está vacía. Agrega productos para empezar.</span>
      </div>`;
  }

  /**
   * Sincroniza los contadores visuales de las tarjetas de producto con las
   * cantidades efectivas en la canasta.
   *
   * @param {Array} cartItems Lista de productos en la canasta
   */
  function syncProductCardSelection(cartItems) {
    if (!listProducts) return;
    const quantities = {};
    cartItems.forEach(function (item) {
      const qty = parseInt(item.selected ?? "0", 10);
      quantities[item.idproduct] = Number.isNaN(qty) ? 0 : Math.max(qty, 0);
    });

    const cards = listProducts.querySelectorAll(".product-card");
    cards.forEach(function (card) {
      const id = card.dataset.idproduct || "";
      const value = quantities[id] || 0;
      card.dataset.selected = String(value);
      card.classList.toggle("has-selection", value > 0);

      const counter = card.querySelector(".product-counter-badge");
      if (counter) {
        counter.textContent = value;
        counter.setAttribute(
          "aria-label",
          "Productos seleccionados: " + value
        );
      }
    });
  }

  /**
   * Actualiza los totales visibles en pantalla.
   *
   * @param {number} subtotal Monto acumulado de la canasta
   */
  function updateTotals(subtotal) {
    const value = Number(subtotal) || 0;
    const formatted = value.toFixed(2);
    if (basketSubtotal) {
      basketSubtotal.textContent = `${getcurrency} ${formatted}`;
    }
    if (lblSubtotal) {
      lblSubtotal.dataset.valor = formatted;
      lblSubtotal.textContent = `${getcurrency} ${formatted}`;
    }
    if (typeof actualizarDesdeMonto === "function") {
      actualizarDesdeMonto();
    }
  }

  /**
   * Vincula los eventos de la canasta (sumar, restar y eliminar).
   */
  function bindCartActions() {
    if (!listCart) return;
    listCart.addEventListener("click", function (event) {
      const btnIncrease = event.target.closest(".btn-cart-increase");
      if (btnIncrease) {
        handleQuantityChange(btnIncrease, "increment");
        return;
      }
      const btnDecrease = event.target.closest(".btn-cart-decrease");
      if (btnDecrease) {
        handleQuantityChange(btnDecrease, "decrement");
        return;
      }
      const btnDelete = event.target.closest(".btn-delete-cart");
      if (btnDelete) {
        handleDelete(btnDelete);
      }
    });

    listCart.addEventListener("change", function (event) {
      const quantityInput = event.target.closest(".cart-quantity-input");
      if (quantityInput) {
        handleQuantityInput(quantityInput);
      }
    });
  }

  /**
   * Obtiene los datos asociados a un item del carrito.
   *
   * @param {HTMLElement} element Elemento hijo dentro del item de canasta
   * @returns {object|null}
   */
  function getCartItemContext(element) {
    const item = element.closest(".basket-item");
    if (!item) return null;
    const idproduct = item.dataset.idproduct || "";
    const stock = parseFloat(item.dataset.stock || "0");
    const quantityInput = item.querySelector(".cart-quantity-input");
    const quantity = parseInt(quantityInput?.value || "0", 10);
    return { item, idproduct, stock, quantity, quantityInput };
  }

  /**
   * Controla el incremento o decremento de la cantidad.
   *
   * @param {HTMLElement} element Botón presionado
   * @param {"increment"|"decrement"} action Acción solicitada
   */
  function handleQuantityChange(element, action) {
    const context = getCartItemContext(element);
    if (!context) return;

    if (action === "increment" && context.stock > 0 && context.quantity >= context.stock) {
      showAlert({
        icon: "warning",
        title: "Stock insuficiente",
        message: "No hay más stock disponible para este producto.",
      });
      return;
    }

    if (action === "decrement" && context.quantity <= 1) {
      showAlert({
        icon: "info",
        title: "Cantidad mínima",
        message: "La cantidad no puede ser menor a 1. Usa el botón eliminar para quitar el producto.",
      });
      return;
    }

    updateCartItemQuantity(context.idproduct, action);
  }

  /**
   * Gestiona la edición manual de la cantidad desde el input numérico.
   * El mínimo permitido es 1 y, si el usuario ingresa 0 o un negativo,
   * el producto se elimina de la canasta.
   *
   * @param {HTMLInputElement} input Input de cantidad editado manualmente
   */
  function handleQuantityInput(input) {
    const context = getCartItemContext(input);
    if (!context) return;

    const rawValue = parseInt(input.value, 10);
    if (Number.isNaN(rawValue)) {
      input.value = context.quantity;
      return;
    }

    if (rawValue <= 0) {
      removeCartItem(context.idproduct);
      return;
    }

    let desired = rawValue;
    if (context.stock > 0 && rawValue > context.stock) {
      desired = context.stock;
      input.value = desired;
      showAlert({
        icon: "warning",
        title: "Stock insuficiente",
        message: "Se ajustó la cantidad al máximo disponible en inventario.",
      });
    }

    if (desired === context.quantity) return;

    updateCartItemQuantity(context.idproduct, "set", desired);
  }

  /**
   * Gestiona la eliminación de un item del carrito.
   *
   * @param {HTMLElement} element Botón de eliminar
   */
  function handleDelete(element) {
    const context = getCartItemContext(element);
    if (!context) return;
    removeCartItem(context.idproduct);
  }

  /**
   * Solicita al servidor el ajuste de cantidad.
   *
   * @param {string} idproduct Identificador del producto en el carrito
   * @param {"increment"|"decrement"} action Acción a ejecutar
   */
  async function updateCartItemQuantity(idproduct, action, quantity = null) {
    const formdata = new FormData();
    formdata.append("idproduct", idproduct);
    formdata.append("action", action);
    if (action === "set" && quantity !== null) {
      formdata.append("quantity", quantity);
    }
    const url = base_url + "/pos/Sales/updateCartItem";
    try {
      const response = await fetch(url, {
        method: "POST",
        body: formdata,
      });
      if (!response.ok) {
        throw new Error(response.statusText + " - " + response.status);
      }
      const data = await response.json();
      showAlert({
        icon: data.icon,
        title: data.title,
        message: data.message,
      });
      if (data.status) {
        getCart();
      }
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: "Ocurrio un error con el servidor: " + error.name,
        icon: "error",
        timer: 4000,
      });
    }
  }

  /**
   * Elimina un producto del carrito en el servidor.
   *
   * @param {string} idproduct Identificador del producto
   */
  async function removeCartItem(idproduct) {
    const formdata = new FormData();
    formdata.append("idproduct", idproduct);
    const url = base_url + "/pos/Sales/deleteCartItem";
    try {
      const response = await fetch(url, {
        method: "POST",
        body: formdata,
      });
      if (!response.ok) {
        throw new Error(response.statusText + " - " + response.status);
      }
      const data = await response.json();
      showAlert({
        icon: data.icon,
        title: data.title,
        message: data.message,
      });
      if (data.status) {
        getCart();
      }
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: "Ocurrio un error con el servidor: " + error.name,
        icon: "error",
        timer: 4000,
      });
    }
  }

  /**
   * Vincula el botón de vaciar canasta con la petición correspondiente.
   */
  function bindEmptyCart() {
    if (!btnEmptyCart) return;
    btnEmptyCart.addEventListener("click", function () {
      clearCart();
    });
  }

  /**
   * Solicita el vaciado completo de la canasta.
   */
  async function clearCart() {
    const url = base_url + "/pos/Sales/clearCart";
    try {
      const response = await fetch(url, { method: "POST" });
      if (!response.ok) {
        throw new Error(response.statusText + " - " + response.status);
      }
      const data = await response.json();
      showAlert({
        icon: data.icon,
        title: data.title,
        message: data.message,
      });
      if (data.status) {
        renderEmptyCart();
        updateTotals(0);
        syncProductCardSelection([]);
      }
    } catch (error) {
      showAlert({
        title: "Ocurrio un error inesperado",
        message: "Ocurrio un error con el servidor: " + error.name,
        icon: "error",
        timer: 4000,
      });
    }
  }
})();
