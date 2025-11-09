<?php
class LoginModel extends Mysql
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Metodo para obtener el usuario
	 * @param string $user
	 * @param string $password
	 */
	public function selectUserLogin(string $user)
	{
		$this->user = $user;
		$arrValues = array(
			$this->user,
			$this->user
		);
		$sql = "SELECT  up.`user`, up.`password`, up.`status`, CONCAT(p.`names`, ' ', p.lastname) AS fullname, p.idPeople, up.idUserApp 
		FROM user_app up
		INNER JOIN people p ON p.idPeople=up.people_id WHERE `user`=?;";
		$request = $this->select($sql, $arrValues);
		return $request;
	}
	
	
}
