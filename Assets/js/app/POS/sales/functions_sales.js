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

  // Botones de navegación en escritorio
  const btnDesktopToStep3 = document.getElementById("btnDesktopToStep3");
  const btnDesktopBackToStep2 = document.getElementById("btnDesktopBackToStep2");

  // Helper para saber si estamos en un dispositivo pequeño (celular)
  function isMobile() {
    return window.innerWidth <= 576;
  }

  // Controla el paso visible en móvil y en escritorio
  let currentStep = isMobile() ? 1 : 2;

  function renderSteps() {
    const steps = [step1, step2, step3];
    const mobile = isMobile();

    if (mobile) {
      steps.forEach(function (step, index) {
        if (!step) return;
        step.classList.toggle("active-step", currentStep === index + 1);
      });
      if (step2) step2.classList.remove("active-desktop");
      if (step3) step3.classList.remove("active-desktop");
    } else {
      const desktopStep = currentStep === 3 ? 3 : 2;
      steps.forEach(function (step) {
        step?.classList.remove("active-step");
      });
      if (step2) step2.classList.toggle("active-desktop", desktopStep === 2);
      if (step3) step3.classList.toggle("active-desktop", desktopStep === 3);
    }
  }

  function goToStep(stepNumber) {
    currentStep = stepNumber;
    renderSteps();

    if (isMobile()) {
      const targets = [step1, step2, step3];
      const target = targets[stepNumber - 1];
      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }
  }

  renderSteps();
  window.addEventListener("resize", renderSteps);

  // Navegar de Paso 1 -> Paso 2 (móvil)
  if (btnToStep2) {
    btnToStep2.addEventListener("click", function () {
      goToStep(2);
    });
  }

  // Navegar de Paso 2 -> Paso 3 (móvil y escritorio)
  if (btnToStep3) {
    btnToStep3.addEventListener("click", function () {
      goToStep(3);
    });
  }

  if (btnDesktopToStep3) {
    btnDesktopToStep3.addEventListener("click", function () {
      goToStep(3);
    });
  }

  // Volver de Paso 2 -> Paso 1 (móvil)
  if (btnBackToStep1) {
    btnBackToStep1.addEventListener("click", function () {
      goToStep(1);
    });
  }

  // Volver de Paso 3 -> Paso 2 (móvil y escritorio)
  if (btnBackToStep2) {
    btnBackToStep2.addEventListener("click", function () {
      goToStep(2);
    });
  }

  if (btnDesktopBackToStep2) {
    btnDesktopBackToStep2.addEventListener("click", function () {
      goToStep(2);
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
