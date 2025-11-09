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
}
