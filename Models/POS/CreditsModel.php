<?php
class CreditsModel extends Mysql
{
    private int $idBusiness;
    private int $idCustomer;
    private string $search;
    private string $startDate;
    private string $endDate;
    private string $saleType;
    private string $paymentStatus;
    /**
     * Inicializa el modelo base y establece la conexión con la base de datos.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Metodo que se encarga de obtener todos los creditos
     */
    public function getCreditsWithFilters(int $idBusiness, string $search, string $startDate, string $endDate)
    {
        $sql = <<<SQL
                SELECT
                    c.idCustomer,
                    concat(c.fullname,' (',
                    c.document_number,
                    ')') AS 'fullname',
                    dt.`name` AS 'document_type',
                    c.document_number,
                    c.phone_number,
                    c.email,
                    c.direction,
                    c.credit_limit,
                    c.default_interest_rate,
                    c.current_interest_rate,
                    c.billing_date,
                    -- Suma condicional: solo suma si es 'Credito', de lo contrario suma 0
                    IFNULL(
                        SUM(
                            CASE WHEN vh.`status` = 'Pendiente' THEN vh.amount ELSE 0 END
                        ),
                        0.00
                    ) AS 'amount_pending'
                FROM
                    customer AS c
                    INNER JOIN document_type AS dt ON dt.idDocumentType = c.documenttype_id
                    INNER JOIN voucher_header AS vh ON vh.customer_id = c.idCustomer
                    AND vh.business_id = c.business_id -- Mantenemos la relación de negocio en el JOIN
                WHERE
                    vh.sale_type = 'Credito'
                    AND c.business_id = ? 
        SQL;
        $this->idBusiness = $idBusiness;
        $arrValues = [$this->idBusiness];
        if (!empty($search) && $search != null) {
            $sql .= "AND (c.fullname LIKE ? OR c.document_number LIKE ?)";
            $this->search = '%' . $search . '%';
            array_push($arrValues, $this->search, $this->search);
        }
        if (!empty($startDate) && $startDate != null && !empty($endDate) && $endDate != null) {
            $sql .= "AND (DATE(c.billing_date) BETWEEN ? AND ?)";
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            array_push($arrValues, $this->startDate, $this->endDate);
        }
        $sql .= <<<SQL
                GROUP BY
                    c.idCustomer
                ORDER BY
                    vh.registration_date DESC;
        SQL;
        return $this->select_all($sql, $arrValues);
    }
    /**
     * Metodo que se encarga de obtener la informacion de un cliente limitado por el negocio
     * @param int $idCustomer
     * @param int $idBusiness
     * @return array
     */
    public function getInfoCustomer(int $idCustomer, int $idBusiness)
    {
        $sql = <<<SQL
                SELECT
                    c.idCustomer,
                    c.fullname,
                    dt.`name` AS 'documentType',
                    c.document_number,
                    c.phone_number,
                    c.email,
                    c.direction,
                    c.credit_limit,
                    c.default_interest_rate,
                    c.current_interest_rate,
                    c.billing_date,
                    DAY(c.billing_date) AS 'day_billing',
                    c.`status`,
                    IFNULL(
                        SUM(vh.amount),
                        0
                    ) AS 'amount_total',
                    (
                        c.credit_limit - IFNULL(
                            SUM(vh.amount),
                            0
                        )
                    ) AS 'amount_disp',
                    CASE WHEN c.credit_limit != 0 THEN ROUND(
                        (IFNULL(
                            SUM(vh.amount),
                            0
                        ) / c.credit_limit)*100,
                        2
                    ) ELSE 0 END AS 'percent_consu'
                FROM
                    customer AS c
                    INNER JOIN document_type AS dt ON dt.idDocumentType = c.documenttype_id
                    LEFT JOIN voucher_header AS vh ON vh.customer_id = c.idCustomer
                    AND vh.sale_type = 'Credito'
                    AND vh.`status` = 'Pendiente'
                WHERE
                    c.idCustomer = ?
                    AND c.business_id = ?;
        SQL;
        $this->idCustomer = $idCustomer;
        $this->idBusiness = $idBusiness;
        $arrValues = [$this->idCustomer, $this->idBusiness];
        return $this->select($sql, $arrValues) ?? [
            "idCustomer" => 0,
            "fullname" => "",
            "documentType" => "",
            "document_number" => "",
            "phone_number" => "",
            "email" => "",
            "direction" => "",
            "credit_limit" => 0,
            "default_interest_rate" => 0,
            "current_interest_rate" => 0,
            "billing_date" => "",
            "day_billing" => 0,
            "status" => "",
            "amount_total" => 0,
            "amount_disp" => 0,
            "percent_consu" => 0
        ];
    }
    /**
     * Aqui se detallan los creditos del cliente
     * @param int $idCustomer
     * @param int $idBusiness
     * @return void
     */
    public function getCreditsCustomer(int $idCustomer, int $idBusiness, string $startDate, string $endDate, string $saleType, string $paymentStatus)
    {
        $sqlCredits = <<<SQL
                SELECT
                    vh.idVoucherHeader,
                    DATE(vh.date_time) AS 'date',
                    CASE WHEN vh.voucher_name != "" THEN vh.voucher_name ELSE CONCAT(
                        'Venta del día ',
                        DATE(vh.date_time)
                    ) END AS 'voucher_name',
                    pm.`name` AS 'payment_method',
                    vh.amount,
                    vh.`status` AS 'payment_status',
                    vh.sale_type,
                    DATE(vh.payment_deadline) AS 'payment_deadline',
                    c.default_interest_rate,
                    c.current_interest_rate,
                    #obtenemos los montos de los intereses
                    ROUND(
                        (c.default_interest_rate / 100)* vh.amount,
                        2
                    ) AS 'amount_default_interest_rate',
                    ROUND(
                        (c.current_interest_rate / 100)* vh.amount,
                        2
                    ) AS 'amount_current_interest_rate'
                FROM
                    voucher_header AS vh
                    INNER JOIN payment_method AS pm ON pm.idPaymentMethod = vh.payment_method_id
                    INNER JOIN customer AS c ON c.idCustomer = vh.customer_id
                WHERE
                    vh.customer_id = ?
                    AND 
                    vh.business_id= ?                
        SQL;
        $this->idCustomer = $idCustomer;
        $this->idBusiness = $idBusiness;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->saleType = $saleType;
        $this->paymentStatus = $paymentStatus;
        $arrValues = [$this->idCustomer, $this->idBusiness];
        if ($this->saleType != 'All') {
            $sqlCredits .= "AND vh.sale_type = ?";
            array_push($arrValues, $this->saleType);
        }
        if ($this->paymentStatus != 'All') {
            $sqlCredits .= "AND vh.`status` = ?";
            array_push($arrValues, $this->paymentStatus);
        }
        if ($this->startDate != null && $this->startDate != '' && $this->endDate != null && $this->endDate != '') {
            $sqlCredits .= "AND DATE(vh.date_time) BETWEEN ? AND ?";
            array_push($arrValues, $this->startDate, $this->endDate);
        }
        $sqlCredits .= <<<SQL
                    ORDER BY
                        CASE 
                            WHEN vh.`status`='Pendiente' THEN 0 ELSE 1
                        END ASC,
                        vh.date_time DESC;
        SQL;
        $dataCredits = $this->select_all($sqlCredits, $arrValues) ?? [
            "idVoucherHeader" => 0,
            "date_time" => "",
            "voucher_name" => "",
            "payment_method" => "",
            "amount" => 0,
            "payment_status" => "",
            "sale_type" => "",
            "payment_deadline" => "",
            "default_interest_rate" => 0,
            "current_interest_rate" => 0,
            "amount_default_interest_rate" => 0,
            "amount_current_interest_rate" => 0
        ];
        foreach ($dataCredits as $key => $value) {
            //Validamos si el credito tiene fecha de vencimiento y que su estado sea pendiente
            if (!empty($value['payment_deadline']) && $value['payment_status'] == 'Pendiente') {
                //sumamos el monto del interes financiado
                $dataCredits[$key]['amount'] = (float) $value['amount'] + (float) $value['amount_current_interest_rate'];
                //verificamos si el credito esta vencido
                $dataDate = dateDifference(date('Y-m-d'), $value['payment_deadline']);
                if ($dataDate['total_dias'] < 0) {
                    $dataCredits[$key]['date_status'] = 'Vencido ' . abs($dataDate['total_dias']) . ' dias';
                    //meses vencidos, converitmos a positivo
                    $month_overdue = abs($dataDate['meses']);
                    if ($month_overdue > 0) {
                        //multiplicamos la cantidad de meses vencidos para obtener el valor de cuanto a crecido el interes por mora
                        $amount_overdue = (float) $month_overdue * (float) $value['amount_default_interest_rate'];
                        $dataCredits[$key]['amount'] = (float) $dataCredits[$key]['amount'] + (float) $amount_overdue;
                    }
                } else if ($dataDate['total_dias'] >= 0 && $dataDate["total_dias"] < 5) {
                    $dataCredits[$key]['date_status'] = 'Proximo a vencer ' . $dataDate['total_dias'] . ' dias';
                } else {
                    $dataCredits[$key]['date_status'] = 'Vigente ' . $dataDate['total_dias'] . ' dias';
                }
                $dataCredits[$key]['days_overdue'] = $dataDate['total_dias'];
            }
        }
        return $dataCredits;
    }
    /**
     * Metodo que se encarga de obtener los kpis del cliente
     * Trae la sumatoria del total comprado,
     * Total pagado y
     * deuda pendiente
     * @param int $idCustomer
     * @param int $idBusiness
     * @return void
     */
    public function getKPISCustomer(int $idCustomer, int $idBusiness, string $startDate, string $endDate, string $saleType, string $paymentStatus)
    {
        $this->idCustomer = $idCustomer;
        $this->idBusiness = $idBusiness;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->saleType = $saleType;
        $this->paymentStatus = $paymentStatus;
        $sql = <<<SQL
                    SELECT
                        vh.amount,
                        vh.`status`,
                        c.current_interest_rate,
                        c.default_interest_rate,
                        c.billing_date
                    FROM
                        voucher_header AS vh
                        INNER JOIN customer AS c ON c.idCustomer=vh.customer_id
                    WHERE
                        vh.customer_id = ?
                    AND vh.business_id = ?               
        SQL;
        $arrValues = [$this->idCustomer, $this->idBusiness];
        if ($this->startDate != null && $this->startDate != '' && $this->endDate != null && $this->endDate != '') {
            $sql .= "AND DATE(vh.date_time) BETWEEN ? AND ?";
            array_push($arrValues, $this->startDate, $this->endDate);
        }
        // $sql .= "GROUP BY vh.customer_id";
        $rstData = $this->select_all($sql, $arrValues);
        //variables para almacenar los valores
        $total_ventas = 0.00;
        $total_pagado = 0.00;
        $total_pendiente = 0.00;
        $total_anulado = 0.00;
        foreach ($rstData as $key => $value) {
            if ($value['status'] == 'Pagado') {
                $total_pagado += (float) $value['amount'];
            } else if ($value['status'] == 'Pendiente') {
                //sumamos el monto del interes financiado
                $interestFinancing = (float) $value['amount'] * (float) $value['current_interest_rate'] / 100;
                $total_pendiente += round((float) $value['amount'] + (float) $interestFinancing, 2);
            } else {
                $total_anulado += (float) $value['amount'];
            }
        }
        //sumamos el total de ventas
        $total_ventas = round((float) $total_pagado + (float) $total_pendiente + (float) $total_anulado, 2);
        return [
            'total_ventas' => $total_ventas,
            'total_pagado' => $total_pagado,
            'total_pendiente' => $total_pendiente,
            'total_anulado' => $total_anulado
        ];
    }
}
