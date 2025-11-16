(function () {
  "use strict";

  var treeviewMenu = $(".app-menu");

  // Toggle Sidebar
  $('[data-toggle="sidebar"]').click(function (event) {
    event.preventDefault();
    $(".app").toggleClass("sidenav-toggled");
  });

  // Activate sidebar treeview toggle
  $("[data-toggle='treeview']").click(function (event) {
    event.preventDefault();
    if (!$(this).parent().hasClass("is-expanded")) {
      treeviewMenu
        .find("[data-toggle='treeview']")
        .parent()
        .removeClass("is-expanded");
    }
    $(this).parent().toggleClass("is-expanded");
  });
})();
/**
 * Creamos una funcion de tipos de alertas con sweetalert2
 */
function showAlert(data = {}, type = "float") {
  switch (type) {
    case "float":
      Swal.fire({
        icon: data.icon ?? "success",
        title: data.title ?? "Satisfactorio",
        text: data.message ?? "Conexión exitosa",
        html: data.html ?? "",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: data.timer ?? 2500,
        timerProgressBar: true,
      });
      break;

    default:
      break;
  }
}
