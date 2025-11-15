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
    private $status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los descuentos ordenados por fecha de creación
     *
     * @return array
     */
    public function select_discounts(): array
    {
        $sql = "SELECT * FROM discounts ORDER BY start_date DESC";
        $request = $this->select_all($sql);
        return $request;
    }

    /**
     * Obtiene un descuento por su ID
     *
     * @param int $idDiscount
     * @return array|false
     */
    public function select_discount_by_id(int $idDiscount)
    {
        $sql = "SELECT * FROM discounts WHERE idDiscount = ?";
        $arrData = array($idDiscount);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    /**
     * Busca un descuento por código
     *
     * @param string $code
     * @return array|false
     */
    public function select_discount_by_code(string $code)
    {
        $sql = "SELECT * FROM discounts WHERE code = ?";
        $arrData = array($code);
        $request = $this->select($sql, $arrData);
        return $request;
    }

    /**
     * Inserta un nuevo descuento
     *
     * @param string $code
     * @param string $type
     * @param float $value
     * @param string $startDate
     * @param string $endDate
     * @param int|null $appliesToPlanId
     * @param int|null $maxUses
     * @param int $isRecurring
     * @return int|false
     */
    public function insert_discount($code, $type, $value, $startDate, $endDate, $appliesToPlanId, $maxUses, $isRecurring)
    {
        $sql = "INSERT INTO discounts (code, type, value, start_date, end_date, applies_to_plan_id, max_uses, is_recurring)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $arrData = array(
            $code,
            $type,
            $value,
            $startDate,
            $endDate,
            $appliesToPlanId,
            $maxUses,
            $isRecurring
        );
        $request = $this->insert($sql, $arrData);
        return $request;
    }

    /**
     * Actualiza un descuento existente
     *
     * @param int $idDiscount
     * @param string $code
     * @param string $type
     * @param float $value
     * @param string $startDate
     * @param string $endDate
     * @param int|null $appliesToPlanId
     * @param int|null $maxUses
     * @param int $isRecurring
     * @param string $status
     * @return bool
     */
    public function update_discount($idDiscount, $code, $type, $value, $startDate, $endDate, $appliesToPlanId, $maxUses, $isRecurring, $status)
    {
        $sql = "UPDATE discounts SET
                code = ?,
                type = ?,
                value = ?,
                start_date = ?,
                end_date = ?,
                applies_to_plan_id = ?,
                max_uses = ?,
                is_recurring = ?,
                status = ?
                WHERE idDiscount = ?";
        $arrData = array(
            $code,
            $type,
            $value,
            $startDate,
            $endDate,
            $appliesToPlanId,
            $maxUses,
            $isRecurring,
            $status,
            $idDiscount
        );
        $request = $this->update($sql, $arrData);
        return $request;
    }

    /**
     * Elimina un descuento
     *
     * @param int $idDiscount
     * @return bool
     */
    public function delete_discount($idDiscount)
    {
        $sql = "DELETE FROM discounts WHERE idDiscount = ?";
        $arrData = array($idDiscount);
        $request = $this->delete($sql, $arrData);
        return $request;
    }

    /**
     * Obtiene todos los descuentos con información del plan asociado
     *
     * @return array
     */
    public function select_discounts_with_plans(): array
    {
        $sql = "SELECT
                    d.*,
                    p.name as plan_name
                FROM discounts AS d
                LEFT JOIN plans AS p ON p.idPlan = d.applies_to_plan_id
                ORDER BY d.start_date DESC";
        $request = $this->select_all($sql);
        return $request;
    }
}
