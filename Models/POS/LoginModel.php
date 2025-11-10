<?php
class LoginModel extends Mysql
{
        /**
         * Encapsulamos la informacion
         */
        protected string $user;
        protected string $password;

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
                $this->user = $user;
                $sql = <<<SQL
                        SELECT
                                up.idUserApp,
                                up.user,
                                up.password,
                                up.`status` AS 'u_status',
                                p.*,
                                p.`status` AS 'p_status'
                                
                        FROM
                                user_app up
                                INNER JOIN people p ON p.idPeople = up.people_id
                        WHERE
                                up.user = ?
                                OR p.email = ?
                        LIMIT
                                1;
                SQL;
                return $this->select($sql, [$this->user, $this->user]);
        }
}
