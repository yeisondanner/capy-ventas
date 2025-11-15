<?php

/**
 * Controlador para la gestión de descuentos
 *
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de descuentos y cupones promocionales del sistema.
 */
class Discounts extends Controllers
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
    * Muestra la vista principal de gestión de descuentos
    *
    * @return void
    */
   public function discounts()
   {
      $data = [
         'page_id' => 21,
         'page_title' => 'Descuentos',
         'page_description' => 'Gestiona los descuentos y cupones promocionales del sistema.',
         'page_container' => 'Discounts',
         'page_view' => 'discounts',
         'page_js_css' => 'discounts',
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

      $this->views->getView($this, 'discounts', $data);
   }

   /**
    * Obtiene la lista de todos los descuentos para mostrar en la tabla
    *
    * @return void
    */
   public function getDiscounts()
   {
      permissionInterface(21);
      $arrData = $this->model->select_discounts();
      $cont = 1;
      foreach ($arrData as $key => $value) {
         $arrData[$key]["cont"] = $cont;

         // Formatear tipo de descuento
         $typeText = $value["type"] == "percentage" ? "Porcentaje" : "Monto Fijo";
         $arrData[$key]["type_text"] = $typeText;

         // Formatear valor según el tipo
         if ($value["type"] == "percentage") {
            $arrData[$key]["value_formatted"] = number_format($value["value"], 2, '.', ',') . "%";
         } else {
            $arrData[$key]["value_formatted"] = "$ " . number_format($value["value"], 2, '.', ',');
         }

         // Formatear fechas
         $arrData[$key]["start_date_formatted"] = !empty($value["start_date"]) ? dateFormat($value["start_date"], "d/m/Y H:i") : "-";
         $arrData[$key]["end_date_formatted"] = !empty($value["end_date"]) ? dateFormat($value["end_date"], "d/m/Y H:i") : "-";

         // Formatear plan aplicable
         $arrData[$key]["plan_name_display"] = !empty($value["plan_name"]) ? $value["plan_name"] : "Todos los planes";

         // Formatear máximo de usos
         $arrData[$key]["max_uses_display"] = !empty($value["max_uses"]) ? $value["max_uses"] : "Ilimitado";

         // Formatear si es recurrente
         $isRecurringText = $value["is_recurring"] == 1 ? "Sí" : "No";
         $arrData[$key]["is_recurring_text"] = $isRecurringText;

         // Botones de acción
         $arrData[$key]["actions"] = '
                <div class="btn-group">
                    <button class="btn btn-success update-item" type="button"
                        data-id="' . $value["idDiscount"] . '"
                        data-code="' . htmlspecialchars($value["code"]) . '"
                        data-type="' . $value["type"] . '"
                        data-value="' . $value["value"] . '"
                        data-start-date="' . ($value["start_date"] ?? "") . '"
                        data-end-date="' . ($value["end_date"] ?? "") . '"
                        data-applies-to-plan-id="' . ($value["applies_to_plan_id"] ?? "") . '"
                        data-max-uses="' . ($value["max_uses"] ?? "") . '"
                        data-is-recurring="' . $value["is_recurring"] . '"
                    ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-info report-item" type="button"
                        data-id="' . $value["idDiscount"] . '"
                        data-code="' . htmlspecialchars($value["code"]) . '"
                        data-type="' . $value["type"] . '"
                        data-type-text="' . $typeText . '"
                        data-value="' . $value["value"] . '"
                        data-value-formatted="' . $arrData[$key]["value_formatted"] . '"
                        data-start-date="' . ($value["start_date"] ?? "") . '"
                        data-start-date-formatted="' . $arrData[$key]["start_date_formatted"] . '"
                        data-end-date="' . ($value["end_date"] ?? "") . '"
                        data-end-date-formatted="' . $arrData[$key]["end_date_formatted"] . '"
                        data-plan-name="' . htmlspecialchars($arrData[$key]["plan_name_display"]) . '"
                        data-max-uses="' . ($value["max_uses"] ?? "") . '"
                        data-max-uses-display="' . $arrData[$key]["max_uses_display"] . '"
                        data-is-recurring="' . $value["is_recurring"] . '"
                        data-is-recurring-text="' . $isRecurringText . '"
                    ><i class="fa fa-user"></i></button>
                    <button class="btn btn-danger delete-item" 
                        data-id="' . $value["idDiscount"] . '" 
                        data-code="' . htmlspecialchars($value["code"]) . '"
                    ><i class="fa fa-remove"></i></button>
                </div>
            ';
         $cont++;
      }
      echo json_encode($arrData);
   }

   /**
    * Obtiene los planes activos para usar en select
    *
    * @return void
    */
   public function getPlansSelect()
   {
      permissionInterface(21);
      $arrData = $this->model->select_plans_active();
      echo json_encode($arrData);
   }

   /**
    * Registra un nuevo descuento en el sistema
    *
    * @return void
    */
   public function setDiscount()
   {
      permissionInterface(21);

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
      $strCode = isset($_POST["txtCode"]) ? strClean($_POST["txtCode"]) : "";
      $strType = isset($_POST["slctType"]) ? strClean($_POST["slctType"]) : "";
      $strValue = isset($_POST["txtValue"]) ? strClean($_POST["txtValue"]) : "";
      $strStartDate = isset($_POST["txtStartDate"]) ? strClean($_POST["txtStartDate"]) : "";
      $strEndDate = isset($_POST["txtEndDate"]) ? strClean($_POST["txtEndDate"]) : "";
      $intAppliesToPlanId = isset($_POST["slctAppliesToPlanId"]) && !empty($_POST["slctAppliesToPlanId"]) ? (int)$_POST["slctAppliesToPlanId"] : null;
      $intMaxUses = isset($_POST["txtMaxUses"]) && !empty($_POST["txtMaxUses"]) ? (int)$_POST["txtMaxUses"] : null;
      $intIsRecurring = isset($_POST["slctIsRecurring"]) ? (int)$_POST["slctIsRecurring"] : 0;

      validateFieldsEmpty(array(
         "CÓDIGO" => $strCode,
         "TIPO" => $strType,
         "VALOR" => $strValue,
      ));

      // Validación de formato de código (máximo 50 caracteres, solo mayúsculas, números y guiones)
      if (strlen($strCode) > 50) {
         registerLog("Ocurrió un error inesperado", "El campo 'Código' excede el límite de 50 caracteres.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Código' no puede exceder 50 caracteres.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar formato del código (solo letras, números y guiones)
      if (!preg_match('/^[A-Z0-9\-]+$/', $strCode)) {
         registerLog("Ocurrió un error inesperado", "El campo 'Código' contiene caracteres inválidos.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Código' solo puede contener letras mayúsculas, números y guiones.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de tipo de descuento
      if (!in_array($strType, ['percentage', 'fixed'])) {
         registerLog("Ocurrió un error inesperado", "El campo 'Tipo' tiene un valor inválido.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Tipo' tiene un valor inválido.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de valor (debe ser numérico)
      if (!is_numeric($strValue) || floatval($strValue) < 0) {
         registerLog("Ocurrió un error inesperado", "El campo 'Valor' debe ser un número válido mayor o igual a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Valor' debe ser un número válido mayor o igual a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación específica según el tipo
      $floatValue = floatval($strValue);
      if ($strType == "percentage" && ($floatValue < 0 || $floatValue > 100)) {
         registerLog("Ocurrió un error inesperado", "El porcentaje debe estar entre 0 y 100.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El porcentaje debe estar entre 0 y 100.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de fechas si se proporcionan
      $startDateFormatted = null;
      $endDateFormatted = null;
      if (!empty($strStartDate)) {
         $startDateFormatted = date('Y-m-d H:i:s', strtotime($strStartDate));
         if ($startDateFormatted === false) {
            registerLog("Ocurrió un error inesperado", "El campo 'Fecha de Inicio' no tiene un formato válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El campo 'Fecha de Inicio' no tiene un formato válido.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      if (!empty($strEndDate)) {
         $endDateFormatted = date('Y-m-d H:i:s', strtotime($strEndDate));
         if ($endDateFormatted === false) {
            registerLog("Ocurrió un error inesperado", "El campo 'Fecha de Fin' no tiene un formato válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El campo 'Fecha de Fin' no tiene un formato válido.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      // Validar que la fecha de inicio sea anterior o igual a la fecha de fin
      if (!empty($strStartDate) && !empty($strEndDate) && strtotime($strStartDate) > strtotime($strEndDate)) {
         registerLog("Ocurrió un error inesperado", "La fecha de inicio debe ser anterior o igual a la fecha de fin.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "La fecha de inicio debe ser anterior o igual a la fecha de fin.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de máximo de usos si se proporciona
      if ($intMaxUses !== null && $intMaxUses < 1) {
         registerLog("Ocurrió un error inesperado", "El máximo de usos debe ser mayor a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El máximo de usos debe ser mayor a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar que el código no exista
      $request = $this->model->select_discount_by_code($strCode);
      if ($request) {
         registerLog("Ocurrió un error inesperado", "El código del descuento ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El código del descuento ingresado ya se encuentra registrado en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }
      unset($request);

      // Convertir código a mayúsculas
      $strCode = strtoupper($strCode);

      // Insertar en la base de datos
      $request = $this->model->insert_discount($strCode, $strType, $floatValue, $startDateFormatted, $endDateFormatted, $intAppliesToPlanId, $intMaxUses, $intIsRecurring);

      if ($request > 0) {
         registerLog("Registro exitoso", "Se registró un nuevo descuento: " . $strCode, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Registro exitoso",
            "message" => "El descuento se ha registrado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar registrar el descuento: " . $strCode, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo registrar el descuento. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }

   /**
    * Actualiza un descuento existente en el sistema
    *
    * @return void
    */
   public function updateDiscount()
   {
      permissionInterface(21);

      if (!$_POST) {
         registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de actualizar una persona", 1, $_SESSION['login_info']['idUser']);
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
      $intId = isset($_POST["idDiscount"]) ? (int)$_POST["idDiscount"] : 0;
      $strCode = isset($_POST["update_txtCode"]) ? strClean($_POST["update_txtCode"]) : "";
      $strType = isset($_POST["update_slctType"]) ? strClean($_POST["update_slctType"]) : "";
      $strValue = isset($_POST["update_txtValue"]) ? strClean($_POST["update_txtValue"]) : "";
      $strStartDate = isset($_POST["update_txtStartDate"]) ? strClean($_POST["update_txtStartDate"]) : "";
      $strEndDate = isset($_POST["update_txtEndDate"]) ? strClean($_POST["update_txtEndDate"]) : "";
      $intAppliesToPlanId = isset($_POST["update_slctAppliesToPlanId"]) && !empty($_POST["update_slctAppliesToPlanId"]) ? (int)$_POST["update_slctAppliesToPlanId"] : null;
      $intMaxUses = isset($_POST["update_txtMaxUses"]) && !empty($_POST["update_txtMaxUses"]) ? (int)$_POST["update_txtMaxUses"] : null;
      $intIsRecurring = isset($_POST["update_slctIsRecurring"]) ? (int)$_POST["update_slctIsRecurring"] : 0;

      validateFieldsEmpty(array(
         "ID DEL DESCUENTO" => $intId,
         "CÓDIGO" => $strCode,
         "TIPO" => $strType,
         "VALOR" => $strValue,
      ));

      // Validar que el descuento exista
      $requestDiscount = $this->model->select_discount_by_id($intId);
      if (!$requestDiscount) {
         registerLog("Ocurrió un error inesperado", "El descuento que intentas actualizar no existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El descuento que intentas actualizar no existe en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de código (máximo 50 caracteres)
      if (strlen($strCode) > 50) {
         registerLog("Ocurrió un error inesperado", "El campo 'Código' excede el límite de 50 caracteres.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Código' no puede exceder 50 caracteres.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar formato del código (solo letras, números y guiones)
      if (!preg_match('/^[A-Z0-9\-]+$/', $strCode)) {
         registerLog("Ocurrió un error inesperado", "El campo 'Código' contiene caracteres inválidos.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Código' solo puede contener letras mayúsculas, números y guiones.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de tipo de descuento
      if (!in_array($strType, ['percentage', 'fixed'])) {
         registerLog("Ocurrió un error inesperado", "El campo 'Tipo' tiene un valor inválido.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Tipo' tiene un valor inválido.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de formato de valor (debe ser numérico)
      if (!is_numeric($strValue) || floatval($strValue) < 0) {
         registerLog("Ocurrió un error inesperado", "El campo 'Valor' debe ser un número válido mayor o igual a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El campo 'Valor' debe ser un número válido mayor o igual a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación específica según el tipo
      $floatValue = floatval($strValue);
      if ($strType == "percentage" && ($floatValue < 0 || $floatValue > 100)) {
         registerLog("Ocurrió un error inesperado", "El porcentaje debe estar entre 0 y 100.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El porcentaje debe estar entre 0 y 100.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de fechas si se proporcionan
      $startDateFormatted = null;
      $endDateFormatted = null;
      if (!empty($strStartDate)) {
         $startDateFormatted = date('Y-m-d H:i:s', strtotime($strStartDate));
         if ($startDateFormatted === false) {
            registerLog("Ocurrió un error inesperado", "El campo 'Fecha de Inicio' no tiene un formato válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El campo 'Fecha de Inicio' no tiene un formato válido.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      if (!empty($strEndDate)) {
         $endDateFormatted = date('Y-m-d H:i:s', strtotime($strEndDate));
         if ($endDateFormatted === false) {
            registerLog("Ocurrió un error inesperado", "El campo 'Fecha de Fin' no tiene un formato válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El campo 'Fecha de Fin' no tiene un formato válido.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      // Validar que la fecha de inicio sea anterior o igual a la fecha de fin
      if (!empty($strStartDate) && !empty($strEndDate) && strtotime($strStartDate) > strtotime($strEndDate)) {
         registerLog("Ocurrió un error inesperado", "La fecha de inicio debe ser anterior o igual a la fecha de fin.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "La fecha de inicio debe ser anterior o igual a la fecha de fin.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validación de máximo de usos si se proporciona
      if ($intMaxUses !== null && $intMaxUses < 1) {
         registerLog("Ocurrió un error inesperado", "El máximo de usos debe ser mayor a 0.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El máximo de usos debe ser mayor a 0.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Validar que el código no esté duplicado (excepto el actual)
      $requestForCode = $this->model->select_discount_by_code($strCode);
      if ($requestForCode) {
         if ($requestForCode['idDiscount'] != $intId) {
            registerLog("Ocurrió un error inesperado", "El código del descuento ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
               "title" => "Ocurrió un error inesperado",
               "message" => "El código del descuento ya existe. Por favor, ingrese un código diferente.",
               "type" => "error",
               "status" => false
            );
            toJson($data);
         }
      }

      // Convertir código a mayúsculas
      $strCode = strtoupper($strCode);

      // Actualizar en la base de datos
      $request = $this->model->update_discount($intId, $strCode, $strType, $floatValue, $startDateFormatted, $endDateFormatted, $intAppliesToPlanId, $intMaxUses, $intIsRecurring);

      if ($request) {
         registerLog("Actualización exitosa", "Se actualizó el descuento con ID: " . $intId, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Actualización exitosa",
            "message" => "El descuento se ha actualizado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar actualizar el descuento con ID: " . $intId, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo actualizar el descuento. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }

   /**
    * Elimina un descuento del sistema
    *
    * @return void
    */
   public function deleteDiscount()
   {
      permissionInterface(21);

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

      $intId = isset($request["idDiscount"]) ? (int)$request["idDiscount"] : 0;

      validateFieldsEmpty(array(
         "ID DEL DESCUENTO" => $intId,
      ));

      // Validar que el descuento exista
      $requestDiscount = $this->model->select_discount_by_id($intId);
      if (!$requestDiscount) {
         registerLog("Ocurrió un error inesperado", "El descuento que intentas eliminar no existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "El descuento que intentas eliminar no existe en el sistema.",
            "type" => "error",
            "status" => false
         );
         toJson($data);
      }

      // Eliminar de la base de datos
      $request = $this->model->delete_discount($intId);

      if ($request) {
         registerLog("Eliminación exitosa", "Se eliminó el descuento con ID: " . $intId, 2, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Eliminación exitosa",
            "message" => "El descuento se ha eliminado correctamente.",
            "type" => "success",
            "status" => true
         );
      } else {
         registerLog("Ocurrió un error inesperado", "Error al intentar eliminar el descuento con ID: " . $intId, 1, $_SESSION['login_info']['idUser']);
         $data = array(
            "title" => "Ocurrió un error inesperado",
            "message" => "No se pudo eliminar el descuento. Por favor, intenta nuevamente.",
            "type" => "error",
            "status" => false
         );
      }
      toJson($data);
   }
}
