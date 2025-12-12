<?php
class BoxModel extends Mysql
{
    protected int $businessId;
    protected int $boxId;
    protected string $status;
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
    // ? Funciones update
    // ? Funciones delete
}
