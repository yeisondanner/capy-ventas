<?php

class RolesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Devuelve los roles registrados para un negocio.
     *
     * @param int $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectRoles(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idRoleApp,
                name,
                description,
                status,
                registration_date,
                update_date
            FROM role_app
            WHERE business_id = ?
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Recupera un rol específico vinculado al negocio indicado.
     *
     * @param int $roleId     Identificador del rol.
     * @param int $businessId Identificador del negocio.
     *
     * @return array
     */
    public function selectRole(int $roleId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idRoleApp,
                name,
                description,
                status
            FROM role_app
            WHERE idRoleApp = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$roleId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca roles por nombre dentro del mismo negocio.
     *
     * @param int $businessId Identificador del negocio.
     * @param string $name    Nombre del rol.
     * @param int $excludeId  Identificador a excluir.
     *
     * @return array
     */
    // * Mis funciones
    // TODO: Funciones get
    public function getBusiness(int $bussinesId)
    {
        $sql = <<<SQL
            SELECT
                userapp_id,
                status
            FROM business
            WHERE idBusiness = ?
              AND status = 'Activo'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$bussinesId]);
        return is_array($result) ? $result : [];
    }

    public function getSuscription(int $userAppId)
    {
        $sql = <<<SQL
            SELECT
                max(plan_id) as plan_id,
                next_billing_date,
                status
            FROM subscriptions
            WHERE user_app_id = ?
              AND status = 'active'
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$userAppId]);
        return is_array($result) ? $result : [];
    }
    // TODO: Funciones getall
    public function getInterfaces()
    {
        $sql = <<<SQL
            SELECT
                idInterface,
                name,
                type,
                module_id,
                status
            FROM interface_app
            WHERE status = 'Activo';
        SQL;

        $result = $this->select_all($sql, []);
        return is_array($result) ? $result : [];
    }

    public function getInterfacesByPlan(int $planId)
    {
        $sql = <<<SQL
            SELECT
                idPlansInterfaceApp as plan_interface_id,
                interface_id,
                plan_id,
                `create`,
                `delete`,
                `update`,
                `read`,
                status
            FROM plans_interface_app
            WHERE plan_id = ?
              AND status = 'Activo';
        SQL;

        $result = $this->select_all($sql, [$planId]);
        return is_array($result) ? $result : [];
    }

    public function getPermissions(int $roleId)
    {
        $sql = <<<SQL
            SELECT
                plans_interface_app_id as plan_interface_id,
                rol_id,
                `create`,
                `delete`,
                `update`,
                `read`,
                status
            FROM permission
            WHERE rol_id = ?
              AND status = 'Activo';
        SQL;

        $result = $this->select_all($sql, [$roleId]);
        return is_array($result) ? $result : [];
    }
    // TODO: Funciones set
    // TODO: Funciones update
    // TODO: Funciones delete

    public function selectRoleByName(int $businessId, string $name, int $excludeId = 0): array
    {
        $sql = 'SELECT idRoleApp FROM role_app WHERE business_id = ? AND name = ?';
        $params = [$businessId, $name];

        if ($excludeId > 0) {
            $sql .= ' AND idRoleApp != ?';
            $params[] = $excludeId;
        }

        $sql .= ' LIMIT 1;';

        $result = $this->select($sql, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un rol de aplicación asociado a un negocio.
     *
     * @param int $businessId Identificador del negocio.
     * @param array $data     Datos del rol.
     *
     * @return int
     */
    public function insertRole(int $businessId, array $data): int
    {
        $sql = <<<SQL
            INSERT INTO role_app
                (name, description, status, business_id)
            VALUES
                (?, ?, ?, ?);
        SQL;

        $params = [
            $data['name'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['status'],
            $businessId,
        ];

        return (int) $this->insert($sql, $params);
    }

    public function setPermission(int $planInterfaceId, int $roleId, int $create, int $read, int $update, int $delete)
    {
        $sql = <<<SQL
            INSERT INTO permission
                (plans_interface_app_id, rol_id, `create`, `read`, `update`, `delete`)
            VALUES
                (?, ?, ?, ?, ?, ?);
        SQL;

        $params = [
            $planInterfaceId,
            $roleId,
            $create,
            $read,
            $update,
            $delete
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza un rol de aplicación.
     *
     * @param int $roleId     Identificador del rol.
     * @param int $businessId Identificador del negocio.
     * @param array $data     Datos a actualizar.
     *
     * @return bool
     */
    public function updateRole(int $roleId, int $businessId, array $data): bool
    {
        $sql = <<<SQL
            UPDATE role_app
            SET
                name        = ?,
                description = ?,
                status      = ?
            WHERE idRoleApp = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['name'],
            $data['description'] !== '' ? $data['description'] : null,
            $data['status'],
            $roleId,
            $businessId,
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Elimina un rol de aplicación.
     *
     * @param int $roleId     Identificador del rol.
     * @param int $businessId Identificador del negocio.
     *
     * @return bool
     */
    public function deleteRole(int $roleId, int $businessId): bool
    {
        $sql = 'DELETE FROM role_app WHERE idRoleApp = ? AND business_id = ? LIMIT 1;';

        return (bool) $this->delete($sql, [$roleId, $businessId]);
    }

    public function dropPermissionsByRole(int $roleId): bool
    {
        $sql = 'DELETE FROM permission WHERE rol_id = ?';

        return (bool) $this->delete($sql, [$roleId]);
    }
    /**
     * Desactiva un rol con dependencias.
     *
     * @param int $roleId     Identificador del rol.
     * @param int $businessId Identificador del negocio.
     *
     * @return bool
     */
    public function deactivateRole(int $roleId, int $businessId): bool
    {
        $sql = <<<SQL
            UPDATE role_app
            SET status = 'Inactivo'
            WHERE idRoleApp = ?
              AND business_id = ?
            LIMIT 1;
        SQL;

        return (bool) $this->update($sql, [$roleId, $businessId]);
    }

    /**
     * Cuenta los empleados asociados a un rol dentro de un negocio.
     *
     * @param int $roleId     Identificador del rol.
     * @param int $businessId Identificador del negocio.
     *
     * @return int
     */
    public function countEmployeesByRole(int $roleId, int $businessId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM employee WHERE rolapp_id = ? AND bussines_id = ?;';

        $result = $this->select($sql, [$roleId, $businessId]);

        return isset($result['total']) ? (int) $result['total'] : 0;
    }

    /**
     * Cuenta los permisos vinculados a un rol.
     *
     * @param int $roleId Identificador del rol.
     *
     * @return int
     */
    public function countPermissionsByRole(int $roleId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM permission WHERE rol_id = ?;';

        $result = $this->select($sql, [$roleId]);

        return isset($result['total']) ? (int) $result['total'] : 0;
    }
}
