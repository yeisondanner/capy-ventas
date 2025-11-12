<?php
class LoginModel extends Mysql
{
        /**
         * Encapsulamos la informacion
         */
        protected string $user;
        protected string $password;
        protected int $iduser;

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
        /**
         * Metodo que que obtiene los negocios asociados al usuario que inicio sesion
         * @param int $id 
         * @return array
         */
        public function select_business(int $id)
        {
                $this->iduser = $id;
                $sql = <<<SQL
                        SELECT
                                *
                        FROM
                                business AS b
                                INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
                                WHERE b.userapp_id=?;
                SQL;
                $request = $this->select_all($sql, [$this->iduser]);
                return $request ?? [];
        }
}
