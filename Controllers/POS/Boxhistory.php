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
    /**
     * Metodo que se encarga de cargar todo el historial de cierres de caja
     * por empleado y por dueño del negocio
     * @return void
     */
    public function loadBoxHistory()
    {
        (!validate_permission_app(12, "r", false)['status']) ? toJson(validate_permission_app(12, "r", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $business_id = $this->getBusinessId();
        $request = $this->model->select_box_history($business_id);
        $cont = 1;
        foreach ($request as $key => $value) {
            $request[$key]['cont'] = $cont;
            $request[$key]['closing_date'] = dateFormat($value['closing_date']);
            $cont++;
        }
        toJson($request);
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
        $idBusiness = $this->getBusinessId();
        toJson($data);
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
}
