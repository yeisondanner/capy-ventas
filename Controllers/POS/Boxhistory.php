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
        //Obtenemos el id del negocio activo
        $business_id = $this->getBusinessId();
        //obtenemos los filtros
        $filterType = $_GET['filterType'] ?? 'daily';
        switch ($filterType) {
            case 'daily':
                $minDate = $_GET['filterDate'] ?? date('Y-m-d');
                $maxDate = $_GET['filterDate'] ?? date('Y-m-d');
                break;
            case 'weekly':
                if (isset($_GET['filterDate']) && !empty($_GET['filterDate'])) {
                    // El formato esperado es YYYY-Www (ej. 2025-W01)
                    $parts = explode("-W", $_GET['filterDate']);
                    $year = (int)$parts[0];
                    $weekNumber = (int)$parts[1];

                    $dto = new DateTime();

                    // setISODate establece la fecha basada en: Año, Semana, Día de la semana (1 = Lunes)
                    $dto->setISODate($year, $weekNumber, 1);
                    $minDate = $dto->format('Y-m-d');

                    // Sumamos 6 días para llegar al domingo
                    $dto->modify('+6 days');
                    $maxDate = $dto->format('Y-m-d');
                } else {
                    $minDate = date('Y-m-d', strtotime('monday this week'));
                    $maxDate = date('Y-m-d', strtotime('sunday this week'));
                }
                break;
            case 'monthly':
                $minDate = isset($_GET['filterDate']) ? $_GET['filterDate'] . date("-01") : date('Y-m-d', strtotime('first day of this month'));
                $maxDate = isset($_GET['filterDate']) ? $_GET['filterDate'] . date("-t") : date('Y-m-d', strtotime('last day of this month'));
                break;
            case 'yearly':
                $minDate = isset($_GET['filterDate']) ? $_GET['filterDate'] . date("-01-01") : date("Y-m-d", strtotime("first day of january this year"));
                $maxDate = isset($_GET['filterDate']) ? $_GET['filterDate'] . date("-12-31") : date("Y-m-d", strtotime("last day of december this year"));
                break;
            case 'custom':
                $minDate = $_GET['minDate'] ?? date("Y-m-01");
                $maxDate = $_GET['maxDate'] ?? date("Y-m-t");
                break;
            case 'all':
                $minDate = null;
                $maxDate = null;
                break;
            default:
                $minDate = $_GET['filterDate'] ?? date('Y-m-d');
                $maxDate = $_GET['filterDate'] ?? date('Y-m-d');
                break;
        }
        $request = $this->model->select_box_history($business_id, $minDate, $maxDate);
        $cont = 1;
        foreach ($request as $key => $value) {
            $request[$key]['cont'] = $cont;
            $request[$key]['closing_date'] = dateFormat($value['closing_date']);
            $cont++;
        }
        toJson($request);
    }
    /**
     * Obtiene los detalles de una sesión de caja específica vía POST.
     * @return void
     */
    public function getBoxSession()
    {
        if (!$_POST) {
            $this->responseError('Solicitud inválida.');
        }

        $idBoxSession = intval($_POST['idBoxSession'] ?? 0);

        if ($idBoxSession <= 0) {
            $this->responseError('Identificador de sesión no válido.');
        }

        $businessId = $this->getBusinessId();

        $data = $this->model->getBoxSessionDetails($idBoxSession, $businessId);

        if (empty($data)) {
            $arrResponse = [
                'status' => false,
                'msg'    => 'No se encontraron datos para este cierre de caja.'
            ];
            toJson($arrResponse);
            return;
        }

        // Formatear datos para la vista
        $data['opening_date'] = dateFormat($data['opening_date']);
        $data['closing_date'] = dateFormat($data['closing_date']);

        // Formatear historial de arqueos
        foreach ($data['counts_history'] as $key => $count) {
            $data['counts_history'][$key]['date_time'] = dateFormat($count['date_time']);
            $data['counts_history'][$key]['type'] = ucfirst($count['type']);
        }

        // Formatear historial de movimientos
        foreach ($data['movements_history'] as $key => $movement) {
            $data['movements_history'][$key]['created_at'] = dateFormat($movement['created_at']);
            $data['movements_history'][$key]['type_movement'] = ucfirst($movement['type_movement']);
        }

        // Agregar logo URL
        $data['logo_url'] = base_url() . '/Loadfile/iconbusiness?f=' . $data['logo'];

        $arrResponse = [
            'status' => true,
            'data'   => $data
        ];

        toJson($arrResponse);
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
