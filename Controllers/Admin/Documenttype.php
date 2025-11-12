<?php

/**
 * Controlador para la gestión de tipos de documento
 * 
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de tipos de documento del sistema.
 */
class DocumentType extends Controllers
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
     * Muestra la vista principal de gestión de tipos de documento
     * 
     * @return void
     */
    public function documenttype()
    {
        $data = [
            'page_id'          => 18,
            'page_title'       => 'Tipos de Documento',
            'page_description' => 'Gestiona los tipos de documento del sistema.',
            'page_container'   => 'DocumentType',
            'page_view'        => 'documenttype',
            'page_js_css'      => 'documenttype',
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

        $this->views->getView($this, 'documenttype', $data);
    }

    /**
     * Obtiene la lista de todos los tipos de documento para mostrar en la tabla
     * 
     * @return void
     */
    public function getDocumentTypes()
    {
        permissionInterface(18);
        $arrData = $this->model->select_document_types();
        $cont = 1;
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;
            
            // Formatear estado
            if ($value["status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }
            
            // Formatear descripción (si está vacía, mostrar guión)
            $arrData[$key]["description_display"] = !empty($value["description"]) ? $value["description"] : "-";
            
            // Formatear fechas de registro y actualización
            $arrData[$key]["registration_date_formatted"] = dateFormat($value["registration_date"]);
            $arrData[$key]["update_date_formatted"] = dateFormat($value["update_date"]);
            
            // Botones de acción
            $arrData[$key]["actions"] = '
                <div class="btn-group">
                    <button class="btn btn-success update-item" type="button"
                        data-id="' . $value["idDocumentType"] . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-description="' . htmlspecialchars($value["description"] ?? "") . '"
                        data-status="' . $value["status"] . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                    ><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-info report-item" type="button"
                        data-id="' . $value["idDocumentType"] . '"
                        data-name="' . htmlspecialchars($value["name"]) . '"
                        data-description="' . htmlspecialchars($value["description"] ?? "") . '"
                        data-status="' . $value["status"] . '"
                        data-registration-date="' . $arrData[$key]["registration_date_formatted"] . '"
                        data-update-date="' . $arrData[$key]["update_date_formatted"] . '"
                    ><i class="fa fa-info-circle"></i></button>
                    <button class="btn btn-danger delete-item" 
                        data-id="' . $value["idDocumentType"] . '" 
                        data-name="' . htmlspecialchars($value["name"]) . '"
                    ><i class="fa fa-remove"></i></button>
                </div>
            ';
            $cont++;
        }
        echo json_encode($arrData);
    }

    /**
     * Registra un nuevo tipo de documento en el sistema
     * 
     * @return void
     */
    public function setDocumentType()
    {
        permissionInterface(18);

        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar un tipo de documento", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        isCsrf();

        validateFields(["txtName"]);

        $strName = strClean($_POST["txtName"]);
        $strDescription = isset($_POST["txtDescription"]) ? strClean($_POST["txtDescription"]) : "";

        validateFieldsEmpty(array(
            "NOMBRE" => $strName,
        ));

        // Validación de formato de nombre (solo letras, números, espacios y caracteres especiales comunes)
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

        // Validar que el nombre no exista
        $request = $this->model->select_document_type_by_name($strName);
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El nombre del tipo de documento ingresado ya se encuentra registrado en el sistema.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El nombre del tipo de documento ingresado ya se encuentra registrado en el sistema.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        unset($request);

        // Convertir nombre a mayúsculas
        $strName = strtoupper($strName);

        // Insertar en la base de datos (description puede ser null)
        $description = !empty($strDescription) ? $strDescription : null;
        $request = $this->model->insert_document_type($strName, $description);

        if ($request > 0) {
            registerLog("Registro exitoso", "El tipo de documento ha sido registrado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El tipo de documento fue registrado satisfactoriamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar el registro del tipo de documento.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tipo de documento no se ha registrado correctamente en el sistema",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Actualiza los datos de un tipo de documento existente
     * 
     * @return void
     */
    public function updateDocumentType()
    {
        permissionInterface(18);

        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de actualizar un tipo de documento", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        isCsrf();

        validateFields(["update_txtId", "update_txtName", "update_slctStatus"]);

        $intId = strClean($_POST["update_txtId"]);
        $strName = strClean($_POST["update_txtName"]);
        $strDescription = isset($_POST["update_txtDescription"]) ? strClean($_POST["update_txtDescription"]) : "";
        $slctStatus = strClean($_POST["update_slctStatus"]);

        validateFieldsEmpty(array(
            "ID" => $intId,
            "NOMBRE" => $strName,
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

        // Validación de formato de nombre
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

        // Validar que el tipo de documento exista
        $result = $this->model->select_document_type_by_id($intId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede actualizar el tipo de documento, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del tipo de documento no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validar que el nombre no esté duplicado (excepto el actual)
        $requestForName = $this->model->select_document_type_by_name($strName);
        if ($requestForName) {
            if ($requestForName['idDocumentType'] != $intId) {
                registerLog("Ocurrió un error inesperado", "El nombre del tipo de documento ya existe en el sistema.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El nombre del tipo de documento ya existe. Por favor, ingrese un nombre diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        // Convertir nombre a mayúsculas
        $strName = strtoupper($strName);

        // Actualizar en la base de datos
        $description = !empty($strDescription) ? $strDescription : null;
        $request = $this->model->update_document_type($intId, $strName, $description, $slctStatus);

        if ($request) {
            registerLog("Registro exitoso", "El tipo de documento se ha actualizado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El tipo de documento ha sido actualizado correctamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar la actualización del tipo de documento.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la actualización del tipo de documento.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }

    /**
     * Elimina un tipo de documento del sistema
     * 
     * @return void
     */
    public function deleteDocumentType()
    {
        permissionInterface(18);

        if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
            registerLog("Ocurrió un error inesperado", "No se encontró el método DELETE durante el intento de eliminar un tipo de documento.", 1, $_SESSION['login_info']['idUser']);
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
            registerLog("Ocurrió un error inesperado", "El ID del tipo de documento es obligatorio para completar el proceso de eliminación.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del tipo de documento es requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El ID del tipo de documento debe ser un valor numérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del tipo de documento debe ser numérico.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        $result = $this->model->select_document_type_by_id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede eliminar el tipo de documento, ya que el ID proporcionado no existe.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del tipo de documento no existe.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        $request = $this->model->delete_document_type($id);
        if ($request) {
            registerLog("Eliminación exitosa", "El tipo de documento con ID {$id} y nombre {$name} fue eliminado satisfactoriamente del sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Eliminación exitosa",
                "message" => "El tipo de documento con ID '{$id}' y nombre '{$name}' ha sido eliminado correctamente del sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No fue posible eliminar el tipo de documento con ID '{$id}' y nombre '{$name}'.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la eliminación del tipo de documento con ID '{$id}' y nombre '{$name}'.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
