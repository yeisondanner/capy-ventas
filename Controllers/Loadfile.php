<?php

class Loadfile extends Controllers
{
    public function __construct()
    {
        parent::__construct("");
    }

    /**
     * Funcion que carga la imagen de perfil del usuario
     * @return void
     */
    public function profile()
    {
        if (isset($_GET['f'])) {
            $nameFile = $_GET['f'];
            $path = getRoute();
            $filePath = $path . "Profile/Users/" . basename($nameFile);
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && in_array($fileType, ["jpg", "jpeg", "png", "JPG", "JPEG", "PNG"])) {
                header("Content-Type: image/$fileType");
                readfile($filePath);
                exit;
            } else {
                //cargamos una imagen por defecto
                header("Content-Type: image/png");
                readfile(getRoute() . "Profile/Users/user.png");
            }
        } else {
            //cargamos una imagen por defecto
            header("Content-Type: image/png");
            readfile(getRoute() . "Profile/Users/user.png");
        }
    }
    /**
     * Funcion que carga la imagen de perfil del sistema
     * @return void
     */
    public function icon()
    {
        if (isset($_GET['f'])) {
            $nameFile = $_GET['f'];
            $path = getRoute();
            $filePath = $path . "Profile/Logo/" . basename($nameFile);
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && in_array($fileType, ["jpg", "jpeg", "png", "JPG", "JPEG", "PNG"])) {
                header("Content-Type: image/$fileType");
                readfile($filePath);
                exit;
            } else {
                //cargamos una imagen por defecto
                header("Content-Type: image/png");
                readfile($filePath . "Profile/Logo/sin-content.png");
            }
        } else {
            //cargamos una imagen por defecto
            header("Content-Type: image/png");
            readfile(getRoute() . "Profile/Logo/sin-content.png");
        }
    }
    /**
     * Metodo que entrega archivos compatibles para su visualizacion dentro del modulo Clust.
     *
     * @return void
     */
    public function viewer()
    {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $filePath = $this->resolveFileFromToken($token);
        if ($filePath === '') {
            $this->sendNotFound();
        }
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $this->resolveMimeType($extension, $filePath);
        $fileName = basename($filePath);
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        $size = @filesize($filePath);
        if ($size !== false) {
            header('Content-Length: ' . $size);
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filePath);
        exit;
    }
    /**
     * Metodo que fuerza la descarga de un archivo almacenado en el modulo Clust.
     *
     * @return void
     */
    public function download()
    {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $filePath = $this->resolveFileFromToken($token);
        if ($filePath === '') {
            $this->sendNotFound();
        }
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $this->resolveMimeType($extension, $filePath);
        $fileName = basename($filePath);
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        $size = @filesize($filePath);
        if ($size !== false) {
            header('Content-Length: ' . $size);
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filePath);
        exit;
    }
    /**
     * Resuelve la ruta absoluta de un archivo a partir del token recibido por la interfaz.
     *
     * @param string $token Token cifrado con la ruta relativa del archivo.
     * @return string Ruta absoluta validada o cadena vacía si es inválida.
     */
    private function resolveFileFromToken(string $token): string
    {
        if ($token === '') {
            return '';
        }
        $decoded = decryption($token);
        if (!is_string($decoded) || $decoded === '') {
            return '';
        }
        $relativePath = str_replace('\\', '/', $decoded);
        $segments = array_filter(explode('/', $relativePath), function ($segment) {
            return $segment !== '' && $segment !== '.' && $segment !== '..';
        });
        if (empty($segments)) {
            return '';
        }
        $basePath = rtrim(getRoute(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'data';
        $realBase = realpath($basePath);
        if ($realBase === false) {
            return '';
        }
        $fullPath = $realBase . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments);
        $realFile = realpath($fullPath);
        if ($realFile === false || strpos($realFile, $realBase) !== 0 || !is_file($realFile)) {
            return '';
        }
        return $realFile;
    }
    /**
     * Devuelve el tipo MIME adecuado segun la extension del archivo.
     *
     * @param string $extension Extension del archivo en minusculas.
     * @param string $filePath Ruta absoluta del archivo evaluado.
     * @return string Tipo MIME reconocido por el navegador.
     */
    private function resolveMimeType(string $extension, string $filePath = ''): string
    {
        $extension = strtolower(trim($extension));
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain; charset=UTF-8',
            'log' => 'text/plain; charset=UTF-8',
            'csv' => 'text/csv; charset=UTF-8',
            'json' => 'application/json',
            'md' => 'text/markdown; charset=UTF-8',
            'xml' => 'application/xml',
        ];
        if ($extension !== '' && isset($map[$extension])) {
            return $map[$extension];
        }
        if ($filePath !== '' && is_file($filePath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = finfo_file($finfo, $filePath);
                finfo_close($finfo);
                if (is_string($detected) && $detected !== '') {
                    return $detected;
                }
            }
        }
        return 'application/octet-stream';
    }
    /**
     * Envía una respuesta 404 y detiene la ejecucion del script.
     *
     * @return void
     */
    private function sendNotFound(): void
    {
        http_response_code(404);
        exit;
    }
}
