<?php
class PermissionModel extends Mysql
{
    protected int $idUserApp;
    protected int $idPlan;
    protected int $idBusiness;
    protected string $datetimeStart;
    protected string $datetimeEnd;
    protected string $datetimeNextBilling;
    protected float $pricePerCycle;
    protected string $dateStart;
    protected string $dateEnd;

    /**
     * Inicializa el modelo base y establece la conexión con la base de datos.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Obtiene los planes de suscripción asociados a un usuario específico.
     */
    public function get_plans_subscription(int $idUser)
    {
        $this->idUserApp = $idUser;
        $sql = <<<SQL
                SELECT
                    ua.plan_expiration_date AS 'fecha_vencimiento',
                    s.*,
                    p.*
                FROM
                    user_app AS ua
                    INNER JOIN subscriptions AS s ON (s.user_app_id = ua.idUserApp AND ua.plan_expiration_date=s.end_date)
                    INNER JOIN plans AS p ON p.idPlan = s.plan_id
                WHERE
                    ua.idUserApp = ?
                ORDER BY s.end_date DESC;
        SQL;
        $request = $this->select($sql, [$this->idUserApp]);
        return $request;
    }
    /**
     * Inserta una suscripción gratuita para un usuario.
     * que no tiene plan vigente o el plan ha expirado
     * @param int $idUser
     */
    public function insert_plan_subscription_free(int $idUser)
    {
        $dataEndNextBilling = date("Y-m-d H:i:s", strtotime("+30 days"));
        $this->idUserApp = $idUser;
        $this->idPlan = 1; // Plan gratuito
        $this->datetimeStart = date("Y-m-d H:i:s");
        $this->dateStart = date("Y-m-d");
        $this->datetimeEnd = $dataEndNextBilling;
        $this->dateEnd = date("Y-m-d");
        $this->datetimeNextBilling = $dataEndNextBilling;
        $this->pricePerCycle = 0.00;
        //consultas  necesarias para insertar la suscripción, factura y actualizar la fecha de vencimiento en user_app
        $sqlInsertSuscription = <<<SQL
                    INSERT INTO `subscriptions` (
                        `user_app_id`, `plan_id`, `start_date`,
                        `end_date`, `next_billing_date`,
                        `price_per_cycle`
                    )
                    VALUES
                        (
                            ?, ?, ?, ?,?, ?
                        );
        SQL;
        $sqlInsertInvoice = <<<SQL
                            INSERT INTO `invoices` 
                            (`subscription_id`, `period_start`, `period_end`, `subtotal`, `discount_amount`, `total`, `status`,`paid_at`) 
                            VALUES (?, ?, ?, 0, 0, 0, 'paid', NOW());
        SQL;
        $sqlUpdatePlanExpirationDate = <<<SQL
                UPDATE `user_app` SET `plan_expiration_date`=? WHERE  `idUserApp`=?;
        SQL;
        $insertResult = $this->insert($sqlInsertSuscription, [
            $this->idUserApp,
            $this->idPlan,
            $this->datetimeStart,
            $this->datetimeEnd,
            $this->datetimeNextBilling,
            $this->pricePerCycle
        ]);
        //insertamos la el registro en la facturación
        if ($insertResult > 0) {
            $insertInvoice = $this->insert($sqlInsertInvoice, [
                $insertResult,
                $this->dateStart,
                $this->dateEnd
            ]);
            //actualizamos la fecha de vencimiento del plan en user_app
            $this->update($sqlUpdatePlanExpirationDate, [
                $this->datetimeEnd,
                $this->idUserApp
            ]);
        }
    }
    /**
     * Obtiene los permisos de las interfaces asignados a un plan
     * @param int $idPlan
     * @return array
     */
    public function get_permissions_functions(int $idPlan)
    {
        $this->idPlan = $idPlan;
        $sql = <<<SQL
                SELECT
                    pia.plan_id,
                    pia.idPlansInterfaceApp,
                    ma.idModule,
                    ia.idInterface,
                    ma.`name` AS 'Module',
                    ia.`name` AS 'Interface',
                    pia.`create`,
                    pia.`delete`,
                    pia.`update`,
                    pia.`status`
                FROM
                    module_app AS ma
                    INNER JOIN interface_app AS ia ON ia.module_id = ma.idModule
                    INNER JOIN plans_interface_app AS pia ON pia.interface_id=ia.idInterface
                    WHERE pia.plan_id=?;
        SQL;
        $request = $this->select_all($sql, [$this->idPlan]);
        return $request ?? [];
    }
    /**
     * Obtiene el negocio dueño de un usuario
     * @param int $idUser
     * @return array
     */
    public function get_bussiness_owner(int $idUserApp, int $idBusiness)
    {
        $this->idUserApp = $idUserApp;
        $this->idBusiness = $idBusiness;
        $sql = <<<SQL
                SELECT
                    *
                FROM
                    business AS b
                WHERE
                    b.idBusiness = ?
                    AND b.userapp_id = ?;
        SQL;
        $request = $this->select($sql, [$this->idBusiness, $this->idUserApp]) ?? [];
        return $request;
    }
}
