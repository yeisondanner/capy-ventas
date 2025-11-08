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
	require_once($controllerFile);
	$controller = new $controller();
	if (method_exists($controller, $method)) {
		$controller->{$method}($params);
	} else {
		//redireccionamos con js al notfound
		echo "<script>window.location.href='" . base_url() . "/im/errors/notfound" . "';</script>";
		die();
	}
} else {
	//redireccionamos con js al notfound
	echo "<script>window.location.href='" . base_url() . "/im/errors/notfound" . "';</script>";
	die();
}
