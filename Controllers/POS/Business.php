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
        $businessOwner = $this->model->selectBusinessesByUserOwner($userId);
        //adicionamos un atributo que indique es el dueño de este negocio
        foreach ($businessOwner as $index => $business) {
            $businessOwner[$index]['is_owner'] = true;
        }
        $businessEmploye = $this->model->selectBusinessesByUserEmployee($userId);
        //adicionamos un atributo que indique es el dueño de este negocio
        foreach ($businessEmploye as $index => $business) {
            $businessEmploye[$index]['is_owner'] = false;
        }
        $businesses = array_merge($businessOwner, $businessEmploye);
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
        validateFields(['businessId', 'owner']);
        $userId = $this->getUserId();
        $businessId = $_POST['businessId'];
        $owner = (bool) ($_POST['owner'] === 'true' ? true : false);
        if ($businessId <= 0) {
            $this->responseError('Identificador de negocio inválido.');
        }
        //validamos que si usuario es Zdueño o empleado
        if ($owner) {
            $business = $this->model->selectBusinessByIdForUser($businessId, $userId);
            $ownerText = 'Dueño';
        } else if (!$owner) {
            $business = $this->model->selectBusinessByIdUserEmploye($businessId, $userId);
            $ownerText = 'Empleado';
        }
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
                        ¡Negocio cambiado con éxito! Ahora estás gestionando como <strong class="text-danger">$ownerText</strong> el negocio <strong>{$business['business']}</strong>.                   
                        <span class="badge bg-danger text-white badge-pill badge-sm">                      
                            Cambiando, espere un momento...
                        </span>
                        <span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span>
                        <span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span>
                        <span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span><span class="spinner-grow spinner-grow-sm bg-danger"></span>
                    </div>
                </div>
            HTML,
            'timer' => 2000,
            'data'    => $business,
            'reload' => true
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
