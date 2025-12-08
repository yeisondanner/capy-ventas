import UpdateBusiness from "./update_business.js";
import DeleteBusiness from "./delete_bussiness.js";
(function () {
  "use strict";
  //creamos un objeto de la clase UpdateBusiness
  const updateBusinessInstance = new UpdateBusiness();
  //creamos un objeto de la clase DeleteBusiness
  const deleteBusinessInstance = new DeleteBusiness();
  /**
   * Eventos al cargar el documento
   * @returns void
   */
  document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
      //cargamos la funcionalidad de actualizacion del negocio
      updateBusinessInstance.updateBusiness();
      //cargamos la funcionalidad de eliminacion del negocio
      deleteBusinessInstance.deleteBusiness();
      //cargamos la imagen seleccionada
      loadPreviewImage();
      //seleccionamos el tipo de negocio
      document.getElementById("update_slctTypeBusiness").value = typeBusiness;
    }, 1000);
  });
  /**
   * Carga la imagen seleccionada en el input de tipo file
   * @returns void
   */
  function loadPreviewImage() {
    if (!document.getElementById("update_logoInput")) return;
    const logoInput = document.getElementById("update_logoInput");
    // Preview de imagen
    logoInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("logoPreview").src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
})();
