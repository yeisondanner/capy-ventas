<?php

class Mysql extends Conexion
{
	private $conexion;
	private $strquery;
	private $arrValues;

	function __construct()
	{
		$this->conexion = new Conexion();
		$this->conexion = $this->conexion->conect();
	}

	//Insertar un registro
	public function insert(string $query, array $arrValues)
	{
		try {
			$this->strquery = $query;
			$this->arrValues = $arrValues;
			$insert = $this->conexion->prepare($this->strquery);
			$resInsert = $insert->execute($this->arrValues);
			if ($resInsert) {
				$lastInsert = $this->conexion->lastInsertId();
			} else {
				$lastInsert = 0;
			}
			return $lastInsert;
		} catch (PDOException $e) {
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			registerLog("Ocurrio un error inesperado", "Error: " . $e->getMessage() . " - " . $e->getCode(), 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => "Error: " . $e->getMessage(),
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}

	}
	//Busca un registro
	public function select(string $query, array $arrValues = [])
	{
		try {
			$this->strquery = $query;
			$this->arrValues = $arrValues;
			$result = $this->conexion->prepare($this->strquery);
			$result->execute($this->arrValues);
			$data = $result->fetch(PDO::FETCH_ASSOC);
			return $data;
		} catch (PDOException $e) {
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			registerLog("Ocurrio un error inesperado", "Error: " . $e->getMessage() . " - " . $e->getCode(), 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => "Error: " . $e->getMessage(),
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
	}
	//Devuelve todos los registros
	public function select_all(string $query, array $arrValues = [])
	{
		try {
			$this->strquery = $query;
			$this->arrValues = $arrValues;
			$result = $this->conexion->prepare($this->strquery);
			$result->execute($this->arrValues);
			$data = $result->fetchall(PDO::FETCH_ASSOC);
			return $data;
		} catch (PDOException $e) {
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			registerLog("Ocurrio un error inesperado", "Error: " . $e->getMessage() . " - " . $e->getCode(), 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => "Error: " . $e->getMessage(),
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
	}
	//Actualiza registros
	public function update(string $query, array $arrValues)
	{
		try {
			$this->strquery = $query;
			$this->arrValues = $arrValues;
			$update = $this->conexion->prepare($this->strquery);
			$resExecute = $update->execute($this->arrValues);
			return $resExecute;
		} catch (PDOException $e) {
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			registerLog("Ocurrio un error inesperado", "Error: " . $e->getMessage() . " - " . $e->getCode(), 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => "Error: " . $e->getMessage(),
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
	}
	//Eliminar un registros
	public function delete(string $query, array $arrValues)
	{
		try {
			$this->arrValues = $arrValues;
			$this->strquery = $query;
			$result = $this->conexion->prepare($this->strquery);
			$del = $result->execute($this->arrValues);
			return $del;
		} catch (PDOException $e) {
			$idUser = isset($_SESSION['login_info']) ? $_SESSION['login_info']['idUser'] : 0;
			registerLog("Ocurrio un error inesperado", "Error: " . $e->getMessage() . " - " . $e->getCode(), 1, $idUser);
			$data = array(
				"title" => "Ocurrio un error grave",
				"message" => "Error: " . $e->getMessage(),
				"type" => "error",
				"status" => false
			);
			toJson($data);
		}
	}
}
