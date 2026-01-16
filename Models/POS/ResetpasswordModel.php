<?php
class ResetpasswordModel extends Mysql
{
    /**
     * Encapsulamos la informacion
     */
    protected int $idUserApp;
    protected string $password;
    protected string $email;

    public function __construct()
    {
        parent::__construct();
    }

    // Obtener usuario por email 
    public function getUserByEmail(string $emailEncrypted)
    {
        $this->email = $emailEncrypted;

        $sql = <<<SQL
        SELECT 
            ua.idUserApp AS id,
            ua.people_id,
            ua.user,
            ua.status,
            p.idPeople,
            p.email
        FROM user_app ua
        INNER JOIN people p ON p.idPeople = ua.people_id
        WHERE p.email = ?
        LIMIT 1;
    SQL;

        return $this->select($sql, [$this->email]);
    }


    //Actualizar password por idUserApp 
    public function updatePassword(int $userId, string $passwordEncrypted)
    {
        $this->idUserApp = $userId;
        $this->password  = $passwordEncrypted;

        $sql = <<<SQL
        UPDATE user_app
        SET password = ?, update_date = CURRENT_TIMESTAMP
        WHERE idUserApp = ?
        LIMIT 1;
    SQL;

        return $this->update($sql, [$this->password, $this->idUserApp]);
    }

    public function isExistsPeople(string $email_hash)
    {
        $this->email = $email_hash;
        $sql = <<<SQL
            SELECT * FROM people
            WHERE email = ?
            LIMIT 1;
            SQL;
        $request = $this->select($sql, [$this->email]);
        return $request;
    }
}
