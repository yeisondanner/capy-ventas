export default class DeleteBusiness {
  #deleteBusiness = document.getElementById("deleteBusiness");
  constructor() {}
  /**
   * Metodo que se encarga de eliminar el negocio
   */
  deleteBusiness() {
    this.#deleteBusiness.addEventListener("click", async () => {
      const id = this.#deleteBusiness.dataset.id;
      const token = this.#deleteBusiness.dataset.token;
      const name = this.#deleteBusiness.dataset.name;
      const url = base_url + "/pos/business/delete_bussiness";
      const formdata = new FormData();
      formdata.append("id", id);
      formdata.append("token", token);
      formdata.append("name", name);
      const config = {
        method: "POST",
        body: formdata,
      };
      const resultQuestion = showAlert(
        {
          title: "Eliminar Negocio",
          message: "¿Estas seguro de eliminar el negocio " + name + "?",
          icon: "warning",
          showCancelButton: true,
          confirmText: "Si, eliminar",
          cancelText: "No, cancelar",
        },
        "confirm"
      );
      resultQuestion.then((result) => {
        if (result.isConfirmed) {
          //volvemos a preguntar para confirmar la eliminacion del negocio
          const resultQuestion2 = showAlert(
            {
              title: "Confirmar eliminacion",
              html: `<p class="text-danger fw-bold">¿Esta seguro de eliminar el negocio ${name}? Esta accion no se puede deshacer.</p>
              <p class="text-muted">Si desea eliminarlo, por favor confirme la eliminacion.</p>
              `,
              icon: "warning",
              showCancelButton: true,
              confirmText: "Si, hazlo Ya!",
              cancelText: "No, Cancelar",
            },
            "confirm"
          );
          resultQuestion2.then(async (result) => {
            showAlert(
              {
                title: "En proceso",
                message: "Eliminando el negocio " + name + "...",
                icon: "warning",
              },
              "loading"
            );
            if (result.isConfirmed) {
              try {
                const response = await fetch(url, config);
                const data = await response.json();
                showAlert(data);
                if (data.url) {
                  setTimeout(() => {
                    window.location.href = data.url;
                  }, data.timer);
                }
                return;
              } catch (error) {
                showAlert({
                  title: "Error",
                  message: "No se pudo eliminar el negocio",
                  icon: "error",
                });
                return;
              }
            } else {
              showAlert({
                title: "Uffff! por poquito",
                message: "Eliminación del negocio cancelada",
                icon: "info",
              });
              return;
            }
          });
        } else {
          showAlert({
            title: "Ok!",
            message: "Se cancelo la eliminación del negocio",
            icon: "info",
          });
        }
      });
    });
  }
}
