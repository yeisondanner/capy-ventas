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
        $idproduct = strClean($_POST['idproduct']);
        $idsupplier = strClean($_POST['idsupplier']);
        $idmeasurement = strClean($_POST['idmeasurement']);
        $idcategory = strClean($_POST['idcategory']);
        $price = strClean($_POST['price']);
        $product = strClean($_POST['product']);
        $stock = strClean($_POST['stock']);
        $supplier = strClean($_POST['supplier']);
        $category = strClean($_POST['category']);
        $selected = strClean($_POST['selected']);
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
            );
        } else {
            $length = count($_SESSION[$this->nameVarCart]);
            //validamos si el producto ya esta en el carrito
            foreach ($_SESSION[$this->nameVarCart] as $key => $item) {
                // --- 5. Comprueba la doble condición ---
                if ($item['idproduct'] == $idproduct && $item['product'] == $product) {
                    $_SESSION[$this->nameVarCart][$key]['selected'] = $selected;
                    toJson([
                        'status' => true,
                        'title' => 'Cantidad actualizada.',
                        'message' => $selected . ' ' . $product . ' agregado al carrito.',
                        'icon' => 'success'
                    ]);
                    break; // ¡Encontrado! No necesitas seguir buscando.
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
            );
        }
        toJson([
            'status' => true,
            'title' => 'Producto agregado al carrito.',
            'message' => $selected . ' ' . $product . ' agregado al carrito.',
            'icon' => 'success'
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
}
