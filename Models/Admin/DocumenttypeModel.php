<?php

/**
 * Modelo para la gestión de tipos de documento
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `document_type`
 * que almacena información de los tipos de documento del sistema.
 */
class DocumentTypeModel extends Mysql
{
    private $idDocumentType;
    private $name;
    private $description;
    private $status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de tipos de documento ordenados por fecha de registro descendente
     * 
     * @return array Lista de todos los tipos de documento registrados
     */
    public function select_document_types(): array
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                document_type
            ORDER BY
                registration_date DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene un tipo de documento por su ID
     * 
     * @param int $idDocumentType ID del tipo de documento a buscar
     * @return array|false Datos del tipo de documento o false si no existe
     */
    public function select_document_type_by_id(int $idDocumentType)
    {
        $this->idDocumentType = $idDocumentType;
        $sql = "SELECT * FROM document_type WHERE idDocumentType = ?";
        $arrValues = array($this->idDocumentType);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un tipo de documento por su nombre para validar duplicados
     * 
     * @param string $name Nombre del tipo de documento a buscar
     * @return array|false Datos del tipo de documento o false si no existe
     */
    public function select_document_type_by_name(string $name)
    {
        $this->name = $name;
        $sql = "SELECT * FROM document_type WHERE name = ?";
        $arrValues = array($this->name);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de tipo de documento en la base de datos
     * 
     * @param string $name Nombre del tipo de documento
     * @param string $description Descripción del tipo de documento (opcional)
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_document_type($name, $description = null)
    {
        $sql = "INSERT INTO `document_type` (`name`, `description`) VALUES (?,?);";
        $arrValues = array(
            $this->name = $name,
            $this->description = $description
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de tipo de documento en la base de datos
     * 
     * @param int $idDocumentType ID del tipo de documento a actualizar
     * @param string $name Nombre del tipo de documento
     * @param string $description Descripción del tipo de documento (opcional)
     * @param string $status Estado (Activo/Inactivo)
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_document_type($idDocumentType, $name, $description, $status)
    {
        $this->idDocumentType = $idDocumentType;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;

        $sql = "UPDATE `document_type` SET `name`=?, `description`=?, `status`=? WHERE idDocumentType=?";
        $arrValues = array(
            $this->name,
            $this->description,
            $this->status,
            $this->idDocumentType
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de tipo de documento de la base de datos
     * 
     * @param int $idDocumentType ID del tipo de documento a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_document_type($idDocumentType)
    {
        $this->idDocumentType = $idDocumentType;
        $sql = "DELETE FROM `document_type` WHERE idDocumentType = ?";
        $arrValues = array($this->idDocumentType);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }
}
