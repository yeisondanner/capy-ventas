<?php

class Customers extends Controllers
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

    /**
     * Clave normalizada del cliente protegido por defecto.
     *
     * @var string|null
     */
    private ?string $protectedCustomerKey = null;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Renderiza la vista principal de gestión de clientes.
     *
     * @return void
     */
    public function customers(): void
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Gestión de clientes',
            'page_description' => 'Administra los clientes registrados en tu negocio.',
            'page_container'   => 'Customers',
            'page_view'        => 'customers',
            'page_js_css'      => 'customers',
            'document_types'   => $this->model->selectDocumentTypes(),
        ];

        $this->views->getView($this, 'customers', $data, 'POS');
    }

    /**
     * Devuelve el listado de clientes pertenecientes al negocio actual.
     *
     * @return void
     */
    public function getCustomers(): void
    {
        $businessId = $this->getBusinessId();
        $customers  = $this->model->selectCustomers($businessId);
        $counter    = 1;

        foreach ($customers as $key => $customer) {
            $rawName          = (string) ($customer['fullname'] ?? '');
            $rawDocumentType  = (string) ($customer['document_type'] ?? '');
            $rawDocument      = (string) ($customer['document_number'] ?? '');
            $rawPhone         = (string) ($customer['phone_number'] ?? '');
            $rawEmail         = (string) ($customer['email'] ?? '');
            $rawAddress       = (string) ($customer['direction'] ?? '');
            $rawStatus        = (string) ($customer['status'] ?? 'Activo');

            $name        = htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8');
            $document    = $rawDocument !== ''
                ? htmlspecialchars($rawDocument, ENT_QUOTES, 'UTF-8')
                : 'Sin documento';
            $documentType = $rawDocumentType !== ''
                ? htmlspecialchars($rawDocumentType, ENT_QUOTES, 'UTF-8')
                : 'Sin tipo';
            $phone       = $rawPhone !== ''
                ? htmlspecialchars($rawPhone, ENT_QUOTES, 'UTF-8')
                : 'Sin teléfono';
            $email       = $rawEmail !== ''
                ? htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8')
                : 'Sin correo';
            $address     = $rawAddress !== ''
                ? htmlspecialchars($rawAddress, ENT_QUOTES, 'UTF-8')
                : 'Sin dirección';

            $customers[$key]['cont']                = $counter;
            $customers[$key]['fullname']            = $name;
            $customers[$key]['fullname_raw']        = $rawName;
            $customers[$key]['document_type']       = $documentType;
            $customers[$key]['document_type_raw']   = $rawDocumentType;
            $customers[$key]['documenttype_id']     = (int) ($customer['documenttype_id'] ?? 0);
            $customers[$key]['document_type_id']    = (int) ($customer['documenttype_id'] ?? 0);
            $customers[$key]['document_number']     = $document;
            $customers[$key]['document_raw']        = $rawDocument;
            $customers[$key]['phone_number']        = $phone;
            $customers[$key]['phone_raw']           = $rawPhone;
            $customers[$key]['email']               = $email;
            $customers[$key]['email_raw']           = $rawEmail;
            $customers[$key]['direction']           = $address;
            $customers[$key]['direction_raw']       = $rawAddress;
            $customers[$key]['status_text']         = $rawStatus;
            $customers[$key]['status']              = $rawStatus === 'Activo'
                ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $isProtected = $this->isProtectedCustomerName($customer['fullname'] ?? '');

            $actions  = '<div class="btn-group btn-group-sm" role="group">';
            $actions .= '<button class="btn btn-outline-secondary text-secondary view-customer" data-id="'
                . (int) $customer['idCustomer'] . '" title="Ver detalles del cliente">'
                . '<i class="bi bi-eye"></i></button>';

            if (!$isProtected) {
                $actions .= '<button class="btn btn-outline-primary text-primary edit-customer" data-id="'
                    . (int) $customer['idCustomer'] . '" title="Editar cliente">'
                    . '<i class="bi bi-pencil-square"></i></button>';
                $actions .= '<button class="btn btn-outline-danger text-danger delete-customer" data-id="'
                    . (int) $customer['idCustomer'] . '" data-name="' . $name . '" data-token="' . csrf(false) . '"'
                    . ' title="Eliminar cliente"><i class="bi bi-trash"></i></button>';
            }

            $actions .= '</div>';
            $customers[$key]['actions'] = $actions;

            $counter++;
        }

        toJson($customers);
    }

    /**
     * Registra un nuevo cliente vinculado al negocio activo.
     *
     * @return void
     */
    public function setCustomer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $businessId = $this->getBusinessId();

        $documentTypeId = (int) ($_POST['txtCustomerDocumentType'] ?? 0);
        $document       = $this->sanitizeNumeric($_POST['txtCustomerDocument'] ?? '', 11);
        $name           = ucwords(strClean($_POST['txtCustomerName'] ?? ''));
        $phone          = $this->sanitizeNumeric($_POST['txtCustomerPhone'] ?? '', 11);
        $email          = $this->sanitizeEmail($_POST['txtCustomerEmail'] ?? '');
        $address        = strClean($_POST['txtCustomerAddress'] ?? '');

        if ($documentTypeId <= 0) {
            $this->responseError('Debes seleccionar un tipo de documento válido.');
        }

        if ($document === '') {
            $this->responseError('El número de documento es obligatorio.');
        }

        if ($name === '') {
            $this->responseError('El nombre del cliente es obligatorio.');
        }

        if ($this->isProtectedCustomerName($name)) {
            $this->responseError('No puedes registrar un cliente con el nombre protegido del sistema.');
        }

        $existing = $this->model->selectCustomerByDocument($businessId, $documentTypeId, $document);
        if (!empty($existing)) {
            $this->responseError('Ya existe un cliente registrado con el mismo documento en tu negocio.');
        }

        $customerId = $this->model->insertCustomer($businessId, [
            'document_type_id' => $documentTypeId,
            'document'         => $document,
            'name'             => $name,
            'phone'            => $phone,
            'email'            => $email,
            'address'          => $address,
        ]);

        if ($customerId <= 0) {
            $this->responseError('No fue posible registrar el cliente, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Cliente registrado',
            'message' => 'El cliente se registró correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Actualiza la información de un cliente existente.
     *
     * @return void
     */
    public function updateCustomer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $businessId = $this->getBusinessId();

        $customerId = (int) ($_POST['customerId'] ?? 0);
        if ($customerId <= 0) {
            $this->responseError('Identificador de cliente inválido.');
        }

        $current = $this->model->findCustomer($customerId, $businessId);
        if (empty($current)) {
            $this->responseError('El cliente seleccionado no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedCustomerName($current['fullname'] ?? '')) {
            $this->responseError('No puedes modificar el cliente protegido del sistema.');
        }

        $documentTypeId = (int) ($_POST['txtCustomerDocumentType'] ?? 0);
        $document       = $this->sanitizeNumeric($_POST['txtCustomerDocument'] ?? '', 11);
        $name           = ucwords(strClean($_POST['txtCustomerName'] ?? ''));
        $phone          = $this->sanitizeNumeric($_POST['txtCustomerPhone'] ?? '', 11);
        $email          = $this->sanitizeEmail($_POST['txtCustomerEmail'] ?? '');
        $address        = strClean($_POST['txtCustomerAddress'] ?? '');

        if ($documentTypeId <= 0) {
            $this->responseError('Debes seleccionar un tipo de documento válido.');
        }

        if ($document === '') {
            $this->responseError('El número de documento es obligatorio.');
        }

        if ($name === '') {
            $this->responseError('El nombre del cliente es obligatorio.');
        }

        $existing = $this->model->selectCustomerByDocument($businessId, $documentTypeId, $document, $customerId);
        if (!empty($existing)) {
            $this->responseError('Ya existe otro cliente con el mismo documento en tu negocio.');
        }

        $updated = $this->model->updateCustomer($customerId, $businessId, [
            'document_type_id' => $documentTypeId,
            'document'         => $document,
            'name'             => $name,
            'phone'            => $phone,
            'email'            => $email,
            'address'          => $address,
        ]);

        if (!$updated) {
            $this->responseError('No fue posible actualizar el cliente, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Cliente actualizado',
            'message' => 'Los datos del cliente fueron actualizados correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Elimina un cliente del negocio activo.
     *
     * @return void
     */
    public function deleteCustomer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->responseError('Solicitud inválida.');
        }

        $customerId = (int) ($payload['id'] ?? 0);
        if ($customerId <= 0) {
            $this->responseError('No fue posible identificar el cliente seleccionado.');
        }

        $token = (string) ($payload['token'] ?? '');

        $userId = $this->getUserId();
        $this->validateCsrfToken($token, $userId);

        $businessId = $this->getBusinessId();

        $customer = $this->model->findCustomer($customerId, $businessId);
        if (empty($customer)) {
            $this->responseError('El cliente seleccionado no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedCustomerName($customer['fullname'] ?? '')) {
            $this->responseError('No puedes eliminar el cliente protegido del sistema.');
        }

        $deleted = $this->model->deleteCustomer($customerId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar el cliente, inténtalo nuevamente.');
        }

        toJson([
            'title'   => 'Cliente eliminado',
            'message' => 'El cliente se eliminó correctamente.',
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
     * Sanitiza una cadena numérica permitiendo únicamente dígitos y limitando su longitud.
     *
     * @param string $value  Valor recibido.
     * @param int    $length Longitud máxima permitida.
     *
     * @return string
     */
    private function sanitizeNumeric(string $value, int $length): string
    {
        $digits = preg_replace('/[^0-9]/', '', $value);
        if (!is_string($digits)) {
            return '';
        }

        return substr($digits, 0, $length);
    }

    /**
     * Sanitiza una dirección de correo y devuelve una cadena segura.
     *
     * @param string $value Valor recibido.
     *
     * @return string
     */
    private function sanitizeEmail(string $value): string
    {
        $clean = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
        return is_string($clean) ? substr($clean, 0, 255) : '';
    }

    /**
     * Determina si el nombre corresponde al cliente protegido del sistema.
     *
     * @param string $name Nombre a evaluar.
     *
     * @return bool
     */
    private function isProtectedCustomerName(string $name): bool
    {
        if ($name === '') {
            return false;
        }

        return $this->normalizeCustomerKey($name) === $this->getProtectedCustomerKey();
    }

    /**
     * Obtiene la clave normalizada del cliente protegido.
     *
     * @return string
     */
    private function getProtectedCustomerKey(): string
    {
        if ($this->protectedCustomerKey === null) {
            $this->protectedCustomerKey = $this->normalizeCustomerKey('Sin cliente');
        }

        return $this->protectedCustomerKey;
    }

    /**
     * Normaliza un nombre de cliente eliminando tildes y homogenizando espacios.
     *
     * @param string $value Texto a normalizar.
     *
     * @return string
     */
    private function normalizeCustomerKey(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $transliterated = $trimmed;

        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT', $trimmed);
            if ($converted !== false && $converted !== null) {
                $transliterated = $converted;
            }
        }

        if ($transliterated === $trimmed) {
            $transliterated = strtr($transliterated, [
                'Á' => 'A',
                'À' => 'A',
                'Â' => 'A',
                'Ä' => 'A',
                'Ã' => 'A',
                'Å' => 'A',
                'É' => 'E',
                'È' => 'E',
                'Ê' => 'E',
                'Ë' => 'E',
                'Í' => 'I',
                'Ì' => 'I',
                'Î' => 'I',
                'Ï' => 'I',
                'Ó' => 'O',
                'Ò' => 'O',
                'Ô' => 'O',
                'Ö' => 'O',
                'Õ' => 'O',
                'Ú' => 'U',
                'Ù' => 'U',
                'Û' => 'U',
                'Ü' => 'U',
                'Ñ' => 'N',
                'Ç' => 'C',
                'á' => 'a',
                'à' => 'a',
                'â' => 'a',
                'ä' => 'a',
                'ã' => 'a',
                'å' => 'a',
                'é' => 'e',
                'è' => 'e',
                'ê' => 'e',
                'ë' => 'e',
                'í' => 'i',
                'ì' => 'i',
                'î' => 'i',
                'ï' => 'i',
                'ó' => 'o',
                'ò' => 'o',
                'ô' => 'o',
                'ö' => 'o',
                'õ' => 'o',
                'ú' => 'u',
                'ù' => 'u',
                'û' => 'u',
                'ü' => 'u',
                'ñ' => 'n',
                'ç' => 'c',
            ]);
        }

        $lower = function_exists('mb_strtolower')
            ? mb_strtolower($transliterated, 'UTF-8')
            : strtolower($transliterated);

        $normalizedSpaces = preg_replace('/\s+/', ' ', $lower ?? '');

        return is_string($normalizedSpaces) ? $normalizedSpaces : '';
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
        toJson([
            'title'  => 'Ocurrió un error',
            'message' => $message,
            'type'   => 'error',
            'icon'   => 'error',
            'status' => false,
        ]);
    }
}
