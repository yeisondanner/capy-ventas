<?php
class Notification extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Muestra el módulo de Gestión de Notificaciones.
     *
     * Requisitos:
     * - Sesión activa (isSession()).
     * - Permiso de acceso para la página con page_id = 9 (permissionInterface()).
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) vía registerLog() con
     *   información de contexto (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'notification' con los datos de página.
     *
     * Seguridad:
     * - Accesos a $_SESSION y $_SERVER realizados de forma defensiva.
     * - Soporte para entornos con proxy/CDN (HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function notification()
    {
        // Verifica sesión activa
        isSession();

        // Datos de la página (una sola asignación para legibilidad y mantenimiento)
        $data = [
            'page_id'          => 9,
            'page_title'       => 'Gestión de notificaciones',
            'page_description' => 'En este módulo puedes emitir notificaciones a todos los usuarios o a un usuario específico.',
            'page_container'   => 'Notification',
            'page_view'        => 'notification',
            'page_js_css'      => 'notification',
            'page_vars'        => ['login', 'login_info'], // evita perder datos de sesión
        ];

        // Autorización temprana: corta la ejecución si no hay permisos
        permissionInterface((int) $data['page_id']);

        // Contexto de request/usuario (defensivo + soporte proxy)
        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        // Payload de auditoría para trazabilidad y diagnósticos
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

        // Renderización de la vista con el layout y assets correspondientes
        $this->views->getView($this, 'notification', $data);
    }

    /**
     * Metodo para obtener las notificaciones 
     * @return void
     */
    public function getNotificationsOfUser()
    {
        isSession();
        $request = $this->model->select_notifications_by_id_of_user($_SESSION['login_info']['idUser']);
        foreach ($request as $key => $value) {
            $request[$key]['date'] = calculateDifferenceDatesActual($value['n_created_at']);
            $request[$key]['n_description'] = decryption($value['n_description']);
            $request[$key]['description'] = limitarCaracteres(decryption($value['n_description']), 50);
        }
        toJson($request);
    }
    /**
     * Metodo que actualiza el estado de lectura de una notificacion
     * @return void
     */
    public function updateNotificationRead()
    {
        isSession();
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado al momento de actualizar el estado de una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!isset($_POST["id"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el id de la notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logro encontrar el id de la notificacion",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $id = strClean($_POST["id"]);
        if (empty($id)) {
            registerLog("Ocurrió un error inesperado", "El id se encuentra vacio, por favor refresque la pagina e intente nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id se encuentra vacio, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que sea numerico
        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El id no es numerico, por favor refresque la pagina e intente nuevmente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id no es numerico, por favor refresque la pagina e intente nuevmente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $request = $this->model->update_notification_read($id);
        if ($request) {
            registerLog("Notificación actualizada", "La notificacion se leyo correctamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Notificacion actualizada correctamente",
                "message" => "Las notifiacion se leyo de manera correcta",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se logro actualizar la notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logro actualizar la notificacion",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que se encarga enviar las data del
     * formulario de notificaciones del frontend
     * y llamar al modelo el ccual se encarga de registrar la notificacion
     * tambien aqui se valida que los campos esten correctos
     * @return void
     */
    public function setNotification()
    {
        //validamos que el usuario este logeado
        isSession();
        //validamos que el usuario tenga los permisos necesarios para realizar esta accion
        permissionInterface(9);
        //validamos que el usuario tenga los permisos necesarios para realizar esta accion
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado al momento de registrar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        #validamos que los campos necesarios se esten enviando de manera correcta
        if (!isset($_POST["txtTitle"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo txtTitle para poder registrar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo txtTitle, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!isset($_POST["txtDescription"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo txtDescription para poder registrar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo txtDescription, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $txtDescription = strClean(encryption($_POST["txtDescription"]));
        $txtTitle = strClean($_POST["txtTitle"]);
        //capturamos el slctUsers que viene con varios datos
        $slctUsers = (!isset($_POST["slctUsers"])) ? null : $_POST["slctUsers"];
        $txtLink = (!isset($_POST["txtLink"])) ? null : strClean($_POST["txtLink"]);
        $slctIcon = (!isset($_POST["slctIcon"])) ? null : strClean($_POST["slctIcon"]);
        $slctColor = (!isset($_POST["slctColor"])) ? null : strClean($_POST["slctColor"]);
        $slctType = (!isset($_POST["slctType"])) ? null : strClean($_POST["slctType"]);
        $slctPriority = (!isset($_POST["slctPriority"])) ? null : strClean($_POST["slctPriority"]);
        $chckNotification = (!isset($_POST["chckNotification"])) ? 'No' : 'Si';
        //validamos que el campo txtDescription no este vacio y el campo txtTitle no este vacio
        if (empty($txtDescription) || empty($txtTitle)) {
            registerLog("Ocurrió un error inesperado", "Los campos txtDescription y txtTitle no pueden estar vacios", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Los campos txtDescription y txtTitle no pueden estar vacios",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el campo $slctUsers no este vacio
        if (empty($slctUsers)) {
            registerLog("Informacion de notificacion", "El usuario a seleccionado enviar la notificacion a todos los usuarios", 3, $_SESSION['login_info']['idUser']);
            //creamos un objeto de la clase de users y obtenemos todos los usuarios activos
            require_once "./models/UsersModel.php";
            $users = new UsersModel();
            $request = $users->select_users();
            foreach ($request as $key => $value) {
                if ($value['u_status'] === 'Activo') {
                    $arrResult[] = $value['idUser'];
                }
            }
            //destruimos el objeto de la clase de users
            unset($users);
        } else {
            //validamos que el campo $slctUsers sea un array
            if (!is_array($slctUsers)) {
                registerLog("Ocurrió un error inesperado", "El campo slctUsers no es un array", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo slctUsers no es un array",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            $arrResult = $slctUsers;
        }
        //validamos que el titulo sea maximo a 255 caracteres
        if (strlen($txtTitle) > 255) {
            registerLog("Ocurrió un error inesperado", "El titulo no puede ser mayor a 255 caracteres", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El titulo no puede ser mayor a 255 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que la descripcion sea mayor a 10 caracteres
        if (strlen($txtDescription) < 10) {
            registerLog("Ocurrió un error inesperado", "La descripcion no puede ser menor a 10 caracteres", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La descripcion no puede ser menor a 10 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //recorremos el array de usuarios
        foreach ($arrResult as $key => $value) {
            //consultamos en la base de datos si el usuario existe como tambien se obtiene la informacion
            require_once "./Models/UsersModel.php";
            $objUser = new UsersModel();
            $request = $objUser->select_user_by_Id($value);

            //registramos la notificacion
            $requestInsert = $this->model->insert_notification($value, $txtTitle, $txtDescription, $slctPriority, $slctType, $slctColor, $slctIcon, $txtLink, $chckNotification);
            //validamos si el envio de notificaicon via correo esta activo
            if ($chckNotification == "Si") {
                //preaparamos el envio por correo 
                $config = [
                    'smtp' => [
                        'host' => decryption(getHost()),
                        'username' => decryption(getUser()),
                        'password' => decryption(getPassword()),
                        'port' => (getPort()),
                        'encryption' => getEncryption() // ssl o tls
                    ],
                    'from' => [
                        'email' => decryption(getFrom()),
                        'name' => decryption(getRemitente())
                    ]
                ];
                //cargamos la plantilla de recuperación de contraseña               
                $data = [
                    'nombres' => $request["u_fullname"],
                    'titulo' => $txtTitle,
                    'descripcion' => decryption($txtDescription),
                    'enlace' => $txtLink
                ];
                // Cargar plantilla HTML externa
                $plantillaHTML = renderTemplate('./Views/Template/email/notification_standar.php', $data);
                $params = [
                    'to' => [decryption($request['u_email'])], // o string
                    'subject' => 'NOTIFICACION [ ' . $txtTitle . ' ]- ' . getCompanyName(),
                    'body' => $plantillaHTML,
                    'attachments' => [] // opcional
                ];
                //enviamos el correo
                if (!sendEmail($config, $params)) {
                    registerLog("Ocurrio un error inesperado", "No se pudo enviar el correo de notificacion al usuario {$request['u_fullname']}", 1, $request["idUser"]);
                }
            }
            //destruimos la variable $request
            unset($request);
        }
        if ($requestInsert > 0) {
            registerLog("Envio de notificacion", "Se ha enviado la notificacion a los usuarios seleccionados", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Notificacion enviada",
                "message" => "Se ha enviado la notificacion a los usuarios seleccionados",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo enviar la notificacion a los usuarios seleccionados", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo enviar la notificacion a los usuarios seleccionados",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que se encarga de obtener todas las notificaciones de cada usuario
     * para la vista de notificaciones la cual se mostrara en la tabla de notificaciones
     * de la vista de notificaciones
     * @return void
     */
    public function get_notifications()
    {
        isSession();
        permissionInterface(9);
        $request = $this->model->select_all_notifications();
        $cont = 1;
        foreach ($request as $key => $value) {
            $value['n_description'] = decryption($value['n_description']);
            //valiodamos si pa foto de perfil esta vacio
            if (empty($value['u_profile'])) {
                $request[$key]['u_profile'] = generateAvatar($value['u_fullname']);
            } else {
                $request[$key]['u_profile'] = base_url() . "/loadfile/profile?f=" . $value['u_profile'];
            }
            $request[$key]['date'] = calculateDifferenceDatesActual($value['n_created_at']);
            $request[$key]["cont"] = $cont;
            $request[$key]["u_email"] = decryption($value['u_email']);
            $request[$key]["n_description"] = ($value['n_description']);
            $request[$key]["description"] = limitarCaracteres($value['n_description'], 50);
            //validamos si la notificacion aun no es leida          
            $request[$key]["actions"] = "<div class='btn-group'>
                                        <button type='button' class='btn btn-success btn-sm update-item' data-data='" . json_encode($request[$key]) . "'><i class='fa fa-fw fa-lg fa-pencil'></i></button>
                                        <button type='button' class='btn btn-info btn-sm item-notification'data-info-notification='" . json_encode($request[$key]) . "' data-admin='true'><i class='fa fa-eye'></i></button>
                                        <button type='button' class='btn btn-danger btn-sm delete-item' data-id='" . $value['idNotification'] . "' data-name='" . $value['n_title'] . "'><i class='fa fa-times'></i></button>
                                        </div>";
            $cont++;
        }
        toJson($request);
    }
    /**
     * Metodo que elimina una notificacion de la base de datos
     * @return void
     */
    public function deleteNotification()
    {
        isSession();
        permissionInterface(9);

        //Validacion de que el Método sea DELETE
        if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
            registerLog("Ocurrió un error inesperado", "Método DELETE no encontrado, al momento de eliminar una notificacion", 1, $_SESSION['login_info']['idUser']);
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
            registerLog("Ocurrió un error inesperado", "El id de la notificacion es requerida para eliminar la notificacion, refresque la pagina y vuelva a intentarlo", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id de la notificacion es requerida, refresca la página e intenta nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que solo ce acepte numeros en el campo id
        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El id de la notificacion no es numerica, por favor refresca la pagina e intenta nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id de la notificacion no es numerica, refresca la página e intenta nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        ///validamos que el id del rol exista en la base de datos
        $result = $this->model->select_notification_by_id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se podra eliminar la notificacion ya que el id solicitado no existe en la base de datos", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El id de la notificacion no existe, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $request = $this->model->delete_notification($id);
        if ($request) {
            registerLog("Eliminación correcta", "Se eliminó de manera correcta la notificacion <strong>{$name}</strong>", 2, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Eliminación correcta",
                "message" => "Se eliminó de manera correcta la notificacion <strong>{$name}</strong>",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo eliminar la notificacion {$name}, por favor inténtalo nuevamente", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logró eliminar de manera correcta la notificacion {$name}",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que actualiza la notificacion de la base de datos
     * @return void
     */
    public function updateNotification()
    {
        isSession();
        permissionInterface(9);
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
        #validamos que los campos necesarios se esten enviando de manera correcta
        if (!isset($_POST["updateTxtTitle"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo txtTitle para poder registrar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo txtTitle, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!isset($_POST["updateTxtDescription"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo txtDescription para poder registrar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo txtDescription, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!isset($_POST["updateTxtIdNotification"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo id para poder actualizar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo id, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (!isset($_POST["updatePreviewStatus"])) {
            registerLog("Ocurrió un error inesperado", "No se encontro el campo slctStatus para poder actualizar una notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontro el campo slctStatus, por favor refresque la pagina e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $updateTxtIdNotification = strClean($_POST["updateTxtIdNotification"]);
        $updateTxtDescription = strClean(encryption($_POST["updateTxtDescription"]));
        $updateTxtTitle = strClean($_POST["updateTxtTitle"]);
        $updatePreviewStatus = strClean($_POST["updatePreviewStatus"]);
        //capturamos el slctUsers que viene con varios datos
        $updateSlctUsers = (!isset($_POST["updateSlctUsers"])) ? null : $_POST["updateSlctUsers"];
        $updateTxtLink = (!isset($_POST["updateTxtLink"])) ? null : strClean($_POST["updateTxtLink"]);
        $updateSlctIcon = (!isset($_POST["updateSlctIcon"])) ? null : strClean($_POST["updateSlctIcon"]);
        $updateSlctColor = (!isset($_POST["updateSlctColor"])) ? null : strClean($_POST["updateSlctColor"]);
        $updateSlctType = (!isset($_POST["updateSlctType"])) ? null : strClean($_POST["updateSlctType"]);
        $updateSlctPriority = (!isset($_POST["updateSlctPriority"])) ? null : strClean($_POST["updateSlctPriority"]);
        //validamos que el campo txtDescription no este vacio y el campo txtTitle no este vacio
        if (empty($updateTxtDescription) || empty($updateTxtTitle) || empty($updateTxtIdNotification)) {
            registerLog("Ocurrió un error inesperado", "Los campos txtDescription y txtTitle no pueden estar vacios", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Los campos txtDescription y txtTitle no pueden estar vacios",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el id sea un numero entero
        if (!is_numeric($updateTxtIdNotification)) {
            registerLog("Ocurrió un error inesperado", "El campo id debe ser un numero entero", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo id debe ser un numero entero",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el titulo sea maximo a 255 caracteres
        if (strlen($updateTxtTitle) > 255) {
            registerLog("Ocurrió un error inesperado", "El titulo no puede ser mayor a 255 caracteres", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El titulo no puede ser mayor a 255 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que la descripcion sea mayor a 10 caracteres
        if (strlen($updateTxtDescription) < 10) {
            registerLog("Ocurrió un error inesperado", "La descripcion no puede ser menor a 10 caracteres", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La descripcion no puede ser menor a 10 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $request = $this->model->update_notification($updateTxtTitle, $updateTxtDescription, $updateSlctPriority, $updateSlctType, $updateSlctColor, $updateSlctIcon, $updateTxtLink, $updatePreviewStatus, $updateTxtIdNotification);
        if ($request) {
            registerLog("Notificacion actualizada", "Se actualizo la notificacion con id: " . $updateTxtIdNotification, 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Notificacion actualizada",
                "message" => "Se actualizo la notificacion con id: " . $updateTxtIdNotification,
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo actualizar la notificacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo actualizar la notificacion",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**-
     * Metodo que obtiene las notificaciones de los usuarios por su id
     * @return void
     */
    public function get_notifications_by_user_for_id()
    {
        isSession();
        $id = $_SESSION['login_info']['idUser']; //obtenemos el id del usuario
        $request = $this->model->select_notifications_by_id_of_user($id, 0);
        $cont = 1;
        $data = [];
        foreach ($request as $key => $value) {
            $request[$key]['n_description'] = decryption($value['n_description']);
            $request[$key]['date'] = calculateDifferenceDatesActual($value['n_created_at']);
            $data[$key]['cont'] = $cont;
            $data[$key]['title'] = $value['n_title'];
            $data[$key]['description'] = limitarCaracteres(decryption($value['n_description']), 50);
            $data[$key]['link'] = $value['n_link'];
            $data[$key]['type'] = $value['n_type'];
            $data[$key]['priority'] = $value['n_priority'];
            $data[$key]['icon'] = $value['n_icon'];
            $data[$key]['color'] = $value['n_color'];
            $data[$key]['read'] = $value['n_is_read'];
            $data[$key]['date_create'] = calculateDifferenceDatesActual($value['n_created_at']);
            $data[$key]['actions'] = "<button class='btn btn-info btn-sm item-notification' data-info-notification='" . json_encode($request[$key]) . "'><i class='fa fa-eye'></i></button>";
            $cont++;
        }
        toJson($data);
    }
}
