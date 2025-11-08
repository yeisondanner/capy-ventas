<?php

class Errors extends Controllers
{
	public function __construct()
	{
		parent::__construct();
	}

	public function notfound()
	{
		isSession();
		$data['page_id'] = 8;
		permissionInterface($data['page_id']);
		$data['page_title'] = "Error 404";
		$data['page_description'] = "Pagina de error 404 para cuando no se encuentra el contenido";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'error';
		$data['page_js_css'] = "404";
		//variables de sesion que se deben mantener activos, para evitar que se pierdan los datos
		$data['page_vars'] = ["login", "login_info"];
		registerLog("Información de navegación", "El usuario entro a: " . $data['page_title'], 3, $_SESSION['login_info']['idUser']);
		$this->views->getView($this, "404", $data);
	}
	public function timeout()
	{

		$data['page_id'] = 8;
		$data['page_title'] = "Tiempo Vencido";
		$data['page_description'] = "Pagina que muestra la pagina de tiempo vencido";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'timeout';
		$data['page_js_css'] = "timeout";
		registerLog("Información de navegación", "El usuario entro a: " . $data['page_title'], 3);
		$this->views->getView($this, "timeout", $data);
	}
}

/*$notFound = new Errors();
$notFound->notFound();
?>*/