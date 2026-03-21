<?php

/**
 * Definimos que el proyecto sea estricto para programar
 */

declare(strict_types=1);
//Llamamos a Composer desde la carpeta Libraries
require_once  './Libraries/vendor/autoload.php';
//inicializamos el dotenv y llamamos o cargamos el archivo .en que esta en la raiz del proyecto
$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();
/**
 * Generación dinámica de la URL base del proyecto.
 *
 * Este bloque construye automáticamente la constante BASE_URL,
 * asegurando que siempre apunte correctamente a la raíz de la aplicación,
 * independientemente del entorno en el que se ejecute (local, servidor, HTTP o HTTPS).
 *
 * Proceso:
 * 1. Determina el protocolo (http o https) según la variable $_SERVER['HTTPS'].
 * 2. Obtiene el host actual desde $_SERVER['HTTP_HOST'].
 * 3. Detecta el directorio donde se encuentra el script ejecutado.
 * 4. Concatena protocolo, host y directorio para formar la URL base.
 * 5. Define la constante BASE_URL garantizando que finalice con una barra (/).
 *
 * Ejemplos de resultados:
 * - http://localhost/index.php          → BASE_URL = "http://localhost/"
 * - http://localhost/sistema/index.php  → BASE_URL = "http://localhost/sistema/"
 * - https://miweb.com/app/test.php      → BASE_URL = "https://miweb.com/app/"
 *
 * Beneficios:
 * - Portabilidad entre distintos entornos.
 * - Centralización de la URL base para incluir recursos o redirigir.
 * - Soporte automático para HTTP y HTTPS.
 */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/');
$baseUrl = $protocol . '://' . $host . $scriptDir;
define('BASE_URL', rtrim($baseUrl, '/'));
//const BASE_URL = "http://192.168.1.12/sersa-ssoma";

//Ruta de almacenamiento de archivos
define('RUTA_ARCHIVOS', $_ENV['RUTA_ARCHIVOS']);
//Nombre del sistema
define('NOMBRE_SISTEMA', $_ENV['NOMBRE_SISTEMA']);
//Nombre de la compania
define('NOMBRE_COMPANIA', $_ENV['NOMBRE_COMPANIA']);
//Version sistema
define('VERSION_SISTEMA', $_ENV['VERSION_SISTEMA']);
//Zona horaria
date_default_timezone_set($_ENV['TIMEZONE']);

//Datos de conexión a Base de Datos
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);
define('DB_PORT', $_ENV['DB_PORT']);

//Deliminadores decimal y millar
define('SPD', $_ENV['SPD'] ?? '.');
define('SPM', $_ENV['SPM'] ?? ',');

//Simbolo de moneda
define('SMONEY', $_ENV['SMONEY']);

//Datos envio de correo
define('MAIL_HOST', $_ENV['MAIL_HOST']);
define('MAIL_PORT', $_ENV['MAIL_PORT']);
define('MAIL_USER', $_ENV['MAIL_USER']);
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD']);
define('MAIL_ENCRYPTION', $_ENV['MAIL_ENCRYPTION']);
define('MAIL_FROM', $_ENV['MAIL_FROM']); //nombre del remitente
define('MAIL_REMITENTE', $_ENV['MAIL_REMITENTE']);
//Variables de encriptacion
define('METHOD', $_ENV['METHOD']);
define('SECRET_KEY', $_ENV['SECRET_KEY']);
define('SECRET_IV', $_ENV['SECRET_IV']);
//nombre de la sesion
define('SESSION_NAME', $_ENV['SESSION_NAME']);
define('SESSION_NAME_POS', $_ENV['SESSION_NAME_POS']);
//Generador de perfiles mediante nombre
define('GENERAR_PERFIL', $_ENV['GENERAR_PERFIL']);
//Variables de la API api.apis.net.pe
define('API_KEY', $_ENV['API_KEY']);
define('API_URL_RENIEC', $_ENV['API_URL_RENIEC']);
define('API_URL_RUC', $_ENV['API_URL_RUC']);
