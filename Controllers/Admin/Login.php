<?php

class Login extends Controllers
{
	public function __construct()
	{
		session_start(config_sesion());
		existLogin();
		parent::__construct();
	}

	public function login()
	{

		$data['page_id'] = 1;
		$data['page_title'] = "Inicio de sesión";
		$data['page_description'] = "Login";
		$data['page_container'] = "Login";
		$data['page_view'] = 'login';
		$data['page_js_css'] = "login";
		$this->views->getView($this, "login", $data);
	}
	public function pwreset($token)
	{
		//validamos que el token no sea nulo
		if (empty($token)) {
			header("Location: " . base_url() . "/im/login");
		}
		//consultamos la base de datos el token 
		$rqstUser = $this->model->select_info_user_by_token($token);
		//validamos si existen informacion de la solicitud
		if (empty($rqstUser)) {
			header("Location: " . base_url() . "/im/login");
		}
		//desencriptamos el token
		$token = decryption($token);
		//separamos el token con el caracter |
		$token = explode("|", $token);
		//almacenmos el id y la fecha de registro en variables diferentes
		$userId = $token[0];
		$fechaRegistro = $token[1];
		//validamos si el id del token y el id del usuario son iguales
		if ($userId != $rqstUser['idUser']) {
			header("Location: " . base_url() . "/im/login");
		}
		//verificamos si el tiempo de duracion del token no expiro los 30 minutos
		$fechaActual = date("Y-m-d H:i:s");
		($difference = dateDifference($fechaRegistro, $fechaActual));
		if ($difference['total_minutos'] > 30) {
			header("Location: " . base_url() . "/Errors/timeout");
		}
		$rqstUser["tiempo_restante"] = $difference;
		$data['page_id'] = 10;
		$data['page_title'] = "Restablecimiento de contraseña";
		$data['page_description'] = "Login";
		$data['page_container'] = "Login";
		$data['page_view'] = 'pwreset';
		$data['page_js_css'] = "pwreset";
		$data['page_info_user'] = $rqstUser;
		$this->views->getView($this, "pwreset", $data);
	}
	/**
	 * Funcion que permite el inicio de sesion del usuario
	 * @return void
	 */
	public function isLogIn()
	{
		//validacion del Método POST
		if (!$_POST) {
			registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de iniciar session", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Método POST no encontrado",
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
		//validacion de que existan los campos
		validateFields(["txtUser", "txtPassword"]);
		//limpieza de los inputs
		$txtUser = strClean($_POST["txtUser"]);
		$txtPassword = strClean($_POST["txtPassword"]);
		//validacion de campos vacios
		validateFieldsEmpty(
			["Usuario o Email" => $txtUser, "Contraseña" => $txtPassword]
		);
		//Validacion de usuario, solo debe soporte minimo 3 caracteres
		if (strlen($txtUser) < 3) {
			registerLog("Ocurrió un error inesperado", "El usuario debe tener al menos 3 caracteres para poder ingresar al sistema", 1);
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
			registerLog("Ocurrió un error inesperado", "La contraseña debe tener al menos 8 caracteres para iniciar sesion", 1);
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
		$request = $this->model->selectUserLogin($txtUser);
		if ($request) {
			//validamos que el usuario no haya sobrepasado el limite de intentos de inicio de intentos
			if ($request['u_login_attempts'] >= 3) {
				registerLog("Ocurrió un error inesperado", "El usuario ha sobrepaso el limite de intentos de inicio de sesion, la cuenta ha sido bloqueada", 1);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "La cuenta se encuentra desactivada, ya que los intentos de inicio de sesion han superado el limite permitido",
					"type" => "error",
					"status" => false
				);
				toJson($data);
			}
			//validamos si el usuario no esta en proceso de recuperacion de contraseña
			if ($request['u_reset_token_password'] != "") {
				registerLog("Ocurrió un error inesperado", "El usuario se encuentra en proceso de recuperacion de contraseña, no puede iniciar sesion, regenere un nuevo token y revise su correo, o si no contactese con el adeministrador del sistema", 1, $request["idUser"]);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "El usuario se encuentra en proceso de recuperacion de contraseña, no puede iniciar sesion, si no tiene acceso al correo por favor contactese con el administrador del sistema",
					"type" => "error",
					"status" => false
				);
				unset($request);
				toJson($data);
			}
			//validamos si la contraseña coinciden
			if ($txtPassword != $request['u_password']) {
				//aunmentamos en uno el intento de inicio de sesion
				$attempts = $request["u_login_attempts"] + 1;
				$this->model->update_attempts_login($request["idUser"], $attempts);
				registerLog("Ocurrió un error inesperado", "La contraseña que se ingreso no coincide con la contraseña registrada en el sistema", 1, $request["idUser"]);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "el usuario o contraseña no coinciden, por favor verifique la informacion ingresada e intente nuevamente, tener en cuenta que solo tiene 3 intentos antes de ser bloqueado",
					"type" => "error",
					"status" => false
				);
				unset($request);
				toJson($data);
			}
			//verificamos si la cuenta se encuentra activa
			if ($request["u_status"] == "Inactivo") {
				registerLog("Ocurrió un error inesperado", "El usuario " . $request["u_fullname"] . ", no inicio sesión por motivo de cuenta desactivada", 1, $request["idUser"]);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "La cuenta del usuario actualmente se encuentra en estado Inactivo",
					"type" => "error",
					"status" => false
				);
				unset($request);
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
				"role" => $request["r_name"],
				"attempts" => $request["u_login_attempts"],
				"token_password" => $request["u_reset_token_password"],
				"space_limit" => $request["u_space_limit"],
				"folder_name" => $request["u_personal_folder_name"]
			);
			//actualizamos el estado de online del usuario
			$this->model->update_online_user($request["idUser"], 1);
			//creamos las cookies para el usuario
			$data_session = json_encode($data_session);
			$_SESSION['login'] = true;
			$_SESSION['login_info'] = json_decode($data_session, true);
			//reiniciamos los intentos de inicio de sesion
			$this->model->update_attempts_login($request["idUser"], 0);
			//creamos las cookies para el usuario
			setcookie("login_info", $data_session, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
			setcookie("login", true, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
			registerLog("Inicio de sesión exitoso", "El usuario " . $request["u_fullname"] . ", completo de manera satisfactoria el inicio de sesion", 2, $request["idUser"]);
			$data = array(
				"title" => "Inicio de sesion exitoso",
				"message" => "Hola " . $request["u_fullname"] . ", se completó de manera satisfactoria el inicio de sesión",
				"type" => "success",
				"status" => true,
				"redirection" => base_url() . "/im/dashboard"
			);
			//destruimos la variable que contiene la información del usuario
			unset($request);
			toJson($data);
		} else {
			registerLog("Ocurrió un error inesperado", "El usuario {$txtUser} o contraseña {$txtPassword} que esta intentando ingresar no existe", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "La cuenta de usuario no existe",
				"type" => "error",
				"status" => false
			);
			unset($request);
			toJson($data);
		}
	}
	/**
	 * Metodo que se encarga de reiniciar la contraseña cuando el usuario la olvido
	 * @return void
	 */
	public function resetPassword()
	{
		//validacion del Método POST
		if (!$_POST) {
			registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de iniciar session", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Método POST no encontrado",
				"type" => "error",
				"redirection" => base_url() . "/im/login",
				"status" => false
			);
			toJson($data);
		}
		//validamos los campos vacios
		validateFields(["txtEmail"]);
		$txtEmail = strClean($_POST["txtEmail"]);
		validateFieldsEmpty(["Email" => $txtEmail]); //validamos que el campo no este vacio
		//validamos que tenga estructura de correo electronico
		if (verifyData("[A-Za-z0-9._%+\-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}", $txtEmail)) {
			registerLog("Ocurrió un error inesperado", "El correo electrónico {$txtEmail} no tiene la estructura correcta", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "El correo electrónico no tiene la estructura correcta",
				"type" => "error",
				"redirection" => base_url() . "/im/login",
				"status" => false
			);
			toJson($data);
		}
		//encriptamos el correo electronico
		$encriptedEmail = encryption($txtEmail);
		//consultamos el usuario con el correo electronico
		$request = $this->model->selectUserLogin($encriptedEmail);
		//validamos si el $request trae informacion
		if ($request) {
			if ($request['u_reset_token_password'] != "") {
				$tokenDecryption = decryption($request['u_reset_token_password']);
				//separamos el id y la fecha del token
				$arrToken = explode("|", $tokenDecryption);
				//obtenemos el id y la fecha 
				$id = $arrToken[0]; //obtenemso el id del usuario
				$fecha = $arrToken[1]; //obtenemos la fecha del token
				//validamos si el timepo de la fecha que se obtuvo sea mayo a 1 minuto de ka fecha actual
				$dateDifference = dateDifference($fecha, date("Y-m-d H:i:s"));
				//validamos si la diferencia de tiempo es mayor a 30 segundos
				if ($dateDifference['total_segundos'] <= 30) {
					registerLog("Ocurrio un error inesperado", "No se puede generar el token, por favor espere 30 segundos", 1, $request["idUser"]);
					$data = array(
						"title" => "Ocurrió un error inesperado",
						"message" => "No se puede generar el token, por favor espere {$dateDifference['total_segundos']} segundos",
						"type" => "error",
						"redirection" => base_url() . "/im/login",
						"status" => false
					);
					//destruimos la informacion de la variables request
					unset($request);
					toJson($data);
				}
			}
			//creamos el enlace de recuperación de contraseña, este contendra el id del usuario y la fecha actual de creacion con todo horas minutos y segundos
			$token = $request["idUser"];
			$token .= "|" . date("Y-m-d H:i:s");
			//encriptamos el token
			$encriptedToken = encryption($token);
			//insertamos el token en la base de datos
			$requestUpdate = $this->model->insertToken($encriptedToken, $request["idUser"]);
			//validamos si el token se inserto correctamente
			if ($requestUpdate) {
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
				//cargamos la plantilla de recuperación de contraseña
				$data = [
					'compania' => getCompanyName(),
					'nombreUsuario' => $request['u_fullname'],
					'email' => decryption($request['u_email']),
					'url_recovery' => base_url() . "/im/login/pwreset/" . $encriptedToken,
					'url_compania' => base_url()
				];
				// Cargar plantilla HTML externa
				$plantillaHTML = renderTemplate('./Views/Template/email/reset_password_token.php', $data);
				$params = [
					'to' => [decryption($request['u_email'])], // o string
					'subject' => 'RECUPERACION DE CUENTA - ' . getCompanyName(),
					'body' => $plantillaHTML,
					'attachments' => [] // opcional
				];
				//enviamos el correo
				if (!sendEmail($config, $params)) {
					registerLog("Ocurrio un error inesperado", "No se pudo enviar el correo de recuperación de contraseña", 1, $request["idUser"]);
					$data = array(
						"title" => "Ocurrió un error inesperado",
						"message" => "No se pudo enviar el correo de recuperación de contraseña",
						"type" => "error",
						"redirection" => base_url() . "/im/login",
						"status" => false
					);
					toJson($data);
				}
				registerLog("Generacion de token correcto", "Se genero de manera correcta el token de recuperacion de contraseña", 2, $request['idUser']);
				$data = array(
					"title" => "Generación de token correcto",
					"message" => "Se genero de manera correcta el token de recuperacion de contraseña, por favor revisa tu corre, como tambien en la seccion de spam",
					"type" => "success",
					"redirection" => base_url() . "/im/login",
					"status" => true
				);
				//destruimos la informacion de la variables request
				unset($request);
				toJson($data);
			} else {
				registerLog("Ocurrió un error inesperado", "No se pudo insertar el token de recuperación de contraseña", 1, $request['idUser']);
				//destruimos la informacion de la variables request
				unset($request);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "No se pudo generar el token de recuperación de contraseña",
					"type" => "error",
					"redirection" => base_url() . "/im/login",
					"status" => false
				);
				toJson($data);
			}
		} else {
			registerLog("Cuenta no encontrada", "Se esta intentado recuperar la contraseña para este correo {$txtEmail} lo cual no se encontro una cuenta asociado a este", 1);
			$data = array(
				"title" => "Correcto",
				"message" => "Enlace generado correctamente y enviado al correo electronico",
				"type" => "success",
				"redirection" => base_url() . "/im/login",
				"status" => true
			);
			toJson($data);
		}
	}
	/**
	 * Metodo que se encarga de actualizar la contraseña
	 */
	public function updatePassword()
	{
		//validacion del Método POST
		if (!$_POST) {
			registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de actualizar la contraseña del usuario", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Método POST no encontrado",
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
		validateFields(['txtPassword', 'txtToken']);
		//recoleccion de los campos y limpieza
		$token = strClean($_POST['txtToken']);
		$pass = strClean($_POST['txtPassword']);
		//validamos si el campos esta vacio
		validateFieldsEmpty(array(
			"TOKEN" => $token,
			"PASSWORD" => $pass
		));
		$encryptPassword = encryption($pass);
		$rqstUser = $this->model->select_info_user_by_token($token);
		//validamos que el campos requestuser no este vacio
		if (empty($rqstUser)) {
			registerLog("Cuenta no encontrada", "No se encontro la cuenta asociada al token {$token} para actualizar la contraseña", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "No se encontro la cuenta asociada al token",
				"type" => "error",
				"redirection" => base_url() . "/im/login",
				"status" => false
			);
			toJson($data);
		}
		//insertamos el nuevo password con el token
		$updatePassword = $this->model->update_password_by_token($token, $encryptPassword);
		//validamos si el password se actualizo correctamente
		if ($updatePassword) {
			registerLog("Contraseña actualizada", "Se actualizo la contraseña de la cuenta asociada al token {$token}", 2, $rqstUser["idUser"]);
			$data = array(
				"title" => "Contraseña actualizada",
				"message" => "Se actualizo la contraseña de manera exitosa",
				"type" => "success",
				"redirection" => base_url() . "/im/login",
				"status" => true
			);
			toJson($data);
		} else {
			registerLog("Ocurrió un error inesperado", "No se pudo actualizar la contraseña de la cuenta asociada al token {$token}", 1, $rqstUser["idUser"]);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "No se pudo actualizar la contraseña de manera exitosa",
				"type" => "error",
				"redirection" => base_url() . "/im/login",
				"status" => false
			);
			toJson($data);
		}
	}
}
