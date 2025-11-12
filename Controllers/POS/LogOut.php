<?php
class LogOut extends Controllers
{
    public function __construct()
    {
        session_start(config_sesion(1));
        //preparamos las vairables a usar
        $urlReturn = base_url() . "/pos/login";
        $nameSession = config_sesion(1)['name'];
        $nameVarLogin = 'login';
        $nameVarLoginInfo = 'login_info';
        if (isset($_SESSION[$nameVarLogin])) {
            if (!isset($_SESSION[$nameVarLoginInfo])) {
                //actualizamos el estado de online del usuario
                //$obj->update_online_user($_SESSION['login_info']['idUser'], 0);
                session_unset();
                session_destroy();
                //destruimos las cookies
                setcookie($nameSession, "", time() - 3600, "/"); // 86400 = 1 day
                setcookie($nameVarLoginInfo, "", time() - 3600, "/"); // 86400 = 1 day
                setcookie($nameVarLogin, "", time() - 3600, "/"); // 86400 = 1 day 
                unset($nameSession);

                header("Location: " . $urlReturn);
            }
            //destruimos las variables de session
            session_unset();
            session_destroy();
            //destruimos las cookies
            setcookie($nameSession, "", time() - 3600, "/"); // 86400 = 1 day
            setcookie($nameVarLoginInfo, "", time() - 3600, "/"); // 86400 = 1 day
            setcookie($nameVarLogin, "", time() - 3600, "/"); // 86400 = 1 day 
            unset($nameSession);
            header("Location: " . $urlReturn);
            die();
        }
        echo "Session expirada";
    }
}
