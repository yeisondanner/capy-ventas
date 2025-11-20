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
