<?php
class MovementsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function select_movements(int $businessId): array
    {
        $sql = <<<SQL
        SELECT
            vh.idVoucherHeader,
            CASE
                WHEN vh.voucher_name IS NULL
                    THEN CONCAT('Venta del dia ', DATE(vh.date_time))
                ELSE vh.voucher_name
            END AS voucher_name,
            vh.amount,
            pm.name,
            vh.date_time
        FROM voucher_header vh
        INNER JOIN payment_method pm
            ON vh.payment_method_id = pm.idPaymentMethod
        WHERE vh.business_id = ?
        ORDER BY vh.date_time DESC;
    SQL;

        return $this->select_all($sql, [$businessId]);
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
            vh.percentage_discount
        FROM voucher_detail vd
        INNER JOIN voucher_header vh
            ON vd.voucherheader_id = vh.idVoucherHeader
        WHERE vh.idVoucherHeader = ?
          AND vh.business_id = ?
        ORDER BY vd.name_product ASC;
        SQL;

        return $this->select_all($sql, [$voucherId, $businessId]);
    }

    public function getTotals(int $businessId): array
    {
        $sql = <<<SQL
                SELECT
                    COALESCE(SUM(vh.amount), 0) AS total_sales
                FROM voucher_header vh
                WHERE vh.business_id = ?
                AND vh.amount > 0;
                SQL;

        $result = $this->select($sql, [$businessId]);

        $totals = [
            'total_sales' => (float)($result['total_sales'] ?? 0),
            'total_expenses' => 0,
        ];

        $totals['balance'] = $totals['total_sales'] - $totals['total_expenses'];

        return $totals;
    }
}
