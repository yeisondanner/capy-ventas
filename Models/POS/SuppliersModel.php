<?php

class SuppliersModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupera los proveedores activos asociados a un negocio.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectSuppliers(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idSupplier,
                document_number,
                company_name,
                phone_number,
                email,
                direction,
                status
            FROM supplier
            WHERE business_id = ?
              AND status = 'Activo'
            ORDER BY idSupplier DESC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Busca un proveedor por su identificador y negocio.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function findSupplier(int $supplierId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idSupplier,
                document_number,
                company_name,
                phone_number,
                email,
                direction,
                status
            FROM supplier
            WHERE idSupplier = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$supplierId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un nuevo proveedor vinculado a un negocio.
     *
     * @param int   $businessId Identificador del negocio activo.
     * @param array $data       Datos del proveedor.
     *
     * @return int
     */
    public function insertSupplier(int $businessId, array $data): int
    {
        $sql = <<<SQL
            INSERT INTO supplier
                (document_number, company_name, phone_number, direction, email, business_id, status)
            VALUES
                (?, ?, ?, ?, ?, ?, 'Activo');
        SQL;

        $params = [
            $data['document'] !== '' ? $data['document'] : null,
            $data['name'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['address'] !== '' ? $data['address'] : null,
            $data['email'] !== '' ? $data['email'] : null,
            $businessId,
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza la información de un proveedor.
     *
     * @param int   $supplierId Identificador del proveedor.
     * @param int   $businessId Identificador del negocio activo.
     * @param array $data       Datos actualizados.
     *
     * @return bool
     */
    public function updateSupplier(int $supplierId, int $businessId, array $data): bool
    {
        $sql = <<<SQL
            UPDATE supplier
            SET
                document_number = ?,
                company_name    = ?,
                phone_number    = ?,
                direction       = ?,
                email           = ?
            WHERE idSupplier = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['document'] !== '' ? $data['document'] : null,
            $data['name'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['address'] !== '' ? $data['address'] : null,
            $data['email'] !== '' ? $data['email'] : null,
            $supplierId,
            $businessId,
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Elimina definitivamente un proveedor sin asociaciones.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return bool
     */
    public function deleteSupplier(int $supplierId, int $businessId): bool
    {
        $sql = 'DELETE FROM supplier WHERE idSupplier = ? AND business_id = ? LIMIT 1;';

        return (bool) $this->delete($sql, [$supplierId, $businessId]);
    }

    /**
     * Desactiva un proveedor que mantiene asociaciones con productos.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return bool
     */
    public function deactivateSupplier(int $supplierId, int $businessId): bool
    {
        $sql = <<<SQL
            UPDATE supplier
            SET status = 'Inactivo'
            WHERE idSupplier = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$supplierId, $businessId]);
    }

    /**
     * Busca un proveedor por nombre dentro del negocio indicado.
     *
     * @param int    $businessId Identificador del negocio activo.
     * @param string $name       Nombre del proveedor.
     * @param int    $excludeId  Identificador a excluir en la búsqueda.
     *
     * @return array
     */
    public function selectSupplierByName(int $businessId, string $name, int $excludeId = 0): array
    {
        $sql = <<<SQL
            SELECT idSupplier
            FROM supplier
            WHERE business_id = ?
              AND company_name = ?
        SQL;

        $params = [$businessId, $name];

        if ($excludeId > 0) {
            $sql .= ' AND idSupplier != ?';
            $params[] = $excludeId;
        }

        $sql .= ' LIMIT 1;';

        $result = $this->select($sql, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * Cuenta los productos asociados a un proveedor específico.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return int
     */
    public function countProductsBySupplier(int $supplierId, int $businessId): int
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS total
            FROM product AS p
            INNER JOIN supplier AS s ON s.idSupplier = p.supplier_id
            WHERE p.supplier_id = ?
              AND s.business_id = ?;
        SQL;

        $result = $this->select($sql, [$supplierId, $businessId]);

        return isset($result['total']) ? (int) $result['total'] : 0;
    }
    /**
     * Obtiene el detalle completo de un proveedor y su negocio asociado.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function getSupplierDetail(int $supplierId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                s.idSupplier,
                s.company_name,
                s.document_number,
                s.phone_number,
                s.email,
                s.direction,
                s.status,
                b.name AS name_bussines,
                b.direction AS direction_bussines,
                b.document_number AS document_bussines,
                b.logo
            FROM supplier s
            INNER JOIN business b ON b.idBusiness = s.business_id
            WHERE s.idSupplier = ?
              AND s.business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$supplierId, $businessId]);

        return is_array($result) ? $result : [];
    }
}
