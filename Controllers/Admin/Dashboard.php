<?php

class Dashboard extends Controllers
{
    public function __construct()
    {
        isSession();
        parent::__construct();
    }
    /**
     * Muestra el módulo de Gestión de Dashboard.
     *
     * Requisitos:
     * - Esta vista no necesita requisitos de verificación de permisos para poder iniciar 
     * ya que es la primera vista despues del login.
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) vía registerLog() con
     *   información de contexto (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'notification' con los datos de página.
     *
     * Seguridad:
     * - Accesos a $_SESSION y $_SERVER realizados de forma defensiva.
     * - Soporte para entornos con proxy/CDN (HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function dashboard()
    {
        // Datos de la página (una sola asignación)
        $data = [
            'page_id'          => 2,
            'page_title'       => 'Panel de control',
            'page_description' => 'Panel de control y métricas clave del sistema.',
            'page_container'   => 'Dashboard',
            'page_view'        => 'dashboard',
            'page_js_css'      => 'dashboard',
            'page_vars'        => ['login', 'login_info'],
        ];
        // Prepara base URL una sola vez
        $base = rtrim(base_url(), '/');
        // Contexto de request/usuario
        $userId = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;
        $storageOverview = $userId ? $this->model->select_user_storage_overview($userId) : [
            'limit_gb'     => 0,
            'used_gb'      => 0.0,
            'available_gb' => 0.0,
            'is_unlimited' => true,
        ];
        $storageValue = $storageOverview['is_unlimited']
            ? 'Dispo.: Ilimitado'
            : sprintf(
                'Dispo.: %s GB de %s GB',
                number_format($storageOverview['available_gb'], 2, ',', '.'),
                number_format($storageOverview['limit_gb'], 0, ',', '.')
            );
        $storageText = $storageOverview['is_unlimited']
            ? sprintf(
                'Cuenta con almacenamiento ilimitado. Uso actual: %s GB.',
                number_format($storageOverview['used_gb'], 2, ',', '.')
            )
            : sprintf(
                'Espacio utilizado: %s GB de %s GB totales.',
                number_format($storageOverview['used_gb'], 2, ',', '.'),
                number_format($storageOverview['limit_gb'], 0, ',', '.')
            );
        // Lecturas de modelo con defensas
        $usersCount = (int) ($this->model->select_count_users()['CantidadUsuariosActivos'] ?? 0);
        $rolesCount = (int) ($this->model->select_count_roles()['CantidadRoles'] ?? 0);

        // Widgets (textos depurados y valores formateados)
        $data['page_widget'] = [
            [
                'title' => 'Usuarios',
                'icon'  => 'fa fa-users',
                'value' => number_format($usersCount, 0, ',', '.'),
                'link'  => "{$base}/users",
                'text'  => 'Cantidad de usuarios activos con acceso al sistema.',
                'color' => 'primary',
            ],
            [
                'title' => 'Roles',
                'icon'  => 'fa fa-tags',
                'value' => number_format($rolesCount, 0, ',', '.'),
                'link'  => "{$base}/roles",
                'text'  => 'Total de roles activos configurados.',
                'color' => 'info',
            ],
            [
                'title' => 'Espacio disponible',
                'icon'  => 'fa fa-hdd-o',
                'value' => $storageValue,
                'link'  => "{$base}/clust",
                'text'  => $storageText,
                'color' => 'warning',
            ],
        ];

        // Contexto de request/usuario
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null; // soporta proxy
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
        $this->views->getView($this, 'dashboard', $data);
    }
}
