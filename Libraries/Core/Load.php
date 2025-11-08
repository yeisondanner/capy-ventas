<?php
$controller = ucwords($controller);
$controllerFile = "Controllers/" . $controller . ".php";
if (file_exists($controllerFile)) {
	require_once($controllerFile);
	$controller = new $controller();
	if (method_exists($controller, $method)) {
		$controller->{$method}($params);
	} else {
		//redireccionamos con js al notfound
		echo "<script>window.location.href='" . base_url() . "/errors/notfound" . "';</script>";
		die();
	}
} else {
	//redireccionamos con js al notfound
	echo "<script>window.location.href='" . base_url() . "/errors/notfound" . "';</script>";
	die();
}

?>