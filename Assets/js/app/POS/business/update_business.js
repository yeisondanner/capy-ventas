export default class UpdateBusiness {
  #businessForm = document.getElementById("businessForm");
  constructor() {}
  /**
   * Metodo que se encarga de actualizar la informacion del negocio
   */
  updateBusiness() {
    if (!this.#businessForm) return;
    this.#businessForm.addEventListener("submit", (e) => {
      e.preventDefault();
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
      } catch (error) {}
    });
  }
}
