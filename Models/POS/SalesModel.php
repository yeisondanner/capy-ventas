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
                        s.company_name AS 'supplier',
                        pf.`name` AS 'photo'
                    FROM
                        product AS p
                        INNER JOIN category AS c ON c.idCategory = p.category_id
                        INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
                        INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
                        LEFT JOIN product_file AS pf ON pf.product_id=p.idProduct
                    WHERE
                        c.business_id = ?
                        AND s.business_id = ?
                        AND p.status = 'Activo'
                    GROUP BY p.idProduct;
        SQL;
        $result = $this->select_all($sql, [$this->idBusiness, $this->idBusiness]);
        return $result;
    }

    /**
     * Obtiene las categorías con mayores ventas para el negocio activo.
     *
     * @param int $idBusiness Identificador del negocio activo.
     * @param int $limit      Cantidad de categorías a devolver.
     *
     * @return array
     */
    public function selectPopularCategories(int $idBusiness, int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $sql = <<<SQL
            SELECT
                c.idCategory AS idCategory,
                c.name       AS category,
                SUM(vd.stock_product) AS total_sold
            FROM voucher_detail AS vd
            INNER JOIN voucher_header AS vh ON vh.idVoucherHeader = vd.voucherheader_id
            INNER JOIN product AS p ON p.idProduct = vd.product_id
            INNER JOIN category AS c ON c.idCategory = p.category_id
            WHERE vh.business_id = ?
            GROUP BY c.idCategory, c.name
            ORDER BY total_sold DESC
            LIMIT $limit;
        SQL;

        return $this->select_all($sql, [$idBusiness]);
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
                business_id,
                user_app_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?);
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
            $data['user_app_id'] ?? 0,
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

    /**
     * Descuenta el stock del producto vendido, permitiendo valores negativos cuando no hay inventario.
     *
     * @param int   $productId   Identificador del producto vendido.
     * @param int   $idBusiness  Identificador del negocio activo.
     * @param float $quantity    Cantidad vendida a descontar.
     *
     * @return bool
     */
    public function decreaseProductStock(
        int $productId,
        int $idBusiness,
        float $quantity
    ): bool {
        $sql = <<<SQL
            UPDATE product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            SET p.stock = p.stock - ?
            WHERE p.idProduct = ?
              AND c.business_id = ?
              AND s.business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$quantity, $productId, $idBusiness, $idBusiness]);
    }

    /**
     * Recupera la cabecera de un comprobante para validar su pertenencia al negocio.
     *
     * @param int $voucherId  Identificador del comprobante.
     * @param int $idBusiness Identificador del negocio activo.
     *
     * @return array|null
     */
    public function selectVoucherById(int $voucherId, int $idBusiness): ?array
    {
        $sql = <<<SQL
            SELECT
                idVoucherHeader AS id,
                voucher_name
            FROM voucher_header
            WHERE idVoucherHeader = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return $this->select($sql, [$voucherId, $idBusiness]);
    }

    /**
     * Actualiza el nombre del comprobante generado.
     *
     * @param int    $voucherId  Identificador del comprobante.
     * @param string $voucherName Nombre a registrar.
     * @param int    $idBusiness Identificador del negocio activo.
     *
     * @return bool
     */
    public function updateVoucherName(
        int $voucherId,
        string $voucherName,
        int $idBusiness
    ): bool {
        $sql = <<<SQL
            UPDATE voucher_header
            SET voucher_name = ?
            WHERE idVoucherHeader = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$voucherName, $voucherId, $idBusiness]);
    }
    /**
     * Metodo que se encarga de obtener todos los metodos de pagos
     * @return void
     */
    public function selectPaymentMethods(): array
    {
        $sql = <<<SQL
            SELECT*FROM payment_method AS pm WHERE pm.`status`='Activo' ORDER BY pm.idPaymentMethod ASC;
        SQL;
        $result = $this->select_all($sql);
        return $result ?? [];
    }
    /**
     * Metodo que se encarga de obtener si hay alguna caja abierta
     * por el usuario que ha iniciado sesion
     * @param array $data
     * @return void
     */
    public function selectOpenBoxByUser(array $data)
    {
        $sql = <<<SQL
                SELECT
                    *
                FROM
                    box_sessions AS b
                WHERE
                    b.userapp_id = ?
                    AND b.`status` = ?
                    AND YEAR(b.opening_date)= ?
                    AND MONTH(b.opening_date)= ?;
        SQL;
        $result = $this->select($sql, [$data['user_app_id'], $data['status'], $data['year'], $data['month']]);
        return $result;
    }
    /**
     * MEtodo que se encarga de registra los movimiento de caja
     * @param array $data
     */
    public function insertBoxMovement(array $data)
    {
        $sql = <<<SQL
            INSERT INTO `box_movements` 
            (`boxSessions_id`, `type_movement`, `concept`, `amount`, `payment_method`, `reference_table`, `reference_id`) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?);
        SQL;
        $result = $this->insert($sql, [
            $data['boxSessions_id'],
            $data['type_movement'],
            $data['concept'],
            $data['amount'],
            $data['payment_method'],
            $data['reference_table'],
            $data['reference_id']
        ]);
        return $result;
    }
    /**
     * Metodo que se encarga de obtener la informacion del metodo del
     * pago mediante su Id
     * @param int $paymentMethodId
     * @return void
     */
    public function selectPaymentMethodById(int $paymentMethodId): ?array
    {
        $sql = <<<SQL
            SELECT*FROM payment_method AS pm WHERE pm.idPaymentMethod=?;
        SQL;
        return $this->select($sql, [$paymentMethodId]);
    }
}
