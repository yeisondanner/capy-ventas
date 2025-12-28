<?php
class BoxhistoryModel extends Mysql
{
    private int $business_id;
    /**
     * metodo que trae los datos de la cajas cerradas de manera globarl del negocio
     * @return void
     */
    public function select_box_history(int $business_id)
    {
        $sql = <<<SQL
                SELECT
                    bx.business_id,
                    bxs.idBoxSessions,
                    bxs.box_id,
                    bxs.opening_date,
                    bxs.closing_date,
                    bxs.initial_amount,
                    bcc.expected_amount,
                    bcc.counted_amount,
                    bcc.difference,
                    bcc.notes,
                    bcc.`type`,
                    CONCAT(p.`names`,' ',p.lastname) AS 'fullname',
                    bxs.`status`
                FROM
                    box AS bx
                    INNER JOIN box_sessions AS bxs ON bxs.box_id = bx.idBox
                    INNER JOIN box_cash_counts AS bcc ON (bcc.box_session_id = bxs.idBoxSessions AND bcc.`type`='Cierre')
                    INNER JOIN user_app AS ua ON ua.idUserApp = bxs.userapp_id
                    INNER JOIN people AS p ON p.idPeople = ua.people_id
                    WHERE bx.business_id=? AND bxs.`status`='Cerrada'
                    ORDER BY bxs.closing_date DESC;
        SQL;
        $this->business_id = $business_id;
        $request = $this->select_all($sql, [$this->business_id]);
        return $request;
    }
}
