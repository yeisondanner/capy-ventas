<?php

class Login extends Controllers
{
	public function __construct()
	{
		//inicializacmos la sesion pasamos el param 1 para inicializar la sesion de la app
		session_start(config_sesion(1));
		existLogin(1);
		parent::__construct("POS");
	}

	public function login()
	{
		$data['page_id'] = 1;
		$data['page_title'] = "Inicio de sesión";
		$data['page_description'] = "Login";
		$data['page_container'] = "Login";
		$data['page_view'] = 'login';
		$data['page_js_css'] = "login";
		$this->views->getView($this, "login", $data, "POS");
	}

	/**
	 * Funcion que permite el inicio de sesion del usuario
	 * @return void
	 */
	public function isLogIn()
	{
		//validacion del Método POST
		if (!$_POST) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Método POST no encontrado",
				"type" => "error",
				"icon" => "error",
				"status" => false
			);
			toJson($data);
			return;
		}
		//validamos que existan los campos solictados
		if (!isset($_POST["txtUser"]) || !isset($_POST["txtPassword"])) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Los campos de usuario y contraseña son obligatorios",
				"type" => "error",
				"icon" => "error",
				"status" => false
			);
			toJson($data);
		}
		//limpieza de los inputs
		$txtUser = strClean($_POST["txtUser"]);
		$txtPassword = strClean($_POST["txtPassword"]);
		//validamos que los campos no esten vacios de manera individual
		if (empty($txtUser)) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "El usuario o Email no puede estar vacio",
				"type" => "error",
				"icon" => "error",
				"status" => false
			);
			toJson($data);
		}
		if (empty($txtPassword)) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "La contraseña no puede estar vacio",
				"type" => "error",
				"icon" => "error",
				"status" => false
			);
			toJson($data);
		}

		//Validacion de usuario, solo debe soporte minimo 3 caracteres
		if (strlen($txtUser) < 4) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "El usuario debe tener al menos 4 caracteres",
				"type" => "info",
				"icon" => "info",
				"status" => false
			);
			toJson($data);
			return;
		}
		//validacion que la contraseña pueda ingresar minimo 8 caracteres
		if (strlen($txtPassword) < 8) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "La contraseña debe tener al menos 8 caracteres",
				"type" => "info",
				"icon" => "info",
				"status" => false
			);
			toJson($data);
			return;
		}
		//encriptamos el usuario
		$txtUser = encryption($txtUser);
		//verificamos si el usuario existe
		$request = $this->model->selectUserLogin($txtUser);
		if (!$request) {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Usuario o contraseña inválidos",
				"type" => "error",
				"icon" => "error",
				"timer" => 5000,
				"status" => false
			);
			toJson($data);
		}
		//encriptamos el usuario
		$txtPassword = encryption($txtPassword);
		//validamos si la contraseña coinciden
		if (($txtPassword === $request['password'])) {

			//verificamos si la cuenta se encuentra activa
			if ($request["u_status"] === "Inactivo") {
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "La cuenta del usuario actualmente se encuentra en estado Inactivo",
					"type" => "error",
					"icon" => "error",
					"status" => false
				);
				unset($request);
				toJson($data);
			}
			//verificamos si la cuenta se encuentra activa
			if ($request["p_status"] === "Inactivo") {
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "Los datos del usuario estan desactivados",
					"type" => "error",
					"icon" => "error",
					"status" => false
				);
				unset($request);
				toJson($data);
			}
			//obtenemos los negocios asociasdos al usuario
			$bussiness = $this->model->select_business($request["idUserApp"]);
			//creamos las variables de session para el usuario
			$data_session = array(
				"idUser" => $request["idUserApp"],
				"user" => $request["user"],
				"email" => $request["email"],
				"profile" => '',
				"fullName" => $request["names"] . ' ' . $request['lastname'],
				"name" => $request["names"],
				"lastname" =>  $request['lastname'],
				"gender" => '',
				"status" => $request["u_status"],
				"p_status" => $request["p_status"],
				"boss_business" => $bussiness
			);
			//preparacion de nombres de variables de acuerdo a la sesion creada
			$name_sesion = config_sesion(1)['name'];
			$nameVarLogin = $name_sesion . 'login';
			$nameVarLoginInfo = $name_sesion . 'login_info';
			$data_session = json_encode($data_session);
			//creacion de variables de sesion
			$_SESSION[$nameVarLogin] = true;
			$_SESSION[$nameVarLoginInfo] = json_decode($data_session, true);
			//creamos las cookies para el usuario
			setcookie($nameVarLoginInfo, $data_session, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
			setcookie($nameVarLogin, true, time() + (86400 * 30), "/"); // 86400 = 1 day => 30 days
			//preparamos las alertas de bienvenida
			$nombres = $request["names"];
			$apellidos = $request['lastname'];
			$txtUser = decryption($txtUser);
			$data = array(
				"title" => "Inicio de sesion exitoso",
				"message" => "Hola " . $request["names"] . " " . $request['lastname'] . "",
				"html" => <<<HTML
							<div class="text-center">           
								<div class="d-flex align-items-center justify-content-center gap-3 mb-3">
									<div class="text-start">
										<h3 class="fs-4 mb-0 fw-bold">{$nombres} {$apellidos}</h3>
										<span class="badge bg-success rounded-pill fs-6"><i class="bi bi-person-fill"></i> Bienvenido</span>
									</div>
								</div>
								<p class="text-muted mt-4 mb-2">Redirigiendo al panel...</p>
								<div class="spinner-border text-success" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
							</div>
				HTML,
				"type" => "success",
				"icon" => "success",
				"timer" => 2000,
				"status" => true,
				"url" => base_url() . "/pos/dashboard"
			);
			//destruimos la variable que contiene la información del usuario
			unset($request);
			toJson($data);
		} else {
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Usuario o contraseña inválidos",
				"type" => "error",
				"icon" => "error",
				"status" => false
			);
			unset($request);
			toJson($data);
			return;
		}
	}
}
