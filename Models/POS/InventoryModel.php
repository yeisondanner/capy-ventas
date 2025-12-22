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
                s.company_name AS supplier,
                p.is_public
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            WHERE c.business_id = ?
              AND s.business_id = ?
              AND p.status = 'Activo'
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
                p.*,
                c.name AS category_name,
                m.name AS measurement_name,
                s.company_name AS supplier_name,
                pf.`name` as 'image_main'
            FROM
                product AS p
                INNER JOIN category AS c ON c.idCategory = p.category_id
                INNER JOIN measurement AS m ON m.idMeasurement = p.measurement_id
                INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
                LEFT JOIN product_file AS pf ON pf.product_id = p.idProduct
            WHERE
                p.idProduct = ?
                AND c.business_id = ?
                AND s.business_id = ?
            GROUP BY
                p.idProduct
            LIMIT
                1;
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
                (category_id, name, stock, purchase_price, sales_price, measurement_id, description, status, supplier_id,is_public)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
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
            $data['is_public']
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
                supplier_id = ?,
                is_public = ?
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
            $data['is_public'],
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
            ORDER BY idCategory DESC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Recupera todas las categorías asociadas a un negocio, sin filtrar por estado.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectCategoryList(int $businessId): array
    {
        $sql = <<<SQL
            SELECT idCategory, name, status
            FROM category
            WHERE business_id = ?
              AND status = 'Activo'
            ORDER BY idCategory DESC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Inserta una nueva categoría vinculada a un negocio.
     *
     * @param int    $businessId Identificador del negocio.
     * @param string $name       Nombre de la categoría.
     *
     * @return int
     */
    public function insertCategory(int $businessId, string $name): int
    {
        $sql = <<<SQL
            INSERT INTO category (business_id, name, status)
            VALUES (?, ?, 'Activo');
        SQL;

        return (int) $this->insert($sql, [$businessId, $name]);
    }

    /**
     * Actualiza los datos de una categoría existente.
     *
     * @param int    $categoryId Identificador de la categoría.
     * @param int    $businessId Identificador del negocio.
     * @param string $name       Nombre actualizado.
     *
     * @return bool
     */
    public function updateCategory(int $categoryId, int $businessId, string $name): bool
    {
        $sql = <<<SQL
            UPDATE category
            SET name = ?
            WHERE idCategory = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$name, $categoryId, $businessId]);
    }

    /**
     * Elimina una categoría asociada a un negocio.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio.
     *
     * @return bool
     */
    public function deleteCategory(int $categoryId, int $businessId): bool
    {
        $sql = 'DELETE FROM category WHERE idCategory = ? AND business_id = ? LIMIT 1;';

        return (bool) $this->delete($sql, [$categoryId, $businessId]);
    }

    /**
     * Desactiva una categoría asociada a un negocio.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio.
     *
     * @return bool
     */
    public function deactivateCategory(int $categoryId, int $businessId): bool
    {
        $sql = <<<SQL
            UPDATE category
            SET status = 'Inactivo'
            WHERE idCategory = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$categoryId, $businessId]);
    }

    /**
     * Obtiene las unidades de medida activas disponibles.
     *
     * @return array
     */
    public function selectMeasurements(): array
    {
        $sql = <<<SQL
            SELECT idMeasurement, concat(`name`, ' (', `description`, ')') AS `name`
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
     * Obtiene una categoría sin filtrar por estado para validar su pertenencia al negocio.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio.
     *
     * @return array
     */
    public function findCategory(int $categoryId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT idCategory, name, status
            FROM category
            WHERE idCategory = ?
              AND business_id = ?
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
     * Busca una categoría por su nombre dentro del negocio indicado.
     *
     * @param string $name       Nombre de la categoría.
     * @param int    $businessId Identificador del negocio.
     * @param int    $excludeId  Identificador a excluir (opcional).
     *
     * @return array
     */
    public function selectCategoryByName(string $name, int $businessId, int $excludeId = 0): array
    {
        $sql = <<<SQL
            SELECT idCategory
            FROM category
            WHERE business_id = ?
              AND name = ?
        SQL;

        $params = [$businessId, $name];

        if ($excludeId > 0) {
            $sql .= ' AND idCategory != ?';
            $params[] = $excludeId;
        }

        $sql .= ' LIMIT 1;';

        $result = $this->select($sql, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * Cuenta los productos asociados a una categoría específica.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio.
     *
     * @return int
     */
    public function countProductsByCategory(int $categoryId, int $businessId): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS total
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            WHERE p.category_id = ?
              AND c.business_id = ?;
        SQL;

        $result = $this->select($sql, [$categoryId, $businessId]);

        return isset($result['total']) ? (int) $result['total'] : 0;
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
    /**
     * Metodo que consulta si el producto
     * no esta relacionado a alguna venta
     * @param int $idproduct
     * @return array
     */
    public function selectSaleProduct(int $idproduct): array
    {
        $sql = <<<SQL
            SELECT*FROM voucher_detail AS vd WHERE vd.product_id=?;
        SQL;

        $result = $this->select($sql, [$idproduct]);

        return is_array($result) ? $result : [];
    }
    /**
     * Metodo que actualiza el estado de un producto
     * @param int $idproduct
     * @param string $status
     * @return bool
     */
    public function updateProductStatus(int $idproduct, string $status): bool
    {
        $sql = <<<SQL
            UPDATE product
            SET status = ?
            WHERE idProduct = ?;
        SQL;

        $result = $this->update($sql, [$status, $idproduct]);

        return $result;
    }
    /**
     * Metodo que se encarga insertar nueva foto
     * del producto
     * @param array $data
     * @return 
     */
    public function insert_product_file(array $data)
    {
        $sql = <<<SQL
            INSERT INTO 
            `product_file` 
            (`product_id`, `name`, `extension`, `size`) 
            VALUES 
            (?, ?, ?, ?);
        SQL;
        $insert = $this->insert($sql, [
            $data['product_id'],
            $data['name'],
            $data['extension'],
            $data['size']
        ]);
        return $insert;
    }
    /**
     * Metodo que se encarga de desactivar la imagen
     * @param int $id
     * @param string $status
     * @return void
     */
    public function update_status_product_file(int $id, string $status)
    {
        $sql = <<<SQL
            UPDATE `product_file` SET `status`=? WHERE  `idProduct_file`=?;
        SQL;
        $request = $this->update($sql, [$status, $id]);
        return $request;
    }
    /**
     * Metodo que se encarga de obtener 
     * todas las imagenes que estan activas
     * @param int $idproduct
     * @return array
     */
    public function selectProductFile(int $idproduct): array
    {
        $sql = <<<SQL
            SELECT*FROM product_file AS pf WHERE pf.product_id=? AND pf.`status`='Activo';
        SQL;

        $result = $this->select_all($sql, [$idproduct]);

        return $result;
    }
}
