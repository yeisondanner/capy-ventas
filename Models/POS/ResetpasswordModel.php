<?php
class ResetpasswordModel extends Mysql
{
    /**
     * Encapsulamos la informacion
     */
    protected string $user;
    protected string $password;
    protected int $iduser;
    protected string $email;

    public function __construct()
    {
        parent::__construct();
    }

    // /**
    //  * Método que obtiene los datos del usuario en base al identificador recibido.
    //  *
    //  * @param string $user Identificador de usuario o correo electrónico.
    //  *
    //  * @return array|null Regresa la información del usuario cuando existe o null si no se encontró.
    //  */
    // public function selectUserLogin(string $user)
    // {
    //     $this->user = $user;
    //     $sql = <<<SQL
    //                     SELECT
    //                             up.idUserApp,
    //                             up.user,
    //                             up.password,
    //                             up.`status` AS 'u_status',
    //                             up.plan_expiration_date,
    //                             p.*,
    //                             p.`status` AS 'p_status',
    //                             pl.`name` AS 'Plan'
    //                     FROM
    //                             user_app up
    //                             INNER JOIN people p ON p.idPeople = up.people_id
    //                             LEFT JOIN subscriptions AS s ON (s.user_app_id = up.idUserApp AND s.end_date=up.plan_expiration_date)
    //                             LEFT JOIN plans AS pl ON pl.idPlan = s.plan_id
    //                     WHERE   up.user = ?
    //                             OR p.email = ?
    //                     LIMIT
    //                             1;
    //             SQL;
    //     return $this->select($sql, [$this->user, $this->user]);
    // }
    // /**
    //  * Metodo que que obtiene los negocios asociados al usuario que inicio sesion
    //  * @param int $id 
    //  * @return array
    //  */
    // public function select_business_owner(int $id)
    // {
    //     $this->iduser = $id;
    //     $sql = <<<SQL
    //             SELECT
    //                     b.idBusiness,
    //                     b.`name` AS 'business',
    //                     bt.`name` AS 'category',
    //                     b.direction,
    //                     b.city,
    //                     b.country,
    //                     b.email,
    //                     b.document_number
    //             FROM
    //                     business AS b
    //                     INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
    //             WHERE
    //                     b.`status` = 'Activo' AND b.userapp_id = ?
    //             ORDER BY
    //                     b.idBusiness ASC
    //             LIMIT 1;
    //             SQL;
    //     $request = $this->select($sql, [$this->iduser]);
    //     return $request ?? [];
    // }
    // /**
    //  * Obtenemos el negocio donde el usuario es empleado
    //  * obtenemos uno nada mas
    //  * @param int $id
    //  * @return array
    //  */
    // public function select_business_employee(int $id)
    // {
    //     $this->iduser = $id;
    //     $sql = <<<SQL
    //                     SELECT
    //                             b.idBusiness,
    //                             b.`name` AS 'business',
    //                             bt.`name` AS 'category',
    //                             b.direction,
    //                             b.city,
    //                             b.country,
    //                             b.email,
    //                             b.document_number
    //                     FROM
    //                             user_app AS ua
    //                             INNER JOIN employee AS e ON e.userapp_id = ua.idUserApp
    //                             INNER JOIN business AS b ON b.idBusiness = e.bussines_id
    //                             INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
    //                     WHERE
    //                             ua.idUserApp = ?
    //                     LIMIT
    //                             1;
    //             SQL;
    //     $request = $this->select($sql, [$this->iduser]);
    //     return $request ?? [];
    // }

    public function isExistsUser(string $email_hash)
    {
        $this->email = $email_hash;
        $sql = <<<SQL
            SELECT * FROM user_app
            WHERE user = ?
            LIMIT 1;
            SQL;
        $request = $this->select($sql, [$this->email]);
        return $request ?? [];
    }

    // public function createPeople(string $names, string $lastname, string $email, string $date_of_birth, string $country, string $telephone_prefix, string $phone_number)
    // {
    //     $sql = <<<SQL
    //         INSERT INTO people
    //             (`names`, lastname, `email`, `date_of_birth`, `country`, `telephone_prefix`, `phone_number`)
    //         VALUES
    //             (?, ?, ?, ?, ?, ?, ?);
    //     SQL;

    //     $params = [
    //         $names,
    //         $lastname,
    //         $email,
    //         $date_of_birth,
    //         $country,
    //         $telephone_prefix,
    //         $phone_number
    //     ];

    //     return (int) $this->insert($sql, $params);
    // }

    // public function createUserApp(string $email, string $password, string $people_id)
    // {
    //     $sql = <<<SQL
    //         INSERT INTO user_app
    //             (`user`, `password`, `people_id`)
    //         VALUES
    //             (?, ?, ?);
    //     SQL;

    //     $params = [
    //         $email,
    //         $password,
    //         $people_id
    //     ];

    //     return (int) $this->insert($sql, $params);
    // }
}
