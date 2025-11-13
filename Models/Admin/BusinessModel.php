<?php

/**
 * Modelo para la gestión de negocios
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `business`
 * que almacena información de los negocios del sistema.
 */
class BusinessModel extends Mysql
{
    private $idBusiness;
    private $typebusinessId;
    private $name;
    private $direction;
    private $city;
    private $documentNumber;
    private $phoneNumber;
    private $country;
    private $telephonePrefix;
    private $email;
    private $status;
    private $userappId;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de negocios ordenados por fecha de registro descendente
     * Incluye información de tipo de negocio y usuario de aplicación mediante JOIN
     * 
     * @return array Lista de todos los negocios registrados con información relacionada
     */
    public function select_businesses(): array
    {
        $query = <<<SQL
            SELECT
                b.*,
                bt.name as business_type_name,
                ua.user as user_app_name
            FROM
                business AS b
            LEFT JOIN
                business_type AS bt ON bt.idBusinessType = b.typebusiness_id
            LEFT JOIN
                user_app AS ua ON ua.idUserApp = b.userapp_id
            ORDER BY
                b.registration_date DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene un negocio por su ID
     * 
     * @param int $idBusiness ID del negocio a buscar
     * @return array|false Datos del negocio o false si no existe
     */
    public function select_business_by_id(int $idBusiness)
    {
        $this->idBusiness = $idBusiness;
        $sql = "SELECT * FROM business WHERE idBusiness = ?";
        $arrValues = array($this->idBusiness);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un negocio por su número de documento para validar duplicados
     * 
     * @param string $documentNumber Número de documento a buscar
     * @return array|false Datos del negocio o false si no existe
     */
    public function select_business_by_document(string $documentNumber)
    {
        $this->documentNumber = $documentNumber;
        $sql = "SELECT * FROM business WHERE document_number = ?";
        $arrValues = array($this->documentNumber);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un negocio por su email para validar duplicados
     * 
     * @param string $email Email a buscar
     * @return array|false Datos del negocio o false si no existe
     */
    public function select_business_by_email(string $email)
    {
        $this->email = $email;
        $sql = "SELECT * FROM business WHERE email = ?";
        $arrValues = array($this->email);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un negocio por su número de teléfono para validar duplicados
     * 
     * @param string $phoneNumber Número de teléfono a buscar
     * @return array|false Datos del negocio o false si no existe
     */
    public function select_business_by_phone(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        $sql = "SELECT * FROM business WHERE phone_number = ?";
        $arrValues = array($this->phoneNumber);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de negocio en la base de datos
     * 
     * @param int $typebusinessId ID del tipo de negocio
     * @param string $name Nombre del negocio
     * @param string $direction Dirección del negocio (opcional)
     * @param string $city Ciudad del negocio (opcional)
     * @param string $documentNumber Número de documento
     * @param string $phoneNumber Número de teléfono
     * @param string $country País (opcional)
     * @param string $telephonePrefix Prefijo telefónico
     * @param string $email Correo electrónico
     * @param int $userappId ID del usuario de aplicación
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_business($typebusinessId, $name, $direction, $city, $documentNumber, $phoneNumber, $country, $telephonePrefix, $email, $userappId)
    {
        $sql = "INSERT INTO `business` (`typebusiness_id`, `name`, `direction`, `city`, `document_number`, `phone_number`, `country`, `telephone_prefix`, `email`, `userapp_id`) VALUES (?,?,?,?,?,?,?,?,?,?);";
        $arrValues = array(
            $this->typebusinessId = $typebusinessId,
            $this->name = $name,
            $this->direction = $direction,
            $this->city = $city,
            $this->documentNumber = $documentNumber,
            $this->phoneNumber = $phoneNumber,
            $this->country = $country,
            $this->telephonePrefix = $telephonePrefix,
            $this->email = $email,
            $this->userappId = $userappId
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de negocio en la base de datos
     * 
     * @param int $idBusiness ID del negocio a actualizar
     * @param int $typebusinessId ID del tipo de negocio
     * @param string $name Nombre del negocio
     * @param string $direction Dirección del negocio (opcional)
     * @param string $city Ciudad del negocio (opcional)
     * @param string $documentNumber Número de documento
     * @param string $phoneNumber Número de teléfono
     * @param string $country País (opcional)
     * @param string $telephonePrefix Prefijo telefónico
     * @param string $email Correo electrónico
     * @param string $status Estado (Activo/Inactivo)
     * @param int $userappId ID del usuario de aplicación
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_business($idBusiness, $typebusinessId, $name, $direction, $city, $documentNumber, $phoneNumber, $country, $telephonePrefix, $email, $status, $userappId)
    {
        $this->idBusiness = $idBusiness;
        $this->typebusinessId = $typebusinessId;
        $this->name = $name;
        $this->direction = $direction;
        $this->city = $city;
        $this->documentNumber = $documentNumber;
        $this->phoneNumber = $phoneNumber;
        $this->country = $country;
        $this->telephonePrefix = $telephonePrefix;
        $this->email = $email;
        $this->status = $status;
        $this->userappId = $userappId;

        $sql = "UPDATE `business` SET `typebusiness_id`=?, `name`=?, `direction`=?, `city`=?, `document_number`=?, `phone_number`=?, `country`=?, `telephone_prefix`=?, `email`=?, `status`=?, `userapp_id`=? WHERE idBusiness=?";
        $arrValues = array(
            $this->typebusinessId,
            $this->name,
            $this->direction,
            $this->city,
            $this->documentNumber,
            $this->phoneNumber,
            $this->country,
            $this->telephonePrefix,
            $this->email,
            $this->status,
            $this->userappId,
            $this->idBusiness
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de negocio de la base de datos
     * 
     * @param int $idBusiness ID del negocio a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_business($idBusiness)
    {
        $this->idBusiness = $idBusiness;
        $sql = "DELETE FROM `business` WHERE idBusiness = ?";
        $arrValues = array($this->idBusiness);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene todos los tipos de negocio activos para usar en select
     * 
     * @return array Lista de tipos de negocio activos
     */
    public function select_business_types_active(): array
    {
        $sql = "SELECT idBusinessType, name FROM business_type WHERE status = 'Activo' ORDER BY name ASC";
        $request = $this->select_all($sql);
        return $request ?? [];
    }

    /**
     * Obtiene todos los usuarios de aplicación activos para usar en select
     * 
     * @return array Lista de usuarios de aplicación activos con información de persona
     * Nota: El campo 'user' viene encriptado, debe desencriptarse en el controlador
     */
    public function select_user_apps_active(): array
    {
        $query = <<<SQL
            SELECT
                ua.idUserApp,
                ua.user,
                p.names,
                p.lastname,
                p.email
            FROM
                user_app AS ua
            INNER JOIN
                people AS p ON p.idPeople = ua.people_id
            WHERE
                ua.status = 'Activo'
            ORDER BY
                p.names, p.lastname ASC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }
    /**
     * Metodo que se encarga de registrar 
     * los registros iniciales de los negocios
     * 
     * @param int $idBusiness
     * @return void
     */
    public function insert_default_data(int $idBusiness)
    {
        $this->idBusiness = $idBusiness;
        $arrValues = array(
            $this->idBusiness
        );
        //sql de la categoria inicial
        $sqls = array(
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
        SQL
        );

        foreach ($sqls as $key => $value) {
            $request[] = $this->insert($value, $arrValues);
        }

        return $request;
    }
}
