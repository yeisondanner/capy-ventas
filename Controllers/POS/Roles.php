<?php

class Roles extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión con la información del usuario POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Renderiza la vista de gestión de roles de la aplicación POS.
     *
     * @return void
     */
    public function roles(): void
    {
        $data = [
            'page_id'          => 6,
            'page_title'       => 'Roles de aplicación',
            'page_description' => 'Administra los roles disponibles para asignar a tus empleados.',
            'page_container'   => 'Roles',
            'page_view'        => 'roles',
            'page_js_css'      => 'roles',
        ];

        $this->views->getView($this, 'roles', $data, 'POS');
    }

    /**
     * Obtiene el listado de roles registrados para el negocio activo.
     *
     * @return void
     */
    public function getRoles(): void
    {
        $businessId = $this->getBusinessId();
        $roles      = $this->model->selectRoles($businessId);
        $counter    = 1;

        foreach ($roles as $key => $role) {
            $name        = htmlspecialchars((string) ($role['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars((string) ($role['description'] ?? 'Sin descripción'), ENT_QUOTES, 'UTF-8');
            $status      = (string) ($role['status'] ?? 'Inactivo');
            $updatedAt   = !empty($role['update_date']) ? dateFormat($role['update_date']) : '-';

            $roles[$key]['cont']        = $counter;
            $roles[$key]['name']        = $name;
            $roles[$key]['description'] = $description;
            $roles[$key]['status_text'] = $status;
            $roles[$key]['status']      = $status === 'Activo'
                ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';
            $roles[$key]['updated_at']  = $updatedAt;

            $roles[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                . '<button class="btn btn-outline-secondary text-secondary report-role" data-id="' . (int) $role['idRoleApp'] . '"'
                . ' data-name="' . $name . '" data-description="' . $description . '" data-status="' . $status . '"'
                . ' data-updated="' . $updatedAt . '"><i class="bi bi-eye"></i></button>'
                . '<button class="btn btn-outline-primary text-primary edit-role" data-id="' . (int) $role['idRoleApp'] . '">'
                . '<i class="bi bi-pencil-square"></i></button>'
                . '<button class="btn btn-outline-danger text-danger delete-role" data-id="' . (int) $role['idRoleApp'] . '"'
                . ' data-name="' . $name . '" data-token="' . csrf(false) . '"><i class="bi bi-trash"></i></button>'
                . '</div>';

            $counter++;
        }

        toJson($roles);
    }

    /**
     * Registra un nuevo rol de aplicación para el negocio activo.
     *
     * @return void
     */
    public function setRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $businessId  = $this->getBusinessId();
        $name        = ucwords(strClean($_POST['txtRoleAppName'] ?? ''));
        $description = strClean($_POST['txtRoleAppDescription'] ?? '');

        if ($name === '') {
            $this->responseError('El nombre del rol es obligatorio.');
        }

        $existing = $this->model->selectRoleByName($businessId, $name);
        if (!empty($existing)) {
            $this->responseError('Ya existe un rol con el mismo nombre en tu negocio.');
        }

        $payload = [
            'name'        => $name,
            'description' => $description,
            'status'      => 'Activo',
        ];

        $inserted = $this->model->insertRole($businessId, $payload);
        if ($inserted <= 0) {
            $this->responseError('No fue posible registrar el rol, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Rol registrado',
            'message' => 'El rol se registró correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Devuelve los datos de un rol específico.
     *
     * @return void
     */
    public function getRole(): void
    {
        $roleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($roleId <= 0) {
            $this->responseError('Identificador de rol inválido.');
        }

        $businessId = $this->getBusinessId();
        $role       = $this->model->selectRole($roleId, $businessId);

        if (empty($role)) {
            $this->responseError('No se encontró el rol solicitado.');
        }

        toJson([
            'status' => true,
            'data'   => [
                'idRoleApp'   => (int) $role['idRoleApp'],
                'name'        => $role['name'],
                'description' => $role['description'] ?? '',
                'status'      => $role['status'],
            ],
        ]);
    }

    /**
     * Actualiza la información de un rol existente.
     *
     * @return void
     */
    public function updateRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $roleId      = (int) ($_POST['roleId'] ?? 0);
        $name        = ucwords(strClean($_POST['txtRoleAppName'] ?? ''));
        $description = strClean($_POST['txtRoleAppDescription'] ?? '');
        $status      = $_POST['txtRoleAppStatus'] === 'Inactivo' ? 'Inactivo' : 'Activo';

        if ($roleId <= 0) {
            $this->responseError('No se pudo identificar el rol seleccionado.');
        }

        if ($name === '') {
            $this->responseError('El nombre del rol es obligatorio.');
        }

        $businessId = $this->getBusinessId();
        $role       = $this->model->selectRole($roleId, $businessId);

        if (empty($role)) {
            $this->responseError('El rol seleccionado no existe o no pertenece a tu negocio.');
        }

        $existing = $this->model->selectRoleByName($businessId, $name, $roleId);
        if (!empty($existing)) {
            $this->responseError('Ya existe otro rol con el mismo nombre en tu negocio.');
        }

        $updated = $this->model->updateRole($roleId, $businessId, [
            'name'        => $name,
            'description' => $description,
            'status'      => $status,
        ]);

        if (!$updated) {
            $this->responseError('No fue posible actualizar el rol, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Rol actualizado',
            'message' => 'Los datos del rol se guardaron correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Elimina o desactiva un rol según sus asociaciones.
     *
     * @return void
     */
    public function deleteRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $this->responseError('Solicitud inválida.');
        }

        $roleId = (int) ($input['id'] ?? 0);
        $token  = (string) ($input['token'] ?? '');

        if ($roleId <= 0) {
            $this->responseError('No se pudo identificar el rol seleccionado.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($token, $userId);

        $businessId = $this->getBusinessId();
        $role       = $this->model->selectRole($roleId, $businessId);

        if (empty($role)) {
            $this->responseError('El rol seleccionado no existe o no pertenece a tu negocio.');
        }

        $employees   = $this->model->countEmployeesByRole($roleId, $businessId);
        $permissions = $this->model->countPermissionsByRole($roleId);

        if ($employees > 0 || $permissions > 0) {
            $deactivated = $this->model->deactivateRole($roleId, $businessId);
            if (!$deactivated) {
                $this->responseError('No fue posible desactivar el rol, inténtalo nuevamente.');
            }

            toJson([
                'title'   => 'Rol desactivado',
                'message' => 'El rol tiene asociaciones activas, por lo que se marcó como inactivo y dejará de mostrarse en los selectores.',
                'type'    => 'info',
                'icon'    => 'info',
                'status'  => true,
            ]);
        }

        $deleted = $this->model->deleteRole($roleId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar el rol, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Rol eliminado',
            'message' => 'El rol se eliminó correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Obtiene el identificador del negocio activo desde la sesión.
     *
     * @return int
     */
    private function getBusinessId(): int
    {
        if (!isset($_SESSION[$this->nameVarBusiness]['idBusiness'])) {
            $this->responseError('No se encontró el negocio activo en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarBusiness]['idBusiness'];
    }

    /**
     * Obtiene el identificador del usuario POS autenticado.
     *
     * @return int
     */
    private function getUserId(): int
    {
        if (!isset($_SESSION[$this->nameVarLoginInfo]['idUser'])) {
            $this->responseError('No se encontró información del usuario en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarLoginInfo]['idUser'];
    }

    /**
     * Valida el token CSRF proporcionado por el cliente.
     *
     * @param string $token  Token recibido.
     * @param int    $userId Identificador del usuario autenticado.
     *
     * @return void
     */
    private function validateCsrfToken(string $token, int $userId): void
    {
        if (empty($token) || empty($_SESSION['data_token']['token'])) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }

        $sessionToken = (string) $_SESSION['data_token']['token'];
        if (!hash_equals($sessionToken, (string) $token)) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }
    }

    /**
     * Envía una respuesta de error estándar en formato JSON y finaliza la ejecución.
     *
     * @param string $message Mensaje descriptivo del error.
     *
     * @return void
     */
    private function responseError(string $message): void
    {
        $data = [
            'title'  => 'Ocurrió un error',
            'message' => $message,
            'type'   => 'error',
            'icon'   => 'error',
            'status' => false,
        ];

        toJson($data);
    }
}
