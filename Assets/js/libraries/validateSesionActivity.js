async function validarActividad() {
  //hacer que la solicitud al servidor no no lance un error  sin terminar la ejecucion del fetc
  // Realizamos la solicitud al servidor para verificar el tiempo de sesión
  fetch(base_url + "/Lock/checkSessionTimeout")
    .then((response) => response.json())
    .then((data) => {
      console.log(data.message);
      if (data.status === "expired") {
        // Redirigir manualmente si quieres desde JS
        window.location.href = data.url;
      }
    })
    .catch((error) => console.error("Error verificando sesión:", error));
}

// Validamos uso del mouse o teclado
document.addEventListener("keydown", validarActividad);
document.addEventListener("click", validarActividad);
