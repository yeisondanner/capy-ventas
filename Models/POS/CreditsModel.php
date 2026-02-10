<?php
class CreditsModel extends Mysql
{
    private int $idBusiness;
    private string $search;
    private string $startDate;
    private string $endDate;
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
}
