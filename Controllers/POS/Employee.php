<?php

class Employee extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión que contiene la información del usuario POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    public function __construct()
    {
        isSession(1);
        parent::__construct("POS");

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    public function employee()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Empleados',
            'page_description' => 'Gestiona los empleados de tu negocio.',
            'page_container'   => 'Employee',
            'page_view'        => 'employee',
            'page_js_css'      => 'employee',
        ];
        $this->views->getView($this, "employee", $data, "POS");
    }

    /**
     * Devuelve el listado de empleados pertenecientes al negocio activo.
     *
     * @return void
     */
    public function getEmployees(): void
    {
        $businessId = $this->getBusinessId();
        $employees  = $this->model->selectEmployees($businessId);
        $counter    = 1;

        foreach ($employees as $key => $employee) {
            $hasUserApp = !empty($employee['userapp_id']) && !empty($employee['user_app_user']);

            $userAppUser = !empty($employee['user_app_user']) ? decryption($employee['user_app_user']) : "";
            $personEmail = !empty($employee['person_email']) ? decryption($employee['person_email']) : "";
            $fullName = trim(($employee['person_names'] ?? "") . " " . ($employee['person_lastname'] ?? ""));

            $fullNameEscaped = htmlspecialchars($fullName ?: "Sin nombre registrado", ENT_QUOTES, 'UTF-8');
            $userAppUserEscaped = htmlspecialchars($userAppUser ?: "-", ENT_QUOTES, 'UTF-8');
            $roleNameEscaped = htmlspecialchars($employee['role_app_name'] ?? "", ENT_QUOTES, 'UTF-8');
            
            $employees[$key]['cont']           = $counter;
            $employees[$key]['full_name']       = $fullNameEscaped;
            $employees[$key]['user_app_display'] = $hasUserApp
                ? ($userAppUserEscaped . " (" . $fullNameEscaped . ")")
                : '<span class="text-muted">Sin usuario asignado</span>';
            $employees[$key]['role_app_name']   = $roleNameEscaped;
            $employees[$key]['status']         = $employee['status'] === 'Activo'
                ? '<span class="badge badge-success bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge badge-secondary bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $employees[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                . '<button class="btn btn-outline-secondary text-secondary report-employee" data-id="' . (int) $employee['idEmployee'] . '" data-full-name="' . $fullNameEscaped . '" title="Ver reporte del empleado">'
                . '<i class="bi bi-file-earmark-text"></i></button>'
                . '<button class="btn btn-outline-primary text-primary edit-employee" data-id="' . (int) $employee['idEmployee'] . '">'
                . '<i class="bi bi-pencil-square"></i></button>'
                . '<button class="btn btn-outline-danger text-danger delete-employee" data-id="' . (int) $employee['idEmployee'] . '" data-full-name="' . $fullNameEscaped . '" data-token="' . csrf(false) . '">'
                . '<i class="bi bi-trash"></i></button>'
                . '</div>';

            $counter++;
        }

        toJson($employees);
    }

    /**
     * Registra un nuevo empleado asociado al negocio activo, creando o reutilizando sus datos personales.
     *
     * @return void
     */
    public function setEmployee(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $requiredFields = ['txtEmployeeUserappId', 'txtEmployeeRolapp'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
                $this->responseError('Todos los campos obligatorios deben estar completos.');
            }
        }

        $businessId = $this->getBusinessId();
        $rolappId   = (int) ($_POST['txtEmployeeRolapp'] ?? 0);
        $userappId  = (int) ($_POST['txtEmployeeUserappId'] ?? 0);
        $status     = 'Activo';

        if ($rolappId <= 0) {
            $this->responseError('Debes seleccionar un rol de aplicación válido.');
        }

        if ($userappId <= 0) {
            $this->responseError('Debes buscar y seleccionar un usuario válido.');
        }

        // Validar que el rol pertenezca al negocio
        $this->ensureRolappBelongsToBusiness($rolappId, $businessId);

        $userAppData = $this->model->selectUserAppWithPerson($userappId);
        if (empty($userAppData) || ($userAppData['status'] ?? '') !== 'Activo') {
            $this->responseError('El usuario indicado no existe o se encuentra inactivo.');
        }

        $this->ensureUserappAvailability($userappId, $businessId);

        $payload = [
            'bussines_id' => $businessId,
            'userapp_id'  => $userappId,
            'rolapp_id'   => $rolappId,
            'status'      => $status,
        ];

        $inserted = $this->model->insertEmployee($payload);
        if ($inserted <= 0) {
            $this->responseError('No fue posible registrar el empleado, inténtalo nuevamente.');
        }

        $data = [
            'title'  => 'Empleado registrado',
            'message' => 'El empleado se registró correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Obtiene la información de un empleado específico del negocio activo.
     *
     * @return void
     */
    public function getEmployee(): void
    {
        $employeeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($employeeId <= 0) {
            $this->responseError('Identificador de empleado inválido.');
        }

        $businessId = $this->getBusinessId();
        $employee   = $this->model->selectEmployee($employeeId, $businessId);

        if (empty($employee)) {
            $this->responseError('No se encontró el empleado solicitado.');
        }

        $userAppUser = !empty($employee['user_app_user']) ? decryption($employee['user_app_user']) : "";
        $personEmail = !empty($employee['person_email']) ? decryption($employee['person_email']) : "";
        $fullName = trim(($employee['person_names'] ?? "") . " " . ($employee['person_lastname'] ?? ""));

        $data = [
            'status' => true,
            'data'   => [
                'idEmployee'      => (int) $employee['idEmployee'],
                'userapp_id'      => !empty($employee['userapp_id']) ? (int) $employee['userapp_id'] : null,
                'rolapp_id'       => (int) $employee['rolapp_id'],
                'status'          => $employee['status'],
                'names'           => $employee['person_names'] ?? '',
                'lastname'        => $employee['person_lastname'] ?? '',
                'full_name'       => $fullName ?: "Sin datos personales",
                'user_app_user'   => $userAppUser,
                'person_email'    => $personEmail,
                'role_app_name'   => $employee['role_app_name'] ?? '',
                'role_app_description' => $employee['role_app_description'] ?? '',
            ],
        ];

        toJson($data);
    }

    /**
     * Actualiza la información de un empleado existente.
     *
     * @return void
     */
    public function updateEmployee(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $requiredFields = [
            'update_txtEmployeeId',
            'update_txtEmployeeUserappId',
            'update_txtEmployeeRolapp',
            'update_txtEmployeeStatus',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
                $this->responseError('Todos los campos obligatorios deben estar completos.');
            }
        }

        $businessId = $this->getBusinessId();
        $employeeId = (int) ($_POST['update_txtEmployeeId'] ?? 0);
        $userappId  = (int) ($_POST['update_txtEmployeeUserappId'] ?? 0);
        $rolappId   = (int) ($_POST['update_txtEmployeeRolapp'] ?? 0);
        $status     = $_POST['update_txtEmployeeStatus'] === 'Inactivo' ? 'Inactivo' : 'Activo';

        if ($employeeId <= 0) {
            $this->responseError('Identificador de empleado inválido.');
        }

        if ($rolappId <= 0) {
            $this->responseError('Debes seleccionar un rol de aplicación válido.');
        }

        if ($userappId <= 0) {
            $this->responseError('Debes buscar y seleccionar un usuario válido.');
        }

        $currentEmployee = $this->model->selectEmployee($employeeId, $businessId);
        if (empty($currentEmployee)) {
            $this->responseError('El empleado seleccionado no existe o no pertenece a tu negocio.');
        }

        $this->ensureRolappBelongsToBusiness($rolappId, $businessId);

        $userAppData = $this->model->selectUserAppWithPerson($userappId);
        if (empty($userAppData) || ($userAppData['status'] ?? '') !== 'Activo') {
            $this->responseError('El usuario indicado no existe o se encuentra inactivo.');
        }

        $this->ensureUserappAvailability($userappId, $businessId, $employeeId);

        $payload = [
            'idEmployee'  => $employeeId,
            'bussines_id' => $businessId,
            'userapp_id'  => $userappId,
            'rolapp_id'   => $rolappId,
            'status'      => $status,
        ];

        $updated = $this->model->updateEmployee($payload);
        if (!$updated) {
            $this->responseError('No fue posible actualizar el empleado, inténtalo nuevamente.');
        }

        $data = [
            'title'  => 'Actualización exitosa',
            'message' => 'La información del empleado se actualizó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Elimina definitivamente un empleado del negocio activo.
     *
     * @return void
     */
    public function deleteEmployee(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = $this->getUserId();

        $this->validateCsrfToken($input['token'] ?? '', $userId);

        $employeeId = isset($input['id']) ? (int) $input['id'] : 0;
        if ($employeeId <= 0) {
            $this->responseError('Identificador de empleado inválido.');
        }

        $businessId = $this->getBusinessId();
        $employee   = $this->model->selectEmployee($employeeId, $businessId);

        if (empty($employee)) {
            $this->responseError('El empleado seleccionado no existe o no pertenece a tu negocio.');
        }

        $deleted = $this->model->deleteEmployee($employeeId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar el empleado, inténtalo nuevamente.');
        }

        $data = [
            'title'  => 'Empleado eliminado',
            'message' => 'El empleado se eliminó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Devuelve los usuarios de aplicación disponibles para el negocio activo.
     *
     * @return void
     */
    public function getUserApps(): void
    {
        $businessId = $this->getBusinessId();
        $excludeEmployeeId = isset($_GET['exclude_employee_id']) ? (int) $_GET['exclude_employee_id'] : null;
        $data = $this->model->selectUserApps($businessId, $excludeEmployeeId);

        // Desencriptar usuarios para mostrar en el select
        foreach ($data as $key => $value) {
            if (!empty($value["user"])) {
                $data[$key]["user"] = decryption($value["user"]);
            }
            if (!empty($value["email"])) {
                $data[$key]["email"] = decryption($value["email"]);
            }
            // Crear nombre completo para mostrar
            $data[$key]["full_name"] = trim($value["names"] . " " . $value["lastname"]);
        }

        toJson([
            'status' => true,
            'data'   => $data,
        ]);
    }

    /**
     * Busca un usuario de aplicación por correo o nombre de usuario y valida su disponibilidad.
     *
     * @return void
     */
    public function findUserApp(): void
    {
        $identifier = strClean($_GET['identifier'] ?? '');
        $excludeEmployeeId = isset($_GET['exclude_employee_id']) ? (int) $_GET['exclude_employee_id'] : null;

        if (empty($identifier)) {
            $this->responseError('Debes ingresar un usuario o correo para buscar.');
        }

        $normalizedIdentifier = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? strtolower($identifier)
            : $identifier;

        $encryptedIdentifier = encryption($normalizedIdentifier);
        $userAppData = $this->model->selectUserAppByIdentifier($encryptedIdentifier);

        if (empty($userAppData)) {
            $this->responseError('No se encontró un usuario activo con el dato proporcionado.');
        }

        $userappId = (int) $userAppData['idUserApp'];
        $businessId = $this->getBusinessId();

        $this->ensureUserappAvailability($userappId, $businessId, $excludeEmployeeId);

        $data = [
            'status' => true,
            'data'   => [
                'idUserApp' => $userappId,
                'user'      => !empty($userAppData['user']) ? decryption($userAppData['user']) : '',
                'people_id' => (int) ($userAppData['idPeople'] ?? 0),
                'names'     => $userAppData['names'] ?? '',
                'lastname'  => $userAppData['lastname'] ?? '',
                'email'     => !empty($userAppData['email']) ? decryption($userAppData['email']) : '',
            ],
        ];

        toJson($data);
    }

    /**
     * Devuelve sugerencias predictivas de usuarios activos disponibles según el dato ingresado.
     *
     * @return void
     */
    public function suggestUserApps(): void
    {
        $query = strtolower(strClean($_GET['q'] ?? ''));
        $excludeEmployeeId = isset($_GET['exclude_employee_id']) ? (int) $_GET['exclude_employee_id'] : null;

        if (strlen($query) < 2) {
            $this->responseError('Ingresa al menos 2 caracteres para obtener sugerencias.');
        }

        $businessId = $this->getBusinessId();
        $candidates = $this->model->selectUserApps($businessId, $excludeEmployeeId);

        $suggestions = [];

        foreach ($candidates as $candidate) {
            $user  = !empty($candidate['user']) ? decryption($candidate['user']) : '';
            $email = !empty($candidate['email']) ? decryption($candidate['email']) : '';
            $fullName = trim(($candidate['names'] ?? '') . ' ' . ($candidate['lastname'] ?? ''));

            if (
                stripos($user, $query) !== false
                || stripos($email, $query) !== false
            ) {
                $suggestions[] = [
                    'idUserApp' => (int) $candidate['idUserApp'],
                    'user'      => $user,
                    'email'     => $email,
                    'full_name' => $fullName,
                ];
            }

            if (count($suggestions) >= 10) {
                break;
            }
        }

        toJson([
            'status' => true,
            'data'   => $suggestions,
        ]);
    }

    /**
     * Devuelve los roles de aplicación disponibles para el negocio activo.
     *
     * @return void
     */
    public function getRoleApps(): void
    {
        $businessId = $this->getBusinessId();
        $data = $this->model->selectRoleApps($businessId);

        toJson([
            'status' => true,
            'data'   => $data,
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
     * Verifica que el usuario de aplicación esté disponible para el negocio activo.
     * Un usuario está disponible si está activo y no está ya asignado como empleado.
     *
     * @param int $userappId Identificador del usuario de aplicación.
     * @param int $businessId Identificador del negocio activo.
     * @param int|null $excludeEmployeeId ID del empleado a excluir (para actualizaciones).
     *
     * @return void
     */
    private function ensureUserappBelongsToBusiness(int $userappId, int $businessId, ?int $excludeEmployeeId = null): void
    {
        // Obtener la lista de usuarios disponibles para el negocio activo
        $availableUsers = $this->model->selectUserApps($businessId, $excludeEmployeeId);
        $found = false;
        
        foreach ($availableUsers as $user) {
            if ((int) $user['idUserApp'] === $userappId) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $this->responseError('El usuario de aplicación seleccionado no está disponible para tu negocio o ya está asignado como empleado.');
        }
    }

    /**
     * Verifica que el usuario no esté ya asignado como empleado en el negocio.
     *
     * @param int      $userappId          Identificador del usuario de aplicación.
     * @param int      $businessId         Identificador del negocio activo.
     * @param int|null $excludeEmployeeId  ID del empleado a excluir (para ediciones).
     *
     * @return void
     */
    private function ensureUserappAvailability(int $userappId, int $businessId, ?int $excludeEmployeeId = null): void
    {
        $existingEmployee = $this->model->selectEmployeeByUserapp($userappId, $businessId, $excludeEmployeeId);

        if (!empty($existingEmployee)) {
            $this->responseError('El usuario ya está asignado como empleado en este negocio.');
        }
    }

    /**
     * Verifica que el rol de aplicación pertenezca al negocio activo.
     *
     * @param int $rolappId Identificador del rol de aplicación.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return void
     */
    private function ensureRolappBelongsToBusiness(int $rolappId, int $businessId): void
    {
        $roleApps = $this->model->selectRoleApps($businessId);
        $found = false;
        foreach ($roleApps as $roleApp) {
            if ((int) $roleApp['idRoleApp'] === $rolappId) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->responseError('El rol de aplicación seleccionado no pertenece a tu negocio o está inactivo.');
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
