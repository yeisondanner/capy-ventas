<?php

class Dashboard extends Controllers
{
	public function __construct()
	{

		parent::__construct("POS");
	}

	public function dashboard()
	{
		$data['page_id'] = 41;
		$data['page_title'] = "Dashboard de la app";
		$data['page_description'] = "Dashboard";
		$data['page_container'] = "Dashboard";
		$data['page_view'] = 'dashboard';
		$data['page_js_css'] = "dashboard";
		$this->views->getView($this, "dashboard", $data, "POS");
	}
}
