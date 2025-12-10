<?php

class Account extends Controllers
{
    public function __construct()
    {
        //inicializacmos la sesion pasamos el param 1 para inicializar la sesion de la app
        session_start(config_sesion(1));
        existLogin(1);
        parent::__construct("POS");
    }

    public function account()
    {
        $data['page_id'] = 1;
        $data['page_title'] = "Crear cuenta en Capy Ventas";
        $data['page_description'] = "Account";
        $data['page_container'] = "Account";
        $data['page_view'] = 'account';
        $data['page_js_css'] = ['account', 'account_api'];
        $this->views->getView($this, "account", $data, "POS");
    }

    // TODO: Funcion para enviar codigo de verificacion
    public function sendCodeVerification()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $accept_terms = strClean($data['accept_terms']);
        $email    = strClean($data['email']);

        // * Valimos que llegue el correo electronico
        if (!$email || empty($email)) {
            $this->responseError("El correo electronico es requerido.");
        }

        // * Validamos que sea un email
        // TODO: falta

        // * Habilitar cuando se consuma el endopoint
        if (!$accept_terms) {
            $this->responseError("Acepte los terminos de referencia.");
        }

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
        // TODO: Generamos el codigo de 6 digitos
        $code = generateVerificationCode(6);

        // TODO: Creamos las sessiones
        saveSessionVerification(encryption($email), encryption($code));
        //cargamos la plantilla de recuperación de contraseña               
        $data = [
            'nombres'     => "Capy Amigo",
            'titulo'      => "Bienvenido a CapyVentas",
            'descripcion' => "Gracias por unirte a nosotros. Estás a un solo paso de gestionar tus ventas de manera más eficiente. \nPor favor, usa el siguiente código de verificación para confirmar tu correo electrónico y activar tu cuenta:",
            'codigo'      => $code
        ];
        // Cargar plantilla HTML externa
        $plantillaHTML = renderTemplate('./Views/Template/email/notification_sendcode.php', $data);
        $params = [
            // 'to' => [decryption($email)], // o string
            'to' => $email, // o string
            'subject' => 'NOTIFICACION [ ' . "hola" . ' ]- ' . getCompanyName(),
            'body' => $plantillaHTML,
            'attachments' => [] // opcional
        ];
        //enviamos el correo
        if (!sendEmail($config, $params)) {
            $this->responseError("No se pudo enviar el correo de notificacion al usuario {$email}");
        }

        return toJson([
            'title'   => 'Revisa tu correo',
            'message' => "Se ha enviado un código de 6 dígitos a $email. Tienes 10 minutos para usarlo.",
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    public function validateVerificationCode()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $code    = strClean($data['code'] ?? '');
        // TODO: Validamos que no este vacio el codigo y que tenga seis caracteres
        if (empty($code) || strlen($code) !== 6) {
            toJson([
                'status' => false,
                'message' => 'El código es incorrecto, ingrese nuevamente un código válido.',
                'title'   => 'Verificación de código.',
                'type'    => 'error',
                'icon'    => 'error',
            ]);
        }

        $response = validateVerificationCode($code);
        toJson($response);
    }

    public function setAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // $raw = file_get_contents('php://input');
        // $data = json_decode($raw, true);
        // TODO: Validamos que existan las variables requeridas
        validateFields(["names", "lastname", "email", "date_of_birth", "country", "telephone_prefix", "phone_number", "password", "confirm_password"]);
        // TODO: Limpiamos las variables
        $names = strClean($_POST["names"]);
        $lastname = strClean($_POST["lastname"]);
        $email = strClean($_POST["email"]);
        $date_of_birth = strClean($_POST["date_of_birth"]);
        $country = strClean($_POST["country"]);
        $telephone_prefix = strClean($_POST["telephone_prefix"]);
        $phone_number = strClean($_POST["phone_number"]);
        $password = strClean($_POST["password"]);
        $confirm_password = strClean($_POST["confirm_password"]);
        // TODO: Validamos que no esten vacias
        validateFieldsEmpty(array(
            "NOMBRE COMPLETO" => $names,
            "APELLIDO COMPLETO" => $lastname,
            "CORREO SECUNDARIO" => $email,
            "FECHA DE CUMPLEAÑOS" => $date_of_birth,
            "CIUDAD" => $country,
            "PREFIJO DE TELEFONO" => $telephone_prefix,
            "NUMERO DE TELEFONO" => $phone_number,
            "CONTRASEÑA" => $password,
            "CONFIRMAR CONTRASEÑA" => $confirm_password,
        ));

        // * Prefijo por defaul
        // ? Validar luego esto
        $telephone_prefix = "+51";

        // TODO: Validación de formato de nombre
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $names)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombres' no cumple con el formato requerido.",
                'type'    => 'error',
                'icon'    => 'error',
                "status" => false,
            ]);
        }

        // TODO: Validación de formato de apellidos
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $lastname)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Apellidos' no cumple con el formato requerido.",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }

        // TODO: Validación de formato de email
        if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $email)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Correo electrónico' no tiene un formato válido.",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }

        // TODO: Validación de formato de ciudad
        if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $country)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Ciudad' no cumple con el formato requerido.",
                'type'    => 'error',
                'icon'    => 'error',
                "status" => false,
            ]);
        }

        // TODO: Validación de formato de número de teléfono (solo números)
        if (!preg_match('/^\d+$/', $phone_number)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Número de teléfono' debe contener solo números.",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }

        // TODO: Validación de password
        if (strlen($password) < 8) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Contraseña' debe contener mínimo 8 caracteres.",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }

        // TODO: Validamos que el confirm_passord sea igual que password
        if (!hash_equals($password, $confirm_password)) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Contraseña' y 'Confirmar Contraseña' no coinciden.",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }

        // TODO: Verificamos que no exista un usuario con este correo
        $is_exists_user = $this->model->isExistsUser(encryption($email));
        if ($is_exists_user) {
            toJson([
                "title" => "Ocurrió un error inesperado",
                "message" => "Ya existe un usuario registrado con este correo electrónico",
                "type" => "error",
                'icon'    => 'error',
                "status" => false
            ]);
        }
        // TODO: Primero creamos la persona con los datos
        // TODO: Creamos la cuenta de usuario
        toJson($is_exists_user);
    }

    private function responseError(string $message): void
    {
        $data = [
            'title'  => 'Ocurrió un error',
            'message' => $message,
            'type'   => 'error',
            'icon'   => 'error',
            'status' => false,
        ];

        toJson($data);
    }
}
