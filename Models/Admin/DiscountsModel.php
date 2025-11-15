<?php

/**
 * Modelo para la gestión de descuentos
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `discounts`
 * que almacena información de los descuentos y cupones promocionales del sistema.
 */
class DiscountsModel extends Mysql
{
    private $idDiscount;
    private $code;
    private $type;
    private $value;
    private $startDate;
    private $endDate;
    private $appliesToPlanId;
    private $maxUses;
    private $isRecurring;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros de descuentos ordenados por fecha de inicio descendente
     * Incluye información del plan asociado mediante LEFT JOIN
     * 
     * @return array Lista de todos los descuentos registrados con información relacionada
     */
    public function select_discounts(): array
    {
        $query = <<<SQL
            SELECT
                d.*,
                p.name as plan_name
            FROM
                discounts AS d
            LEFT JOIN
                plans AS p ON p.idPlan = d.applies_to_plan_id
            ORDER BY
                d.start_date DESC, d.idDiscount DESC;
        SQL;
        $request = $this->select_all($query);
        return $request ?? [];
    }

    /**
     * Obtiene un descuento por su ID
     * 
     * @param int $idDiscount ID del descuento a buscar
     * @return array|false Datos del descuento o false si no existe
     */
    public function select_discount_by_id(int $idDiscount)
    {
        $this->idDiscount = $idDiscount;
        $sql = "SELECT * FROM discounts WHERE idDiscount = ?";
        $arrValues = array($this->idDiscount);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene un descuento por su código para validar duplicados
     * 
     * @param string $code Código del descuento a buscar
     * @return array|false Datos del descuento o false si no existe
     */
    public function select_discount_by_code(string $code)
    {
        $this->code = $code;
        $sql = "SELECT * FROM discounts WHERE code = ?";
        $arrValues = array($this->code);
        $request = $this->select($sql, $arrValues);
        return $request;
    }

    /**
     * Inserta un nuevo registro de descuento en la base de datos
     * 
     * @param string $code Código del descuento
     * @param string $type Tipo de descuento (percentage/fixed)
     * @param float $value Valor del descuento
     * @param string|null $startDate Fecha de inicio (formato: Y-m-d H:i:s)
     * @param string|null $endDate Fecha de fin (formato: Y-m-d H:i:s)
     * @param int|null $appliesToPlanId ID del plan al que aplica (null si aplica a todos)
     * @param int|null $maxUses Límite máximo de usos (null si no tiene límite)
     * @param int $isRecurring Si es 1, el descuento se aplica en todos los ciclos; si es 0, solo en la primera factura
     * @return int|false ID del registro insertado o false en caso de error
     */
    public function insert_discount($code, $type, $value, $startDate, $endDate, $appliesToPlanId, $maxUses, $isRecurring)
    {
        $sql = "INSERT INTO `discounts` (`code`, `type`, `value`, `start_date`, `end_date`, `applies_to_plan_id`, `max_uses`, `is_recurring`) VALUES (?,?,?,?,?,?,?,?);";
        $arrValues = array(
            $this->code = $code,
            $this->type = $type,
            $this->value = $value,
            $this->startDate = $startDate,
            $this->endDate = $endDate,
            $this->appliesToPlanId = $appliesToPlanId,
            $this->maxUses = $maxUses,
            $this->isRecurring = $isRecurring
        );
        $request = $this->insert($sql, $arrValues);
        return $request;
    }

    /**
     * Actualiza un registro de descuento en la base de datos
     * 
     * @param int $idDiscount ID del descuento a actualizar
     * @param string $code Código del descuento
     * @param string $type Tipo de descuento (percentage/fixed)
     * @param float $value Valor del descuento
     * @param string|null $startDate Fecha de inicio (formato: Y-m-d H:i:s)
     * @param string|null $endDate Fecha de fin (formato: Y-m-d H:i:s)
     * @param int|null $appliesToPlanId ID del plan al que aplica (null si aplica a todos)
     * @param int|null $maxUses Límite máximo de usos (null si no tiene límite)
     * @param int $isRecurring Si es 1, el descuento se aplica en todos los ciclos; si es 0, solo en la primera factura
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function update_discount($idDiscount, $code, $type, $value, $startDate, $endDate, $appliesToPlanId, $maxUses, $isRecurring)
    {
        $this->idDiscount = $idDiscount;
        $this->code = $code;
        $this->type = $type;
        $this->value = $value;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->appliesToPlanId = $appliesToPlanId;
        $this->maxUses = $maxUses;
        $this->isRecurring = $isRecurring;

        $sql = "UPDATE `discounts` SET `code`=?, `type`=?, `value`=?, `start_date`=?, `end_date`=?, `applies_to_plan_id`=?, `max_uses`=?, `is_recurring`=? WHERE idDiscount=?";
        $arrValues = array(
            $this->code,
            $this->type,
            $this->value,
            $this->startDate,
            $this->endDate,
            $this->appliesToPlanId,
            $this->maxUses,
            $this->isRecurring,
            $this->idDiscount
        );

        $request = $this->update($sql, $arrValues);
        return $request;
    }

    /**
     * Elimina un registro de descuento de la base de datos
     * 
     * @param int $idDiscount ID del descuento a eliminar
     * @return bool true si se eliminó correctamente, false en caso contrario
     */
    public function delete_discount($idDiscount)
    {
        $this->idDiscount = $idDiscount;
        $sql = "DELETE FROM `discounts` WHERE idDiscount = ?";
        $arrValues = array($this->idDiscount);
        $request = $this->delete($sql, $arrValues);
        return $request;
    }

    /**
     * Obtiene todos los planes activos para usar en select
     * 
     * @return array Lista de planes activos
     */
    public function select_plans_active(): array
    {
        $sql = "SELECT idPlan, name FROM plans WHERE is_active = 1 ORDER BY name ASC";
        $request = $this->select_all($sql);
        return $request ?? [];
    }
}
