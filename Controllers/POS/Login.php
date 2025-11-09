<?php

class Login extends Controllers
{
	public function __construct()
	{

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
		header('Content-Type: application/json; charset=utf-8');
		//validacion del Método POST
		if (!$_POST) {
			registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de iniciar session", 1);
			$data = array(
				"title" => "Ocurrió un error inesperado",
				"message" => "Método POST no encontrado",
				"type" => "error",
				"status" => false
			);
			toJson($data); return;
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
			toJson($data); return;
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
			toJson($data); return;
		}
		$request = $this->model->selectUserLogin($txtUser);
		if ($request) {

			//validamos si la contraseña coinciden
			if (password_verify($txtPassword, $request['password'])) {

				//verificamos si la cuenta se encuentra activa
				if ($request["status"] == "Inactivo") {
					registerLog("Ocurrió un error inesperado", "El usuario " . $request["fullname"] . ", no inicio sesión por motivo de cuenta desactivada", 1, $request["idUserApp"]);
					$data = array(
						"title" => "Ocurrió un error inesperado",
						"message" => "La cuenta del usuario actualmente se encuentra en estado Inactivo",
						"type" => "error",
						"status" => false
					);
					unset($request);
					toJson($data); return;
				}
				//creamos las variables de session para el usuario
				$_SESSION['user_data_pos'] = array(
					"idUserApp" => $request["idUserApp"],
					"idPeople" => $request["idPeople"],
					"user" => $request["user"],
					"fullName" => $request["fullname"],
					"status" => $request["status"]
				);

				registerLog("Inicio de sesión exitoso", "El usuario " . $request["fullname"] . ", completo de manera satisfactoria el inicio de sesion", 2, $request["idUserApp"]);
				$data = array(
					"title" => "Inicio de sesion exitoso",
					"message" => "Hola " . $request["fullname"] . ", se completó de manera satisfactoria el inicio de sesión",
					"type" => "success",
					"status" => true,
                                        "redirection" => base_url() . "/pos/dashboard"
				);
				//destruimos la variable que contiene la información del usuario
				unset($request);
				toJson($data); return;
			} else {
				registerLog("Ocurrió un error inesperado", "El usuario {$txtUser} o contraseña que esta intentando ingresar no existe", 1);
				$data = array(
					"title" => "Ocurrió un error inesperado",
					"message" => "La cuenta de usuario no existe",
					"type" => "error",
					"status" => false
				);
				unset($request);
				toJson($data); return;
			}
		}
		registerLog("Ocurrió un error inesperado", "La cuenta de usuario {$txtUser} no existe", 1);
		$data = array(
			"title" => "Ocurrió un error inesperado",
			"message" => "Usuario o contraseña inválidos",
			"type" => "error",
			"status" => false
		);
		toJson($data);
		return;
	}
}
