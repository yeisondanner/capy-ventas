<?php
class BoxhistoryModel extends Mysql
{
    /**
     * metodo que trae los datos de la cajas cerradas de manera globarl del negocio
     * @return void
     */
    public function select_box_history(int $business_id, $minDate = null, $maxDate = null)
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
                    bxs.`status`,
                    bcc.notes
                FROM
                    box AS bx
                    INNER JOIN box_sessions AS bxs ON bxs.box_id = bx.idBox
                    INNER JOIN box_cash_counts AS bcc ON (bcc.box_session_id = bxs.idBoxSessions AND bcc.`type`='Cierre')
                    INNER JOIN user_app AS ua ON ua.idUserApp = bxs.userapp_id
                    INNER JOIN people AS p ON p.idPeople = ua.people_id
                    WHERE bx.business_id=? AND bxs.`status`='Cerrada'
        SQL;
        $arrValues = [$business_id];
        if ($minDate != null && $maxDate != null) {
            $sql .= " AND DATE(bxs.closing_date) BETWEEN ? AND ?";
            array_push($arrValues, $minDate, $maxDate);
        }
        $sql .= "ORDER BY bxs.closing_date DESC;";
        $request = $this->select_all($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene los detalles de una sesión de caja cerrada
     * @param int $boxSessionId
     * @param int $businessId
     * @return array|null
     */
    public function getBoxSessionDetails(int $boxSessionId, int $businessId): ?array
    {
        // 1. Obtener información principal de la sesión y el cierre final
        $sqlSession = <<<SQL
                SELECT
                    b.name AS name_business,
                    b.direction AS direction_business,
                    b.document_number AS document_business,
                    b.logo,
                    bx.name AS box_name,
                    CONCAT(p.`names`,' ',p.lastname) AS fullname,
                    bxs.opening_date,
                    bxs.closing_date,
                    bxs.initial_amount,
                    bcc.expected_amount,
                    bcc.counted_amount,
                    bcc.difference,
                    bcc.notes
                FROM
                    box_sessions AS bxs
                    INNER JOIN box AS bx ON bx.idBox = bxs.box_id
                    INNER JOIN business AS b ON b.idBusiness = bx.business_id
                    INNER JOIN user_app AS ua ON ua.idUserApp = bxs.userapp_id
                    INNER JOIN people AS p ON p.idPeople = ua.people_id
                    INNER JOIN box_cash_counts AS bcc ON (bcc.box_session_id = bxs.idBoxSessions AND bcc.`type`='Cierre')
                WHERE
                    bxs.idBoxSessions = ? AND
                    bx.business_id = ?
        SQL;
        $sessionData = $this->select($sqlSession, [$boxSessionId, $businessId]);

        if (empty($sessionData)) {
            return null;
        }

        // 2. Obtener historial de arqueos (Cierres y Arqueos intermedios si hubiera)
        $sqlCounts = <<<SQL
            SELECT
                bcc.idBoxCashCounts,
                bcc.cash_counts_date as date_time,
                bcc.type,
                bcc.expected_amount,
                bcc.counted_amount,
                bcc.difference,
                bcc.notes
            FROM
                box_cash_counts AS bcc
            WHERE
                bcc.box_session_id = ?
            ORDER BY bcc.cash_counts_date DESC
        SQL;
        $countsHistory = $this->select_all($sqlCounts, [$boxSessionId]);

        // Obtener detalles de denominaciones para todos los arqueos de la sesión
        $sqlDetails = <<<SQL
            SELECT
                bccd.box_cash_count_id,
                bccd.quantity,
                bccd.total,
                cd.value,
                cd.label,
                cd.type AS denomination_type
            FROM
                box_cash_count_details AS bccd
                INNER JOIN box_cash_counts AS bcc ON bccd.box_cash_count_id = bcc.idBoxCashCounts
                INNER JOIN currency_denominations AS cd ON bccd.currency_denomination_id = cd.idDenomination
            WHERE
                bcc.box_session_id = ?
            ORDER BY cd.value DESC
        SQL;
        $allDetails = $this->select_all($sqlDetails, [$boxSessionId]);

        // Asociar detalles a su respectivo arqueo
        if (!empty($countsHistory) && !empty($allDetails)) {
            foreach ($countsHistory as &$count) {
                $count['details'] = [];
                foreach ($allDetails as $detail) {
                    if ($detail['box_cash_count_id'] == $count['idBoxCashCounts']) {
                        $count['details'][] = $detail;
                    }
                }
            }
        } else {
            foreach ($countsHistory as &$count) {
                $count['details'] = [];
            }
        }

        // 3. Obtener historial de movimientos (Ingresos/Egresos)
        $sqlMovements = <<<SQL
            SELECT
                bm.movement_date AS created_at,
                bm.type_movement,
                bm.concept,
                bm.amount,
                bm.payment_method
            FROM
                box_movements AS bm
            WHERE
                bm.boxSessions_id = ?
            ORDER BY bm.movement_date DESC
        SQL;
        $movementsHistory = $this->select_all($sqlMovements, [$boxSessionId]);

        $sessionData['counts_history'] = $countsHistory;
        $sessionData['movements_history'] = $movementsHistory;

        return $sessionData;
    }
}
