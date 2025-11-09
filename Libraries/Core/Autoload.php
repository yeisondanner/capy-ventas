<?php

/**
 * El parametro class se llena cuando entra al controlador y verifica que este 
 * esta heredando un controlador Ejem. : Controllers
 */
spl_autoload_register(function ($class) {
	//echo $class;
	$core = "Libraries/" . 'Core/' . $class . ".php";
	if (file_exists($core)) {
		//requerimos el archivo mediante herencia con un autoload
		require_once("Libraries/" . 'Core/' . $class . ".php");
	}
});
