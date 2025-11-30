<?php

class Movements extends Controllers
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
     * Clave normalizada de la categoría protegida por defecto.
     *
     * @var string|null
     */
    private ?string $protectedCategoryKey = null;

    public function __construct()
    {
        isSession(1);
        parent::__construct("POS");

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness   = $sessionName . 'business_active';
        $this->nameVarLoginInfo  = $sessionName . 'login_info';
    }

    public function movements()
    {
        $businessId = $this->getBusinessId();

        // Por defecto, usar filtros para hoy al cargar la página
        $minDate = date('Y-m-d');
        $maxDate = date('Y-m-d');
        $searchConcept = null; // Por defecto no hay búsqueda

        $totals = $this->model->getTotals($businessId, $minDate, $maxDate, $searchConcept);

        $data = [
            'page_id'          => 2,
            'page_title'       => 'Inventario de productos',
            'page_description' => 'Gestiona los productos disponibles en tu negocio.',
            'page_container'   => 'Movements',
            'page_view'        => 'movements',
            'page_js_css'      => 'movements',
            'totals'           => $totals,
        ];
        $this->views->getView($this, "movements", $data, "POS");
    }

    /**
     * Devuelve los movimientos (ventas) del negocio activo para DataTables.
     *
     * @return void
     */
    public function getMovements(): void
    {
        // ID del negocio desde la sesión
        $businessId = $this->getBusinessId();

        // Filtros de fecha
        $minDate = (isset($_GET["minDate"]) && !empty($_GET["minDate"])) ? strClean($_GET['minDate']) : null;
        $maxDate = (isset($_GET["maxDate"]) && !empty($_GET["maxDate"])) ? strClean($_GET['maxDate']) : null;
        $filterType = (isset($_GET["filterType"]) && !empty($_GET["filterType"])) ? strClean($_GET['filterType']) : 'daily';
        $searchConcept = (isset($_GET["searchConcept"]) && !empty($_GET["searchConcept"])) ? strClean($_GET['searchConcept']) : null;

        // Calcular fechas según el tipo de filtro SI NO se han enviado fechas específicas
        if ($filterType !== 'custom') {
            // Si se ha seleccionado un tipo de filtro diferente a personalizado Y no se han enviado fechas específicas en minDate y maxDate
            // entonces calcular las fechas predeterminadas
            if ($minDate === null && $maxDate === null) {
                switch ($filterType) {
                    case 'daily':
                        $minDate = date('Y-m-d');
                        $maxDate = date('Y-m-d');
                        break;
                    case 'weekly':
                        $minDate = date('Y-m-d', strtotime('monday this week'));
                        $maxDate = date('Y-m-d', strtotime('sunday this week'));
                        break;
                    case 'monthly':
                        $minDate = date('Y-m-01');
                        $maxDate = date('Y-m-t');
                        break;
                    case 'yearly':
                        $minDate = date('Y-01-01');
                        $maxDate = date('Y-12-31');
                        break;
                }
            }
            // Si se han enviado fechas específicas, se usan esas fechas y no se sobrescriben
        }

        // Traemos solo los movimientos de ese negocio con filtros
        $arrData = $this->model->select_movements($businessId, $minDate, $maxDate, $searchConcept);
        
        $cont = 1; // Contador para la tabla


        foreach ($arrData as $key => $value) {
            
            $idVoucher = $value['idVoucherHeader'];

            $arrData[$key]['cont'] = $cont;
            $arrData[$key]['date_time'] = dateFormat($value['date_time']);
            $arrData[$key]['actions'] = '
                <div class="btn-group">
                    <button
                        class="btn btn-info report-item"
                        title="Ver reporte"
                        type="button"
                        data-idvoucher="' . $idVoucher . '">
                        <i class="bi bi-clipboard2-data-fill"></i>
                    </button>
                </div>
            ';

            $cont++;
        }

        toJson($arrData);
    }

    /**
     * Devuelve el detalle de un comprobante (voucher) del negocio activo.
     *
     * @return void
     */
    public function getVoucher(): void
    {
        if (!$_POST) {
            $this->responseError('Solicitud inválida.');
        }

        $idVoucherHeader = intval($_POST['idVoucherHeader'] ?? 0);

        if ($idVoucherHeader <= 0) {
            $this->responseError('Identificador de comprobante no válido.');
        }

        // ID del negocio desde la sesión
        $businessId = $this->getBusinessId();

        $rows = $this->model->select_voucher($idVoucherHeader, $businessId);

        if (empty($rows)) {
            $arrResponse = [
                'status' => false,
                'msg'    => 'No se encontraron datos para este comprobante.'
            ];
            toJson($arrResponse);
            return;
        }

        // Cabecera desde la primera fila
        $headerRow = $rows[0];

        $header = [
            'name_bussines'       => $headerRow['name_bussines'],
            'direction_bussines'  => $headerRow['direction_bussines'],
            'document_bussines'   => $headerRow['document_bussines'],
            'date_time'           => dateFormat($headerRow['date_time']),
            'name_customer'       => $headerRow['name_customer'],
            'direction_customer'  => $headerRow['direction_customer'],
            'fullname'            => $headerRow['fullname'],
            'amount'              => $headerRow['amount'],
            'percentage_discount' => $headerRow['percentage_discount'],
        ];

        // Detalle (todas las filas)
        $details = [];
        foreach ($rows as $row) {
            $details[] = [
                'name_product'        => $row['name_product'],
                'unit_of_measurement' => $row['unit_of_measurement'],
                'sales_price_product' => $row['sales_price_product'],
                'stock_product'       => $row['stock_product'],
            ];
        }

        $arrResponse = [
            'status'  => true,
            'header'  => $header,
            'details' => $details,
        ];

        toJson($arrResponse);
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
     * Obtiene los totales de movimientos para mostrar en las cards dinámicas.
     *
     * @return void
     */
    public function getTotals(): void
{
    $businessId = $this->getBusinessId();

    // Filtros de fecha
    $minDate = (isset($_GET["minDate"]) && !empty($_GET["minDate"])) ? strClean($_GET['minDate']) : null;
    $maxDate = (isset($_GET["maxDate"]) && !empty($_GET["maxDate"])) ? strClean($_GET['maxDate']) : null;
    $filterType = (isset($_GET["filterType"]) && !empty($_GET["filterType"])) ? strClean($_GET['filterType']) : 'daily';
    $searchConcept = (isset($_GET["searchConcept"]) && !empty($_GET["searchConcept"])) ? strClean($_GET['searchConcept']) : null;

    // Calcular fechas según el tipo de filtro SI NO se han enviado fechas específicas
    if ($filterType !== 'custom') {
        // Si se ha seleccionado un tipo de filtro diferente a personalizado Y no se han enviado fechas específicas en minDate y maxDate
        // entonces calcular las fechas predeterminadas
        if ($minDate === null && $maxDate === null) {
            switch ($filterType) {
                case 'daily':
                    $minDate = date('Y-m-d');
                    $maxDate = date('Y-m-d');
                    break;
                case 'weekly':
                    $minDate = date('Y-m-d', strtotime('monday this week'));
                    $maxDate = date('Y-m-d', strtotime('sunday this week'));
                    break;
                case 'monthly':
                    $minDate = date('Y-m-01');
                    $maxDate = date('Y-m-t');
                    break;
                case 'yearly':
                    $minDate = date('Y-01-01');
                    $maxDate = date('Y-12-31');
                    break;
            }
        }
        // Si se han enviado fechas específicas, se usan esas fechas y no se sobrescriben
    }

    $totals = $this->model->getTotals($businessId, $minDate, $maxDate, $searchConcept);

    // Valores crudos (por si los quieres seguir usando)
    $balanceRaw        = (float)($totals['balance']        ?? 0);
    $totalSalesRaw     = (float)($totals['total_sales']    ?? 0);
    $totalExpensesRaw  = (float)($totals['total_expenses'] ?? 0);

    // Símbolo de moneda desde el helper
    $currency = getCurrency(); // ej. "S/"

    // Formateados
    $formattedTotals = [
        'balance'         => $currency . ' ' . number_format($balanceRaw, 2, '.', ','),
        'total_sales'     => $currency . ' ' . number_format($totalSalesRaw, 2, '.', ','),
        'total_expenses'  => $currency . ' ' . number_format($totalExpensesRaw, 2, '.', ','),
    ];

    $arrResponse = [
        'status' => true,
        'totals' => $formattedTotals,
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
            'title'   => 'Ocurrió un error',
            'message' => $message,
            'type'    => 'error',
            'icon'    => 'error',
            'status'  => false,
        ];

        toJson($data);
    }
}
