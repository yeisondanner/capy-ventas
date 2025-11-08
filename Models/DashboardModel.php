<?php
class DashboardModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Metodo que se encarga de obtener los datos de los usuarios activos
     */
    public function select_count_users()
    {
        $query = "SELECT COUNT(*) AS CantidadUsuariosActivos FROM tb_user AS tbu WHERE tbu.u_status='Activo';";
        $request = $this->select($query);
        return $request;

    }
    /**
     * Metodo que se encarga de obtener los datos de los roles
     */
    public function select_count_roles()
    {
        $query = "SELECT COUNT(*) AS CantidadRoles FROM tb_role AS tbr WHERE tbr.r_status='Activo';";
        $request = $this->select($query);
        return $request;
    }

    /**
     * Obtiene el resumen de almacenamiento del usuario autenticado.
     *
     * Recupera el límite configurado en la cuenta (tb_user.u_space_limit) y el
     * espacio utilizado en archivos (suma de tb_file.f_size). Cuando el límite
     * es 0 se considera almacenamiento ilimitado.
     *
     * @param int $userId Identificador del usuario autenticado.
     *
     * @return array<string, float|int|bool> Arreglo asociativo con las claves:
     *                                       - limit_gb (int)
     *                                       - used_gb (float)
     *                                       - available_gb (float)
     *                                       - is_unlimited (bool)
     */
    public function select_user_storage_overview(int $userId): array
    {
        $cleanUserId = (int) strClean($userId);

        $limitQuery = "SELECT u_space_limit FROM tb_user WHERE idUser = ? LIMIT 1;";
        $limitRow = $this->select($limitQuery, [$cleanUserId]);
        $spaceLimit = isset($limitRow['u_space_limit']) ? (int) $limitRow['u_space_limit'] : 0;

        $usedQuery = "SELECT COALESCE(SUM(tbf.f_size), 0) AS space_used FROM tb_file AS tbf WHERE tbf.user_id = ?;";
        $usedRow = $this->select($usedQuery, [$cleanUserId]);
        $spaceUsedBytes = isset($usedRow['space_used']) ? (float) $usedRow['space_used'] : 0.0;
        $convertedUsed = valConvert($spaceUsedBytes);
        $spaceUsedGb = (float) ($convertedUsed['GB'] ?? 0.0);

        if ($spaceLimit === 0) {
            return [
                'limit_gb'     => 0,
                'used_gb'      => $spaceUsedGb,
                'available_gb' => 0.0,
                'is_unlimited' => true,
            ];
        }

        $spaceAvailableGb = max($spaceLimit - $spaceUsedGb, 0.0);

        return [
            'limit_gb'     => $spaceLimit,
            'used_gb'      => $spaceUsedGb,
            'available_gb' => $spaceAvailableGb,
            'is_unlimited' => false,
        ];
    }

}
