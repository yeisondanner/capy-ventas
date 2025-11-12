<?php
class LogOut extends Controllers
{
    public function __construct()
    {
        //preparamos las vairables a usar
        $urlReturn = base_url() . "/im/login";
        require_once "./Models/Admin/LoginModel.php";
        $obj = new LoginModel();
        session_start(config_sesion());

        if (isset($_SESSION['login'])) {
            if (!isset($_SESSION['login_info'])) {
                registerLog("Cierre de sesión", "Se cerro de manera forzada la sesion de los usuarios", 2);
                //actualizamos el estado de online del usuario
                //$obj->update_online_user($_SESSION['login_info']['idUser'], 0);
                session_unset();
                session_destroy();
                //destruimos las cookies
                setcookie(config_sesion()['name'], "", time() - 3600, "/"); // 86400 = 1 day
                setcookie('login_info', "", time() - 3600, "/"); // 86400 = 1 day
                setcookie("login", "", time() - 3600, "/"); // 86400 = 1 day 
                unset(config_sesion()['name']);

                header("Location: " . $urlReturn);
                die();
            }
            registerLog("Cierre de sesión", "El usuario " . $_SESSION['login_info']["fullName"] . " ha cerrado sesión en el sistema", 2, $_SESSION['login_info']['idUser']);
            //actualizamos el estado de online del usuario
            $obj->update_online_user($_SESSION['login_info']['idUser'], 0);
            //destruimos las variables de session
            session_unset();
            session_destroy();
            //destruimos las cookies
            setcookie(config_sesion()['name'], "", time() - 3600, "/"); // 86400 = 1 day
            setcookie('login_info', "", time() - 3600, "/"); // 86400 = 1 day
            setcookie("login", "", time() - 3600, "/"); // 86400 = 1 day 
            unset(config_sesion()['name']);
            header("Location: " . $urlReturn);
            die();
        }
        header("Location: " . base_url() . "/im/Errors/sessionexpired");
    }
}
