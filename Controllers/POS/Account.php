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

		// $raw = file_get_contents('php://input');
		// $data = json_decode($raw, true);

		// toJson($data);

		$email    = strClean($_POST['email']);
		$accept_terms = strClean($_POST['accept_terms']);

		$accept_terms = ($accept_terms && $accept_terms !== 'false') ? true : false;
		// * Habilitar cuando se consuma el endopoint
		// if(!$accept_terms){
		// 	$this->responseError("Acepte los terminos de referencia.");
		// }

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

		// $raw = file_get_contents('php://input');
		// $data = json_decode($raw, true);

		// toJson($data);

		$code    = strClean($_POST['code']);
		$response = validateVerificationCode($code);

		toJson($response);
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
