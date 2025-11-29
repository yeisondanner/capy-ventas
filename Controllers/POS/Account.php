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
}
