<?php
class MovementsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function select_movements()
    {
        $sql = "SELECT
                CASE
                    WHEN vh.voucher_name IS NULL
                        THEN CONCAT('Venta del dia ', DATE(vh.date_time))
                    ELSE vh.voucher_name
                END AS voucher_name,
                vh.amount,
                pm.`name`,
                vh.date_time
            FROM voucher_header vh
            INNER JOIN payment_method pm
            ON vh.payment_method_id = pm.idPaymentMethod;";
        $request = $this->select_all($sql);
        return $request;
    }
}
