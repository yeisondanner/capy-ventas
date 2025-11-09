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
    public function Businesstype()
    {
        // Datos de la página (asignación única para claridad y mantenimiento)
        $data = [
            'page_id'          => 66,
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
}
