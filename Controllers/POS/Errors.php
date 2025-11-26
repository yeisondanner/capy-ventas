<?php
class Errors extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión con los datos del usuario POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;
    /**
     * Construye el controlador y garantiza la carga del modelo correspondiente.
     */
    public function __construct()
    {

        parent::__construct('POS');
        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness  = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Renderiza la página de inicio pública utilizando contenido estático.
     *
     * @return void
     */
    public function plan_vencido()
    {
        isSession(1);
        $data = [
            'page_id'          => 0,
            'page_title'       => 'Plan vencido',
            'page_description' => 'Plan de vencido.',
            'page_container'   => 'Errors',
            'page_view'        => 'plan_vencido',
            'page_js_css'      => 'plan_vencido',
        ];
        $this->views->getView($this, "plan_vencido", $data, "POS");
    }
}
