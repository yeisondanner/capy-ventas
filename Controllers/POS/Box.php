<?php

class Box extends Controllers
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

    // TODO: Endpoint para mostrar todas las cajas diponibles del negocio
    public function getBoxs()
    {
        // * Consultamos el ID del negocio
        $businessId = $this->getBusinessId();

        // * Consultamos las cajas habilitadas del negocio
        $boxs = $this->model->getBoxs($businessId);

        // * Mantenlo si el status NO es Inactivo
        $boxesActivas = array_filter($boxs, fn($box) => $box['status'] !== 'Inactivo');

        // * IMPORTANTE: Re-indexar los números (0, 1, 2...)
        $boxesActivas = array_values($boxesActivas);

        // * Agregamos un estado mas
        foreach ($boxesActivas as $key => $value) {
            $boxesActivas[$key]["session"] = $value["status"];
        }

        // * cosultamos cajas disponibles
        foreach ($boxesActivas as $key => $value) {
            $usingBox = $this->model->getUsingBox($value["idBox"]);
            if ($usingBox && $usingBox["status"] !== "Cerrada") {
                $boxesActivas[$key]["session"] = $usingBox["status"];
            }
        }

        // * Mensaje de respuesta correcta
        if ($boxesActivas && !empty($boxesActivas)) {
            toJson([
                'title'  => 'Respuesta correcta',
                'message' => 'Lista de cajas disponibles',
                'type'   => 'success',
                'icon'   => 'success',
                'status' => true,
                'data' => $boxesActivas
            ]);
        }

        // * Mensaje de respuesta de error
        toJson($this->responseError('Solicite al administrador que habilite almenos una caja.'));
    }

    // TODO: Endpoint para aperturar un caja
    public function setOpenBox()
    {
        // * Validamos que llegue el metodo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Decodificamos la cadena de texto en formato JSON para validar
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        // * Consultamos el ID del negocio
        $businessId = $this->getBusinessId();

        // * Limpiamos los campos
        $box_id = (int) strClean($data["box_id"]);
        $cash_opening_amount = (float) strClean($data["cash_opening_amount"]);

        // * Validamos que no este vacio los campos
        validateFieldsEmpty(array(
            "ID DE LA CAJA" => $box_id
        ));

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Validamos que el usuario no haya aperturado una caja
        $exisUserOpenBox = $this->model->getBoxSessionsByUserId($userId);
        if ($exisUserOpenBox) {
            $this->responseError("Ya cuentas con una caja aperturada.");
        }

        // * validamos que caja pertenesca al negocio
        $exisBox = $this->model->getBoxByIdAndBusinessId($box_id, $businessId);
        if (!$exisBox) {
            $this->responseError("Esta caja no pertenece al negocio.");
        }

        // * Validamos que la caja no este aperturada o en arqueo por otro usuario
        $boxChecked = $this->model->getBoxByStatusAndBoxId("Cerrada", $exisBox["idBox"]);
        if ($boxChecked) {
            $this->responseError("Esta caja se encuentra aperturado por otro usuario.");
        }

        // * Aperturamos la caja
        $boxSessions = $this->model->insertBoxSessions($exisBox["idBox"], $userId, $cash_opening_amount);
        if ($boxSessions <= 0) {
            $this->responseError("Error al aperturar su caja. Comunicate con el administrador de la Capy Tienda.");
        }

        // * Registramos en movimientos para mejar el historial por caja
        $boxMovements = $this->model->insertBoxMovements($boxSessions, "Inicio", "Apertura de caja", $cash_opening_amount, "Efectivo");
        if ($boxMovements > 0) {
            toJson([
                'title'   => 'Apertura de Caja',
                'message' => 'Caja ' . $boxSessions . ' aperturada correctamente.',
                'type'    => 'success',
                'icon'    => 'success',
                'status'  => true,
            ]);
        }

        toJson([
            'title'   => 'Apertura de Caja',
            'message' => 'Se aperturo la caja ' . $boxSessions . ', pero no se registro como primer movimiento correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    // TODO: Endpoint para registrar todos los arqueos y cierre de caja
    public function setBoxCashCount()
    {
        // * Validamos que llegue el metodo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Decodificamos la cadena de texto en formato JSON para validar
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        // * Limpiamos los campos
        $type = strClean($data['type']);
        $notes = strClean($data['notes']);
        $conteo_efectivo = $data['conteo_efectivo'];

        // * Validamos que no este vacio los campos
        validateFieldsEmpty(array(
            "TIPO DE CAJA" => $type
        ));

        // * Validar TYPE (Debe ser uno de los valores permitidos en tu ENUM)
        $allowed_types = ['Cierre', 'Auditoria']; // Los valores de tu base de datos
        if (!in_array($type, $allowed_types)) {
            $this->responseError("El tipo de arqueo es inválido. Debe ser 'Cierre' o 'Auditoria'.");
        }

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Consultamos si el usuario tiene un caja aperturada
        $boxSessions = $this->model->getBoxSessionsByUserId($userId);
        if (!$boxSessions) {
            $this->responseError('No tienes ninguna caja aperturada. Por favor apertura tu turno.');
        }

        // * Consultamos las denominaciones de las monedas
        $currencyDenominations = $this->model->getCurrencyDenominations();
        if (!$currencyDenominations) {
            $this->responseError('Ninguna moneda preconfigurada, solicita a tu Capy Adminstrador que configure las denominaciones de las monedas.');
        }

        // * Creamos un auxiliar para luego validar
        $auxCurrencyDenominations = array();
        foreach ($currencyDenominations as $key => $value) {
            $auxCurrencyDenominations[$value["idDenomination"]] = (float) $value["value"];
        }

        // * Validamos que las denominaciones recibidas si existan en la BD
        $total_efectivo_contado = 0;
        foreach ($conteo_efectivo as $key => $value) {
            if (!isset($auxCurrencyDenominations[$value["denomination_id"]])) {
                $this->responseError('Las denominaciones del dinero enviadas no existen.');
            }
            $conteo_efectivo[$key]["total_real"] = $auxCurrencyDenominations[$value["denomination_id"]] * ((int) $value["cantidad"]);
            $total_efectivo_contado += $conteo_efectivo[$key]["total_real"];
        }

        // * (Opcional) Si esta el total efectivo esta en 0 lo obligamos al usuario a seleccionar almenos uno
        // if($total_efectivo_contado === 0){
        //     $this->responseError('Por favor seleccione almenos un efectivo.');
        // }

        // * Consultamos todos los movimientos asociados a la caja aperturada para calcular el total del sistema
        $boxMovements = $this->model->getBoxMovements($boxSessions["idBoxSessions"]);
        $total_efectivo_sistema = 0;
        foreach ($boxMovements as $key => $value) {
            $amount = (float) $value["amount"];
            // ? Calculamos el total efectivo del sistema
            if ($value["payment_method"] === "Efectivo") {
                if ($value["type_movement"] !== "Egreso") {
                    $total_efectivo_sistema += $amount;
                } else {
                    $total_efectivo_sistema -= $amount;
                }
            }
        }

        // ? Calculamos la diferencia
        $difference = $total_efectivo_sistema - $total_efectivo_contado;

        // ? Si no hay un mensaje, lo agregamos por default
        if (is_null($notes) || empty($notes)) {
            if ($difference == 0) {
                $notes = "Cuadre perfecto";
            } else if ($difference < 0) {
                $notes = "Monto sobrante a favor";
            } else {
                $notes = "Descuadre detectado";
            }
        }

        // ? Sacamos el valor absoluto
        $difference = abs($difference);

        // * Registramos el arqueo o cierre de caja
        $insertArqueoBox = $this->model->insertBoxCashCount($boxSessions["idBoxSessions"], $type, $total_efectivo_sistema, $total_efectivo_contado, $difference, $notes);

        if ($insertArqueoBox <= 0) {
            $this->responseError('Error al registrar ' . $type . ' de caja.');
        }

        // * Registramos los detalles de caja
        foreach ($conteo_efectivo as $key => $value) {
            $this->model->insertBoxCashCountDetails($insertArqueoBox, $value["denomination_id"], $value["cantidad"], $value["total_real"]);
        }

        toJson([
            'title'   => 'Gestión de Caja',
            'message' => $type . ' de caja registrado correctamente.',
            'type'    => 'success',
            'icon'    => 'success',
            'status'  => true,
        ]);
    }

    // TODO: Endpoint para cerrar caja
    public function setCloseBoxSession()
    {
        // * Validamos que llegue el metodo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Decodificamos la cadena de texto en formato JSON para validar
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $notes = strClean($data["notes"]);
        if (!$notes || empty($notes)) {
            $notes = null;
        }

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Consultamos si el usuario tiene un caja aperturada
        $boxSessions = $this->model->getBoxSessionsByUserId($userId);
        if (!$boxSessions) {
            $this->responseError('No tienes ninguna caja aperturada. Por favor apertura tu turno.');
        }

        // * sacamos la fecha actual del servidor
        $fecha_actual = date('Y-m-d H:i:s');

        // * Cerramos caja
        $closeSession = $this->model->updateCloseSession($boxSessions["idBoxSessions"], $fecha_actual, $notes, "Cerrada");
        if (!$closeSession) {
            $this->responseError('Error al momento de cerrar caja.');
        }

        toJson([
            'status'  => true,
            'title'   => 'Cerrar Caja',
            'message' => 'Caja #' . $boxSessions["idBoxSessions"] . ' cerrada Correctamente',
            'type'    => 'success',
            'icon'    => 'success',
        ]);
    }

    // TODO: Endpoint que devuelve si el usuario tiene aperturado un caja
    public function getuserCheckedBox()
    {
        // * Validamos que llegue el metodo GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Validamos que el usuario no haya aperturado una caja
        $exisUserOpenBox = $this->model->getBoxSessionsByUserId($userId);
        if (!$exisUserOpenBox) {
            toJson([
                'title'   => 'Apertura de Caja',
                'message' => 'Cajas disponibles',
                'type'    => 'success',
                'icon'    => 'success',
                'status'  => true,
            ]);
        }

        $this->responseError("Ya cuentas con una caja aperturada.");
    }

    // TODO: Endopoint que devuelve los movimientos y gestion de caja
    public function getManagementBox()
    {
        // * Validamos que llegue el metodo GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Consultamos si el usuario tiene un caja aperturada
        $boxSessions = $this->model->getBoxSessionsByUserId($userId);
        if (!$boxSessions) {
            $this->responseError('No tienes ninguna caja aperturada. Por favor apertura tu turno.');
        }

        // * Consultamos los metodos de pagos disponibles por la app
        $paymentMethod = $this->model->getPaymentMethods();

        // * Consultamos todos los movimientos asociados a la caja aperturada
        $boxMovements = $this->model->getBoxMovements($boxSessions["idBoxSessions"]);

        // * Consultamos los ultimos 4 movimientos asociados a la caja aperturada
        $boxMovements_limit = $this->model->getBoxMovementsByLimit($boxSessions["idBoxSessions"], 4);

        $arrayPaymentMethod = array();
        $totalGeneral = 0;
        $totalTransacciones = 0;
        $totalEfectivo_egreso = 0;
        foreach ($boxMovements as $key => $value) {
            $amount = (float) $value["amount"];
            $totalTransacciones++;

            // ? Calculamos los totales por metodo de pago
            if (!isset($arrayPaymentMethod[$value["payment_method"]])) {
                $arrayPaymentMethod[$value["payment_method"]] = 0;
            }

            // ? Calculamos el total general de ingreso incluyendo bancos
            if ($value["type_movement"] !== "Inicio") {
                if ($value["type_movement"] !== "Egreso") {
                    $totalGeneral += $amount;
                    $arrayPaymentMethod[$value["payment_method"]] += $amount;
                } else {
                    $totalGeneral -= $amount;
                    $arrayPaymentMethod[$value["payment_method"]] -= $amount;
                }
            }

            // ? Calculamos el total de efectivo que sale
            if ($value["type_movement"] === "Egreso" && $value["payment_method"] === "Efectivo") {
                $totalEfectivo_egreso += $amount;
            }
        }

        // * Devolvemos la respuesta formateada
        $arrayResponse = [
            "status" => true,
            "amount_base" => (float) $boxSessions["initial_amount"],
            "total_general" => $totalGeneral,
            "payment_method" => $paymentMethod,
            "total_payment_method" => $arrayPaymentMethod,
            "total_transacciones" => $totalTransacciones,
            "total_efectivo_egreso" => $totalEfectivo_egreso,
            "movements_limit" => $boxMovements_limit,
        ];
        toJson($arrayResponse);
    }

    // TODO: Endpoint que devuelve los las denominaciones de la moneda
    public function getCurrencyDenominations()
    {
        // * Consultamos las denominaciones de las monedas
        $currencyDenominations = $this->model->getCurrencyDenominations();
        if (!$currencyDenominations) {
            $this->responseError('Ninguna moneda preconfigurada, solicita a tu Capy Adminstrador que configure las denominaciones de las monedas.');
        }

        toJson([
            'status'  => true,
            'title'   => 'Denominaciones de monedas',
            'message' => 'Lista de denominaciones de monedas de tu negocio.',
            'type'    => 'success',
            'icon'    => 'success',
            'data' => $currencyDenominations
        ]);
    }

    // TODO: Funcion que devuelve el ultimo arqueo de caja
    public function getLastCashCount()
    {
        // * Validamos que llegue el metodo GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }

        // * Consultamos el ID del usuario
        $userId = $this->getUserId();

        // * Consultamos si el usuario tiene un caja aperturada
        $boxSessions = $this->model->getBoxSessionsByUserId($userId);
        if (!$boxSessions) {
            $this->responseError('No tienes ninguna caja aperturada. Por favor apertura tu turno.');
        }

        // * Consultamos si tiene un arqueo de caja realizado y trael el ultimo realizado
        $cashCount = $this->model->getLastCashCount($boxSessions["idBoxSessions"]);

        toJson([
            'status'  => true,
            'title'   => 'Arqueo de caja',
            'message' => 'Ultimo arqueo de caja.',
            'type'    => 'success',
            'icon'    => 'success',
            'data' => $cashCount
        ]);
    }

    // TODO: Consultamos el ID de negocio
    private function getBusinessId(): int
    {
        if (!isset($_SESSION[$this->nameVarBusiness]['idBusiness'])) {
            $this->responseError('No se encontró el negocio activo en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarBusiness]['idBusiness'];
    }

    // TODO: Consultamos el ID del usuario
    private function getUserId(): int
    {
        if (!isset($_SESSION[$this->nameVarLoginInfo]['idUser'])) {
            $this->responseError('No se encontró el usuario activo en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarLoginInfo]['idUser'];
    }

    // TODO: Mensaje de respuesta
    private function responseError(string $message): array
    {
        $data = [
            'title'  => 'Ocurrió un error',
            'message' => $message,
            'type'   => 'error',
            'icon'   => 'error',
            'status' => false,
        ];

        toJson($data);
    }
}
