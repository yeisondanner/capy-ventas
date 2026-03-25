<?php
class InventoryModel extends Mysql
{
    protected int $idProduct;
    protected string $name;
    protected float $stock;
    protected float $purchase_price;
    protected float $sales_price;
    protected int $measurement_id;
    protected string $description;
    protected string $status;
    protected int $supplier_id;
    protected string $is_public;
    protected string $code;
    protected string $expiration_date;
    protected int $userapp_id;
    protected int $category_id;
    protected string $bar_code_format;
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
                IFNULL(p.bar_code, 'Sin código') AS bar_code,
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
                p.is_public,
                IFNULL(DATE(p.expiration_date),'-') as 'expiration_date'
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
                LEFT JOIN product_file AS pf ON pf.product_id = p.idProduct AND pf.`status`='Activo'
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
     * Recupera todo el historial del producto echo a lo largo del tiempo
     *
     * @param int $productId  Identificador del producto.
     * @return array
     */
    public function selectProductHistory(int $productId): array
    {
        $sql = <<<SQL
            SELECT
                ph.idProductHistory,
                ph.bar_code_format,
                ph.bar_code,
                CONCAT(p.`names`, ' ', p.lastname) AS 'fullname_user',
                ph.`name` AS 'name_product',
                IFNULL(DATE(ph.expiration_date),'-') AS 'expiration_date_product',
                ph.stock AS 'stock_product',
                ph.sales_price AS 'sales_price_product',
                ph.purchase_price AS 'purchase_price_product',
                ph.registration_date AS 'registration_date_product',
                m.`name` AS 'measurement',
                c.`name` AS 'category'
            FROM
                product_history AS ph
                INNER JOIN user_app AS ua ON ua.idUserApp = ph.userapp_id
                INNER JOIN people AS p ON p.idPeople = ua.people_id
                INNER JOIN measurement AS m ON m.idMeasurement = ph.measurement_id
                INNER JOIN category AS c ON c.idCategory=ph.category_id
            WHERE 
                ph.product_id=?
                AND 
                ph.`status`='Activo';
        SQL;

        $data = $this->select_all($sql, [$productId]);
        foreach ($data as $key => $value) {
            $data[$key]['stock_product_text'] = $value['stock_product'] . ' ' . $value['measurement'];
            $data[$key]['purchase_price_text'] = getCurrency() . ' ' . $value['purchase_price_product'];
            $data[$key]['sales_price_text'] = getCurrency() . ' ' . $value['sales_price_product'];
        }
        return $data;
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
                (category_id, name, stock, purchase_price, sales_price, measurement_id, description, status, supplier_id,is_public,bar_code,expiration_date,bar_code_format)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?);
        SQL;
        $this->category_id = $data['category_id'];
        $this->name = $data['name'];
        $this->stock = $data['stock'];
        $this->purchase_price = $data['purchase_price'];
        $this->sales_price = $data['sales_price'];
        $this->measurement_id = $data['measurement_id'];
        $this->description = $data['description'];
        $this->status = $data['status'];
        $this->supplier_id = $data['supplier_id'];
        $this->is_public = $data['is_public'];
        $this->code = $data['code'];
        $this->expiration_date = $data['expiration_date'];
        $this->bar_code_format = $data['barcode_format'];

        $params = [
            $this->category_id,
            $this->name,
            $this->stock,
            $this->purchase_price,
            $this->sales_price,
            $this->measurement_id,
            $this->description !== '' ? $this->description : null,
            $this->status,
            $this->supplier_id,
            $this->is_public,
            $this->code,
            $this->expiration_date !== '' ? $this->expiration_date : null,
            $this->bar_code_format

        ];

        return (int) $this->insert($sql, $params);
    }
    /**
     * Metodo que se encarga de registra el historial del producto
     */
    public function insertProductHistory(array $data)
    {
        $sql = <<<SQL
            INSERT INTO `product_history` 
            (
            `product_id`, 
            `category_id`, 
            `bar_code`, 
            `name`, 
            `stock`, 
            `purchase_price`, 
            `sales_price`, 
            `measurement_id`, 
            `description`, 
            `status`,
            `expiration_date`, 
            `supplier_id`, 
            `is_public`, 
            `userapp_id`,
            `bar_code_format`
            ) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?);

        SQL;
        $this->idProduct = $data['idProduct'];
        $this->category_id = $data['category_id'];
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->stock = $data['stock'];
        $this->purchase_price = $data['purchase_price'];
        $this->sales_price = $data['sales_price'];
        $this->measurement_id = $data['measurement_id'];
        $this->description = $data['description'];
        $this->status = $data['status'];
        $this->expiration_date = $data['expiration_date'];
        $this->supplier_id = $data['supplier_id'];
        $this->is_public = $data['is_public'];
        $this->userapp_id = $data['user_id'];
        $this->bar_code_format = $data['barcode_format'];
        $params = [
            $this->idProduct,
            $this->category_id,
            $this->code,
            $this->name,
            $this->stock,
            $this->purchase_price,
            $this->sales_price,
            $this->measurement_id,
            $this->description !== '' ? $this->description : null,
            $this->status,
            $this->expiration_date !== '' ? $this->expiration_date : null,
            $this->supplier_id,
            $this->is_public,
            $this->userapp_id,
            $this->bar_code_format
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
                is_public = ?,
                bar_code = ?,
                expiration_date = ?,
                bar_code_format = ?
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
            $data['code'],
            $data['expiration_date'] !== '' ? $data['expiration_date'] : null,
            $data['barcode_format'],
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
        $sql = 'DELETE FROM product WHERE idProduct = ?;';
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
            SELECT idCategory as 'id', name as 'name'
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
            SELECT
                m.idMeasurement AS 'id',
                CONCAT(m.`name`, ' (', m.`description`, ')') AS `name`,
                COUNT(p.measurement_id) AS 'popularity'
            FROM
                measurement AS m
                LEFT JOIN product AS p ON p.measurement_id= m.idMeasurement
            WHERE
                m.`status` = 'Activo'
            GROUP BY m.idMeasurement
            ORDER BY
                popularity  DESC;
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
            SELECT idSupplier as 'id', company_name as 'name'
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
              AND p.`status` = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$businessId, $name]);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca un producto por código dentro del negocio indicado.
     *
     * @param string $code       Código del producto.
     * @param int    $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectProductByCode(string $code, int $businessId): array
    {
        $sql = <<<SQL
            SELECT p.idProduct
            FROM product AS p
            INNER JOIN category AS c ON c.idCategory = p.category_id
            WHERE c.business_id = ?
              AND p.bar_code = ?
              AND p.`status` = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$businessId, $code]);

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
        //recorremos para colocar la url de la imagen
        $result = $this->select_all($sql, [$idproduct]);
        foreach ($result as $key => $value) {
            $result[$key]['url'] = base_url() . '/Loadfile/iconproducts?f=' . $value['name'];
        }
        return $result;
    }
    /**
     * Metodo que se encarga de obtener las imagenes asociadas al producto
     * @param int $id
     * @return array
     */
    public function selectProductFiles(int $id)
    {
        $this->idProduct = $id;
        $sql = <<<SQL
                SELECT*FROM product_file AS pf WHERE pf.product_id=?;
        SQL;
        $result = $this->select_all($sql, [$this->idProduct]);
        return $result;
    }
}
