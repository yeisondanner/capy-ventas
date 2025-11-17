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
                        p.purchase_price AS 'purchase_price',
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

    /**
     * Obtiene la información del negocio activo.
     *
     * @param int $idBusiness Identificador del negocio.
     *
     * @return array|null
     */
    public function selectBusinessById(int $idBusiness): ?array
    {
        $sql = <<<SQL
            SELECT
                name,
                document_number,
                direction
            FROM business
            WHERE idBusiness = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        return $this->select($sql, [$idBusiness]);
    }

    /**
     * Recupera la información de un cliente específico.
     *
     * @param int $idCustomer Identificador del cliente.
     * @param int $idBusiness Identificador del negocio asociado.
     *
     * @return array|null
     */
    public function selectCustomerById(int $idCustomer, int $idBusiness): ?array
    {
        $sql = <<<SQL
            SELECT
                fullname,
                direction
            FROM customer
            WHERE idCustomer = ?
              AND business_id = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        return $this->select($sql, [$idCustomer, $idBusiness]);
    }

    /**
     * Obtiene la información del método de pago solicitado.
     *
     * @param int $paymentMethodId Identificador del método de pago.
     *
     * @return array|null
     */
    public function selectPaymentMethod(int $paymentMethodId): ?array
    {
        $sql = <<<SQL
            SELECT
                idPaymentMethod,
                name
            FROM payment_method
            WHERE idPaymentMethod = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        return $this->select($sql, [$paymentMethodId]);
    }

    /**
     * Recupera la información de los productos necesarios para el voucher.
     *
     * @param array $productIds  Listado de productos.
     * @param int   $idBusiness  Identificador del negocio activo.
     *
     * @return array
     */
    public function selectProductsForVoucher(array $productIds, int $idBusiness): array
    {
        if (count($productIds) === 0) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $params       = array_merge($productIds, [$idBusiness, $idBusiness]);

        $sql = <<<SQL
            SELECT
                p.idProduct     AS idproduct,
                p.name          AS product,
                p.purchase_price AS purchase_price,
                p.sales_price   AS price,
                p.stock         AS stock,
                m.name          AS measurement,
                c.name          AS category
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            WHERE p.idProduct IN ($placeholders)
              AND c.business_id = ?
              AND s.business_id = ?;
        SQL;

        return $this->select_all($sql, $params);
    }

    /**
     * Inserta la cabecera del comprobante de venta.
     *
     * @param array $data Datos normalizados de la cabecera.
     *
     * @return int
     */
    public function insertVoucherHeader(array $data): int
    {
        $sql = <<<SQL
            INSERT INTO voucher_header (
                name_customer,
                direction_customer,
                name_bussines,
                document_bussines,
                direction_bussines,
                date_time,
                amount,
                percentage_discount,
                fixed_discount,
                how_much_do_i_pay,
                voucher_name,
                payment_method_id,
                business_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;

        $values = [
            $data['name_customer'] ?? '',
            $data['direction_customer'] ?? '',
            $data['name_bussines'] ?? '',
            $data['document_bussines'] ?? '',
            $data['direction_bussines'] ?? '',
            $data['date_time'] ?? '',
            $data['amount'] ?? 0,
            $data['percentage_discount'] ?? 0,
            $data['fixed_discount'] ?? 0,
            $data['how_much_do_i_pay'] ?? 0,
            $data['voucher_name'] ?? null,
            $data['payment_method_id'] ?? 0,
            $data['business_id'] ?? 0,
        ];

        return (int) $this->insert($sql, $values);
    }

    /**
     * Inserta un detalle del comprobante de venta.
     *
     * @param array $data Datos normalizados del detalle.
     *
     * @return int
     */
    public function insertVoucherDetail(array $data): int
    {
        $sql = <<<SQL
            INSERT INTO voucher_detail (
                product_id,
                voucherheader_id,
                name_product,
                unit_of_measurement,
                name_category,
                sales_price_product,
                purchase_price_product,
                stock_product,
                subtotal
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;

        $values = [
            $data['product_id'] ?? 0,
            $data['voucherheader_id'] ?? 0,
            $data['name_product'] ?? '',
            $data['unit_of_measurement'] ?? '',
            $data['name_category'] ?? '',
            $data['sales_price_product'] ?? 0,
            $data['purchase_price_product'] ?? 0,
            $data['stock_product'] ?? 0,
            $data['subtotal'] ?? 0,
        ];

        return (int) $this->insert($sql, $values);
    }
}
