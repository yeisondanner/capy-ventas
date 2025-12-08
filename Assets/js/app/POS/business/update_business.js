export default class UpdateBusiness {
  #businessForm = document.getElementById("businessForm");
  #btnUpdateBusiness = document.getElementById("btnUpdateBusiness");
  constructor() {}
  /**
   * Metodo que se encarga de actualizar la informacion del negocio
   */
  updateBusiness() {
    if (!this.#businessForm) return;
    if (!this.#btnUpdateBusiness) return;
    this.#businessForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const htmlbtninit = this.#btnUpdateBusiness.innerHTML;
      this.#btnUpdateBusiness.innerHTML =
        "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Actualizando...";
      //desactivar el boton
      this.#btnUpdateBusiness.disabled = true;
      const formData = new FormData(this.#businessForm);
      const endpoint = base_url + "/pos/business/update";
      const config = {
        method: "POST",
        body: formData,
      };
      showAlert(
        {
          title: "Actualizando negocio",
          message: "Por favor, espere...",
        },
        "loading"
      );
      try {
        const response = await fetch(endpoint, config);
        const data = await response.json();
        showAlert(data);
      } catch (error) {
        showAlert({
          title: "Error",
          message: "Ocurrió un error al actualizar el negocio.",
          icon: "error",
        });
      } finally {
        this.#btnUpdateBusiness.innerHTML = htmlbtninit;
        this.#btnUpdateBusiness.disabled = false;
      }
    });
  }
}
