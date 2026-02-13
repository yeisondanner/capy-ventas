<?php

class Credits extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión que contiene la información del usuario POS.EWFDWF
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    /**
     * Clave normalizada del cliente protegido por defecto.
     *
     * @var string|null
     */
    private ?string $protectedCustomerKey = null;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }

    /**
     * Renderiza la vista principal de gestión de clientes.
     *
     * @return void
     */
    public function credits(): void
    {
        validate_permission_app(15, "r");
        $data = [
            'page_id' => 15,
            'page_title' => 'Historial de créditos',
            'page_description' => 'Administra los créditos registrados en tu negocio.',
            'page_container' => 'Credits',
            'page_view' => 'credits',
            'page_js_css' => 'credits'
        ];

        $this->views->getView($this, 'credits', $data, 'POS');
    }
    /**
     * Metodo que se encarga de obtener todos los creditos registrados en el negocio
     * 
     * @return void
     */
    public function getAllCreditsFilters()
    {
        validate_permission_app(15, "r", false, false, false);
        $businessId = $this->getBusinessId();
        $search = strClean($_GET['search'] ?? '');
        $startDate = strClean($_GET['startDate'] ?? '');
        $endDate = strClean($_GET['endDate'] ?? '');
        $data = $this->model->getCreditsWithFilters($businessId, $search, $startDate, $endDate);
        $cont = 1;
        foreach ($data as $key => $value) {
            $data[$key]['actions'] = '';
            $data[$key]['cont'] = $cont;
            $data[$key]["amount_pending"] = (float) $value["amount_pending"];
            $data[$key]["credit_limit"] = (float) $value["credit_limit"];
            $cont++;
        }
        toJson($data);
    }
    /**
     * Metodo que se encarga de obtener la informacion del cliente y sus creditos
     * 
     * @return void
     */
    public function getInfoCustomerAndCredits()
    {
        validate_permission_app(15, "r", false, false, false);
        validateFields(["idCustomer", "startDate", "endDate"]);
        $idCustomer = strClean($_POST['idCustomer']);
        $startDate = ($_POST['startDate']);
        $endDate = ($_POST['endDate']);
        validateFieldsEmpty(["ID del cliente" => $idCustomer]);
        $idBusiness = (int) $this->getBusinessId();
        $dataCustomer = $this->model->getInfoCustomer($idCustomer, $idBusiness);
        $dataKPIS = $this->model->getKPISCustomer($idCustomer, $idBusiness, $startDate, $endDate);
        $dataCredits = $this->model->getCreditsCustomer($idCustomer, $idBusiness, $startDate, $endDate);
        $arrData = [
            'customer' => $dataCustomer,
            'customerSales' => $dataCredits,
            'kpis' => [
                "total_ventas" => $dataKPIS['total_ventas'] ?? 0.00,
                "total_pagado" => $dataKPIS['total_pagado'] ?? 0.00,
                "total_pendiente" => $dataKPIS['total_pendiente'] ?? 0.00
            ],
            'status' => true
        ];
        toJson($arrData);
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
            'title' => 'Ocurrió un error',
            'message' => $message,
            'type' => 'error',
            'icon' => 'error',
            'status' => false,
        ];

        toJson($data);
    }
}
