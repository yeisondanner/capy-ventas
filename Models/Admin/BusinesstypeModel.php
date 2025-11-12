<?php

/**
 * Modelo para la gestión de tipos de negocio
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `business_type`
 * que almacena información de los tipos de negocio del sistema.
 */
class BusinessTypeModel extends Mysql
{
    private $idBusinessType;
    private $name;
    private $description;
    private $status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de tipos de negocio ordenados por fecha de registro descendente
     * 
     * @return array Lista de todos los tipos de negocio registrados
     */
    public function select_business_types(): array
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                business_type
            ORDER BY
                registration_date DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene un tipo de negocio por su ID
     * 
     * @param int $idBusinessType ID del tipo de negocio a buscar
     * @return array|false Datos del tipo de negocio o false si no existe
     */
    public function select_business_type_by_id(int $idBusinessType)
    {
        $this->idBusinessType = $idBusinessType;
        $sql = "SELECT * FROM business_type WHERE idBusinessType = ?";
        $arrValues = array($this->idBusinessType);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un tipo de negocio por su nombre para validar duplicados
     * 
     * @param string $name Nombre del tipo de negocio a buscar
     * @return array|false Datos del tipo de negocio o false si no existe
     */
    public function select_business_type_by_name(string $name)
    {
        $this->name = $name;
        $sql = "SELECT * FROM business_type WHERE name = ?";
        $arrValues = array($this->name);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de tipo de negocio en la base de datos
     * 
     * @param string $name Nombre del tipo de negocio
     * @param string $description Descripción del tipo de negocio (opcional)
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_business_type($name, $description = null)
    {
        $sql = "INSERT INTO `business_type` (`name`, `description`) VALUES (?,?);";
        $arrValues = array(
            $this->name = $name,
            $this->description = $description
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de tipo de negocio en la base de datos
     * 
     * @param int $idBusinessType ID del tipo de negocio a actualizar
     * @param string $name Nombre del tipo de negocio
     * @param string $description Descripción del tipo de negocio (opcional)
     * @param string $status Estado (Activo/Inactivo)
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_business_type($idBusinessType, $name, $description, $status)
    {
        $this->idBusinessType = $idBusinessType;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;

        $sql = "UPDATE `business_type` SET `name`=?, `description`=?, `status`=? WHERE idBusinessType=?";
        $arrValues = array(
            $this->name,
            $this->description,
            $this->status,
            $this->idBusinessType
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de tipo de negocio de la base de datos
     * 
     * @param int $idBusinessType ID del tipo de negocio a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_business_type($idBusinessType)
    {
        $this->idBusinessType = $idBusinessType;
        $sql = "DELETE FROM `business_type` WHERE idBusinessType = ?";
        $arrValues = array($this->idBusinessType);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }
}
