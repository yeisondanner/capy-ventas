(() => {
  "use strict";
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
    if (!document.getElementById("filter-type")) return;
    // Mostrar u ocultar campos de rango personalizado según selección y actualizar comportamiento del campo de fecha
    const filterType = document.getElementById("filter-type");
    filterType.addEventListener("change", function () {
      const filterType = this.value;
      const dateContainer = document.getElementById("date-container");
      const dateRangeContainer = document.getElementById(
        "date-range-container"
      );
      const dateToContainer = document.getElementById("date-to-container");
      const dateLabel = document.getElementById("date-label");

      if (filterType === "custom") {
        dateRangeContainer.style.display = "block";
        dateToContainer.style.display = "block";
        dateContainer.style.display = "none";
        // Limpiar los campos de fecha cuando se cambia de rango personalizado a otro tipo
        document.getElementById("min-date").value = "";
        document.getElementById("max-date").value = "";
      } else {
        dateRangeContainer.style.display = "none";
        dateToContainer.style.display = "none";
        dateContainer.style.display = "block";

        // Limpiar los campos de fecha personalizados
        document.getElementById("min-date").value = "";
        document.getElementById("max-date").value = "";

        // Actualizar la etiqueta del campo de fecha según el tipo de filtro
        switch (filterType) {
          case "daily":
            dateLabel.textContent = "Fecha:";
            document.getElementById("filter-date").type = "date";
            document.getElementById("filter-date").min = null;
            document.getElementById("filter-date").max = null;
            document.getElementById("filter-date").step = null;
            document.getElementById("filter-date").value =
              setDefaultDateValue("daily");
            break;
          case "weekly":
            dateLabel.textContent = "Semana:";
            document.getElementById("filter-date").type = "week";
            document.getElementById("filter-date").min = null;
            document.getElementById("filter-date").max = null;
            document.getElementById("filter-date").step = null;
            document.getElementById("filter-date").value =
              setDefaultDateValue("weekly");
            break;
          case "monthly":
            dateLabel.textContent = "Mes:";
            document.getElementById("filter-date").type = "month";
            document.getElementById("filter-date").min = null;
            document.getElementById("filter-date").max = null;
            document.getElementById("filter-date").step = null;
            document.getElementById("filter-date").value =
              setDefaultDateValue("monthly");
            break;
          case "yearly":
            dateLabel.textContent = "Año:";
            document.getElementById("filter-date").type = "number";
            document.getElementById("filter-date").min = "1970";
            document.getElementById("filter-date").max =
              new Date().getFullYear() + 10;
            document.getElementById("filter-date").step = "1";
            document.getElementById("filter-date").value =
              setDefaultDateValue("yearly");
            break;
        }
      }
    });
  }
})();
