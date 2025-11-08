<?php
class Conexion
{
	private $conect;

	public function __construct()
	{
		$connectionString = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
		try {
			$this->conect = new PDO($connectionString, DB_USER, DB_PASSWORD);
			$this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo "conexión exitosa";
		} catch (PDOException $e) {
			$this->conect = 'Conexion Cerrada';
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			$message = "Error: " . $e->getMessage() . "<br>" . $this->conect . " - " . $e->getCode();
			registerLog("Ocurrio un error inesperado", $message, 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => $message,
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
	}

	public function conect()
	{
		return $this->conect;
	}
}

?>