<?php
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
$url = !empty($_GET['url']) ? $_GET['url'] : 'home/home';
$arrUrl = explode("/", $url);
$folder = $arrUrl[0];
if (count($arrUrl) === 1) {
	$controller = "home";
	$method = "home";
} else {
	$controller = $arrUrl[1];
	$method = $arrUrl[1];
}
$params = "";
if (!empty($arrUrl[2])) {
	if ($arrUrl[2] != "") {
		$method = $arrUrl[2];
	}
}

if (!empty($arrUrl[3])) {
	if ($arrUrl[3] != "") {
		for ($i = 3; $i < count($arrUrl); $i++) {
			$params .= $arrUrl[$i] . ',';
			# code...
		}
		$params = trim($params, ',');
	}
}
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
