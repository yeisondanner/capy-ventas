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

  /**
   * Inicializamos todos los eventos despues de cargard todo el DOM
   */
  document.addEventListener("DOMContentLoaded", () => {
    //inicializamos la funcion toggleFilters
    toggleFilters();
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
        }
      }
    });
  }
})();
