<?php

class Users extends Controllers
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
     * Funcion que devuelve la vista de la gestion de usuarios
     * @return void
     */
    public function users()
    {
        $data['page_id'] = 3;
        permissionInterface($data['page_id']);
        $data['page_title'] = "Gestión de Usuarios";
        $data['page_description'] = "Permite registrar, editar, eliminar y controlar el acceso de los usuarios al sistema, asignando roles y permisos según su perfil. Garantiza seguridad, control de accesos y trazabilidad de acciones.";
        $data['page_container'] = "Users";
        $data['page_view'] = 'users';
        $data['page_js_css'] = "users";
        $data['page_vars'] = ["login", "login_info", "lastConsult"];
        $payload = [
            'event'     => 'page_view',
            'page'      => $data['page_title'],
            'page_id'   => $data['page_id'],
            'container' => $data['page_container'],
            'user_id'   => $_SESSION['login_info']['idUser'] ?? null,
            'ip'        => obtenerIP(),
            'method'    => $_SERVER['REQUEST_METHOD'] ?? null,
            'url'       => $_SERVER['REQUEST_URI'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 180),
            'timestamp' => date('c')
        ];

        registerLog(
            "Navegación",
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $payload['user_id']
        );
        $this->views->getView($this, "users", $data);
    }
    /***
     * Esta función te permite abrir la vista del perfil de la cuenta del usuario
     */
    public function profile()
    {
        // Datos de la página (una sola asignación)
        $data = [
            'page_id'          => 7,
            'page_title'       => 'Perfil del Usuario',
            'page_description' => 'Permite gestionar de manera centralizada y segura la información del usuario, como la foto de perfil, nombre de la empresa y descripción.',
            'page_container'   => 'Users',
            'page_view'        => 'profile',
            'page_js_css'      => 'profile',
            'page_vars'        => ['login', 'login_info'],
        ];

        // Autorización temprana
        permissionInterface($data['page_id']);

        // Variables de request/usuario (robustas)
        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = obtenerIP(); // soporta proxy/CDN
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        // Payload de auditoría
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

        // Registro (nivel 3 asumido como INFO)
        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        // Render de la vista
        $this->views->getView($this, 'profile', $data);
    }


    /**
     * Funcion que desvuuelve la lista de los usuarios a la vista
     * @return void
     */
    public function getUsers()
    {
        permissionInterface(3);
        $arrData = $this->model->select_users();
        $cont = 1; //Contador para la tabla
        foreach ($arrData as $key => $value) {
            $arrData[$key]["u_user"] = decryption($value["u_user"]);
            $arrData[$key]["u_email"] = decryption($value["u_email"]);
            if (empty($value['u_profile'])) {
                $profile = generateAvatar($value['u_fullname']);
            } else {
                $profile = base_url() . "/loadfile/profile?f=" . $value['u_profile'];
            }
            //formateamos las fechas de registro y actualizacion
            $arrData[$key]["cont"] = $cont;
            if ($value["u_status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }
            $resetPw = "";
            //si el atributo de u_reset_token_password esta vacio no genera el boton pero si no genera un boton con el enlace a la recuperaciones de contraseña y un tol tip indicando que solo se muestra cuando el usuario tiene un token generado
            if (!empty($value['u_reset_token_password'])) {
                $resetPw = '<a title="Enlace de recuperacion de contrsaeña, este enlace solo esta activo 30 minutos, y solo aparece este boton cuando el usuario esta en proceso de recuperacion de contraseña" href="' . base_url() . '/login/pwreset/' . $value['u_reset_token_password'] . '" class="btn btn-primary" target="_Blank">
                <i class="fa fa-lock"></i></a>';
            }
            if (!empty($value['u_reset_token_password'])) {
                //desencriptamos el token de recuperacion de contraseña
                $token = decryption($value['u_reset_token_password']);
                //separamos el token en 2 partes usando el caracter |
                $token = explode("|", $token);
                //si el token tiene 2 partes, es decir, el token de recuperacion de contraseña
                $id = $token[0];
                $datetime = $token[1];
                //calculamos las diferencias de horas
                $requestDate = dateDifference($datetime, date("Y-m-d H:i:s"));
                if ($requestDate['total_minutos'] > 30) {
                    $statusLink = "<span class='badge badge-danger'>Vencido " . calculateDifferenceDatesActual($datetime) . "</span>";
                } else {
                    $statusLink = "<span class='badge badge-success'>Activo</span>";
                }
            } else {
                $statusLink = "<span class='badge badge-info'>No hay token de recuperacion de contraseña</span>";
            }
            if ($value["idUser"] != 1) {
                $arrData[$key]["actions"] = '   
                <div class="btn-group">      
                ' . $resetPw . '       
                <button class="btn btn-success update-item" type="button"
                    data-id="' . $value["idUser"] . '"
                    data-fullname="' . $value["u_fullname"] . '"
                    data-dni="' . $value["u_dni"] . '"
                    data-gender="' . $value["u_gender"] . '"
                    data-email="' . decryption($value["u_email"]) . '"
                    data-user="' . decryption($value["u_user"]) . '"
                    data-password="' . decryption($value["u_password"]) . '"
                    data-rol="' . $value["r_name"] . '"
                    data-rolid="' . $value["role_id"] . '"
                    data-profile="' . $profile . '"                    
                    data-profile-update="' . $value["u_profile"] . '"
                    data-registrationDate="' . dateFormat($value["u_registrationDate"]) . '"
                    data-status="' . $value["u_status"] . '"
                    data-updateDate="' . dateFormat($value["u_updateDate"]) . '"
                ><i class="fa fa-pencil"></i></button>
                <button class="btn btn-info report-item" type="button"
                    data-id="' . $value["idUser"] . '"
                    data-fullname="' . $value["u_fullname"] . '"
                    data-dni="' . $value["u_dni"] . '"
                    data-gender="' . $value["u_gender"] . '"
                    data-email="' . decryption($value["u_email"]) . '"
                    data-user="' . decryption($value["u_user"]) . '"
                    data-password="' . decryption($value["u_password"]) . '"
                    data-rol="' . $value["r_name"] . '"
                    data-profile="' . $profile . '"
                    data-registrationDate="' . dateFormat($value["u_registrationDate"]) . '"
                    data-status="' . $value["u_status"] . '"
                    data-updateDate="' . dateFormat($value["u_updateDate"]) . '"
                    data-attemp="' . $value['u_login_attempts'] . '"
                    data-token="' . $value['u_reset_token_password'] . '"
                    data-url="' . base_url() . "/im/login/pwreset/" . $value['u_reset_token_password'] . '"
                    data-status-link="' . $statusLink . '"
                    data-folder-name="' . $value['u_personal_folder_name'] . '"
                    data-storage-used="0"
                    data-storage-available="' . $value['u_space_limit'] . '"
                ><i class="fa fa-user"></i></button>
                 <a href="' . base_url() . '/im/pdf/user/' . encryption($value['idUser']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                <button class="btn btn-danger delete-item" data-id="' . $value["idUser"] . '" data-img="' . encryption($value["u_profile"]) . '"  data-fullname="' . $value["u_fullname"] . '" ><i class="fa fa-remove"></i></button>
                               </div>
                '; //Botones de acciones
            } else {
                //validamos si el usuario que inicio sesion es el administrador
                if ($_SESSION['login_info']['idUser'] == 1) {
                    $btns = $resetPw . '<button class="btn btn-success update-item" type="button"
                    data-id="' . $value["idUser"] . '"
                    data-fullname="' . $value["u_fullname"] . '"
                    data-dni="' . $value["u_dni"] . '"
                    data-gender="' . $value["u_gender"] . '"
                    data-email="' . decryption($value["u_email"]) . '"
                    data-user="' . decryption($value["u_user"]) . '"
                    data-password="' . decryption($value["u_password"]) . '"
                    data-rol="' . $value["r_name"] . '"
                    data-rolid="' . $value["role_id"] . '"
                    data-profile="' . $profile . '"                    
                    data-profile-update="' . $value["u_profile"] . '"
                    data-registrationDate="' . dateFormat($value["u_registrationDate"]) . '"
                    data-status="' . $value["u_status"] . '"
                    data-updateDate="' . dateFormat($value["u_updateDate"]) . '"
                ><i class="fa fa-pencil"></i></button>
                
                <button class="btn btn-info report-item" type="button"
                    data-id="' . $value["idUser"] . '"
                    data-fullname="' . $value["u_fullname"] . '"
                    data-dni="' . $value["u_dni"] . '"
                    data-gender="' . $value["u_gender"] . '"
                    data-email="' . decryption($value["u_email"]) . '"
                    data-user="' . decryption($value["u_user"]) . '"
                    data-password="' . decryption($value["u_password"]) . '"
                    data-rol="' . $value["r_name"] . '"
                    data-profile="' . $profile . '"
                    data-registrationDate="' . dateFormat($value["u_registrationDate"]) . '"
                    data-status="' . $value["u_status"] . '"
                    data-updateDate="' . dateFormat($value["u_updateDate"]) . '"
                       data-attemp="' . $value['u_login_attempts'] . '"
                    data-token="' . $value['u_reset_token_password'] . '"
                    data-url="' . base_url() . "/login/pwreset/" . $value['u_reset_token_password'] . '"
                    data-status-link="' . $statusLink . '"
                    data-folder-name="' . $value['u_personal_folder_name'] . '"
                    data-storage-used="0"
                    data-storage-available="' . $value['u_space_limit'] . '"
                ><i class="fa fa-user"></i></button>
                <a href="' . base_url() . '/im/pdf/user/' . encryption($value['idUser']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                ';
                } else {
                    $btns = '';
                }
                $arrData[$key]["actions"] = ' 
                 <div class="btn-group">                          
                    ' . $btns . '
                
                </div>                
                ';
            }
            $cont++;
        }
        echo json_encode($arrData);
    }
    /**
     * Funcion que permite el registro del usuario nuevo
     * @return void
     */
    public function setUser()
    {
        permissionInterface(3);

        //validacion del metodo POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar un usuario", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        //validacion si se elecicono el tipo de genero      
        validateFields(["txtGender", "txtFullName", "txtDNI", "txtUser", "txtEmail", "txtPassword", "slctRole", "rngStorage", "numStorage", "txtRootFolder"]);
        //limpiesa de los inputs
        $strFullName = strClean($_POST["txtFullName"]);
        $strDNI = strClean($_POST["txtDNI"]);
        $strGender = strClean($_POST["txtGender"]);
        $strUser = strClean($_POST["txtUser"]);
        $strEmail = strClean($_POST["txtEmail"]);
        $intRole = strClean($_POST["slctRole"]);
        $strPassword = strClean($_POST["txtPassword"]);
        $strProfile = ($_FILES) ? $_FILES["flPhoto"]["name"] : "";
        $rngStorage = strClean($_POST["rngStorage"]);
        $numStorage = strClean($_POST["numStorage"]);
        $txtRootFolder = ($_POST["txtRootFolder"] != "") ? strClean($_POST["txtRootFolder"]) : $strDNI;
        //validacion de campo si estan vacios       
        validateFieldsEmpty(array(
            "NOMBRES COMPLETOS" => $strFullName,
            "DNI" => $strDNI,
            "GENERO" => $strGender,
            "USUARIO" => $strUser,
            "CORREO ELECTRÓNICO" => $strEmail,
            "ROL" => $intRole,
            "CONTRASEÑA" => $strPassword,
            "CAPACIDAD DE ALMACENAMIENTO" => $rngStorage,
            "NÚMERO DE ALMACENAMIENTO" => $numStorage
        ));
        //validacion de los formatos de texto que se ingresa en los campos
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strFullName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre' presenta un formato inválido. Ingrese un texto válido conforme a los criterios establecidos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre' no cumple con el formato de texto requerido. Verifique que solo contenga caracteres válidos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("^\d{8}$", $strDNI)) {
            registerLog("El campo 'DNI' no cumple con el formato requerido. Verifique que contenga únicamente 8 dígitos numéricos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El valor ingresado en el campo 'DNI' no es válido. Se requiere un número compuesto por exactamente 8 dígitos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-ZáéíóúÁÉÍÓÚñÑ]+", $strGender)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Género' presenta un formato inválido. Seleccione una opción válida para completar el registro del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Género' contiene un valor no válido. Seleccione una opción conforme al formato establecido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Usuario' presenta un formato inválido. Ingrese un valor alfanumérico conforme a los criterios establecidos para el registro.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Usuario' no cumple con el formato requerido. Asegúrese de ingresar un texto válido conforme a los criterios definidos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Correo electrónico' presenta un formato inválido. Ingrese una dirección de correo válida para completar el registro.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido. Verifique que la dirección ingresada sea correcta y cumpla con el estándar requerido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validación que la contraseña pueda ingresar minimo 8 caracteres
        if (strlen($strPassword) < 8) {
            registerLog("Ocurrió un error inesperado", "La contraseña debe contener como mínimo 8 caracteres para completar el registro del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La contraseña ingresada debe tener una longitud mínima de 8 caracteres.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que los valores de capacidad de almacenamiento sea numericos y nuermo de almacenamiento
        if (!is_numeric($rngStorage) || !is_numeric($numStorage)) {
            registerLog("Ocurrió un error inesperado", "Los campos de capacidad de almacenamiento y número de almacenamiento deben ser numéricos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Los campos de capacidad de almacenamiento y número de almacenamiento deben ser numéricos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que estos valores sean iguales entre si
        if ($rngStorage != $numStorage) {
            registerLog("Ocurrió un error inesperado", "Los campos de capacidad de almacenamiento y número de almacenamiento deben ser iguales.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Los campos de capacidad de almacenamiento y número de almacenamiento deben ser iguales.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el campo del nombre de la carpeta tenga la estructura correcta para una carpeta
        if (verifyData("[a-zA-Z0-9_-]{3,15}", $txtRootFolder)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' presenta un formato inválido. Ingrese un nombre válido para la carpeta del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' no cumple con el formato requerido. Asegúrese de ingresar un nombre válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el dni del usuario no exista en la base de datos
        $request = $this->model->select_user_by_dni($strDNI);
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El DNI ingresado ya se encuentra registrado en el sistema. Verifique que el número de documento sea correcto.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El número de documento ingresado ya se encuentra registrado en el sistema. Verifique que el número de documento sea correcto.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //destruimos la variable de la solicitud
        unset($request);
        $request = $this->model->select_user_by_email(encryption($strEmail));
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El correo electrónico ingresado ya se encuentra registrado en el sistema. Verifique que la dirección de correo sea correcta.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El correo electrónico ingresado ya se encuentra registrado en el sistema. Verifique que la dirección de correo sea correcta.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //destruimos la variable de la solicitud
        unset($request);
        $request = $this->model->select_user_by_user(encryption($strUser));
        if ($request) {
            registerLog("Ocurrió un error inesperado", "El nombre de usuario ingresado ya se encuentra registrado en el sistema. Verifique que el nombre de usuario sea correcto.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El nombre de usuario ingresado ya se encuentra registrado en el sistema. Verifique que el nombre de usuario sea correcto.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //destruimos la variable de la solicitud
        unset($request);
        //Encriptando información sensible
        $strUser = encryption($strUser); //encrypt user
        $strPassword = encryption($strPassword); //encrypt password
        $strEmail = encryption($strEmail); //encrypt email
        //convertimos a mayusculas el nombre completo
        $strFullName = strtoupper($strFullName);
        //proceso de subida de la imagen de perfil al servidor
        if (isset($_FILES["flPhoto"]["name"]) && !empty($_FILES["flPhoto"]["name"])) {
            //Valdiacion de  tipos de imagen para subir
            if (isFile("image", $_FILES["flPhoto"], ["jpg", "png", "jpeg"])) {
                registerLog("Ocurrió un error inesperado", "Solo se permiten imágenes en formato JPEG, JPG o PNG para la foto de perfil del usuario.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El formato de la imagen no es válido. Solo se permiten archivos en formato JPEG, JPG o PNG.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //Validacion de que la ruta de la imagen exista y si que se cree
            $ruta = getRoute() . "Profile/Users/";
            if (verifyFolder($ruta, 0777, true)) {
                registerLog("Información sobre la carga de archivos", "El usuario subió una foto de perfil y se validó correctamente la existencia de la ruta especificada para almacenar el archivo.", 3, $_SESSION['login_info']['idUser']);
            }
            //preparamos la ruta final para subir al servidor
            $nameFinalPhoto = uniqid() . "." . pathinfo($_FILES["flPhoto"]['name'], PATHINFO_EXTENSION);
            $rutaFinal = $ruta . $nameFinalPhoto;
            //Subimos la imagen al servidor
            $request_moveFile = resizeAndCompressImage($_FILES["flPhoto"]["tmp_name"], $rutaFinal, 0.8, 100, 100);
            if (!$request_moveFile) {
                registerLog("Error en la carga de imagen", "No se pudo subir la foto de perfil durante el registro del usuario. Verifique el formato y tamaño del archivo.", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se pudo completar la carga de la foto de perfil. Verifique que el archivo cumpla con el formato y tamaño permitidos.",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            }
            $strProfile = $nameFinalPhoto; //se asigna el nombre de la imagen a la variable
        }
        $request = $this->model->insert_user($strUser, $strPassword, $strEmail, $strProfile, $strFullName, $strGender, $strDNI, $intRole, $numStorage, $txtRootFolder); //insert user in database
        if ($request > 0) {
            //registro de notificacion para el usuario registrado
            setNotification(
                $request,
                '¡Bienvenido(a) al sistema,  ' . $strFullName . '!',
                encryption("Nos alegra tenerte con nosotros. Ya puedes acceder con tu usuario y contraseña para comenzar a disfrutar de todas las funcionalidades que hemos preparado para ti. Explora el sistema, personaliza tu perfil y si necesitas ayuda, no dudes en contactarnos. ¡Éxitos en esta nueva experiencia!"),
                1,
                "success",
                "success",
                "fa-bullhorn"
            );
            /**
             * =================================================================================================================================================
             * validacion de creacion de rutas
             * =================================================================================================================================================
             */
            //validamos la creacion de las carpetas
            $url = getRoute();
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta raiz Storage no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            $url .= "data";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta data data no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            $url .= "/root";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta root data no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            $url .= "/" . $txtRootFolder;
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta raiz del usuario $txtRootFolder no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            //una ves creada la carpeta insertamos los valores en la tabla para crear la carpeta
            require_once "./Models/ClustModel.php";
            $obj = new ClustModel();
            $requestInsertFolder = $obj->insert_folder($request, $txtRootFolder, 1);
            if (!$requestInsertFolder) {
                registerLog("Error al crear carpeta", "No se pudo crear la carpeta para el usuario.", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se pudo crear la carpeta para el usuario. Pero la data del usuario si se logro registrar",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            unset($obj);
            /**
             * =================================================================================
             * Finalizacion de creacion de rutas
             * =================================================================================
             */
            //registro de logs
            registerLog("Registro exitoso", "El usuario ha sido registrado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El usuario fue registrado satisfactoriamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            //registro de logs
            registerLog("Ocurrió un error inesperado", "No se pudo completar el registro del usuario. Por favor, intente nuevamente o contacte al soporte técnico.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El usuario no se ha registrado correctamente en el sistema",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Funcion que se encarga de eliminar un usuario
     * @return void
     */
    public function deleteUser()
    {
        permissionInterface(3);

        //Validacion de que el metodo sea DELETE     
        if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
            registerLog("Ocurrió un error inesperado", "No se encontró el método DELETE durante el intento de eliminar un usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método DELETE no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //capturamos la solicitud enviada
        $request = json_decode(file_get_contents("php://input"), true); //convertimos la solicitud a un array
        //validacion isCsrf
        isCsrf($request["token"]);
        //validamos que la soslicitud tenga los campos necesarios
        $id = strClean($request["id"]);
        $fullName = strClean($request["fullname"]);
        $img = strClean($request["img"]);
        //validamos que los campos no esten vacios
        if ($id == "" || $img == "") {
            registerLog("Ocurrió un error inesperado", "El ID del usuario es obligatorio para completar el proceso de eliminación.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del usuario es requerido. Por favor, actualice la página e intente nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que solo ce acepte numeros en el campo id
        if (!is_numeric($id)) {
            registerLog("Ocurrió un error inesperado", "El ID del usuario debe ser un valor numérico válido para realizar la eliminación.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del usuario debe ser numérico. Por favor, actualice la página e intente nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        ///validamos que el id del usuario exista en la base de datos
        $result = $this->model->select_user_by_Id($id);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede eliminar el usuario, ya que el ID proporcionado no existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del usuario no existe. Por favor, actualice la página y vuelva a intentarlo.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Realizamos la eliminacion del usuario, en la base de datos
        $request = $this->model->delete_user($id);
        if ($request > 0) {
            //Pprocedemos a eliminar la imagen del usuario
            $img = decryption($img);
            if ($img != null) {
                $ruta = getRoute() . "Profile/Users";
                if (delFolder($ruta, $img)) {
                    registerLog("Atención", "No se pudo eliminar la imagen del usuario durante el proceso de eliminación. Sin embargo, el usuario fue eliminado correctamente. Es posible que el archivo de imagen no exista en el sistema.", 3, $_SESSION['login_info']['idUser']);
                    $data = array(
                        "title" => "Atención",
                        "message" => "No se logró eliminar la imagen de perfil del usuario; sin embargo, el registro del usuario fue eliminado correctamente.",
                        "type" => "info",
                        "status" => true
                    );
                    toJson($data);
                }
            }
            //eliminamos la carpeta personal del usuario
            $ruta = getRoute() . "data/root/" . $result["u_personal_folder_name"];
            if (delFolder($ruta, "*", true)) {
                registerLog("Atención", "No se pudo eliminar la carpeta personal del usuario durante el proceso de eliminación. Sin embargo, el usuario fue eliminado correctamente.", 3, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Atención",
                    "message" => "No se logró eliminar la carpeta personal del usuario; sin embargo, el registro del usuario fue eliminado correctamente.",
                    "type" => "info",
                    "status" => true
                );
                toJson($data);
            }
            registerLog("Eliminación exitosa", "El usuario con ID {$id} y nombre {$fullName} fue eliminado satisfactoriamente del sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Eliminación exitosa",
                "message" => "El usuario con ID '{$id}' y nombre '{$fullName}' ha sido eliminado correctamente del sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No fue posible eliminar al usuario con ID '{$id}' y nombre '{$fullName}'.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar la eliminación del usuario con ID '{$id}' y nombre '{$fullName}'.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Funcion que se encarga de actualizar un usuario
     * @return void
     */
    public function updateUser()
    {
        permissionInterface(3);
        //validacion del metodo POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "No se encontró el método POST al momento de actualizar un usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF        
        /*
         *Proceso de validacion de campos que existan en el formulario
         *y que no esten vacios, si el campo no existe
         */
        if ($_POST["update_txtId"] != 1) {
            if (!isset($_POST["update_slctRole"])) {
                registerLog("Ocurrió un error inesperado", "El campo 'Rol' es obligatorio al momento de actualizar un usuario.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El campo 'Rol' es obligatorio.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        validateFields(["update_txtGender", "update_slctStatus", "update_txtFullName", "update_txtDNI", "update_txtUser", "update_txtEmail", "update_txtPassword", "update_txtFotoActual"]);
        //limpiesa de los inputs
        $intId = strClean($_POST["update_txtId"]);
        $strFullName = strClean($_POST["update_txtFullName"]);
        $strDNI = strClean($_POST["update_txtDNI"]);
        $strGender = strClean($_POST["update_txtGender"]);
        $strUser = strClean($_POST["update_txtUser"]);
        $strEmail = strClean($_POST["update_txtEmail"]);
        $intRole = ($intId == 1) ? 1 : strClean($_POST["update_slctRole"]);
        $strPassword = strClean($_POST["update_txtPassword"]);
        $strFotoActual = $_POST["update_txtFotoActual"];
        $strProfile = ($_FILES) ? $_FILES["update_flPhoto"]["name"] : "";
        $slctStatus = strClean($_POST["update_slctStatus"]);
        //validacion de campo si estan vacios        
        validateFieldsEmpty(array(
            "NOMBRES COMPLETOS" => $strFullName,
            "ESTADO" => $slctStatus,
            "DNI" => $strDNI,
            "GÉNERO" => $strGender,
            "USUARIO" => $strUser,
            "CORREO ELECTRÓNICO" => $strEmail,
            "ROL" => $intRole,
            "CONTRASEÑA" => $strPassword
        ));
        //validacion que el id sea numerico
        if (!is_numeric($intId)) {
            registerLog("Ocurrió un error inesperado", "El ID debe ser un valor numérico válido al momento de actualizar un usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID debe ser numérico. Refresque la página y vuelva a intentarlo.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion de los formatos de texto que se ingresa en los campos
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strFullName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre' no cumple con el formato de texto requerido al momento de actualizar un usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre' no presenta un formato de texto válido. Verifique que solo contenga caracteres permitidos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("^\d{8}$", $strDNI)) {
            registerLog("Ocurrió un error inesperado", "El campo 'DNI' no cumple con el formato requerido al momento de actualizar un usuario. Asegúrese de ingresar exactamente 8 dígitos numéricos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'DNI' no cumple con el formato válido. Debe contener exactamente 8 dígitos numéricos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-ZáéíóúÁÉÍÓÚñÑ]+", $strGender)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Género' no cumple con el formato requerido al momento de actualizar un usuario. Seleccione una opción válida.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Género' no cumple con el formato válido. Seleccione una opción permitida del listado.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Usuario' no cumple con el formato de texto requerido al momento de actualizar un usuario. Ingrese un valor alfanumérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Usuario' no cumple con el formato válido. Ingrese un valor alfanumérico conforme a los criterios establecidos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Correo electrónico' no cumple con el formato requerido al momento de actualizar un usuario. Ingrese una dirección válida, por ejemplo: usuario@dominio.com.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido. Ingrese una dirección en el formato correcto, por ejemplo: usuario@dominio.com.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que la contraseña pueda ingresar minimo 8 caracteres
        if (strlen($strPassword) < 8) {
            registerLog("Ocurrió un error inesperado", "La contraseña debe tener como mínimo 8 caracteres al momento de actualizar un usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La contraseña debe contener al menos 8 caracteres para ser válida.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que usuario exista en la base de datos
        $result = $this->model->select_user_by_Id($intId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario, ya que el ID proporcionado no existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del usuario no existe. Por favor, actualice la página y vuelva a intentarlo.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el dni del usuario no exista en la base de datos
        $requestForDni = $this->model->select_user_by_dni($strDNI);
        if ($requestForDni) {
            if ($requestForDni['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario, ya que el DNI proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El DNI del usuario ya existe. Por favor, ingrese un DNI diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //validamos que el correo electronico no se duplique con otro usuarios
        $requestForEmail = $this->model->select_user_by_email(encryption($strEmail));
        if ($requestForEmail) {
            if ($requestForEmail['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario , ya que el correo electrónico proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El correo electrónico del usuario ya existe. Por favor, ingrese un correo electrónico diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //validamos que el usuario no exista en la base de datos
        $requestForUser = $this->model->select_user_by_user(encryption($strUser));
        if ($requestForUser) {
            if ($requestForUser['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario, ya que el usuario proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El usuario ya existe. Por favor, ingrese un usuario diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }

        //Encriptando informacion sensible       
        if ($strPassword == "") {
            $strPassword = $result['u_password'];
        } else {
            $strPassword = encryption($strPassword); //encrypt user
        }
        //Encriptando informacion sensible
        $strUser = encryption($strUser); //encrypt user
        $strEmail = encryption($strEmail); //encrypt email
        //proceso de subida de la imagen de perfil al servidor
        if (isset($_FILES["update_flPhoto"]["name"]) && !empty($_FILES["update_flPhoto"]["name"])) {
            //Validacion de  tipos de imagen para subir
            if (isFile("image", $_FILES["update_flPhoto"], ["jpg", "png", "jpeg"])) {
                registerLog("Ocurrió un error inesperado", "Solo se permiten imágenes en formato JPEG, JPG o PNG para la foto de perfil del usuario.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El formato de la imagen no es válido. Asegúrese de utilizar archivos en formato JPEG, JPG o PNG.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //Validacion de que la ruta de la imagen exista y si que se cree
            $ruta = getRoute() . "Profile/Users/";
            if (verifyFolder($ruta, 0777, true)) {
                registerLog("Información sobre la carga de archivos", "El usuario subió una foto de perfil y se verificó que la ruta de destino para almacenar el archivo exista correctamente.", 3, $_SESSION['login_info']['idUser']);
            }
            //preparamos la ruta final para subir al servidor
            $nameFinalPhoto = uniqid() . "." . pathinfo($_FILES["update_flPhoto"]['name'], PATHINFO_EXTENSION);
            $rutaFinal = $ruta . $nameFinalPhoto;
            //Subimos la imagen al servidor
            $request_moveFile = resizeAndCompressImage($_FILES["update_flPhoto"]["tmp_name"], $rutaFinal, 0.8, 100, 100);
            if (!$request_moveFile) {
                registerLog("Registro exitoso", "El usuario fue actualizado correctamente, pero no se logró subir la foto de perfil.", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se pudo completar la carga de la foto de perfil. Verifique el formato y tamaño del archivo e intente nuevamente.",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            }
            //eliminar la imagen anterior
            $ruta = getRoute() . "Profile/Users";
            if (delFolder($ruta, $strFotoActual)) {
                registerLog("Atención", "No se pudo eliminar la imagen anterior del usuario durante el proceso de eliminación.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se logró eliminar la imagen de perfil. Verifique si el archivo existe o si hay permisos insuficientes.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            $strProfile = $nameFinalPhoto; //se asigna el nombre de la imagen a la variable
        } else {
            $strProfile = $strFotoActual; //se asigna el nombre de la imagen a la variable
        }
        //Proceso de actualizacion de datos
        $request = $this->model->update_user($intId, $strUser, $strPassword, $strEmail, $strProfile, $strFullName, $strGender, $strDNI, $intRole, $slctStatus); //insert user in database
        if ($request > 0) {
            /**
             * Validamos la creacion de la carpeta por si no se haya creado cuando se registro, esto se realizara cada ves que se actualiza
             */
            $infoUser = $this->model->select_user_by_Id($intId);
            //validamos la creacion de las carpetas
            $url = getRoute();
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta raiz Storage no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            $url .= "data";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta data data no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            $url .= "/root";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta root data no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }

            $url .= "/" . $infoUser['u_personal_folder_name'];
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Informacion de creacion de ruta", "La carpeta raiz del usuario {$infoUser['u_personal_folder_name']} no estaba creada, por lo que se procedio a crear", 3, $_SESSION['login_info']['idUser']);
            }
            //mensaje de correccion
            registerLog("Registro exitoso", "El usuario se ha actualizado correctamente en el sistema.", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El usuario ha sido actualizado correctamente en el sistema.",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "No se pudo completar la actualización del usuario. Por favor, intente nuevamente o contacte al soporte técnico.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se pudo completar el registro del usuario. Por favor, verifique los datos e intente nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * metodo que se encarga de actualizar el perfil del usuario
     * @return never
     */
    public function updateProfile(): void
    {
        permissionInterface(7);
        //validacion del metodo POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "No se detectó el método POST al intentar actualizar un usuario. Verifique el tipo de solicitud enviada.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf(); //validacion de ataque CSRF
        /**
         * Proceso de actualizado de perfil de un usuario
         */
        validateFields(["update_txtGender", "update_txtFullName", "update_txtDNI", "update_txtUser", "update_txtEmail", "update_txtPassword"]);
        //recibimos los datos del formulario
        $intId = $_SESSION['login_info']['idUser']; //id del usuario
        $strFullName = strClean($_POST["update_txtFullName"]);
        $strDNI = strClean($_POST["update_txtDNI"]);
        $strGender = strClean($_POST["update_txtGender"]);
        $strUser = strClean($_POST["update_txtUser"]);
        $strEmail = strClean($_POST["update_txtEmail"]);
        $strProfile = ($_FILES) ? $_FILES["update_flPhoto"]["name"] : "";
        $strPassword = strClean($_POST["update_txtPassword"]);
        $strFotoActual = $_POST["update_txtFotoActual"];
        //validamos que los campos esten llenos
        //validamos que los campos no esten vacios
        validateFieldsEmpty(array(
            "CODIGO USUARIO MASTER" => $intId,
            "NOMBRE COMPLETO" => $strFullName,
            "DNI" => $strDNI,
            "GÉNERO" => $strGender,
            "USUARIO" => $strUser,
            "CORREO ELECTRÓNICO" => $strEmail
        ));
        //validacion de los formatos de texto que se ingresa en los campos
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strFullName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre' no cumple con el formato de texto requerido al actualizar el usuario. Verifique que solo contenga caracteres válidos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre' no tiene un formato válido. Asegúrese de ingresar solo caracteres alfabéticos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("^\d{8}$", $strDNI)) {
            registerLog("Ocurrió un error inesperado", "El campo 'DNI' no cumple con el formato requerido al actualizar un usuario. Asegúrese de ingresar exactamente 8 dígitos numéricos.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'DNI' no tiene un formato válido. Debe contener exactamente 8 dígitos numéricos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-ZáéíóúÁÉÍÓÚñÑ]+", $strGender)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Género' no cumple con el formato requerido al momento de actualizar un usuario. Seleccione una opción válida del listado.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Género' no tiene un formato válido. Seleccione una opción permitida del listado.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Usuario' no cumple con el formato requerido al momento de actualizar un usuario. Ingrese un valor alfanumérico válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Usuario' no tiene un formato válido. Ingrese un valor alfanumérico conforme a los criterios establecidos.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Correo electrónico' no cumple con el formato requerido al momento de actualizar un usuario. Ingrese una dirección válida, como ejemplo@dominio.com.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido. Ingrese una dirección en el formato correcto, por ejemplo: usuario@dominio.com.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que la contraseña pueda ingresar minimo 8 caracteres Fañtaaaaaa
        if ($strPassword != "" && strlen($strPassword) < 8) {
            registerLog("Ocurrió un error inesperado", "La contraseña debe tener al menos 8 caracteres, al momento de actualizar un usuario", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La contraseña debe tener al menos 8 carácteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        //validacion que usuario exista en la base de datos
        $result = $this->model->select_user_by_Id($intId);
        if (!$result) {
            registerLog("Ocurrió un error inesperado", "No se podrá actualizar el perfil del usuario, ya que el ID no existe en la base de datos", 1, $_SESSION['login_info']['idUser']);

            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del usuario no existe, refresque la página y vuelva a intentarlo",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el dni del usuario no exista en la base de datos
        $request = $this->model->select_user_by_dni($strDNI);
        if ($request) {
            if ($request['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario, ya que el DNI proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El DNI del usuario ya existe. Por favor, ingrese un DNI diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //validamos que el correo electronico no se duplique con otro usuarios
        $request = $this->model->select_user_by_email(encryption($strEmail));
        if ($request) {
            if ($request['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario , ya que el correo electrónico proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El correo electrónico del usuario ya existe. Por favor, ingrese un correo electrónico diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //validamos que el usuario no exista en la base de datos
        $request = $this->model->select_user_by_user(encryption($strUser));
        if ($request) {
            if ($request['idUser'] != $intId) {
                registerLog("Ocurrió un error inesperado", "No se puede actualizar el usuario, ya que el usuario proporcionado ya existe en la base de datos.", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El usuario ya existe. Por favor, ingrese un usuario diferente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
        //Encriptando informacion sensible       
        if ($strPassword == "") {
            $strPassword = $result['u_password'];
        } else {
            $strPassword = encryption($strPassword); //encrypt user
        }
        $strUser = encryption($strUser); //encrypt user
        $strEmail = encryption($strEmail); //encrypt email
        //proceso de subida de la imagen de perfil al servidor
        if (isset($_FILES["update_flPhoto"]["name"]) && !empty($_FILES["update_flPhoto"]["name"])) {
            //Validacion de  tipos de imagen para subir
            if (isFile("image", $_FILES["update_flPhoto"], ["jpg", "png", "jpeg"])) {
                registerLog("Ocurrió un error inesperado", "Para la foto de perfil, únicamente se admiten archivos en formato JPEG, JPG o PNG", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "Este tipo de archivo de imagen no está permitido. Por favor, usa uno con un formato válido",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //validacion de tamaño permitido para imagen
            $sizeFile = $_FILES["update_flPhoto"]["size"];
            if (valConvert($sizeFile)["Mb"] > 2) {
                registerLog("Ocurrió un error inesperado", "La imagen seleccionada es demasiado grande. El tamaño máximo permitido para la foto de perfil es de 2 MB", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La imagen excede el tamaño permitido. Por favor, sube una imagen que no supere los 2 MB",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //Validacion de que la ruta de la imagen exista y si que se cree
            $ruta = getRoute() . "Profile/Users/";
            if (verifyFolder($ruta, 0777, true)) {
                registerLog("Información de subida de archivos", "El usuario cargó una foto de perfil, y se validó que la ruta especificada para subir el archivo sea correcta", 3, $_SESSION['login_info']['idUser']);
            }
            //preparamos la ruta final para subir al servidor
            $nameFinalPhoto = uniqid() . "." . pathinfo($_FILES["update_flPhoto"]['name'], PATHINFO_EXTENSION);
            $rutaFinal = $ruta . $nameFinalPhoto;
            //Subimos la imagen al servidor
            $request_moveFile = resizeAndCompressImage($_FILES["update_flPhoto"]["tmp_name"], $rutaFinal, 0.5, 100, 100);
            if (!$request_moveFile) {
                registerLog("Registro exitoso", "No se logró cargar la foto de perfil durante el proceso de actualización del perfil del usuario", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se logró subir la foto de perfil",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            }
            //eliminar la imagen anterior
            $ruta = getRoute() . "Profile/Users";
            if (delFolder($ruta, $strFotoActual)) {
                registerLog("Atención", "No fue posible eliminar la imagen anterior del usuario durante la actualización de su perfil", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se logró proceder con la eliminación de la imagen de perfil",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            $strProfile = $nameFinalPhoto; //se asigna el nombre de la imagen a la variable
        } else {
            $strProfile = $strFotoActual; //se asigna el nombre de la imagen a la variable
        }

        //proceso de actualizacion de datos
        $request = $this->model->update_user($intId, $strUser, $strPassword, $strEmail, $strProfile, $strFullName, $strGender, $strDNI); //insert user in database
        if ($request > 0) {
            registerLog("Registro exitoso", "El perfil del usuario se actualizó correctamente al realizar la modificación de sus datos", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Registro exitoso",
                "message" => "El perfil del usuario se ha actualizado correctamente. Por favor, cierre sesión para que los cambios sean aplicados",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "El perfil del usuario no se ha actualizado correctamente. Por favor, intente nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El registro del usuario no se ha completado correctamente. Por favor, intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que que te permite obtener los usuarios activos del sistema
     * @return never
     */
    public function get_users_active_for_notification(): void
    {
        permissionInterface(3);
        $arrData = $this->model->select_users();
        foreach ($arrData as $key => $value) {
            if ($value['u_status'] === 'Activo') {
                $arrResult[] = [
                    "id" => $value['idUser'], // o el campo ID correcto
                    "text" => $value['r_name'] . " - " . $value['u_fullname'] // o como desees mostrarlo
                ];
            }
        }
        toJson($arrResult);
    }
}
