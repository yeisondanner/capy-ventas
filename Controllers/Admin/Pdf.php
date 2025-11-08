<?php
class Pdf extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Genera un reporte PDF con los datos de un usuario específico.
     *
     * @param string $data ID del usuario cifrado.
     * @return void
     */
    public function user($data)
    {
        // Validaciones de sesión y permisos
        isSession();
        permissionInterface(3);

        // Desencriptamos el ID del usuario
        $id = decryption($data);

        // Validación de ID vacío
        if (empty($id)) {
            $this->responseError("No se puede generar la boleta: ID vacío o manipulado.");
        }

        // Validación de que el ID sea numérico
        if (!is_numeric($id)) {
            $this->responseError("No se puede generar la boleta: ID inválido (no numérico).");
        }

        // Cargar modelo de usuario
        require_once "./Models/UsersModel.php";
        $objUser = new UsersModel();
        $request = $objUser->select_user_by_Id($id);

        // Cargar librería de generación PDF personalizada
        require_once "./Libraries/fpdf186/user.php";

        // Obtener ruta de foto de perfil del usuario
        $file_profile = $request["u_profile"] ?? null;
        $url_profile = getRoute() . '/Profile/Users/' . ($file_profile ?: 'user.png');

        // Verificamos si el archivo existe, caso contrario usamos imagen por defecto
        if (!file_exists($url_profile)) {
            $url_profile = getRoute() . '/Profile/Users/user.png';
        }

        // Datos del usuario para el reporte
        $usuario = [
            'dni' => $request['u_dni'],
            'genero' => $request['u_gender'],
            'usuario' => decryption($request['u_user']),
            'contrasena' => decryption($request['u_password']),
            'email' => decryption($request['u_email']),
            'rol' => $request['r_name'],
            'fecha_registro' => dateFormat($request['u_registrationDate']),
            'fecha_actualizacion' => dateFormat($request['u_updateDate']),
            'nombres_completos' => strtoupper($request['u_fullname']),
            'foto' => $url_profile
        ];

        // Información institucional (logo y datos)
        $systemInfo = getSystemInfo();
        $file_logo = $systemInfo["c_logo"] ?? 'sin-content.png';
        $url_logo = getRoute() . '/Profile/Logo/' . $file_logo;

        // Validamos existencia del logo
        if (!file_exists($url_logo)) {
            $url_logo = getRoute() . '/Profile/Logo/sin-content.png';
        }

        // Datos del encabezado del reporte
        $headerData = [
            'nombre_comite' => $systemInfo['c_company_name'],
            'ruc' => $systemInfo['c_ruc'],
            'direccion' => $systemInfo['c_address'],
            'telefono' => $systemInfo['c_phone'],
            'correo' => $systemInfo['c_mail'],
            'logo' => $url_logo
        ];

        // Paleta de colores del reporte
        $colores = [
            'primario' => hexToRgb($systemInfo["c_color_primary"]),
            'secundario' => hexToRgb($systemInfo["c_color_secondary"]),
        ];

        // Instanciar el reporte
        $reporte = new ReporteUsuario($usuario, $headerData, $colores);
        $nombre_pdf = "{$request['u_dni']} - {$request['u_fullname']}";

        // Metadatos del documento
        $reporte->SetTitle($nombre_pdf);
        $reporte->SetAuthor($nombre_pdf);
        $reporte->SetSubject($nombre_pdf);

        // Generar y mostrar PDF
        $reporte->generarReporte();
        $reporte->outputPDF("$nombre_pdf.pdf");

        unset($request);
    }

    /**
     * Maneja errores de validación mostrando un mensaje estructurado
     * y deteniendo la ejecución.
     *
     * @param string $logMessage Mensaje para el log.
     * @return void
     */
    private function responseError(string $logMessage): void
    {
        registerLog("Ocurrió un error inesperado", $logMessage, 1, $_SESSION['login_info']['idUser']);
        $response = [
            "title" => "Ocurrió un error inesperado",
            "message" => "No se logró completar la generación de la boleta, inténtelo más tarde",
            "type" => "error",
            "status" => false
        ];
        dep($response);
        die();
    }
    /**
     * Genera un reporte PDF con la información de un rol y sus permisos.
     *
     * @param string $data ID del rol encriptado.
     * @return void
     */
    public function rol($data)
    {
        // Validación de sesión y permisos
        isSession();
        permissionInterface(4);

        // Desencriptar el ID
        $id = decryption($data);

        // Validación de ID
        if (empty($id)) {
            $this->responseError("No se puede generar la boleta: ID vacío o manipulado.");
        }

        if (!is_numeric($id)) {
            $this->responseError("No se puede generar la boleta: ID inválido (no numérico).");
        }

        // Cargar modelo de roles
        require_once "./Models/RolesModel.php";
        $objRol = new RolesModel();

        // Obtener información del rol y sus permisos
        $infRol = $objRol->select_rol_by_id($id);
        $detailRol = $objRol->select_permissions_by_role($id);

        // Información del sistema
        $systemInfo = getSystemInfo();

        // Validar y preparar logo
        $file_logo = $systemInfo["c_logo"] ?? 'sin-content.png';
        $url_logo = getRoute() . '/Profile/Logo/' . $file_logo;
        if (!file_exists($url_logo)) {
            $url_logo = getRoute() . '/Profile/Logo/sin-content.png';
        }

        // Datos del encabezado
        $headerData = [
            'nombre_comite' => $systemInfo['c_company_name'],
            'ruc' => $systemInfo['c_ruc'],
            'direccion' => $systemInfo['c_address'],
            'telefono' => $systemInfo['c_phone'],
            'correo' => $systemInfo['c_mail'],
            'logo' => $url_logo
        ];

        // Datos principales del rol
        $rolData = [
            'nombre' => $infRol['r_name'],
            'codigo' => '#' . $infRol['idRole'],
            'descripcion' => $infRol['r_description'],
            'estado' => $infRol['r_status'],
            'fecha_registro' => dateFormat($infRol['r_registrationDate']),
            'fecha_actualizacion' => dateFormat($infRol['r_updateDate']),
        ];

        // Paleta de colores del reporte
        $colores = [
            'primario' => hexToRgb($systemInfo["c_color_primary"]),
            'secundario' => hexToRgb($systemInfo["c_color_secondary"]),
        ];

        // Mapeo de valores booleanos
        $mapBool = ['0' => 'No', '1' => 'Sí'];

        // Procesar permisos
        $permisos = [];
        foreach ($detailRol as $modulo) {
            if (!isset($modulo['interface']) || !is_array($modulo['interface'])) {
                continue;
            }

            foreach ($modulo['interface'] as $interfaz) {
                $permisos[] = [
                    'nombre' => $interfaz['i_name'],
                    'ruta' => $interfaz['i_url'],
                    'menu' => $mapBool[(string) $interfaz['i_isOption']],
                    'publico' => $mapBool[(string) $interfaz['i_isPublic']],
                    'menu_nav' => $mapBool[(string) $interfaz['i_isListNav']],
                    'descripcion' => $interfaz['i_description'] ?? ''
                ];
            }
        }

        // =============== Generación del PDF =============== //
        require_once "./Libraries/fpdf186/rol.php";
        $pdf = new rol($headerData, $colores);
        $nombre_pdf = $infRol['idRole'] . "-" . $infRol['r_name'];
        $pdf->SetTitle($nombre_pdf);
        $pdf->generarReporteRol($rolData, $permisos);
        $pdf->Output('I', $nombre_pdf . ".pdf");

        // Limpiar memoria
        unset($infRol, $detailRol);
    }

}