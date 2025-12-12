<?php
class BoxModel extends Mysql
{
    protected int $businessId;
    // ? Funciones get
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
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql, [$this->businessId]);
    }

    // ? Funciones insert
    // ? Funciones update
    // ? Funciones delete
}
