<?php

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
//const BASE_URL = "http://localhost/sis-roles";
//Ruta de almacenamiento de archivos
const RUTA_ARCHIVOS = "./Storage/";
//Nombre del sistema
const NOMBRE_SISTEMA = "Sistema de Roles";
//Nombre de la compania
const NOMBRE_COMPANIA = "CYD TECH";
//Version sistema
const VERSION_SISTEMA = "0.0.0.1";
//Zona horaria
date_default_timezone_set('America/Lima');

//Datos de conexión a Base de Datos
const DB_HOST = "localhost";
const DB_NAME = "bd_capyventas";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8";

//Deliminadores decimal y millar Ej. 24,1989.00
const SPD = ".";
const SPM = ",";

//Simbolo de moneda
const SMONEY = "S/.";

//Datos envio de correo
const MAIL_HOST = "mail.shaday-pe.com";
const MAIL_PORT = 465;
const MAIL_USER = "pureba@shaday-pe.com";
const MAIL_PASSWORD = "X5XFy46Qp?g_";
const MAIL_ENCRYPTION = "ssl";
const MAIL_FROM = "info@shaday-pe.com"; //nombre del remitente
const MAIL_REMITENTE = "Sistema de Roles";
//Variables de encriptacion
const METHOD = "AES-256-CBC";
const SECRET_KEY = "SystemOfPredios2025";
const SECRET_IV = "@2025BajoNaranjillo";
//nombre de la sesion
const SESSION_NAME = "Sistemade-gestion-de-Roles";
//Generador de perfiles mediante nombre
const GENERAR_PERFIL = "https://ui-avatars.com/api/?name=";
//Variables de la API api.apis.net.pe
const API_KEY = "apis-token-13092.cwy578uEtUFPCWYnJN5uI83i6WuTRvVM";
const API_URL_RENIEC = "https://api.apis.net.pe/v2/reniec/dni?numero=";
const API_URL_RUC = "https://api.apis.net.pe/v2/sunat/ruc?numero=";
