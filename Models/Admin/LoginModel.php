<?php
class LoginModel extends Mysql
{
	private int $idUser;
	private string $user;
	private string $password;
	private string $email;
	private string $profile;
	private string $fullName;
	private string $gender;
	private string $dni;
	private string $status;
	private int $role_id;
	private int $attemp;
	private string $token;
	private string $online;
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Metodo para obtener el usuario
	 * @param string $user
	 */
	public function selectUserLogin(string $user)
	{
		$this->user = $user;
		$arrValues = array(
			$this->user,
			$this->user
		);
		$sql = "SELECT  tbu.*,tbr.r_name FROM tb_user AS tbu INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id WHERE tbu.u_user=? OR tbu.u_email=?;";
		$request = $this->select($sql, $arrValues);
		return $request;
	}
	/**
	 * Metodo que permite actualizar el estado de online del usuario
	 * @param int $id
	 * @param string $online
	 * @return bool
	 */
	public function update_online_user(int $id, string $online)
	{
		$this->idUser = $id;
		$this->online = $online;
		$arrValues = array(
			$this->online,
			$this->idUser

		);
		$sql = "UPDATE `tb_user` SET `u_online`=? WHERE  `idUser`=?;";
		$request = $this->update($sql, $arrValues);
		return $request;
	}
	/**
	 * Metodo que permite obtener los usuarios en linea
	 * @return array
	 */
	public function select_users_online()
	{
		$sql = "SELECT tbu.u_fullname FROM tb_user AS tbu WHERE tbu.u_online=1;";
		$request = $this->select_all($sql);
		return $request;
	}
	/**
	 * Metodo que se encarga de actualizas el intento de inicio de sesion
	 * @param  int  $id
	 * @param  int $attemp
	 * @return void
	 */
	public function update_attempts_login(int $id, int $attemp)
	{
		$this->idUser = $id;
		$this->attemp = $attemp;
		$arrValues = array(
			$this->attemp,
			$this->idUser
		);
		$sql = "UPDATE `tb_user` SET `u_login_attempts`=? WHERE  `idUser`=?;";
		$request = $this->update($sql, $arrValues);
		return $request;
	}
	/**
	 * Metodo que permite el ingreso de un token de recuperacion
	 * @param int $id
	 * @param string $token
	 * @return void
	 */
	public function insertToken(string $token, int $id)
	{
		$this->idUser = $id;
		$this->token = $token;
		$arrValues = array(
			$this->token,
			$this->idUser
		);
		$sql = "UPDATE `tb_user` SET `u_reset_token_password`=? WHERE  `idUser`=?;";
		$request = $this->update($sql, $arrValues);
		return $request;
	}
	/**
	 * Consultamos el token de recuperacion para ver si tiene algun dato asociado, que no me recupere la contraseña
	 * @param string $token
	 * @return array
	 */
	public function select_info_user_by_token(string $token)
	{
		$this->token = $token;
		$sql = "SELECT tbu.idUser,tbu.u_user,tbu.u_email,tbu.u_profile,tbu.u_fullname,tbu.u_reset_token_password FROM tb_user AS tbu WHERE tbu.u_reset_token_password=?;";
		$request = $this->select($sql, [$this->token]);
		return $request;
	}
	/**
	 * Metodo que te permite actualizar la contraseña de un usuario mediante consulta 
	 * del token de recuperacion
	 * @param  string  $token
	 * @param  string $pass
	 * @return  void
	 */
	public function update_password_by_token(string $token, string $pass)
	{
		$this->token = $token;
		$this->password = $pass;
		$arrValues = array(
			$this->password,
			$this->token
		);
		$sql = "UPDATE `tb_user` SET `u_password`=?,`u_reset_token_password`='' WHERE `u_reset_token_password`=?";
		$request = $this->update($sql, $arrValues);
		return $request;
	}
}
