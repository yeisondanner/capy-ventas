<?php

/**
 * Controlador para la gestión de planes
 *
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de planes disponibles en CapyVentas.
 */
class Plans extends Controllers
{
   /**
    * Constructor de la clase
    */
   public function __construct()
   {
      isSession();
      parent::__construct();
   }

   /**
    * Muestra la vista principal de gestión de planes
    *
    * @return void
    */
   public function plans()
   {
      $data = [
         'page_id' => 16,
         'page_title' => 'Planes',
         'page_description' => 'Gestiona los planes disponibles en CapyVentas.',
         'page_container' => 'Plans',
         'page_view' => 'plans',
         'page_js_css' => 'plans',
         'page_vars' => ['permission_data', 'login', 'login_info'],
      ];

      permissionInterface($data['page_id']);

      $userId = isset($_SESSION['login_info']['idUser']) ? (int)$_SESSION['login_info']['idUser'] : null;
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
      $method = $_SERVER['REQUEST_METHOD'] ?? null;
      $url = $_SERVER['REQUEST_URI'] ?? null;
      $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

      $payload = [
         'event' => 'page_view',
         'page' => $data['page_title'],
         'page_id' => $data['page_id'],
         'container' => $data['page_container'],
         'user_id' => $userId,
         'ip' => $ip,
         'method' => $method,
         'url' => $url,
         'user_agent' => $userAgent,
         'timestamp' => date('c'),
      ];

      registerLog(
         'Navegación',
         json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
         3,
         $userId
      );

      $this->views->getView($this, 'plans', $data);
   }

   /**
    * Obtiene la lista de todos los planes para mostrar en la tabla
    *
    * @return void
    */
   public function getPlans()
   {
      permissionInterface(16);
      $arrData = $this->model->select_plans();
      $cont = 1;
      foreach ($arrData as $key => $value) {
         $arrData[$key]["cont"] = $cont;

         // Formatear precio
         $arrData[$key]["base_price_formatted"] = number_format($value["base_price"], 2, '.', ',');

         // Formatear periodo de facturación
         $billingPeriodText = $value["billing_period"] == "monthly" ? "Mensual" : "Anual";
         $arrData[$key]["billing_period_text"] = $billingPeriodText;

         // Formatear estado
         $statusText = $value["is_active"] == 1 ? "Activo" : "Inactivo";
         $statusBadge = $value["is_active"] == 1 ? "success" : "danger";
         $arrData[$key]["status_text"] = $statusText;
         $arrData[$key]["status_badge"] = $statusBadge;
         $arrData[$key]["status"] = '<span class="badge badge-' . $statusBadge . '">' . $statusText . '</span>';

         // Botones de acción
         $arrData[$key]["actions"] = '
                <div class="btn-group">
                    <button class="btn btn-success update-item" type="button"
                        data-id="' . $value["idPlan"] . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-description="' . htmlspecialchars($value["description"] ?? "") . '"
                        data-base-price="' . $value["base_price"] . '"
                        data-billing-period="' . $value["billing_period"] . '"
                        data-is-active="' . $value["is_active"] . '"
                    ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-info report-item" type="button"
                        data-id="' . $value["idPlan"] . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-description="' . htmlspecialchars($value["description"] ?? "") . '"
                        data-base-price="' . $value["base_price"] . '"
                        data-base-price-formatted="' . $arrData[$key]["base_price_formatted"] . '"
                        data-billing-period="' . $value["billing_period"] . '"
                        data-billing-period-text="' . $billingPeriodText . '"
                        data-is-active="' . $value["is_active"] . '"
                        data-status-text="' . $statusText . '"
                    ><i class="fa fa-user"></i></button>
                    <button class="btn btn-danger delete-item" 
                        data-id="' . $value["idPlan"] . '" 
                        data-name="' . htmlspecialchars($value["name"]) . '"
                    ><i class="fa fa-remove"></i></button>
                </div>
            ';
         $cont++;
      }
      echo json_encode($arrData);
   }

   /**
    * Registra un nuevo plan en el sistema
    *
    * @return void
    */
   public function setPlan()
   {
      permissionInterface(16);
      if (!$_POST) {
         registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar una persona", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "Método POST no encontrado",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      isCsrf();

      // Obtener y limpiar datos
      $strName = isset($_POST["txtName"]) ? strClean($_POST["txtName"]) : "";
      $strDescription = isset($_POST["txtDescription"]) ? strClean($_POST["txtDescription"]) : "";
      $strBasePrice = isset($_POST["txtBasePrice"]) ? strClean($_POST["txtBasePrice"]) : "";
      $strBillingPeriod = isset($_POST["slctBillingPeriod"]) ? strClean($_POST["slctBillingPeriod"]) : "monthly";
      $intIsActive = isset($_POST["slctIsActive"]) ? (int)$_POST["slctIsActive"] : 1;

      validateFieldsEmpty(array(
         "NOMBRE" => $strName,
         "PRECIO BASE" => $strBasePrice,
      ));

      // Validación de formato de nombre (máximo 50 caracteres)
      if (strlen($strName) > 50) {
         registerLog("Ocurrió un error inesperado", "El campo 'Nombre' excede el límite de 50 caracteres.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Nombre' no puede exceder 50 caracteres.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de precio (debe ser numérico y mayor a 0)
      if (!is_numeric($strBasePrice) || floatval($strBasePrice) < 0) {
         registerLog("Ocurrió un error inesperado", "El campo 'Precio Base' debe ser un número válido mayor o igual a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Precio Base' debe ser un número válido mayor o igual a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de periodo de facturación
      if (!in_array($strBillingPeriod, ['monthly', 'yearly'])) {
         registerLog("Ocurrió un error inesperado", "El campo 'Periodo de Facturación' tiene un valor inválido.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Periodo de Facturación' tiene un valor inválido.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar que el nombre no exista
      $request = $this->model->select_plan_by_name($strName);
      if ($request) {
         registerLog("Ocurrió un error inesperado", "El nombre del plan ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El nombre del plan ingresado ya se encuentra registrado en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }
      unset($request);

      // Convertir nombre a mayúsculas
      $strName = strtoupper($strName);

      // Convertir precio a float
      $floatBasePrice = floatval($strBasePrice);

      // Si la descripción está vacía, establecer como null
      $strDescription = empty($strDescription) ? null : $strDescription;

      // Insertar en la base de datos
      $request = $this->model->insert_plan($strName, $strDescription, $floatBasePrice, $strBillingPeriod, $intIsActive);

      if ($request > 0) {
         registerLog("Registro exitoso", "Se registró un nuevo plan: " . $strName, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Registro exitoso",
            "message" => "El plan se ha registrado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar registrar el plan: " . $strName, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo registrar el plan. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }

   /**
    * Actualiza un plan existente en el sistema
    *
    * @return void
    */
   public function updatePlan()
   {
      permissionInterface(16);

      if (!$_POST) {
         registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar una persona", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "Método POST no encontrado",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      isCsrf();

      // Obtener y limpiar datos
      $intId = isset($_POST["idPlan"]) ? (int)$_POST["idPlan"] : 0;
      $strName = isset($_POST["update_txtName"]) ? strClean($_POST["update_txtName"]) : "";
      $strDescription = isset($_POST["update_txtDescription"]) ? strClean($_POST["update_txtDescription"]) : "";
      $strBasePrice = isset($_POST["update_txtBasePrice"]) ? strClean($_POST["update_txtBasePrice"]) : "";
      $strBillingPeriod = isset($_POST["update_slctBillingPeriod"]) ? strClean($_POST["update_slctBillingPeriod"]) : "monthly";
      $intIsActive = isset($_POST["update_slctIsActive"]) ? (int)$_POST["update_slctIsActive"] : 1;

      validateFieldsEmpty(array(
         "ID DEL PLAN" => $intId,
         "NOMBRE" => $strName,
         "PRECIO BASE" => $strBasePrice,
      ));

      // Validar que el plan exista
      $requestPlan = $this->model->select_plan_by_id($intId);
      if (!$requestPlan) {
         registerLog("Ocurrió un error inesperado", "El plan que intentas actualizar no existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El plan que intentas actualizar no existe en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de nombre (máximo 50 caracteres)
      if (strlen($strName) > 50) {
         registerLog("Ocurrió un error inesperado", "El campo 'Nombre' excede el límite de 50 caracteres.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Nombre' no puede exceder 50 caracteres.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de precio (debe ser numérico y mayor a 0)
      if (!is_numeric($strBasePrice) || floatval($strBasePrice) < 0) {
         registerLog("Ocurrió un error inesperado", "El campo 'Precio Base' debe ser un número válido mayor o igual a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Precio Base' debe ser un número válido mayor o igual a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de periodo de facturación
      if (!in_array($strBillingPeriod, ['monthly', 'yearly'])) {
         registerLog("Ocurrió un error inesperado", "El campo 'Periodo de Facturación' tiene un valor inválido.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Periodo de Facturación' tiene un valor inválido.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar que el nombre no esté duplicado (excepto el actual)
      $requestForName = $this->model->select_plan_by_name($strName);
      if ($requestForName) {
         if ($requestForName['idPlan'] != $intId) {
            registerLog("Ocurrió un error inesperado", "El nombre del plan ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El nombre del plan ya existe. Por favor, ingrese un nombre diferente.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      // Convertir nombre a mayúsculas
      $strName = strtoupper($strName);

      // Convertir precio a float
      $floatBasePrice = floatval($strBasePrice);

      // Si la descripción está vacía, establecer como null
      $strDescription = empty($strDescription) ? null : $strDescription;

      // Actualizar en la base de datos
      $request = $this->model->update_plan($intId, $strName, $strDescription, $floatBasePrice, $strBillingPeriod, $intIsActive);

      if ($request) {
         registerLog("Actualización exitosa", "Se actualizó el plan con ID: " . $intId, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Actualización exitosa",
            "message" => "El plan se ha actualizado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar actualizar el plan con ID: " . $intId, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo actualizar el plan. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }

   /**
    * Elimina un plan del sistema
    *
    * @return void
    */
   public function deletePlan()
   {
      permissionInterface(16);

      if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
         registerLog("Ocurrió un error inesperado", "No se encontró el método DELETE durante el intento de eliminar una persona.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "Método DELETE no encontrado",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      $request = json_decode(file_get_contents("php://input"), true);
      isCsrf($request["token"]);

      $intId = isset($request["idPlan"]) ? (int)$request["idPlan"] : 0;

      validateFieldsEmpty(array(
         "ID DEL PLAN" => $intId,
      ));

      // Validar que el plan exista
      $requestPlan = $this->model->select_plan_by_id($intId);
      if (!$requestPlan) {
         registerLog("Ocurrió un error inesperado", "El plan que intentas eliminar no existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El plan que intentas eliminar no existe en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Eliminar de la base de datos
      $request = $this->model->delete_plan($intId);

      if ($request) {
         registerLog("Eliminación exitosa", "Se eliminó el plan con ID: " . $intId, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Eliminación exitosa",
            "message" => "El plan se ha eliminado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar eliminar el plan con ID: " . $intId, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo eliminar el plan. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }
}
