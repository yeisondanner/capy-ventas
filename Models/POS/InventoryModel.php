<?php
class InventoryModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupera los productos pertenecientes al negocio indicado.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectProducts(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                p.idProduct,
                p.category_id,
                p.name,
                p.stock,
                p.purchase_price,
                p.sales_price,
                p.measurement_id,
                p.description,
                p.status,
                p.supplier_id,
                c.name AS category,
                m.name AS measurement,
                s.company_name AS supplier
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            WHERE c.business_id = ?
              AND s.business_id = ?
            ORDER BY p.idProduct DESC;
        SQL;

        return $this->select_all($sql, [$businessId, $businessId]);
    }

    /**
     * Recupera un producto específico validando que pertenezca al negocio recibido.
     *
     * @param int $productId  Identificador del producto.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectProduct(int $productId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                p.*
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            WHERE p.idProduct = ?
              AND c.business_id = ?
              AND s.business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$productId, $businessId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un nuevo producto en la base de datos.
     *
     * @param array $data Datos del producto a registrar.
     *
     * @return int
     */
    public function insertProduct(array $data): int
    {
        $sql = <<<SQL
            INSERT INTO product
                (category_id, name, stock, purchase_price, sales_price, measurement_id, description, status, supplier_id)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;

        $params = [
            $data['category_id'],
            $data['name'],
            $data['stock'],
            $data['purchase_price'],
            $data['sales_price'],
            $data['measurement_id'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['status'],
            $data['supplier_id'],
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza la información de un producto existente.
     *
     * @param array $data Datos del producto a actualizar.
     *
     * @return bool
     */
    public function updateProduct(array $data): bool
    {
        $sql = <<<SQL
            UPDATE product
            SET
                category_id = ?,
                name = ?,
                stock = ?,
                purchase_price = ?,
                sales_price = ?,
                measurement_id = ?,
                description = ?,
                status = ?,
                supplier_id = ?
            WHERE idProduct = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['category_id'],
            $data['name'],
            $data['stock'],
            $data['purchase_price'],
            $data['sales_price'],
            $data['measurement_id'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['status'],
            $data['supplier_id'],
            $data['idProduct'],
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Elimina un producto por su identificador.
     *
     * @param int $productId Identificador del producto.
     *
     * @return bool
     */
    public function deleteProduct(int $productId): bool
    {
        $sql = 'DELETE FROM product WHERE idProduct = ? LIMIT 1;';
        return (bool) $this->delete($sql, [$productId]);
    }

    /**
     * Obtiene las categorías activas asociadas a un negocio.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectCategories(int $businessId): array
    {
        $sql = <<<SQL
            SELECT idCategory, name
            FROM category
            WHERE business_id = ?
              AND status = 'Activo'
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Obtiene las unidades de medida activas disponibles.
     *
     * @return array
     */
    public function selectMeasurements(): array
    {
        $sql = <<<SQL
            SELECT idMeasurement, name
            FROM measurement
            WHERE status = 'Activo'
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql);
    }

    /**
     * Valida si una categoría pertenece al negocio activo.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectCategory(int $categoryId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT idCategory
            FROM category
            WHERE idCategory = ?
              AND business_id = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$categoryId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Valida si una unidad de medida existe y está activa.
     *
     * @param int $measurementId Identificador de la unidad de medida.
     *
     * @return array
     */
    public function selectMeasurement(int $measurementId): array
    {
        $sql = <<<SQL
            SELECT idMeasurement
            FROM measurement
            WHERE idMeasurement = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$measurementId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Obtiene los proveedores activos asociados a un negocio.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectSuppliers(int $businessId): array
    {
        $sql = <<<SQL
            SELECT idSupplier, company_name
            FROM supplier
            WHERE business_id = ?
              AND status = 'Activo'
            ORDER BY company_name ASC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Valida si un proveedor pertenece al negocio activo y está disponible.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectSupplier(int $supplierId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT idSupplier
            FROM supplier
            WHERE idSupplier = ?
              AND business_id = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$supplierId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca un producto por nombre dentro del negocio indicado.
     *
     * @param string $name       Nombre del producto.
     * @param int    $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectProductByName(string $name, int $businessId): array
    {
        $sql = <<<SQL
            SELECT p.idProduct
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            WHERE c.business_id = ?
              AND p.name = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$businessId, $name]);

        return is_array($result) ? $result : [];
    }
}
