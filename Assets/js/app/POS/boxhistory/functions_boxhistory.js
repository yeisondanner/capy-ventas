import ReadBox from "./read_box.js";
(() => {
  "use strict";
  //obtenemos los elementos del DOM
  const dateContainer = document.getElementById("date-container") ?? null;
  const dateRangeContainer =
    document.getElementById("date-range-container") ?? null;
  const dateToContainer = document.getElementById("date-to-container") ?? null;
  const dateLabel = document.getElementById("date-label") ?? null;
  const filterType = document.getElementById("filter-type") ?? null;
  const minDate = document.getElementById("min-date") ?? null;
  const maxDate = document.getElementById("max-date") ?? null;
  const filterDate = document.getElementById("filter-date") ?? null;
  const resetBtn = document.getElementById("reset-btn") ?? null;
  const filterBtn = document.getElementById("filter-btn") ?? null;
  //creamos un objeto de la clase ReadBox
  const readBox = new ReadBox();
  /**
   * Inicializamos todos los eventos despues de cargard todo el DOM
   */
  document.addEventListener("DOMContentLoaded", () => {
    readBox.loadTable();
    //inicializamos la funcion toggleFilters
    toggleFilters();
    //inicializamos la funcion resetFilters
    resetFiltersBtn();
    //inicializamos la funcion inputEvents
    inputAndBtnEvents();
  });
  /**
   * Funcion que se encarga de mostras/ocultar los filtros de acuerdo al tipo de filtro seleccionado
   */
  function toggleFilters() {
    //validamos que existan los elementos necesarios
    if (
      !filterType ||
      !minDate ||
      !maxDate ||
      !filterDate ||
      !dateLabel ||
      !dateContainer ||
      !dateRangeContainer ||
      !dateToContainer
    )
      return;
    // Mostrar u ocultar campos de rango personalizado según selección y actualizar comportamiento del campo de fecha
    filterType.addEventListener("change", function () {
      //obtenemos el valor del filtro seleccionado
      const filterTypeValue = this.value;
      if (filterTypeValue === "custom") {
        dateRangeContainer.style.display = "block";
        dateToContainer.style.display = "block";
        dateContainer.style.display = "none";
        // Limpiar los campos de fecha cuando se cambia de rango personalizado a otro tipo
        minDate.value = "";
        maxDate.value = "";
      } else {
        dateRangeContainer.style.display = "none";
        dateToContainer.style.display = "none";
        dateContainer.style.display = "block";

        // Limpiar los campos de fecha personalizados
        minDate.value = "";
        maxDate.value = "";

        // Actualizar la etiqueta del campo de fecha según el tipo de filtro
        switch (filterTypeValue) {
          case "daily":
            dateLabel.textContent = "Fecha:";
            filterDate.type = "date";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("daily");
            break;
          case "weekly":
            dateLabel.textContent = "Semana:";
            filterDate.type = "week";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("weekly");
            break;
          case "monthly":
            dateLabel.textContent = "Mes:";
            filterDate.type = "month";
            filterDate.min = null;
            filterDate.max = null;
            filterDate.step = null;
            filterDate.value = setDefaultDateValue("monthly");
            break;
          case "yearly":
            dateLabel.textContent = "Año:";
            filterDate.type = "number";
            filterDate.min = "1970";
            filterDate.max = new Date().getFullYear() + 10;
            filterDate.step = "1";
            filterDate.value = setDefaultDateValue("yearly");
            break;
          case "all":
            dateContainer.style.display = "none";
            dateRangeContainer.style.display = "none";
            dateToContainer.style.display = "none";
            break;
        }
      }
      //ejecutamos la funcion loadTable
      readBox.loadTable();
    });
  }
  /**
   * MEtodo que se encarga de detectar el evento clic al boton de resetear los filtros
   */
  function resetFiltersBtn() {
    resetBtn.addEventListener("click", resetFilters);
  }
  /**
   * Metodo que se encarga de limpiar los filtros
   */
  function resetFilters() {
    minDate.value = "";
    maxDate.value = "";
    filterType.value = "daily";
    filterDate.type = "date";
    filterDate.min = null;
    filterDate.max = null;
    filterDate.step = null;
    filterDate.value = "daily";
    dateLabel.textContent = "Fecha:";
    dateRangeContainer.style.display = "none";
    dateToContainer.style.display = "none";
    dateContainer.style.display = "block";
    readBox.loadTable();
  }
  // Función para inicializar el campo de fecha con valores predeterminados según el tipo de filtro
  function setDefaultDateValue(filterType) {
    const now = new Date();
    // Para evitar problemas de zona horaria, usamos la fecha local en lugar de ISO
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const todayStr = `${year}-${month}-${day}`;

    switch (filterType) {
      case "daily":
        return todayStr;
      case "weekly":
        const weekNum = getWeekNumber(now);
        const weekYear = now.getFullYear();
        return `${weekYear}-W${weekNum.toString().padStart(2, "0")}`;
      case "monthly":
        return (
          now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0")
        );
      case "yearly":
        return now.getFullYear().toString();
      default:
        return todayStr;
    }
  }
  // Función para obtener el número de semana
  function getWeekNumber(d) {
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    var yearStart = new Date(Date.UTC(d.getFullYear(), 0, 1));
    var weekNo = Math.ceil(
      ((d - yearStart) / 86400000 + yearStart.getUTCDay() + 1) / 7
    );
    return weekNo;
  }
  //Metodo que se encarga de de activarse de los eventos del input
  function inputAndBtnEvents() {
    filterDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    minDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    maxDate.addEventListener("change", function () {
      readBox.loadTable();
    });
    filterBtn.addEventListener("click", function () {
      readBox.loadTable();
    });
  }
})();
