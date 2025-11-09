<?php
class LoginModel extends Mysql
{
	public function __construct()
	{
		parent::__construct();
	}

        /**
         * Método que obtiene los datos del usuario en base al identificador recibido.
         *
         * @param string $user Identificador de usuario o correo electrónico.
         *
         * @return array|null Regresa la información del usuario cuando existe o null si no se encontró.
         */
        public function selectUserLogin(string $user)
        {

                $sql = "SELECT  up.user, up.password, up.status, CONCAT(p.names, ' ', p.lastname) AS fullname, p.idPeople, up.idUserApp
                FROM user_app up
		INNER JOIN people p ON p.idPeople=up.people_id WHERE up.user=? OR p.email=? LIMIT 1;";
		return $this->select($sql, [$user, $user]);
	}
	
	
}
