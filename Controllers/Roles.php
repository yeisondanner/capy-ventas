<?php

class Roles extends Controllers
{
    public function __construct()
    {
        isSession();
        parent::__construct();
    }

    /**
     * Muestra el módulo de Gestión de Roles.
     *
     * Requisitos:
     * - Permiso de acceso para la página con page_id = 4 (permissionInterface()).
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) mediante registerLog() con
     *   contexto del request (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'roles' con los datos de página y variables de sesión requeridas.
     *
     * Seguridad:
     * - Acceso defensivo a $_SESSION y $_SERVER.
     * - Soporte para entornos con proxy/CDN (cabecera HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function roles()
    {
        // Datos de la página (asignación única para claridad y mantenimiento)
        $data = [
            'page_id'          => 4,
            'page_title'       => 'Gestión de Roles',
            'page_description' => 'Gestiona los roles y sus permisos de acceso al sistema.',
            'page_container'   => 'Roles',
            'page_view'        => 'roles',
            'page_js_css'      => 'roles',
            'page_vars'        => ['permission_data', 'login', 'login_info'], // mantener contexto de permisos y sesión
        ];

        // Autorización temprana: detiene el flujo si no hay permisos
        permissionInterface($data['page_id']);

        // Contexto de request/usuario (defensivo + soporte proxy/CDN)
        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        // Payload de auditoría (estable y fácil de consultar)
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
            'timestamp'  => date('c'), // ISO-8601
        ];

        // Registro (nivel 3 asumido como INFO)
        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        // Render de la vista del módulo
        $this->views->getView($this, 'roles', $data);
    }

    /**
     * Funcion que desvuelve la lista de los roles a la vista
     * @return void
     */
    public function getRoles()
    {
        permissionInterface(4);
        $arrData = $this->model->select_roles();
        $cont = 1; //Contador para la tabla
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;
            if ($value["r_status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }
            if ($value["idRole"] != 1) {
                $arrData[$key]["actions"] = '
                 <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-success update-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '"  data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-info report-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '"  data-description="' . $value["r_description"] . '" data-registrationDate="' . dateFormat($value['r_registrationDate']) . '" data-updateDate="' . dateFormat($value['r_updateDate']) . '" type="button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></button>
                  <a href="' . base_url() . '/pdf/rol/' . encryption($value['idRole']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                <button class="btn btn-secondary permission-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '"   data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-th-list" aria-hidden="true"></i></button>
                <button class="btn btn-danger delete-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '" ><i class="fa fa-remove"></i></button>
                </div>
                '; //Botones de acciones
            } else {
                $arrData[$key]["actions"] = ' 
                 <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-success update-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '" data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-info report-item" data-id="' . $value["idRole"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '" data-description="' . $value["r_description"] . '" data-registrationDate="' . dateFormat($value['r_registrationDate']) . '" data-updateDate="' . dateFormat($value['r_updateDate']) . '"  type="button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></button> 
                 <a href="' . base_url() . '/pdf/rol/' . encryption($value['idRole']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                </div>';
            }
            $cont++;
        }
        toJson($arrData);
    }
    /**
     * Funcion que permite el registro del usuario nuevo
     * @return void
     */
    public function setRoles()
    {
        permissionInterface(4);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado al registrar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        //validamos que existan los inputs necesarios        
        validateFields(["txtRoleName", "txtRoleDescription"]);
        // Limpieza de los inputs
        $strRoleName = strClean($_POST["txtRoleName"]);
        $strRoleDescription = strClean($_POST["txtRoleDescription"]);
        // Validación de campos vacíos
        validateFieldsEmpty(array(
            "NOMBRE" => $strRoleName
        ));
        // Validación del formato de texto en el nombre del rol (solo letras y espacios, mínimo 4 caracteres, máximo 250)
        if (verifyData("[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,250}", $strRoleName)) {
            registerLog("Ocurrió un error inesperado", "El campo Nombre no cumple con el formato de texto al registrar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo nombre no cumple con el formato de texto",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Validación del formato de la descripción del rol (permite letras, números, guiones, espacios, mínimo 20 caracteres)
        if ($strRoleDescription != "") {
            if (verifyData("[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+", $strRoleDescription)) {
                registerLog("Ocurrió un error inesperado", "El campo Descripción no cumple con el formato de texto al registrar un rol", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo nombre no cumple con el formato de texto",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //falta valida que el nombre no exista en la base de datos
        //convertimos que el nombre tenga la primera letra en mayuscula
        $strRoleName = ucwords($strRoleName);
        $request = $this->model->insert_role($strRoleName, $strRoleDescription); //insert  roles in database
        if ($request > 0) {
            registerLog("Registro exitoso", "El rol se ha registrado correctamente, al momento de registrar un usuario", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El rol se ha registrado correctamente",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "El rol no se ha registrado correctamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El rol no se ha registrado correctamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Función que devuelve los roles al select de la vista registro de roles.
     * @return void
     */
    public function getRolesSelect()
    {
        $arrData = $this->model->select_roles();
        // Eliminando el rol root para que no se pueda asignar desde la interfaz
        foreach ($arrData as $key => $value) {
            if ($value["idRole"] == 1) {
                unset($arrData[$key]); // Elimina el rol 1
                break;
            }
        }

        $arrData = array_values($arrData); // Reasigna los índices numéricos
        echo json_encode($arrData);
    }

    /**
     * Función que se encarga de eliminar un rol
     * @return void
     */
    public function deleteRoles()
    {
        permissionInterface(4);

        //Validacion de que el Método sea DELETE
        if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
            registerLog("Ocurrió un error inesperado", "Método DELETE no encontrado, al momento de eliminar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método DELETE no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        // Capturamos la solicitud enviada
        $request = json_decode(file_get_contents("php://input"), true);
        // Validación isCsrf
        isCsrf($request["token"]);
        // Validamos que la solicitud tenga los campos necesarios
        $id = strClean($request["id"]);
        $name = strClean($request["name"]);
        //validamos que los campos no esten vacios
        if ($id == "") {
            registerLog("Ocurrió un error inesperado", "El id del rol es requerido, al momento de eliminar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol es requerido, refresca la página e intenta nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que solo ce acepte numeros en el campo id
        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El id del rol debe ser numérico, al momento de eliminar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol debe, ser numérico, refresca la página e intenta nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion de si la tabla depende de alguna tabla en especifico
        $this->model->validation_dependenci_tables($id);
        ///validamos que el id del rol exista en la base de datos
        $result = $this->model->select_rol_by_id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se podra eliminar el usuario, ya que el id no existe en la base de datos", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no existe, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $request = $this->model->delete_role($id);
        if ($request) {
            registerLog("Eliminación correcta", "Se eliminó de manera correcta el rol {$name}", 2, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Eliminación correcta",
                "message" => "Se eliminó de manera correcta el rol {$name}",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo eliminar el rol {$name}, por favor inténtalo nuevamente", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logró eliminar de manera correcta el rol {$name}",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Método que se encarga de actualizar un rol
     * @return void
     */
    public function updateRole()
    {
        permissionInterface(4);
        //validacion del Método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de actualizar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        //validamos que existan los inputs necesarios               
        validateFields(["update_txtId", "update_txtRoleName", "update_txtRoleDescription", "update_txtRoleStatus"]);
        //Captura de datos enviamos
        $update_txtId = strClean($_POST["update_txtId"]);
        $update_txtRoleName = strClean($_POST["update_txtRoleName"]);
        $update_txtRoleDescription = strClean($_POST["update_txtRoleDescription"]);
        $update_txtRoleStatus = strClean($_POST["update_txtRoleStatus"]);
        //validacion de los campos que no llegen vacios
        validateFieldsEmpty(array(
            "ID ROLES" => $update_txtId,
            "NOMBRE DEL ROL" => $update_txtRoleName,
            "ESTADO DEL ROL" => $update_txtRoleStatus
        ));
        //validacion de que el id sea numérico
        if (!is_numeric($update_txtId)) {
            registerLog("Ocurrió un error inesperado", "El id del rol debe ser numérico, al momento de actualizar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol debe ser numérico",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que los roles no sean mayores a 250 caracteres
        if (strlen($update_txtRoleName) > 50) {
            registerLog("Ocurrió un error inesperado", "El nombre del rol no puede ser mayor a 50 caracteres, al momento de actualizar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El nombre del rol no puede ser mayor a 50 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Validamos los caracteres permitidos en el nombre
        if (verifyData("[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,250}", $update_txtRoleName)) {
            registerLog("Ocurrió un error inesperado", "El campo Nombre no cumple con el formato de texto al registrar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo nombre no cumple con el formato de texto",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if ($update_txtRoleDescription != "") {
            if (verifyData("[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+", $update_txtRoleDescription)) {
                registerLog("Ocurrió un error inesperado", "El campo Descripción no cumple con el formato de texto al registrar un rol", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo nombre no cumple con el formato de texto",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //validamos que el id del rol exista en la base de datos
        $result = $this->model->select_rol_by_id($update_txtId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se pudo actualizar el rol, ya que el id no existe en la base de datos", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no existe, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //registramos el rol en la base de datos
        $result = $this->model->update_role($update_txtId, $update_txtRoleName, $update_txtRoleDescription, $update_txtRoleStatus);
        if ($result) {
            registerLog("Rol actualizado", "Se actualizo la informacion del rol con el id: " . $update_txtId, 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Rol actualizado",
                "message" => "Se actualizo el rol con el id: " . $update_txtId,
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo actualizar el rol, al momento de actualizar un rol", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo actualizar el rol, al momento de actualizar un rol",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Summary of getOptionsByRole
     * @return void
     */
    public function getOptionsByRoleAdd()
    {
        permissionInterface(4);
        if (!$_GET) {
            registerLog("Ocurrió un error inesperado", "Método GET no encontrado, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método GET no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que exista el id del rol
        if (!isset($_GET["id"])) {
            registerLog("Ocurrió un error inesperado", "El id del rol no puede estar vacio, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no puede estar vacio, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //almacenamos el id del rol
        $idRole = strClean($_GET["id"]);
        //validamos si el campo esta vacio
        if (empty($idRole)) {
            registerLog("Ocurrió un error inesperado", "El id del rol no puede estar vacio, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no puede estar vacio, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos si el id es numérico
        if (!is_numeric($idRole)) {
            registerLog("Ocurrió un error inesperado", "El id del rol no puede ser un caracter, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no puede ser un caracter, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //consultamos en la base de datos por los permisos del rol
        $result = $this->model->select_permissions_by_role($idRole);
        toJson(["modules" => $result, "status" => true]);
    }
    /**
     * Funcion quye devuelve todas las opciones de los permisos si estan activos o no de aceurdo al rol
     * @return void
     */
    public function getOptionByRoleAll()
    {
        permissionInterface(4);
        //Validamos el Método
        if (!$_GET) {
            registerLog("Ocurrió un error inesperado", "Método GET no encontrado, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método GET no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Capturamos el parametro enviado
        $id = strClean($_GET['id']);
        //validamos si el campo esta vacio
        if (empty($id)) {
            registerLog("Ocurrió un error inesperado", "El id del rol no puede estar vacio, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no puede estar vacio, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Validamos si el id es numérico
        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El id del rol no puede ser un caracter, al momento de obtener sus permisos solicitados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no puede ser un caracter, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //obtenemos los modulos e interfaces habilitadas
        $result = $this->model->selects_all_modules_and_interface($id);
        registerLog("Atención a petición de información", "Petición de modulos activos y disponibles devueltas correctamente: ss" . json_encode($result), 3, $_SESSION['login_info']['idUser']);
        toJson(['status' => true, 'modules' => $result]);
    }
    /**
     * Funcion que prepara el los permisos para el registro
     * @return void
     */
    public function preparePermission()
    {
        permissionInterface(4);
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado, cierre de sesión forzado", "Método POST no encontrado, al momento de seleccionar un permiso", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Validamos que existan los inputs
        validateFields(["idRole", "idInterface"]);
        //recuperamos los inputs que se enviaron
        $idDetail = (isset($_POST['idDetail'])) ? strClean($_POST['idDetail']) : "Fail";
        $idRole = strClean($_POST['idRole']);
        $idInterface = strClean($_POST['idInterface']);
        $status = (isset($_POST['status'])) ? strClean($_POST['status']) : "Activo";
        //Validamos que los campos no esten vacios
        validateFieldsEmpty(array(
            "ID DETALLE" => $idDetail,
            "ID ROL" => $idRole,
            "ID INTERFAZ" => $idInterface,
            "ESTADO" => $status
        ));
        //validamos que los campos sean numericos uno por uno
        if (!is_numeric($idRole)) {
            registerLog("Cierre de sesión forzado", "El id del rol no cumple con la estructura numerica que se necesita", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del rol no cumple con la estructura numerica que se necesita",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!is_numeric($idInterface)) {
            registerLog("Cierre de sesión forzado", "El id de la interfaz no cumple con la estructura numerica que se necesita", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id de la interfaz no cumple con la estructura numerica que se necesita",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!is_numeric($idDetail) && $idDetail != "Fail") {
            registerLog("Cierre de sesión forzado", "El id del detalle no cumple con la estructura numerica que se necesita", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id del detalle no cumple con la estructura numerica que se necesita",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //procedemos almacenar los la informacon en una variable de sesion para poderla utilizar
        if (!isset($_SESSION["permission_data"])) {
            $_SESSION["permission_data"][0] = array(
                "idDetail" => $idDetail,
                "idRole" => $idRole,
                "idInterface" => $idInterface,
                "status" => $status
            );
        } else {
            //Buscamos en el array que no existan datos duplicados, si hubiese se motificaria el estado
            $count = 0;
            foreach ($_SESSION["permission_data"] as $key => $value) {
                if ($value["idRole"] == $idRole && $value["idInterface"] == $idInterface) {
                    $_SESSION["permission_data"][$key]["status"] = $status;
                    registerLog("Atención alerta de información", "Se modificó o creó nuevo registro de los permisos, información detallada: " . json_encode($_SESSION["permission_data"]), 3, $_SESSION['login_info']['idUser']);
                    $data = array(
                        "title" => "Atencion alerta de información",
                        "message" => "Se modificó la información de permisos con éxito",
                        "type" => "info",
                        "status" => true
                    );
                    toJson($data);
                    exit;
                }
            }
            array_push($_SESSION["permission_data"], array("idDetail" => $idDetail, "idRole" => $idRole, "idInterface" => $idInterface, "status" => $status));
        }
        registerLog("Atención alerta de información", "Se modificó o creó nuevo registro de los permisos, información detallada: " . json_encode($_SESSION["permission_data"]), 3, $_SESSION['login_info']['idUser']);
        $data = array(
            "title" => "Atención alerta de información",
            "message" => "Se modificó la información de permisos con éxito",
            "type" => "info",
            "status" => true
        );
        toJson($data);
    }
    /**
     * Registro de la informacion de datos de cada permiso de cada rol
     * @return never
     */
    public function setDataPermission()
    {
        if (!isset($_SESSION["permission_data"])) {
            registerLog("Ocurrió un error inesperado", "No se ha hecho ninguna modificación en los permisos", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se selecciono ningún permiso o modifación",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $arrData = $_SESSION["permission_data"];
        foreach ($arrData as $key => $value) {
            //Primero consultamos en la base de datos si existe el registro, para poder actualizarlo caso contrario se registrar
            $resultData = $this->model->select_userroldetail($value["idInterface"], $value["idRole"]);
            if ($resultData) {
                //Si en caso existiese el registro , actualizamos el estado
                $resultSetData = $this->model->update_status_interface_role_detail($value["idInterface"], $value["idRole"], $value["status"]);
            } else {
                //si en caso no hubiese un registro insertamos un nuevo registro
                $resultSetData = $this->model->insert_new_permission_role_detail($value["idInterface"], $value["idRole"], $value["status"]);
            }
        }
        registerLog("Registro de permisos correcto", "Se registró de manera correcta los permisos para el rol", 2, $_SESSION['login_info']['idUser']);

        $data = array(
            "title" => "Registro de permisos correcto",
            "message" => "Se registró de manera correcta los permisos para el rol",
            "type" => "success",
            "status" => true
        );
        unset($_SESSION["permission_data"]);
        toJson($data);
    }
}
