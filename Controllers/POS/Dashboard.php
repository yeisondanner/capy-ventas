<?php

class Dashboard extends Controllers
{
	/**
	 * Nombre de la variable de sesión que almacena el negocio activo.
	 *
	 * @var string
	 */
	protected string $nameVarBusiness;

	/**
	 * Nombre de la variable de sesión que contiene la información del usuario POS.EWFDWF
	 *
	 * @var string
	 */
	protected string $nameVarLoginInfo;
	public function __construct()
	{
		isSession(1);
		parent::__construct("POS");
		$sessionName = config_sesion(1)['name'] ?? '';
		$this->nameVarBusiness = $sessionName . 'business_active';
		$this->nameVarLoginInfo = $sessionName . 'login_info';
	}

	public function dashboard()
	{
		$data = [
			'page_id'          => 0,
			'page_title'       => 'Dashboard',
			'page_description' => 'Panel de control de la aplicación',
			'page_container'   => 'Dashboard',
			'page_view'        => 'dashboard',
			'page_js_css'      => 'dashboard',
			'page_vars'        => [
				$this->nameVarBusiness,
				$this->nameVarLoginInfo,
			],
		];
		$this->views->getView($this, "dashboard", $data, "POS");
	}
}
