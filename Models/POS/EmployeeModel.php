<?php

/**
 * Modelo para la gestión de empleados en el POS
 * 
 * Este modelo maneja todas las operaciones CRUD relacionadas con la tabla `employee`
 * que almacena información de los empleados asociados a negocios.
 */
class EmployeeModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupera los empleados pertenecientes al negocio indicado.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectEmployees(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                e.idEmployee,
                e.bussines_id,
                e.userapp_id,
                e.rolapp_id,
                e.status,
                e.registration_date,
                e.update_date,
                ua.user AS user_app_user,
                ua.idUserApp AS user_app_id,
                p.idPeople AS person_id,
                p.names AS person_names,
                p.lastname AS person_lastname,
                p.email AS person_email,
                ra.name AS role_app_name,
                ra.description AS role_app_description
            FROM employee AS e
            LEFT JOIN user_app AS ua ON ua.idUserApp = e.userapp_id
            LEFT JOIN people AS p ON p.idPeople = ua.people_id
            INNER JOIN role_app AS ra ON ra.idRoleApp = e.rolapp_id
            WHERE e.bussines_id = ?
            ORDER BY e.registration_date DESC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Recupera un empleado específico validando que pertenezca al negocio recibido.
     *
     * @param int $employeeId Identificador del empleado.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectEmployee(int $employeeId, int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                e.*,
                ua.user AS user_app_user,
                ua.idUserApp AS user_app_id,
                p.names AS person_names,
                p.lastname AS person_lastname,
                p.email AS person_email,
                ra.name AS role_app_name,
                ra.description AS role_app_description
            FROM employee AS e
            LEFT JOIN user_app AS ua ON ua.idUserApp = e.userapp_id
            LEFT JOIN people AS p ON p.idPeople = ua.people_id
            INNER JOIN role_app AS ra ON ra.idRoleApp = e.rolapp_id
            WHERE e.idEmployee = ?
              AND e.bussines_id = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$employeeId, $businessId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un nuevo empleado en la base de datos.
     *
     * @param array $data Datos del empleado a registrar.
     *
     * @return int
     */
    public function insertEmployee(array $data): int
    {
        $sql = <<<SQL
            INSERT INTO employee
                (bussines_id, userapp_id, rolapp_id, status)
            VALUES
                (?, ?, ?, ?);
        SQL;

        $params = [
            $data['bussines_id'],
            $data['userapp_id'] ?? null,
            $data['rolapp_id'],
            $data['status'],
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza la información de un empleado existente.
     *
     * @param array $data Datos del empleado a actualizar.
     *
     * @return bool
     */
    public function updateEmployee(array $data): bool
    {
        $sql = <<<SQL
            UPDATE employee
            SET
                userapp_id = ?,
                rolapp_id = ?,
                status = ?
            WHERE idEmployee = ?
              AND bussines_id = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['userapp_id'] ?? null,
            $data['rolapp_id'],
            $data['status'],
            $data['idEmployee'],
            $data['bussines_id'],
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Elimina un empleado por su identificador.
     *
     * @param int $employeeId Identificador del empleado.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return bool
     */
    public function deleteEmployee(int $employeeId, int $businessId): bool
    {
        $sql = 'DELETE FROM employee WHERE idEmployee = ? AND bussines_id = ? LIMIT 1;';
        return (bool) $this->delete($sql, [$employeeId, $businessId]);
    }

    /**
     * Obtiene los usuarios de aplicación activos disponibles para el negocio.
     * Incluye todos los usuarios activos que no estén asignados como empleados en este negocio.
     * 
     * @param int $businessId Identificador del negocio activo.
     * @param int|null $excludeEmployeeId ID del empleado a excluir (para actualizaciones, permite incluir el usuario actual).
     *
     * @return array
     */
    public function selectUserApps(int $businessId, ?int $excludeEmployeeId = null): array
    {
        $sql = <<<SQL
            SELECT DISTINCT
                ua.idUserApp,
                ua.user,
                p.names,
                p.lastname,
                p.email
            FROM user_app AS ua
            INNER JOIN people AS p ON p.idPeople = ua.people_id
            WHERE ua.status = 'Activo'
              AND ua.idUserApp NOT IN (
                  SELECT e.userapp_id
                  FROM employee AS e
                  WHERE e.bussines_id = ?
                    AND e.userapp_id IS NOT NULL
        SQL;

        $params = [$businessId];

        if ($excludeEmployeeId !== null) {
            $sql .= ' AND e.idEmployee != ?';
            $params[] = $excludeEmployeeId;
        }

        $sql .= <<<SQL
              )
            ORDER BY p.names, p.lastname ASC;
        SQL;

        return $this->select_all($sql, $params);
    }

    /**
     * Obtiene los roles de aplicación activos asociados al negocio.
     *
     * @param int $businessId Identificador del negocio activo.
     *
     * @return array
     */
    public function selectRoleApps(int $businessId): array
    {
        $sql = <<<SQL
            SELECT
                idRoleApp,
                name,
                description
            FROM role_app
            WHERE business_id = ?
              AND status = 'Activo'
            ORDER BY name ASC;
        SQL;

        return $this->select_all($sql, [$businessId]);
    }

    /**
     * Valida si un usuario de aplicación ya está asignado como empleado en el negocio.
     *
     * @param int $userappId Identificador del usuario de aplicación.
     * @param int $businessId Identificador del negocio activo.
     * @param int|null $excludeEmployeeId ID del empleado a excluir (para actualizaciones).
     *
     * @return array
     */
    public function selectEmployeeByUserapp(int $userappId, int $businessId, ?int $excludeEmployeeId = null): array
    {
        $sql = <<<SQL
            SELECT *
            FROM employee
            WHERE userapp_id = ?
              AND bussines_id = ?
              AND userapp_id IS NOT NULL
        SQL;

        $params = [$userappId, $businessId];

        if ($excludeEmployeeId !== null) {
            $sql .= ' AND idEmployee != ?';
            $params[] = $excludeEmployeeId;
        }

        $sql .= ' LIMIT 1;';

        $result = $this->select($sql, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca una persona por su correo electrónico encriptado.
     *
     * @param string $email Correo electrónico encriptado.
     *
     * @return array
     */
    public function selectPersonByEmail(string $email): array
    {
        $sql = 'SELECT * FROM people WHERE email = ? LIMIT 1;';
        $result = $this->select($sql, [$email]);

        return is_array($result) ? $result : [];
    }

    /**
     * Registra una nueva persona.
     *
     * @param array $data Datos de la persona.
     *
     * @return int
     */
    public function insertPerson(array $data): int
    {
        $sql = <<<SQL
            INSERT INTO people
                (names, lastname, email, date_of_birth, country, telephone_prefix, phone_number)
            VALUES
                (?, ?, ?, NULL, NULL, NULL, NULL);
        SQL;

        $params = [
            $data['names'],
            $data['lastname'],
            $data['email'],
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza la información básica de una persona.
     *
     * @param array $data Datos a actualizar.
     *
     * @return bool
     */
    public function updatePerson(array $data): bool
    {
        $sql = <<<SQL
            UPDATE people
            SET
                names = ?,
                lastname = ?,
                email = ?
            WHERE idPeople = ?
            LIMIT 1;
        SQL;

        $params = [
            $data['names'],
            $data['lastname'],
            $data['email'],
            $data['idPeople'],
        ];

        return (bool) $this->update($sql, $params);
    }

    /**
     * Obtiene el usuario de aplicación asociado a una persona.
     *
     * @param int $peopleId Identificador de la persona.
     *
     * @return array
     */
    public function selectUserAppByPeopleId(int $peopleId): array
    {
        $sql = 'SELECT * FROM user_app WHERE people_id = ? LIMIT 1;';
        $result = $this->select($sql, [$peopleId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca un usuario de aplicación por su nombre de usuario encriptado.
     *
     * @param string $user Nombre de usuario encriptado.
     *
     * @return array
     */
    public function selectUserAppByUser(string $user): array
    {
        $sql = 'SELECT * FROM user_app WHERE user = ? LIMIT 1;';
        $result = $this->select($sql, [$user]);

        return is_array($result) ? $result : [];
    }

    /**
     * Busca un usuario de aplicación por su usuario o por el correo de la persona asociada.
     *
     * @param string $identifier Valor encriptado que representa el usuario o el correo.
     *
     * @return array
     */
    public function selectUserAppByIdentifier(string $identifier): array
    {
        $sql = <<<SQL
            SELECT
                ua.idUserApp,
                ua.user,
                ua.status,
                p.idPeople,
                p.names,
                p.lastname,
                p.email
            FROM user_app AS ua
            INNER JOIN people AS p ON p.idPeople = ua.people_id
            WHERE ua.status = 'Activo'
              AND (ua.user = ? OR p.email = ?)
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$identifier, $identifier]);

        return is_array($result) ? $result : [];
    }

    /**
     * Obtiene un usuario de aplicación y su persona asociada por identificador.
     *
     * @param int $userappId Identificador del usuario de aplicación.
     *
     * @return array
     */
    public function selectUserAppWithPerson(int $userappId): array
    {
        $sql = <<<SQL
            SELECT
                ua.idUserApp,
                ua.user,
                ua.status,
                p.idPeople,
                p.names,
                p.lastname,
                p.email
            FROM user_app AS ua
            INNER JOIN people AS p ON p.idPeople = ua.people_id
            WHERE ua.idUserApp = ?
            LIMIT 1;
        SQL;

        $result = $this->select($sql, [$userappId]);

        return is_array($result) ? $result : [];
    }

    /**
     * Inserta un usuario de aplicación asociado a una persona.
     *
     * @param array $data Datos del usuario.
     *
     * @return int
     */
    public function insertUserApp(array $data): int
    {
        $sql = 'INSERT INTO user_app (user, password, people_id) VALUES (?, ?, ?);';
        $params = [
            $data['user'],
            $data['password'],
            $data['people_id'],
        ];

        return (int) $this->insert($sql, $params);
    }

    /**
     * Actualiza un usuario de aplicación existente.
     *
     * @param array $data Datos a actualizar.
     *
     * @return bool
     */
    public function updateUserApp(array $data): bool
    {
        $sql = 'UPDATE user_app SET user = ?, password = ?, status = ? WHERE idUserApp = ? LIMIT 1;';
        $params = [
            $data['user'],
            $data['password'],
            $data['status'],
            $data['idUserApp'],
        ];

        return (bool) $this->update($sql, $params);
    }
}
