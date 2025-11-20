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

        $requiredFields = ['txtEmployeeNames', 'txtEmployeeLastname', 'txtEmployeeEmail', 'txtEmployeeRolapp'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
                $this->responseError('Todos los campos obligatorios deben estar completos.');
            }
        }

        $businessId = $this->getBusinessId();
        $rolappId   = (int) ($_POST['txtEmployeeRolapp'] ?? 0);
        $names      = strtoupper(strClean($_POST['txtEmployeeNames'] ?? ''));
        $lastname   = strtoupper(strClean($_POST['txtEmployeeLastname'] ?? ''));
        $email      = strtolower(strClean($_POST['txtEmployeeEmail'] ?? ''));
        $status     = 'Activo';
        $createUser = isset($_POST['chkEmployeeCreateUser']) && $_POST['chkEmployeeCreateUser'] === 'on';
        $username   = strClean($_POST['txtEmployeeUser'] ?? '');
        $password   = $_POST['txtEmployeePassword'] ?? '';

        if ($rolappId <= 0) {
            $this->responseError('Debes seleccionar un rol de aplicación válido.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responseError('Debes ingresar un correo electrónico válido.');
        }

        // Validar que el rol pertenezca al negocio
        $this->ensureRolappBelongsToBusiness($rolappId, $businessId);

        $encryptedEmail = encryption($email);
        $person         = $this->model->selectPersonByEmail($encryptedEmail);
        $personId       = $person['idPeople'] ?? 0;

        if ($personId > 0) {
            $this->model->updatePerson([
                'idPeople' => $personId,
                'names'    => $names,
                'lastname' => $lastname,
                'email'    => $encryptedEmail,
            ]);
        } else {
            $personId = $this->model->insertPerson([
                'names'    => $names,
                'lastname' => $lastname,
                'email'    => $encryptedEmail,
            ]);
        }

        if ($personId <= 0) {
            $this->responseError('No fue posible registrar los datos de la persona.');
        }

        $userappId = null;

        if ($createUser) {
            if (empty($username) || empty($password)) {
                $this->responseError('Para crear el usuario debes ingresar un nombre de usuario y una contraseña.');
            }

            if (strlen($password) < 8) {
                $this->responseError('La contraseña debe contener al menos 8 caracteres.');
            }

            $encryptedUsername = encryption($username);
            $existingPersonUser = $this->model->selectUserAppByPeopleId($personId);
            $existingUser      = $this->model->selectUserAppByUser($encryptedUsername);
            if (!empty($existingPersonUser)) {
                $userappId = (int) $existingPersonUser['idUserApp'];

                if (!empty($existingUser) && (int) $existingUser['idUserApp'] !== $userappId) {
                    $this->responseError('El nombre de usuario ya se encuentra en uso.');
                }

                $this->model->updateUserApp([
                    'idUserApp' => $userappId,
                    'user'      => $encryptedUsername,
                    'password'  => encryption($password),
                    'status'    => 'Activo',
                ]);
            } else {
                if (!empty($existingUser)) {
                    $this->responseError('El nombre de usuario ya se encuentra en uso.');
                }

                $userappId = $this->model->insertUserApp([
                    'user'      => $encryptedUsername,
                    'password'  => encryption($password),
                    'people_id' => $personId,
                ]);
            }

            if ($userappId <= 0) {
                $this->responseError('No fue posible crear el usuario para el empleado.');
            }

            $this->ensureUserappBelongsToBusiness($userappId, $businessId);
        }

        $payload = [
            'bussines_id' => $businessId,
            'userapp_id'  => $userappId,
            'rolapp_id'   => $rolappId,
            'people_id'   => $personId,
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
                'people_id'       => !empty($employee['people_id']) ? (int) $employee['people_id'] : null,
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
            'update_txtEmployeeNames',
            'update_txtEmployeeLastname',
            'update_txtEmployeeEmail',
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
        $rolappId   = (int) ($_POST['update_txtEmployeeRolapp'] ?? 0);
        $status     = $_POST['update_txtEmployeeStatus'] === 'Inactivo' ? 'Inactivo' : 'Activo';
        $names      = strtoupper(strClean($_POST['update_txtEmployeeNames'] ?? ''));
        $lastname   = strtoupper(strClean($_POST['update_txtEmployeeLastname'] ?? ''));
        $email      = strtolower(strClean($_POST['update_txtEmployeeEmail'] ?? ''));
        $createUser = isset($_POST['update_chkEmployeeCreateUser']) && $_POST['update_chkEmployeeCreateUser'] === 'on';
        $username   = strClean($_POST['update_txtEmployeeUser'] ?? '');
        $password   = $_POST['update_txtEmployeePassword'] ?? '';

        if ($employeeId <= 0) {
            $this->responseError('Identificador de empleado inválido.');
        }

        if ($rolappId <= 0) {
            $this->responseError('Debes seleccionar un rol de aplicación válido.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responseError('Debes ingresar un correo electrónico válido.');
        }

        $currentEmployee = $this->model->selectEmployee($employeeId, $businessId);
        if (empty($currentEmployee)) {
            $this->responseError('El empleado seleccionado no existe o no pertenece a tu negocio.');
        }

        $this->ensureRolappBelongsToBusiness($rolappId, $businessId);

        $encryptedEmail = encryption($email);
        $person         = $this->model->selectPersonByEmail($encryptedEmail);
        $personId       = $person['idPeople'] ?? ($currentEmployee['people_id'] ?? 0);

        if ($personId > 0) {
            $this->model->updatePerson([
                'idPeople' => $personId,
                'names'    => $names,
                'lastname' => $lastname,
                'email'    => $encryptedEmail,
            ]);
        } else {
            $personId = $this->model->insertPerson([
                'names'    => $names,
                'lastname' => $lastname,
                'email'    => $encryptedEmail,
            ]);
        }

        if ($personId <= 0) {
            $this->responseError('No fue posible actualizar los datos de la persona.');
        }

        $userappId = null;
        if ($createUser) {
            if (empty($username)) {
                $this->responseError('El nombre de usuario es obligatorio para crear el acceso.');
            }

            $encryptedUsername = encryption($username);
            $existingUser      = $this->model->selectUserAppByUser($encryptedUsername);
            $currentUserId     = $currentEmployee['userapp_id'] ?? null;

            if (!empty($existingUser) && (int) $existingUser['idUserApp'] !== (int) $currentUserId) {
                $this->responseError('El nombre de usuario ya está en uso por otro registro.');
            }

            $personUser = $this->model->selectUserAppByPeopleId($personId);

            if (!empty($personUser)) {
                $userappId = (int) $personUser['idUserApp'];
                $passwordToUse = !empty($password) ? encryption($password) : $personUser['password'];

                $this->model->updateUserApp([
                    'idUserApp' => $userappId,
                    'user'      => $encryptedUsername,
                    'password'  => $passwordToUse,
                    'status'    => 'Activo',
                ]);
            } else {
                if (empty($password)) {
                    $this->responseError('Debes ingresar una contraseña para crear el usuario.');
                }

                if (strlen($password) < 8) {
                    $this->responseError('La contraseña debe contener al menos 8 caracteres.');
                }

                $userappId = $this->model->insertUserApp([
                    'user'      => $encryptedUsername,
                    'password'  => encryption($password),
                    'people_id' => $personId,
                ]);
            }

            if ($userappId <= 0) {
                $this->responseError('No fue posible actualizar o crear el usuario de acceso.');
            }

            $this->ensureUserappBelongsToBusiness($userappId, $businessId, $employeeId);
        }

        $payload = [
            'idEmployee'  => $employeeId,
            'bussines_id' => $businessId,
            'userapp_id'  => $userappId,
            'rolapp_id'   => $rolappId,
            'people_id'   => $personId,
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
