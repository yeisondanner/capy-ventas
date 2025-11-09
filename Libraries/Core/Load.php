<?php
$controller = ucwords($controller);
if (in_array($folder, $arrayFakeFolder)) {
	//cambiamos el nombre del sistem ya que viene con un fakename
	if ($folder === "im") {
		$folder = "Admin";
	} else if ($folder === "pos") {
		$folder = "pos";
	}
	$controllerFile = "Controllers/" . $folder . "/" . $controller . ".php";
} else {
	$controllerFile = "Controllers/" . $controller . ".php";
}
if (file_exists($controllerFile)) {
	//instaciamos el controlador
	require_once($controllerFile);
	//creamos el objeto
	$controller = new $controller();
	//validamos el metodo
	if (method_exists($controller, $method)) {
		//ejecutamos el metodo y mandamos el parametro
		$controller->{$method}($params);
	} else {
		//redireccionamos con js al notfound porque no encontro el metodo
		echo "<script>window.location.href='" . base_url() . "/im/errors/notfound" . "';</script>";
		die();
	}
} else {
	//redireccionamos con js al notfound el archivo
	echo "<script>window.location.href='" . base_url() . "/im/errors/notfound" . "';</script>";
	die();
}
