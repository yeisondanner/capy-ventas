<?php

class System extends Controllers
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
     * Muestra el módulo de Configuración del Sistema.
     *
     * Requisitos:
     * - Permiso de acceso para la página con page_id = 6 (permissionInterface()).
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) mediante registerLog() con
     *   contexto del request (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'system' con los datos de página.
     *
     * Seguridad:
     * - Acceso defensivo a $_SESSION y $_SERVER.
     * - Soporte para entornos con proxy/CDN (cabecera HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function system()
    {
        // Datos de la página (asignación única para claridad y mantenimiento)
        $data = [
            'page_id'          => 6,
            'page_title'       => 'Configuración del Sistema',
            'page_description' => 'Permite cambiar el logo, nombre y otros parámetros globales del sistema.',
            'page_container'   => 'System',
            'page_view'        => 'system',
            'page_js_css'      => 'system',
            'page_vars'        => ['login', 'login_info'],
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
        $this->views->getView($this, 'system', $data);
    }

    /**
     * Funcion que permite guardar la informacion general del sistema
     * @return void
     */
    public function setInfoGeneral()
    {
        //Se verifica si se tiene permiso para acceder a la vista
        permissionInterface(6);
        //validacion del metodo POST
        if (!$_POST) {
            registerLog("Ocurrio un error inesperado", "Metodo POST no encontrado, al momento de registrar o actualizar la informacion del sistema", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "Metodo POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Validacion que la tabla solo tenga un solo registro
        $request_data_all = $this->model->selects_info_system();
        //Si el resultado llega vacios entonces se asigna un valor de 0
        if (empty($request_data_all)) {
            $request_data_all = 0;
        }
        //si el resultado es un array y tiene mas de un registro entonces se procede a truncar la tabla
        if (is_array($request_data_all) && count($request_data_all) > 1) {
            $this->model->truncate_info_system();
            registerLog("Ocurrio un error inesperado", "La tabla de configuracion solo puede tener un registro, no se puede registrar mas de un registro, por lo que se procedio a truncar la tabla con la informacion", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "No se puede realizar el registro actualmente, refresca la pagina e intenta de nuevo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        //validacion que los campos existan        
        $inputs = array(
            "nombreSistema",
            "descripcion",
            "txtColorPrimary",
            "txtColorSecondary",
            "txtNameInsitution",
            "txtRuc",
            "txtAddress",
            "txtPhone",
            "txtMail",
            "txtDurationLock",
            "rdLoaderSelect",
            "txtTextLoader",
            "txtKeyApi",
            "txtPasswordApi",
            "txtUserAPi",
            "smtpHost",
            "smtpPort",
            "smtpEncryption",
            "smtpUsername",
            "smtpPassword",
            "fromEmail",
            "fromName",
        );
        validateFields($inputs);
        //validamos  que exista los campos de la api
        //recuperacion de los datos del formulario
        $nombreSistema = strClean($_POST['nombreSistema']);
        $descripcion = strClean($_POST['descripcion']);
        $txtColorPrimary = "#" . strClean($_POST['txtColorPrimary']);
        $txtColorSecondary = "#" . strClean($_POST['txtColorSecondary']);
        $logo = ($_FILES) ? $_FILES["logo"]["name"] : "";
        //informacion de contacto
        $txtNameInsitution = strClean($_POST['txtNameInsitution']);
        $txtRuc = strClean($_POST['txtRuc']);
        $txtAddress = strClean($_POST['txtAddress']);
        $txtPhone = strClean($_POST['txtPhone']);
        $txtMail = strClean($_POST['txtMail']);
        $txtDurationLock = "0" . strClean($_POST['txtDurationLock']);
        $rdLoaderSelect = strClean($_POST['rdLoaderSelect']);
        $txtTextLoader = strClean($_POST['txtTextLoader']);
        $txtKeyApi = strClean($_POST['txtKeyApi']);
        $txtPasswordApi = strClean($_POST['txtPasswordApi']);
        $txtUserAPi = strClean($_POST['txtUserAPi']);
        //cargamos los datos del correo electronico
        $smtpHost = strClean($_POST['smtpHost']);
        $smtpPort = strClean($_POST['smtpPort']);
        $smtpEncryption = strClean($_POST['smtpEncryption']);
        $smtpUsername = strClean($_POST['smtpUsername']);
        $smtpPassword = strClean($_POST['smtpPassword']);
        $fromEmail = strClean($_POST['fromEmail']);
        $fromName = strClean($_POST['fromName']);
        //validamos que los campos no esten vacios
        validateFieldsEmpty(array(
            "Nombre del sistema" => $nombreSistema,
            "Descripción" => $descripcion,
            "Color primario" => $txtColorPrimary,
            "Color secundario" => $txtColorSecondary,
            "Nombre Empresa/Insitucion/Entidad" => $txtNameInsitution,
            "RUC" => $txtRuc,
            "Dirección" => $txtAddress,
            "Celular" => $txtPhone,
            "Correo" => $txtMail,
            "Dur. de Bloq. (Minutos)" => $txtDurationLock,
            "Selecciona tu Loader" => $rdLoaderSelect,
            "Texto del loader" => $txtTextLoader,
            "Usuario" => $txtKeyApi,
            "Contraseña" => $txtPasswordApi,
            "Llave API Reniec" => $txtUserAPi,
            "Servidor SMTP" => $smtpHost,
            "Puerto SMTP" => $smtpPort,
            "Cifrado SMTP" => $smtpEncryption,
            "Nombre SMTP" => $smtpUsername,
            "Contraseña SMTP" => $smtpPassword,
            "Correo Electronico Remitente" => $fromEmail,
            "Nombre del correo remitente" => $fromName
        ));

        //Validacion de que el campo tenga la misma estructura que el nombre del sistema
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $nombreSistema)) {
            registerLog("Ocurrio un error inesperado", "El nombre del sistema no puede tener caracteres especiales", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El nombre del sistema no puede tener caracteres especiales",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        ///Validacion de que el campo tenga la misma estructura que el nombre del sistema
        if (verifyData("[a-zA-ZÁÉÍÓÚáéíóúÜüÑñ0-9\s.,;:!?()-]+", $descripcion)) {
            registerLog("Ocurrio un error inesperado", "La descripcion del sistema no puede tener caracteres especiales", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "La descripcion del sistema no puede tener caracteres especiales",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        //validamos que el campo sea numerico
        if (!is_numeric($txtDurationLock)) {
            registerLog("Ocurrio un error inesperado", "El tiempo de bloqueo no puede tener caracteres especiales", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El tiempo de bloqueo no puede tener caracteres especiales",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el txtDurationLock sea 0 o 9999
        if ($txtDurationLock < 0 || $txtDurationLock > 9999) {
            registerLog("Ocurrio un error inesperado", "El tiempo de bloqueo no puede ser menor a 0 o mayor a 9999", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El tiempo de bloqueo no puede ser menor a 0 o mayor a 9999",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que $rdLoaderSelect sea numerico
        if (!is_numeric($rdLoaderSelect)) {
            registerLog("Ocurrio un error inesperado", "El tipo de carga no puede tener caracteres especiales", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El tipo de carga no puede tener caracteres especiales",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que $rdLoaderSelect sea mayor a 0
        if ($rdLoaderSelect < 1) {
            registerLog("Ocurrio un error inesperado", "El tipo de carga no puede ser menor a 1", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El tipo de carga no puede ser menor a 1",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos los datos de configuracion de correo electronico
        if (verifyData('(?:(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}|(?:\d{1,3}\.){3}\d{1,3})', $smtpHost)) {
            registerLog("Ocurrio un error inesperado", "El servidor de correo no es valido", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El servidor de correo no es valido",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validmoas el puerto
        if (!is_numeric($smtpPort) || $smtpPort < 1) {
            registerLog("Ocurrio un error inesperado", "El puerto no es numerico o menor a 1", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El puerto no es numerico o menor a 1",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //encritamos lo datos del correo
        $smtpHost = encryption($smtpHost);
        $smtpUsername = encryption($smtpUsername);
        $smtpPassword = encryption($smtpPassword);
        $fromEmail = encryption($fromEmail);
        $fromName = encryption($fromName);
        //proceso de subida de la imagen de perfil al servidor
        if (isset($_FILES["logo"]["name"]) && !empty($_FILES["logo"]["name"])) {
            //Valdiacion de  tipos de imagen para subir
            if (isFile("image", $_FILES["logo"], ["jpg", "png", "jpeg"])) {
                registerLog("Ocurrio un error inesperado", "Para subir el logo/icono para el sistema solo se permite fotos de tipo JPEG, JPG, PNG", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "El formato de la imagen no cumple con los formatos solicitados",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //Validacion de que la ruta de la imagen exista y si que se cree
            $ruta = getRoute() . "Profile/Logo/";
            if (verifyFolder($ruta, 0777, true)) {
                registerLog("Informacion de subida de archivos", "El usuario subio una foto de perfil, donde se valido que exista la ruta solicitada para subir el archivo", 3, $_SESSION['login_info']['idUser']);
            }
            //preparamos la ruta final para subir al servidor
            $nameFinalPhoto = uniqid() . "." . pathinfo($logo, PATHINFO_EXTENSION);
            $rutaFinal = $ruta . $nameFinalPhoto;
            //validamos si la imagen es mayor a 2 mg para redimensionar el archivo caso contrario subimos sin cambio
            if (valConvert((int)$_FILES["logo"]["size"])['MB'] > 2) {
                //Subimos la imagen al servidor
                $request_moveFile = resizeAndCompressImage($_FILES["logo"]["tmp_name"], $rutaFinal, 0.8, 100, 100);
            } else {
                $request_moveFile = move_uploaded_file($_FILES["logo"]["tmp_name"], $rutaFinal);
            }
            if (!$request_moveFile) {
                registerLog("Registro exitoso", "No se logro subir el logo del sistema al momento de registrar sus datos principales", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "No se logro subir el logo del sistema",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            }
            $logo = $nameFinalPhoto; //se asigna el nombre de la imagen a la variable
        }
        //convertimos los datos de txtDurationLock que estan en minutos a segundos
        $txtDurationLock = $txtDurationLock * 60;
        //obtnemos el loader seleccionado
        $loader = getLoader($rdLoaderSelect); //obtenemos el loader seleccionado
        //validamos que si existe los datos de configuracion se actulice entonces la informacion
        if (is_array($request_data_all) && count($request_data_all) == 1) {
            //Se obtiene la informacion de sistema con una funcion que solo trae un solo registro
            $request_data = $this->model->select_info_system();
            $idConfiguration = $request_data['idConfiguration'];
            $c_logo = $request_data['c_logo'];
            if (isset($_POST["profile_exist"]) && empty($_FILES["logo"]["name"])) {
                $logo = $c_logo; //se asigna el logo existente
            }
            //Encriptamos los datos de la api para que no se vean en el navegador
            $txtUserAPi = encryption($txtUserAPi);
            $txtPasswordApi = encryption($txtPasswordApi);
            $txtKeyApi = encryption($txtKeyApi);
            $request = $this->model->update_info_system(
                $idConfiguration,
                $nombreSistema,
                $descripcion,
                $logo,
                $txtColorPrimary,
                $txtColorSecondary,
                $txtNameInsitution,
                $txtRuc,
                $txtAddress,
                $txtPhone,
                $txtMail,
                $txtDurationLock,
                $rdLoaderSelect,
                $loader,
                $txtTextLoader,
                $txtUserAPi,
                $txtPasswordApi,
                $txtKeyApi,
                $smtpHost,
                $smtpPort,
                $smtpEncryption,
                $smtpUsername,
                $smtpPassword,
                $fromEmail,
                $fromName,
            );
            if ($request) {
                if (isset($_POST["profile_exist"]) && !empty($_FILES["logo"]["name"])) {
                    //Procedemos a eliminar el icono del sistema
                    $img = $c_logo;
                    $ruta = getRoute() . "Profile/Logo";
                    if (delFolder($ruta, $img)) {
                        registerLog("Atención", "No se pudo eliminar el logo del sistema, al momento de eliminar un logo, pero si se elimino el usuario, posiblemente porque no existe el archivo", 3, $_SESSION['login_info']['idUser']);
                        $data = array(
                            "title" => "Atención",
                            "message" => "No se logro eliminar el logo del sistema, pero si se actualizao la informacion del sistema",
                            "type" => "info",
                            "status" => true
                        );
                        toJson($data);
                    }
                }
                registerLog("Actualizacion exitosa", "Se actualizo la informacion del sistema correctamente", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Actualizacion exitosa",
                    "message" => "Se actualizo la informacion del sistema correctamente",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            } else {
                registerLog("Ocurrio un error inesperado", "No se logro actualizar la informacion del sistema", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "No se logro actualizar la informacion del sistema",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //Encriptamos los datos de la api para que no se vean en el navegador
        $txtUserAPi = encryption($txtUserAPi);
        $txtPasswordApi = encryption($txtPasswordApi);
        $txtKeyApi = encryption($txtKeyApi);
        // caso contrario se realice el registro de informacion en la tabla
        $request = $this->model->insert_info_system(
            $nombreSistema,
            $descripcion,
            $logo,
            $txtColorPrimary,
            $txtColorSecondary,
            $txtNameInsitution,
            $txtRuc,
            $txtAddress,
            $txtPhone,
            $txtMail,
            $txtDurationLock,
            $rdLoaderSelect,
            $loader,
            $txtTextLoader,
            $txtUserAPi,
            $txtPasswordApi,
            $txtKeyApi,
            $smtpHost,
            $smtpPort,
            $smtpEncryption,
            $smtpUsername,
            $smtpPassword,
            $fromEmail,
            $fromName,
        );
        if ($request) {
            registerLog("Registro exitoso", "Se registro la informacion del sistema correctamente", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "Se registro la informacion del sistema correctamente",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrio un error inesperado", "No se logro registrar la informacion del sistema", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "No se logro registrar la informacion del sistema",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
