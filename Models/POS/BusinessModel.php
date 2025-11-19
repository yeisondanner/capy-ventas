<?php

class BusinessModel extends Mysql
{
    private int $userId;
    private int $businessId;

    /**
     * Obtiene todos los negocios asociados a un usuario específico.
     *
     * @param int $userId Identificador del usuario propietario.
     * @return array Lista de negocios.
     */
    public function selectBusinessesByUser(int $userId): array
    {
        $this->userId = $userId;
        $sql = <<<SQL
            SELECT
                b.idBusiness,
                b.`name` AS business,
                b.document_number,
                b.status,
                bt.`name` AS category
            FROM business AS b
            INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            WHERE b.userapp_id = ?
            ORDER BY b.registration_date DESC;
        SQL;

        $request = $this->select_all($sql, [$this->userId]);
        return $request ?? [];
    }

    /**
     * Busca un negocio por su identificador y usuario propietario.
     *
     * @param int $businessId Identificador del negocio.
     * @param int $userId     Identificador del propietario.
     * @return array|null Datos del negocio o null si no pertenece al usuario.
     */
    public function selectBusinessByIdForUser(int $businessId, int $userId): ?array
    {
        $this->businessId = $businessId;
        $this->userId     = $userId;

        $sql = <<<SQL
            SELECT
                b.idBusiness,
                b.`name` AS business,
                b.document_number,
                b.status,
                bt.`name` AS category
            FROM business AS b
            INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            WHERE b.idBusiness = ? AND b.userapp_id = ?
            LIMIT 1;
        SQL;

        $request = $this->select($sql, [$this->businessId, $this->userId]);
        return $request ?: null;
    }

    /**
     * Valida si existe un negocio con el mismo documento para el usuario.
     *
     * @param string   $documentNumber Número de documento del negocio.
     * @param int      $userId         Propietario.
     * @param int|null $excludeId      Identificador a excluir de la búsqueda.
     * @return array|null Registro encontrado o null si no existe.
     */
    public function findBusinessByDocument(string $documentNumber, int $userId, ?int $excludeId = null): ?array
    {
        $this->userId = $userId;

        $params = [$documentNumber, $this->userId];
        $excludeSql = '';

        if (!empty($excludeId)) {
            $excludeSql      = ' AND b.idBusiness != ?';
            $this->businessId = (int) $excludeId;
            $params[]         = $this->businessId;
        }

        $sql = <<<SQL
            SELECT b.idBusiness
            FROM business AS b
            WHERE b.document_number = ? AND b.userapp_id = ?{$excludeSql}
            LIMIT 1;
        SQL;

        $request = $this->select($sql, $params);
        return $request ?: null;
    }

    /**
     * Inserta un nuevo negocio asociado a un usuario.
     *
     * @param array $data    Datos del negocio a registrar.
     * @param int   $userId  Identificador del propietario.
     * @return int|false     Identificador del negocio creado o false si falla.
     */
    public function insertBusiness(array $data, int $userId)
    {
        $this->userId = $userId;

        $sql = <<<SQL
            INSERT INTO `business`
                (`typebusiness_id`, `name`, `direction`, `city`, `document_number`, `phone_number`, `country`, `telephone_prefix`, `email`, `userapp_id`)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;

        $params = [
            (int) ($data['typebusiness_id'] ?? 0),
            $data['name'] ?? '',
            $data['direction'] ?? null,
            $data['city'] ?? null,
            $data['document_number'] ?? '',
            $data['phone_number'] ?? '',
            $data['country'] ?? null,
            $data['telephone_prefix'] ?? '',
            $data['email'] ?? '',
            $this->userId,
        ];

        return $this->insert($sql, $params);
    }

    /**
     * Obtiene los tipos de negocio activos.
     *
     * @return array
     */
    public function selectBusinessTypes(): array
    {
        $sql = <<<SQL
            SELECT idBusinessType, `name`
            FROM business_type
            WHERE `status` = 'Activo'
            ORDER BY `name` ASC;
        SQL;

        $request = $this->select_all($sql);
        return $request ?? [];
    }

    /**
     * Registra los datos básicos por defecto para un nuevo negocio.
     *
     * @param int $businessId Identificador del negocio recién creado.
     * @return array|null     Resultado de las inserciones.
     */
    public function insertDefaultData(int $businessId)
    {
        $this->businessId = $businessId;
        $params = [$this->businessId];

        $sqls = [
            <<<SQL
                INSERT INTO category (`business_id`,`name`)
                VALUES
                (?,'Sin categoría');
            SQL,
            <<<SQL
                INSERT INTO `supplier` (`document_number`, `company_name`, `phone_number`, `direction`, `email`, `business_id`)
                VALUES
                ('00000000000', 'Sin proveedor', '999999999', 'Sin proveedor', 'Sin proveedor', ?);
            SQL,
            <<<SQL
                INSERT INTO `customer` (`fullname`, `documenttype_id`, `document_number`, `phone_number`, `email`, `direction`, `business_id`)
                VALUES
                ('Sin cliente', 1, 'Sin cliente', '999999999', 'sincliente@capyventas.com', 'Sin cliente', ?);
            SQL,
        ];

        $request = [];
        foreach ($sqls as $statement) {
            $request[] = $this->insert($statement, $params);
        }

        return $request;
    }
}
