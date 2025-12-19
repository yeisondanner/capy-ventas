<?php

class Sales extends Controllers
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
     * Nombre de la variable de sesion que contiene la informacion del carrito
     * 
     * @var string
     */
    protected string $nameVarCart;
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
        $this->nameVarCart = $sessionName . 'cart';
    }

    /**
     * Renderiza la vista principal de gestión de proveedores.
     *
     * @return void
     */
    public function sales(): void
    {
        validate_permission_app(1, "r");
        $data = [
            'page_id'          => 1,
            'page_title'       => 'Nueva venta',
            'page_description' => 'Realiza tus ventas en esta sección.',
            'page_container'   => 'Sales',
            'page_view'        => 'sales',
            'page_js_css'      => 'sales',
        ];

        $this->views->getView($this, 'sales', $data, 'POS');
    }
    /**
     * Obtiene los productos del negocio.
     *
     * @return void
     */
    public function getProducts(): void
    {
        validate_permission_app(1, "r", false);
        $businessId = $this->getBusinessId();
        $products   = $this->model->selectProducts($businessId);
        if (!$products) {
            $this->responseError('No se encontraron productos en el negocio.');
        }
        toJson(['products' => $products, 'status' => true]);
    }

    /**
     * Devuelve las categorías más vendidas del negocio activo.
     *
     * @return void
     */
    public function getPopularCategories(): void
    {
        validate_permission_app(1, "r");
        $businessId = $this->getBusinessId();
        $categories = $this->model->selectPopularCategories($businessId, 5);
        if (!is_array($categories) || count($categories) === 0) {
            toJson([
                'status'     => true,
                'categories' => [],
            ]);

            return;
        }

        $response = array_map(static function ($category) {
            return [
                'idcategory' => (int) ($category['idCategory'] ?? 0),
                'category'   => (string) ($category['category'] ?? ''),
                'total_sold' => (float) ($category['total_sold'] ?? 0),
            ];
        }, $categories);

        toJson([
            'status'     => true,
            'categories' => $response,
        ]);
    }

    /**
     * Devuelve los clientes vinculados al negocio que inició sesión.
     *
     * @return void
     */
    public function getCustomers(): void
    {
        validate_permission_app(1, "r");
        $businessId = $this->getBusinessId();
        $customers  = $this->model->selectCustomers($businessId);

        if (!is_array($customers) || count($customers) === 0) {
            toJson([
                'status'    => true,
                'customers' => [],
            ]);

            return;
        }

        $response = array_map(static function ($customer) {
            return [
                'id'             => (int) ($customer['idCustomer'] ?? 0),
                'name'           => (string) ($customer['fullname'] ?? ''),
                'document'       => (string) ($customer['document_number'] ?? ''),
                'document_type'  => (string) ($customer['document_type'] ?? ''),
            ];
        }, $customers);

        toJson([
            'status'    => true,
            'customers' => $response,
        ]);
    }
    /**
     * Agrega un producto al carrito de compras.
     *
     * @return void
     */
    public function addCart(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "c", false)['status']) ? toJson(validate_permission_app(1, "c", false)) : '';
        //VALIDACION DE METODO POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields([
            'idproduct',
            'idsupplier',
            'idmeasurement',
            'idcategory',
            'price',
            'purchase_price',
            'product',
            'stock',
            'supplier',
            'category'
        ]);
        $idproduct    = strClean($_POST['idproduct']);
        $idsupplier   = strClean($_POST['idsupplier']);
        $idmeasurement = strClean($_POST['idmeasurement']);
        $idcategory   = strClean($_POST['idcategory']);
        $price        = (float) strClean($_POST['price']);
        $purchasePrice = (float) strClean($_POST['purchase_price'] ?? 0);
        $product      = strClean($_POST['product']);
        $stock        = (float) strClean($_POST['stock']);
        $supplier     = strClean($_POST['supplier']);
        $category     = strClean($_POST['category']);
        $selected     = max(1, (int) strClean($_POST['selected']));
        $measurement  = strClean($_POST['measurement']);
        validateFieldsEmpty([
            'ID PRODUCTO' => $idproduct,
            'ID PROVEEDOR' => $idsupplier,
            'ID MEDIDIMIENTO' => $idmeasurement,
            'ID CATEGORIA' => $idcategory,
            'PRECIO' => $price,
            'PRECIO COMPRA' => $purchasePrice,
            'PRODUCTO' => $product,
            'PROVEEDOR' => $supplier,
            'CATEGORIA' => $category,
            'SELECCIONADO' => $selected,
            'UNIDAD MEDIDA' => $measurement
        ]);
        if ($stock > 0) {
            $selected = min($selected, (int) $stock);
        }
        $userId = $this->getUserId();
        if (!isset($_SESSION[$this->nameVarCart])) {
            $_SESSION[$this->nameVarCart][0] = array(
                'idproduct' => $idproduct,
                'idsupplier' => $idsupplier,
                'idmeasurement' => $idmeasurement,
                'idcategory' => $idcategory,
                'price' => $price,
                'purchase_price' => $purchasePrice,
                'product' => $product,
                'stock' => $stock,
                'supplier' => $supplier,
                'category' => $category,
                'selected' => $selected,
                'userId' => $userId,
                'measurement' => $measurement
            );
        } else {
            $length = count($_SESSION[$this->nameVarCart]);
            //validamos si el producto ya esta en el carrito
            foreach ($_SESSION[$this->nameVarCart] as $key => $item) {
                // --- 5. Comprueba la doble condición ---
                if ($item['idproduct'] == $idproduct && $item['product'] == $product) {
                    $_SESSION[$this->nameVarCart][$key]['selected'] = $selected;
                    toJson($this->getCartPayload('Cantidad actualizada.', $product, $selected));

                    return;
                }
            }
            $_SESSION[$this->nameVarCart][$length] = array(
                'idproduct' => $idproduct,
                'idsupplier' => $idsupplier,
                'idmeasurement' => $idmeasurement,
                'idcategory' => $idcategory,
                'price' => $price,
                'purchase_price' => $purchasePrice,
                'product' => $product,
                'stock' => $stock,
                'supplier' => $supplier,
                'category' => $category,
                'selected' => $selected,
                'userId' => $userId,
                'measurement' => $measurement
            );
        }
        toJson($this->getCartPayload('Producto agregado al carrito.', $product, $selected));
        return;
    }
    /**
     * Metodo que se encarga de obtener todos los productos del carrito
     *
     * @return void
     */
    public function getCart(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "r", false)['status']) ? toJson(validate_permission_app(1, "r", false)) : '';
        $cart = $this->normalizeCart();
        $subtotal = $this->calculateSubtotal($cart);
        toJson([
            'cart' => $cart,
            'subtotal' => $subtotal,
            'status' => true,
        ]);
    }

    /**
     * Actualiza la cantidad seleccionada de un producto dentro del carrito.
     *
     * @return void
     */
    public function updateCartItem(): void
    {
        validate_permission_app(1, "u");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        if (!isset($_SESSION[$this->nameVarCart])) {
            $this->responseError('El carrito está vacío.');
        }

        $idproduct = strClean($_POST['idproduct'] ?? '');
        $action    = strClean($_POST['action'] ?? '');
        $quantity  = $_POST['quantity'] ?? null;

        if ($idproduct === '' || $action === '') {
            $this->responseError('Los datos del producto son obligatorios.');
        }

        foreach ($_SESSION[$this->nameVarCart] as $index => $item) {
            if ($item['idproduct'] !== $idproduct) {
                continue;
            }

            $current  = (int) $item['selected'];
            $stock    = (float) $item['stock'];
            $newValue = $current;

            if ($action === 'increment') {
                if ($stock > 0 && $current >= $stock) {
                    $this->responseError('No hay más stock disponible para este producto.');
                }
                $newValue = $stock > 0 ? min($current + 1, (int) $stock) : $current + 1;
            } elseif ($action === 'decrement') {
                $newValue = max($current - 1, 1);
            } elseif ($action === 'set') {
                if ($quantity === null || $quantity === '') {
                    $this->responseError('La cantidad es obligatoria.');
                }

                if (!is_numeric($quantity)) {
                    $this->responseError('La cantidad ingresada no es válida.');
                }

                $requested = (int) $quantity;

                if ($requested <= 0) {
                    unset($_SESSION[$this->nameVarCart][$index]);
                    $_SESSION[$this->nameVarCart] = $this->normalizeCart();
                    toJson($this->getCartPayload('Producto eliminado del carrito.', $item['product'], 0));

                    return;
                }

                $newValue = $stock > 0 ? min($requested, (int) $stock) : $requested;
            } else {
                $this->responseError('Acción no reconocida.');
            }

            $_SESSION[$this->nameVarCart][$index]['selected'] = $newValue;

            toJson($this->getCartPayload('Cantidad actualizada.', $item['product'], $newValue));

            return;
        }

        $this->responseError('Producto no encontrado en el carrito.');
    }

    /**
     * Elimina un producto específico del carrito.
     *
     * @return void
     */
    public function deleteCartItem(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "d", false)['status']) ? toJson(validate_permission_app(1, "d", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['idproduct']);
        if (!isset($_SESSION[$this->nameVarCart])) {
            $this->responseError('El carrito está vacío.');
        }
        $idproduct = strClean($_POST['idproduct'] ?? '');
        validateFieldsEmpty(['ID PRODUCTO' => $idproduct]);
        foreach ($_SESSION[$this->nameVarCart] as $index => $item) {
            if ($item['idproduct'] !== $idproduct) {
                continue;
            }

            unset($_SESSION[$this->nameVarCart][$index]);
            $_SESSION[$this->nameVarCart] = array_values($_SESSION[$this->nameVarCart]);

            toJson($this->getCartPayload('Producto eliminado del carrito.', $item['product'], 0));

            return;
        }

        $this->responseError('No se encontró el producto en el carrito.');
    }

    /**
     * Vacía por completo el carrito de compras.
     *
     * @return void
     */
    public function clearCart(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "d", false)['status']) ? toJson(validate_permission_app(1, "d", false)) : '';
        if (isset($_SESSION[$this->nameVarCart])) {
            unset($_SESSION[$this->nameVarCart]);
        }

        toJson([
            'status' => true,
            'title'  => 'Carrito vaciado',
            'message' => 'Todos los productos fueron eliminados de la canasta.',
            'icon'   => 'success',
            'cart'   => [],
            'subtotal' => 0,
        ]);
    }
    /**
     * Obtiene el identificador del negocio activo desde la sesión.
     *
     * @return int
     */
    private function getBusinessId(): int
    {
        validate_permission_app(1, "r");
        if (!isset($_SESSION[$this->nameVarBusiness]['idBusiness'])) {
            $this->responseError('No se encontró el negocio activo en la sesión.');
        }
        return (int) $_SESSION[$this->nameVarBusiness]['idBusiness'];
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
     * Calcula el subtotal del carrito.
     *
     * @param array $cart Lista de productos en el carrito.
     *
     * @return float
     */
    private function calculateSubtotal(array $cart): float
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += (float) $item['price'] * (int) $item['selected'];
        }

        return round($subtotal, 2);
    }

    /**
     * Devuelve el carrito normalizado para evitar saltos de índices.
     *
     * @return array
     */
    private function normalizeCart(): array
    {
        if (!isset($_SESSION[$this->nameVarCart])) {
            return [];
        }
        //valida que el stock no sea menor o igual a cero
        foreach ($_SESSION[$this->nameVarCart] as $item) {
            if ($item['stock'] <= 0) {
                $this->responseError('El stock del producto ' . $item['name'] . ' no es suficiente.');
            }
        }
        return array_values($_SESSION[$this->nameVarCart]);
    }

    /**
     * Construye la respuesta estándar del carrito incluyendo el subtotal.
     *
     * @param string $title    Título descriptivo de la acción realizada.
     * @param string $product  Nombre del producto afectado.
     * @param int    $selected Cantidad seleccionada del producto.
     *
     * @return array
     */
    private function getCartPayload(string $title, string $product, int $selected): array
    {
        $cart = $this->normalizeCart();
        $subtotal = $this->calculateSubtotal($cart);

        return [
            'status' => true,
            'title'  => $title,
            'message' => $selected > 0
                ? $selected . ' ' . $product . ' en la canasta.'
                : $product . ' eliminado de la canasta.',
            'icon'      => 'success',
            'cart'      => $cart,
            'subtotal'  => $subtotal,
        ];
    }

    /**
     * Registra la venta en las tablas voucher_header y voucher_detail.
     *
     * @return void
     */
    public function finalizeSale(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "c", false)['status']) ? toJson(validate_permission_app(1, "c", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $cart = $this->normalizeCart();
        if (count($cart) === 0) {
            $this->responseError('No hay productos en el carrito para registrar la venta.');
        }
        $saleDate          = strClean($_POST['saleDate'] ?? '');
        $paymentMethodId   = (int) strClean($_POST['paymentMethodId'] ?? '0');
        $customerId        = (int) strClean($_POST['customerId'] ?? '0');
        $voucherName       = trim(strClean($_POST['voucherName'] ?? ''));
        $discountAmount    = (float) strClean($_POST['discountAmount'] ?? 0);
        $discountPercent   = (float) strClean($_POST['discountPercentage'] ?? 0);
        $paidAmount        = max(0, (float) strClean($_POST['paidAmount'] ?? 0));

        if ($paymentMethodId <= 0) {
            $this->responseError('Selecciona un método de pago válido.');
        }

        $businessId = $this->getBusinessId();
        $subtotal   = $this->calculateSubtotal($cart);

        $discountAmount  = max(0, min($discountAmount, $subtotal));
        $discountPercent = $subtotal > 0
            ? min(round(($discountAmount / $subtotal) * 100, 2), 100)
            : 0;
        $totalAmount     = max($subtotal - $discountAmount, 0);

        $saleDateTime = date('Y-m-d H:i:s');
        if ($saleDate !== '') {
            $date = DateTime::createFromFormat('Y-m-d', $saleDate);
            if ($date !== false) {
                $saleDateTime = $date->format('Y-m-d') . ' ' . date('H:i:s');
            }
        }

        $paymentMethod = $this->model->selectPaymentMethod($paymentMethodId);
        if (!$paymentMethod) {
            $this->responseError('El método de pago elegido no existe o está inactivo.');
        }

        $businessInfo = $this->model->selectBusinessById($businessId);
        if (!$businessInfo) {
            $this->responseError('No se encontró la información del negocio para registrar la venta.');
        }

        $customerInfo = $customerId > 0
            ? $this->model->selectCustomerById($customerId, $businessId)
            : null;

        $customerName      = $customerInfo['fullname'] ?? 'Sin cliente';
        $customerDirection = $customerInfo['direction'] ?? 'Sin dirección';

        $productIds = array_values(array_unique(array_map(static function ($item) {
            return (int) ($item['idproduct'] ?? 0);
        }, $cart)));

        if (count($productIds) === 0) {
            $this->responseError('No se encontraron productos válidos para registrar en la venta.');
        }

        $productsInfo = $this->model->selectProductsForVoucher($productIds, $businessId);
        $productsById = [];

        foreach ($productsInfo as $product) {
            $productsById[(int) ($product['idproduct'] ?? 0)] = $product;
        }

        $headerId = $this->model->insertVoucherHeader([
            'name_customer'      => $customerName,
            'direction_customer' => $customerDirection,
            'name_bussines'      => (string) ($businessInfo['name'] ?? ''),
            'document_bussines'  => (string) ($businessInfo['document_number'] ?? ''),
            'direction_bussines' => (string) ($businessInfo['direction'] ?? ''),
            'date_time'          => $saleDateTime,
            'amount'             => $totalAmount,
            'percentage_discount' => $discountPercent,
            'fixed_discount'     => $discountAmount,
            'how_much_do_i_pay'  => $paidAmount,
            'voucher_name'       => $voucherName !== '' ? $voucherName : null,
            'payment_method_id'  => $paymentMethodId,
            'business_id'        => $businessId,
            'user_app_id'        => $this->getUserId(),
        ]);

        if ($headerId <= 0) {
            $this->responseError('No fue posible registrar la cabecera de la venta.');
        }

        foreach ($cart as $item) {
            $productId = (int) ($item['idproduct'] ?? 0);
            if ($productId === 0) {
                continue;
            }

            $quantity = max(0, (float) ($item['selected'] ?? 0));
            if ($quantity === 0.0) {
                continue;
            }

            $productData   = $productsById[$productId] ?? [];
            $salesPrice    = (float) ($item['price'] ?? 0);
            $purchasePrice = (float) ($productData['purchase_price'] ?? ($item['purchase_price'] ?? 0));
            $detailSubtotal = round($salesPrice * $quantity, 2);

            $detailId = $this->model->insertVoucherDetail([
                'product_id'             => $productId,
                'voucherheader_id'       => $headerId,
                'name_product'           => (string) ($productData['product'] ?? ($item['product'] ?? '')),
                'unit_of_measurement'    => (string) ($productData['measurement'] ?? ($item['measurement'] ?? '')),
                'name_category'          => (string) ($productData['category'] ?? ($item['category'] ?? '')),
                'sales_price_product'    => $salesPrice,
                'purchase_price_product' => $purchasePrice,
                'stock_product'          => $quantity,
                'subtotal'               => $detailSubtotal,
            ]);

            if ($detailId <= 0) {
                $this->responseError('No fue posible registrar el detalle de la venta.');
            }

            $stockUpdated = $this->model->decreaseProductStock($productId, $businessId, $quantity);
            if (!$stockUpdated) {
                $this->responseError('No fue posible actualizar el stock del producto vendido.');
            }
        }

        unset($_SESSION[$this->nameVarCart]);

        toJson([
            'status'  => true,
            'title'   => 'Venta registrada',
            'message' => 'La venta se registró correctamente.',
            'icon'    => 'success',
            'sale_id' => $headerId,
            'voucher_name' => $voucherName !== '' ? $voucherName : null,
            'total'   => $totalAmount,
        ]);
    }

    /**
     * Actualiza el nombre del voucher recién generado tras finalizar la venta.
     *
     * @return void
     */
    public function updateVoucherName(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(1, "u", false)['status']) ? toJson(validate_permission_app(1, "u", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $voucherId  = (int) strClean($_POST['saleId'] ?? '0');
        $voucherName = trim(strClean($_POST['voucherName'] ?? ''));

        if ($voucherId <= 0) {
            $this->responseError('El identificador de la venta no es válido.');
        }

        if ($voucherName === '') {
            $this->responseError('El nombre del comprobante es obligatorio.');
        }

        $businessId = $this->getBusinessId();

        $voucher = $this->model->selectVoucherById($voucherId, $businessId);
        if (!$voucher) {
            $this->responseError('No se encontró el comprobante solicitado para tu negocio.');
        }

        $updated = $this->model->updateVoucherName($voucherId, $voucherName, $businessId);

        if (!$updated) {
            $this->responseError('No se pudo actualizar el nombre del comprobante.');
        }

        toJson([
            'status'       => true,
            'title'        => 'Nombre actualizado',
            'message'      => 'Se guardó el nombre del voucher generado.',
            'icon'         => 'success',
            'sale_id'      => $voucherId,
            'voucher_name' => $voucherName,
        ]);
    }
    /**
     * Metodo que se encarga de otbener todos los metodos de pagos
     * 
     * @return void
     */
    public function getPaymentMethods(): void
    {
        validate_permission_app(1, "r");
        $paymentMethods = $this->model->selectPaymentMethods();
        toJson(['status' => true, 'payment_methods' => $paymentMethods]);
    }
}
