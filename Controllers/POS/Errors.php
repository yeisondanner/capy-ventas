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
    public function no_permisos()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'No tienes permisos',
            'page_description' => 'No tienes permisos.',
            'page_container'   => 'Errors',
            'page_view'        => 'no_permisos',
            'page_js_css'      => 'no_permisos',
        ];
        $this->views->getView($this, "no_permisos", $data, "POS");
    }
    public function estado_interfaz()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'No tienes permisos',
            'page_description' => 'No tienes permisos.',
            'page_container'   => 'Errors',
            'page_view'        => 'estado_interfaz',
            'page_js_css'      => 'estado_interfaz',
        ];
        $this->views->getView($this, "estado_interfaz", $data, "POS");
    }
    public function estado_permisos()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'No tienes permisos',
            'page_description' => 'No tienes permisos.',
            'page_container'   => 'Errors',
            'page_view'        => 'estado_permisos',
            'page_js_css'      => 'estado_permisos',
        ];
        $this->views->getView($this, "estado_permisos", $data, "POS");
    }
    public function estado_plan_interfaz()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'No tienes permisos',
            'page_description' => 'No tienes permisos.',
            'page_container'   => 'Errors',
            'page_view'        => 'estado_plan_interfaz',
            'page_js_css'      => 'estado_plan_interfaz',
        ];
        $this->views->getView($this, "estado_plan_interfaz", $data, "POS");
    }
    public function no_permisos_pia()
    {
        $data = [
            'page_id'          => 0,
            'page_title'       => 'No tienes permisos',
            'page_description' => 'No tienes permisos.',
            'page_container'   => 'Errors',
            'page_view'        => 'no_permisos_pia',
            'page_js_css'      => 'no_permisos_pia',
        ];
        $this->views->getView($this, "no_permisos_pia", $data, "POS");
    }
}
