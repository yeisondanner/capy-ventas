<?php

class Logs extends Controllers
{
    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        isSession();
        parent::__construct();
    }
    /**
     * Funcion que devuelve la vista de la gestion de usuarios
     * @return void
     */
    public function logs()
    {
        // Datos de la página (una sola asignación)
        $data = [
            'page_id'          => 5,
            'page_title'       => 'Historial de registros',
            'page_description' => 'Visualiza los registros de usuarios, incluidos los cambios realizados en el sistema.',
            'page_container'   => 'Logs',
            'page_view'        => 'logs',
            'page_js_css'      => 'logs',
            'page_vars'        => ['login', 'login_info'], // mantener sesión viva
        ];

        // Autorización temprana
        permissionInterface($data['page_id']);

        // Contexto de request/usuario (defensivo)
        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null; // soporta proxy/CDN
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        // Payload de auditoría
        $payload = [
            'event'      => 'page_view',
            'page'       => $data['page_title'],
            'page_id'    => $data['page_id'],
            'container'  => $data['page_container'],
            'user_id'    => $userId,
            'ip'         => $ip,
            'method'     => $method,
            'url'        => $url,
            'user_agent' => $userAgent,
            'timestamp'  => date('c'),
        ];

        // Registro (nivel 3 asumido como INFO)
        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        // Render de la vista
        $this->views->getView($this, 'logs', $data);
    }

    public function getLogs()
    {
        permissionInterface(5);
        $filter_type = isset($_GET["filterType"]) ? strClean($_GET['filterType']) : 0;
        $minData = (isset($_GET["minData"]) && !empty($_GET["minData"])) ? strClean($_GET['minData']) : 0;
        $maxData = (isset($_GET["maxData"]) && !empty($_GET["maxData"])) ? strClean($_GET['maxData']) : 0;
        $request = $this->model->select_logs($minData, $maxData, $filter_type);
        $cont = 1;
        foreach ($request as $key => $value) {
            $request[$key]['cont'] = $cont;
            $request[$key]['l_registrationDate'] = dateFormat($value['l_registrationDate']);
            $request[$key]['actions'] = '
            <button class="btn btn-info report-item"
                data-id="' . $value['idLog'] . '"
                data-title="' . $value['l_title'] . '"
                data-description="' . str_replace("'", "¬", str_replace('"', '|', $value['l_description'])) . '"
                data-registrationdate="' . dateFormat($value['l_registrationDate']) . '"
                data-updatedate="' . dateFormat($value['l_updateDate']) . '"
                data-type="' . $value['tl_name'] . '"
                data-fullname="' . $value['u_fullname'] . '"
                data-email="' . decryption($value['u_email']) . '"
                data-user="' . decryption($value['u_user']) . '"
            type="button" >
                <i class="fa fa-list" aria-hidden="true"></i>
            </button>
            ';;
            $cont++;
        }
        toJson($request);
    }

    /**
     * Obtiene el listado de años disponibles en los registros para poblar el filtro del gráfico.
     *
     * @return void
     */
    public function getLogYears()
    {
        permissionInterface(5);

        $years = $this->model->getAvailableYears();
        $normalizedYears = [];

        foreach ($years as $value) {
            $normalizedYears[] = (int) $value['year'];
        }

        if (empty($normalizedYears)) {
            $normalizedYears[] = (int) date('Y');
        }

        toJson($normalizedYears);
    }

    /**
     * Devuelve el resumen estadístico por tipo y el comportamiento mensual del año seleccionado.
     *
     * @return void
     */
    public function getLogSummary()
    {
        permissionInterface(5);

        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

        $totals = $this->model->getLogTotalsByYear($year);
        $monthly = $this->model->getLogMonthlyTrend($year);

        $series = [];
        foreach ($totals as $item) {
            $typeId = (int) $item['idTypeLog'];
            $series[$typeId] = [
                'id'    => $typeId,
                'name'  => $item['tl_name'],
                'data'  => array_fill(0, 12, 0),
            ];
        }

        foreach ($monthly as $row) {
            $month = isset($row['month']) ? (int) $row['month'] : 0;
            $typeId = (int) $row['idTypeLog'];
            if ($month >= 1 && $month <= 12 && isset($series[$typeId])) {
                $series[$typeId]['data'][$month - 1] = (int) $row['total'];
            }
        }

        $labels = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];

        $response = [
            'year'    => $year,
            'totals'  => $totals,
            'monthly' => [
                'labels' => $labels,
                'series' => array_values($series),
            ],
        ];

        toJson($response);
    }
}
