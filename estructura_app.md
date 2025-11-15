# Estructura y Funcionamiento del Sistema Capy-Ventas

## Tabla de Contenidos
1. [Arquitectura General](#arquitectura-general)
2. [Sistema de Enrutamiento](#sistema-de-enrutamiento)
3. [Estructura de Directorios](#estructura-de-directorios)
4. [Núcleo del Framework (Core)](#núcleo-del-framework-core)
5. [Módulos del Sistema](#módulos-del-sistema)
6. [Sistema de Vistas](#sistema-de-vistas)
7. [Sistema de Assets (JS/CSS)](#sistema-de-assets-jscss)
8. [Sistema de Base de Datos](#sistema-de-base-de-datos)
9. [Sistema de Seguridad](#sistema-de-seguridad)
10. [Helpers y Utilidades](#helpers-y-utilidades)

---

## Arquitectura General

El sistema **Capy-Ventas** está construido sobre un **framework MVC (Modelo-Vista-Controlador) personalizado** desarrollado en PHP. El sistema maneja dos interfaces principales:

- **Admin (Panel Administrativo)**: Accesible mediante la ruta `/im/*`
- **POS (Point of Sale)**: Accesible mediante la ruta `/pos/*`

### Flujo de Ejecución

1. **Punto de Entrada**: `index.php`
   - Recibe la URL desde `$_GET['url']`
   - Parsea la URL para extraer: `folder`, `controller`, `method`, `params`
   - Maneja carpetas especiales (`im` → `Admin`, `pos` → `POS`)
   - Carga el autoloader y el sistema de carga de controladores

2. **Autoloader**: `Libraries/Core/Autoload.php`
   - Registra automáticamente las clases del núcleo del framework

3. **Carga de Controlador**: `Libraries/Core/Load.php`
   - Valida la existencia del archivo del controlador
   - Instancia el controlador
   - Verifica la existencia del método
   - Ejecuta el método con los parámetros correspondientes

---

## Sistema de Enrutamiento

### Formato de URL

```
http://localhost/capy-ventas/{folder}/{controller}/{method}/{params}
```

### Ejemplos de Rutas

- **Admin Dashboard**: `/im/dashboard` → `Controllers/Admin/Dashboard.php::dashboard()`
- **Admin Login**: `/im/login` → `Controllers/Admin/Login.php::login()`
- **Admin Users**: `/im/users` → `Controllers/Admin/Users.php::users()`
- **POS Login**: `/pos/login` → `Controllers/POS/Login.php::login()`

### Carpetas Especiales (Fake Folders)

El sistema utiliza "carpetas falsas" para mantener URLs limpias:

- `im` → Se convierte internamente a `Admin`
- `pos` → Se convierte internamente a `POS`

### Manejo de Errores de Enrutamiento

- **Controlador no encontrado**: Redirige a `/im/errors/controllernotfound`
- **Método no encontrado**: Redirige a `/im/errors/methodnotfound`

---

## Estructura de Directorios

```
capy-ventas/
├── Assets/                    # Recursos estáticos
│   ├── css/
│   │   ├── app/              # CSS específico por módulo
│   │   │   ├── Admin/        # CSS del panel administrativo
│   │   │   └── POS/          # CSS del panel POS
│   │   └── libraries/        # CSS de librerías externas
│   └── js/
│       ├── app/              # JavaScript específico por módulo
│       │   ├── Admin/        # JS del panel administrativo
│       │   └── POS/          # JS del panel POS
│       └── libraries/        # JS de librerías externas
├── Bd/                       # Scripts de base de datos
├── Config/                   # Configuración del sistema
│   └── Config.php           # Constantes y configuración
├── Controllers/              # Controladores (Lógica de negocio)
│   ├── Admin/               # Controladores del panel administrativo
│   ├── POS/                 # Controladores del panel POS
│   ├── Home.php             # Controlador principal
│   └── Loadfile.php         # Controlador para carga de archivos
├── Helpers/                 # Funciones auxiliares
│   └── Helpers.php         # Funciones globales del sistema
├── Libraries/               # Librerías del framework
│   ├── Core/               # Núcleo del framework
│   │   ├── Autoload.php    # Sistema de autoload
│   │   ├── Conexion.php    # Clase de conexión a BD
│   │   ├── Controllers.php # Clase base de controladores
│   │   ├── Load.php        # Cargador de controladores
│   │   ├── Mysql.php       # Clase para operaciones BD
│   │   └── Views.php       # Sistema de vistas
│   ├── fpdf186/            # Librería para generación de PDFs
│   ├── PHPMailer/          # Librería para envío de correos
│   └── phpqrcode/          # Librería para generación de códigos QR
├── Models/                  # Modelos (Acceso a datos)
│   ├── Admin/              # Modelos del panel administrativo
│   ├── POS/                # Modelos del panel POS
│   ├── HomeModel.php       # Modelo principal
│   └── LoadfileModel.php   # Modelo para carga de archivos
├── Storage/                # Almacenamiento de archivos
│   ├── data/               # Archivos de datos
│   └── Profile/            # Imágenes de perfil
├── Views/                  # Vistas (Interfaz de usuario)
│   ├── App/                # Vistas de la aplicación
│   │   ├── Admin/          # Vistas del panel administrativo
│   │   └── POS/            # Vistas del panel POS
│   ├── Template/           # Plantillas reutilizables
│   │   ├── panel/          # Plantillas del panel Admin
│   │   └── panelPos/       # Plantillas del panel POS
│   └── home.php            # Vista principal
└── index.php               # Punto de entrada del sistema
```

---

## Núcleo del Framework (Core)

### 1. Autoload.php
**Función**: Sistema de carga automática de clases del núcleo.

```php
spl_autoload_register(function ($class) {
    $core = "Libraries/Core/" . $class . ".php";
    if (file_exists($core)) {
        require_once($core);
    }
});
```

### 2. Controllers.php
**Función**: Clase base para todos los controladores.

**Características**:
- Carga automática del modelo correspondiente
- Inicializa el sistema de vistas
- Permite especificar la carpeta del modelo (`Admin` o `POS`)

**Métodos principales**:
- `__construct(string $folderModel = "Admin")`: Constructor que inicializa vistas y modelo
- `loadModel()`: Carga automática del modelo basado en el nombre del controlador

### 3. Conexion.php
**Función**: Maneja la conexión a la base de datos usando PDO.

**Características**:
- Configuración desde `Config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD)
- Manejo de errores con registro en logs
- Retorna objeto PDO para operaciones

### 4. Mysql.php
**Función**: Clase para operaciones CRUD en la base de datos.

**Métodos principales**:
- `insert(string $query, array $arrValues)`: Inserta un registro
- `select(string $query, array $arrValues = [])`: Busca un registro
- `select_all(string $query, array $arrValues = [])`: Devuelve todos los registros
- `update(string $query, array $arrValues)`: Actualiza registros
- `delete(string $query, array $arrValues)`: Elimina registros

**Características**:
- Uso de prepared statements para prevenir SQL Injection
- Manejo de errores con registro automático en logs
- Retorna respuestas JSON en caso de error

### 5. Views.php
**Función**: Sistema de renderizado de vistas.

**Método principal**:
```php
getView($controller, $view, $data = "", string $type = "Admin")
```

**Lógica**:
- Determina la ruta de la vista según el tipo (`Admin` o `POS`)
- Carga la vista correspondiente desde `Views/App/{Type}/{Controller}/{view}.php`

### 6. Load.php
**Función**: Carga y ejecuta controladores.

**Proceso**:
1. Valida la existencia del archivo del controlador
2. Requiere el archivo
3. Instancia el controlador
4. Verifica la existencia del método
5. Ejecuta el método con parámetros
6. Maneja errores redirigiendo a páginas de error

---

## Módulos del Sistema

### Módulo: Login (Admin)
**Controlador**: `Controllers/Admin/Login.php`  
**Modelo**: `Models/Admin/LoginModel.php`  
**Vista**: `Views/App/Admin/Login/login.php`  
**JS**: `Assets/js/app/Admin/login/functions_login.js`  
**CSS**: `Assets/css/app/Admin/login/style_login.css`

**Funcionalidades**:
- Inicio de sesión (`isLogIn()`)
- Recuperación de contraseña (`resetPassword()`)
- Actualización de contraseña (`updatePassword()`)
- Validación de intentos de inicio de sesión (máximo 3)
- Bloqueo de cuenta por intentos fallidos
- Generación de tokens para recuperación
- Envío de correos electrónicos para recuperación

**Seguridad**:
- Encriptación de credenciales
- Validación CSRF
- Límite de intentos de inicio de sesión
- Tokens con expiración (30 minutos)

### Módulo: Dashboard (Admin)
**Controlador**: `Controllers/Admin/Dashboard.php`  
**Modelo**: `Models/Admin/DashboardModel.php`  
**Vista**: `Views/App/Admin/Dashboard/dashboard.php`  
**JS**: `Assets/js/app/Admin/dashboard/functions_dashboard.js`  
**CSS**: `Assets/css/app/Admin/dashboard/style_dashboard.css`

**Funcionalidades**:
- Panel de control principal
- Widgets con métricas del sistema
- Información de almacenamiento del usuario
- Contador de usuarios activos
- Contador de roles
- Registro de auditoría de navegación

### Módulo: Users (Admin)
**Controlador**: `Controllers/Admin/Users.php`  
**Modelo**: `Models/Admin/UsersModel.php`  
**Vista**: `Views/App/Admin/Users/users.php`  
**JS**: `Assets/js/app/Admin/users/functions_users.js`  
**CSS**: `Assets/css/app/Admin/users/style_users.css`

**Funcionalidades**:
- Gestión de usuarios (CRUD)
- Asignación de roles
- Gestión de perfiles
- Control de almacenamiento por usuario
- Gestión de estados (Activo/Inactivo)

### Módulo: Roles (Admin)
**Controlador**: `Controllers/Admin/Roles.php`  
**Modelo**: `Models/Admin/RolesModel.php`  
**Vista**: `Views/App/Admin/Roles/roles.php`  
**JS**: `Assets/js/app/Admin/roles/functions_roles.js`  
**CSS**: `Assets/css/app/Admin/roles/style_roles.css`

**Funcionalidades**:
- Gestión de roles
- Asignación de permisos por módulo
- Gestión de interfaces
- Control de acceso basado en roles

### Módulo: System (Admin)
**Controlador**: `Controllers/Admin/System.php`  
**Modelo**: `Models/Admin/SystemModel.php`  
**Vista**: `Views/App/Admin/System/system.php`  
**JS**: `Assets/js/app/Admin/system/functions_system.js`  
**CSS**: `Assets/css/app/Admin/system/style_system.css`

**Funcionalidades**:
- Configuración del sistema
- Gestión de información de la empresa
- Configuración de correo electrónico
- Configuración de colores del sistema
- Configuración de logo
- Configuración de API keys (RENIEC/SUNAT)

### Módulo: Notification (Admin)
**Controlador**: `Controllers/Admin/Notification.php`  
**Modelo**: `Models/Admin/NotificationModel.php`  
**Vista**: `Views/App/Admin/Notification/notification.php`  
**JS**: `Assets/js/app/Admin/notification/functions_notification.js`  
**CSS**: `Assets/css/app/Admin/notification/style_notification.css`

**Funcionalidades**:
- Sistema de notificaciones para usuarios
- Prioridades de notificación
- Tipos de notificación (info, success, warning, error, custom)
- Enlaces en notificaciones
- Marcado de notificaciones como leídas

### Módulo: Logs (Admin)
**Controlador**: `Controllers/Admin/Logs.php`  
**Modelo**: `Models/Admin/LogsModel.php`  
**Vista**: `Views/App/Admin/Logs/logs.php`  
**JS**: `Assets/js/app/Admin/logs/functions_logs.js`  
**CSS**: `Assets/css/app/Admin/logs/style_logs.css`

**Funcionalidades**:
- Registro de eventos del sistema
- Tipos de logs (Error, Info, Warning, etc.)
- Filtrado y búsqueda de logs
- Auditoría de acciones de usuarios

### Módulo: Clust (Admin)
**Controlador**: `Controllers/Admin/Clust.php`  
**Modelo**: `Models/Admin/ClustModel.php`  
**Vista**: `Views/App/Admin/Clust/clust.php`  
**JS**: `Assets/js/app/Admin/clust/functions_clust.js`  
**CSS**: `Assets/css/app/Admin/clust/style_clust.css`

**Funcionalidades**:
- Gestión de almacenamiento
- Visualización de espacio utilizado
- Gestión de archivos

### Módulo: Errors (Admin)
**Controlador**: `Controllers/Admin/Errors.php`  
**Modelo**: `Models/Admin/ErrorsModel.php`  
**Vista**: `Views/App/Admin/Errors/`  
**JS**: `Assets/js/app/Admin/errors/functions_*.js`  
**CSS**: `Assets/css/app/Admin/errors/style_*.css`

**Vistas de Error**:
- `404.php`: Página no encontrada
- `controllers.php`: Controlador no encontrado
- `method.php`: Método no encontrado
- `timeout.php`: Token expirado

### Módulo: Lock (Admin)
**Controlador**: `Controllers/Admin/Lock.php`  
**Vista**: `Views/App/Admin/Lock/lock.php`  
**JS**: `Assets/js/app/Admin/lock/functions_lock.js`  
**CSS**: `Assets/css/app/Admin/lock/style_lock.css`

**Funcionalidades**:
- Bloqueo de pantalla por inactividad
- Desbloqueo con contraseña

### Módulo: Customersuserapp (Admin)
**Controlador**: `Controllers/Admin/Customersuserapp.php`  
**Modelo**: `Models/Admin/CustomersuserappModel.php`  
**Vista**: `Views/App/Admin/Customersuserapp/customersuserapp.php`  
**JS**: `Assets/js/app/Admin/customersuserapp/functions_plans.js`  
**CSS**: `Assets/css/app/Admin/customersuserapp/style_customersuserapp.css`

**Funcionalidades**:
- Gestión de clientes de aplicación de usuario

### Módulo: Apireniecsunat (Admin)
**Controlador**: `Controllers/Admin/Apireniecsunat.php`

**Funcionalidades**:
- Integración con API de RENIEC (consulta de DNI)
- Integración con API de SUNAT (consulta de RUC)
- Validación de datos de personas y empresas

### Módulo: Pdf (Admin)
**Controlador**: `Controllers/Admin/Pdf.php`

**Funcionalidades**:
- Generación de documentos PDF
- Uso de librería FPDF

### Módulo: Business Type (Admin)
**Controlador**: `Controllers/Admin/business_type.php`  
**Modelo**: `Models/Admin/business_type.php`

**Funcionalidades**:
- Gestión de tipos de negocio

### Módulo: POS (Point of Sale)
**Controlador**: `Controllers/POS/Login.php`  
**Modelo**: `Models/POS/LoginModel.php`  
**Vista**: `Views/App/POS/Login/login.php`

**Funcionalidades**:
- Sistema de punto de venta
- Login específico para POS

---

## Sistema de Vistas

### Estructura de Vistas

Cada módulo tiene su propia carpeta dentro de `Views/App/{Type}/{Controller}/`:

```
Views/App/Admin/{Controller}/
├── {view}.php              # Vista principal
└── Libraries/
    ├── head.php            # Head específico del módulo
    └── foot.php            # Footer específico del módulo
```

### Plantillas del Sistema

#### Panel Admin (`Views/Template/panel/`)
- `head.php`: Encabezado HTML con meta tags, CSS, scripts
- `foot.php`: Pie de página con scripts JavaScript
- `navbar.php`: Barra de navegación superior
- `sidebarmenu.php`: Menú lateral (sidebar)

#### Panel POS (`Views/Template/panelPos/`)
- `head.php`: Encabezado para POS
- `foot.php`: Pie de página para POS
- `navbar.php`: Barra de navegación POS
- `sidebarmenu.php`: Menú lateral POS

### Variables Disponibles en Vistas

Las vistas reciben un array `$data` con la siguiente estructura:

```php
$data = [
    'page_id'          => int,        // ID de la página
    'page_title'       => string,     // Título de la página
    'page_description' => string,     // Descripción
    'page_container'   => string,     // Contenedor (nombre del módulo)
    'page_view'        => string,     // Nombre de la vista
    'page_js_css'      => string,     // Nombre para JS/CSS
    'page_vars'        => array,      // Variables de sesión a mantener
    'page_widget'      => array,      // Widgets (opcional)
    'page_info_user'   => array,      // Info de usuario (opcional)
];
```

### Funciones Helper en Vistas

- `base_url()`: URL base del proyecto
- `media()`: Ruta a Assets
- `getSystemInfo()`: Información del sistema
- `getCompanyName()`: Nombre de la compañía
- `versionSystem()`: Versión del sistema
- `getCurrency()`: Símbolo de moneda
- `loadOptions()`: Carga opciones del menú según permisos

---

## Sistema de Assets (JS/CSS)

### Estructura de CSS

```
Assets/css/
├── app/
│   ├── Admin/
│   │   ├── {module}/
│   │   │   └── style_{page_js_css}.css
│   │   └── ...
│   └── POS/
└── libraries/
    └── Admin/          # Librerías externas (Bootstrap, DataTables, etc.)
```

### Estructura de JavaScript

```
Assets/js/
├── app/
│   ├── Admin/
│   │   ├── {module}/
│   │   │   └── functions_{page_js_css}.js
│   │   └── ...
│   └── POS/
└── libraries/
    └── Admin/          # Librerías externas (jQuery, Bootstrap, etc.)
```

### Carga Automática de Assets

El sistema carga automáticamente los archivos CSS y JS según:
- `page_container`: Determina la carpeta
- `page_js_css`: Determina el nombre del archivo

**Ejemplo**:
- `page_container = "Dashboard"` → `Assets/css/app/Admin/dashboard/`
- `page_js_css = "dashboard"` → `style_dashboard.css` y `functions_dashboard.js`

### Librerías Principales

**CSS**:
- Bootstrap (via main.css)
- Font Awesome 4.7.0
- DataTables
- Toastr (notificaciones)
- Select2

**JavaScript**:
- jQuery 3.7.1
- Bootstrap
- DataTables
- Chart.js
- Toastr
- Select2
- Moment.js
- FullCalendar

---

## Sistema de Base de Datos

### Configuración

Definida en `Config/Config.php`:

```php
const DB_HOST = "localhost";
const DB_NAME = "bd_capyventas";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8";
```

### Clase Mysql

Extiende de `Conexion` y proporciona métodos para:
- **Insert**: `insert(string $query, array $arrValues)`
- **Select**: `select(string $query, array $arrValues = [])`
- **Select All**: `select_all(string $query, array $arrValues = [])`
- **Update**: `update(string $query, array $arrValues)`
- **Delete**: `delete(string $query, array $arrValues)`

### Características de Seguridad

- Uso de **Prepared Statements** (PDO)
- Prevención de SQL Injection
- Manejo de errores con registro en logs
- Respuestas JSON en caso de error

### Modelos

Todos los modelos extienden de `Mysql`:

```php
class UsersModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function select_users(): array
    {
        $query = "SELECT * FROM tb_user";
        return $this->select_all($query);
    }
}
```

---

## Sistema de Seguridad

### 1. Autenticación

- **Sesiones**: Uso de sesiones PHP con nombre personalizado
- **Cookies**: Almacenamiento de información de login (30 días)
- **Validación de sesión**: Función `isSession()` verifica login activo
- **Bloqueo por intentos**: Máximo 3 intentos fallidos

### 2. Encriptación

- **Método**: AES-256-CBC
- **Funciones**:
  - `encryption($string)`: Encripta datos
  - `decryption($string)`: Desencripta datos
- **Uso**: Contraseñas, tokens, datos sensibles

### 3. Protección CSRF

- **Token CSRF**: Generado por función `csrf()`
- **Validación**: Función `isCsrf($token)`
- **Expiración**: 10 minutos
- **Almacenamiento**: En sesión `$_SESSION['data_token']`

### 4. Sanitización de Datos

- **Función**: `strClean($strCadena)`
- **Características**:
  - Elimina etiquetas HTML/PHP
  - Previene XSS
  - Previene SQL Injection
  - Normaliza espacios

### 5. Validación de Campos

- **Función**: `validateFields(array $fields, string $method = "POST")`
- **Función**: `validateFieldsEmpty(array $fields)`
- Verifica existencia y no vacío de campos

### 6. Control de Acceso

- **Permisos por rol**: Sistema de roles e interfaces
- **Función**: `permissionInterface(int $idInterface)`
- **Función**: `loadOptions(int $id_user, $data)`
- Menú dinámico según permisos del usuario

### 7. Logs y Auditoría

- **Función**: `registerLog($title, $description, $typeLog, $idUser = 0)`
- **Tipos de log**:
  - 1: Error
  - 2: Info/Success
  - 3: Warning
- Registra todas las acciones importantes del sistema

---

## Helpers y Utilidades

### Funciones de Sistema

- `base_url()`: URL base del proyecto
- `media()`: Ruta a Assets
- `versionSystem()`: Versión del sistema
- `getSystemName()`: Nombre del sistema
- `getCurrency()`: Símbolo de moneda (S/.)
- `getRoute()`: Ruta de almacenamiento

### Funciones de Formato

- `formatMoney($cantidad)`: Formatea valores monetarios
- `dateFormat($date)`: Formatea fechas
- `limitarCaracteres($texto, $limite, $sufijo = '...')`: Limita longitud de texto
- `normalizarTexto($texto, $regex, $maxLength, $trim)`: Normaliza texto según regex

### Funciones de Fecha

- `dateDifference($fechaInicio, $fechaFin)`: Calcula diferencia entre fechas
- `calculateDifferenceDates($fechaInicio, $fechaFin, $incluirHoras)`: Diferencia formateada
- `calculateDifferenceDatesActual($fecha)`: Diferencia desde fecha actual ("Hace X minutos")

### Funciones de Archivos

- `isFile(string $expectedType, array $file, array $extensions)`: Valida tipo de archivo
- `verifyFolder(string $ruta, int $permissions, bool $recursive)`: Verifica/crea carpetas
- `delFolder(string $carpeta, string $val, bool $deleteFolder)`: Elimina carpetas
- `resizeAndCompressImage($sourcePath, $destinationPath, $maxSizeMB, $newWidth, $newHeight)`: Redimensiona imágenes

### Funciones de Generación

- `passGenerator($length = 10)`: Genera contraseñas aleatorias
- `token()`: Genera tokens únicos
- `generateAvatar(string $nombre)`: Genera avatar desde nombre
- `generarQR(string $data, string $filename, string $path, float $size, float $margin)`: Genera códigos QR

### Funciones de Correo

- `sendEmail(array $config, array $params)`: Envía correos electrónicos
- `renderTemplate(string $path, array $variables)`: Renderiza plantillas PHP
- `getRemitente()`, `getFrom()`, `getHost()`, etc.: Obtienen configuración de correo

### Funciones de Sesión

- `isSession()`: Valida sesión activa
- `existLogin()`: Verifica si existe login (para redirigir desde login)
- `deleteSessionVariable(array $data)`: Elimina variables de sesión no necesarias
- `config_sesion()`: Configuración de sesión

### Funciones de Notificaciones

- `setNotification(int $iduser, string $title, string $description, int $priority, string $type, string $color, string $icon, string $link)`: Crea notificaciones

### Funciones de API

- `getApiKeyReniec()`: Obtiene API key de RENIEC
- `getApiUrlReniec()`: URL de API RENIEC
- `getApiUrlSunat()`: URL de API SUNAT

### Funciones de Utilidad

- `dep($data)`: Muestra datos formateados (debug)
- `toJson($data)`: Convierte a JSON y termina ejecución
- `obtenerIP()`: Obtiene IP del cliente
- `activeItem($idPage, $idPageValue)`: Retorna clase "active" si coincide
- `getLoader(int $num)`: Retorna HTML de loader según número
- `getAllUsersOnline()`: Obtiene usuarios en línea
- `hexToRGB($hex)`: Convierte color hexadecimal a RGB

---

## Configuración del Sistema

### Archivo: `Config/Config.php`

**Constantes Principales**:

```php
// URLs y Rutas
const BASE_URL = "http://localhost/capy-ventas";
const RUTA_ARCHIVOS = "./Storage/";

// Sistema
const NOMBRE_SISTEMA = "Sistema de Roles";
const NOMBRE_COMPANIA = "CYD TECH";
const VERSION_SISTEMA = "0.0.0.1";

// Base de Datos
const DB_HOST = "localhost";
const DB_NAME = "bd_capyventas";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "utf8";

// Moneda
const SPD = ".";  // Separador decimal
const SPM = ",";  // Separador de millares
const SMONEY = "S/.";  // Símbolo de moneda

// Correo
const MAIL_HOST = "mail.shaday-pe.com";
const MAIL_PORT = 465;
const MAIL_USER = "pureba@shaday-pe.com";
const MAIL_PASSWORD = "X5XFy46Qp?g_";
const MAIL_ENCRYPTION = "ssl";
const MAIL_FROM = "info@shaday-pe.com";
const MAIL_REMITENTE = "Sistema de Roles";

// Encriptación
const METHOD = "AES-256-CBC";
const SECRET_KEY = "SystemOfPredios2025";
const SECRET_IV = "@2025BajoNaranjillo";

// Sesión
const SESSION_NAME = "Sistema-de-capy-ventas-2025-08-11";

// APIs
const API_KEY = "apis-token-13092.cwy578uEtUFPCWYnJN5uI83i6WuTRvVM";
const API_URL_RENIEC = "https://api.apis.net.pe/v2/reniec/dni?numero=";
const API_URL_RUC = "https://api.apis.net.pe/v2/sunat/ruc?numero=";
```

---

## Flujo de una Petición Completa

### Ejemplo: Acceso a Dashboard

1. **Usuario accede**: `http://localhost/capy-ventas/im/dashboard`

2. **index.php**:
   - Parsea URL: `folder=im`, `controller=dashboard`, `method=dashboard`
   - Convierte `im` → `Admin`
   - Carga `Autoload.php` y `Load.php`

3. **Load.php**:
   - Busca `Controllers/Admin/Dashboard.php`
   - Instancia `new Dashboard()`
   - Ejecuta `dashboard()`

4. **Dashboard::dashboard()**:
   - Valida sesión con `isSession()`
   - Carga modelo `DashboardModel`
   - Obtiene datos del modelo
   - Prepara array `$data`
   - Llama a `$this->views->getView($this, "dashboard", $data)`

5. **Views::getView()**:
   - Determina ruta: `Views/App/Admin/Dashboard/dashboard.php`
   - Requiere la vista

6. **Vista dashboard.php**:
   - Incluye `head.php` (carga CSS, JS)
   - Incluye `navbar.php`
   - Incluye `sidebarmenu.php`
   - Renderiza contenido HTML
   - Incluye `foot.php` (carga scripts finales)

7. **Respuesta HTML**:
   - Se envía al navegador
   - JavaScript se ejecuta
   - Se realizan peticiones AJAX si es necesario

---

## Convenciones de Nomenclatura

### Controladores
- Nombre en PascalCase
- Extienden de `Controllers`
- Métodos en camelCase
- Ejemplo: `Users.php` con método `insertUser()`

### Modelos
- Nombre: `{Controller}Model`
- Extienden de `Mysql`
- Métodos descriptivos: `select_users()`, `insert_user()`, `update_user()`
- Ejemplo: `UsersModel.php`

### Vistas
- Nombre en minúsculas
- Ubicación: `Views/App/{Type}/{Controller}/{view}.php`
- Ejemplo: `Views/App/Admin/Users/users.php`

### JavaScript
- Nombre: `functions_{page_js_css}.js`
- Ubicación: `Assets/js/app/{Type}/{module}/functions_{name}.js`
- Ejemplo: `Assets/js/app/Admin/users/functions_users.js`

### CSS
- Nombre: `style_{page_js_css}.css`
- Ubicación: `Assets/css/app/{Type}/{module}/style_{name}.css`
- Ejemplo: `Assets/css/app/Admin/users/style_users.css`

---

## Notas Importantes

1. **Sesiones**: El sistema utiliza sesiones PHP. Siempre iniciar sesión antes de usar funciones que dependan de `$_SESSION`.

2. **Encriptación**: Todos los datos sensibles (contraseñas, tokens) deben encriptarse antes de guardarse en BD.

3. **Validación**: Siempre validar campos con `validateFields()` y `validateFieldsEmpty()` antes de procesar.

4. **CSRF**: Todos los formularios deben incluir token CSRF con `csrf()`.

5. **Logs**: Registrar acciones importantes con `registerLog()`.

6. **Respuestas JSON**: Usar `toJson($data)` para respuestas AJAX.

7. **Permisos**: Verificar permisos con `permissionInterface()` antes de mostrar contenido.

8. **Versionamiento**: Los assets incluyen versión del sistema para evitar caché: `?v={VERSION_SISTEMA}`

---

## Conclusión

Este documento describe la estructura completa del sistema **Capy-Ventas**, un framework MVC personalizado en PHP que maneja un panel administrativo y un sistema POS. El sistema está diseñado con énfasis en seguridad, modularidad y mantenibilidad.

Para más información sobre módulos específicos, consultar los archivos de código fuente correspondientes.

---

**Última actualización**: 2025-01-XX  
**Versión del sistema**: 0.0.0.1
