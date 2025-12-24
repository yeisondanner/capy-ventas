<?php

class Boxhistory extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena la información del usuario en POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    /**
     * Nombre de la variable de sesión que almacena el negocio activo en POS.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }
    /**
     * Metodo que se encarga de renderizar la vista 
     * @return void
     */
    public function boxhistory()
    {
        validate_permission_app(12, "r");
        $data = [
            'page_id'          => 12,
            'page_title'       => 'Historial de cajas',
            'page_description' => 'Administra los clientes registrados en tu negocio.',
            'page_container'   => 'Boxhistory',
            'page_view'        => 'boxhistory',
            'page_js_css'      => 'boxhistory',
        ];
        $this->views->getView($this, 'boxhistory', $data, 'POS');
    }
}
