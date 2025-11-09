<?php

class UsersModel extends Mysql
{
    private $idUser;
    private $user;
    private $password;
    private $email;
    private $profile;
    private $fullname;
    private $gender;
    private $dni;
    private $role;
    private $status;
    private $numStorage;
    private $txtRootFolder;

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Funcion que se encarga de la seleccion de todos los usuarios
     * @return array
     */
    public function select_users(): array
    {
        $query = <<<SQL
                    SELECT
                        tbu.*,
                        tbr.r_name
                    FROM
                        tb_user AS tbu
                        INNER JOIN tb_role AS tbr ON tbr.idRole = tbu.role_id
                        ORDER BY tbu.u_registrationDate DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }
    /**
     * Funcion que inserta el registro en la tabla de la base de datos
     * @return void
     */
    public function insert_user($user, $pasword, $email, $profile = null, $fullname, $gender, $dni, $role, $numStorage, $txtRootFolder)
    {
        $sql = "INSERT INTO `tb_user` (`u_user`, `u_password`, `u_email`, `u_profile`, `u_fullname`, `u_gender`, `u_dni`, `role_id`,`u_space_limit`,`u_personal_folder_name`) VALUES (?,?,?,?,?,?,?,?,?,?);";
        $arrValues = array(
            $this->user = $user,
            $this->password = $pasword,
            $this->email = $email,
            $this->profile = $profile,
            $this->fullname = $fullname,
            $this->gender = $gender,
            $this->dni = $dni,
            $this->role = $role,
            $this->numStorage = $numStorage,
            $this->txtRootFolder = $txtRootFolder
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que eliminar un registro de la base de datos
     * @param mixed $id
     * @return bool
     */
    public function delete_user($id)
    {
        $this->idUser = $id;
        $sql = "DELETE FROM `tb_user` WHERE idUser = ?";
        $arrValues = array($this->idUser);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }
    /**
     * Funcion que actualiza el registro en la base de datos
     * @param mixed $intId
     * @param mixed $strUser
     * @param mixed $strPassword
     * @param mixed $strEmail
     * @param mixed $strProfile
     * @param mixed $strFullName
     * @param mixed $strGender
     * @param mixed $strDNI
     * @param mixed $intRole
     * @param mixed $slctStatus
     * @return bool
     */
    public function update_user($intId, $strUser, $strPassword, $strEmail, $strProfile, $strFullName, $strGender, $strDNI, $intRole = false, $slctStatus = false)
    {
        $this->idUser = $intId;
        $this->user = $strUser;
        $this->password = $strPassword;
        $this->email = $strEmail;
        $this->profile = $strProfile;
        $this->fullname = $strFullName;
        $this->gender = $strGender;
        $this->dni = $strDNI;
        $this->role = $intRole;
        $this->status = $slctStatus;
        if (!$this->role && !$this->status) {
            $sql = "UPDATE `tb_user` SET `u_user`=?,`u_password`=?,`u_email`=?,`u_profile`=?,`u_fullname`=?,`u_gender`=?,`u_dni`=?,`u_login_attempts`=0,`u_reset_token_password`='' WHERE idUser=?";
            $arrValues = array(
                $this->user,
                $this->password,
                $this->email,
                $this->profile,
                $this->fullname,
                $this->gender,
                $this->dni,
                $this->idUser
            );
        } else {
            $sql = "UPDATE `tb_user` SET `u_user`=?,`u_password`=?,`u_email`=?,`u_profile`=?,`u_fullname`=?,`u_gender`=?,`u_dni`=?,`role_id`=?,`u_status`=?,`u_login_attempts`=0,`u_reset_token_password`='' WHERE idUser=?";
            $arrValues = array(
                $this->user,
                $this->password,
                $this->email,
                $this->profile,
                $this->fullname,
                $this->gender,
                $this->dni,
                $this->role,
                $this->status,
                $this->idUser
            );
        }

        $request = $this->update($sql, $arrValues);
        return $request;
    }
    /**
     * Meotodo que obtiene el usuario por el id
     * @param int $idUser
     */
    public function select_user_by_Id(int $idUser)
    {
        $this->idUser = $idUser;
        $sql = "SELECT*FROM tb_user AS tbu 
                INNER JOIN tb_role AS tbr ON tbr.idRole=tbu.role_id WHERE tbu.idUser=?;";
        $arrValues = array($this->idUser);
        $request = $this->select($sql, $arrValues);
        return $request;
    }
    /**
     * Metdodo que obtiene que el usuario por el dni  para evitar duplicados o errores
     * @param string $dni
     */
    public function select_user_by_dni(string $dni)
    {
        $this->dni = $dni;
        $sql = "SELECT * FROM tb_user WHERE u_dni = ?";
        $arrValues = array($this->dni);
        $request = $this->select($sql, $arrValues);
        return $request;
    }
    /**
     * Metodo que obtiene el usuario por el email para evitar duplicados o errores
     * @param string $email
     */
    public function select_user_by_email(string $email)
    {
        $this->email = $email;
        $sql = "SELECT * FROM tb_user WHERE u_email = ?";
        $arrValues = array($this->email);
        $request = $this->select($sql, $arrValues);
        return $request;
    }
    /**
     * Metodo que obtiene el usuario por el nombre de usuario para evitar duplicados o errores
     * @param string $user
     */
    public function select_user_by_user(string $user)
    {
        $this->user = $user;
        $sql = "SELECT * FROM tb_user WHERE u_user = ?";
        $arrValues = array($this->user);
        $request = $this->select($sql, $arrValues);
        return $request;
    }
}
