<?php
class BoxModel extends Mysql
{
    protected int $businessId;
    protected int $boxId;
    protected string $status;
    protected int $userId;
    protected float $initialAmount;
    protected int $boxSessionsId;
    protected string $typeMovement;
    protected string $concept;
    protected string $paymentMethod;
    protected int $limit;

    // ? Funciones get
    public function getUsingBox(int $boxId)
    {
        $this->boxId = $boxId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE box_id = ?
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->boxId]);
    }

    public function getBoxByIdAndBusinessId(int $boxId, int $businessId)
    {
        $this->boxId = $boxId;
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                *
            FROM box
            WHERE idBox = ? AND business_id = ?
            ORDER BY idBox ASC;
        SQL;

        return $this->select($sql, [$this->boxId, $this->businessId]);
    }

    public function getBoxByStatusAndBoxId(string $status, $boxId)
    {
        $this->status = $status;
        $this->boxId = $boxId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE `status` != ? AND box_id = ?
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->status, $this->boxId]);
    }

    public function getBoxSessionsByUserId(int $userId)
    {
        $this->userId = $userId;
        $sql = <<<SQL
            SELECT
                *
            FROM box_sessions
            WHERE userapp_id = ? AND `status` != "Cerrada"
            ORDER BY box_id ASC;
        SQL;

        return $this->select($sql, [$this->userId]);
    }

    // ? Funciones getAll
    public function getBoxs(int $businessId)
    {
        $this->businessId = $businessId;
        $sql = <<<SQL
            SELECT
                idBox,
                `name`,
                `status`
            FROM `box`
            WHERE business_id = ?
            ORDER BY idBox ASC;
        SQL;

        return $this->select_all($sql, [$this->businessId]);
    }

    public function getBoxMovements(int $boxSessionsId)
    {
        $this->boxSessionsId = $boxSessionsId;
        $sql = <<<SQL
            SELECT
                type_movement,
                concept,
                amount,
                payment_method,
                movement_date
            FROM box_movements
            WHERE boxSessions_id = ?
            ORDER BY type_movement ASC;
        SQL;

        return $this->select_all($sql, [$this->boxSessionsId]);
    }

    public function getBoxMovementsByLimit(int $boxSessionsId, int $limit)
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->limit = $limit;
        $sql = <<<SQL
            SELECT
                type_movement,
                concept,
                amount,
                payment_method,
                movement_date
            FROM box_movements
            WHERE boxSessions_id = ?
            ORDER BY type_movement DESC
            LIMIT $this->limit;
        SQL;

        return $this->select_all($sql, [$this->boxSessionsId]);
    }

    public function getPaymentMethods()
    {
        $sql = <<<SQL
            SELECT
                icon,
                `name`
            FROM payment_method
            ORDER BY idPaymentMethod ASC;
        SQL;

        return $this->select_all($sql, []);
    }

    // ? Funciones insert
    public function insertBoxSessions(int $boxId, int $userId, float $initialAmount)
    {
        $this->boxId = $boxId;
        $this->userId = $userId;
        $this->initialAmount = $initialAmount;
        $sql = <<<SQL
            INSERT INTO box_sessions
                (box_id, userapp_id, initial_amount)
            VALUES
                (?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxId, $this->userId, $this->initialAmount]);
    }

    public function insertBoxMovements(int $boxSessionsId, string $typeMovement, string $concept, float $initialAmount, string $paymentMethod)
    {
        $this->boxSessionsId = $boxSessionsId;
        $this->typeMovement = $typeMovement;
        $this->concept = $concept;
        $this->initialAmount = $initialAmount;
        $this->paymentMethod = $paymentMethod;
        $sql = <<<SQL
            INSERT INTO box_movements
                (boxSessions_id, type_movement, concept, amount, payment_method)
            VALUES
                (?, ?, ?, ?, ?);
        SQL;
        return (int) $this->insert($sql, [$this->boxSessionsId, $this->typeMovement, $this->concept, $this->initialAmount, $this->paymentMethod]);
    }


    // ? Funciones update
    // ? Funciones delete
}
