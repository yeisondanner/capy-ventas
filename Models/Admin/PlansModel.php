<?php

/**
 * Modelo para la gestión de planes
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `plans`
 * que almacena información de los planes disponibles en CapyVentas.
 */
class PlansModel extends Mysql
{
    private $idPlan;
    private $name;
    private $description;
    private $basePrice;
    private $billingPeriod;
    private $isActive;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de planes ordenados por ID descendente
     * 
     * @return array Lista de todos los planes registrados
     */
    public function select_plans(): array
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                plans
            ORDER BY
                idPlan DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene un plan por su ID
     * 
     * @param int $idPlan ID del plan a buscar
     * @return array|false Datos del plan o false si no existe
     */
    public function select_plan_by_id(int $idPlan)
    {
        $this->idPlan = $idPlan;
        $sql = "SELECT * FROM plans WHERE idPlan = ?";
        $arrValues = array($this->idPlan);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un plan por su nombre para validar duplicados
     * 
     * @param string $name Nombre del plan a buscar
     * @return array|false Datos del plan o false si no existe
     */
    public function select_plan_by_name(string $name)
    {
        $this->name = $name;
        $sql = "SELECT * FROM plans WHERE name = ?";
        $arrValues = array($this->name);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de plan en la base de datos
     * 
     * @param string $name Nombre del plan
     * @param string|null $description Descripción del plan
     * @param float $basePrice Precio base del plan
     * @param string $billingPeriod Periodo de facturación (monthly/yearly)
     * @param int $isActive Estado activo (1) o inactivo (0)
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_plan($name, $description, $basePrice, $billingPeriod, $isActive)
    {
        $sql = "INSERT INTO `plans` (`name`, `description`, `base_price`, `billing_period`, `is_active`) VALUES (?,?,?,?,?);";
        $arrValues = array(
            $this->name = $name,
            $this->description = $description,
            $this->basePrice = $basePrice,
            $this->billingPeriod = $billingPeriod,
            $this->isActive = $isActive
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de plan en la base de datos
     * 
     * @param int $idPlan ID del plan a actualizar
     * @param string $name Nombre del plan
     * @param string|null $description Descripción del plan
     * @param float $basePrice Precio base del plan
     * @param string $billingPeriod Periodo de facturación (monthly/yearly)
     * @param int $isActive Estado activo (1) o inactivo (0)
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_plan($idPlan, $name, $description, $basePrice, $billingPeriod, $isActive)
    {
        $this->idPlan = $idPlan;
        $this->name = $name;
        $this->description = $description;
        $this->basePrice = $basePrice;
        $this->billingPeriod = $billingPeriod;
        $this->isActive = $isActive;

        $sql = "UPDATE `plans` SET `name`=?, `description`=?, `base_price`=?, `billing_period`=?, `is_active`=? WHERE idPlan=?";
        $arrValues = array(
            $this->name,
            $this->description,
            $this->basePrice,
            $this->billingPeriod,
            $this->isActive,
            $this->idPlan
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de plan de la base de datos
     * 
     * @param int $idPlan ID del plan a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_plan($idPlan)
    {
        $this->idPlan = $idPlan;
        $sql = "DELETE FROM `plans` WHERE idPlan = ?";
        $arrValues = array($this->idPlan);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }
}
