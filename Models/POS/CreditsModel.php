<?php
class CreditsModel extends Mysql
{
    private int $idBusiness;
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
    public function getAllCredits(int $idBusiness)
    {
        $sql = <<<SQL
                SELECT
                    vh.idVoucherHeader,
                    c.idCustomer,
                    c.fullname,
                    dt.`name` AS 'document_type',
                    c.document_number,
                    c.phone_number,
                    c.email,
                    c.direction,
                    c.credit_limit,
                    c.default_interest_rate,
                    c.current_interest_rate,
                    c.billing_date
                FROM
                    customer AS c
                    INNER JOIN voucher_header AS vh ON vh.customer_id = c.idCustomer
                    INNER JOIN document_type AS dt ON dt.idDocumentType = c.documenttype_id
                WHERE
                    vh.sale_type = 'Credito'
                    AND c.business_id = ?
                    AND vh.business_id = ?
                ORDER BY
                    vh.registration_date DESC
                GROUP BY
                    c.idCustomer;
        SQL;
        $this->idBusiness = $idBusiness;
        return $this->select_all($sql, [$this->idBusiness, $this->idBusiness]);
    }

}
