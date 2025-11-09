<?php

class Businesstype extends Controllers
{
    public function __construct()
    {
        isSession();
        parent::__construct();
    }

    /**
     * Muestra el módulo de Gestión de Roles.
     *
     * Requisitos:
     * - Permiso de acceso para la página con page_id = 4 (permissionInterface()).
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) mediante registerLog() con
     *   contexto del request (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'roles' con los datos de página y variables de sesión requeridas.
     *
     * Seguridad:
     * - Acceso defensivo a $_SESSION y $_SERVER.
     * - Soporte para entornos con proxy/CDN (cabecera HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function businesstype()
    {
        // Datos de la página (asignación única para claridad y mantenimiento)
        $data = [
            'page_id'          => 12,
            'page_title'       => 'Gestión de negocios',
            'page_description' => 'Gestiona todos los tipos de negocios.',
            'page_container'   => 'Businesstype',
            'page_view'        => 'Businesstype',
            'page_js_css'      => 'Businesstype',
            'page_vars'        => ['permission_data', 'login', 'login_info'], // mantener contexto de permisos y sesión
        ];

        // Autorización temprana: detiene el flujo si no hay permisos
        permissionInterface($data['page_id']);

        // Contexto de request/usuario (defensivo + soporte proxy/CDN)
        $userId    = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $method    = $_SERVER['REQUEST_METHOD'] ?? null;
        $url       = $_SERVER['REQUEST_URI'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 180) : null;

        // Payload de auditoría (estable y fácil de consultar)
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
            'timestamp'  => date('c'), // ISO-8601
        ];

        // Registro (nivel 3 asumido como INFO)
        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        // Render de la vista del módulo
        $this->views->getView($this, 'businesstype', $data);
    }
    /**
     * Funcion que desvuelve la lista de los roles a la vista
     * @return void
     */
    public function getBusinesstype()
    {
        permissionInterface(12);
        $arrData = $this->model->select_businesstype();
        $cont = 1; //Contador para la tabla
        foreach ($arrData as $key => $value) {
            $arrData[$key]["cont"] = $cont;
            if ($value["r_status"] == "Activo") {
                $arrData[$key]["status"] = '<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>';
            } else {
                $arrData[$key]["status"] = '<span class="badge badge-danger"><i class="fa fa-close"></i> Inactivo</span>';
            }
            if ($value["idBusinessType"] != 1) {
                $arrData[$key]["actions"] = '
                 <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-success update-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '"  data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-info report-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '"  data-description="' . $value["r_description"] . '" data-registrationDate="' . dateFormat($value['r_registrationDate']) . '" data-updateDate="' . dateFormat($value['r_updateDate']) . '" type="button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></button>
                  <a href="' . base_url() . '/pdf/rol/' . encryption($value['idBusinessType']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                <button class="btn btn-secondary permission-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '"   data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-th-list" aria-hidden="true"></i></button>
                <button class="btn btn-danger delete-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '" ><i class="fa fa-remove"></i></button>
                </div>
                '; //Botones de acciones
            } else {
                $arrData[$key]["actions"] = ' 
                 <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-success update-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '" data-description="' . $value["r_description"] . '" type="button"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-info report-item" data-id="' . $value["idBusinessType"] . '" data-name="' . $value["r_name"] . '" data-status="' . $value['r_status'] . '" data-description="' . $value["r_description"] . '" data-registrationDate="' . dateFormat($value['r_registrationDate']) . '" data-updateDate="' . dateFormat($value['r_updateDate']) . '"  type="button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></button> 
                 <a href="' . base_url() . '/pdf/rol/' . encryption($value['idBusinessType']) . '" target="_Blank" class="btn btn-warning btn-sm report-pdf">
                        <i class="fa fa-print"></i>
                    </a>
                </div>';
            }
            $cont++;
        }
        toJson($arrData);
    }
    /**
     * Funcion que permite el registro del usuario nuevo
     * @return void
     */
}
