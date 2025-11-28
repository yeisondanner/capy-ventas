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
            'page_js_css'      => ['roles', 'roles_api'],
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
                . '<button class="btn btn-secondary report-role" data-id="' . (int) $role['idRoleApp'] . '"'
                . ' data-name="' . $name . '" data-description="' . $description . '" data-status="' . $status . '"'
                . ' data-updated="' . $updatedAt . '"><i class="bi bi-eye"></i></button>'
                . '<button class="btn btn-warning update_role" data-id="' . (int) $role['idRoleApp'] . '">'
                . '<i class="bi bi-pencil-square"></i></button>'
                . '<button class="btn btn-danger delete-role" data-id="' . (int) $role['idRoleApp'] . '"'
                . ' data-name="' . $name . '" data-token="' . csrf(false) . '"><i class="bi bi-trash"></i></button>'
                . '</div>';

            $counter++;
        }

        toJson([
            "data" => $roles
        ]);
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

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $businessId  = $this->getBusinessId();
        $name    = strClean($data['name']);
        $description = ucwords(strClean($data['description']));
        $permissions = $data['permissions'];

        // TODO: Funcion no se encuenta en uso
        // $userId = $this->getUserId();
        // $this->validateCsrfToken($_POST['token'] ?? '', $userId);

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

        // TODO: Insertamos los permisos de la interfaces
        foreach ($permissions as $key => $value) {
            if (!empty($value)) {
                $create = in_array('create', $value) ? 1 : 0;
                $update = in_array('update', $value) ? 1 : 0;
                $read = in_array('read', $value) ? 1 : 0;
                $delete = in_array('delete', $value) ? 1 : 0;
                $this->model->setPermission($key, $inserted, $create, $read, $update, $delete);
            }
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

        $permissions = $this->model->getPermissions($role['idRoleApp']);
        $interface_permissions = $this->selectPermissions();

        $auxInterfacesPermissions = array();
        foreach ($interface_permissions as $key => $value) {
            $auxInterfacesPermissions[$value['plan_interface_id']] = [
                "create" => $value["create"],
                "read" => $value["read"],
                "update" => $value["update"],
                "delete" => $value["delete"],
            ];
        }

        toJson([
            'status' => true,
            'message' => 'Lista de interfaces con sus permisos',
            'type' => 'success',
            'data' => [
                "role" => [
                    "idRoleApp" => $role["idRoleApp"],
                    "name" => $role["name"],
                    "description" => $role["description"],
                ],
                "permissions_interface" => $interface_permissions,
                "permissions_app" => $permissions,
            ]
        ]);
    }

    public function getPermissions(): void
    {
        $interface_permissions = $this->selectPermissions();
        toJson([
            'status' => true,
            'message' => 'Lista de interfaces con sus permisos',
            'type' => 'success',
            'data' => $interface_permissions
        ]);
    }

    public function selectPermissions()
    {
        // TODO: Consultamos el ID del negocio
        $businessId = $this->getBusinessId();
        // TODO: Concultamos la suscripcion inscrita
        $business = $this->model->getBusiness($businessId);
        if (!$business) {
            toJson([
                'status' => false,
                'message' => 'No tiene ningun negocio activo.',
                'type' => 'error',
                'data' => [],
            ]);
        }
        // TODO: Consultamos la suscripcion
        $suscription = $this->model->getSuscription($business['userapp_id']);
        if (!$suscription) {
            toJson([
                'status' => false,
                'message' => 'Este usuario no tiene ninguna suscription activa.',
                'type' => 'error',
                'data' => [],
            ]);
        }
        // TODO: Consultamos todas la interfaces
        $interfaces = $this->model->getInterfaces();
        if (!$interfaces) {
            toJson([
                'status' => false,
                'message' => 'No hay ninguna interfaz activa.',
                'type' => 'error',
                'data' => [],
            ]);
        }
        $auxInterfaces = [];
        foreach ($interfaces as $value) {
            $auxInterfaces[$value['idInterface']] = $value['name'];
        }
        // TODO: Consultamos las inferfaces segun el plan
        $interfaces_plan = $this->model->getInterfacesByPlan($suscription['plan_id']);
        if (!$interfaces_plan) {
            toJson([
                'status' => false,
                'message' => 'Este plan no cuenta con ninguna interfaz vinculada.',
                'type' => 'error',
                'data' => [],
            ]);
        }
        foreach ($interfaces_plan as $key => $value) {
            $interfaces_plan[$key]['interface_name'] = $auxInterfaces[$value['interface_id']];
        }

        return $interfaces_plan;
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
