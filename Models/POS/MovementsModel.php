<?php
class MovementsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function select_movements(int $businessId, $minDate = null, $maxDate = null, $searchConcept = null): array
    {
        $sql = "SELECT
            vh.idVoucherHeader,
            CASE
                WHEN vh.voucher_name IS NULL
                    THEN CONCAT('Venta del dia ', DATE(vh.date_time))
                ELSE vh.voucher_name
            END AS voucher_name,
            vh.amount,
            pm.name,
            vh.date_time,
            CONCAT(p.`names`, ' ', p.lastname) AS fullname
        FROM voucher_header vh
        INNER JOIN payment_method pm
            ON vh.payment_method_id = pm.idPaymentMethod
      	INNER JOIN user_app ua
      	ON vh.user_app_id = ua.idUserApp
      	INNER JOIN people p
      	ON ua.people_id = p.idPeople
        WHERE vh.business_id = ?";

        $arrValues = [$businessId];

        if ($minDate != null && $maxDate != null) {
            $sql .= " AND DATE(vh.date_time) BETWEEN ? AND ?";
            array_push($arrValues, $minDate, $maxDate);
        }

        // Agregar filtro por concepto si se proporciona
        if ($searchConcept != null && !empty($searchConcept)) {
            $sql .= " AND (vh.voucher_name LIKE ? OR vh.name_customer LIKE ?)";
            $searchParam = '%' . $searchConcept . '%';
            array_push($arrValues, $searchParam, $searchParam);
        }

        $sql .= " ORDER BY vh.date_time DESC";

        return $this->select_all($sql, $arrValues);
    }

    public function select_voucher(int $voucherId, int $businessId): array
    {
        $sql = <<<SQL
        SELECT
            vh.name_bussines,
            vh.direction_bussines,
            vh.document_bussines,
            vh.date_time,
            vh.name_customer,
            vh.direction_customer,
            vd.name_product,
            vd.unit_of_measurement,
            vd.sales_price_product,
            vh.amount,
            vh.percentage_discount,
            vd.stock_product,
            CONCAT(p.`names`, ' ', p.lastname) AS fullname
        FROM voucher_detail vd
        INNER JOIN voucher_header vh
            ON vd.voucherheader_id = vh.idVoucherHeader
        INNER JOIN user_app ua
            ON vh.user_app_id = ua.idUserApp
        INNER JOIN people p
            ON ua.people_id = p.idPeople
        WHERE vh.idVoucherHeader = ?
          AND vh.business_id = ?
        ORDER BY vd.name_product ASC;
    SQL;

        return $this->select_all($sql, [$voucherId, $businessId]);
    }


    public function getTotals(int $businessId, $minDate = null, $maxDate = null, $searchConcept = null): array
    {
        $sql = "SELECT
                    COALESCE(SUM(vh.amount), 0) AS total_sales
                FROM voucher_header vh
                WHERE vh.business_id = ?";

        $arrValues = [$businessId];

        // Si se proporcionan fechas, añadir la condición de rango
        if ($minDate != null && $maxDate != null) {
            $sql .= " AND DATE(vh.date_time) BETWEEN ? AND ?";
            array_push($arrValues, $minDate, $maxDate);
        }

        // Agregar filtro por concepto si se proporciona
        if ($searchConcept != null && !empty($searchConcept)) {
            $sql .= " AND (vh.voucher_name LIKE ? OR vh.name_customer LIKE ?)";
            $searchParam = '%' . $searchConcept . '%';
            array_push($arrValues, $searchParam, $searchParam);
        }

        $sql .= " AND vh.amount > 0";

        $result = $this->select($sql, $arrValues);

        $totals = [
            'total_sales' => (float)($result['total_sales'] ?? 0),
            'total_expenses' => 0, // Asumiendo que no hay gastos por ahora
        ];

        $totals['balance'] = $totals['total_sales'] - $totals['total_expenses'];

        return $totals;
    }
}
