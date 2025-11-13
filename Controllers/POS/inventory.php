<?php

class Inventory extends Controllers
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
     * Clave normalizada de la categoría protegida por defecto.
     *
     * @var string|null
     */
    private ?string $protectedCategoryKey = null;

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
        ];
        $this->views->getView($this, "inventory", $data, "POS");
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
            $supplierName = htmlspecialchars($product['supplier'], ENT_QUOTES, 'UTF-8');

            $formattedStock = number_format((float) $product['stock'], 2, SPD, SPM);

            $products[$key]['cont']        = $counter;
            $products[$key]['name']        = $productName;
            $products[$key]['category']    = $categoryName;
            $products[$key]['supplier']    = $supplierName;
            $products[$key]['measurement'] = $measurementName;
            $products[$key]['stock']       = $formattedStock . ' ' . $measurementName;
            $products[$key]['purchase_price'] = $currency . ' ' . formatMoney((float) $product['purchase_price']);
            $products[$key]['sales_price']    = $currency . ' ' . formatMoney((float) $product['sales_price']);
            $products[$key]['status']         = $product['status'] === 'Activo'
                ? '<span class="badge badge-success bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge badge-secondary bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $products[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                . '<button class="btn btn-outline-secondary text-secondary report-product" data-id="' . (int) $product['idProduct'] . '" data-name="' . $productName . '" title="Ver reporte del producto">'
                . '<i class="bi bi-file-earmark-text"></i></button>'
                . '<button class="btn btn-outline-primary text-primary edit-product" data-id="' . (int) $product['idProduct'] . '">'
                . '<i class="bi bi-pencil-square"></i></button>'
                . '<button class="btn btn-outline-danger text-danger delete-product" data-id="' . (int) $product['idProduct'] . '" data-name="' . $productName . '" data-token="' . csrf(false) . '">'
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
            'txtProductSupplier',
            'txtProductPurchasePrice',
            'txtProductSalesPrice',
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
        $supplierId    = (int) ($_POST['txtProductSupplier'] ?? 0);
        $stock         = $this->resolveOptionalStock($_POST['txtProductStock'] ?? null);
        $purchase      = $this->sanitizeDecimal($_POST['txtProductPurchasePrice'] ?? '0', 'precio de compra');
        $sales         = $this->sanitizeDecimal($_POST['txtProductSalesPrice'] ?? '0', 'precio de venta');
        $status        = 'Activo';
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

        if ($supplierId <= 0) {
            $this->responseError('Debes seleccionar un proveedor válido.');
        }

        $this->ensureCategoryBelongsToBusiness($categoryId, $businessId);
        $this->ensureMeasurementExists($measurementId);
        $this->ensureSupplierBelongsToBusiness($supplierId, $businessId);

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
            'supplier_id'    => $supplierId,
        ];

        $productId = $this->model->insertProduct($payload);
        if ($productId <= 0) {
            $this->responseError('No fue posible registrar el producto, inténtalo nuevamente.');
        }

        registerLog('Registro de producto POS', 'Se registró el producto: ' . $name, 2, $userId);

        $data = [
            'title'  => 'Registro exitoso',
            'message' => 'El producto se registró correctamente.',
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

        $currencySymbol = getCurrency();

        $data = [
            'status' => true,
            'data'   => [
                'idProduct'      => (int) $product['idProduct'],
                'category_id'    => (int) $product['category_id'],
                'category_name'  => $product['category_name'] ?? '',
                'measurement_id' => (int) $product['measurement_id'],
                'measurement_name' => $product['measurement_name'] ?? '',
                'supplier_id'    => (int) $product['supplier_id'],
                'supplier_name'  => $product['supplier_name'] ?? '',
                'name'           => $product['name'],
                'stock'          => (float) $product['stock'],
                'stock_text'     => number_format((float) $product['stock'], 2, SPD, SPM)
                    . ' ' . ($product['measurement_name'] ?? ''),
                'purchase_price' => (float) $product['purchase_price'],
                'purchase_price_text' => $currencySymbol . ' '
                    . formatMoney((float) $product['purchase_price']),
                'sales_price'    => (float) $product['sales_price'],
                'sales_price_text'    => $currencySymbol . ' '
                    . formatMoney((float) $product['sales_price']),
                'description'    => $product['description'] ?? '',
                'status'         => $product['status'],
                'currency_symbol' => $currencySymbol,
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
            'update_txtProductSupplier',
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
        $supplierId    = (int) ($_POST['update_txtProductSupplier'] ?? 0);
        $stock         = $this->resolveOptionalStock($_POST['update_txtProductStock'] ?? null);
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

        if ($supplierId <= 0) {
            $this->responseError('Debes seleccionar un proveedor válido.');
        }

        $currentProduct = $this->model->selectProduct($productId, $businessId);
        if (empty($currentProduct)) {
            $this->responseError('El producto seleccionado no existe o no pertenece a tu negocio.');
        }

        $this->ensureCategoryBelongsToBusiness($categoryId, $businessId);
        $this->ensureMeasurementExists($measurementId);
        $this->ensureSupplierBelongsToBusiness($supplierId, $businessId);

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
            'supplier_id'    => $supplierId,
        ];

        $updated = $this->model->updateProduct($payload);
        if (!$updated) {
            $this->responseError('No fue posible actualizar el producto, inténtalo nuevamente.');
        }

        registerLog('Actualización de producto POS', 'Se actualizó el producto: ' . $name, 3, $userId);

        $data = [
            'title'  => 'Actualización exitosa',
            'message' => 'La información del producto se actualizó correctamente.',
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

        registerLog('Eliminación de producto POS', 'Se eliminó el producto: ' . $product['name'], 3, $userId);

        $data = [
            'title'  => 'Producto eliminado',
            'message' => 'El producto se eliminó correctamente.',
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
     * Devuelve el listado completo de categorías para el mantenimiento en el POS.
     *
     * @return void
     */
    public function getCategoryList(): void
    {
        $businessId = $this->getBusinessId();
        $data       = $this->model->selectCategoryList($businessId);

        toJson([
            'status' => true,
            'data'   => $data,
        ]);
    }

    /**
     * Registra una nueva categoría asociada al negocio activo.
     *
     * @return void
     */
    public function setCategory(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $rawName = $_POST['txtCategoryName'] ?? '';
        $name    = ucwords(strClean($rawName));

        if ($name === '') {
            $this->responseError('El nombre de la categoría es obligatorio.');
        }

        $businessId = $this->getBusinessId();
        $existing   = $this->model->selectCategoryByName($name, $businessId);

        if (!empty($existing)) {
            $this->responseError('Ya existe una categoría con el mismo nombre en tu negocio.');
        }

        $categoryId = $this->model->insertCategory($businessId, $name);
        if ($categoryId <= 0) {
            $this->responseError('No fue posible registrar la categoría, inténtalo nuevamente.');
        }

        registerLog('Registro de categoría POS', 'Se registró la categoría: ' . $name, 2, $userId);

        toJson([
            'title'  => 'Categoría registrada',
            'message' => 'La categoría se registró correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ]);
    }

    /**
     * Actualiza el nombre de una categoría existente del negocio activo.
     *
     * @return void
     */
    public function updateCategory(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $categoryId = (int) ($_POST['categoryId'] ?? 0);
        $rawName    = $_POST['txtCategoryName'] ?? '';
        $name       = ucwords(strClean($rawName));

        if ($categoryId <= 0) {
            $this->responseError('No fue posible identificar la categoría seleccionada.');
        }

        if ($name === '') {
            $this->responseError('El nombre de la categoría es obligatorio.');
        }

        $businessId = $this->getBusinessId();
        $category   = $this->model->findCategory($categoryId, $businessId);

        if (empty($category)) {
            $this->responseError('La categoría seleccionada no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedCategoryName($category['name'] ?? '')) {
            $this->responseError('No puedes modificar la categoría predeterminada del sistema.');
        }

        $existing = $this->model->selectCategoryByName($name, $businessId, $categoryId);
        if (!empty($existing)) {
            $this->responseError('Ya existe otra categoría con el mismo nombre en tu negocio.');
        }

        $updated = $this->model->updateCategory($categoryId, $businessId, $name);
        if (!$updated) {
            $this->responseError('No fue posible actualizar la categoría, inténtalo nuevamente.');
        }

        registerLog('Actualización de categoría POS', 'Se actualizó la categoría: ' . $name, 2, $userId);

        toJson([
            'title'  => 'Categoría actualizada',
            'message' => 'La categoría se actualizó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ]);
    }

    /**
     * Elimina o desactiva una categoría del negocio activo según sus asociaciones.
     *
     * Si la categoría no tiene registros relacionados se elimina definitivamente.
     * En caso contrario, únicamente se desactiva para mantener la integridad de datos.
     *
     * @return void
     */
    public function deleteCategory(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            $this->responseError('Solicitud inválida.');
        }

        $categoryId = (int) ($payload['id'] ?? 0);
        $token      = (string) ($payload['token'] ?? '');

        if ($categoryId <= 0) {
            $this->responseError('No fue posible identificar la categoría seleccionada.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($token, $userId);

        $businessId = $this->getBusinessId();
        $category   = $this->model->findCategory($categoryId, $businessId);

        if (empty($category)) {
            $this->responseError('La categoría seleccionada no existe o no pertenece a tu negocio.');
        }

        if ($this->isProtectedCategoryName($category['name'] ?? '')) {
            $this->responseError('No puedes eliminar la categoría predeterminada del sistema.');
        }

        $productsAssociated = $this->model->countProductsByCategory($categoryId, $businessId);

        if ($productsAssociated > 0) {
            $deactivated = $this->model->deactivateCategory($categoryId, $businessId);
            if (!$deactivated) {
                $this->responseError('No fue posible desactivar la categoría, inténtalo nuevamente.');
            }

            registerLog('Desactivación de categoría POS', 'Se desactivó la categoría: ' . $category['name'], 2, $userId);

            toJson([
                'title'   => 'Categoría desactivada',
                'message' => 'La categoría tiene registros asociados, por lo que se desactivó y se ocultó del listado.',
                'type'    => 'success',
                'icon'    => 'success',
                'status'  => true,
            ]);

            return;
        }

        $deleted = $this->model->deleteCategory($categoryId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar la categoría, inténtalo nuevamente.');
        }

        registerLog('Eliminación de categoría POS', 'Se eliminó la categoría: ' . $category['name'], 3, $userId);

        toJson([
            'title'   => 'Categoría eliminada',
            'message' => 'La categoría se eliminó correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
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
     * Devuelve los proveedores activos para el negocio en sesión.
     *
     * @return void
     */
    public function getSuppliers(): void
    {
        $businessId = $this->getBusinessId();
        $data       = $this->model->selectSuppliers($businessId);

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
     * Determina el valor de stock permitido considerando que el campo es opcional.
     *
     * @param string|null $value Valor recibido desde el formulario.
     *
     * @return float
     */
    private function resolveOptionalStock(?string $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return 0.0;
        }

        return $this->sanitizeDecimal($trimmed, 'stock');
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
     * Verifica que el proveedor pertenezca al negocio activo y esté disponible.
     *
     * @param int $supplierId Identificador del proveedor.
     * @param int $businessId Identificador del negocio activo.
     *
     * @return void
     */
    private function ensureSupplierBelongsToBusiness(int $supplierId, int $businessId): void
    {
        $supplier = $this->model->selectSupplier($supplierId, $businessId);
        if (empty($supplier)) {
            $this->responseError('El proveedor seleccionado no pertenece a tu negocio o está inactivo.');
        }
    }

    /**
     * Determina si el nombre corresponde a la categoría protegida por defecto.
     *
     * @param string $name Nombre a evaluar.
     *
     * @return bool
     */
    private function isProtectedCategoryName(string $name): bool
    {
        if ($name === '') {
            return false;
        }

        return $this->normalizeCategoryKey($name) === $this->getProtectedCategoryKey();
    }

    /**
     * Obtiene la clave de comparación de la categoría protegida.
     *
     * @return string
     */
    private function getProtectedCategoryKey(): string
    {
        if ($this->protectedCategoryKey === null) {
            $this->protectedCategoryKey = $this->normalizeCategoryKey('Sin Categoría');
        }

        return $this->protectedCategoryKey;
    }

    /**
     * Normaliza un nombre de categoría para comparaciones internas.
     *
     * @param string $value Texto a normalizar.
     *
     * @return string
     */
    private function normalizeCategoryKey(string $value): string
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
