<?php

class Views
{
	function getView($controller, $view, $data = "", string $type = "Admin")
	{
		$controller = get_class($controller);
		if ($type === "Admin") {
			if ($controller == "Home") {
				$view = "Views/" . $view . ".php";
			} else {
				$view = "Views/App/Admin/" . $controller . "/" . $view . ".php";
			}
		} else if ($type === "POS") {
			if ($controller == "Home") {
				$view = "Views/" . $view . ".php";
			} else {
				$view = "Views/App/POS/" . $controller . "/" . $view . ".php";
			}
		} else if ($type === "out") {
			$view = "Views/App/" . $controller . "/" . $view . ".php";
		} else {
			echo "Error: Tipo de vista no encontrado";
			die();
		}
		require_once($view);
	}
}
