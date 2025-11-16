// Esperamos a que todo el DOM esté cargado antes de manipular elementos
document.addEventListener("DOMContentLoaded", function () {
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
    } else {
      step2.classList.add("desktop-hidden");
      step3.classList.remove("desktop-hidden");
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
  const inputDescuentoMonto = document.getElementById("descuentoMonto");
  const inputDescuentoPorc = document.getElementById("descuentoPorc");
  const lblSubtotal = document.getElementById("lblSubtotal");
  const lblTotal = document.getElementById("lblTotal");

  // Cuando el usuario escribe un monto de descuento fijo
  function actualizarDesdeMonto() {
    if (
      !lblSubtotal ||
      !lblTotal ||
      !inputDescuentoMonto ||
      !inputDescuentoPorc
    )
      return;

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
  }

  // Cuando el usuario escribe un porcentaje de descuento
  function actualizarDesdePorcentaje() {
    if (
      !lblSubtotal ||
      !lblTotal ||
      !inputDescuentoMonto ||
      !inputDescuentoPorc
    )
      return;

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
  }

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

  // Botón para finalizar la venta en el modal de cobro
  const btnFinalizarVenta = document.getElementById("btnFinalizarVenta");
  // Donde mostraremos el total en el resumen tipo voucher
  const spanResumenTotal = document.getElementById("resumenTotalVenta");

  if (btnFinalizarVenta) {
    btnFinalizarVenta.addEventListener("click", function () {
      // Cerramos el modal de cobro si existe
      if (modalCobro) {
        modalCobro.hide();
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
});
