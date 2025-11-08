window.addEventListener("load", () => {
  loadImagePreview();
  updateDate();
});
function loadImagePreview() {
  const flPhotoNew = document.getElementById("update_flPhoto");
  const imgNewProfile = document.querySelector("#imgNewProfile");
  flPhotoNew.addEventListener("change", () => {
    if (flPhoto.files[0]) {
      imgNewProfile.src = URL.createObjectURL(flPhoto.files[0]);
    }
  });
}
//funcion que actualiza los datos dl usuarios enviandolos al servidor
function updateDate() {
  const formUpdate = document.getElementById("formUpdate");
  formUpdate.addEventListener("submit", (e) => {
    //enviamos el formulario por metodo PUT con todo archivo de imagen
    e.preventDefault();
    const formData = new FormData(formUpdate);
    const header = new Headers();
    const config = {
      method: "POST",
      headers: header,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/Users/updateProfile";
    //cargamos el loader
    elementLoader.classList.remove("d-none");
    fetch(url, config)
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Error en la solicitud " +
              response.status +
              " - " +
              response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        toastr.options = {
          closeButton: true,
          onclick: null,
          showDuration: "300",
          hideDuration: "1000",
          timeOut: "5000",
          progressBar: true,
          onclick: null,
        };
        if (!data.status) {
          toastr[data.type](data.message, data.title);
          //ocultamos el loader
          elementLoader.classList.add("d-none");
          return false;
        }
        //limpiar el formulario
        formUpdate.reset();
        //ocultar el modal abierto
        $("#editarPerfilModal").modal("hide");
        //actualizar la tabla
        toastr[data.type](data.message, data.title);
        //ocultamos el loader
        elementLoader.classList.add("d-none");
        return false;
      })
      .catch((error) => {
        toastr.options = {
          closeButton: true,
          timeOut: 0,
          onclick: null,
        };
        toastr["error"](
          "Error en la solicitud al servidor: " +
            error.message +
            " - " +
            error.name,
          "Ocurrio un error inesperado"
        );
        //ocultamos el loader
        elementLoader.classList.add("d-none");
      });
  });
}
