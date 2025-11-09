<?php
class Clust extends Controllers
{
    public function __construct()
    {
        isSession();
        parent::__construct();
    }
    /**
     * Muestra el módulo Clust (gestión de archivos y carpetas).
     *
     * Requisitos:
     * - Permiso de acceso para la página con page_id = 11 (permissionInterface()).
     *
     * Efectos colaterales:
     * - Registra un evento de auditoría (page_view) mediante registerLog() con
     *   contexto del request (usuario, IP, método, URL, user-agent, timestamp).
     * - Renderiza la vista 'clust' con datos de página, assets versionados y componentes.
     *
     * Seguridad:
     * - Accesos defensivos a $_SESSION y $_SERVER.
     * - Soporte para entornos con proxy/CDN (HTTP_X_FORWARDED_FOR).
     *
     * @return void
     */
    public function clust()
    {
        // Datos de la página (asignación única)
        $data = [
            'page_id'          => 11,
            'page_title'       => 'Clust - Gestión de archivos y carpetas',
            'page_description' => 'Administra tus archivos y carpetas de manera eficiente.',
            'page_container'   => 'Clust',
            'page_view'        => 'clust',
            'page_js_css'      => 'clust',
            'page_vars'        => ['login', 'login_info', 'folder_open'],
        ];

        // Autorización temprana
        permissionInterface($data['page_id']);

        // Usuario actual (defensivo)
        $userId = isset($_SESSION['login_info']['idUser']) ? (int) $_SESSION['login_info']['idUser'] : null;

        // Componentes del módulo (ej. uso de almacenamiento)
        // Si el usuario no está definido, dejamos null para que la vista lo maneje.
        $spaceUsed = null;
        if ($userId !== null) {
            // select_space_used() debería devolver estructura esperada por la vista.
            // Envolvemos en (int) o validamos según tu implementación si retorna arreglo.
            $spaceUsed = $this->model->select_space_used($userId) ?? null;
        }
        $data['page_components'] = [
            'storage' => $spaceUsed,
        ];

        // Contexto de request/usuario (defensivo + soporte proxy/CDN)
        $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
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
            'timestamp'  => date('c'), // ISO-8601
        ];

        // Registro (nivel 3 asumido como INFO)
        registerLog(
            'Navegación',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            3,
            $userId
        );

        // Render de la vista
        $this->views->getView($this, 'clust', $data);
    }

    /**
     * Metodo que obtiene los archivos y carpetas
     * @return void
     */
    public function getFiles()
    {
        $requestFiles = $this->resolveActiveFolder();
        //validamos que la carpeta que consulto exista su informacion correspondiente
        if (empty($requestFiles)) {
            if (isset($_SESSION['folder_open'])):
                unset($_SESSION['folder_open']);
            endif;
            registerLog("Ocurrió un error inesperado", "La carpeta que el usuario esta solicitando no se encuentra en la base de datos", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Folder no encontrado o no tienes permiso para acceder, por favor intentalo mas tarde",
                "type" => "error",
                "url" => base_url() . "/im/clust",
                "status" => false
            );
            toJson($data);
        }
        $idFolder = $requestFiles["idFolder"];
        $requestFolders = $this->model->select_folders($idFolder);
        foreach ($requestFolders as $key => $value) {
            if ($value["user_id"] != $_SESSION['login_info']['idUser']) {
                $requestFolders[$key]["iconUser"] = '<i class="fa fa-user text-success position-absolute" title="Esta carpeta pertenece al usuario con codigo ' . $value['user_id'] . '" style="bottom: 0%;"></i>';
            } else {
                $requestFolders[$key]["iconUser"] = '';
            }
            $requestFolders[$key]["name_short"] = limitarCaracteres($value['f_name'], 20, "...");
            $requestFolders[$key]["full_name_encryption"] = encryption($value['f_name']);
            $requestFolders[$key]["fullnamefolder"] = $value['f_name'];
        }
        $requestFiles = $this->model->select_files_by_folder($idFolder);
        foreach ($requestFiles as $key => $value) {
            $requestFiles[$key]['name_short'] = limitarCaracteres($value['f_name'], 20, "...");
            $requestFiles[$key]['full_name_encryption'] = encryption($value['f_name']);
        }
        //recolectamos la url breadcrumb
        $breadcrumbTrail = $this->trimBreadcrumbToUserRoot(
            $this->buildBreadcrumbTrail($idFolder)
        );
        $breadcrumbNames = array_column($breadcrumbTrail, 'name');
        $breadcrumbconcat = implode('/', $breadcrumbNames);
        if ($breadcrumbconcat !== '') {
            $breadcrumbconcat .= '/';
        }
        $arraybreadcrumb = $breadcrumbNames;
        if (!empty($arraybreadcrumb)) {
            $arraybreadcrumb[] = '';
        }
        $array = [
            "folders" => $requestFolders,
            "files" => $requestFiles,
            "breadcrumbconcat" => $breadcrumbconcat,
            "arraybreadcrumb" => $arraybreadcrumb,
            "breadcrumbTrail" => $breadcrumbTrail,
            "status" => true
        ];
        toJson($array);
    }

    /**
     * Metodo que se encarga de registrar un nuevo folder dentro de la basde de datos
     * @return void
     */
    public function createFolder()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf();
        validateFields(["txtName", "txtIdFather"]);
        $name = strClean($_POST['txtName']);
        $iduser = $_SESSION['login_info']['idUser'];
        $requestFolderRoot = $this->resolveActiveFolder();
        if (empty($requestFolderRoot)) {
            registerLog("Ocurrió un error inesperado", "No se encontró la carpeta de destino para crear la nueva carpeta", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontró la carpeta seleccionada para crear el nuevo directorio. Por favor, actualiza la página e intenta nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $idfather = $requestFolderRoot['idFolder'];
        //validamos que los campos no sean vacios
        validateFieldsEmpty(
            ["NOMBRE" => $name]
        );
        //validamos que la cantidad de caracteres no supere el limite permitido
        if (strlen($name) > 255) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' excede el límite de caracteres permitido (255). Por favor, ingrese un nombre más corto.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' excede el límite de caracteres permitido (255). Por favor, ingrese un nombre más corto.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el nombre no supere el minimo de caracteres permitidos
        if (strlen($name) < 1) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' no puede estar vacío. Por favor, ingrese un nombre válido para la carpeta del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' no puede estar vacío. Por favor, ingrese un nombre válido para la carpeta del usuario.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el campo del nombre de la carpeta tenga la estructura correcta para una carpeta
        if (verifyData("[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ_-]{1,255}", $name)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' presenta un formato inválido. Ingrese un nombre válido para la carpeta del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' no cumple con el formato requerido. Asegúrese de ingresar un nombre válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el nombre de la carpeta no sea el mismo
        $dataFolder = $this->model->select_folder_by_id_and_name_and_father($name, $idfather);
        if ($dataFolder) {
            //registro de logs
            registerLog("Ocurrio un error inesperado", "El nombre de la carpeta ya existe para este usuario por favor cambie el nombre", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "El nombre de la carpeta ya existe para este usuario, por favor cambie a otro nombre",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $requestInsertForlder = $this->model->insert_folder($iduser, $name, $idfather);
        //validamos el registro del nuevo archivos
        if ($requestInsertForlder > 0) {
            //validamos la creacion de las carpetas
            $url = getRoute();
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz Storage no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $url .= "data";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz data no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            //Reconstruimos la ruta donde se va almacenar la carpeta
            $url .= "/" . $this->rebuildStoragePath($idfather);
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz del usuario no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $url .= "/" . $name;
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz data no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            //registro de logs
            registerLog("Logrado con exito", "Se registro y creo de manera exitosa la carpeta", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Logrado con exito",
                "message" => "Carpeta creada con exito",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            //registro de logs
            registerLog("Ocurrio un error inesperado", "No se logro registrar la carpeta en la base de datos", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrio un error inesperado",
                "message" => "No se logro completar la creación de la carpeta",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que se encarga de eliminar un folder con todos sus archivos que contiene
     */
    public function deleteFolderAndFiles()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf();
        validateFields(['id', 'name', 'type']);
        $id = strClean($_POST['id']);
        $name = strClean($_POST['name']);
        $type = strClean($_POST['type']);
        validateFieldsEmpty(['ID' => $id, 'NOMBRE' => $name, 'TIPO' => $type]);
        if (!intval($id)) {
            registerLog("Ocurrió un error inesperado", "El ID no es numerico, por favor refresca la pagina e intenta nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El ID del archivo no es numerico, por favor refresca la pagina e intenta nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos si es folder o archivo
        if ($type === "folder") {
            $datafolder = $this->model->select_folder_id($_SESSION['login_info']['idUser'], $id);
            //validamos que si el id asociado al folder sea el mismo del usuario que inicio seison para pode eliminarlo
            if (!$datafolder) {
                registerLog("Ocurrió un error inesperado", "No se encontro la carpeta solicitada, por favor refresque la pagina e intentelo nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se encontro la carpeta solicitada o no tiene permiso de eliminar esta carpeta, refresca la pagina e intentalo nuevamente",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //reconstruimos la url antes de eliminar el registro de la base de datos
            $url = getRoute();
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz Storage no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $url .= "data";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz data no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $url .= "/" . $this->rebuildStoragePath($id);
            //metodo que se encarga de eliminar la carpeta 
            $arrForlderDelete = $this->model->delete_folder($id);
            if ($arrForlderDelete) {
                if (delFolder($url, "*", true)) {
                    registerLog("Ocurrio un error inesperado", "La carpeta seleccionada " . $id . " no se pudo eliminar, ruta " . $url, 1, $_SESSION['login_info']['idUser']);
                }
                registerLog("satisfactorio", "Se elimino de manera correcta la carpeta", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Satisfactorio",
                    "message" => "Se elimino de manera correcta la carpeta",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            } else {
                registerLog("Ocurrió un error inesperado", "No se logro eliminar la carpeta por favor intentalo nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se logro completo eliminar la carpeta intentalo nuevamente",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        } else if ($type === "file") {
            //codigo para eliminar archivo
            $datafile = $this->model->select_file_id($_SESSION['login_info']['idUser'], $id);
            //validamos que si el id asociado al folder sea el mismo del usuario que inicio seison para pode eliminarlo
            if (!$datafile) {
                registerLog("Ocurrió un error inesperado", "No se encontro el archivo solicitado, por favor refresque la pagina e intentelo nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se encontro el archivo solicitado o no tiene permiso de eliminar este archivo, refresca la pagina e intentalo nuevamente",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //reconstruimos la url antes de eliminar el registro de la base de datos
            $url = getRoute();
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz Storage no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $url .= "data";
            if (verifyFolder($url)) {
                //registro de logs
                registerLog("Ocurrio un error inesperado", "La carpeta raiz data no estaba creada, por lo que se procedio a crear", 1, $_SESSION['login_info']['idUser']);
            }
            $filenameextension = $datafile['f_name'] . '.' . $datafile['f_extension'];
            $url .= "/" . $this->rebuildStoragePath($datafile['folder_id']);
            //metodo que se encarga de eliminar el archivo 
            $arrFileDelete = $this->model->delete_file($id, $_SESSION['login_info']['idUser']);
            if ($arrFileDelete) {
                if (delFolder($url, $filenameextension, false)) {
                    registerLog("Ocurrio un error inesperado", "El archivo seleccionado " . $id . " no se pudo eliminar, ruta " . $url, 1, $_SESSION['login_info']['idUser']);
                }
                registerLog("satisfactorio", "Se elimino de manera correcta el archivo", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Satisfactorio",
                    "message" => "Se elimino de manera correcta el archivo",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            } else {
                registerLog("Ocurrió un error inesperado", "No se logro eliminar el archivo por favor intentalo nuevamente", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se logro completo eliminar el archivo intentalo nuevamente",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        } else {
            registerLog("Ocurrió un error inesperado", "El tipo de elemento a eliminar no es válido", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tipo de elemento a eliminar no es válido",
                "type" => "error",
                "status" => false
            );
        }
    }
    /**
     * Resuelve la carpeta activa tomando como referencia la sesión del usuario.
     *
     * El método intenta recuperar la carpeta seleccionada por el usuario utilizando el
     * identificador almacenado en la sesión. Si no está disponible, intenta resolverla
     * por nombre y, en última instancia, retorna la carpeta raíz del usuario.
     *
     * @return array Datos de la carpeta activa o un arreglo vacío si no se encuentra.
     */
    private function resolveActiveFolder(): array
    {
        $userId = $_SESSION['login_info']['idUser'];
        if (isset($_SESSION['folder_open']) && is_array($_SESSION['folder_open'])) {
            $sessionFolder = $_SESSION['folder_open'];
            if (!empty($sessionFolder['id'])) {
                $folder = $this->model->select_folder_of_user_for_ids((int) $sessionFolder['id'], $userId);
                if (!empty($folder)) {
                    $_SESSION['folder_open'] = [
                        'id' => (int) $folder['idFolder'],
                        'name' => $folder['f_name']
                    ];
                    return $folder;
                }
            }
            if (!empty($sessionFolder['name'])) {
                $folder = $this->model->select_folder_root($userId, $sessionFolder['name']);
                if (!empty($folder)) {
                    $_SESSION['folder_open'] = [
                        'id' => (int) $folder['idFolder'],
                        'name' => $folder['f_name']
                    ];
                    return $folder;
                }
            }
        }
        $folder = $this->model->select_folder_root($userId, $_SESSION['login_info']['folder_name']);
        return $folder ?? [];
    }
    /**
     * Construye la jerarquía de carpetas desde la raíz hasta la carpeta solicitada.
     *
     * @param int $folderId Identificador de la carpeta objetivo.
     * @return array Lista ordenada de carpetas con sus identificadores y nombres.
     */
    private function buildBreadcrumbTrail(int $folderId): array
    {
        $trail = [];
        $currentId = $folderId;
        $maxIterations = 100;
        $iterations = 0;
        while ($currentId > 0 && $iterations < $maxIterations) {
            $folder = $this->model->select_folder_of_user_for_ids($currentId);
            if (empty($folder)) {
                break;
            }
            $trail[] = [
                'id' => (int) $folder['idFolder'],
                'name' => $folder['f_name']
            ];
            $parentId = isset($folder['f_idFather']) ? (int) $folder['f_idFather'] : 0;
            if ($parentId === 0 || $parentId === (int) $folder['idFolder']) {
                break;
            }
            $currentId = $parentId;
            $iterations++;
        }
        return array_reverse($trail);
    }
    /**
     * Limita el rastro de carpetas a la raíz perteneciente al usuario autenticado.
     *
     * @param array $trail Rastro completo devuelto por buildBreadcrumbTrail.
     * @return array Segmentos del breadcrumb visibles para el usuario.
     */
    private function trimBreadcrumbToUserRoot(array $trail): array
    {
        $rootName = $_SESSION['login_info']['folder_name'] ?? '';
        if ($rootName === '') {
            return $trail;
        }
        foreach ($trail as $index => $segment) {
            if (isset($segment['name']) && $segment['name'] === $rootName) {
                return array_slice($trail, $index);
            }
        }
        return $trail;
    }
    /**
     * Reconstruye la ruta de almacenamiento de un usuario desde su carpeta en la BD.
     *
     * El método:
     * - Obtiene la carpeta base según el ID de usuario/carpeta.
     * - Recorre la jerarquía de carpetas subiendo hasta la raíz.
     * - Construye la ruta en el orden correcto (de raíz → hoja).
     *
     * @param int $folderId ID de la carpeta desde la que se parte (ej: carpeta del usuario).
     * @return string Ruta completa reconstruida (ej: "root/carpeta1/carpeta2/").
     */
    private function rebuildStoragePath(int $folderId): string
    {
        $pathParts = [];

        // Obtener la carpeta inicial
        $folder = $this->model->select_folder_of_user_for_ids($folderId);

        // Subir por la jerarquía hasta llegar a la raíz
        while ($folder && $folder['idFolder'] != 1) {
            $pathParts[] = $folder['f_name'];
            $folder = $this->model->select_folder_of_user_for_ids($folder['f_idFather']);
        }

        // Agregar el nodo raíz si existe
        if ($folder) {
            $pathParts[] = $folder['f_name'];
        }

        // Invertir para que quede de raíz a hoja
        $pathParts = array_reverse($pathParts);

        // Construir la ruta final con "/"
        return implode("/", $pathParts) . "/";
    }
    /**
     * Convierte un tamaño en bytes a un formato legible para el usuario final.
     *
     * @param int $bytes Tamaño en bytes recuperado desde la base de datos.
     * @return string Cadena formateada con la unidad adecuada.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $size = (float) $bytes;
        $index = 0;
        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }
        $decimals = $index === 0 ? 0 : ($size < 10 ? 2 : 1);
        $formatted = number_format($size, $decimals, ',', '.');
        return $formatted . ' ' . $units[$index];
    }
    /**
     * Formatea una fecha proveniente de la base de datos para su visualización.
     *
     * @param string|null $timestamp Fecha con formato compatible con MySQL.
     * @return string Fecha formateada (dd/mm/aaaa hh:mm) o '-' si no es válida.
     */
    private function formatTimestampForDisplay(?string $timestamp): string
    {
        if ($timestamp === null || $timestamp === '' || $timestamp === '0000-00-00 00:00:00') {
            return '-';
        }
        try {
            $date = new \DateTime($timestamp);
            return $date->format('d/m/Y H:i');
        } catch (\Exception $exception) {
            return $timestamp;
        }
    }
    /**
     * Normaliza la ruta relativa del archivo para mostrarla en el visor.
     *
     * @param string $relativeFolder Ruta reconstruida del contenedor del archivo.
     * @return string Ruta legible para el usuario.
     */
    private function normalizeViewerLocation(string $relativeFolder): string
    {
        $normalized = str_replace('\\', '/', $relativeFolder);
        $normalized = trim($normalized, '/');
        return $normalized === '' ? '-' : $normalized;
    }
    /**
     * Determina la configuración de previsualización adecuada de acuerdo a la extensión del archivo.
     *
     * @param string $extension Extensión del archivo evaluado.
     * @return array Arreglo con llaves 'preview' (bool) y 'render' (string) para el front-end.
     */
    private function resolvePreviewConfig(string $extension): array
    {
        $extension = strtolower($extension);
        $imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp", "svg"];
        $textExtensions = ["txt", "log", "csv", "json", "md", "xml"];
        if (in_array($extension, $imageExtensions, true)) {
            return ["preview" => true, "render" => "image"];
        }
        if ($extension === "pdf") {
            return ["preview" => true, "render" => "pdf"];
        }
        if (in_array($extension, $textExtensions, true)) {
            return ["preview" => true, "render" => "text"];
        }
        return ["preview" => false, "render" => "download"];
    }
    /**
     * Metodo que se encarga de modificar el nombre de la carpeta seleccionada
     */
    public function updateFolderAndFiles()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf();
        validateFields(["id", "name", "update_txtName", "type"]);
        //almacenamos los valores dentro de las variables
        $id = strClean($_POST["id"]);
        $name = strClean($_POST["name"]);
        $update_txtName = strClean($_POST["update_txtName"]);
        $type = strClean($_POST["type"]);
        //validamos que no esten vacios
        validateFieldsEmpty([
            "ID" => $id,
            "NOMBRE ORIGINAL" => $name,
            "NOMBRE ACTUAL" => $update_txtName,
            "TIPO DE ELEMENTO" => $type
        ]);
        //validamos que la cantidad de caracteres no supere el limite permitido
        if (strlen($update_txtName) > 255) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' excede el límite de caracteres permitido (255). Por favor, ingrese un nombre más corto.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' excede el límite de caracteres permitido (255). Por favor, ingrese un nombre más corto.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el nombre no supere el minimo de caracteres permitidos
        if (strlen($update_txtName) < 1) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' no puede estar vacío. Por favor, ingrese un nombre válido para la carpeta del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' no puede estar vacío. Por favor, ingrese un nombre válido para la carpeta del usuario.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //validamos que el campo del nombre de la carpeta tenga la estructura correcta para una carpeta
        if (verifyData("[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ_-]{1,255}", $update_txtName)) {
            registerLog("Ocurrió un error inesperado", "El campo 'Nombre de la carpeta' presenta un formato inválido. Ingrese un nombre válido para la carpeta del usuario.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El campo 'Nombre de la carpeta' no cumple con el formato requerido. Asegúrese de ingresar un nombre válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //obtenemos el nombre de la ruta base pero de almacenamiento
        $urlbase = getRoute();
        //concatenamos la ruta data
        $urlbase .= "data/";
        //validamos si el tipo es folder o file
        if ($type === "folder") {
            $urlOrginal = $urlbase . $this->rebuildStoragePath($id);
            // 1. Eliminar la última barra para evitar problemas
            $urllimpia = rtrim($urlOrginal, '/');
            // 2. Obtener la carpeta padre (sin la última parte)
            $urlfather = dirname($urllimpia);
            // 3. Construir la nueva ruta
            $newurl = $urlfather . '/' . $update_txtName;
            //cambiamos el nombre del archivo 
            if (!@rename($urllimpia, $newurl)) {
                registerLog("Ocurrió un error inesperado", "No se logro el cambio del nombre de la carpeta", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El cambio del nombre de la carpeta no se pudo realizar",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            $request = $this->model->update_folder($id, $_SESSION['login_info']['idUser'], $update_txtName);
            if ($request) {
                registerLog("Accion lograda", "Nombre de carpeta cambiado correctamente", 2, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Correcto",
                    "message" => "Nombre cambiado",
                    "type" => "success",
                    "status" => true
                );
                toJson($data);
            } else {
                registerLog("Ocurrió un error inesperado", "No se logro el cambio del nombre de la carpeta", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El cambio del nombre de la carpeta no se pudo realizar",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
        } else if ($type === "file") {
            $requestFiles = $this->resolveActiveFolder();
            if (empty($requestFiles)) {
                registerLog("Ocurrió un error inesperado", "No se encontró la carpeta contenedora del archivo a renombrar", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "No se encontró la carpeta contenedora del archivo seleccionado. Por favor, actualiza la página e intenta nuevamente.",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //consultamos si el archivo existe dentro de la carpeta solicitada como tambien en la base de datos
            $dataFile = $this->model->select_file_by_idfolder_and_name($requestFiles['idFolder'], $name);
            $urlWhitFile = $urlbase . $this->rebuildStoragePath($requestFiles['idFolder']);
            $urlOrginalFile = $urlWhitFile . $name . "." . $dataFile['f_extension'];
            $urlUpdateFile = $urlWhitFile . $update_txtName . "." . $dataFile['f_extension'];
            //realizamos el cambio del nombre de los archivos
            if (!@rename($urlOrginalFile, $urlUpdateFile)) {
                registerLog("Ocurrió un error inesperado", "No se logro el cambio del nombre del archivo", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El cambio del nombre del archivo no se pudo realizar",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            //procedemos a actualizar la informacion dentro de la bd
            $requestFile = $this->model->update_file($dataFile['idFile'], $_SESSION['login_info']['idUser'], $update_txtName);
            if (!$requestFile) {
                registerLog("Ocurrió un error inesperado", "No se logro el cambio del nombre del archivo", 1, $_SESSION['login_info']['idUser']);
                $data = array(
                    "title" => "Ocurrió un error inesperado",
                    "message" => "El cambio del nombre del archivo no se pudo realizar",
                    "type" => "error",
                    "status" => false
                );
                toJson($data);
            }
            registerLog("Accion lograda", "Nombre de archivo cambiado correctamente", 2, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Correcto",
                "message" => "Nombre cambiado",
                "type" => "success",
                "status" => true
            );
            toJson($data);
        } else {
            registerLog("Ocurrió un error inesperado", "El tipo de elemento no es valido", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tipo de elemento no es valido",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
    }
    /**
     * Metodo que se encarga de ingresar a la carpeta
     * este metodo se encarga de registra un nuevo valor a la carpeta
     */
    public function open_folder()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        validateFields(["id", "name"]);
        $id = intval(strClean($_POST["id"]));
        $name = strClean($_POST["name"]);
        validateFieldsEmpty([
            "ID" => $id,
            "NOMBRE DE LA CARPETA" => $name
        ]);
        $decodedName = decryption($name);
        if ($decodedName === '' || !is_string($decodedName)) {
            registerLog("Ocurrió un error inesperado", "No se pudo descifrar el nombre de la carpeta solicitada", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No fue posible identificar la carpeta seleccionada. Por favor, actualiza la página e intenta nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        if (isset($_SESSION['folder_open'])) {
            unset($_SESSION['folder_open']);
        }
        //pasamos el nombre del nombre de descriptado, para ello el valor debe venir encriptado
        $_SESSION['folder_open'] = ["id" => $id, "name" => $decodedName];
        registerLog("Satisfactorio", "Ingreso a la carpeta de manera correcta", 2, $_SESSION['login_info']['idUser']);
        $data = array(
            "title" => "Satisfactorio",
            "message" => "Ingreso a la carpeta de manera correcta",
            "type" => "success",
            "status" => true
        );
        toJson($data);
    }
    /**
     * Metodo que se encarga de ingresar a la carpeta
     * este metodo se encarga de registra un nuevo valor a la carpeta
     */
    public function open_folder_for_breadcrumb()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        validateFields(["name"]);
        $id = isset($_POST['id']) ? intval(strClean($_POST['id'])) : 0;
        $name = strClean($_POST["name"]);
        validateFieldsEmpty([
            "NOMBRE DE LA CARPETA" => $name
        ]);
        if (isset($_SESSION['folder_open'])) {
            unset($_SESSION['folder_open']);
        }
        $resolvedFolder = [];
        if ($id > 0) {
            $resolvedFolder = $this->model->select_folder_of_user_for_ids($id, $_SESSION['login_info']['idUser']);
        }
        if (empty($resolvedFolder)) {
            $resolvedFolder = $this->model->select_folder_root($_SESSION['login_info']['idUser'], $name);
        }
        if (!empty($resolvedFolder)) {
            $_SESSION['folder_open'] = [
                "id" => (int) $resolvedFolder['idFolder'],
                "name" => $resolvedFolder['f_name']
            ];
        } else {
            $_SESSION['folder_open'] = ["name" => $name];
        }
        registerLog("Satisfactorio", "Ingreso a la carpeta de manera correcta", 2, $_SESSION['login_info']['idUser']);
        $data = array(
            "title" => "Satisfactorio",
            "message" => "Ingreso a la carpeta de manera correcta",
            "type" => "success",
            "status" => true
        );
        toJson($data);
    }
    /**
     * Metodo que construye la informacion necesaria para visualizar o descargar un archivo.
     *
     * @return void
     */
    public function getFileResource()
    {
        permissionInterface(11);
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        validateFields(["id", "code"]);
        $idFile = intval($_POST['id']);
        $code = trim((string) $_POST['code']);
        validateFieldsEmpty([
            "ID DEL ARCHIVO" => $idFile,
            "CODIGO DEL ARCHIVO" => $code
        ]);
        $file = $this->model->select_file_by_id_and_user($idFile, $_SESSION['login_info']['idUser']);
        if (empty($file)) {
            registerLog("Ocurrió un error inesperado", "No se encontró información del archivo solicitado o no pertenece al usuario", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se encontró información del archivo seleccionado, por favor verifica e intenta nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //encriptamos el nombre del archivo para comparar con el codigo enviado
        $expectedCode = encryption($file['f_name']);
        if (!hash_equals($expectedCode, $code)) {
            registerLog("Ocurrió un error inesperado", "El código recibido no coincide con el archivo solicitado", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El archivo solicitado no coincide con la información enviada, por favor actualiza la página e intenta nuevamente.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $relativeFolder = $this->rebuildStoragePath($file['folder_id']);
        $relativePath = $relativeFolder . $file['f_name'] . '.' . $file['f_extension'];
        $fullPath = getRoute() . 'data/' . $relativePath;
        if (!is_file($fullPath)) {
            registerLog("Ocurrió un error inesperado", "El archivo solicitado no existe en la ruta esperada: " . $relativePath, 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El archivo solicitado no se encuentra disponible, por favor verifica que no haya sido eliminado.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $previewConfig = $this->resolvePreviewConfig($file['f_extension']);
        $fileSizeBytes = (int) $file['f_size'];
        $sizeReadable = $this->formatFileSize($fileSizeBytes);
        $registeredAt = $this->formatTimestampForDisplay($file['f_registrationDate'] ?? '');
        $updatedAt = $this->formatTimestampForDisplay($file['f_updateDate'] ?? '');
        $locationLabel = $this->normalizeViewerLocation($relativeFolder);
        $token = encryption($relativePath);
        $downloadUrl = base_url() . '/loadfile/download?token=' . urlencode($token);
        $viewerUrl = $previewConfig['preview'] ? base_url() . '/loadfile/viewer?token=' . urlencode($token) : '';
        registerLog("Información de navegación", "El usuario solicitó el visor para el archivo: " . $file['f_name'] . '.' . $file['f_extension'], 3, $_SESSION['login_info']['idUser']);
        $data = array(
            "title" => "Recurso disponible",
            "message" => "Archivo listo para su visualización o descarga.",
            "type" => "success",
            "status" => true,
            "filename" => $file['f_name'] . '.' . $file['f_extension'],
            "extension" => strtolower($file['f_extension']),
            "preview" => $previewConfig['preview'],
            "render" => $previewConfig['render'],
            "viewer" => $viewerUrl,
            "download" => $downloadUrl,
            "path" => $relativePath,
            "location" => $locationLabel,
            "size_bytes" => $fileSizeBytes,
            "size_readable" => $sizeReadable,
            "registered_at" => $registeredAt,
            "updated_at" => $updatedAt
        );
        toJson($data);
    }
    /**
     * Metodo que se encagra de subir los archivos al servidor
     * y registrar la informacion en la base de datos
     */
    public function upload_files()
    {
        permissionInterface(11);
        // Validación del método POST
        if (!$_POST) {
            registerLog("Ocurrió un error inesperado", "Método POST no encontrado, para poder realizar la operacion es necesario este metodo, por favor refresca la pagina", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Método POST no encontrado",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        isCsrf();
        validateFields(["inputName"]);
        $inputName = strClean($_POST["inputName"]);
        //obtenemos el archivo
        $file = $_FILES['inputFiles'];
        //validamos que el archivo no sea mayor a lo permitido por el servidor
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $intuploadMax = intval(str_replace("M", "", $uploadMax));
        $intpostMax = intval(str_replace("M", "", $postMax));
        $sizeMb = valConvert($file['size'])["MB"];
        if ($sizeMb > $intuploadMax || $sizeMb > $intpostMax) {
            registerLog("Ocurrió un error inesperado", "El tamaño del archivo supera el limite permitido por el servidor, tamaño maximo permitido " . $uploadMax, 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El tamaño del archivo supera el limite permitido por el servidor, tamaño maximo permitido " . $uploadMax,
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //obtenemos el id del usuario
        $iduser = $_SESSION['login_info']['idUser'];
        //validamos que  la capacidad de almacenamiento del usuario no se haya superado
        $space_remaining = $this->model->select_total_space_remaining($iduser);
        //validamos que archivo a subir no supere el espacio restante del usuario
        if ($file['size'] > $space_remaining) {
            registerLog("Ocurrió un error inesperado", "No tienes espacio suficiente para subir este archivo, por favor libera espacio e intentalo nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No tienes espacio suficiente para subir este archivo, por favor libera espacio e intentalo nuevamente, o aumenta tu plan de almacenamiento si es posible",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //obtenemos el nombre y la extension del archivo
        $nameFile = $file['name'];
        $extension = pathinfo($nameFile, PATHINFO_EXTENSION);
        $name = pathinfo($nameFile, PATHINFO_FILENAME);
        $nameFile = $inputName . "." . $extension;
        //validamos que el nombre del archivo este correcto
        if (verifyData("[a-zA-Z0-9 _-]{1,255}", $inputName)) {
            registerLog("Ocurrió un error inesperado", "El nombre del archivo no cumple con el formato requerido. Asegúrese de ingresar un nombre válido.", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El nombre del archivo no cumple con el formato requerido. Asegúrese de ingresar un nombre válido.",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        $requestFiles = $this->resolveActiveFolder();
        //validamos que la que no este vacio el array de request ya que esta valida si la carpeta existe y esta permitida por el usuario
        if (empty($requestFiles)) {
            if (isset($_SESSION['folder_open'])):
                unset($_SESSION['folder_open']);
            endif;
            registerLog("Ocurrió un error inesperado", "La carpeta que el usuario esta solicitando no se encuentra en la base de datos", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "Folder no encontrado o no tienes permiso para acceder, por favor intentalo mas tarde",
                "type" => "error",
                "url" => base_url() . "/clust",
                "status" => false
            );
            toJson($data);
        }

        //preparacion de la ruta donde se va almacenar el archivo y obtencion de los datos del archivo
        $route = getRoute();
        $size = $file['size'];
        $rebuildPath = $this->rebuildStoragePath($requestFiles['idFolder']);
        //validamos que el nombre del archivo no exista en la base de datos para la carpeta seleccionada
        $dataFile = $this->model->select_file_by_idfolder_and_name($requestFiles['idFolder'], $inputName);
        if ($dataFile) {
            registerLog("Ocurrió un error inesperado", "El nombre del archivo ya existe en la carpeta seleccionada, por favor cambie el nombre del archivo e intente nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "El nombre del archivo ya existe en la carpeta seleccionada, por favor cambie el nombre del archivo e intente nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //prodedemos a mover el archivo a la carpeta seleccionada
        $route .= "data/" . $rebuildPath;
        if (!move_uploaded_file($file['tmp_name'], $route . $nameFile)) {
            registerLog("Ocurrió un error inesperado", "No se logro mover el archivo a la carpeta seleccionada, por favor intentalo nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logro mover el archivo a la carpeta seleccionada, por favor intentalo nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        //insertamos el archivo en la carpeta seleccionada
        $requestInsertFile = $this->model->insert_file(
            $iduser,
            $requestFiles['idFolder'],
            $inputName,
            $extension,
            $size,
            '',
            0
        );
        //validamos que la insercion del archivo en la base de datos se haya realizado correctamente caso contrario debera mostrar un error
        if (!($requestInsertFile)) {
            registerLog("Ocurrió un error inesperado", "No se logro registrar el archivo en la base de datos, por favor intentalo nuevamente", 1, $_SESSION['login_info']['idUser']);
            $data = array(
                "title" => "Ocurrió un error inesperado",
                "message" => "No se logro registrar el archivo en la base de datos, por favor intentalo nuevamente",
                "type" => "error",
                "status" => false
            );
            toJson($data);
        }
        registerLog("Correcto", "Archivo subido con exito", 2, $_SESSION['login_info']['idUser']);
        $data = array(
            "title" => "Correcto",
            "message" => "Archivo subido con exito",
            "type" => "success",
            "status" => true
        );
        toJson($data);
    }
}
