<?php

/**
 * Controlador para la gestión de descuentos
 *
 * Este controlador maneja todas las operaciones relacionadas con la gestión
 * de descuentos y cupones promocionales del sistema.
 */
class Discounts extends Controllers
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
     * Muestra la vista principal de gestión de descuentos
     *
     * @return void
     */
    public function discounts()
    {
        $data = [
            'page_id'          => 21,
            'page_title'       => 'Gestión de Descuentos',
            'page_description' => 'Gestiona los descuentos y cupones promocionales del sistema.',
            'page_container'   => 'Discounts',
            'page_view'        => 'discounts',
            'page_js_css'      => 'discounts',
            'page_vars'        => ['permission_data', 'login', 'login_info'],
        ];

        permissionInterface($data['page_id']);

        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

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

        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        $this->views->getView($this, 'discounts', $data);
    }

    /**
     * Obtiene la lista de descuentos para DataTables
     *
     * @return void
     */
    public function getDiscounts()
    {
        if (permissionInterface(21)) {
            $arrResponse = $this->model->select_discounts_with_plans();
            $arrData = array();

            for ($i = 0; $i < count($arrResponse); $i++) {
                $btnView = '';
                $btnEdit = '';
                $btnDelete = '';

                if (permissionInterface(21)) {
                    $btnView = '<button class="btn btn-info btn-sm report-item"
                                data-id="' . $arrResponse[$i]['idDiscount'] . '"
                                data-code="' . $arrResponse[$i]['code'] . '"
                                data-type="' . $arrResponse[$i]['type'] . '"
                                data-value="' . $arrResponse[$i]['value'] . '"
                                data-start-date="' . $arrResponse[$i]['start_date'] . '"
                                data-end-date="' . $arrResponse[$i]['end_date'] . '"
                                data-applies-to-plan-id="' . $arrResponse[$i]['applies_to_plan_id'] . '"
                                data-plan-name="' . ($arrResponse[$i]['plan_name'] ?? 'Todos los planes') . '"
                                data-max-uses="' . $arrResponse[$i]['max_uses'] . '"
                                data-is-recurring="' . $arrResponse[$i]['is_recurring'] . '"
                                data-status="' . $arrResponse[$i]['status'] . '"
                                data-toggle="modal"
                                data-target="#modalReport"
                                title="Ver Descuento">
                                <i class="fa fa-eye"></i>
                            </button>';
                }

                if (permissionInterface(21)) {
                    $btnEdit = '<button class="btn btn-success btn-sm update-item"
                                data-id="' . $arrResponse[$i]['idDiscount'] . '"
                                data-code="' . $arrResponse[$i]['code'] . '"
                                data-type="' . $arrResponse[$i]['type'] . '"
                                data-value="' . $arrResponse[$i]['value'] . '"
                                data-start-date="' . $arrResponse[$i]['start_date'] . '"
                                data-end-date="' . $arrResponse[$i]['end_date'] . '"
                                data-applies-to-plan-id="' . $arrResponse[$i]['applies_to_plan_id'] . '"
                                data-plan-name="' . ($arrResponse[$i]['plan_name'] ?? '') . '"
                                data-max-uses="' . $arrResponse[$i]['max_uses'] . '"
                                data-is-recurring="' . $arrResponse[$i]['is_recurring'] . '"
                                data-status="' . $arrResponse[$i]['status'] . '"
                                data-toggle="modal"
                                data-target="#modalUpdate"
                                title="Editar Descuento">
                                <i class="fa fa-pencil"></i>
                            </button>';
                }

                if (permissionInterface(21)) {
                    $btnDelete = '<button class="btn btn-danger btn-sm delete-item"
                                data-id="' . $arrResponse[$i]['idDiscount'] . '"
                                data-code="' . $arrResponse[$i]['code'] . '"
                                data-toggle="modal"
                                data-target="#confirmModalDelete"
                                title="Eliminar Descuento">
                                <i class="fa fa-trash"></i>
                            </button>';
                }

                $arrStatus = ($arrResponse[$i]['status'] == 'Activo') ?
                    '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>' :
                    '<span class="badge badge-danger"><i class="fa fa-times"></i> Inactivo</span>';

                $arrType = ($arrResponse[$i]['type'] == 'percentage') ? 'Porcentaje' : 'Monto Fijo';

                $arrData[$i] = array(
                    $i + 1,
                    $arrResponse[$i]['code'],
                    $arrType,
                    $arrResponse[$i]['value'],
                    ($arrResponse[$i]['plan_name'] ?? 'Todos los planes'),
                    dateFormat($arrResponse[$i]['start_date'], 'd/m/Y H:i'),
                    dateFormat($arrResponse[$i]['end_date'], 'd/m/Y H:i'),
                    $arrStatus,
                    '<div class="btn-group">' . $btnView . $btnEdit . $btnDelete . '</div>'
                );
            }

            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        } else {
            $arrData = array('status' => false, 'msg' => 'Permiso denegado');
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    /**
     * Registra un nuevo descuento
     *
     * @return void
     */
    public function setDiscount()
    {
        permissionInterface(21);

        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, al momento de registrar una persona", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }

        isCsrf();

        $intIdDiscount = isset($_POST['idDiscount']) ? intval($_POST['idDiscount']) : 0;

        $strCode = strClean($_POST['txtCode']);
        $strType = strClean($_POST['slctType']);
        $strValue = floatval($_POST['txtValue']);
        $strStartDate = strClean($_POST['txtStartDate']);
        $strEndDate = strClean($_POST['txtEndDate']);
        $intAppliesToPlanId = !empty($_POST['slctPlanId']) ? intval($_POST['slctPlanId']) : null;
        $intMaxUses = !empty($_POST['txtMaxUses']) ? intval($_POST['txtMaxUses']) : null;
        $intIsRecurring = !empty($_POST['chkIsRecurring']) ? 1 : 0;

        // Validaciones
        $arrValidation = validateFields([
            'txtCode' => $strCode,
            'slctType' => $strType,
            'txtValue' => $strValue,
            'txtStartDate' => $strStartDate,
            'txtEndDate' => $strEndDate
        ]);

        $arrValidationEmpty = validateFieldsEmpty([
            'txtCode' => $strCode,
            'slctType' => $strType,
            'txtValue' => $strValue,
            'txtStartDate' => $strStartDate,
            'txtEndDate' => $strEndDate
        ]);

        if ($arrValidation['status'] == false) {
            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => $arrValidation['msg'], 'type' => 'error');
        } else if ($arrValidationEmpty['status'] == false) {
            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => $arrValidationEmpty['msg'], 'type' => 'error');
        } else {
            // Validar formato de fechas
            $dateStartParts = explode(' ', $strStartDate);
            $dateEndParts = explode(' ', $strEndDate);

            if (count($dateStartParts) == 2) {
                $dateStart = explode('-', $dateStartParts[0]);
                if (!checkdate($dateStart[1], $dateStart[2], $dateStart[0])) {
                    $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Formato de fecha de inicio inválido', 'type' => 'error');
                }
            } else {
                $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Formato de fecha de inicio inválido', 'type' => 'error');
            }

            if (count($dateEndParts) == 2) {
                $dateEnd = explode('-', $dateEndParts[0]);
                if (!checkdate($dateEnd[1], $dateEnd[2], $dateEnd[0])) {
                    $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Formato de fecha de fin inválido', 'type' => 'error');
                }
            } else {
                $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Formato de fecha de fin inválido', 'type' => 'error');
            }

            if ($arrResponse['status'] == false) {
                // Error en fechas, no continuar
            } else {
                // Verificar si el código ya existe
                $arrDiscount = $this->model->select_discount_by_code($strCode);

                if ($arrDiscount && $arrDiscount['idDiscount'] != $intIdDiscount) {
                    $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'El código de descuento ya existe', 'type' => 'error');
                } else {
                    // Validar tipo de descuento
                    if ($strType !== 'percentage' && $strType !== 'fixed') {
                        $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Tipo de descuento inválido', 'type' => 'error');
                    } else {
                        // Validar valor positivo
                        if ($strValue < 0) {
                            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'El valor del descuento debe ser positivo', 'type' => 'error');
                        } else {
                            // Verificar que la fecha de fin sea posterior a la de inicio
                            if (strtotime($strEndDate) < strtotime($strStartDate)) {
                                $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'La fecha de fin debe ser posterior a la fecha de inicio', 'type' => 'error');
                            } else {
                                if ($intIdDiscount == 0) {
                                    // Insertar nuevo descuento
                                    $request = $this->model->insert_discount(
                                        $strCode,
                                        $strType,
                                        $strValue,
                                        $strStartDate,
                                        $strEndDate,
                                        $intAppliesToPlanId,
                                        $intMaxUses,
                                        $intIsRecurring
                                    );

                                    if ($request > 0) {
                                        $arrResponse = array(
                                            'status' => true,
                                            'title' => 'Registro exitoso',
                                            'message' => 'El descuento fue registrado satisfactoriamente en el sistema.',
                                            'type' => 'success'
                                        );

                                        // Registrar log
                                        $userId = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
                                        registerLog(
                                            'Descuentos',
                                            'Insertar descuento: ' . $strCode,
                                            2,
                                            $userId
                                        );
                                    } else {
                                        $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'No se pudo registrar el descuento', 'type' => 'error');
                                    }
                                } else {
                                    // Actualizar descuento existente
                                    $strStatus = strClean($_POST['slctStatus']);
                                    if ($strStatus !== 'Activo' && $strStatus !== 'Inactivo') {
                                        $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Estado inválido', 'type' => 'error');
                                    } else {
                                        $request = $this->model->update_discount(
                                            $intIdDiscount,
                                            $strCode,
                                            $strType,
                                            $strValue,
                                            $strStartDate,
                                            $strEndDate,
                                            $intAppliesToPlanId,
                                            $intMaxUses,
                                            $intIsRecurring,
                                            $strStatus
                                        );

                                        if ($request == true) {
                                            $arrResponse = array(
                                                'status' => true,
                                                'title' => 'Actualización exitosa',
                                                'message' => 'El descuento fue actualizado satisfactoriamente en el sistema.',
                                                'type' => 'success'
                                            );

                                            // Registrar log
                                            $userId = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
                                            registerLog(
                                                'Descuentos',
                                                'Actualizar descuento ID: ' . $intIdDiscount . ' - ' . $strCode,
                                                2,
                                                $userId
                                            );
                                        } else {
                                            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'No se pudo actualizar el descuento', 'type' => 'error');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Permiso denegado', 'type' => 'error');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);

        die();
    }

    /**
     * Actualiza un descuento existente
     *
     * @return void
     */
    public function updateDiscount()
    {
        // Este método ya está manejado en setDiscount()
        $this->setDiscount();
    }

    /**
     * Elimina un descuento
     *
     * @return void
     */
    public function deleteDiscount()
    {
        if (permissionInterface(21)) {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $json = json_decode(file_get_contents('php://input'), true);

                if (!isCsrf($json['token'])) {
                    $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Token CSRF inválido', 'type' => 'error');
                } else {
                    $intId = intval($json['id']);

                    if (empty($intId)) {
                        $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'ID vacío', 'type' => 'error');
                    } else {
                        $arrDiscount = $this->model->select_discount_by_id($intId);

                        if (empty($arrDiscount)) {
                            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Descuento no encontrado', 'type' => 'error');
                        } else {
                            $request = $this->model->delete_discount($intId);

                            if ($request) {
                                $arrResponse = array(
                                    'status' => true,
                                    'title' => 'Eliminación exitosa',
                                    'message' => 'El descuento con ID \'' . $intId . '\' y código \'' . $arrDiscount['code'] . '\' ha sido eliminado correctamente del sistema.',
                                    'type' => 'success'
                                );

                                // Registrar log
                                $userId = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
                                registerLog(
                                    'Descuentos',
                                    'Eliminar descuento ID: ' . $intId . ' - ' . $arrDiscount['code'],
                                    2,
                                    $userId
                                );
                            } else {
                                $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'No se pudo eliminar el descuento', 'type' => 'error');
                            }
                        }
                    }
                }

                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $arrResponse = array('status' => false, 'title' => 'Error', 'message' => 'Permiso denegado', 'type' => 'error');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    /**
     * Obtiene la lista de planes para el combobox
     *
     * @return void
     */
    public function getPlans()
    {
        if (permissionInterface(21)) {
            // Direct database query to get active plans
            $sql = "SELECT idPlan, name FROM plans WHERE is_active = 1 ORDER BY name";
            $request = $this->model->select_all($sql);

            if ($request) {
                $arrResponse = array(
                    'status' => true,
                    'data' => $request
                );
            } else {
                $arrResponse = array(
                    'status' => false,
                    'data' => array()
                );
            }

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        } else {
            $arrResponse = array('status' => false, 'data' => array());
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
