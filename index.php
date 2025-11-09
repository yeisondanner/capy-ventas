<?php
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
$url = !empty($_GET['url']) ? $_GET['url'] : 'home/home';
$arrUrl = explode("/", $url);
$folder = $arrUrl[0];
$arrayFakeFolder = ["im", "pos"];
//validamos que el folder esta dentro o no del parametro
if (in_array(strtolower($folder), $arrayFakeFolder)) {
	$controller = $arrUrl[1];
	$method = $arrUrl[1];

	if (!empty($arrUrl[2])) {
		if ($arrUrl[2] != "") {
			$method = $arrUrl[2];
		}
	}
	$params = "";
	if (!empty($arrUrl[3])) {
		if ($arrUrl[3] != "") {
			for ($i = 3; $i < count($arrUrl); $i++) {
				$params .= $arrUrl[$i] . ',';
				# code...
			}
			$params = trim($params, ',');
		}
	}
} else {
	$controller = $folder;
	$method = $controller;
	if (!empty($arrUrl[1])) {
		if ($arrUrl[1] != "") {
			$method = $arrUrl[1];
		}
	}
	$params = "";
	if (!empty($arrUrl[2])) {
		if ($arrUrl[2] != "") {
			for ($i = 2; $i < count($arrUrl); $i++) {
				$params .= $arrUrl[$i] . ',';
				# code...
			}
			$params = trim($params, ',');
		}
	}
}
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
