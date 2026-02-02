<?php

class BoxManagement extends Controllers
{
    protected string $nameVarBusiness;
    protected string $nameVarLoginInfo;

    public function __construct()
    {
        isSession(1);
        parent::__construct("POS");

        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness   = $sessionName . 'business_active';
        $this->nameVarLoginInfo  = $sessionName . 'login_info';
    }

    public function boxmanagement()
    {
        validate_permission_app(13, "r");
        $data = [
            'page_id'          => 13,
            'page_title'       => 'Gestión de cajas',
            'page_description' => 'Gestiona las cajas de tu negocio.',
            'page_container'   => 'Boxmanagement',
            'page_view'        => 'boxmanagement',
            'page_js_css'      => 'boxmanagement',
        ];
        $this->views->getView($this, "boxmanagement", $data, "POS");
    }

    public function getBoxes(): void
    {
        validate_permission_app(13, "r", false, false, false);
        $businessId = $this->getBusinessId();

        $arrData = $this->model->select_boxes($businessId);

        $cont = 1;

        foreach ($arrData as $key => $value) {
            $idBox = (int) $value['idBox'];
            $boxNameRaw = (string)($value['name'] ?? '');
            $boxName = htmlspecialchars($boxNameRaw, ENT_QUOTES, 'UTF-8');

            $arrData[$key]['cont'] = $cont;

            $validationUpdate = (int) validate_permission_app(13, "u", false)['update'];

            $validationDelete = (int) validate_permission_app(13, "d", false)['delete'];


            $isCajaPrincipal = (mb_strtolower(trim($boxNameRaw), 'UTF-8') === mb_strtolower('Caja Principal', 'UTF-8'));

            if ($isCajaPrincipal) {
                $arrData[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                    . '<button class="btn btn-outline-info btn-sm report-box" data-id="' . $idBox . '" data-name="' . $boxName . '" title="Ver reporte de la caja">'
                    . '<i class="bi bi-file-earmark-text"></i></button>';
            } else {
                $arrData[$key]['actions'] = '<div class="btn-group btn-group-sm" role="group">'
                    . '<button class="btn btn-outline-info btn-sm report-box" data-id="' . $idBox . '" data-name="' . $boxName . '" title="Ver reporte de la caja">'
                    . '<i class="bi bi-file-earmark-text"></i></button>';

                if ($validationUpdate === 1) {
                    $arrData[$key]['actions'] .= '<button class="btn btn-outline-primary edit-box" data-id="' . $idBox . '">'
                        . '<i class="bi bi-pencil-square"></i></button>';
                }

                if ($validationDelete === 1) {
                    $arrData[$key]['actions'] .= '<button class="btn btn-outline-danger delete-box" data-id="' . $idBox . '" data-name="' . $boxName . '" data-token="' . csrf(false) . '">'
                        . '<i class="bi bi-trash"></i></button>';
                }

                $arrData[$key]['actions'] .= '</div>';
            }

            $arrData[$key]['name'] = $boxName;
            $arrData[$key]['description'] = htmlspecialchars($value['description'] ?? '', ENT_QUOTES, 'UTF-8');
            $arrData[$key]['status'] = $value['status'];

            $arrData[$key]['statusBadge'] = $value['status'] === 'Activo'
                ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>'
                : '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Inactivo</span>';

            $arrData[$key]['registrationDate'] = dateFormat($value['registrationDate'] ?? '');

            $cont++;
        }

        toJson($arrData);
    }


    private function getBusinessId(): int
    {
        if (!isset($_SESSION[$this->nameVarBusiness]['idBusiness'])) {
            $this->responseError('No se encontró el negocio activo en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarBusiness]['idBusiness'];
    }

    public function getBox(): void
    {
        validate_permission_app(13, "r", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $idBox = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($idBox <= 0) {
            $this->responseError('Identificador de caja inválido.');
        }

        $businessId = $this->getBusinessId();
        $box = $this->model->select_box($idBox, $businessId);

        if (empty($box)) {
            $this->responseError('No se encontró la caja o no pertenece a este negocio.');
        }

        toJson([
            'status' => true,
            'data'   => [
                'idBox'            => (int) $box['idBox'],
                'name'             => $box['name'],
                'description'      => $box['description'] ?? '',
                'status'           => $box['status'],
                'registrationDate' => dateFormat($box['registrationDate'] ?? ''),
            ],
        ]);
    }

    public function setBox(): void
    {
        validate_permission_app(13, "c", false, false, false);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        // Validar campos obligatorios
        $requiredFields = ['nameBox'];
        validateFields($requiredFields);

        $businessId = $this->getBusinessId();
        $name = ucwords(strClean($_POST['nameBox'] ?? ''));
        $description = strClean($_POST['descriptionBox'] ?? '');

        if ($name === '') {
            $this->responseError('El nombre de la caja es obligatorio.');
        }

        $existingBox = $this->model->selectBoxByName($name, $businessId);
        if (!empty($existingBox)) {
            $this->responseError('Ya existe una caja con el mismo nombre en tu negocio.');
        }

        $request = $this->model->insert_box([
            'name' => $name,
            'description' => $description,
            'business_id' => $businessId
        ]);

        if ($request > 0) {
            toJson([
                'title'  => 'Registro exitoso',
                'message' => 'La caja se registró correctamente.',
                'type'   => 'success',
                'icon'   => 'success',
                'status' => true,
            ]);
        }

        $this->responseError('No fue posible registrar la caja, inténtalo nuevamente.');
    }

    public function updateBox(): void
    {
        validate_permission_app(13, "u", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $userId = $this->getUserId();
        $this->validateCsrfToken($_POST['token'] ?? '', $userId);

        $requiredFields = ['update_idBox', 'update_nameBox'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                $this->responseError('El campo ' . $field . ' es obligatorio.');
            }
        }

        $businessId = $this->getBusinessId();
        $idBox = (int) ($_POST['update_idBox'] ?? 0);
        $name = ucwords(strClean($_POST['update_nameBox'] ?? ''));
        $description = strClean($_POST['update_descriptionBox'] ?? '');

        if ($idBox <= 0) $this->responseError('Identificador de caja inválido.');
        if ($name === '') $this->responseError('El nombre de la caja es obligatorio.');

        $currentBox = $this->model->select_box($idBox, $businessId);
        if (empty($currentBox)) {
            $this->responseError('La caja seleccionada no existe o no pertenece a tu negocio.');
        }

        $existingBox = $this->model->selectBoxByName($name, $businessId);
        if (!empty($existingBox) && (int) $existingBox['idBox'] !== $idBox) {
            $this->responseError('Ya existe otra caja con el mismo nombre en tu negocio.');
        }

        $request = $this->model->update_box([
            'idBox'       => $idBox,
            'name'        => $name,
            'description' => $description,
            'business_id' => $businessId
        ]);

        if ($request) {
            toJson([
                'title'  => 'Actualización exitosa',
                'message' => 'La información de la caja se actualizó correctamente.',
                'type'   => 'success',
                'icon'   => 'success',
                'status' => true,
            ]);
        }

        $this->responseError('No fue posible actualizar la caja, inténtalo nuevamente.');
    }

    public function deleteBox(): void
    {
        validate_permission_app(13, "d", false, false, false);
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->responseError('Método de solicitud no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $userId = $this->getUserId();
        $this->validateCsrfToken($input['token'] ?? '', $userId);

        $idBox = isset($input['id']) ? (int)$input['id'] : 0;
        if ($idBox <= 0) {
            $this->responseError('Identificador de caja inválido.');
        }

        $businessId = $this->getBusinessId();
        $box = $this->model->select_box($idBox, $businessId);

        if (empty($box)) {
            $this->responseError('La caja seleccionada no existe o no pertenece a tu negocio.');
        }

        // Si está referenciada en otras tablas, se inactiva en lugar de eliminar
        if ($this->model->box_has_references($idBox)) {
            $ok = $this->model->inactivate_box($idBox, $businessId);
            if (!$ok) {
                $this->responseError('No fue posible inactivar la caja, inténtalo nuevamente.');
            }

            toJson([
                'title'  => 'Caja inactivada',
                'message' => 'La caja está vinculada a otros registros. Se marcó como INACTIVA y ya no se mostrará en la tabla.',
                'type'   => 'success',
                'icon'   => 'success',
                'status' => true,
            ]);
        }

        // Proceder a eliminar la caja si no tiene referencias
        $deleted = $this->model->delete_box($idBox, $businessId);

        if (!$deleted) {
            $this->responseError('No fue posible eliminar la caja, inténtalo nuevamente.');
        }

        toJson([
            'title'  => 'Caja eliminada',
            'message' => 'La caja se eliminó correctamente.',
            'type'   => 'success',
            'icon'   => 'success',
            'status' => true,
        ]);
    }



    private function getUserId(): int
    {
        if (!isset($_SESSION[$this->nameVarLoginInfo]['idUser'])) {
            $this->responseError('No se encontró información del usuario en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarLoginInfo]['idUser'];
    }

    private function validateCsrfToken(string $token, int $userId): void
    {
        if (empty($token) || empty($_SESSION['data_token']['token'])) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }

        $sessionToken = (string) $_SESSION['data_token']['token'];
        if (!hash_equals($sessionToken, (string) $token)) {
            $this->responseError('La sesión ha expirado, actualiza la página e inténtalo nuevamente.');
        }
    }

    private function responseError(string $message): void
    {
        toJson([
            'title'   => 'Ocurrió un error',
            'message' => $message,
            'type'    => 'error',
            'icon'    => 'error',
            'status'  => false,
        ]);
    }
}
