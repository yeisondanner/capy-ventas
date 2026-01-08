<?php

class Controllers
{
	protected $views;
	protected $model = null;
	protected string $folderModel;
	public function __construct(string $folderModel = "Admin")
	{
		$this->folderModel = $folderModel;
		$this->views = new Views();
		$this->loadModel();
	}

	public function loadModel()
	{
		//HomeModel.php
		$model = get_class($this) . "Model";
		$routClass = "Models/" . $model . ".php";
		if (!empty($this->folderModel)) {
			$routClass = "Models/{$this->folderModel}/" . $model . ".php";
		}
		if (file_exists($routClass)) {
			require_once($routClass);
			$this->model = new $model();
		} else {
			echo "No existe el archivo del modelo";
			die();
		}
	}
}
