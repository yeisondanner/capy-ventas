<?php

class Resetpassword extends Controllers
{
	public function __construct()
	{
		//inicializacmos la sesion pasamos el param 1 para inicializar la sesion de la app
		session_start(config_sesion(1));
		existLogin(1);
		parent::__construct("POS");
	}

	public function resetpassword()
	{
		$data['page_id'] = 1;
		$data['page_title'] = "Crear cuenta en Capy Ventas";
		$data['page_description'] = "Resetpassword";
		$data['page_container'] = "Resetpassword";
		$data['page_view'] = 'resetpassword';
		$data['page_js_css'] = ['resetpassword', 'resetpassword_api'];
		$this->views->getView($this, "resetpassword", $data, "POS");
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

		// * Validación de formato de email
		if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $email)) {
			$this->responseError("El campo 'Correo electrónico' no tiene un formato válido.");
		}

		// * Verificamos que no exista un usuario con este email
		$is_exists_user = $this->model->isExistsPeople(encryption($email));
		if (!$is_exists_user) {
			$this->responseError("No existe usuario con este correo electrónico, créese su cuenta por favor.");
		}

		// * Verificamos que acepte los terminos y condiciones
		if (!$accept_terms) {
			$this->responseError("Acepte los terminos de referencia.");
		}

		// * Configuracion para correo electronico
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
		// * Generamos el codigo de 6 digitos
		$code = generateVerificationCode(6);

		// * Creamos las sessiones
		saveSessionVerification(encryption($email), encryption($code));
		//cargamos la plantilla de recuperación de contraseña               
		$data = [
			'nombres'     => $is_exists_user['names'] . ' ' . $is_exists_user['lastname'],
			'titulo'      => "Bienvenido a CapyVentas",
			'descripcion' => "Gracias por ser parte de nosotros. Estás a un solo paso de poder recuperar tu contraseña. \nPor favor, usa el siguiente código de verificación para poder recuperar tu contraseña:",
			'codigo'      => $code
		];
		// * Cargar plantilla HTML externa
		$plantillaHTML = renderTemplate('./Views/Template/email/notification_sendcode.php', $data);
		// * Envialos los parámetros
		$params = [
			// 'to' => [decryption($email)], // o string
			'to' => $email, // o string
			'subject' => 'CapyVentas - Recuperación de contraseña',
			'body' => $plantillaHTML,
			'attachments' => [] // opcional
		];
		// * Enviamos el correo
		if (!sendEmail($config, $params)) {
			$this->responseError("No se pudo enviar el correo de notificacion al usuario {$email}");
		}

		// * Respuesta correcta
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
				'title' => 'Verificación de código.',
				'type' => 'error',
				'icon' => 'error',
			]);
		}

		$response = validateVerificationCode($code);
		toJson($response);
	}

	public function updatePassword()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->responseError('Método de solicitud no permitido.');
		}

		$raw  = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$code             = strClean($data["code"] ?? "");
		$email            = strClean($data["email"] ?? "");
		$password         = strClean($data["password"] ?? "");
		$confirm_password = strClean($data["confirmPassword"] ?? "");

		// Validación básica
		if ($code === "" || $email === "" || $password === "" || $confirm_password === "") {
			$this->responseError("Faltan datos obligatorios.");
		}

		// Validar código de verificación
		$resp = validateVerificationCode($code);
		if (empty($resp['status'])) {
			$this->responseError($resp['message'] ?? "Código inválido o expirado.");
		}

		// Validar que el correo del POST sea el mismo correo verificado 
		if (
			empty($_SESSION['verificacion_correo']) ||
			$_SESSION['verificacion_correo'] !== encryption($email)
		) {
			$this->responseError("El correo no coincide con el proceso de verificación.");
		}

		//  Asegurar que el status de verificación esté en true
		if (empty($_SESSION['verificacion_status']) || $_SESSION['verificacion_status'] !== true) {
			$this->responseError("Primero debes validar el código de verificación.");
		}

		// Confirmación de contraseña
		if (!hash_equals($password, $confirm_password)) {
			$this->responseError("El campo 'Contraseña' y 'Confirmar Contraseña' no coinciden.");
		}

		// Obtener usuario por email (en tu caso lo guardas encriptado)
		$user = $this->model->getUserByEmail(encryption($email));
		if (empty($user) || empty($user['id'])) {
			$this->responseError("No existe una cuenta con ese correo.");
		}

		$userId = (int)$user['id'];

		// Actualizar password (mantengo tu encryption, aunque lo ideal es password_hash)
		$updatePassword = $this->model->updatePassword($userId, encryption($password));

		if ($updatePassword > 0) {
			// Limpieza de sesión de verificación
			$this->limpiarSesionVerificacion();

			// Recomendado: regenerar id de sesión tras operación sensible
			if (session_status() === PHP_SESSION_ACTIVE) {
				session_regenerate_id(true);
			}

			toJson([
				'title'   => 'Contraseña actualizada correctamente',
				'message' => 'Tu contraseña fue actualizada correctamente. Inicia sesión con tu correo y contraseña.',
				'type'    => 'success',
				'icon'    => 'success',
				'status'  => true,
			]);
			return;
		}

		$this->responseError("No se pudo actualizar tu contraseña. Intente nuevamente.");
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

	private function limpiarSesionVerificacion(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// Eliminamos solo las variables específicas de este proceso
		unset($_SESSION['verificacion_correo']);
		unset($_SESSION['verificacion_codigo']);
		unset($_SESSION['verificacion_tiempo']);
	}
}
