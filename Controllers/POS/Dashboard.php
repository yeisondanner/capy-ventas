<?php

class Dashboard extends Controllers
{
	public function __construct()
	{
		isSession(1);
		parent::__construct("POS");
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
		];
		$this->views->getView($this, "dashboard", $data, "POS");
	}
}
