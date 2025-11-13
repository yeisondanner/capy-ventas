<?php

/**
 * Controlador para la gestión de negocios
 * 
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de negocios del sistema.
 */
class Business extends Controllers
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
     * Muestra la vista principal de gestión de negocios
     * 
     * @return void
     */
    public function business()
    {
        $data = [
            'page_id'          => 17,
            'page_title'       => 'Negocios',
            'page_description' => 'Gestiona los negocios del sistema.',
            'page_container'   => 'Business',
            'page_view'        => 'business',
            'page_js_css'      => 'business',
            'page_vars'        => ['permission_data', 'login', 'login_info'],
        ];

        permissionInterface($data['page_id']);

        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        $payload = [
            'event'      => 'page_view',
            'page'       => $data['page_title'],
            'page_id'    => $data['page_id'],
            'container'  => $data['page_container'],
            'user_id'    => $userId,
            'ip'         => $ip,
            'method'     => $method,
            'url'        => $url,
            'user_agent' => $userAgent,
            'timestamp'  => date('c'),
        ];

        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        $this->views->getView($this, 'business', $data);
    }

    /**
     * Obtiene la lista de todos los negocios para mostrar en la tabla
     * 
     * @return void
     */
    public function getBusinesses()
    {
        permissionInterface(17);
        $arrData = $this->model->select_businesses();
        $cont = 1;
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;

            // Formatear estado
            if ($value["status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }

            // Formatear teléfono completo
            $arrData[$key]["phone_full"] = $value["telephone_prefix"] . " " . $value["phone_number"];

            // Formatear dirección (si está vacía, mostrar guión)
            $arrData[$key]["direction_display"] = !empty($value["direction"]) ? $value["direction"] : "-";

            // Formatear ciudad (si está vacía, mostrar guión)
            $arrData[$key]["city_display"] = !empty($value["city"]) ? $value["city"] : "-";

            // Formatear país (si está vacía, mostrar guión)
            $arrData[$key]["country_display"] = !empty($value["country"]) ? $value["country"] : "-";

            // Formatear tipo de negocio (desencriptar si es necesario)
            $businessTypeName = $value["business_type_name"] ?? "Sin tipo";
            $arrData[$key]["business_type_display"] = $businessTypeName;

            // Formatear usuario de aplicación (desencriptar)
            $userAppName = "Sin usuario";
            if (!empty($value["user_app_name"])) {
                $userAppName = decryption($value["user_app_name"]);
            }
            $arrData[$key]["user_app_display"] = $userAppName;

            // Formatear fechas de registro y actualización
            $arrData[$key]["registration_date_formatted"] = dateFormat($value["registration_date"]);
            $arrData[$key]["update_date_formatted"] = dateFormat($value["update_date"]);

            // Botones de acción
            $arrData[$key]["actions"] = '
                <div class="btn-group">
                    <button class="btn btn-success update-item" type="button"
                        data-id="' . $value["idBusiness"] . '"
                        data-typebusiness-id="' . $value["typebusiness_id"] . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-direction="' . htmlspecialchars($value["direction"] ?? "") . '"
                        data-city="' . htmlspecialchars($value["city"] ?? "") . '"
                        data-document-number="' . htmlspecialchars($value["document_number"]) . '"
                        data-phone-number="' . htmlspecialchars($value["phone_number"]) . '"
                        data-country="' . htmlspecialchars($value["country"] ?? "") . '"
                        data-telephone-prefix="' . htmlspecialchars($value["telephone_prefix"]) . '"
                        data-email="' . htmlspecialchars($value["email"]) . '"
                        data-status="' . $value["status"] . '"
                        data-userapp-id="' . $value["userapp_id"] . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                    ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-info report-item" type="button"
                        data-id="' . $value["idBusiness"] . '"
                        data-typebusiness-id="' . $value["typebusiness_id"] . '"
                        data-typebusiness-name="' . htmlspecialchars($businessTypeName) . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-direction="' . htmlspecialchars($value["direction"] ?? "") . '"
                        data-city="' . htmlspecialchars($value["city"] ?? "") . '"
                        data-document-number="' . htmlspecialchars($value["document_number"]) . '"
                        data-phone-number="' . htmlspecialchars($value["phone_number"]) . '"
                        data-country="' . htmlspecialchars($value["country"] ?? "") . '"
                        data-telephone-prefix="' . htmlspecialchars($value["telephone_prefix"]) . '"
                        data-email="' . htmlspecialchars($value["email"]) . '"
                        data-status="' . $value["status"] . '"
                        data-userapp-id="' . $value["userapp_id"] . '"
                        data-userapp-name="' . htmlspecialchars($userAppName) . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                    ><i class="fa fa-info-circle"></i></button>
                    <button class="btn btn-danger delete-item" 
                        data-id="' . $value["idBusiness"] . '" 
                        data-name="' . htmlspecialchars($value["name"]) . '"
                    ><i class="fa fa-remove"></i></button>
                </div>
            ';
            $cont++;
        }
        echo json_encode($arrData);
    }

    /**
     * Obtiene los tipos de negocio activos para usar en select
     * 
     * @return void
     */
    public function getBusinessTypesSelect()
    {
        permissionInterface(17);
        $arrData = $this->model->select_business_types_active();
        echo json_encode($arrData);
    }

    /**
     * Obtiene los usuarios de aplicación activos para usar en select
     * 
     * @return void
     */
    public function getUserAppsSelect()
    {
        permissionInterface(17);
        $arrData = $this->model->select_user_apps_active();
        // Desencriptar usuarios para mostrar en el select
        foreach ($arrData as $key => $value) {
            if (!empty($value["user"])) {
                $arrData[$key]["user"] = decryption($value["user"]);
            }
            if (!empty($value["email"])) {
                $arrData[$key]["email"] = (decryption($value["email"]));
            }
            // Crear nombre completo para mostrar
            $arrData[$key]["full_name"] = $value["names"] . " " . $value["lastname"];
        }
        toJson($arrData);
    }

    /**
     * Registra un nuevo negocio en el sistema
     * 
     * @return void
     */
    public function setBusiness()
    {
        permissionInterface(17);

        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar un negocio", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        isCsrf();

        validateFields(["slctTypeBusiness", "txtName", "txtDocumentNumber", "txtPhoneNumber", "txtTelephonePrefix", "txtEmail", "slctUserApp"]);

        $intTypeBusinessId = strClean($_POST["slctTypeBusiness"]);
        $strName = strClean($_POST["txtName"]);
        $strDirection = isset($_POST["txtDirection"]) ? strClean($_POST["txtDirection"]) : "";
        $strCity = isset($_POST["txtCity"]) ? strClean($_POST["txtCity"]) : "";
        $strDocumentNumber = strClean($_POST["txtDocumentNumber"]);
        $strPhoneNumber = strClean($_POST["txtPhoneNumber"]);
        $strCountry = isset($_POST["txtCountry"]) ? strClean($_POST["txtCountry"]) : "";
        $strTelephonePrefix = strClean($_POST["txtTelephonePrefix"]);
        $strEmail = strClean($_POST["txtEmail"]);
        $intUserAppId = strClean($_POST["slctUserApp"]);

        validateFieldsEmpty(array(
            "TIPO DE NEGOCIO" => $intTypeBusinessId,
            "NOMBRE" => $strName,
            "NÚMERO DE DOCUMENTO" => $strDocumentNumber,
            "NÚMERO DE TELÉFONO" => $strPhoneNumber,
            "PREFIJO TELEFÓNICO" => $strTelephonePrefix,
            "CORREO ELECTRÓNICO" => $strEmail,
            "USUARIO DE APLICACIÓN" => $intUserAppId,
        ));

        // Validación que el tipo de negocio sea numérico
        if (!is_numeric($intTypeBusinessId)) {
            registerLog("Ocurrió un error inesperado", "El tipo de negocio debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tipo de negocio debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación que el usuario de aplicación sea numérico
        if (!is_numeric($intUserAppId)) {
            registerLog("Ocurrió un error inesperado", "El usuario de aplicación debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El usuario de aplicación debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación de formato de nombre
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $strName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre' no cumple con el formato requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación de formato de email
        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Correo electrónico' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación de formato de número de documento (solo números, 11 caracteres)
        if (!preg_match('/^\d{11}$/', $strDocumentNumber)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Número de documento' debe contener exactamente 11 números.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Número de documento' debe contener exactamente 11 números.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación de formato de número de teléfono (solo números)
        if (!preg_match('/^\d+$/', $strPhoneNumber)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Número de teléfono' debe contener solo números.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Número de teléfono' debe contener solo números.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validar que el número de documento no exista
        $request = $this->model->select_business_by_document($strDocumentNumber);
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El número de documento ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El número de documento ingresado ya se encuentra registrado en el sistema.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        unset($request);

        // Validar que el email no exista
        $request = $this->model->select_business_by_email($strEmail);
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El correo electrónico ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El correo electrónico ingresado ya se encuentra registrado en el sistema.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        unset($request);

        // Validar que el teléfono no exista
        $request = $this->model->select_business_by_phone($strPhoneNumber);
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El número de teléfono ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El número de teléfono ingresado ya se encuentra registrado en el sistema.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        unset($request);

        // Convertir nombre y país a mayúsculas
        $strName = strtoupper($strName);
        if (!empty($strCountry)) {
            $strCountry = strtoupper($strCountry);
        }
        if (!empty($strCity)) {
            $strCity = strtoupper($strCity);
        }

        // Insertar en la base de datos
        $direction = !empty($strDirection) ? $strDirection : null;
        $city = !empty($strCity) ? $strCity : null;
        $country = !empty($strCountry) ? $strCountry : null;
        $request = $this->model->insert_business($intTypeBusinessId, $strName, $direction, $city, $strDocumentNumber, $strPhoneNumber, $country, $strTelephonePrefix, $strEmail, $intUserAppId);

        if ($request > 0) {
            registerLog("Registro exitoso", "El negocio ha sido registrado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El negocio fue registrado satisfactoriamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar el registro del negocio.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El negocio no se ha registrado correctamente en el sistema",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Actualiza los datos de un negocio existente
     * 
     * @return void
     */
    public function updateBusiness()
    {
        permissionInterface(17);

        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de actualizar un negocio", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        isCsrf();

        validateFields(["update_txtId", "update_slctTypeBusiness", "update_txtName", "update_txtDocumentNumber", "update_txtPhoneNumber", "update_txtTelephonePrefix", "update_txtEmail", "update_slctStatus", "update_slctUserApp"]);

        $intId = strClean($_POST["update_txtId"]);
        $intTypeBusinessId = strClean($_POST["update_slctTypeBusiness"]);
        $strName = strClean($_POST["update_txtName"]);
        $strDirection = isset($_POST["update_txtDirection"]) ? strClean($_POST["update_txtDirection"]) : "";
        $strCity = isset($_POST["update_txtCity"]) ? strClean($_POST["update_txtCity"]) : "";
        $strDocumentNumber = strClean($_POST["update_txtDocumentNumber"]);
        $strPhoneNumber = strClean($_POST["update_txtPhoneNumber"]);
        $strCountry = isset($_POST["update_txtCountry"]) ? strClean($_POST["update_txtCountry"]) : "";
        $strTelephonePrefix = strClean($_POST["update_txtTelephonePrefix"]);
        $strEmail = strClean($_POST["update_txtEmail"]);
        $slctStatus = strClean($_POST["update_slctStatus"]);
        $intUserAppId = strClean($_POST["update_slctUserApp"]);

        validateFieldsEmpty(array(
            "ID" => $intId,
            "TIPO DE NEGOCIO" => $intTypeBusinessId,
            "NOMBRE" => $strName,
            "NÚMERO DE DOCUMENTO" => $strDocumentNumber,
            "NÚMERO DE TELÉFONO" => $strPhoneNumber,
            "PREFIJO TELEFÓNICO" => $strTelephonePrefix,
            "CORREO ELECTRÓNICO" => $strEmail,
            "ESTADO" => $slctStatus,
            "USUARIO DE APLICACIÓN" => $intUserAppId,
        ));

        // Validación que el ID sea numérico
        if (!is_numeric($intId)) {
            registerLog("Ocurrió un error inesperado", "El ID debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación que el tipo de negocio sea numérico
        if (!is_numeric($intTypeBusinessId)) {
            registerLog("Ocurrió un error inesperado", "El tipo de negocio debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tipo de negocio debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación que el usuario de aplicación sea numérico
        if (!is_numeric($intUserAppId)) {
            registerLog("Ocurrió un error inesperado", "El usuario de aplicación debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El usuario de aplicación debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validaciones de formato (similar a setBusiness)
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $strName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre' no cumple con el formato requerido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre' no presenta un formato válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Correo electrónico' no cumple con el formato requerido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (!preg_match('/^\d{11}$/', $strDocumentNumber)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Número de documento' debe contener exactamente 11 números.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Número de documento' debe contener exactamente 11 números.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (!preg_match('/^\d+$/', $strPhoneNumber)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Número de teléfono' debe contener solo números.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Número de teléfono' debe contener solo números.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validar que el negocio exista
        $result = $this->model->select_business_by_id($intId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede actualizar el negocio, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del negocio no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validar que el número de documento no esté duplicado (excepto el actual)
        $requestForDocument = $this->model->select_business_by_document($strDocumentNumber);
        if ($requestForDocument) {
            if ($requestForDocument['idBusiness'] != $intId) {
                registerLog("Ocurrió un error inesperado", "El número de documento ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El número de documento ya existe. Por favor, ingrese un número diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Validar que el email no esté duplicado (excepto el actual)
        $requestForEmail = $this->model->select_business_by_email($strEmail);
        if ($requestForEmail) {
            if ($requestForEmail['idBusiness'] != $intId) {
                registerLog("Ocurrió un error inesperado", "El correo electrónico ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El correo electrónico ya existe. Por favor, ingrese un correo diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Validar que el teléfono no esté duplicado (excepto el actual)
        $requestForPhone = $this->model->select_business_by_phone($strPhoneNumber);
        if ($requestForPhone) {
            if ($requestForPhone['idBusiness'] != $intId) {
                registerLog("Ocurrió un error inesperado", "El número de teléfono ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El número de teléfono ya existe. Por favor, ingrese un número diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Convertir a mayúsculas
        $strName = strtoupper($strName);
        if (!empty($strCountry)) {
            $strCountry = strtoupper($strCountry);
        }
        if (!empty($strCity)) {
            $strCity = strtoupper($strCity);
        }

        // Actualizar en la base de datos
        $direction = !empty($strDirection) ? $strDirection : null;
        $city = !empty($strCity) ? $strCity : null;
        $country = !empty($strCountry) ? $strCountry : null;
        $request = $this->model->update_business($intId, $intTypeBusinessId, $strName, $direction, $city, $strDocumentNumber, $strPhoneNumber, $country, $strTelephonePrefix, $strEmail, $slctStatus, $intUserAppId);

        if ($request) {
            registerLog("Registro exitoso", "El negocio se ha actualizado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El negocio ha sido actualizado correctamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar la actualización del negocio.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la actualización del negocio.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Elimina un negocio del sistema
     * 
     * @return void
     */
    public function deleteBusiness()
    {
        permissionInterface(17);

        if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
            registerLog("Ocurrió un error inesperado", "No se encontró el método DELETE durante el intento de eliminar un negocio.", 1, $_SESSION['login_info']['idUser']);
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

        $id = strClean($request["id"]);
        $name = strClean($request["name"]);

        if ($id == "") {
            registerLog("Ocurrió un error inesperado", "El ID del negocio es obligatorio para completar el proceso de eliminación.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del negocio es requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El ID del negocio debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del negocio debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        $result = $this->model->select_business_by_id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede eliminar el negocio, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del negocio no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        $request = $this->model->delete_business($id);
        if ($request) {
            registerLog("Eliminación exitosa", "El negocio con ID {$id} y nombre {$name} fue eliminado satisfactoriamente del sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Eliminación exitosa",
                "message" => "El negocio con ID '{$id}' y nombre '{$name}' ha sido eliminado correctamente del sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No fue posible eliminar el negocio con ID '{$id}' y nombre '{$name}'.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la eliminación del negocio con ID '{$id}' y nombre '{$name}'.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
