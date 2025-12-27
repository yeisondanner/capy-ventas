<?php

class BusinessModel extends Mysql
{
    private int $userId;
    private int $businessId;
    private int $typebusinessId;
    private string $name;
    private string $direction;
    private string $city;
    private string $documentNumber;
    private string $phoneNumber;
    private string $country;
    private string $telephonePrefix;
    private string $email;
    private string $taxName;
    private float $tax;
    private string $openBox;
    private string $logo;
    private string $extension;
    /**
     * Obtiene todos los negocios asociados a un usuario específico.
     *
     * @param int $userId Identificador del usuario propietario.
     * @return array Lista de negocios.
     */
    public function selectBusinessesByUserOwner(int $userId): array
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
            WHERE b.userapp_id = ? AND b.status = 'Activo'
            ORDER BY b.registration_date DESC;
        SQL;

        $request = $this->select_all($sql, [$this->userId]);
        return $request ?? [];
    }
    /**
     * Obtiene todos los negocios asociados a un usuario específico.
     *
     * @param int $userId Identificador del usuario propietario.
     * @return array Lista de negocios.
     */
    public function selectBusinessesByUserEmployee(int $userId): array
    {
        $this->userId = $userId;
        $sql = <<<SQL
                    SELECT
                        b.idBusiness,
                        b.`name` AS business,
                        b.document_number,
                        b.status,
                        bt.`name` AS category
                    FROM
                        user_app AS ua
                        INNER JOIN employee AS e ON e.userapp_id = ua.idUserApp
                        INNER JOIN business AS b ON b.idBusiness = e.bussines_id
                        INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
                    WHERE
                        ua.idUserApp = ? AND b.status = 'Activo';
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
                b.`name` AS 'business',
                bt.`name` AS 'category',
                b.direction,
                b.city,
                b.country,
                b.email,
                b.document_number,
                b.logo,
                b.openBox,
                b.taxname,
                b.tax
            FROM business AS b
            INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            WHERE b.idBusiness = ? AND b.userapp_id = ?
            LIMIT 1;
        SQL;

        $request = $this->select($sql, [$this->businessId, $this->userId]);
        return $request ?: null;
    }
    /**
     * Busca un negocio por su identificador y usuario empleado.
     *
     * @param int $businessId Identificador del negocio.
     * @param int $userId     Identificador del empleado.
     * @return array|null Datos del negocio o null si no pertenece al usuario.
     */
    public function selectBusinessByIdUserEmploye(int $businessId, int $userId)
    {
        $this->businessId = $businessId;
        $this->userId     = $userId;
        $sql = <<<SQL
                SELECT
                     b.idBusiness,
                                b.`name` AS 'business',
                                bt.`name` AS 'category',
                                b.direction,
                                b.city,
                                b.country,
                                b.email,
                                b.document_number,
                                b.logo,
                                b.openBox,
                                b.taxname,
                                b.tax
                FROM
                    user_app AS ua
                    INNER JOIN employee AS e ON e.userapp_id = ua.idUserApp
                    INNER JOIN business AS b ON b.idBusiness = e.bussines_id
                    INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
                WHERE
                        b.idBusiness = ?
                    AND ua.idUserApp = ?
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
            WHERE b.document_number = ? AND b.userapp_id = ?{$excludeSql} AND b.status = 'Activo'
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
        $this->businessId = $data['idBusiness'] ?? 0;
        $this->typebusinessId = $data['typebusiness_id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->direction = $data['direction'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->documentNumber = $data['document_number'] ?? '';
        $this->phoneNumber = $data['phone_number'] ?? '';
        $this->country = $data['country'] ?? null;
        $this->telephonePrefix = $data['telephone_prefix'] ?? '';
        $this->email = $data['email'] ?? '';
        $sql = <<<SQL
                    INSERT INTO `business`
                        (`typebusiness_id`, `name`, `direction`, `city`, `document_number`, `phone_number`, `country`, `telephone_prefix`, `email`, `userapp_id`)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
        SQL;

        $params = [
            $this->typebusinessId,
            $this->name,
            $this->direction,
            $this->city,
            $this->documentNumber,
            $this->phoneNumber,
            $this->country,
            $this->telephonePrefix,
            $this->email,
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
     * obtenemos el tipo del negocio por id
     * obtenemos la información 
     * @param int $id
     * @return array
     */
    public function selectBusinessTypeById(int $id): array
    {
        $sql = <<<SQL
            SELECT idBusinessType, `name`
            FROM business_type
            WHERE idBusinessType = ?;
        SQL;

        $request = $this->select($sql, [$id]);
        return $request ?: [];
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
            <<<SQL
                INSERT INTO `box` (`name`, `business_id`) VALUES ('Caja Principal', ?);
            SQL
        ];

        $request = [];
        foreach ($sqls as $statement) {
            $request[] = $this->insert($statement, $params);
        }

        return $request;
    }
    /**
     * Seleccionamos la informacion del negocio
     * por su id
     * @param int $idbusiness
     * @return array
     */
    public function select_info_business(int $idbusiness): array
    {
        $this->businessId = $idbusiness;
        $sql = <<<SQL
            SELECT
                *,
                b.`name` AS 'business',
                bt.`name` AS 'businesstype'
            FROM business AS b
            INNER JOIN business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            WHERE b.idBusiness = ?
            LIMIT 1;
        SQL;

        $request = $this->select($sql, [$this->businessId]);
        return $request ?: null;
    }
    /**
     * Metodo que se encarga de actualizar la informacion del negocio
     * @param array $data
     * @return int
     */
    public function updateBusiness(array $data)
    {
        $this->businessId = $data['idBusiness'] ?? 0;
        $this->typebusinessId = $data['typebusiness_id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->direction = $data['direction'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->documentNumber = $data['document_number'] ?? '';
        $this->phoneNumber = $data['phone_number'] ?? '';
        $this->country = $data['country'] ?? null;
        $this->telephonePrefix = $data['telephone_prefix'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->taxName = $data['taxname'] ?? '';
        $this->tax = $data['tax'] ?? 0;
        $this->openBox = $data['openBox'] ?? '';
        $this->logo = $data['logo'] ?? '';
        $sql = <<<SQL
            UPDATE 
                `business` 
                SET 
                    `typebusiness_id`=?, 
                    `name`=?, 
                    `direction`=?, 
                    `city`=?, 
                    `document_number`=?, 
                    `phone_number`=?, 
                    `country`=?, 
                    `telephone_prefix`=?, 
                    `email`=?, 
                    `taxname`=?, 
                    `tax`=?, 
                    `openBox`=?, 
                    `logo`=? 
            WHERE  
            `idBusiness`=?;
        SQL;
        $params = [
            $this->typebusinessId,
            $this->name,
            $this->direction,
            $this->city,
            $this->documentNumber,
            $this->phoneNumber,
            $this->country,
            $this->telephonePrefix,
            $this->email,
            $this->taxName,
            $this->tax,
            $this->openBox,
            $this->logo,
            $this->businessId,
        ];
        return $this->update($sql, $params);
    }
    /**
     * Metodo que desactiva un negocio
     * @param int $businessId
     * @return int
     */
    public function disableBusiness(int $businessId)
    {
        $this->businessId = $businessId;
        $sql = <<<SQL
            UPDATE 
                `business` 
                SET 
                    `status`='Inactivo' 
            WHERE  
            `idBusiness`=?;
        SQL;
        $params = [$this->businessId];
        return $this->update($sql, $params);
    }
    /**
     * Metodo que activa un negocio
     * @param int $businessId
     * @return int
     */
    public function enableBusiness(int $businessId)
    {
        $this->businessId = $businessId;
        $sql = <<<SQL
            UPDATE 
                `business` 
                SET 
                    `status`='Activo' 
            WHERE  
            `idBusiness`=?;
        SQL;
        $params = [$this->businessId];
        return $this->update($sql, $params);
    }
}
