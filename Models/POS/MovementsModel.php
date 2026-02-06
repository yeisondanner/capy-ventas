<?php
class MovementsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Metodo que se encarga de obtener las ventas y gastos dependiendo del tipo de movimiento
     * @param int $businessId
     * @param string $minDate
     * @param string $maxDate
     * @param string $searchConcept
     * @param string $type_movements
     * @return array
     */
    public function select_movements(int $businessId, $minDate = null, $maxDate = null, $searchConcept = null, $type_movements = 'income'): array
    {
        //verificamos que el tipo de movimiento sea income o expense
        if ($type_movements !== 'income' && $type_movements !== 'expense') {
            return [];
        }
        if ($type_movements === 'income') {
            /**
             * Seccion de obtener las ventas de acuerdo a los filtros proporcionados
             */
            $sql = <<<SQL
                SELECT
                    vh.idVoucherHeader AS 'id',
                    CASE WHEN vh.voucher_name IS NULL THEN CONCAT(
                        'Venta del dia ',
                        DATE(vh.date_time)
                    ) ELSE vh.voucher_name END AS 'name',
                    vh.amount AS 'amount',
                    pm.name AS 'method_payment',
                    vh.date_time AS 'date_time',
                    CONCAT(p.`names`, ' ', p.lastname) AS 'fullname'
                FROM
                    voucher_header vh
                    INNER JOIN payment_method pm ON vh.payment_method_id = pm.idPaymentMethod
                    INNER JOIN user_app ua ON vh.user_app_id = ua.idUserApp
                    INNER JOIN people p ON ua.people_id = p.idPeople
                    WHERE 
                    vh.status='Pagado' AND
                    vh.business_id = ?
            SQL;

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
        } else if ($type_movements === 'expense') {
            /**
             * Seccion de obtener los gastos de acuerdo a los filtros proporcionados
             */
            $sql = <<<SQL
                SELECT
                    ee.idExpenseEconomic AS 'id',
                    CASE WHEN ee.name_expense IS NULL THEN CONCAT(
                        'Gastos del dia ',
                        DATE(ee.expense_date)
                    ) ELSE ee.name_expense END AS 'name',
                    ee.amount AS 'amount',
                    pm.`name` AS 'method_payment',
                    ee.expense_date AS 'date_time',
                    CONCAT(p.`names`, ' ', p.lastname) AS 'fullname'
                FROM
                    expense_economic AS ee
                    INNER JOIN payment_method AS pm ON pm.idPaymentMethod = ee.PaymentMethod_id
                    INNER JOIN user_app AS ua ON ua.idUserApp = ee.userapp_id
                    INNER JOIN people AS p ON p.idPeople = ua.people_id
                    WHERE 
                    ee.business_id=?
            SQL;
            $arrValues = [$businessId];
            if ($minDate != null && $maxDate != null) {
                $sql .= " AND DATE(ee.expense_date) BETWEEN ? AND ?";
                array_push($arrValues, $minDate, $maxDate);
            }
            // Agregar filtro por concepto si se proporciona
            if ($searchConcept != null && !empty($searchConcept)) {
                $sql .= " AND (ee.name_expense LIKE ? OR p.names LIKE ? OR p.lastname LIKE ?)";
                $searchParam = '%' . $searchConcept . '%';
                array_push($arrValues, $searchParam, $searchParam, $searchParam);
            }
            $sql .= " ORDER BY ee.expense_date DESC";
        }
        $arrMovements = $this->select_all($sql, $arrValues);
        //adicionamos un campo mas para identificar el tipo de movimiento
        array_walk($arrMovements, function (&$item) use ($type_movements) {
            $item['type'] = $type_movements;
        });
        return $arrMovements;
    }

    public function select_voucher(int $voucherId, int $businessId): array
    {
        $sqlHeader = <<<SQL
            SELECT
                    vh.name_bussines,
                    vh.direction_bussines,
                    vh.document_bussines,
                    vh.date_time,
                    vh.name_customer,
                    vh.direction_customer,
                    vh.amount,
                    vh.percentage_discount,
                    vh.voucher_name,
                    vh.tax_name,
                    vh.tax_percentage,
                    vh.tax_amount,
                    vh.sale_type,
                    b.logo,
                    vh.idVoucherHeader AS 'id',
                    CONCAT(p.`names`, ' ', p.lastname) AS fullname,
                    vh.status,
                    vh.sale_type
                FROM
                    voucher_header vh
                    INNER JOIN business AS b ON b.idBusiness = vh.business_id
                    INNER JOIN user_app ua ON vh.user_app_id = ua.idUserApp
                    INNER JOIN people p ON ua.people_id = p.idPeople
                WHERE
                    vh.idVoucherHeader = ?
                    AND vh.business_id = ?
                LIMIT 1;
        SQL;
        $sqlDetail = <<<SQL
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
                    b.logo,
                    CONCAT(p.`names`, ' ', p.lastname) AS fullname
                FROM
                    voucher_detail vd
                    INNER JOIN voucher_header vh ON vd.voucherheader_id = vh.idVoucherHeader
                    INNER JOIN business AS b ON b.idBusiness=vh.business_id
                    INNER JOIN user_app ua ON vh.user_app_id = ua.idUserApp
                    INNER JOIN people p ON ua.people_id = p.idPeople
                WHERE vh.idVoucherHeader = ?
                AND vh.business_id = ?
                ORDER BY vd.name_product ASC;
            SQL;

        $header = $this->select($sqlHeader, [$voucherId, $businessId]);
        $detail = $this->select_all($sqlDetail, [$voucherId, $businessId]);
        return [
            'header' => $header,
            'detail' => $detail
        ];
    }

    /**
     * Metodo que se encarga de obtener los totales de ventas y gastos
     * asi mismo se encarga de obtener el balance
     * @param int $businessId
     * @param string $minDate
     * @param string $maxDate
     * @param string $searchConcept
     * @return array
     */
    public function getTotals(int $businessId, $minDate = null, $maxDate = null, $searchConcept = null): array
    {
        $sqlTotalSales = <<<SQL
                SELECT
                    COALESCE(SUM(vh.amount), 0) AS total_sales
                FROM voucher_header vh
                WHERE vh.business_id = ? 
        SQL;
        $sqlTotalExpenses = <<<SQL
                SELECT
                    COALESCE(
                        SUM(ee.amount),
                        0
                    ) AS 'total_expense'
                FROM
                    expense_economic AS ee
                WHERE
                    ee.`status`='pagado' AND
                    ee.business_id=?   
        SQL;

        $arrValues = [$businessId];

        // Si se proporcionan fechas, añadir la condición de rango
        if ($minDate != null && $maxDate != null) {
            $sqlTotalSales .= " AND DATE(vh.date_time) BETWEEN ? AND ?";
            $sqlTotalExpenses .= " AND DATE(ee.expense_date) BETWEEN ? AND ?";
            array_push($arrValues, $minDate, $maxDate);
        }

        // Agregar filtro por concepto si se proporciona
        if ($searchConcept != null && !empty($searchConcept)) {
            $sqlTotalSales .= " AND (vh.voucher_name LIKE ? OR vh.name_customer LIKE ?)";
            $sqlTotalExpenses .= " AND (ee.name_expense LIKE ? OR ee.name_expense LIKE ?)";
            $searchParam = '%' . $searchConcept . '%';
            array_push($arrValues, $searchParam, $searchParam);
        }

        $sqlTotalSales .= " AND vh.amount > 0";
        $sqlTotalExpenses .= " AND ee.amount > 0";

        $resultTotalSale = $this->select($sqlTotalSales, $arrValues);
        $resultTotalExpense = $this->select($sqlTotalExpenses, $arrValues);

        $totals = [
            'total_sales' => (float) ($resultTotalSale['total_sales'] ?? 0),
            'total_expenses' => (float) ($resultTotalExpense['total_expense'] ?? 0),
        ];

        $totals['balance'] = $totals['total_sales'] - $totals['total_expenses'];

        return $totals;
    }

    public function select_expense(int $expenseId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                ee.idExpenseEconomic AS id,
                ee.name_expense,
                ee.description,
                ee.amount,
                ee.expense_date,
                ee.voucher_reference,
                ee.status,
                pm.name AS payment_method,
                ec.name AS category_name,
                s.company_name AS supplier_name,
                CONCAT(p.names, ' ', p.lastname) AS fullname,
                b.name AS name_bussines,
                b.direction AS direction_bussines,
                b.document_number AS document_bussines,
                b.logo
            FROM
                expense_economic ee
                INNER JOIN business b ON b.idBusiness = ee.business_id
                INNER JOIN payment_method pm ON pm.idPaymentMethod = ee.PaymentMethod_id
                INNER JOIN expense_category ec ON ec.idExpenseCategory = ee.expensecategory_id
                LEFT JOIN supplier s ON s.idSupplier = ee.supplier_id
                INNER JOIN user_app ua ON ua.idUserApp = ee.userapp_id
                INNER JOIN people p ON p.idPeople = ua.people_id
            WHERE
                ee.idExpenseEconomic = ?
                AND ee.business_id = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$expenseId, $businessId]);
    }
}
