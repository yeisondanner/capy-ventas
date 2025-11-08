window.addEventListener("load", function () {
  unlock(); // Function that handles the unlock form submission
});
function unlock() {
  const formUnlock = document.getElementById("formUnlock");
  formUnlock.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the form from submitting normally
    const formData = new FormData(formUnlock); // Create a FormData object from the form
    const headers = new Headers();
    const config = {
      method: "POST",
      headers: headers,
      node: "no-cache",
      cors: "cors",
      body: formData,
    };
    const url = base_url + "/Lock/unLockLogin"; // URL to send the form data
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
          //limpiar el formulario
          formUnlock.reset();
          toastr[data.type](data.message, data.title);
          elementLoader.classList.add("d-none");
          return false;
        }
        toastr[data.type](data.message, data.title);
        formUnlock.reset();
        setTimeout(() => {
          elementLoader.classList.add("d-none");
          window.location.href = data.redirection;
        }, 1000);
        return true;
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
        elementLoader.classList.add("d-none");
      });
  });
}
