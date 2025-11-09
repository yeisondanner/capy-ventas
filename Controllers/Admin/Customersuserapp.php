<?php

/**
 * Controlador para la gestión de clientes de la aplicación
 * 
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de personas (clientes) que tendrán acceso a la aplicación móvil.
 */
class Customersuserapp extends Controllers
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
     * Muestra la vista principal de gestión de clientes
     * 
     * @return void
     */
    public function customersuserapp()
    {
        $data = [
            'page_id'          => 15,
            'page_title'       => 'Clientes App',
            'page_description' => 'Gestiona los clientes para el acceso a la App.',
            'page_container'   => 'Customersuserapp',
            'page_view'        => 'customersuserapp',
            'page_js_css'      => 'customersuserapp',
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

        $this->views->getView($this, 'customersuserapp', $data);
    }

    /**
     * Obtiene la lista de todas las personas para mostrar en la tabla
     * 
     * @return void
     */
    public function getPeople()
    {
        permissionInterface(15);
        $arrData = $this->model->select_people_with_users();
        $cont = 1;
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;
            
            // Formatear nombre completo
            $fullName = $value["names"] . " " . $value["lastname"];
            $arrData[$key]["full_name"] = $fullName;
            
            // Formatear estado
            if ($value["status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }
            
            // Formatear fecha de nacimiento
            $arrData[$key]["date_of_birth_formatted"] = date("d/m/Y", strtotime($value["date_of_birth"]));
            
            // Formatear teléfono completo
            $arrData[$key]["phone_full"] = $value["telephone_prefix"] . " " . $value["phone_number"];
            
            // Formatear fechas de registro y actualización
            $arrData[$key]["registration_date_formatted"] = dateFormat($value["registration_date"]);
            $arrData[$key]["update_date_formatted"] = dateFormat($value["update_date"]);
            
            // Información del usuario de la app
            $userApp = $this->model->select_user_app_by_people_id($value["idPeople"]);
            $userName = $userApp ? decryption($userApp["user"]) : "Sin usuario";
            $userIdApp = $userApp ? $userApp["idUserApp"] : "";
            $userStatus = $userApp ? $userApp["status"] : "";
            $userPassword = $userApp && !empty($userApp["password"]) ? decryption($userApp["password"]) : "";
            
            $arrData[$key]["user_app"] = $userName;
            $arrData[$key]["has_user"] = $userApp ? true : false;
            
            // Botones de acción
            $arrData[$key]["actions"] = '
                <div class="btn-group">
                    <button class="btn btn-success update-item" type="button"
                        data-id="' . $value["idPeople"] . '"
                        data-names="' . htmlspecialchars($value["names"]) . '"
                        data-lastname="' . htmlspecialchars($value["lastname"]) . '"
                        data-email="' . htmlspecialchars($value["email"]) . '"
                        data-date-of-birth="' . $value["date_of_birth"] . '"
                        data-country="' . htmlspecialchars($value["country"]) . '"
                        data-telephone-prefix="' . htmlspecialchars($value["telephone_prefix"]) . '"
                        data-phone-number="' . htmlspecialchars($value["phone_number"]) . '"
                        data-status="' . $value["status"] . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                        data-user-app-id="' . $userIdApp . '"
                        data-user="' . htmlspecialchars($userName) . '"
                        data-user-password="' . htmlspecialchars($userPassword) . '"
                        data-user-status="' . $userStatus . '"
                        data-has-user="' . ($userApp ? "1" : "0") . '"
                    ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-info report-item" type="button"
                        data-id="' . $value["idPeople"] . '"
                        data-names="' . htmlspecialchars($value["names"]) . '"
                        data-lastname="' . htmlspecialchars($value["lastname"]) . '"
                        data-email="' . htmlspecialchars($value["email"]) . '"
                        data-date-of-birth="' . $arrData[$key]["date_of_birth_formatted"] . '"
                        data-country="' . htmlspecialchars($value["country"]) . '"
                        data-telephone-prefix="' . htmlspecialchars($value["telephone_prefix"]) . '"
                        data-phone-number="' . htmlspecialchars($value["phone_number"]) . '"
                        data-status="' . $value["status"] . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                        data-user="' . htmlspecialchars($userName) . '"
                        data-user-password="' . htmlspecialchars($userPassword) . '"
                        data-user-status="' . $userStatus . '"
                        data-has-user="' . ($userApp ? "1" : "0") . '"
                    ><i class="fa fa-user"></i></button>
                    <button class="btn btn-danger delete-item" 
                        data-id="' . $value["idPeople"] . '" 
                        data-fullname="' . htmlspecialchars($fullName) . '"
                        data-user-app-id="' . $userIdApp . '"
                    ><i class="fa fa-remove"></i></button>
                </div>
            ';
            $cont++;
        }
        echo json_encode($arrData);
    }

    /**
     * Registra una nueva persona en el sistema
     * 
     * @return void
     */
    public function setPeople()
    {
        permissionInterface(15);

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

        validateFields(["txtNames", "txtLastname", "txtEmail", "txtDateOfBirth", "txtCountry", "txtTelephonePrefix", "txtPhoneNumber", "slctStatus"]);

        $strNames = strClean($_POST["txtNames"]);
        $strLastname = strClean($_POST["txtLastname"]);
        $strEmail = strClean($_POST["txtEmail"]);
        $strDateOfBirth = strClean($_POST["txtDateOfBirth"]);
        $strCountry = strClean($_POST["txtCountry"]);
        $strTelephonePrefix = strClean($_POST["txtTelephonePrefix"]);
        $strPhoneNumber = strClean($_POST["txtPhoneNumber"]);
        $slctStatus = strClean($_POST["slctStatus"]);
        
        // Campos opcionales de usuario
        $strUser = isset($_POST["txtUser"]) ? strClean($_POST["txtUser"]) : "";
        $strPassword = isset($_POST["txtPassword"]) ? strClean($_POST["txtPassword"]) : "";
        $slctUserStatus = isset($_POST["slctUserStatus"]) ? strClean($_POST["slctUserStatus"]) : "Activo";

        validateFieldsEmpty(array(
            "NOMBRES" => $strNames,
            "APELLIDOS" => $strLastname,
            "CORREO ELECTRÓNICO" => $strEmail,
            "FECHA DE NACIMIENTO" => $strDateOfBirth,
            "PAÍS" => $strCountry,
            "PREFIJO TELEFÓNICO" => $strTelephonePrefix,
            "NÚMERO DE TELÉFONO" => $strPhoneNumber,
            "ESTADO" => $slctStatus
        ));

        // Validación de formato de nombres
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strNames)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombres' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombres' no cumple con el formato de texto requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación de formato de apellidos
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strLastname)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Apellidos' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Apellidos' no cumple con el formato de texto requerido.",
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

        // Validación de formato de fecha
        $dateParts = explode('-', $strDateOfBirth);
        if (count($dateParts) != 3 || !checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
            registerLog("Ocurrió un error inesperado", "El campo 'Fecha de nacimiento' no tiene un formato válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Fecha de nacimiento' no tiene un formato válido (YYYY-MM-DD).",
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

        // Validar que el email no exista
        $request = $this->model->select_people_by_email($strEmail);
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
        $request = $this->model->select_people_by_phone($strPhoneNumber);
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

        // Validar usuario si se proporciona
        if (!empty($strUser)) {
            // Validación de formato de usuario
            if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
                registerLog("Ocurrió un error inesperado", "El campo 'Usuario' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo 'Usuario' no cumple con el formato requerido. Debe tener entre 3 y 15 caracteres alfanuméricos.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }

            // Validar que el usuario no exista (encriptamos para buscar en BD)
            $strUserEncrypted = encryption($strUser);
            $request = $this->model->select_user_app_by_user($strUserEncrypted);
            if ($request) {
                registerLog("Ocurrió un error inesperado", "El nombre de usuario ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El nombre de usuario ingresado ya se encuentra registrado en el sistema.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            unset($request);

            // Validar contraseña si se proporciona usuario
            if (empty($strPassword)) {
                registerLog("Ocurrió un error inesperado", "La contraseña es obligatoria cuando se proporciona un usuario.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La contraseña es obligatoria cuando se proporciona un usuario.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }

            // Validación que la contraseña tenga mínimo 8 caracteres
            if (strlen($strPassword) < 8) {
                registerLog("Ocurrió un error inesperado", "La contraseña debe contener como mínimo 8 caracteres.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La contraseña debe tener una longitud mínima de 8 caracteres.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Convertir nombres y apellidos a mayúsculas
        $strNames = strtoupper($strNames);
        $strLastname = strtoupper($strLastname);
        $strCountry = strtoupper($strCountry);

        // Insertar en la base de datos
        $request = $this->model->insert_people($strNames, $strLastname, $strEmail, $strDateOfBirth, $strCountry, $strTelephonePrefix, $strPhoneNumber, $slctStatus);

        if ($request > 0) {
            // Si se proporcionó usuario, crear el registro en user_app
            if (!empty($strUser) && !empty($strPassword)) {
                $strUserEncrypted = encryption($strUser);
                $strPasswordEncrypted = encryption($strPassword);
                $requestUser = $this->model->insert_user_app($strUserEncrypted, $strPasswordEncrypted, $slctUserStatus, $request);
                
                if (!$requestUser) {
                    registerLog("Atención", "La persona fue registrada pero no se pudo crear el usuario de la aplicación.", 3, $_SESSION['login_info']['idUser']);
                    $data = array(
                        "title" => "Registro parcial",
                        "message" => "La persona fue registrada correctamente, pero no se pudo crear el usuario de la aplicación. Puede crearlo posteriormente.",
                        "type" => "warning",
                        "status" => true
                    );
                    toJson($data);
                }
            }

            registerLog("Registro exitoso", "La persona ha sido registrada correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "La persona fue registrada satisfactoriamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar el registro de la persona.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La persona no se ha registrado correctamente en el sistema",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Actualiza los datos de una persona existente
     * 
     * @return void
     */
    public function updatePeople()
    {
        permissionInterface(15);

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

        validateFields(["update_txtId", "update_txtNames", "update_txtLastname", "update_txtEmail", "update_txtDateOfBirth", "update_txtCountry", "update_txtTelephonePrefix", "update_txtPhoneNumber", "update_slctStatus"]);

        $intId = strClean($_POST["update_txtId"]);
        $strNames = strClean($_POST["update_txtNames"]);
        $strLastname = strClean($_POST["update_txtLastname"]);
        $strEmail = strClean($_POST["update_txtEmail"]);
        $strDateOfBirth = strClean($_POST["update_txtDateOfBirth"]);
        $strCountry = strClean($_POST["update_txtCountry"]);
        $strTelephonePrefix = strClean($_POST["update_txtTelephonePrefix"]);
        $strPhoneNumber = strClean($_POST["update_txtPhoneNumber"]);
        $slctStatus = strClean($_POST["update_slctStatus"]);
        
        // Campos opcionales de usuario
        $intUserAppId = isset($_POST["update_txtUserAppId"]) ? strClean($_POST["update_txtUserAppId"]) : "";
        $strUser = isset($_POST["update_txtUser"]) ? strClean($_POST["update_txtUser"]) : "";
        $strPassword = isset($_POST["update_txtPassword"]) ? strClean($_POST["update_txtPassword"]) : "";
        $slctUserStatus = isset($_POST["update_slctUserStatus"]) ? strClean($_POST["update_slctUserStatus"]) : "Activo";

        validateFieldsEmpty(array(
            "ID" => $intId,
            "NOMBRES" => $strNames,
            "APELLIDOS" => $strLastname,
            "CORREO ELECTRÓNICO" => $strEmail,
            "FECHA DE NACIMIENTO" => $strDateOfBirth,
            "PAÍS" => $strCountry,
            "PREFIJO TELEFÓNICO" => $strTelephonePrefix,
            "NÚMERO DE TELÉFONO" => $strPhoneNumber,
            "ESTADO" => $slctStatus
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

        // Validación de formatos (similar a setPeople)
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strNames)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombres' no cumple con el formato requerido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombres' no presenta un formato de texto válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strLastname)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Apellidos' no cumple con el formato requerido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Apellidos' no presenta un formato de texto válido.",
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

        // Validar que la persona exista
        $result = $this->model->select_people_by_id($intId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede actualizar la persona, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID de la persona no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validar que el email no esté duplicado (excepto el actual)
        $requestForEmail = $this->model->select_people_by_email($strEmail);
        if ($requestForEmail) {
            if ($requestForEmail['idPeople'] != $intId) {
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
        $requestForPhone = $this->model->select_people_by_phone($strPhoneNumber);
        if ($requestForPhone) {
            if ($requestForPhone['idPeople'] != $intId) {
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

        // Validar usuario si se proporciona
        if (!empty($strUser)) {
            // Validación de formato de usuario
            if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
                registerLog("Ocurrió un error inesperado", "El campo 'Usuario' presenta un formato inválido.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo 'Usuario' no cumple con el formato requerido.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }

            // Validar que el usuario no esté duplicado (excepto el actual)
            // Primero encriptamos el usuario para buscar en la BD
            $strUserEncrypted = encryption($strUser);
            $requestForUser = $this->model->select_user_app_by_user($strUserEncrypted);
            if ($requestForUser) {
                if ($requestForUser['idUserApp'] != $intUserAppId) {
                    registerLog("Ocurrió un error inesperado", "El nombre de usuario ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
                    $data = array(
                        "title" => "Ocurrió un error inesperado",
                        "message" => "El nombre de usuario ya existe. Por favor, ingrese un usuario diferente.",
                        "type" => "error",
                        "status" => false
                    );
                    toJson($data);
                }
            }

            // Si se proporciona contraseña, validar longitud
            if (!empty($strPassword) && strlen($strPassword) < 8) {
                registerLog("Ocurrió un error inesperado", "La contraseña debe tener al menos 8 caracteres.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La contraseña debe contener al menos 8 caracteres.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Convertir a mayúsculas
        $strNames = strtoupper($strNames);
        $strLastname = strtoupper($strLastname);
        $strCountry = strtoupper($strCountry);

        // Actualizar en la base de datos
        $request = $this->model->update_people($intId, $strNames, $strLastname, $strEmail, $strDateOfBirth, $strCountry, $strTelephonePrefix, $strPhoneNumber, $slctStatus);

        if ($request) {
            // Manejar usuario de la aplicación
            if (!empty($strUser)) {
                // Encriptar usuario para guardar en BD
                $strUserEncrypted = encryption($strUser);
                
                // Si existe ID de usuario, actualizar
                if (!empty($intUserAppId) && is_numeric($intUserAppId)) {
                    // Si se proporciona contraseña, encriptarla; si no, mantener la actual (null)
                    $strPasswordEncrypted = !empty($strPassword) ? encryption($strPassword) : null;
                    $requestUser = $this->model->update_user_app($intUserAppId, $strUserEncrypted, $strPasswordEncrypted, $slctUserStatus);
                    
                    if (!$requestUser) {
                        registerLog("Atención", "La persona fue actualizada pero no se pudo actualizar el usuario de la aplicación.", 3, $_SESSION['login_info']['idUser']);
                    }
                } else {
                    // Si no existe, crear nuevo usuario
                    // Si no se proporciona contraseña, generar una aleatoria
                    $strPasswordEncrypted = !empty($strPassword) ? encryption($strPassword) : encryption(passGenerator(10));
                    $requestUser = $this->model->insert_user_app($strUserEncrypted, $strPasswordEncrypted, $slctUserStatus, $intId);
                    
                    if (!$requestUser) {
                        registerLog("Atención", "La persona fue actualizada pero no se pudo crear el usuario de la aplicación.", 3, $_SESSION['login_info']['idUser']);
                    }
                }
            }

            registerLog("Registro exitoso", "La persona se ha actualizado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "La persona ha sido actualizada correctamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar la actualización de la persona.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar el registro de la persona.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Elimina una persona del sistema
     * 
     * @return void
     */
    public function deletePeople()
    {
        permissionInterface(15);

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

        $id = strClean($request["id"]);
        $fullName = strClean($request["fullname"]);
        $userAppId = isset($request["user_app_id"]) ? strClean($request["user_app_id"]) : "";

        if ($id == "") {
            registerLog("Ocurrió un error inesperado", "El ID de la persona es obligatorio para completar el proceso de eliminación.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID de la persona es requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El ID de la persona debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID de la persona debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        $result = $this->model->select_people_by_id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede eliminar la persona, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID de la persona no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Eliminar usuario de la aplicación si existe
        if (!empty($userAppId) && is_numeric($userAppId)) {
            $this->model->delete_user_app($userAppId);
        }

        $request = $this->model->delete_people($id);
        if ($request) {
            registerLog("Eliminación exitosa", "La persona con ID {$id} y nombre {$fullName} fue eliminada satisfactoriamente del sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Eliminación exitosa",
                "message" => "La persona con ID '{$id}' y nombre '{$fullName}' ha sido eliminada correctamente del sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No fue posible eliminar a la persona con ID '{$id}' y nombre '{$fullName}'.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la eliminación de la persona con ID '{$id}' y nombre '{$fullName}'.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
