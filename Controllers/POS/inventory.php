<?php

class inventory extends Controllers
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

    public function inventory()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Inventario de productos',
            'page_description' => 'Gestiona los productos disponibles en tu negocio.',
            'page_container'   => 'Inventory',
            'page_view'        => 'inventory',
            'page_js_css'      => 'inventory',
            'page_assets'      => [
                'css_version' => '1.0.0',
                'js_version'  => '1.0.0',
            ],
        ];

        $this->views->getView($this, 'inventory', $data, 'POS');
    }

    /**
     * Devuelve el listado de productos pertenecientes al negocio activo.
     *
     * @return void
     */
    public function getProducts(): void
    {
        $businessId = $this->getBusinessId();
        $products   = $this->model->selectProducts($businessId);
        $currency   = getCurrency();
        $counter    = 1;

        foreach ($products as $key => $product) {
            $productName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $categoryName = htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8');
            $measurementName = htmlspecialchars($product['measurement'], ENT_QUOTES, 'UTF-8');

            $products[$key]['cont']        = $counter;
            $products[$key]['name']        = $productName;
            $products[$key]['category']    = $categoryName;
            $products[$key]['measurement'] = $measurementName;
            $products[$key]['stock']       = number_format((float) $product['stock'], 2, SPD, SPM);
            $products[$key]['purchase_price'] = $currency . ' ' . formatMoney((float) $product['purchase_price']);
            $products[$key]['sales_price']    = $currency . ' ' . formatMoney((float) $product['sales_price']);
            $products[$key]['status']         = $product['status'] === 'Activo'
                ? '<span class="badge badge-success bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge badge-secondary bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $products[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                . '<button class="btn btn-outline-primary text-primary edit-product" data-id="' . (int) $product['idProduct'] . '">'
                . '<i class="bi bi-pencil-square"></i></button>'
                . '<button class="btn btn-outline-danger text-danger delete-product" data-id="' . (int) $product['idProduct'] . '" data-name="' . $productName . '">'
                . '<i class="bi bi-trash"></i></button>'
                . '</div>';

            $counter++;
        }

        toJson($products);
    }

    /**
     * Registra un nuevo producto asociado al negocio activo.
     *
     * @return void
     */
    public function setProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $requiredFields = [
            'txtProductName',
            'txtProductCategory',
            'txtProductMeasurement',
            'txtProductStock',
            'txtProductPurchasePrice',
            'txtProductSalesPrice',
            'txtProductStatus',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                $this->responseError('El campo ' . $field . ' es obligatorio.');
            }
        }

        $businessId    = $this->getBusinessId();
        $name          = ucwords(strClean($_POST['txtProductName'] ?? ''));
        $categoryId    = (int) ($_POST['txtProductCategory'] ?? 0);
        $measurementId = (int) ($_POST['txtProductMeasurement'] ?? 0);
        $stock         = $this->sanitizeDecimal($_POST['txtProductStock'] ?? '0', 'stock');
        $purchase      = $this->sanitizeDecimal($_POST['txtProductPurchasePrice'] ?? '0', 'precio de compra');
        $sales         = $this->sanitizeDecimal($_POST['txtProductSalesPrice'] ?? '0', 'precio de venta');
        $status        = $_POST['txtProductStatus'] === 'Inactivo' ? 'Inactivo' : 'Activo';
        $description   = strClean($_POST['txtProductDescription'] ?? '');

        if ($name === '') {
            $this->responseError('El nombre del producto es obligatorio.');
        }

        if ($categoryId <= 0) {
            $this->responseError('Debes seleccionar una categoría válida.');
        }

        if ($measurementId <= 0) {
            $this->responseError('Debes seleccionar una unidad de medida válida.');
        }

        $this->ensureCategoryBelongsToBusiness($categoryId, $businessId);
        $this->ensureMeasurementExists($measurementId);

        $existingProduct = $this->model->selectProductByName($name, $businessId);
        if (!empty($existingProduct)) {
            $this->responseError('Ya existe un producto con el mismo nombre en tu negocio.');
        }

        $payload = [
            'category_id'    => $categoryId,
            'name'           => $name,
            'stock'          => $stock,
            'purchase_price' => $purchase,
            'sales_price'    => $sales,
            'measurement_id' => $measurementId,
            'description'    => $description,
            'status'         => $status,
        ];

        $productId = $this->model->insertProduct($payload);
        if ($productId <= 0) {
            $this->responseError('No fue posible registrar el producto, inténtalo nuevamente.');
        }

        registerLog('Registro de producto POS', 'Se registró el producto: ' . $name, 2, $userId);

        $data = [
            'title'  => 'Registro exitoso',
            'message'=> 'El producto se registró correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Devuelve la información detallada de un producto para edición o consulta.
     *
     * @return void
     */
    public function getProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
        }

        $businessId = $this->getBusinessId();
        $product    = $this->model->selectProduct($productId, $businessId);

        if (empty($product)) {
            $this->responseError('No se encontró el producto solicitado.');
        }

        $data = [
            'status' => true,
            'data'   => [
                'idProduct'      => (int) $product['idProduct'],
                'category_id'    => (int) $product['category_id'],
                'measurement_id' => (int) $product['measurement_id'],
                'name'           => $product['name'],
                'stock'          => (float) $product['stock'],
                'purchase_price' => (float) $product['purchase_price'],
                'sales_price'    => (float) $product['sales_price'],
                'description'    => $product['description'] ?? '',
                'status'         => $product['status'],
            ],
        ];

        toJson($data);
    }

    /**
     * Actualiza la información de un producto existente.
     *
     * @return void
     */
    public function updateProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $requiredFields = [
            'update_txtProductId',
            'update_txtProductName',
            'update_txtProductCategory',
            'update_txtProductMeasurement',
            'update_txtProductStock',
            'update_txtProductPurchasePrice',
            'update_txtProductSalesPrice',
            'update_txtProductStatus',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                $this->responseError('El campo ' . $field . ' es obligatorio.');
            }
        }

        $businessId    = $this->getBusinessId();
        $productId     = (int) ($_POST['update_txtProductId'] ?? 0);
        $name          = ucwords(strClean($_POST['update_txtProductName'] ?? ''));
        $categoryId    = (int) ($_POST['update_txtProductCategory'] ?? 0);
        $measurementId = (int) ($_POST['update_txtProductMeasurement'] ?? 0);
        $stock         = $this->sanitizeDecimal($_POST['update_txtProductStock'] ?? '0', 'stock');
        $purchase      = $this->sanitizeDecimal($_POST['update_txtProductPurchasePrice'] ?? '0', 'precio de compra');
        $sales         = $this->sanitizeDecimal($_POST['update_txtProductSalesPrice'] ?? '0', 'precio de venta');
        $status        = $_POST['update_txtProductStatus'] === 'Inactivo' ? 'Inactivo' : 'Activo';
        $description   = strClean($_POST['update_txtProductDescription'] ?? '');

        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
        }

        if ($name === '') {
            $this->responseError('El nombre del producto es obligatorio.');
        }

        if ($categoryId <= 0) {
            $this->responseError('Debes seleccionar una categoría válida.');
        }

        if ($measurementId <= 0) {
            $this->responseError('Debes seleccionar una unidad de medida válida.');
        }

        $currentProduct = $this->model->selectProduct($productId, $businessId);
        if (empty($currentProduct)) {
            $this->responseError('El producto seleccionado no existe o no pertenece a tu negocio.');
        }

        $this->ensureCategoryBelongsToBusiness($categoryId, $businessId);
        $this->ensureMeasurementExists($measurementId);

        $existingProduct = $this->model->selectProductByName($name, $businessId);
        if (!empty($existingProduct) && (int) $existingProduct['idProduct'] !== $productId) {
            $this->responseError('Ya existe otro producto con el mismo nombre en tu negocio.');
        }

        $payload = [
            'idProduct'      => $productId,
            'category_id'    => $categoryId,
            'name'           => $name,
            'stock'          => $stock,
            'purchase_price' => $purchase,
            'sales_price'    => $sales,
            'measurement_id' => $measurementId,
            'description'    => $description,
            'status'         => $status,
        ];

        $updated = $this->model->updateProduct($payload);
        if (!$updated) {
            $this->responseError('No fue posible actualizar el producto, inténtalo nuevamente.');
        }

        registerLog('Actualización de producto POS', 'Se actualizó el producto: ' . $name, 3, $userId);

        $data = [
            'title'  => 'Actualización exitosa',
            'message'=> 'La información del producto se actualizó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Elimina definitivamente un producto del negocio activo.
     *
     * @return void
     */
    public function deleteProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = $this->getUserId();

        $this->validateCsrfToken($input['token'] ?? '', $userId);

        $productId = isset($input['id']) ? (int) $input['id'] : 0;
        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
        }

        $businessId = $this->getBusinessId();
        $product    = $this->model->selectProduct($productId, $businessId);

        if (empty($product)) {
            $this->responseError('El producto seleccionado no existe o no pertenece a tu negocio.');
        }

        $deleted = $this->model->deleteProduct($productId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar el producto, inténtalo nuevamente.');
        }

        registerLog('Eliminación de producto POS', 'Se eliminó el producto: ' . $product['name'], 4, $userId);

        $data = [
            'title'  => 'Producto eliminado',
            'message'=> 'El producto se eliminó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ];

        toJson($data);
    }

    /**
     * Devuelve las categorías disponibles para el negocio activo.
     *
     * @return void
     */
    public function getCategories(): void
    {
        $businessId = $this->getBusinessId();
        $data = $this->model->selectCategories($businessId);

        toJson([
            'status' => true,
            'data'   => $data,
        ]);
    }

    /**
     * Devuelve las unidades de medida disponibles en el sistema.
     *
     * @return void
     */
    public function getMeasurements(): void
    {
        $data = $this->model->selectMeasurements();

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
     * Valida que el valor recibido sea un número decimal correcto.
     *
     * @param string $value     Valor recibido desde el formulario.
     * @param string $fieldName Nombre descriptivo del campo.
     *
     * @return float
     */
    private function sanitizeDecimal(string $value, string $fieldName): float
    {
        $normalized = str_replace([','], [''], trim($value));

        if (!is_numeric($normalized)) {
            $this->responseError('El campo ' . $fieldName . ' debe ser numérico.');
        }

        $number = (float) $normalized;
        if ($number < 0) {
            $this->responseError('El campo ' . $fieldName . ' no puede ser negativo.');
        }

        return round($number, 2);
    }

    /**
     * Verifica que la categoría pertenezca al negocio activo.
     *
     * @param int $categoryId Identificador de la categoría.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return void
     */
    private function ensureCategoryBelongsToBusiness(int $categoryId, int $businessId): void
    {
        $category = $this->model->selectCategory($categoryId, $businessId);
        if (empty($category)) {
            $this->responseError('La categoría seleccionada no pertenece a tu negocio o está inactiva.');
        }
    }

    /**
     * Verifica que la unidad de medida exista y esté activa.
     *
     * @param int $measurementId Identificador de la unidad de medida.
     *
     * @return void
     */
    private function ensureMeasurementExists(int $measurementId): void
    {
        $measurement = $this->model->selectMeasurement($measurementId);
        if (empty($measurement)) {
            $this->responseError('La unidad de medida seleccionada no es válida.');
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
            'message'=> $message,
            'type'   => 'error',
            'icon'   => 'error',
            'status' => false,
        ];

        toJson($data);
    }
}
