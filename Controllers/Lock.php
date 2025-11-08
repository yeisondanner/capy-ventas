<?php
class Lock extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lock()
    {
        isSession();
        $data['page_id'] = 20;
        permissionInterface($data['page_id']);
        $data['page_title'] = "Bloqueo de pantalla";
        $data['page_description'] = "Vista de bloqueo de pantalla";
        $data['page_container'] = "Lock";
        $data['page_js_css'] = "lock";
        registerLog("Información de navegación", "El usuario entró a :" . $data['page_title'], 3, $_SESSION['login_info']['idUser']);
        $this->views->getView($this, "lock", $data);
    }
    /**
     * Funcion que se encarga de desbloquear el inicio de sesion del usuario
     * @return never
     */
    public function unLockLogin(): void
    {
        isSession();
        //validacion del Método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento desbloquear la sesión", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //limpieza de los inputs
        $txtUser = strClean($_POST["txtUser"]);
        $txtPassword = strClean($_POST["txtPassword"]);
        //validacion de campos vacios
        if ($txtUser == "" || $txtPassword == "") {
            registerLog("Ocurrió un error inesperado", "Todos los campos de login deben estar llenos", 1);
            $data = array(
                "title" => "Error",
                "message" => "Todos los campos son obligatorios",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Validacion de usuario, solo debe soporte minimo 3 caracteres
        if (strlen($txtUser) < 3) {
            registerLog("Ocurrió un error inesperado", "El usuario debe tener al menos 3 caracteres para poder ingresar al sistema", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El usuario debe tener al menos 3 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validacion que la contraseña pueda ingresar minimo 8 caracteres
        if (strlen($txtPassword) < 8) {
            registerLog("Ocurrió un error inesperado", "La contraseña debe tener al menos 8 caracteres para ingresar al sistema", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La contraseña debe tener al menos 8 caracteres",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //Encriptacion de la informacion
        $txtUser = encryption($txtUser);
        $txtPassword = encryption($txtPassword);
        //requerimos el modelo de login
        require_once "./Models/LoginModel.php";
        $obj = new LoginModel();
        $request = $obj->selectUserLogin($txtUser, $txtPassword);
        if ($request) {
            if ($request["u_status"] == "Inactivo") {
                registerLog("Ocurrió un error inesperado", "El usuario " . $request["u_fullname"] . ", no inicio sesion por motivo de cuenta desactivada", 1, $request["idUser"]);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "La cuenta del usuario actualmente se encuentra en estado Inactivo",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //creamos las variables de session para el usuario
            $data_session = array(
                "idUser" => $request["idUser"],
                "user" => $request["u_user"],
                "email" => $request["u_email"],
                "profile" => $request["u_profile"],
                "fullName" => $request["u_fullname"],
                "gender" => $request["u_gender"],
                "dni" => $request["u_dni"],
                "status" => $request["u_status"],
                "registrationDate" => $request["u_registrationDate"],
                "updateDate" => $request["u_updateDate"],
                "role_id" => $request["role_id"],
                "role" => $request["r_name"]
            );
            $data_session = json_encode($data_session);
            $_SESSION['login'] = true;
            $_SESSION['login_info'] = json_decode($data_session, true);
            //creamos las cookies para el usuario
            setcookie("login_info", $data_session, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
            setcookie("login", true, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
            $_SESSION['last_activity_time'] = time(); // update last activity timestamp
            registerLog("Inicio de sesion exitoso", "El usuario " . $request["u_fullname"] . ", completo de manera satisfactoria el desbloqueo del sistema", 2, $request["idUser"]);
            $data = array(
                "title" => "Inicio de sesión exitoso",
                "message" => "Hola " . $request["u_fullname"] . ", se completo de manera satisfactoria el desbloqueo del sistema",
                "type" => "success",
                "status" => true,
                "redirection" => base_url() . "/dashboard"
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "El usuario {$txtUser} o contraseña {$txtPassword} que esta intentando ingresar no existe", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "La cuenta de usuario no existe",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Funcion que valida el tiempo de inactividad del usuario
     * @param mixed $max_inactive_seconds
     * @return void
     */
    public function checkSessionTimeout()
    {
        isSession();
        $time_in_seconds = getSystemInfo()["c_duration_lock"];
        //validamos si no esta en cero
        if ($time_in_seconds == 0) {
            //depaso destruimos la variable de sesion por si se alla inicializado
            unset($_SESSION['last_activity_time']);
            toJson([
                "status" => "success",
                "message" => "El sistema no esta configurado para bloquear la cuenta por inactividad",
            ]);
            return;
        }
        $max_inactive_seconds = $time_in_seconds;
        isSession();
        // Validar si la sesión está activa
        if (isset($_SESSION['last_activity_time'])) {
            $inactive_duration = time() - $_SESSION['last_activity_time'];
            if ($inactive_duration > $max_inactive_seconds) {
                // Registrar log
                registerLog("Información de navegación", "Cuenta de usuario bloqueada por inactividad", 3, $_SESSION['login_info']['idUser']);
                toJson([
                    "status" => "expired",
                    "url" => base_url() . "/Lock",
                    "message" => "Sesión expirada por inactividad"
                ]);
            }
            if (($inactive_duration / 60) >= 1) {
                registerLog("Tiempo de inactividad", "El usuario ha estado inactivo durante -Tiempo inactivo: " . floor($inactive_duration / 60) . " minutos y " . ($inactive_duration % 60) . " segundos.", 3, $_SESSION['login_info']['idUser']);

            }
            echo json_encode([
                "status" => "active",
                "message" => "Tiempo inactivo: " . floor($inactive_duration / 60) . " minutos y " . ($inactive_duration % 60) . " segundos."
            ]);
        } else {
            echo json_encode([
                "status" => "new",
                "message" => "Primera actividad registrada en esta sesión."
            ]);
        }
        $_SESSION['last_activity_time'] = time();
    }
}