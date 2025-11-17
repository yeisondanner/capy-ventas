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
        $data = [
            'page_id'          => 0,
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
        $businessId = $this->getBusinessId();
        $products   = $this->model->selectProducts($businessId);
        if (!$products) {
            $this->responseError('No se encontraron productos en el negocio.');
        }
        toJson(['products' => $products, 'status' => true]);
    }
    /**
     * Agrega un producto al carrito de compras.
     *
     * @return void
     */
    public function addCart(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $idproduct    = strClean($_POST['idproduct']);
        $idsupplier   = strClean($_POST['idsupplier']);
        $idmeasurement = strClean($_POST['idmeasurement']);
        $idcategory   = strClean($_POST['idcategory']);
        $price        = (float) strClean($_POST['price']);
        $product      = strClean($_POST['product']);
        $stock        = (float) strClean($_POST['stock']);
        $supplier     = strClean($_POST['supplier']);
        $category     = strClean($_POST['category']);
        $selected     = max(1, (int) strClean($_POST['selected']));
        $measurement  = strClean($_POST['measurement']);
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        if (!isset($_SESSION[$this->nameVarCart])) {
            $this->responseError('El carrito está vacío.');
        }

        $idproduct = strClean($_POST['idproduct'] ?? '');
        $action    = strClean($_POST['action'] ?? '');

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        if (!isset($_SESSION[$this->nameVarCart])) {
            $this->responseError('El carrito está vacío.');
        }

        $idproduct = strClean($_POST['idproduct'] ?? '');
        if ($idproduct === '') {
            $this->responseError('El producto es obligatorio.');
        }

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
}
