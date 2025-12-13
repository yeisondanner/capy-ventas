<?php
class BoxModel extends Mysql
{
    protected int $businessId;
    protected int $boxId;
    protected string $status;
    protected int $userId;
    protected float $initialAmount = 0;

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

    public function getBoxByUserId(int $userId)
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

    // ? Funciones update
    // ? Funciones delete
}
