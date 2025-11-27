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
    protected int $idRoleApp;
    protected int $idInterface;

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
                    pia.`read`,
                    pia.`update`,
                    pia.`delete`,
                    pia.`status`
                FROM
                    module_app AS ma
                    INNER JOIN interface_app AS ia ON ia.module_id = ma.idModule
                    INNER JOIN plans_interface_app AS pia ON pia.interface_id=ia.idInterface
                    WHERE pia.plan_id=? AND pia.`status` = 'Activo' AND ia.`status`='Activo';
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
    /**
     * Consultamos la informacion del negocio que usuario 
     * esta como empleado
     * @param int $idbusiness
     * @return array
     */
    public function get_business_employee(int $idBusiness)
    {
        $this->idBusiness = $idBusiness;
        $sql = <<<SQL
                SELECT
                    *
                FROM
                    business AS b
                    INNER JOIN user_app AS ua ON ua.idUserApp = b.userapp_id
                    INNER JOIN subscriptions AS s ON (s.user_app_id=ua.idUserApp AND s.end_date=ua.plan_expiration_date AND s.plan_id!=1)
                WHERE
                    b.idBusiness = ?;
        SQL;
        $request = $this->select($sql, [$this->idBusiness]) ?? [];
        return $request;
    }
    /**
     * Obtiene la informacion del usuario
     * @param int $idUserApp
     * @return array
     */
    public function get_information_user(int $idUserApp)
    {
        $this->idUserApp = $idUserApp;
        $sql = <<<SQL
                SELECT
                    *
                FROM
                    employee AS e
                    INNER JOIN user_app AS ua ON ua.idUserApp = e.userapp_id
                    INNER JOIN role_app AS ra ON ra.idRoleApp=e.rolapp_id
                WHERE
                    e.userapp_id = ?;
        SQL;
        $request = $this->select($sql, [$this->idUserApp]) ?? [];
        return $request;
    }
    /**
     * Obtiene los permisos de las interfaces asignados a un plan de acuerdo al rol del usuario
     * @param int $idUserApp
     * @param int $idBusiness
     * @param int $idRoleApp
     * @return array
     */
    public function get_permssion_user_employes(int $idUserApp, int $idBusiness, int $idRoleApp)
    {
        $this->idUserApp = $idUserApp;
        $this->idBusiness = $idBusiness;
        $this->idRoleApp = $idRoleApp;
        $sql = <<<SQL
                SELECT
                    pia.plan_id,
                    pia.idPlansInterfaceApp,
                    ma.idModule,
                    ia.idInterface,
                    ma.`name` AS 'Module',
                    ia.`name` AS 'Interface',
                    CONCAT(pia.`create`,'-',pms.`create`) AS 'create',
                    CONCAT(pia.`read`,'-',pms.`read`) AS 'read',
                    CONCAT(pia.`update`,'-',pms.`update`) AS 'update',
                    CONCAT(pia.`delete`,'-',pms.`delete`) AS 'delete',
                    CONCAT(pia.`status`,'-',pms.`status`) AS 'status',
                    ua.plan_expiration_date
                FROM
                    user_app AS ua
                    INNER JOIN employee AS e ON e.userapp_id = ua.idUserApp
                    INNER JOIN role_app AS ra ON ra.idRoleApp = e.rolapp_id
                    INNER JOIN permission AS pms ON pms.rol_id = ra.idRoleApp
                    INNER JOIN plans_interface_app AS pia ON pia.idPlansInterfaceApp = pms.plans_interface_app_id
                    INNER JOIN interface_app AS ia ON ia.idInterface = pia.interface_id
                    INNER JOIN module_app AS ma ON ma.idModule = ia.module_id
                    INNER JOIN plans AS pl ON pl.idPlan = pia.plan_id
                WHERE
                    e.userapp_id = ?
                    AND e.bussines_id = ?
                    AND ra.idRoleApp=?
                    AND pia.`status` = 'Activo'
                    AND pms.`status` = 'Activo'
                    AND ia.`status`='Activo';
        SQL;
        $request = $this->select_all($sql, [$this->idUserApp, $this->idBusiness, $this->idRoleApp]) ?? [];
        return $request;
    }
    /**
     * Obtiene los permisos de la interface
     * @param int $iduser
     * @param int $idbusiness
     * @param int $idinterface
     * @return array
     */
    public function get_permission_interface(int $iduser, int $idbusiness, int $idinterface)
    {
        $this->idUserApp = $iduser;
        $this->idBusiness = $idbusiness;
        $this->idInterface = $idinterface;
        $sql = <<<SQL
            SELECT
                e.bussines_id,
                e.idEmployee,
                e.userapp_id,
                ra.idRoleApp,
                p.idPermission,
                ia.idInterface,
                ma.`name` AS 'modulo',
                ia.`name` AS 'interface',
                p.`create`,
                p.`read`,
                p.`update`,
                p.`delete`,
                p.`status` AS 'permission_status',
                pia.`status` AS 'plans_interface_status',
                ia.`status` AS 'interface_status',
                pia.`create` AS 'pia_create',
                pia.`read` AS 'pia_read',
                pia.`update` AS 'pia_update',
                pia.`delete` AS 'pia_delete'
            FROM
                employee AS e
                INNER JOIN role_app AS ra ON ra.idRoleApp = e.rolapp_id
                INNER JOIN permission AS p ON p.rol_id = ra.idRoleApp
                INNER JOIN plans_interface_app AS pia ON pia.idPlansInterfaceApp = p.plans_interface_app_id
                INNER JOIN interface_app AS ia ON ia.idInterface = pia.interface_id
                INNER JOIN module_app AS ma ON ma.idModule = ia.module_id
            WHERE
                e.userapp_id = ?
                AND e.bussines_id =? 
                AND ia.idInterface = ?
                AND e.rolapp_id =(
                    SELECT
                        e.rolapp_id
                    FROM
                        employee AS e
                    WHERE
                        e.userapp_id = ?
                        AND e.bussines_id = ?
                    LIMIT
                        1
                );
        SQL;
        $request = $this->select($sql, [$this->idUserApp, $this->idBusiness, $this->idInterface, $this->idUserApp, $this->idBusiness]) ?? [];
        return $request;
    }
    /**
     * Obtiene los permisos de la interface como propietario por el plan
     * @param int $idUserApp
     * @param int $idInterface
     * @return array
     */
    public function get_permission_interface_owner(int $idUserApp, int $idInterface)
    {
        $this->idUserApp = $idUserApp;
        $this->idInterface = $idInterface;
        $sql = <<<SQL
            SELECT
                ua.idUserApp,
                s.idSubscription,
                p.idPlan,
                pia.idPlansInterfaceApp,
                ia.idInterface,
                p.`name` AS 'plan',
                ia.`name` AS 'interface',
                pia.`create`,
                pia.`read`,
                pia.`update`,
                pia.`delete`,
                pia.`status` AS 'pia_status',
                ia.`status` AS 'ia_status'
            FROM
                user_app AS ua
                INNER JOIN subscriptions AS s ON (
                    s.user_app_id = ua.idUserApp
                    AND s.end_date = ua.plan_expiration_date
                )
                INNER JOIN plans AS p ON p.idPlan=s.plan_id
                INNER JOIN plans_interface_app AS pia ON pia.plan_id=p.idPlan
                INNER JOIN interface_app AS ia ON ia.idInterface=pia.interface_id
                WHERE ua.idUserApp=? AND ia.idInterface=?;
        SQL;
        $request = $this->select($sql, [$this->idUserApp, $this->idInterface]) ?? [];
        return $request;
    }
}
