<?php
//Funcion de versionamiento
function versionSystem(): string
{
    return "v=" . VERSION_SISTEMA;
}
//Funcion que retorna el nombre del sistema
function getSystemName()
{
    return NOMBRE_SISTEMA;
}
//Retorla la url del proyecto
function base_url()
{
    return BASE_URL;
}
//Funcion que te permite devolver el tipo de moneda de la app
function getCurrency()
{
    return SMONEY;
}
//Funcion que devuelve la ruta de los archivos subidos al sistema
function getRoute()
{
    return RUTA_ARCHIVOS;
}
//Retorla la url de Assets
function media()
{
    return BASE_URL . "/Assets";
}
function headerAdmin($data = [])
{
    $view_header = "./Views/Template/panel/head.php";
    deleteSessionVariable($data);
    require_once($view_header);
}
function footerAdmin($data = "")
{
    $view_footer = "./Views/Template/panel/foot.php";
    require_once($view_footer);
}

function headerPos($data = [])
{
    $view_header = "./Views/Template/panelPos/head.php";
    deleteSessionVariable($data);
    require_once($view_header);
}
function footerPos($data = "")
{
    $view_footer = "./Views/Template/panelPos/foot.php";
    require_once($view_footer);
}

//Muestra información formateada
function dep($data)
{
    $format = print_r('<pre>');
    $format .= print_r($data);
    $format .= print_r('</pre>');
    return $format;
}
/**
 * Obtiene el nombre del remitente ya sea del config o de la basde de datos
 * @return mixed
 */
function getRemitente()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_REMITENTE;
    }
    return $request['c_email_sender_name'];
}
/**
 * Obtiene el email del remitente ya sea del config o de la basde de datos
 * @return mixed
 */
function getFrom()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_FROM;
    }
    return $request['c_email_sender'];
}
/**
 * Obtiene el nombre de la empresa  ya sea del config o de la basde de datos
 * @return mixed
 */
function getCompanyName()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return NOMBRE_COMPANIA;
    }
    return $request['c_company_name'];
}
/**
 * devuelve el host del correo
 */
function getHost()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_HOST;
    }
    return ($request['c_email_server_smtp']);
}
/**
 * Devuelve el puerto del correo
 */
function getPort()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_PORT;
    }
    return $request['c_email_port'];
}
/**
 * Devuelve el usuario del correo
 */
function getUser()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_USER;
    }
    return $request['c_email_user_smtp'];
}
/**
 * Devuelve la contraseña del usuario del correo
 */
function getPassword()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_PASSWORD;
    }
    return $request['c_email_password_smtp'];
}
/**
 * Devuelve la encriptacion del correo
 */
function getEncryption()
{
    $request = getSystemInfo();
    if (empty($request)) {
        return MAIL_ENCRYPTION;
    }
    return $request['c_email_encryption'];
}

/**
 * Envía un correo electrónico utilizando una plantilla HTML.
 *
 * @param array  $data     Datos necesarios para el envío del correo.
 *                         Debe incluir:
 *                         - 'asunto': Asunto del mensaje.
 *                         - 'email' : Dirección de correo del destinatario.
 * @param string $template Nombre de la plantilla HTML (sin extensión .php).
 *
 * @return bool Devuelve TRUE si el envío fue exitoso, FALSE en caso contrario.
 */
function sendEmail(array $config, array $params)
{
    require_once "./Libraries/Core/sendMail.php";
    /*  $config = [
          'smtp' => [
              'host' => 'mail.shaday-pe.com',
              'username' => 'pureba@shaday-pe.com',
              'password' => 'X5XFy46Qp?g_',
              'port' => 465,
              'encryption' => 'ssl' // ssl o tls
          ],
          'from' => [
              'email' => 'pureba@shaday-pe.com',
              'name' => 'Nombre Remitente'
          ]
      ];
      $params = [
          'to' => ['correo1@dominio.com', 'correo2@dominio.com'], // o string
          'subject' => 'Asunto del correo',
          'body' => '<h1>Hola</h1><p>Este es un correo.</p>',
          'attachments' => ['./documentos/archivo.pdf'] // opcional
      ];*/
    $objMail = new sendMail($config);
    $request = $objMail->send(params: $params);
    return $request; // TRUE o FALSE
}

/**
 * Limpia y sanitiza una cadena para prevenir ataques de XSS y SQL Injection.
 *
 * @param string $strCadena Cadena de texto a limpiar.
 * @return string Cadena limpia y segura.
 */
function strClean($strCadena)
{
    // 1. Normalizar espacios y eliminar espacios al inicio y final
    // Reemplaza múltiples espacios por uno solo y hace trim
    $string = preg_replace('/\s+/', ' ', trim($strCadena));

    // 2. Eliminar barras invertidas
    // Previene problemas con caracteres escapados como \"
    $string = stripslashes($string);

    // 3. Eliminar etiquetas HTML y PHP
    // Previene ejecución de código HTML/JS embebido
    $string = strip_tags($string);

    // 4. Definir patrones maliciosos comunes para eliminar
    // Incluye etiquetas <script>, consultas SQL peligrosas y símbolos de inyección
    $patrones = [
        '/<script\b[^>]*>(.*?)<\/script>/is', // Etiquetas <script> y su contenido
        '/(SELECT\s+.*\s+FROM)/i',            // Consultas SELECT
        '/(DELETE\s+FROM)/i',                 // Consultas DELETE
        '/(INSERT\s+INTO)/i',                 // Consultas INSERT
        '/(DROP\s+TABLE)/i',                  // Eliminación de tablas
        '/(UPDATE\s+.*\s+SET)/i',             // Consultas UPDATE
        '/(OR\s+\'1\'\s*=\s*\'1\')/i',        // Comparaciones siempre verdaderas con comillas simples
        '/(OR\s+"1"\s*=\s*"1")/i',            // Comparaciones siempre verdaderas con comillas dobles
        '/(OR\s+´1´\s*=\s*´1´)/i',            // Comparaciones siempre verdaderas con comillas latinas
        '/(--|#|\/\*)/i',                     // Comentarios SQL (--, #, /*)
        '/(\^|\[|\]|==)/',                    // Caracteres especiales peligrosos
        '/(LIKE\s+[\'"`´])/i'                  // Inyecciones usando LIKE
    ];

    // 5. Eliminar todos los patrones maliciosos detectados
    $string = preg_replace($patrones, '', $string);

    // 6. Codificar caracteres especiales en entidades HTML
    // Previene ejecución de código HTML/JS (ataques XSS)
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

    // 7. Devolver cadena limpia
    return $string;
}
//Genera una contraseña de 10 caracteres
function passGenerator($length = 10)
{
    $pass = "";
    $longitudPass = $length;
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $longitudCadena = strlen($cadena);

    for ($i = 1; $i <= $longitudPass; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}
//Genera un token
function token()
{
    $r1 = bin2hex(random_bytes(10));
    $r2 = bin2hex(random_bytes(10));
    $r3 = bin2hex(random_bytes(10));
    $r4 = bin2hex(random_bytes(10));
    $token = $r1 . '-' . $r2 . '-' . $r3 . '-' . $r4;
    return $token;
}
//Formato para valores monetarios
function formatMoney($cantidad)
{
    $cantidad = number_format($cantidad, 2, SPD, SPM);
    return $cantidad;
}
function activeItem($idPage, $idPageValue)
{
    if ($idPage == $idPageValue) {
        return "active";
    }
}
//funcion que se encarga de convertir la informacion a tipo JSON
function toJson($data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    die();
}
/**Funcion verificar datos*/
function verifyData($filtro, $cadena): bool
{
    if (preg_match('/^' . $filtro . '$/', $cadena)) {
        return false;
    } else {
        return true;
    }
}
/**Encriptar texto plano ah hash*/
function encryption($string)
{
    $output = FALSE;
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
    $output = base64_encode($output);
    return $output;
}
/**Desencripta de hash a texto plano */
function decryption($string): string
{
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
    return $output;
}
//function que registra logs en la base de datos del sistema
function registerLog($title, $description, $typeLog, $idUser = 0, string $table = "tb_user")
{
    require_once "./Models/Admin/LogsModel.php";
    $obj = new LogsModel();
    $obj->insert_log($title, $description, $typeLog, $idUser, $table);
}
/**
 * Funcion que validad ataque CSRF
 */
function isCsrf($token = "", int $session = 0)
{
    $table = "";
    //Validamos la sesion para obtener el nombre de la sesion
    if ($session === 0) {
        $name_sesion = config_sesion($session)['name'] ?? '';
        $nameVarToken = 'data_token';
        $nameVarLoginInfo =  'login_info';
        $table = "tb_user";
    } else if ($session === 1) {
        $name_sesion = config_sesion($session)['name'] ?? '';
        $nameVarToken = $name_sesion . 'data_token';
        $nameVarLoginInfo = $name_sesion . 'login_info';
        $table = "user_app";
    }
    //Validamos el token
    if ($token != "") {
        $_POST['token'] = $token;
    }
    if (isset($_POST['token'])) {
        if (isset($_SESSION[$nameVarToken])) {
            //validamos si el tiempo de expiracion del token es menor a 10 minutos
            $datetime = $_SESSION[$nameVarToken]["datatime"]; // get datetime from session
            $timeTranscurridos = dateDifference($datetime, date("Y-m-d H:i:s"))["total_minutos"];
            if ($timeTranscurridos > 10) {
                registerLog(
                    "Ocurrio un error inesperado",
                    "El token de seguridad ha expirado. Por favor, actualice la página para generar uno nuevo. Tenga en cuenta que el token tiene una vigencia máxima de 10 minutos.",
                    1,
                    $_SESSION[$nameVarLoginInfo]['idUser'],
                    $table
                );
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "Error: El token de seguridad ha expirado. Por favor, actualice la página para generar uno nuevo. Tenga en cuenta que el token tiene una vigencia máxima de 10 minutos.",
                    "type" => "error",
                    "icon" => "error",
                    "status" => false
                );
                unset($_SESSION[$nameVarToken]);
                toJson($data);
            }

            if (!empty($_SESSION[$nameVarToken]["token"])) {
                if (!hash_equals($_SESSION[$nameVarToken]["token"], $_POST['token'])) {
                    registerLog(
                        "Ocurrio un error inesperado",
                        "El token proporcionado en el formulario no coincide con el token generado por la página, lo que indica un posible intento de vulneración del sistema de registro.",
                        1,
                        $_SESSION[$nameVarLoginInfo]['idUser'],
                        $table
                    );
                    $data = array(
                        "title" => "Ocurrio un error inesperado",
                        "message" => "Error: La sesión ha expirado o el token de seguridad es inválido. Por favor, actualiza la página e intenta nuevamente",
                        "type" => "error",
                        "icon" => "error",
                        "status" => false
                    );
                    toJson($data);
                }
            } else {
                registerLog("Ocurrio un error inesperado", "Token de seguridad no encontrado en la sesión.", 1, $_SESSION[$nameVarLoginInfo]['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "Error: La sesión ha expirado o el token de seguridad es inválido. Por favor, actualiza la página e intenta nuevamente",
                    "type" => "error",
                    "icon" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //unset($_SESSION['token']);
        } else {
            registerLog("Ocurrio un error inesperado", "No se encontró el token de seguridad en la sesión.", 1, $_SESSION[$nameVarLoginInfo]['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "Error: La sesión ha expirado o el token de seguridad es inválido. Por favor, actualiza la página e intenta nuevamente",
                "type" => "error",
                "icon" => "error",
                "status" => false
            );
            toJson($data);
        }
    } else {
        registerLog("Ocurrio un error inesperado", "Campo token no encontrado en el formulario.", 1, $_SESSION[$nameVarLoginInfo]['idUser']);
        $data = array(
            "title" => "Ocurrio un error inesperado",
            "message" => "Error: La sesión ha expirado, el token de seguridad es inválido o el campo no encontrado en el formulario. Por favor, actualiza la página e intenta nuevamente",
            "type" => "error",
            "status" => false
        );
        toJson($data);
    }
}

/**
 * Funcion que previene ataque CSRF 
 */
function csrf(bool $input = true, int $session = 0)
{
    //unset($_SESSION['token']);
    if ($session == 0) {
        $sessionName = config_sesion($session)['name'] ?? '';
        $nameVarToken = 'data_token';
    } else if ($session == 1) {
        $sessionName = config_sesion($session)['name'] ?? '';
        $nameVarToken = $sessionName . 'data_token';
    }
    if (empty($_SESSION[$nameVarToken])) {
        $_SESSION[$nameVarToken] = array(
            "token" => token(),
            "datatime" => date("Y-m-d H:i:s")
        );
    }
    $token = $_SESSION[$nameVarToken]["token"];
    if (!$input) {
        return $token;
    } else {
        return '<input type="hidden" name="token" value="' . $token . '" >';
    }
}
//configuracion de la sesion
function config_sesion(int $sesion = 0)
{
    switch ($sesion) {
        case 0:
            return ["name" => SESSION_NAME];
            break;
        case 1:
            return ["name" => SESSION_NAME_POS];
            break;
        default:
            return "Nombre de inicio de sesion no definido";
            break;
    }
}
//Funcion que valida si un usuario  tiene validado el inicio de sesion correcto
function isSession(int $sesion = 0)
{
    switch ($sesion) {
        case 0:
            if (session_status() === PHP_SESSION_NONE) {
                session_start(config_sesion());
            }
            if (isset($_SESSION['login'])) {
            } else if (!isset($_SESSION['login']) && isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['login_info'] = json_decode($_COOKIE['login_info'], true);
                registerLog("Informacion sobre sesión de usuario", "El usuario tenia una sesion abierta, de alguna manera se cerro, se procedio a volver a abrirla", 1, $_SESSION['login_info']['idUser']);
                header("Location: " . base_url() . "/im/logOut");
            } else {
                //obtener ip
                $ip = obtenerIP();
                registerLog("Intento de inicio de interfaz", "No se logro intentar iniciar la interfaz desde la parte externa del sistema, IP de intento de logeo - {$ip}", 1, 0);
                header("Location: " . base_url() . "/im/logOut");
            }
            break;
        case 1:
            $name_sesion = config_sesion(1)['name'];
            $nameVarLogin = $name_sesion . 'login';
            $nameVarLoginInfo = $name_sesion . 'login_info';
            if (session_status() === PHP_SESSION_NONE) {
                session_start(config_sesion(1));
            }
            if (isset($_SESSION[$nameVarLogin])) {
            } else if (!isset($_SESSION[$nameVarLogin]) && isset($_COOKIE[$nameVarLogin])) {
                $_SESSION[$nameVarLogin] = $_COOKIE[$nameVarLogin];
                $_SESSION[$nameVarLoginInfo] = json_decode($_COOKIE[$nameVarLoginInfo], true);
                header("Location: " . base_url() . "/pos/LogOut");
            } else {
                //obtener ip
                $ip = obtenerIP();
                header("Location: " . base_url() . "/pos/LogOut");
            }
            break;
        default:
            # code...
            break;
    }
}
//validacion de login de inicio si existe
function existLogin(int $sesion = 0)
{
    switch ($sesion) {
        case 0:
            //sirve para regresar al dashboard desde el login
            if (isset($_SESSION['login'])) {

                header("Location: " . base_url() . "/im/dashboard");
            }
            break;
        case 1:
            $name_sesion = config_sesion(1)['name'];
            $nameVarLogin = $name_sesion . 'login';            //sirve para regresar al dashboard desde el login
            if (isset($_SESSION[$nameVarLogin])) {

                header("Location: " . base_url() . "/pos/dashboard");
            }
            break;

        default:
            echo "Validacion incorrecta de session";
            die();
            break;
    }
}
/**
 * Valida si un archivo subido corresponde al tipo y extensiones permitidas.
 *
 * IMPORTANTE:
 * - Devuelve false si el archivo es válido.
 * - Devuelve true si hay error.
 *
 * @param string $expectedType   Tipo esperado (ejemplo: "image", "video", "audio", "application").
 * @param array  $file           Información del archivo (ej. $_FILES["archivo"]).
 * @param array  $extensions     Extensiones permitidas (ej. ["jpeg","png","gif","pdf"]).
 *
 * @return bool  false si cumple las validaciones, true si hay error.
 */
function isFile(string $expectedType, array $file, array $extensions = []): bool
{
    // Validar que tenga "type" y "name"
    if (!isset($file["type"]) || !isset($file["name"])) {
        return true; // Error: datos incompletos
    }

    // Separar tipo y sub-tipo del MIME
    $arrType = explode("/", strtolower($file["type"]));
    if (count($arrType) < 2) {
        return true; // Error: MIME inválido
    }

    // Validar tipo principal (image, video, etc.)
    if ($expectedType !== "" && $expectedType !== $arrType[0]) {
        return true; // Error: tipo no coincide
    }

    // Validar extensión real del archivo
    $realExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    // Validar extensiones permitidas (si se definen)
    if (!empty($extensions)) {
        if (!in_array($arrType[1], $extensions, true) && !in_array($realExtension, $extensions, true)) {
            return true; // Error: extensión no válida
        }
    }

    return false; // Correcto ✅
}
/**
 * Función que valida la existencia de una carpeta y la crea en caso de no existir.
 *
 * @param string $ruta        Ruta de la carpeta a verificar o crear.
 * @param int    $permissions Permisos de la carpeta (por defecto 0777).
 * @param bool   $recursive   Indica si se deben crear carpetas de manera recursiva (por defecto false).
 *
 * @return bool Retorna true si la carpeta ya existía o se creó correctamente,
 *              retorna false si no se pudo crear la carpeta.
 */
function verifyFolder(string $ruta, int $permissions = 0777, bool $recursive = false): bool
{
    // Verifica si la ruta ya es un directorio válido
    if (is_dir($ruta)) {
        return false; // Ya existe la carpeta
    }

    // Intenta crear la carpeta si no existe
    if (@mkdir($ruta, $permissions, $recursive)) {
        return true; // Se creó exitosamente
    }

    // Si llega aquí, hubo un error al crear la carpeta
    return false;
}
/**
 * Convierte un valor en Bytes, KB, MB, GB o TB a todas las unidades posibles
 *
 * @param float $size      Tamaño numérico (ej: 2, 10, 500)
 * @param string $unit     Unidad de entrada (Bytes, KB, MB, GB, TB) - default "Bytes"
 * @param int $precision   Número de decimales - default 2
 * @return array           Valores convertidos (Bytes, KB, MB, GB, TB)
 */
function valConvert(float $size, string $unit = "Bytes", int $precision = 2): array
{
    // Normalizamos la unidad
    $unit = strtoupper($unit);

    // Convertimos el valor a bytes
    switch ($unit) {
        case "KB":
            $bytes = $size * 1024;
            break;
        case "MB":
            $bytes = $size * 1024 ** 2;
            break;
        case "GB":
            $bytes = $size * 1024 ** 3;
            break;
        case "TB":
            $bytes = $size * 1024 ** 4;
            break;
        default: // Bytes
            $bytes = $size;
    }

    // Calculamos todas las posibilidades
    return [
        "Bytes" => round($bytes, $precision),
        "KB" => round($bytes / 1024, $precision),
        "MB" => round($bytes / (1024 ** 2), $precision),
        "GB" => round($bytes / (1024 ** 3), $precision),
        "TB" => round($bytes / (1024 ** 4), $precision),
    ];
}
/**
 * Elimina archivos y carpetas internas de forma recursiva.
 *
 * @param string $carpeta      Ruta de la carpeta que se desea limpiar/eliminar.
 * @param string $val          Patrón de búsqueda (por defecto "*").
 * @param bool   $deleteFolder Si es true, elimina también la carpeta principal.
 *
 * @return bool
 *         true  -> Hubo un error durante la eliminación.
 *         false -> Todo salió bien, no se encontraron errores.
 */
function delFolder(string $carpeta, string $val = "*", bool $deleteFolder = false): bool
{
    if (!is_dir($carpeta)) {
        return false; // no existe la carpeta, no es error
    }

    if (empty($val)) {
        $val = "*";
    }

    // Obtenemos archivos y carpetas dentro
    $arrFile = glob($carpeta . DIRECTORY_SEPARATOR . $val, GLOB_NOSORT);

    if ($arrFile !== false) {
        foreach ($arrFile as $file) {
            if (is_file($file)) {
                // Intentamos eliminar archivo
                if (!unlink($file)) {
                    return true; // error al eliminar archivo
                }
            } elseif (is_dir($file)) {
                // Llamada recursiva para subcarpeta
                if (delFolder($file, "*", true)) {
                    return true; // error dentro de la subcarpeta
                }
            }
        }
    }

    // Eliminar la carpeta principal si se indicó
    if ($deleteFolder) {
        if (!rmdir($carpeta)) {
            return true; // error al eliminar carpeta
        }
    }

    return false; // todo salió bien
}

//Funcion que carga las opciones del sidebar
function loadOptions(int $id_user, $data = null)
{
    //requerimos el modelo userModel
    require_once "./Models/Admin/RolesModel.php";
    $obj = new RolesModel();
    $arrData = $obj->select_module_iterface_by_user($id_user);
    $arrDataListNav = $obj->select_module_iterface_by_user_is_not_list_nav($id_user);
    $arrDataAll = $obj->select_module_iterface_by_user_all($id_user);
    if ($arrData || $arrDataListNav) {
        $_SESSION['login_interface'] = $arrDataAll;
        $sideBar = "";
        foreach ($arrData as $key => $value) {
            $sideBar .= '
                        <li class="treeview ' . isExpanded($data["page_id"], $value['interface']) . '"><a class="app-menu__item" href="#" data-toggle="treeview">' . $value['m_icon'] . '&nbsp;
                            <span class="app-menu__label">' . $value['m_name'] . '</span><i
                                    class="treeview-indicator fa fa-angle-right"></i></a>
                            <ul class="treeview-menu">';
            foreach ($value['interface'] as $key2 => $value2) {
                if ($value2['i_isOption'] == '0') {
                    if ($value2['i_isListNav'] == '1') {
                        $sideBar .= ' <li><a class="treeview-item ' . activeItem($value2['idInterface'], $data["page_id"]) . '" href="' . base_url() . '/im/' . $value2['i_url'] . '"><i class="icon fa fa-circle-o"></i>' . $value2['i_name'] . '</a></li>   ';
                    }
                }
            }
            $sideBar .= '</ul>
                        </li>
                        ';
        }
        echo $sideBar;
    } else {
        registerLog("Cierre de sesion forzado", "La cuenta del usuario no tiene permisos a niguna funcion, por eso se esta forzando a cerrar session", 3, $_SESSION['login_info']['idUser']);
        //Revisar si se puede mejorar esta ruta, a una interfaz donde diga que la cuenta no tiene permisos, por eso nos fuerza a cerrar sesion
        echo '<script>window.location.href="' . base_url() . '/im/LogOut";</script>';
    }
    //limpiamos el objeto por seguridad
    unset($obj);
}
//Funcion que busca si el id de la inerfaz esta dentro para poder expandir el item
function isExpanded(int $idInterface, array $array)
{
    foreach ($array as $interfaz) {
        if ($interfaz['i_isListNav'] === '1') {
            if ($interfaz['idInterface'] == $idInterface) {
                return 'is-expanded'; // retorna la clase para expandir el item
            }
        }
    }
    return null; // Retorna null si no se encuentra
}
//Funcion que permite saber si el usuario tiene permisos para acceder a una interfaz
function permissionInterface(int $idInterface)
{
    $login_interface = $_SESSION['login_interface'] ?? [];
    foreach ($login_interface as $modulo) {
        foreach ($modulo['interface'] as $interfaz) {
            if ($interfaz['idInterface'] == $idInterface) {
                return true; // retorna la clase para expandir el item
            }
        }
    }
    registerLog("Cierre de sesion forzado", "La cuenta no tiene permiso a ingresar en esta vista, por lo que se esta forzando a cerrar sesion", 3, $_SESSION['login_info']['idUser']);
    //Revisar si se puede mejorar esta ruta, a una interfaz donde diga que la cuenta no tiene permisos, por eso nos fuerza a cerrar sesion
    echo '<script>window.location.href="' . base_url() . '/im/LogOut";</script>'; // Retorna null si no se encuentra
}
// Funcion que da formato a la fecha
function dateFormat($date): string
{
    $date = strtotime($date);
    $date = date("M d - Y", $date) . "  " . date("h:i:s a", $date);
    return $date;
}
//Funcion que devuelve la IP del usuario
function obtenerIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // IP desde share internet.
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // IP pasada desde un proxy.
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // IP remota.
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
//Funcion que obtiene la informacion de sistema
function getSystemInfo()
{
    require_once "./Models/Admin/SystemModel.php";
    $obj = new SystemModel();
    $arrData = $obj->select_info_system();
    unset($obj);
    return $arrData;
}

//Funcion que redimensiona la imagen y tamaño de la imagen
function resizeAndCompressImage($sourcePath, $destinationPath, $maxSizeMB = 2, $newWidth = null, $newHeight = null)
{
    $maxSizeBytes = $maxSizeMB * 1024 * 1024; // Convertir MB a Bytes

    // Obtener información de la imagen
    list($width, $height, $type) = getimagesize($sourcePath);
    // Verifica que GD esté habilitada
    if (!extension_loaded('gd')) {
        registerLog(
            "Extensión GD no disponible",
            "No se pudo cargar la extensión GD. Actívela en el servidor.",
            1,
            $_SESSION['login_info']['idUser']
        );

        $data = array(
            "title" => "Error al procesar imagen",
            "message" => "El servidor no tiene la extensión GD habilitada. Contacte al soporte técnico.",
            "type" => "error",
            "status" => false
        );
        toJson($data);
        exit;
    }
    // Crear una imagen desde el archivo original
    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false; // Tipo de imagen no compatible
    }

    // Si no se especifican nuevas dimensiones, mantener las originales
    if ($newWidth === null) {
        $newWidth = $width;
    }
    if ($newHeight === null) {
        $newHeight = ($height * $newWidth) / $width;
    }

    // Crear una imagen en blanco con las nuevas dimensiones
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Redimensionar la imagen
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Ajustar la calidad dinámicamente para alcanzar el peso deseado
    $quality = 90; // Comenzar con calidad alta
    do {
        // Guardar la imagen temporalmente en buffer
        ob_start();
        if ($type == IMAGETYPE_JPEG) {
            imagejpeg($resizedImage, null, $quality);
        } elseif ($type == IMAGETYPE_PNG) {
            imagepng($resizedImage, null, 9); // PNG usa nivel de compresión (0-9)
        } elseif ($type == IMAGETYPE_GIF) {
            imagegif($resizedImage);
        }
        $imageData = ob_get_clean();
        $fileSize = strlen($imageData); // Obtener el tamaño en bytes

        // Reducir calidad progresivamente hasta alcanzar el peso límite
        $quality -= 5;
    } while ($fileSize > $maxSizeBytes && $quality > 10);

    // Guardar la imagen final
    file_put_contents($destinationPath, $imageData);

    // Liberar memoria
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    return true;
}
/**
 * Función para generar un código QR y guardarlo en una ruta específica
 * 
 * @param string $data      - Contenido del QR (texto o URL)
 * @param string $filename  - Nombre del archivo (sin la ruta)
 * @param string $path      - Ruta donde se guardará el archivo
 * @param int $size         - Tamaño del QR (1-10 recomendado)
 * @param int $margin       - Margen del QR
 * 
 * @return string           - Ruta completa del archivo guardado o mensaje de error
 */
function generarQR(string $data, string $filename = "codigo_qr.png", string $path = "qr_codes/", float $size = 10, float $margin = 2, int $maxIntentos = 3)
{
    // Incluir la biblioteca
    require_once './Libraries/phpqrcode/qrlib.php';

    // Asegurar que la ruta exista, si no, crearla
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    // Ruta completa donde se guardará el QR
    $filePath = $path . $filename;

    // Intentar generar el código QR hasta `maxIntentos`
    $intento = 0;
    while ($intento < $maxIntentos) {
        // Generar y guardar el código QR
        QRcode::png($data, $filePath, QR_ECLEVEL_L, $size, $margin);

        // Verificar si el archivo se creó correctamente
        if (file_exists($filePath)) {
            return $filePath;
        }

        // Incrementar el contador de intentos
        $intento++;
    }

    // Si llegó aquí, significa que falló en todos los intentos
    return false;
}
/**
 * Calcula la diferencia entre dos fechas y devuelve el resultado en
 * años, meses, días, horas, minutos y segundos.
 *
 * @param string $fechaInicio Fecha inicial en formato 'Y-m-d H:i:s'.
 * @param string $fechaFin    Fecha final en formato 'Y-m-d H:i:s'.
 *
 * @return string Retorna una cadena con la diferencia formateada, por ejemplo:
 *                "25 años, 4 meses, 17 días, 2 horas, 35 minutos, 20 segundos"
 *
 * @example
 * echo calcularDiferenciaFechas("2000-01-01 12:00:00", "2025-05-18 14:35:20");
 */
function calculateDifferenceDates($fechaInicio, $fechaFin, $incluirHoras = false)
{
    // Crear objetos DateTime a partir de las fechas ingresadas
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);

    // Calcular la diferencia
    $diferencia = $inicio->diff($fin);


    // Construir la cadena base
    $resultado = "{$diferencia->y} años, {$diferencia->m} meses, {$diferencia->d} días";

    // Si se desea incluir horas, minutos y segundos
    if ($incluirHoras) {
        $resultado .= ", {$diferencia->h} horas, {$diferencia->i} minutos, {$diferencia->s} segundos";
    }

    return $resultado;
}
/**
 * Metodo para obtener todos los usuarios online
 * @return array
 */
function getAllUsersOnline()
{
    require_once "./Models/Admin/LoginModel.php";
    $usersModel = new LoginModel();
    $arrData = $usersModel->select_users_online();
    return $arrData;
}
/**
 * Calcula y devuelve una descripción amigable de la diferencia entre la fecha actual y una fecha dada.
 *
 * Esta función toma una fecha en formato 'Y-m-d H:i:s' y compara con la fecha y hora actual del sistema.
 * Devuelve una cadena legible para humanos como:
 * - "Ahora"
 * - "Hace 10 segundos"
 * - "Hace 5 minutos"
 * - "Hace 2 horas"
 * - "Hace 3 días"
 * - "Hace 1 semana"
 * - O la fecha exacta si supera 7 días: "El 2025-06-25 14:22:00"
 *
 * Es útil para interfaces de usuario donde se muestran notificaciones, comentarios, logs, publicaciones, etc.
 *
 * @param string $fecha Fecha a comparar, en formato 'Y-m-d H:i:s'. Por ejemplo: '2025-07-04 14:30:00'
 * 
 * @return string Devuelve una cadena descriptiva de la diferencia entre la fecha actual y la fecha dada.
 *                Si la fecha es futura, devuelve "En el futuro".
 *                Si la diferencia es menor a 10 segundos, devuelve "Ahora".
 *                Para diferencias mayores, retorna la unidad más significativa hasta 7 días.
 *                Si supera 7 días, devuelve la fecha exacta con formato.
 */
function calculateDifferenceDatesActual($fecha)
{
    // Convertimos la fecha dada en un objeto DateTime
    $fechaDada = new DateTime($fecha);

    // Obtenemos la fecha y hora actual como objeto DateTime
    $ahora = new DateTime();

    // Calculamos la diferencia en segundos entre ahora y la fecha dada
    $diferencia = $ahora->getTimestamp() - $fechaDada->getTimestamp();

    // Si la fecha dada es futura
    if ($diferencia < 0) {
        return 'En el futuro';
    }

    // Si la diferencia es menor a 10 segundos
    if ($diferencia < 10) {
        return 'Ahora';
    }

    // Si es menor a 60 segundos, devolvemos los segundos
    if ($diferencia < 60) {
        return 'Hace ' . $diferencia . ' segundo' . ($diferencia > 1 ? 's' : '');
    }

    // Si es menor a una hora, devolvemos los minutos
    if ($diferencia < 3600) {
        $minutos = floor($diferencia / 60);
        return 'Hace ' . $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
    }

    // Si es menor a un día, devolvemos las horas
    if ($diferencia < 86400) {
        $horas = floor($diferencia / 3600);
        return 'Hace ' . $horas . ' hora' . ($horas > 1 ? 's' : '');
    }

    // Si es menor a una semana (7 días), devolvemos los días
    if ($diferencia < 604800) {
        $dias = floor($diferencia / 86400);
        return 'Hace ' . $dias . ' día' . ($dias > 1 ? 's' : '');
    }

    // Si supera los 7 días, devolvemos la fecha completa en formato legible
    return 'El ' . $fechaDada->format('Y-m-d H:i:s');
}
/**
 * Funcion que registra notificaciones para los usuarios 
 * @param int $iduser
 * @param string $title
 * @param string $description
 * @param int $priority
 * @param string $type => ['info','success','warning','error','custom']
 * @param string $color
 * @param string $icon
 * @param string $link
 * @return void
 */
function setNotification(int $iduser, string $title, string $description, int $priority = 1, string $type = "info", string $color = "info", string $icon = "fa-bell", string $link = "")
{
    //creamos un objeto del modelo de notificaciones
    require_once "./Models/Admin/NotificationModel.php";
    $obj = new NotificationModel();
    $obj->insert_notification($iduser, $title, $description, $priority, $type, $color, $icon, $link);
}
/**
 * Limita la cantidad de caracteres en una cadena.
 *
 * @param string $texto El texto original.
 * @param int $limite Número máximo de caracteres permitidos.
 * @param string $sufijo Texto a añadir al final si se recorta (por defecto "...").
 * @return string El texto limitado.
 */
function limitarCaracteres($texto, $limite, $sufijo = '...')
{
    if (strlen($texto) > $limite) {
        return substr($texto, 0, $limite - strlen($sufijo)) . $sufijo;
    } else {
        return $texto;
    }
}
/**
 * Metodo que genera un avatar aleatorio para un usuario ingresando su nombre
 * @param string $nombre
 * @return string
 */
function generateAvatar(string $nombre)
{
    return GENERAR_PERFIL . $nombre . "&background=random";
}
/**
 * Metodo que devuelve el tipo de loader a utilizar dependiendo al numero de entrada que recibe
 * @param int $num
 * @return string
 */
function getLoader(int $num)
{
    switch ($num) {
        case 1:
            $loader = '<div class="spinner-border mx-auto mb-2" role="status"></div>';
            break;
        case 2:
            $loader = '<div class="loader-spinner mx-auto mb-2"></div>';
            break;
        case 3:
            $loader = ' <div class="loader-dots mx-auto mb-2">
                                                        <span></span><span></span><span></span>
                                                    </div>';
            break;
        case 4:
            $loader = '<div class="loader-bar mx-auto mb-2">
                                                        <div></div>
                                                    </div>';
            break;
        case 5:
            $loader = '<div class="loader-pulse mx-auto mb-2"></div>';
            break;
        case 6:
            $loader = '<div class="loader-dual-ring mx-auto mb-2"></div>';
            break;
        case 7:
            $loader = '<div class="loader-ripple mx-auto mb-2">
                                                        <div></div>
                                                        <div></div>
                                                    </div>';
            break;
        case 8:
            $loader = ' <div class="loader-circle-dots mx-auto mb-2">
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                    </div>';
            break;
        case 9:
            $loader = '<div class="loader-bars mx-auto mb-2">
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                    </div>';
            break;
        case 10:
            $loader = '  <div class="loader-flip mx-auto mb-2"></div>';
            break;
        case 11:
            $loader = '<div class="loader-fade-circle mx-auto mb-2"></div>';
            break;
        case 12:
            $loader = '<div class="loader-rotate-square mx-auto mb-2"></div>';
            break;
        case 13:
            $loader = '<div class="loader-lines mx-auto mb-2">
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                    </div>';
            break;
        default:
            $loader = '<div class="spinner-border mx-auto mb-2" role="status"></div>';
            break;
    }
    return $loader;
}
//Funicion que obtiene la key de la API de RENIEC
function getApiKeyReniec()
{
    //vamos a validar si la llave ya tiene registro en la base de datos para solo utilizar esas llave y no la del config
    if (getSystemInfo()["c_key_api_reniec_sunat"] != "") {
        return getSystemInfo()["c_key_api_reniec_sunat"];
    } else {
        return API_KEY;
    }
}
//Funcion que obtiene la url de la API de RENIEC
function getApiUrlReniec()
{
    return API_URL_RENIEC;
}
//Funcion que obtiene la key de la API de SUNAT
function getApiUrlSunat()
{
    return API_URL_RUC;
}
/**
 * Funcion que recibe como parametro un array con la cantidad de campos 
 * que evalua y verifica si existen esos campos de acuerdo al formulario que se envia si es post o get
 * y devuelve un mensaje de error si no existen los campos en formato json
 * para esto solo funciona cuando el formulario esta validado su inicio de sesion
 * @param array $fields
 * @return void
 */
function validateFields(array $fields, string $method = "POST")
{
    foreach ($fields as $field) {
        if ($method === "POST") {
            if (!isset($_POST[$field])) {
                //registerLog("Ocurrio un error inesperado", "No se encontro el campo $field, por favor verifique o refresque la pagina e intente nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "Error: El campo $field no se encuentra en el formulario enviado. Por favor, verifique o refresque la página e intente nuevamente.",
                    "type" => "error",
                    "icon" => "error",
                    "status" => false
                );
                toJson($data);
            }
        } elseif ($method === "GET") {
            if (!isset($_GET[$field])) {
                //registerLog("Ocurrio un error inesperado", "No se encontro el campo $field, por favor verifique o refresque la pagina e intente nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrio un error inesperado",
                    "message" => "Error: El campo $field no se encuentra en el formulario enviado. Por favor, verifique o refresque la página e intente nuevamente.",
                    "type" => "error",
                    "icon" => "error",
                    "status" => false
                );
                toJson($data);
            }
        }
    }
}
/**
 * Funcion que recibe un array de datos y valida si estan vacios o no
 * para devolver un error si estan vacios
 * la informacion que recibe es de la siguiente estructura:
 * array(
 *     "campo1" => "valor1",
 *    "campo2" => "valor2",
 *   "campon" => "valorn",)
 * deve mostrar un mensaje de error si alguno de los campos esta vacio con el nombre del campo 
 * @param array $fields
 * @return void
 */
function validateFieldsEmpty(array $fields)
{
    foreach ($fields as $field => $value) {
        if (empty($value)) {
            //registerLog("Ocurrio un error inesperado", "El campo $field esta vacio, por favor verifique e intente nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "Error: El campo $field no puede estar vacio. Por favor, verifique e intente nuevamente.",
                "type" => "error",
                "icon" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
}
/**
 * Elimina de la sesión todas las variables que no están marcadas como necesarias
 * para la vista actual.
 *
 * Esta función recibe un arreglo con las variables que deben mantenerse activas
 * en la sesión bajo la clave 'page_vars'. Las demás variables definidas en la lista
 * de variables globales del sistema serán eliminadas de $_SESSION.
 *
 * Ejemplo de uso:
 * deleteSessionVariable([
 *     'page_vars' => ['permission_data'] // Se mantendrá activa
 * ]);
 *
 * @param array $data Arreglo con la clave 'page_vars' que contiene un array de variables que deben mantenerse.
 * @return void
 */
function deleteSessionVariable(array $data)
{
    // Lista de todas las variables de sesión utilizadas globalmente en el sistema
    $allSessionVariables = ["permission_data", "login", "login_info", "data_token", "lastConsult", "folder_open"];

    // Variables que deben mantenerse activas en esta vista
    $varsToKeep = $data["page_vars"] ?? [];

    // Filtramos las variables que deben eliminarse (las que no están en $varsToKeep)
    $varsToDelete = array_diff($allSessionVariables, $varsToKeep);
    // Eliminamos esas variables de la sesión si están definidas
    foreach ($varsToDelete as $sessionVar) {
        if (isset($_SESSION[$sessionVar])) {
            unset($_SESSION[$sessionVar]);
        }
    }
}
/**
 * Convierte un color en formato hexadecimal (#RRGGBB o #RGB) a un arreglo RGB válido para FPDF.
 *
 * @param string $hex Color hexadecimal con o sin el símbolo '#' (ej. "#ff0000" o "ff0000" o "#f00").
 * @return array Arreglo con tres valores enteros [R, G, B], cada uno entre 0 y 255.
 *
 * Ejemplo de uso:
 *   $rgb = hexToRGB("#ff9900"); // Devuelve [255, 153, 0]
 */
function hexToRGB($hex)
{
    // Elimina el símbolo '#' si está presente
    $hex = ltrim($hex, '#');

    // Si el formato es de 3 caracteres (#f00), lo expandimos a 6 caracteres (#ff0000)
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] .
            $hex[1] . $hex[1] .
            $hex[2] . $hex[2];
    }

    // Convertimos cada par de caracteres hexadecimales a valores decimales
    return [
        hexdec(substr($hex, 0, 2)), // Rojo
        hexdec(substr($hex, 2, 2)), // Verde
        hexdec(substr($hex, 4, 2))  // Azul
    ];
}

/**
 * Calcula la diferencia entre dos fechas/horas y devuelve un desglose detallado.
 *
 * Notas:
 *  - Acepta cadenas de fecha/hora válidas para el constructor de DateTime 
 *    (por ejemplo: "YYYY-MM-DD" o "YYYY-MM-DD H:i:s").
 *  - Utiliza DateTime y DateInterval para obtener la diferencia exacta.
 *  - Los valores 'años', 'meses', 'días', 'horas', 'minutos' y 'segundos' 
 *    representan la diferencia desglosada en cada unidad.
 *  - Los valores 'total_*' representan el total acumulado de días, horas, minutos o segundos.
 *  - No considera fracciones de segundo (microsegundos).
 *
 * @param string $fechaInicio   Fecha/hora de inicio (formato válido para DateTime).
 * @param string $fechaFin      Fecha/hora de fin (formato válido para DateTime).
 * @return array Arreglo asociativo con:
 *     - 'años'           => int  (años completos de diferencia)
 *     - 'meses'          => int  (meses restantes después de años completos)
 *     - 'días'           => int  (días restantes después de meses completos)
 *     - 'horas'          => int  (horas restantes después de días completos)
 *     - 'minutos'        => int  (minutos restantes después de horas completas)
 *     - 'segundos'       => int  (segundos restantes después de minutos completos)
 *     - 'total_dias'     => int  (diferencia total en días)
 *     - 'total_horas'    => int  (diferencia total en horas)
 *     - 'total_minutos'  => int  (diferencia total en minutos)
 *     - 'total_segundos' => int  (diferencia total en segundos)
 *
 * @throws Exception Si el formato de fecha/hora no es válido para DateTime.
 */
function dateDifference($fechaInicio, $fechaFin)
{
    // Convertir las cadenas en objetos DateTime
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);

    // Calcular la diferencia entre las dos fechas
    $diferencia = $inicio->diff($fin);

    // Retornar los valores individuales y acumulados
    return [
        'años' => $diferencia->y,
        'meses' => $diferencia->m,
        'días' => $diferencia->d,
        'horas' => $diferencia->h,
        'minutos' => $diferencia->i,
        'segundos' => $diferencia->s,
        'total_dias' => $diferencia->days,
        'total_horas' => ($diferencia->days * 24) + $diferencia->h,
        'total_minutos' => (($diferencia->days * 24) + $diferencia->h) * 60 + $diferencia->i,
        'total_segundos' => (((($diferencia->days * 24) + $diferencia->h) * 60) + $diferencia->i) * 60 + $diferencia->s
    ];
}
/**
 * renderTemplate
 * --------------
 * Procesa una plantilla PHP que contiene HTML y variables dinámicas,
 * devolviendo el HTML final como una cadena lista para su uso en envíos de correo,
 * generación de vistas, o cualquier otro propósito.
 *
 * Este método es especialmente útil cuando se trabaja con plantillas que incluyen
 * código PHP embebido (por ejemplo, <?= $variable ?>), ya que permite su ejecución
 * y la inserción de datos de forma segura.
 *
 * Modo de uso:
 *   $html = renderTemplate(__DIR__ . '/plantillas/recovery.php', [
 *       'data' => [
 *           'nombreUsuario' => 'Juan Pérez',
 *           'email'         => 'juan@example.com',
 *           'url_recovery'  => 'https://midominio.com/recovery/ABC123'
 *       ]
 *   ]);
 *
 * Parámetros:
 * @param string $path       Ruta absoluta o relativa al archivo de plantilla PHP.
 * @param array  $variables  Array asociativo con las variables que estarán disponibles
 *                            dentro de la plantilla. Las claves serán los nombres de
 *                            variables y sus valores serán los datos a insertar.
 *
 * Retorno:
 * @return string HTML final procesado, con todo el código PHP ejecutado y sustituido.
 *
 * Consideraciones:
 * - La plantilla debe ser un archivo .php para que el código embebido se procese.
 * - Es buena práctica aislar las plantillas en un directorio específico, por seguridad.
 * - Las rutas deben validarse previamente para evitar inclusiones arbitrarias.
 * - Si se usan funciones o constantes dentro de la plantilla (como getCompanyName()
 *   o WEB_EMPRESA), deben estar definidas antes de llamar a esta función.
 */
function renderTemplate(string $path, array $variables = []): string
{
    $variables = ["data" => $variables]; // Agregar un nivel de profundidad para evitar conflictos de nombres
    // Validar que el archivo exista
    if (!file_exists($path)) {
        throw new InvalidArgumentException("La plantilla no existe en la ruta especificada: {$path}");
    }

    // Extraer variables para que estén disponibles en el ámbito de la plantilla
    // Esto convierte las claves del array en variables
    extract($variables, EXTR_SKIP);

    // Iniciar almacenamiento en buffer de salida
    ob_start();

    // Incluir y procesar la plantilla (ejecuta el código PHP que contenga)
    include $path;

    // Obtener el contenido procesado y limpiar el buffer
    return ob_get_clean();
}
/**
 * Normaliza un texto según un conjunto de caracteres permitidos definido por una expresión regular.
 *
 * Esta función elimina todos los caracteres que no coincidan con el conjunto definido en `$regex`,
 * recorta la longitud máxima permitida y opcionalmente limpia los espacios en blanco al inicio y al final.
 * 
 * Además:
 * - Se asegura de escapar el patrón dinámico con `preg_quote` para mayor seguridad.
 * - Utiliza `mb_substr` para cortar correctamente cadenas en UTF-8.
 * - Incluye manejo básico de errores para evitar fallos en caso de regex inválido.
 *
 * @param string $texto     Cadena original a normalizar.
 * @param string $regex     Conjunto de caracteres válidos en notación de rango de regex.
 *                          Ejemplo: `"a-zA-Z0-9 _-"` permite letras, números, espacios, guion bajo y guion medio.
 * @param int    $maxLength Longitud máxima de la cadena resultante (por defecto 255).
 * @param bool   $trim      Si es `true`, elimina espacios en blanco al inicio y al final del texto (por defecto `true`).
 *
 * @return string Texto normalizado que cumple con las reglas establecidas.
 *
 * @example
 * ```php
 * // Definir los caracteres permitidos
 * $regex = "a-zA-Z0-9 _-";
 * 
 * echo normalizarTexto("Hola_Mundo-123!", $regex);
 * // Resultado: "Hola_Mundo-123" (quita el signo de exclamación)
 *
 * echo normalizarTexto("mañana con café!!!", $regex);
 * // Resultado: "mana con caf" (elimina ñ y á)
 *
 * echo normalizarTexto("Texto#Largo*Con$Simbolos", $regex);
 * // Resultado: "TextoLargoConSimbolos"
 * ```
 */
function normalizarTexto(string $texto, string $regex, int $maxLength = 255, bool $trim = true): string
{
    try {
        // 1. Escapar el patrón de regex para mayor seguridad.
        $regexSafe = preg_quote($regex, '/');

        // 2. Eliminar caracteres que no coincidan con el conjunto permitido.
        $texto = preg_replace("/[^$regexSafe]/u", '', $texto);

        // 3. Eliminar espacios en blanco adicionales (si corresponde).
        if ($trim) {
            $texto = trim($texto);
        }

        // 4. Recortar la longitud al máximo permitido (soporte UTF-8).
        $texto = mb_substr($texto, 0, $maxLength, 'UTF-8');

        return $texto;
    } catch (Exception $e) {
        // Fallback: en caso de error, devolver el texto truncado.
        return mb_substr($texto, 0, $maxLength, 'UTF-8');
    }
}
/**
 * Obtiene la informacion de la sesion del usuario y valida si el usuario tiene un plan activo
 * 
 * @return void
 */
function get_option_and_permission_app()
{
    //nombres iniciales de las variables de sesion
    $sessionName = config_sesion(1)['name'] ?? '';
    $nameVarBusiness = $sessionName . 'business_active';
    $nameVarLoginInfo = $sessionName . 'login_info';
    $nameVarPermission = $sessionName . 'menu_permission';
    //variables de negocio activo
    $idUser = $_SESSION[$nameVarLoginInfo]['idUser'];
    $idBusiness = $_SESSION[$nameVarBusiness]['idBusiness'];
    //requerimos el modelo de permisos
    require_once "./Models/POS/PermissionModel.php";
    $objPermission = new PermissionModel();
    $arrDataPlans = $objPermission->get_plans_subscription($idUser);
    //validamos que el usuario tenga asociado un plan activo
    if (empty($arrDataPlans)) {
        //si el usuario no tiene ningun plan activo, lo registramos el plan free   
        $objPermission->insert_plan_subscription_free($idUser);
        //Como acabamos de registrar el plan free, lo obtenemos
        $arrDataPlans = $objPermission->get_plans_subscription($idUser);
    }
    $fecha_vencimiento = $arrDataPlans['fecha_vencimiento']; #obtener la fecha de vencimiento del plan
    $fecha_actual = date("Y-m-d H:i:s"); #obtener la fecha actual
    $data_vencimiento = dateDifference($fecha_actual, $fecha_vencimiento); #calcular la diferencia entre las dos fechas
    if ((int)$data_vencimiento['total_dias'] <= 0) {
        //validamos que el plan este vencido 2 dias para poder cambiar al plan free o si el plan es el free registramos el plan free
        if ((int)$data_vencimiento['total_dias'] <= -2 || (int)$arrDataPlans['idPlan'] === 1) {
            //cambiamos al plan free
            $objPermission->insert_plan_subscription_free($idUser);
            //redirigimos al usuario a la pantalla de inicio
            header("Location: " . base_url() . "/pos/dashboard");
            die();
        }
    } else {
        /**
         * validamos si el usuario es dueño o 
         * no del negocio activo, si fuere dueño, 
         * tiene acceso a todos los permisos que el plan permite, 
         * caso contrario responde a un rol de usuario el cual esta limitado a permisos
         */
        $dataOwnerBusiness = $objPermission->get_bussiness_owner($idUser, $idBusiness);
        if (!empty($dataOwnerBusiness)) {
            //Ahora consultamos los permisos del plan, que vistas y funciones tiene permitida
            $arrPermissionsFunctions = $objPermission->get_permissions_functions((int)$arrDataPlans['idPlan']);
            if (isset($_SESSION[$nameVarPermission])) {
                unset($_SESSION[$nameVarPermission]);
            }
            //preparamos un array con el menu permitido                
            $_SESSION[$nameVarPermission] = $arrPermissionsFunctions;
        } else {
            //primero validamos que el plan del negocio no este vencido y no sea el plan free
            $dataBusinessEmployee = $objPermission->get_business_employee($idBusiness);
            if (empty($dataBusinessEmployee)) {
                echo "El negocio no tiene un plan activo o esta 
                en plan free, por favor contacte el dueño 
                del negocio para que pueda renovar el plan";
                die();
            }
            //validamos que el plan del negocio no este vencido
            $fecha_vencimiento = $dataBusinessEmployee['plan_expiration_date']; #obtener la fecha de vencimiento del plan
            $fecha_actual = date("Y-m-d H:i:s"); #obtener la fecha actual
            $data_vencimiento = dateDifference($fecha_actual, $fecha_vencimiento);
            if ((int)$data_vencimiento['total_dias'] <= 0) {
                echo "El plan del negocio ha expirado, por favor contacte al dueño 
                del negocio para que pueda renovar el plan";
                die();
            }
            //obtenemos el rol que tiene el usuario en el negocio
            $dataInformationUser = $objPermission->get_information_user($idUser);
            //validamos que el usuario tenga un rol
            if (empty($dataInformationUser)) {
                echo "El usuario no tiene un rol asignado, por favor contacte al dueño 
                del negocio para que pueda asignarle un rol";
                die();
            }
            $roleUser = $dataInformationUser['rolapp_id'];
            /**
             *si el usuario no es dueño del negocio obtenemos los permisos que tiene el usuario en este negocio
             *Primero consultamos la informacion del negocio
             *Luego consultamos los permisos del usuario en este negocio y del plan asociado                   
             */
            $permissionUser = $objPermission->get_permssion_user_employes($idUser, $idBusiness, $roleUser);
            //validamos que el usuario tenga permisos
            if (empty($permissionUser)) {
                echo "El usuario no tiene permisos asignados, por favor contacte al dueño 
                del negocio para que pueda asignarle permisos";
                die();
            }
            unset($_SESSION[$nameVarPermission]);
            $_SESSION[$nameVarPermission] = $permissionUser;
        }
    }

    unset($objPermission);
}
/**
 * Funcion que devuelve el widget del indicador de plan en un array
 * 
 * @return array
 */
function get_widget_plan(string $plan)
{
    $arrWidgetPlan = array(
        'Gratis' => [
            'sm' => <<<HTML
                <span class="badge bg-warning text-dark shadow"><i class="bi bi-star-fill"></i> Gratis</span>
            HTML,
        ],
        'Pro' => [
            'sm' => <<<HTML
                <span class="badge bg-success text-white shadow"><i class="bi bi-award-fill"></i> Pro</span>
            HTML,
        ],
        'Full Max' => [
            'sm' => <<<HTML
                <span class="badge bg-danger text-white shadow"><i class="bi bi-heart-fill"></i> Full Max</span>
            HTML,
        ]
    );
    return $arrWidgetPlan[$plan];
}
