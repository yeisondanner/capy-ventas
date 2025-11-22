<?php

class Business extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión con los datos del usuario POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness  = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Devuelve los negocios registrados por el usuario autenticado.
     *
     * @return void
     */
    public function getBusinesses(): void
    {
        $userId         = $this->getUserId();
        $activeBusiness = isset($_SESSION[$this->nameVarBusiness]['idBusiness'])
            ? (int) $_SESSION[$this->nameVarBusiness]['idBusiness']
            : null;

        $businesses = $this->model->selectBusinessesByUser($userId);

        foreach ($businesses as $index => $business) {
            $businesses[$index]['business'] = htmlspecialchars($business['business'] ?? '', ENT_QUOTES, 'UTF-8');
            $businesses[$index]['category'] = htmlspecialchars($business['category'] ?? '', ENT_QUOTES, 'UTF-8');
            $businesses[$index]['is_active'] = $activeBusiness === (int) ($business['idBusiness'] ?? 0);
        }

        toJson([
            'status' => true,
            'data'   => $businesses,
        ]);
    }

    /**
     * Obtiene los tipos de negocio activos para el formulario.
     *
     * @return void
     */
    public function getBusinessTypes(): void
    {
        $types = $this->model->selectBusinessTypes();

        foreach ($types as $index => $type) {
            $types[$index]['name'] = htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8');
        }

        toJson([
            'status' => true,
            'data'   => $types,
        ]);
    }

    /**
     * Registra un nuevo negocio asociado al usuario actual.
     *
     * @return void
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        isCsrf("", 1);
        validateFields(['businessType', 'businessName', 'businessDocument', 'businessPhone', 'businessTelephonePrefix', 'businessEmail']);
        $userId = $this->getUserId();

        $typebusinessId  = (int) $_POST['businessType'];
        $name            = strClean($_POST['businessName']);
        $documentNumber  = strClean($_POST['businessDocument']);
        $phoneNumber     = strClean($_POST['businessPhone']);
        $telephonePrefix = strClean($_POST['businessTelephonePrefix']);
        $email           = strClean($_POST['businessEmail']);
        $direction       = strClean($_POST['businessDirection']);
        $city            = strClean($_POST['businessCity']);
        $country         = strClean($_POST['businessCountry']);
        validateFieldsEmpty([
            'TIPO DE NEGOCIO' => $typebusinessId,
            'NOMBRE' => $name,
            'DOCUMENTO' => $documentNumber,
            'TELEFONO' => $phoneNumber,
            'PREFIX' => $telephonePrefix,
            'CORREO' => $email,
        ]);

        if ($this->model->findBusinessByDocument($documentNumber, $userId)) {
            $this->responseError('Ya registraste un negocio con el mismo número de documento.');
        }
        //preparamos los datos para insertarlos en la base de datos
        $data = [
            'typebusiness_id'  => $typebusinessId,
            'name'             => $name,
            'direction'        => $direction,
            'city'             => $city,
            'document_number'  => $documentNumber,
            'phone_number'     => $phoneNumber,
            'country'          => $country,
            'telephone_prefix' => $telephonePrefix,
            'email'            => $email,
        ];

        $businessId = $this->model->insertBusiness($data, $userId);

        if (empty($businessId)) {
            $this->responseError('No se pudo registrar el negocio, intenta nuevamente.');
        }
        //insertamos los datos por defecto
        $this->model->insertDefaultData((int) $businessId);

        $newBusiness = $this->model->selectBusinessByIdForUser((int) $businessId, $userId);
        if ($newBusiness) {
            $_SESSION[$this->nameVarBusiness] = $newBusiness;
        }
        toJson([
            'status'  => true,
            'icon'    => 'success',
            'title'   => 'Negocio creado',
            'message' => 'El negocio se registró correctamente.',
            'data'    => $newBusiness,
        ]);
    }

    /**
     * Define un negocio como activo para la sesión actual.
     *
     * @return void
     */
    public function setActiveBusiness(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId     = $this->getUserId();
        $businessId = isset($_POST['businessId']) ? (int) $_POST['businessId'] : 0;
        $token      = isset($_POST['token']) ? (string) $_POST['token'] : '';

        $this->validateCsrfToken($token, $userId);

        if ($businessId <= 0) {
            $this->responseError('Identificador de negocio inválido.');
        }

        $business = $this->model->selectBusinessByIdForUser($businessId, $userId);
        if (!$business) {
            $this->responseError('El negocio seleccionado no pertenece a tu cuenta.');
        }

        $_SESSION[$this->nameVarBusiness] = $business;

        toJson([
            'status'  => true,
            'title'   => 'Negocio seleccionado',
            'message' => $business['business'] . ' ha sido seleccionado como el negocio actual.',
            'html'    => <<<HTML
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>
                        ¡Negocio cambiado con éxito! Ahora estás gestionando <strong>{$business['business']}</strong>.
                    </div>
                </div>
            HTML,
            'timer' => 6000,
            'data'    => $business,
        ]);
    }

    /**
     * Obtiene el identificador del usuario autenticado en POS.
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
     * Valida el token CSRF recibido.
     *
     * @param string $token  Token recibido en la solicitud.
     * @param int    $userId Identificador del usuario autenticado.
     * @return void
     */
    private function validateCsrfToken(string $token, int $userId): void
    {
        if (empty($token) || empty($_SESSION['data_token']['token'])) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }

        $sessionToken = (string) $_SESSION['data_token']['token'];
        if (!hash_equals($sessionToken, $token)) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }
    }

    /**
     * Envía una respuesta de error estándar en formato JSON y finaliza la ejecución.
     *
     * @param string $message Mensaje descriptivo del error.
     * @return void
     */
    private function responseError(string $message): void
    {
        toJson([
            'title'   => 'Ocurrió un error',
            'message' => $message,
            'type'    => 'error',
            'icon'    => 'error',
            'status'  => false,
        ]);
    }
}
