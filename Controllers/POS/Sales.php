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
}
