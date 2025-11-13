<?php

class Suppliers extends Controllers
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
     * Clave normalizada del proveedor protegido por defecto.
     *
     * @var string|null
     */
    private ?string $protectedSupplierKey = null;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Renderiza la vista principal de gestión de proveedores.
     *
     * @return void
     */
    public function suppliers(): void
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Gestión de proveedores',
            'page_description' => 'Administra los proveedores registrados en tu negocio.',
            'page_container'   => 'Suppliers',
            'page_view'        => 'suppliers',
            'page_js_css'      => 'suppliers',
        ];

        $this->views->getView($this, 'suppliers', $data, 'POS');
    }

    /**
     * Devuelve el listado de proveedores activos pertenecientes al negocio actual.
     *
     * @return void
     */
    public function getSuppliers(): void
    {
        $businessId = $this->getBusinessId();
        $suppliers  = $this->model->selectSuppliers($businessId);
        $counter    = 1;

        foreach ($suppliers as $key => $supplier) {
            $rawName      = (string) ($supplier['company_name'] ?? '');
            $rawDocument  = (string) ($supplier['document_number'] ?? '');
            $rawPhone     = (string) ($supplier['phone_number'] ?? '');
            $rawEmail     = (string) ($supplier['email'] ?? '');
            $rawAddress   = (string) ($supplier['direction'] ?? '');
            $rawStatus    = (string) ($supplier['status'] ?? 'Activo');

            $name     = htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8');
            $document = $rawDocument !== ''
                ? htmlspecialchars($rawDocument, ENT_QUOTES, 'UTF-8')
                : 'Sin documento';
            $phone    = $rawPhone !== ''
                ? htmlspecialchars($rawPhone, ENT_QUOTES, 'UTF-8')
                : 'Sin teléfono';
            $email    = $rawEmail !== ''
                ? htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8')
                : 'Sin correo';
            $address  = $rawAddress !== ''
                ? htmlspecialchars($rawAddress, ENT_QUOTES, 'UTF-8')
                : 'Sin dirección';

            $suppliers[$key]['cont']             = $counter;
            $suppliers[$key]['company_name']     = $name;
            $suppliers[$key]['company_raw']      = $rawName;
            $suppliers[$key]['document_number']  = $document;
            $suppliers[$key]['document_raw']     = $rawDocument;
            $suppliers[$key]['phone_number']     = $phone;
            $suppliers[$key]['phone_raw']        = $rawPhone;
            $suppliers[$key]['email']            = $email;
            $suppliers[$key]['email_raw']        = $rawEmail;
            $suppliers[$key]['direction']        = $address;
            $suppliers[$key]['direction_raw']    = $rawAddress;
            $suppliers[$key]['status_text']      = $rawStatus;
            $suppliers[$key]['status']           = $rawStatus === 'Activo'
                ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $isProtected = $this->isProtectedSupplierName($supplier['company_name']);

            $actions  = '<div class="btn-group btn-group-sm" role="group">';
            $actions .= '<button class="btn btn-outline-secondary text-secondary view-supplier" data-id="'
                . (int) $supplier['idSupplier'] . '" title="Ver detalles del proveedor">'
                . '<i class="bi bi-eye"></i></button>';

            if (!$isProtected) {
                $actions .= '<button class="btn btn-outline-primary text-primary edit-supplier" data-id="'
                    . (int) $supplier['idSupplier'] . '" title="Editar proveedor">'
                    . '<i class="bi bi-pencil-square"></i></button>';
                $actions .= '<button class="btn btn-outline-danger text-danger delete-supplier" data-id="'
                    . (int) $supplier['idSupplier'] . '" data-name="' . $name . '" data-token="' . csrf(false) . '"'
                    . ' title="Eliminar proveedor"><i class="bi bi-trash"></i></button>';
            }

            $actions .= '</div>';
            $suppliers[$key]['actions'] = $actions;

            $counter++;
        }

        toJson($suppliers);
    }

    /**
     * Registra un nuevo proveedor vinculado al negocio activo.
     *
     * @return void
     */
    public function setSupplier(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $businessId = $this->getBusinessId();

        $name          = ucwords(strClean($_POST['txtSupplierName'] ?? ''));
        $document      = $this->sanitizeNumeric($_POST['txtSupplierDocument'] ?? '', 11);
        $phone         = $this->sanitizeNumeric($_POST['txtSupplierPhone'] ?? '', 11);
        $email         = $this->sanitizeEmail($_POST['txtSupplierEmail'] ?? '');
        $address       = strClean($_POST['txtSupplierAddress'] ?? '');

        if ($name === '') {
            $this->responseError('El nombre del proveedor es obligatorio.');
        }

        if ($this->isProtectedSupplierName($name)) {
            $this->responseError('No puedes registrar un proveedor con el nombre protegido del sistema.');
        }

        $existing = $this->model->selectSupplierByName($businessId, $name);
        if (!empty($existing)) {
            $this->responseError('Ya existe un proveedor con el mismo nombre en tu negocio.');
        }

        $supplierId = $this->model->insertSupplier($businessId, [
            'name'     => $name,
            'document' => $document,
            'phone'    => $phone,
            'email'    => $email,
            'address'  => $address,
        ]);

        if ($supplierId <= 0) {
            $this->responseError('No fue posible registrar el proveedor, inténtalo nuevamente.');
        }

        registerLog('Registro de proveedor POS', 'Se registró el proveedor: ' . $name, 2, $userId);

        toJson([
            'title'   => 'Proveedor registrado',
            'message' => 'El proveedor se registró correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Actualiza la información de un proveedor existente.
     *
     * @return void
     */
    public function updateSupplier(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $businessId = $this->getBusinessId();

        $supplierId = (int) ($_POST['supplierId'] ?? 0);
        if ($supplierId <= 0) {
            $this->responseError('Identificador de proveedor inválido.');
        }

        $current = $this->model->findSupplier($supplierId, $businessId);
        if (empty($current)) {
            $this->responseError('El proveedor seleccionado no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedSupplierName($current['company_name'] ?? '')) {
            $this->responseError('No puedes modificar el proveedor protegido del sistema.');
        }

        $name     = ucwords(strClean($_POST['txtSupplierName'] ?? ''));
        $document = $this->sanitizeNumeric($_POST['txtSupplierDocument'] ?? '', 11);
        $phone    = $this->sanitizeNumeric($_POST['txtSupplierPhone'] ?? '', 11);
        $email    = $this->sanitizeEmail($_POST['txtSupplierEmail'] ?? '');
        $address  = strClean($_POST['txtSupplierAddress'] ?? '');

        if ($name === '') {
            $this->responseError('El nombre del proveedor es obligatorio.');
        }

        $existing = $this->model->selectSupplierByName($businessId, $name, $supplierId);
        if (!empty($existing)) {
            $this->responseError('Ya existe otro proveedor con el mismo nombre en tu negocio.');
        }

        $updated = $this->model->updateSupplier($supplierId, $businessId, [
            'name'     => $name,
            'document' => $document,
            'phone'    => $phone,
            'email'    => $email,
            'address'  => $address,
        ]);

        if (!$updated) {
            $this->responseError('No fue posible actualizar el proveedor, inténtalo nuevamente.');
        }

        registerLog('Actualización de proveedor POS', 'Se actualizó el proveedor: ' . $current['company_name'], 2, $userId);

        toJson([
            'title'   => 'Proveedor actualizado',
            'message' => 'Los datos del proveedor fueron actualizados correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    /**
     * Elimina o desactiva un proveedor según sus asociaciones.
     *
     * @return void
     */
    public function deleteSupplier(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->responseError('Solicitud inválida.');
        }

        $supplierId = (int) ($payload['id'] ?? 0);
        if ($supplierId <= 0) {
            $this->responseError('No fue posible identificar el proveedor seleccionado.');
        }

        $token = (string) ($payload['token'] ?? '');

        $userId = $this->getUserId();
        $this->validateCsrfToken($token, $userId);

        $businessId = $this->getBusinessId();

        $supplier = $this->model->findSupplier($supplierId, $businessId);
        if (empty($supplier)) {
            $this->responseError('El proveedor seleccionado no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedSupplierName($supplier['company_name'] ?? '')) {
            $this->responseError('No puedes eliminar el proveedor protegido del sistema.');
        }

        $associatedProducts = $this->model->countProductsBySupplier($supplierId, $businessId);
        if ($associatedProducts > 0) {
            $deactivated = $this->model->deactivateSupplier($supplierId, $businessId);
            if (!$deactivated) {
                $this->responseError('No fue posible desactivar el proveedor, inténtalo nuevamente.');
            }

            registerLog('Desactivación de proveedor POS', 'Se desactivó el proveedor: ' . $supplier['company_name'], 3, $userId);

            toJson([
                'title'   => 'Proveedor desactivado',
                'message' => 'El proveedor tiene productos asociados, por lo que se desactivó y dejó de mostrarse en el listado.',
                'type'    => 'info',
                'icon'    => 'info',
                'status'  => true,
            ]);
        }

        $deleted = $this->model->deleteSupplier($supplierId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar el proveedor, inténtalo nuevamente.');
        }

        registerLog('Eliminación de proveedor POS', 'Se eliminó el proveedor: ' . $supplier['company_name'], 3, $userId);

        toJson([
            'title'   => 'Proveedor eliminado',
            'message' => 'El proveedor se eliminó correctamente.',
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
            registerLog('Seguridad POS', 'Token CSRF inválido o ausente.', 1, $userId);
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }

        $sessionToken = (string) $_SESSION['data_token']['token'];
        if (!hash_equals($sessionToken, (string) $token)) {
            registerLog('Seguridad POS', 'Token CSRF no coincide.', 1, $userId);
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
     * Determina si el nombre corresponde al proveedor protegido del sistema.
     *
     * @param string $name Nombre a evaluar.
     *
     * @return bool
     */
    private function isProtectedSupplierName(string $name): bool
    {
        if ($name === '') {
            return false;
        }

        return $this->normalizeSupplierKey($name) === $this->getProtectedSupplierKey();
    }

    /**
     * Obtiene la clave normalizada del proveedor protegido.
     *
     * @return string
     */
    private function getProtectedSupplierKey(): string
    {
        if ($this->protectedSupplierKey === null) {
            $this->protectedSupplierKey = $this->normalizeSupplierKey('Sin Proveedor');
        }

        return $this->protectedSupplierKey;
    }

    /**
     * Normaliza un nombre de proveedor eliminando tildes y homogenizando espacios.
     *
     * @param string $value Texto a normalizar.
     *
     * @return string
     */
    private function normalizeSupplierKey(string $value): string
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
