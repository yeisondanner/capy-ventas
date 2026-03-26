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
     * Nombre de la variable de sesión que contiene la información del usuario POS.DDD
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    /**
     * Clave normalizada de la categoría protegida por defecto.
     *
     * @var string|null
     */
    private string $protectedCategoryKey = "Sin categoría";

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
        validate_permission_app(3, "r");
        $data = [
            'page_id' => 3,
            'page_title' => 'Inventario de productos',
            'page_description' => 'Gestiona los productos disponibles en tu negocio.',
            'page_container' => 'Inventory',
            'page_view' => 'inventory',
            'page_js_css' => 'inventory',
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
        validate_permission_app(3, "r", false, false, false);
        $validationUpdate = (int) validate_permission_app(3, "u", false)['update'];
        $validationDelete = (int) validate_permission_app(3, "d", false)['delete'];
        $businessId = $this->getBusinessId();
        $products = $this->model->selectProducts($businessId);
        $currency = getCurrency();
        $counter = 1;
        foreach ($products as $key => $product) {
            $productName = decodeUniversalText($product['name']);
            $categoryName = decodeUniversalText($product['category']);
            $measurementName = decodeUniversalText($product['measurement']);
            $supplierName = decodeUniversalText($product['supplier']);
            $formattedStock = (float) $product['stock'];
            $gain = formatMoney((float) $product['sales_price'] - $product['purchase_price']);
            //obtenemos los dias que faltan por vencer
            $days_expiration = $product['expiration_date'] == "-" ? "-" : dateDifference(date("Y-m-d"), $product['expiration_date']);
            //agregamos un icono de una flechita arriba si la ganacia es mayor a 0 de color verde y una flechita abajo si la ganacia es menor a 0 de color rojo
            $gainIcon = $gain > 0 ? '<i class="bi bi-arrow-up text-success"></i>' : '<i class="bi bi-arrow-down text-danger"></i>';

            $products[$key]['update'] = $validationUpdate;
            $products[$key]['delete'] = $validationDelete;
            $products[$key]['cont'] = $counter;
            $products[$key]['name'] = $productName;
            $products[$key]['category'] = $categoryName;
            $products[$key]['supplier'] = $supplierName;
            $products[$key]['measurement'] = $measurementName;
            $products[$key]['stock'] = $formattedStock;
            $products[$key]['stock_mesurement'] = $formattedStock . ' ' . $measurementName;
            $products[$key]['purchase_price'] = $currency . ' ' . formatMoney((float) $product['purchase_price']);
            $products[$key]['sales_price'] = $currency . ' ' . formatMoney((float) $product['sales_price']);
            $products[$key]['gain'] = $gainIcon . $currency . ' ' . $gain;
            $products[$key]['days_expiration'] = $days_expiration;
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
        //VALIDACION DE PERMISOS
        validate_permission_app(3, "c", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $userId = $this->getUserId();
        //validamos si chkProductStatus esta activo o inactivo
        if (isset($_POST['chkProductStatus']) && $_POST['chkProductStatus'] === 'on') {
            $statusChbx = 'Si';
        } else {
            $statusChbx = 'No';
        }
        $requiredFields = [
            'txtProductName',
            'slctProductCategory',
            'slctProductMeasurement',
            'slctProductSupplier',
            'txtProductPurchasePrice',
            'txtProductSalesPrice',
            'txtProductCode',
            'slctBarcodeFormat'
        ];
        validateFields($requiredFields);
        $businessId = $this->getBusinessId();
        $name = ucwords(strClean($_POST['txtProductName'] ?? ''));
        $categoryId = (int) ($_POST['slctProductCategory'] ?? 0);
        $measurementId = (int) ($_POST['slctProductMeasurement'] ?? 0);
        $supplierId = (int) ($_POST['slctProductSupplier'] ?? 0);
        $stock = $this->resolveOptionalStock($_POST['txtProductStock'] ?? null);
        $purchase = $this->sanitizeDecimal($_POST['txtProductPurchasePrice'] ?? '0', 'precio de compra');
        $sales = $this->sanitizeDecimal($_POST['txtProductSalesPrice'] ?? '0', 'precio de venta');
        $status = 'Activo';
        $description = strClean($_POST['txtProductDescription'] ?? '');
        $dateExpiration = strClean($_POST['txtProductDateExpirated'] ?? '');
        $code = strClean($_POST['txtProductCode'] ?? '');
        $barcodeFormat = strClean($_POST['slctBarcodeFormat'] ?? 'CODE128');
        $flInput = $_FILES['flInput'];
        validateFieldsEmpty([
            'NOMBRE DEL PRODUCTO' => $name,
            'CODIGO DEL PRODUCTO' => $code,
            'CATEGORIA' => $categoryId,
            'UNIDAD DE MEDIDA' => $measurementId,
            'PROVEEDOR' => $supplierId,
            'PRECIO DE COMPRA' => $purchase,
            'PRECIO DE VENTA' => $sales,
            'TIPO DE CODIGO' => $barcodeFormat
        ]);
        if ($categoryId <= 0) {
            $this->responseError('Debes seleccionar una categoría válida.');
        }

        if ($measurementId <= 0) {
            $this->responseError('Debes seleccionar una unidad de medida válida.');
        }

        if ($supplierId <= 0) {
            $this->responseError('Debes seleccionar un proveedor válido.');
        }
        //verificamos que el codigo tenga una longitud de 50 caracteres
        if (strlen($code) > 60) {
            $this->responseError('El código no puede exceder los 60 caracteres.');
        }
        //validamos que el campo no este vacio
        if (!empty($flInput['name'])) {
            //validamos si el archivo es valido de acuerdo al tipo de archivo
            if (isFile('image', $flInput, ['png', 'jpg', 'jpeg'])) {
                $this->responseError('El archivo debe ser una imagen.');
            }
        }
        //validamos que el estock solo tenga 11 digitos
        if ($stock > 99999999.99) {
            $this->responseError('El stock no puede exceder los 11 dígitos.');
        }
        //validamos que el precio de compra solo tenga 11 digitos
        if ($purchase > 99999999.99) {
            $this->responseError('El precio de compra no puede exceder los 11 dígitos.');
        }
        //validamos que el precio de venta solo tenga 11 digitos
        if ($sales > 99999999.99) {
            $this->responseError('El precio de venta no puede exceder los 11 dígitos.');
        }
        //validamos que la fecha de vencimiento sea valida
        if (!empty($dateExpiration)) {
            if (!strtotime($dateExpiration)) {
                $this->responseError('La fecha de vencimiento no es válida.');
            }
        }
        //verificamos que la categoria pertenezca al negocio
        $this->ensureCategoryBelongsToBusiness($categoryId, $businessId);
        //verificamos que la unidad de medida exista
        $this->ensureMeasurementExists($measurementId);
        //verificamos que el proveedor pertenezca al negocio
        $this->ensureSupplierBelongsToBusiness($supplierId, $businessId);
        //verificamos que el producto no exista
        $existingProduct = $this->model->selectProductByName($name, $businessId);
        if (!empty($existingProduct)) {
            $this->responseError('Ya existe un producto con el mismo nombre en tu negocio.');
        }
        //verificamos que el codigo no exista
        $existingProductCode = $this->model->selectProductByCode($code, $businessId);
        if (!empty($existingProductCode)) {
            $this->responseError('Ya existe un producto con el mismo código de registro en tu negocio.');
        }


        $payload = [
            'category_id' => $categoryId,
            'name' => $name,
            'stock' => $stock,
            'purchase_price' => $purchase,
            'sales_price' => $sales,
            'measurement_id' => $measurementId,
            'description' => $description,
            'status' => $status,
            'supplier_id' => $supplierId,
            'is_public' => $statusChbx,
            'code' => $code,
            'expiration_date' => $dateExpiration,
            'user_id' => $userId,
            'idProduct' => 0,
            'barcode_format' => $barcodeFormat
        ];

        $productId = $this->model->insertProduct($payload);
        if ($productId <= 0) {
            $this->responseError('No fue posible registrar el producto, inténtalo nuevamente.');
        }
        //actualizamos el id del usuario
        $payload['idProduct'] = $productId;
        $productHistoryId = $this->model->insertProductHistory($payload);
        if ($productHistoryId <= 0) {
            $this->responseError('No fue posible registrar el historial del producto, inténtalo nuevamente.');
        }
        //validamos que el campo no este vacio
        if (!empty($flInput['name'])) {
            //preparamos la ruta de almacenamiento del logo
            $urlFile = getRoute();
            verifyFolder($urlFile);
            $urlFile .= '/Products';
            verifyFolder($urlFile);
            $urlFile .= '/logo';
            verifyFolder($urlFile);
            $extension = pathinfo($flInput['name'], PATHINFO_EXTENSION);
            $productname = $productId . '-' . time() . '.' . $extension;
            $sizefile = valConvert($flInput['size'])['MB'];
            $urlFile .= '/' . $productname;
            if ($sizefile > 2) {
                resizeAndCompressImage($flInput['tmp_name'], $urlFile, 2);
            } else {
                move_uploaded_file($flInput['tmp_name'], $urlFile);
            }
            $arrValues = [
                'product_id' => $productId,
                'name' => $productname,
                'extension' => $extension,
                'size' => $flInput['size']
            ];
            $this->model->insert_product_file($arrValues);
        }

        $data = [
            'title' => 'Registro exitoso',
            'message' => 'El producto se registró correctamente.',
            'type' => 'success',
            'icon' => 'success',
            'timer' => 2500,
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
        //VALIDACION DE PERMISOS
        validate_permission_app(3, "r", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }
        //obtenemos el id del producto
        $productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
        }
        $businessId = $this->getBusinessId();
        $product = $this->model->selectProduct($productId, $businessId);

        if (empty($product)) {
            $this->responseError('No se encontró el producto solicitado.');
        }
        //perpimision for history
        $history = validate_permission_app(16, "r", false, true, true);
        $productHistory = [];
        if ($history['status']) {
            $productHistory = $this->model->selectProductHistory($productId);
        }
        $currencySymbol = getCurrency();
        //obtenemos todas la imagenes del producto
        $images = $this->model->selectProductFile($productId);
        $stock = (float) round($product['stock'], 2);
        $stockText = $stock . ' ' . ($product['measurement_name'] ?? '');
        $purchasePrice = (float) round($product['purchase_price'], 2);
        $purchasePriceText = $currencySymbol . ' ' . ($purchasePrice);
        $salesPrice = (float) round($product['sales_price'], 2);
        $salesPriceText = $currencySymbol . ' ' . ($salesPrice);
        $data = [
            'status' => true,
            'data' => [
                'idProduct' => (int) $product['idProduct'],
                'category_id' => (int) $product['category_id'],
                'category_name' => $product['category_name'] ?? '',
                'measurement_id' => (int) $product['measurement_id'],
                'measurement_name' => $product['measurement_name'] ?? '',
                'supplier_id' => (int) $product['supplier_id'],
                'supplier_name' => $product['supplier_name'] ?? '',
                'name' => decodeUniversalText($product['name']),
                'stock' => $stock,
                'stock_text' => $stockText,
                'purchase_price' => $purchasePrice,
                'purchase_price_text' => $purchasePriceText,
                'sales_price' => $salesPrice,
                'sales_price_text' => $salesPriceText,
                'description' => decodeUniversalText($product['description'] ?? ''),
                'status' => $product['status'],
                'currency_symbol' => $currencySymbol,
                'images' => $images ?? [],
                'image_main' => $product['image_main'] ?? '',
                'image_main_url' => base_url() . '/Loadfile/iconproducts?f=' . $product['image_main'] ?? '',
                'is_public' => $product['is_public'] ?? 'No',
                'bar_code' => $product['bar_code'] ?? '',
                'barcode_format' => $product['bar_code_format'] ?? 'CODE128',
                'expiration_date' => $product['expiration_date'] ?? '',
                'product_history' => $productHistory ?? [],
                'permission_history' => $history
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
        //VALIDACION DE PERMISOS
        validate_permission_app(3, "u", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $userId = $this->getUserId();
        //validamos si chkProductStatus esta activo o inactivo
        if (isset($_POST['update_chkProductStatus']) && $_POST['update_chkProductStatus'] === 'on') {
            $statusChbx = 'Si';
        } else {
            $statusChbx = 'No';
        }
        $requiredFields = [
            'update_txtProductId',
            'update_txtProductName',
            'update_slctProductCategory',
            'update_slctProductMeasurement',
            'update_slctProductSupplier',
            'update_txtProductStock',
            'update_txtProductPurchasePrice',
            'update_txtProductSalesPrice',
            'update_txtProductCode',
            'update_slctBarcodeFormat'
        ];
        validateFields($requiredFields);
        $businessId = $this->getBusinessId();
        $productId = (int) ($_POST['update_txtProductId'] ?? 0);
        $name = ucwords(strClean($_POST['update_txtProductName'] ?? ''));
        $categoryId = (int) ($_POST['update_slctProductCategory'] ?? 0);
        $measurementId = (int) ($_POST['update_slctProductMeasurement'] ?? 0);
        $supplierId = (int) ($_POST['update_slctProductSupplier'] ?? 0);
        $stock = $this->resolveOptionalStock($_POST['update_txtProductStock'] ?? null);
        $purchase = $this->sanitizeDecimal($_POST['update_txtProductPurchasePrice'] ?? '0', 'precio de compra');
        $sales = $this->sanitizeDecimal($_POST['update_txtProductSalesPrice'] ?? '0', 'precio de venta');
        $status = 'Activo';
        $description = strClean($_POST['update_txtProductDescription'] ?? '');
        $dateExpiration = strClean($_POST['update_txtProductDateExpirated'] ?? '');
        $flInput = $_FILES['update_flInput'];
        $code = strClean($_POST['update_txtProductCode'] ?? '');
        $barcodeFormat = strClean($_POST['update_slctBarcodeFormat'] ?? 'CODE128');
        validateFieldsEmpty([
            'NOMBRE DEL PRODUCTO' => $name,
            'CODIGO DEL PRODUCTO' => $code,
            'CATEGORIA' => $categoryId,
            'UNIDAD DE MEDIDA' => $measurementId,
            'PROVEEDOR' => $supplierId,
            'PRECIO DE COMPRA' => $purchase,
            'PRECIO DE VENTA' => $sales,
            'FORMATO DE CODIGO DE BARRAS' => $barcodeFormat
        ]);
        //verificamos que el codigo tenga una longitud de 50 caracteres
        if (strlen($code) > 60) {
            $this->responseError('El código no puede exceder los 60 caracteres.');
        }
        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
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
        //validamos que el estock solo tenga 11 digitos
        if ($stock > 99999999.99) {
            $this->responseError('El stock no puede exceder los 11 dígitos.');
        }
        //validamos que el precio de compra solo tenga 11 digitos
        if ($purchase > 99999999.99) {
            $this->responseError('El precio de compra no puede exceder los 11 dígitos.');
        }
        //validamos que el precio de venta solo tenga 11 digitos
        if ($sales > 99999999.99) {
            $this->responseError('El precio de venta no puede exceder los 11 dígitos.');
        }

        if (!empty($dateExpiration) && !strtotime($dateExpiration)) {
            $this->responseError('La fecha de vencimiento no es válida.');
        }
        //validamos que el campo no este vacio
        if (!empty($flInput['name'])) {
            //validamos si el archivo es valido de acuerdo al tipo de archivo
            if (isFile('image', $flInput, ['png', 'jpg', 'jpeg'])) {
                $this->responseError('El archivo debe ser una imagen.');
            }
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

        $existingProductCode = $this->model->selectProductByCode($code, $businessId);
        if (!empty($existingProductCode) && (int) $existingProductCode['idProduct'] !== $productId) {
            $this->responseError('Ya existe otro producto con el mismo código de registro en tu negocio.');
        }

        $payload = [
            'category_id' => $categoryId,
            'name' => $name,
            'stock' => $stock,
            'purchase_price' => $purchase,
            'sales_price' => $sales,
            'measurement_id' => $measurementId,
            'description' => $description,
            'status' => $status,
            'supplier_id' => $supplierId,
            'is_public' => $statusChbx,
            'code' => $code,
            'expiration_date' => $dateExpiration,
            'user_id' => $this->getUserId(),
            'idProduct' => $productId,
            'barcode_format' => $barcodeFormat
        ];

        $updated = $this->model->updateProduct($payload);
        if (!$updated) {
            $this->responseError('No fue posible actualizar el producto, inténtalo nuevamente.');
        }
        $productHistoryId = $this->model->insertProductHistory($payload);
        if ($productHistoryId <= 0) {
            $this->responseError('No fue posible registrar el historial del producto, inténtalo nuevamente.');
        }

        //validamos que el campo no este vacio
        if (!empty($flInput['name'])) {
            //preparamos la ruta de almacenamiento del logo
            $urlFile = getRoute();
            verifyFolder($urlFile);
            $urlFile .= '/Products';
            verifyFolder($urlFile);
            $urlFile .= '/logo';
            verifyFolder($urlFile);
            $extension = pathinfo($flInput['name'], PATHINFO_EXTENSION);
            $productname = $productId . '-' . time() . '.' . $extension;
            $sizefile = valConvert($flInput['size'])['MB'];
            $urlFile .= '/' . $productname;
            if ($sizefile > 2) {
                resizeAndCompressImage($flInput['tmp_name'], $urlFile, 2);
            } else {
                move_uploaded_file($flInput['tmp_name'], $urlFile);
            }
            $arrValues = [
                'product_id' => $productId,
                'name' => $productname,
                'extension' => $extension,
                'size' => $flInput['size']
            ];
            $this->model->insert_product_file($arrValues);
        }
        $data = [
            'title' => 'Actualización exitosa',
            'message' => 'La información del producto se actualizó correctamente.',
            'type' => 'success',
            'icon' => 'success',
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
        //VALIDACION DE PERMISOS
        validate_permission_app(3, "d", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['id']);
        $productId = strClean($_POST['id']);
        $nameProduct = $_POST['name'];
        validateFieldsEmpty(['ID' => $productId]);
        if ($productId <= 0) {
            $this->responseError('Identificador de producto inválido.');
        }
        $businessId = $this->getBusinessId();
        $product = $this->model->selectProduct($productId, $businessId);

        if (empty($product)) {
            $this->responseError('El producto seleccionado no existe o no pertenece a tu negocio.');
        }
        //consultamos si el producto no esta relacionado con una venta
        $sale = $this->model->selectSaleProduct($productId);
        //consultamos si el producto no esta relacionado con un archivo
        $files = $this->model->selectProductFiles($productId);
        if (!empty($sale)) {
            $deleted = $this->model->updateProductStatus($productId, 'Inactivo');
        } else if ($files) {
            $deleted = $this->model->updateProductStatus($productId, 'Inactivo');
        } else {
            $deleted = $this->model->deleteProduct($productId);
        }

        if (!$deleted) {
            $this->responseError('No fue posible eliminar el producto, inténtalo nuevamente.');
        }

        $data = [
            'title' => 'Producto eliminado',
            'message' => 'Producto: ' . $nameProduct . ' se eliminó correctamente.',
            'type' => 'success',
            'icon' => 'success',
            'status' => true,
            'timer' => 2500,
        ];

        toJson($data);
    }
    /**
     * Metodo que se encarga de generar el codigo de producto
     */
    public function generateProductCode(): void
    {
        try {
            //VALIDACION DE PERMISOS con el id 3 para lectura
            validate_permission_app(3, "r", false, false, false);
            //obtenemos el id del negocio
            $userId = $this->getUserId();
            //obtenemos el nombre del negocio
            $businessName = $_SESSION[$this->nameVarBusiness]['business'];
            //obtenemos solo las iniciales del nombre asegurándonos de que sea un string
            $initialsBusiness = getInitials((string)$businessName, 0);
            //obtenemos el año actual
            $year = date('Y');
            //obtenemos el año, mes,dia, hora, minuto, segundo y milisegundo
            $date = date('YmdHis');
            //ahora preparamos el codigo
            $code = $userId . $year . "-" . $initialsBusiness . $date;

            toJson([
                'status' => true,
                'title' => 'Código generado',
                'timer' => 2500,
                'message' => 'Código generado correctamente.',
                'type' => 'success',
                'icon' => 'success',
                'code' => $code,
            ]);
        } catch (\Throwable $th) {
            // Si ocurre algún error, retornamos un JSON manejando la excepción
            $this->responseError('Ocurrió un error al generar el código. - ' . $th->getMessage());
        }
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
        if (empty($data)) {
            $this->responseError('No se encontraron categorías.');
        }
        toJson([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Devuelve el listado completo de categorías para el mantenimiento en el POS.
     *
     * @return void
     */
    public function getCategoryList(): void
    {
        //VALIDACION DE PERMISOS
        validate_permission_app(10, "r", false, false, false);
        $businessId = $this->getBusinessId();
        $data = $this->model->selectCategoryList($businessId);
        $validationUpdate =  validate_permission_app(10, "u", false)['update'];
        $validationDelete =  validate_permission_app(10, "d", false)['delete'];
        foreach ($data as $key => $value) {
            $data[$key]['name'] = decodeUniversalText($value['name']);
            $data[$key]['update'] = $validationUpdate;
            $data[$key]['delete'] = $validationDelete;
        }
        toJson($data);
    }

    /**
     * Registra una nueva categoría asociada al negocio activo.
     *
     * @return void
     */
    public function setCategory(): void
    {
        //VALIDACION DE PERMISOS
        validate_permission_app(10, "c", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['txtCategoryName']);
        $userId = $this->getUserId();
        $rawName = strClean($_POST['txtCategoryName'] ?? '');
        $name = ucwords(strClean($rawName));
        validateFieldsEmpty(['Nombre de la categoría' => $name]);
        //validamos que la categoria no sea mayor a  255 caracteres
        if (strlen($name) > 255) {
            $this->responseError('El nombre de la categoría no puede exceder los 255 caracteres.');
        }

        $businessId = $this->getBusinessId();
        $existing = $this->model->selectCategoryByName($name, $businessId);

        if (!empty($existing)) {
            $this->responseError('Ya existe una categoría con el mismo nombre en tu negocio.');
        }

        $categoryId = $this->model->insertCategory($businessId, $name);
        if ($categoryId <= 0) {
            $this->responseError('No fue posible registrar la categoría, inténtalo nuevamente.');
        }

        toJson([
            'title' => 'Categoría registrada',
            'message' => 'La categoría se registró correctamente.',
            'type' => 'success',
            'icon' => 'success',
            'status' => true,
            'timer' => 2500,
        ]);
    }

    /**
     * Actualiza el nombre de una categoría existente del negocio activo.
     *
     * @return void
     */
    public function updateCategory(): void
    {
        //VALIDACION DE PERMISOS
        validate_permission_app(10, "u", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $categoryId = (int) ($_POST['categoryId'] ?? 0);
        $rawName = strClean($_POST['txtCategoryName'] ?? '');
        $name = ucwords(strClean($rawName));

        if ($categoryId <= 0) {
            $this->responseError('No fue posible identificar la categoría seleccionada.');
        }

        if ($name === '') {
            $this->responseError('El nombre de la categoría es obligatorio.');
        }

        $businessId = $this->getBusinessId();
        $category = $this->model->findCategory($categoryId, $businessId);

        if (empty($category)) {
            $this->responseError('La categoría seleccionada no existe o no pertenece a tu negocio.');
        }

        if ($category['name'] === $this->protectedCategoryKey) {
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

        toJson([
            'title' => 'Categoría actualizada',
            'message' => 'La categoría se actualizó correctamente.',
            'type' => 'success',
            'icon' => 'success',
            'status' => true,
            'timer' => 2500,
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
        //VALIDACION DE PERMISOS
        validate_permission_app(10, "d", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['id']);
        $categoryId = (int) ($_POST['id'] ?? 0);
        $name = ($_POST['name'] ?? '');

        if ($categoryId <= 0) {
            $this->responseError('No fue posible identificar la categoría seleccionada.');
        }

        $businessId = $this->getBusinessId();
        $category = $this->model->findCategory($categoryId, $businessId);

        if (empty($category)) {
            $this->responseError('La categoría seleccionada no existe o no pertenece a tu negocio.');
        }

        if ($category['name'] === $this->protectedCategoryKey) {
            $this->responseError('No puedes eliminar la categoría predeterminada del sistema.');
        }

        $productsAssociated = $this->model->countProductsByCategory($categoryId, $businessId);

        if ($productsAssociated > 0) {
            $deactivated = $this->model->deactivateCategory($categoryId, $businessId);
            if (!$deactivated) {
                $this->responseError('No fue posible desactivar la categoría, inténtalo nuevamente.');
            }

            toJson([
                'title' => 'Categoría desactivada',
                'message' => 'La categoría: "' . $name . '" se eliminó correctamente.',
                'type' => 'success',
                'icon' => 'success',
                'status' => true,
            ]);

            return;
        }

        $deleted = $this->model->deleteCategory($categoryId, $businessId);
        if (!$deleted) {
            $this->responseError('No fue posible eliminar la categoría, inténtalo nuevamente.');
        }

        toJson([
            'title' => 'Categoría eliminada',
            'message' => 'La categoría: "' . $name . '" se eliminó correctamente.',
            'type' => 'success',
            'icon' => 'success',
            'status' => true,
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
            'data' => $data,
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
        $data = $this->model->selectSuppliers($businessId);

        toJson([
            'status' => true,
            'data' => $data,
        ]);
    }
    /**
     * Agrega un producto a la cola de impresión de códigos de barras.
     *
     * @return void
     */
    public function setProductInQueue(): void
    {
        validate_permission_app(17, "c", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['product', 'quantity']);
        $product = strClean($_POST['product'] ?? '');
        $quantity = (int) strClean($_POST['quantity'] ?? '');
        validateFieldsEmpty(['PRODUCTO' => $product, 'CANTIDAD' => $quantity]);
        if ($product === 'all') {
        }
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
     * Metodo que se encarga de eliminar la foto
     * del producto
     */
    public function deletePhotoImage()
    {
        //VALIDACION DE PERMISOS
        validate_permission_app(3, "u", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['id', 'name']);
        $id = (int) strClean($_POST['id']);
        $name = (string) strClean($_POST['name']);
        validateFieldsEmpty([
            'ID' => $id,
            'NOMBRES' => $name
        ]);
        //Validamos el id de manera numerica
        if (!is_numeric($id)) {
            $this->responseError("El id debe ser numerico");
        }
        $updateStatus = $this->model->update_status_product_file($id, 'Inactivo');
        if ($updateStatus) {
            toJson([
                'title' => 'Imagen eliminada',
                'message' => 'La imagen se elimino correctamente.',
                'type' => 'success',
                'icon' => 'success',
                'status' => true,
            ]);
        } else {
            $this->responseError('No fue posible eliminar la imagen, inténtalo nuevamente.');
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
            'title' => 'Ocurrió un error',
            'message' => $message,
            'type' => 'error',
            'icon' => 'error',
            'status' => false,
            'timer' => 2000
        ];

        toJson($data);
    }
}
