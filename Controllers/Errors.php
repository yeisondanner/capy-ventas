<?php

class Errors extends Controllers
{
	public function __construct()
	{
		parent::__construct();
	}

	public function notfound()
	{
		$data['page_id'] = 8;
		$data['page_title'] = "No encontramos esta página";
		$data['page_description'] = "Es posible que la dirección haya cambiado o que el enlace esté roto. Vamos a llevarte de vuelta.";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'error';
		$data['page_js_css'] = "404";
		$this->views->getView($this, "404", $data, 'out');
	}
	public function controllernotfound()
	{
		$data['page_id'] = 13;
		$data['page_title'] = "¿Te has perdido?";
		$data['page_description'] = "Parece que la ruta que buscas no existe. No te preocupes, puedes volver al inicio.";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'error';
		$data['page_js_css'] = "controllers";
		$this->views->getView($this, "controllers", $data, 'out');
	}
	public function methodnotfound()
	{
		$data['page_id'] = 14;
		$data['page_title'] = "Algo salió mal";
		$data['page_description'] = "No pudimos completar esa acción. Por favor, intenta de nuevo o regresa al inicio.";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'error';
		$data['page_js_css'] = "method";
		$this->views->getView($this, "method", $data, 'out');
	}
	public function timeout()
	{

		$data['page_id'] = 8;
		$data['page_title'] = "¡El tiempo voló!";
		$data['page_description'] = "La operación tardó más de lo esperado. Verifica tu conexión e inténtalo nuevamente.";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'timeout';
		$data['page_js_css'] = "timeout";
		$this->views->getView($this, "timeout", $data, 'out');
	}
	public function sessionexpired()
	{
		$data['page_id'] = 16;
		$data['page_title'] = "Tu sesión ha terminado";
		$data['page_description'] = "Por tu seguridad, cerramos tu sesión tras un tiempo de inactividad. Por favor, ingresa de nuevo.";
		$data['page_container'] = "Errors";
		$data['page_view'] = 'timeout';
		$data['page_js_css'] = "timeout";
		$this->views->getView($this, "sessionexpired", $data, 'out');
	}
}
