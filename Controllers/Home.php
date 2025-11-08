<?php

class Home extends Controllers
{
	public function __construct()
	{
		parent::__construct();
	}

	public function home()
	{
		$data['page_id'] = 0;
		$data['page_title'] = "Principal";
		$data['page_description'] = "home";
		$data['page_js_css'] = "home";
		$this->views->getView($this, "home", $data);
	}

}
?>