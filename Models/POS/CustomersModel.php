<?php

class CustomersModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupera los clientes asociados a un negocio.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectCustomers(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                c.idCustomer,
                c.documenttype_id,
                dt.name AS document_type,
                c.document_number,
                c.fullname,
                c.phone_number,
                c.email,
                c.direction,
                c.credit_limit,
                c.default_interest_rate,
                c.current_interest_rate,
                c.billing_date,
                c.status
            FROM customer AS c
            INNER JOIN document_type AS dt ON dt.idDocumentType = c.documenttype_id
            WHERE c.business_id = ?
              AND c.status = 'Activo'
            ORDER BY c.idCustomer DESC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Busca un cliente por su identificador y negocio.
     *
     * @param int $customerId Identificador del cliente.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function findCustomer(int $customerId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idCustomer,
                documenttype_id,
                document_number,
                fullname,
                phone_number,
                email,
                direction,
                status
            FROM customer
            WHERE idCustomer = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$customerId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un nuevo cliente vinculado a un negocio.
     *
     * @param int   $businessId Identificador del negocio activo.
     * @param array $data       Datos del cliente.
     *
     * @return int
     */
    public function insertCustomer(int $businessId, array $data): int
    {
        $sql = <<<SQL
            INSERT INTO customer
                (documenttype_id, document_number, fullname, phone_number, email, direction, business_id, status)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, 'Activo');
        SQL;

        $params = [
            $data['document_type_id'],
            $data['document'],
            $data['name'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['email'] !== '' ? $data['email'] : null,
            $data['address'] !== '' ? $data['address'] : null,
            $businessId,
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza la información de un cliente.
     *
     * @param int   $customerId Identificador del cliente.
     * @param int   $businessId Identificador del negocio activo.
     * @param array $data       Datos actualizados.
     *
     * @return bool
     */
    public function updateCustomer(int $customerId, int $businessId, array $data): bool
    {
        $sql = <<<SQL
            UPDATE customer
            SET
                documenttype_id = ?,
                document_number = ?,
                fullname        = ?,
                phone_number    = ?,
                email           = ?,
                direction       = ?,
                credit_limit    = ?,
                default_interest_rate = ?,
                current_interest_rate = ?,
                billing_date    = ?
            WHERE idCustomer = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['document_type_id'],
            $data['document'],
            $data['name'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['email'] !== '' ? $data['email'] : null,
            $data['address'] !== '' ? $data['address'] : null,
            $data['credit_limit'],
            $data['default_interest_rate'],
            $data['current_interest_rate'],
            $data['billing_date'] !== '' ? $data['billing_date'] : null,
            $customerId,
            $businessId,
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Elimina definitivamente un cliente.
     *
     * @param int $customerId Identificador del cliente.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return bool
     */
    public function deleteCustomer(int $customerId, int $businessId): bool
    {
        $sql = 'DELETE FROM customer WHERE idCustomer = ? AND business_id = ? LIMIT 1;';

        return (bool) $this->delete($sql, [$customerId, $businessId]);
    }

    /**
     * Busca un cliente por documento dentro del negocio indicado.
     *
     * @param int $businessId     Identificador del negocio activo.
     * @param int $documentTypeId Identificador del tipo de documento.
     * @param string $document    Número de documento.
     * @param int $excludeId      Identificador a excluir en la búsqueda.
     *
     * @return array
     */
    public function selectCustomerByDocument(int $businessId, int $documentTypeId, string $document, int $excludeId = 0): array
    {
        $sql = <<<SQL
            SELECT idCustomer
            FROM customer
            WHERE business_id = ?
              AND documenttype_id = ?
              AND document_number = ?
        SQL;

        $params = [$businessId, $documentTypeId, $document];

        if ($excludeId > 0) {
            $sql .= ' AND idCustomer != ?';
            $params[] = $excludeId;
        }

        $sql .= ' LIMIT 1;';

        $result = $this->select($sql, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * Obtiene los tipos de documento activos disponibles.
     *
     * @return array
     */
    public function selectDocumentTypes(): array
    {
        $sql = <<<SQL
            SELECT
                idDocumentType AS id,
                name
            FROM document_type
            WHERE status = 'Activo'
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql);
    }
    /**
     * Obtiene el detalle completo de un cliente y su negocio asociado.
     *
     * @param int $customerId Identificador del cliente.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function getCustomerDetail(int $customerId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                c.idCustomer,
                c.fullname,
                dt.name AS document_type,
                c.document_number,
                c.phone_number,
                c.email,
                c.direction,
                c.status,
                b.name AS name_bussines,
                b.direction AS direction_bussines,
                b.document_number AS document_bussines,
                b.logo,
                c.credit_limit,
                c.default_interest_rate,
                c.current_interest_rate,
                c.billing_date
            FROM customer c
            INNER JOIN document_type dt ON dt.idDocumentType = c.documenttype_id
            INNER JOIN business b ON b.idBusiness = c.business_id
            WHERE c.idCustomer = ?
              AND c.business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$customerId, $businessId]);

        return is_array($result) ? $result : [];
    }
}
