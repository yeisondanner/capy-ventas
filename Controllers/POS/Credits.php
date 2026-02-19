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
        $saleType = strClean($_POST['saleType']);
        $paymentStatus = strClean($_POST['paymentStatus']);
        validateFieldsEmpty([
            "ID del cliente" => $idCustomer,
            "TIPO DE VENTAS" => $saleType,
            "ESTADO DE PAGO" => $paymentStatus
        ]);
        $idBusiness = (int) $this->getBusinessId();
        $dataCustomer = $this->model->getInfoCustomer($idCustomer, $idBusiness);
        //validamos si los tipos existen
        if (!in_array($saleType, ["All", "Credito", "Contado"])) {
            $saleType = "All";
        }
        if (!in_array($paymentStatus, ["All", 'Pendiente', "Pagado", "Anulado"])) {
            $paymentStatus = "All";
        }
        $dataKPIS = $this->model->getKPISCustomer($idCustomer, $idBusiness, $startDate, $endDate, $saleType, $paymentStatus);
        $dataCredits = $this->model->getCreditsCustomer($idCustomer, $idBusiness, $startDate, $endDate, $saleType, $paymentStatus);
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
     * MEtodo que se encarga de obtner la informacion
     * del credito seleccionado a pagar mediante su id de voucher
     * y id del negocio el cual se pasan mediante el emtodo POST
     * @return void
     */
    public function getInfoCreditToPay()
    {
        validate_permission_app(15, "r", false, false, false);
        validateFields(["idVoucher"]);
        $idVoucher = strClean($_POST['idVoucher']);
        $idBusiness = (int) $this->getBusinessId();
        validateFieldsEmpty([
            "ID del voucher" => $idVoucher,
        ]);
        $data = $this->model->getInfoCreditToPay($idVoucher, $idBusiness);
        if (empty($data)) {
            $this->responseError('No se encontró el crédito seleccionado.');
        }
        $arrData = [
            'infoCredit' => $data,
            'status' => true
        ];
        toJson($arrData);
    }
    /**
     * MEtodo que se encarga de obtner los metodos de pagos que esta permitido 
     * en el sistema
     * @return void
     */
    public function getPaymentMethods()
    {
        validate_permission_app(15, "r", false, false, false);
        $data = $this->model->getPaymentMethods();
        $arrData = [
            'paymentMethods' => $data,
            'status' => true
        ];
        toJson($arrData);
    }
    /**
     * Metodo que se encarga de pagar el credito seleccionado individual
     * @return void
     */
    public function setPaymentCreditIndividually()
    {
        validate_permission_app(15, "c", false, false, false);
        $payment_date = date('Y-m-d H:i:s');
        validateFields(["idvoucher", "paymentMethod"]);
        $idvoucher = strClean($_POST["idvoucher"]);
        $paymentMethod = strClean($_POST["paymentMethod"]);
        $amountReceived = $_POST['amountReceived'] ?? 0.0;
        $voucher_name = "Pago de credito realizada el " . $payment_date;
        $typmovementvox = "Ingreso";
        validateFieldsEmpty([
            "ID DEL VOUCHER" => $idvoucher,
            "METODO DE PAGO" => $paymentMethod
        ]);
        $idBusiness = (int) $this->getBusinessId();
        //validamos si el id es numerico
        if (!is_numeric($idvoucher)) {
            $this->responseError('El ID del voucher debe ser numérico.');
        }
        //validamos que el metodo de pago sea numerico
        if (!is_numeric($paymentMethod)) {
            $this->responseError('El metodo de pago debe ser numérico.');
        }
        //validamos que el monto recibido sea numerico
        if ($amountReceived > 0 && !empty($amountReceived) && !is_numeric($amountReceived)) {
            $this->responseError('El monto recibido debe ser numérico.');
        } else {
            $amountReceived = 0;
        }
        //validamos si el credito existe
        $dataCredit = $this->model->getInfoCreditToPay($idvoucher, $idBusiness);
        if (empty($dataCredit)) {
            $this->responseError('No se encontró el crédito seleccionado.');
        }
        //validamos si el metodo de pago existe en la base de datos
        $dataPaymentMethod = $this->model->getPaymentMethod($paymentMethod);
        if (empty($dataPaymentMethod)) {
            $this->responseError('No se encontró el metodo de pago seleccionado.');
        }
        //validamos si el usuario tiene permiso de leer caja
        $validationReadBox = (validate_permission_app(11, "r", false)) ? (int) validate_permission_app(11, "r", false)['read'] : 0;
        //validamos si el usuario tiene permiso de leer caja
        if ($validationReadBox === 1) {
            //validamos si es obligatorio abrir caja antes de registrar una venta
            $openBox = $_SESSION[$this->nameVarBusiness]['openBox'] ?? 'No';
            //validamos si es necesario abrir caja para registrar la venta
            if ($openBox === 'Si') {
                $requestOpenBox = $this->model->selectOpenBoxByUser([
                    'user_app_id' => $this->getUserId(),
                    'business_id' => $idBusiness,
                    'status' => 'Abierta',
                    'year' => date('Y'),
                    'month' => date('m'),
                    'day' => date('d'),
                ]);
                if (!$requestOpenBox) {
                    $this->responseError('No se podra realizar un pago de credito, mientras no abra caja. Para este negocio es obligatorio abrir caja.', 6000);
                }
            }
        }
        $updatePaymentCredit = $this->model->updatePaymentCredit([
            'idVoucher' => $idvoucher,
            'idBusiness' => $idBusiness,
            'amount' => $dataCredit['amount_total'],
            'how_much_do_i_pay' => $amountReceived,
            'voucher_name' => $voucher_name,
            'payment_method_id' => $paymentMethod,
            'status' => 'Pagado',
            'default_interest_rate' => $dataCredit['default_interest_rate'],
            'amount_default_interest_rate' => $dataCredit['amount_total_overdue'],
            'current_interest_rate' => $dataCredit['current_interest_rate'],
            'amount_current_interest_rate' => $dataCredit['amount_current_interest_rate'],
            'payment_date' => $payment_date
        ]);
        //validamos si la caja esta abierta para registrar la venta
        $requestOpenBox = $this->model->selectOpenBoxByUser([
            'user_app_id' => $this->getUserId(),
            'business_id' => $idBusiness,
            'status' => 'Abierta',
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
        ]);
        //validar el registro de la venta en movimientos de caja (aun falta eso)
        if (!$updatePaymentCredit) {
            $this->responseError('No fue posible registrar el pago del crédito.');
        }
        //validamos si la caja esta abierta para registrar la venta
        if ($requestOpenBox) {
            //registramos el movimiento de la caja
            $insertMovement = $this->model->insertBoxMovement([
                'boxSessions_id' => $requestOpenBox['idBoxSessions'] ?? 0,
                'type_movement' => $typmovementvox,
                'concept' => $voucher_name,
                'amount' => $dataCredit['amount_total'],
                'payment_method' => (string) ($dataPaymentMethod['name'] ?? 'Efectivo'),
                'reference_table' => 'voucher_header',
                'reference_id' => $idvoucher,
            ]);
        }
        toJson([
            'status' => true,
            'message' => 'Pago del crédito registrado correctamente.',
            'title' => 'Pago del crédito',
            'icon' => 'success',
            'timer' => 2000
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
            'title' => 'Ocurrió un error',
            'message' => $message,
            'type' => 'error',
            'icon' => 'error',
            'status' => false,
            'timer' => 2000,
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
