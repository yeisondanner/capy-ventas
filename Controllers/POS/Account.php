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
		$email = strClean($data['email']);

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
		if ($is_exists_user) {
			$this->responseError("Ya existe un usuario registrado con este correo electrónico.");
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
			'nombres' => "Capy Amigo",
			'titulo' => "Bienvenido a CapyVentas",
			'descripcion' => "Gracias por unirte a nosotros. Estás a un solo paso de gestionar tus ventas de manera más eficiente. \nPor favor, usa el siguiente código de verificación para confirmar tu correo electrónico y activar tu cuenta:",
			'codigo' => $code
		];
		// * Cargar plantilla HTML externa
		$plantillaHTML = renderTemplate('./Views/Template/email/notification_sendcode.php', $data);
		// * Envialos los parámetros
		$params = [
			// 'to' => [decryption($email)], // o string
			'to' => $email, // o string
			'subject' => 'SOLICITUD DE CODIGO DE VERIFICACION',
			'body' => $plantillaHTML,
			'attachments' => [] // opcional
		];
		// * Enviamos el correo
		if (!sendEmail($config, $params)) {
			$this->responseError("No se pudo enviar el correo de notificacion al usuario {$email}");
		}

		// * Respuesta correcta
		return toJson([
			'title' => 'Revisa tu correo',
			'message' => "Se ha enviado un código de 6 dígitos a $email. Tienes 10 minutos para usarlo.",
			'type' => 'success',
			'icon' => 'success',
			'status' => true,
		]);
	}

	public function validateVerificationCode()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->responseError('Método de solicitud no permitido.');
		}

		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		$code = strClean($data['code'] ?? '');
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

	public function setAccount()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->responseError('Método de solicitud no permitido.');
		}

		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);


		// * Validamos que el codigo recibido sea el correcto
		$code = strClean($data["code"]);
		validateVerificationCode($code);

		// * Validamos que existan las variables requeridas
		if (!$data["names"] || !$data["lastname"] || !$data["email"] || !$data["date_of_birth"] || !$data["country"] || !$data["telephone_prefix"] || !$data["phone_number"] || !$data["password"] || !$data["confirm_password"] || !$data["username"]) {
			$this->responseError("Ingrese los campos requeridos.");
		}
		// * Limpiamos las variables
		$names = strClean($data["names"]);
		$lastname = strClean($data["lastname"]);
		$email = strClean($data["email"]);
		$date_of_birth = strClean($data["date_of_birth"]);
		$country = strClean($data["country"]);
		$telephone_prefix = strClean($data["telephone_prefix"]);
		$phone_number = strClean($data["phone_number"]);
		$password = strClean($data["password"]);
		$confirm_password = strClean($data["confirm_password"]);
		$username = strClean($data["username"]);
		// * Validamos que no esten vacias
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
			"NOMBRE DE USUARIO" => $username,
		));
		// * Prefijo por defaul
		// ? Validar luego esto
		$telephone_prefix = "+51";

		// * Pais por default
		// ? Luego borrar esto
		$country = "PERU";

		// * Validación de formato de nombre
		if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $names)) {
			$this->responseError("El campo 'Nombres' no cumple con el formato requerido.");
		}

		// * Validación de formato de apellidos
		if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $lastname)) {
			$this->responseError("El campo 'Apellidos' no cumple con el formato requerido.");
		}

		// * Validación de formato de email
		if (verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $email)) {
			$this->responseError("El campo 'Correo electrónico' no tiene un formato válido.");
		}

		// * Validación de formato de ciudad
		if (verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s\-_.,()]+", $country)) {
			$this->responseError("El campo 'Ciudad' no cumple con el formato requerido.");
		}

		// * Validación de formato de número de teléfono (solo números)
		if (!preg_match('/^\d+$/', $phone_number)) {
			$this->responseError("El campo 'Número de teléfono' debe contener solo números.");
		}

		// * Validación de password
		if (strlen($password) < 8) {
			$this->responseError("El campo 'Contraseña' debe contener mínimo 8 caracteres.");
		}

		// * Validamos que el confirm_passord sea igual que password
		if (!hash_equals($password, $confirm_password)) {
			$this->responseError("El campo 'Contraseña' y 'Confirmar Contraseña' no coinciden.");
		}

		// * Verificamos que no exista un usuario con este correo
		$is_exists_user = $this->model->isExistsPeople(encryption($email));
		if ($is_exists_user) {
			toJson([
				"title" => "Ocurrió un error inesperado",
				"message" => "Ya existe un usuario registrado con este correo electrónico",
				"type" => "error",
				'icon' => 'error',
				"status" => false
			]);
		}

		// * Primero creamos la persona con los datos
		$people = $this->model->createPeople($names, $lastname, encryption($email), $date_of_birth, $country, $telephone_prefix, $phone_number);
		if ($people <= 0) {
			$this->responseError("No se pudo registrar tus datos personales. Por favor intente nuevamente.");
		}
		// * Creamos la cuenta de usuario
		$userApp = $this->model->createUserApp(encryption($email), encryption($password), $people);
		if ($userApp > 0) {
			// * Eliminamos la sesiones
			$this->limpiarSesionVerificacion();
			// * respuesta
			toJson([
				'title' => 'Cuenta creada correctamente',
				'message' => 'Bienvenido a la familia CapyVentas, tu cuenta fue creada correctamente. Inicia sesión con tu correo y contraseña.',
				'type' => 'success',
				'icon' => 'success',
				'status' => true,
			]);
		}
		$this->responseError("No se pudo crear tu cuenta :(. Comunicate con el Capy Administrador.");
	}

	private function responseError(string $message): void
	{
		$data = [
			'title' => 'Ocurrió un error',
			'message' => $message,
			'type' => 'error',
			'icon' => 'error',
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
