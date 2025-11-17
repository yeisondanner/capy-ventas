<?php
class SalesModel extends Mysql
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
     * Metodo que se encarga de obtener todos los productos deel negocio
     * que esta iniciado sesion
     */
    public function selectProducts(int $idBusiness)
    {
        $this->idBusiness = $idBusiness;
        $sql = <<<SQL
                    SELECT
                        p.idProduct AS 'idproduct',
                        p.category_id AS 'idcategory',
                        p.measurement_id AS 'idmeasurement',
                        p.supplier_id AS 'idsupplier',
                        p.`name` AS 'product',
                        m.`name` AS 'measurement',
                        p.stock AS 'stock',
                        p.sales_price AS 'price',
                        c.`name` AS 'category',
                        s.company_name AS 'supplier'
                    FROM
                        product AS p
                        INNER JOIN category AS c ON c.idCategory = p.category_id
                        INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
                        INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
                    WHERE
                        c.business_id = ?
                        AND s.business_id = ?;
        SQL;
        $result = $this->select_all($sql, [$this->idBusiness, $this->idBusiness]);
        return $result;
    }

    /**
     * Obtiene los clientes asociados al negocio que ha iniciado sesión.
     *
     * @param int $idBusiness Identificador del negocio activo.
     *
     * @return array
     */
    public function selectCustomers(int $idBusiness): array
    {
        $sql = <<<SQL
            SELECT
                c.idCustomer AS idCustomer,
                c.fullname   AS fullname,
                c.document_number AS document_number,
                dt.name AS document_type
            FROM customer AS c
            INNER JOIN document_type AS dt ON dt.idDocumentType = c.documenttype_id
            WHERE c.business_id = ?
              AND c.status = 'Activo'
            ORDER BY c.fullname ASC;
        SQL;

        return $this->select_all($sql, [$idBusiness]);
    }
}
