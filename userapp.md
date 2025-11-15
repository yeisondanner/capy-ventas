# Documentación Completa del Módulo Customersuserapp

## Índice
1. [Descripción General](#descripción-general)
2. [Estructura de Archivos](#estructura-de-archivos)
3. [Estructura de Base de Datos](#estructura-de-base-de-datos)
4. [Modelo (CustomersuserappModel.php)](#modelo-customersuserappmodelphp)
5. [Controlador (Customersuserapp.php)](#controlador-customersuserappphp)
6. [Vista (customersuserapp.php)](#vista-customersuserappphp)
7. [JavaScript (functions_plans.js)](#javascript-functions_customersuserappjs)
8. [Estilos CSS (style_customersuserapp.css)](#estilos-css-style_customersuserappcss)
9. [Flujos de Trabajo](#flujos-de-trabajo)
10. [Validaciones y Seguridad](#validaciones-y-seguridad)
11. [Integración con el Sistema](#integración-con-el-sistema)

---

## Descripción General

El módulo **Customersuserapp** gestiona clientes (personas) que tendrán acceso a la aplicación móvil. Permite registrar información personal y opcionalmente crear credenciales de acceso (usuario y contraseña) asociadas a cada persona.

### Características Principales

- ✅ **Gestión completa de personas**: CRUD completo para la tabla `people`
- ✅ **Usuarios opcionales**: Cada persona puede tener un usuario de aplicación asociado
- ✅ **Seguridad**: Usuario y contraseña almacenados encriptados
- ✅ **Validaciones robustas**: Frontend y backend
- ✅ **Interfaz moderna**: DataTables con exportación, modales Bootstrap
- ✅ **Logging**: Registro de todas las operaciones
- ✅ **Permisos**: Control de acceso mediante `permissionInterface`

### Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (MVC personalizado)
- **Base de Datos**: MySQL (InnoDB)
- **Frontend**: HTML5, Bootstrap 4, jQuery, DataTables
- **JavaScript**: ES6+ (Fetch API, async/await)
- **Notificaciones**: Toastr
- **Iconos**: Font Awesome 4.7.0

---

## Estructura de Archivos

```
capy-ventas/
├── Controllers/
│   └── Admin/
│       └── Customersuserapp.php          # Controlador principal
├── Models/
│   └── Admin/
│       └── CustomersuserappModel.php      # Modelo de datos
├── Views/
│   └── App/
│       └── Admin/
│           └── Customersuserapp/
│               └── customersuserapp.php   # Vista principal
├── Assets/
│   ├── js/
│   │   └── app/
│   │       └── Admin/
│   │           └── customersuserapp/
│   │               └── functions_plans.js  # Lógica JavaScript
│   └── css/
│       └── app/
│           └── Admin/
│               └── customersuserapp/
│                   └── style_customersuserapp.css   # Estilos personalizados
```

---

## Estructura de Base de Datos

### Tabla: `people`

```sql
CREATE TABLE IF NOT EXISTS `people` (
  `idPeople` int(11) NOT NULL AUTO_INCREMENT,
  `names` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `date_of_birth` date NOT NULL,
  `country` varchar(50) NOT NULL,
  `telephone_prefix` char(7) NOT NULL,
  `phone_number` char(11) NOT NULL,
  `status` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idPeople`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Campos:**
- `idPeople`: ID único (AUTO_INCREMENT)
- `names`: Nombres (almacenados en mayúsculas)
- `lastname`: Apellidos (almacenados en mayúsculas)
- `email`: Correo electrónico (único)
- `date_of_birth`: Fecha de nacimiento (formato: YYYY-MM-DD)
- `country`: País (almacenado en mayúsculas)
- `telephone_prefix`: Prefijo telefónico (ej: +51)
- `phone_number`: Número de teléfono (solo números, único)
- `status`: Estado (Activo/Inactivo, default: Activo)
- `registration_date`: Fecha de registro (automático)
- `update_date`: Fecha de actualización (automático)

### Tabla: `user_app`

```sql
CREATE TABLE IF NOT EXISTS `user_app` (
  `idUserApp` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `password` text DEFAULT NULL,
  `status` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `people_id` int(11) NOT NULL,
  PRIMARY KEY (`idUserApp`),
  KEY `people_id` (`people_id`),
  CONSTRAINT `user_app_ibfk_1` FOREIGN KEY (`people_id`) REFERENCES `people` (`idPeople`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Campos:**
- `idUserApp`: ID único (AUTO_INCREMENT)
- `user`: Nombre de usuario (almacenado encriptado)
- `password`: Contraseña (almacenada encriptada)
- `status`: Estado (Activo/Inactivo, default: Activo)
- `registration_date`: Fecha de registro (automático)
- `update_date`: Fecha de actualización (automático)
- `people_id`: FK a `people.idPeople` (relación 1:1)

**Relación:**
- Una persona puede tener **máximo un usuario** de aplicación
- Al eliminar una persona, se debe eliminar manualmente su usuario asociado

---

## Modelo (CustomersuserappModel.php)

Extiende de `Mysql` (clase base para operaciones CRUD con PDO).

### Propiedades Privadas

```php
private $idPeople;
private $names;
private $lastname;
private $email;
private $dateOfBirth;
private $country;
private $telephonePrefix;
private $phoneNumber;
private $status;
```

### Métodos para Tabla `people`

#### 1. `select_people(): array`

Obtiene todos los registros de personas ordenados por fecha de registro descendente.

**Retorna:** Array de todas las personas

**SQL:**
```sql
SELECT * FROM people ORDER BY registration_date DESC;
```

---

#### 2. `select_people_by_id(int $idPeople)`

Obtiene una persona por su ID.

**Parámetros:**
- `$idPeople` (int): ID de la persona

**Retorna:** `array|false` - Datos de la persona o `false` si no existe

**SQL:**
```sql
SELECT * FROM people WHERE idPeople = ?
```

---

#### 3. `select_people_by_email(string $email)`

Busca una persona por email (para validar duplicados).

**Parámetros:**
- `$email` (string): Email a buscar

**Retorna:** `array|false` - Datos de la persona o `false` si no existe

---

#### 4. `select_people_by_phone(string $phoneNumber)`

Busca una persona por número de teléfono (para validar duplicados).

**Parámetros:**
- `$phoneNumber` (string): Número de teléfono a buscar

**Retorna:** `array|false` - Datos de la persona o `false` si no existe

---

#### 5. `insert_people($names, $lastname, $email, $dateOfBirth, $country, $telephonePrefix, $phoneNumber)`

Inserta una nueva persona. **Nota:** No incluye `status` (usa el default de la BD: 'Activo').

**Parámetros:**
- `$names` (string): Nombres
- `$lastname` (string): Apellidos
- `$email` (string): Email
- `$dateOfBirth` (string): Fecha de nacimiento (YYYY-MM-DD)
- `$country` (string): País
- `$telephonePrefix` (string): Prefijo telefónico
- `$phoneNumber` (string): Número de teléfono

**Retorna:** `int|false` - ID del registro insertado o `false` en caso de error

**SQL:**
```sql
INSERT INTO `people` (`names`, `lastname`, `email`, `date_of_birth`, `country`, `telephone_prefix`, `phone_number`) 
VALUES (?,?,?,?,?,?,?);
```

---

#### 6. `update_people($idPeople, $names, $lastname, $email, $dateOfBirth, $country, $telephonePrefix, $phoneNumber, $status)`

Actualiza una persona existente.

**Parámetros:**
- `$idPeople` (int): ID de la persona
- `$names` (string): Nombres
- `$lastname` (string): Apellidos
- `$email` (string): Email
- `$dateOfBirth` (string): Fecha de nacimiento
- `$country` (string): País
- `$telephonePrefix` (string): Prefijo telefónico
- `$phoneNumber` (string): Número de teléfono
- `$status` (string): Estado ('Activo' o 'Inactivo')

**Retorna:** `bool` - `true` si se actualizó correctamente

**SQL:**
```sql
UPDATE `people` SET `names`=?, `lastname`=?, `email`=?, `date_of_birth`=?, 
`country`=?, `telephone_prefix`=?, `phone_number`=?, `status`=? 
WHERE idPeople=?
```

---

#### 7. `delete_people($idPeople)`

Elimina una persona.

**Parámetros:**
- `$idPeople` (int): ID de la persona

**Retorna:** `bool` - `true` si se eliminó correctamente

**SQL:**
```sql
DELETE FROM `people` WHERE idPeople = ?
```

---

### Métodos para Tabla `user_app`

#### 8. `select_user_app_by_people_id(int $peopleId)`

Obtiene el usuario de aplicación asociado a una persona.

**Parámetros:**
- `$peopleId` (int): ID de la persona

**Retorna:** `array|false` - Datos del usuario o `false` si no existe

**SQL:**
```sql
SELECT * FROM user_app WHERE people_id = ?
```

---

#### 9. `select_user_app_by_user(string $user)`

Busca un usuario por nombre de usuario (para validar duplicados).

**Parámetros:**
- `$user` (string): Nombre de usuario **encriptado**

**Retorna:** `array|false` - Datos del usuario o `false` si no existe

**SQL:**
```sql
SELECT * FROM user_app WHERE user = ?
```

**⚠️ Importante:** El parámetro `$user` debe estar encriptado antes de llamar a este método.

---

#### 10. `insert_user_app($user, $password, $peopleId)`

Inserta un nuevo usuario de aplicación. **Nota:** No incluye `status` (usa el default de la BD: 'Activo').

**Parámetros:**
- `$user` (string): Nombre de usuario **encriptado**
- `$password` (string): Contraseña **encriptada**
- `$peopleId` (int): ID de la persona asociada

**Retorna:** `int|false` - ID del registro insertado o `false` en caso de error

**SQL:**
```sql
INSERT INTO `user_app` (`user`, `password`, `people_id`) VALUES (?,?,?);
```

---

#### 11. `update_user_app($idUserApp, $user, $password, $status)`

Actualiza un usuario de aplicación.

**Parámetros:**
- `$idUserApp` (int): ID del usuario
- `$user` (string): Nombre de usuario **encriptado**
- `$password` (string|null): Contraseña **encriptada** o `null` para mantener la actual
- `$status` (string): Estado ('Activo' o 'Inactivo')

**Retorna:** `bool` - `true` si se actualizó correctamente

**Comportamiento:**
- Si `$password` es `null` o vacío: No actualiza la contraseña
- Si `$password` tiene valor: Actualiza la contraseña

**SQL (con contraseña):**
```sql
UPDATE `user_app` SET `user`=?, `password`=?, `status`=? WHERE idUserApp=?
```

**SQL (sin contraseña):**
```sql
UPDATE `user_app` SET `user`=?, `status`=? WHERE idUserApp=?
```

---

#### 12. `delete_user_app($idUserApp)`

Elimina un usuario de aplicación.

**Parámetros:**
- `$idUserApp` (int): ID del usuario

**Retorna:** `bool` - `true` si se eliminó correctamente

**SQL:**
```sql
DELETE FROM `user_app` WHERE idUserApp = ?
```

---

#### 13. `select_people_with_users(): array`

Obtiene todas las personas con sus usuarios asociados mediante LEFT JOIN.

**Retorna:** Array de personas con información de usuario (si existe)

**SQL:**
```sql
SELECT
    p.*,
    ua.idUserApp,
    ua.user,
    ua.status as user_status
FROM
    people AS p
LEFT JOIN
    user_app AS ua ON ua.people_id = p.idPeople
ORDER BY
    p.registration_date DESC;
```

**Estructura del resultado:**
```php
[
    [
        'idPeople' => 1,
        'names' => 'JUAN',
        'lastname' => 'PÉREZ',
        'email' => 'juan@example.com',
        // ... otros campos de people
        'idUserApp' => 1,        // null si no tiene usuario
        'user' => 'usuario_encriptado',   // null si no tiene usuario
        'user_status' => 'Activo' // null si no tiene usuario
    ],
    // ...
]
```

---

## Controlador (Customersuserapp.php)

Extiende de `Controllers` (clase base del framework MVC).

### Constructor

```php
public function __construct()
{
    isSession();  // Valida que exista sesión activa
    parent::__construct();  // Carga el modelo automáticamente
}
```

### Métodos Públicos

#### 1. `customersuserapp()`

Muestra la vista principal del módulo.

**Funcionalidad:**
- Valida permisos (`permissionInterface(15)`)
- Registra log de navegación
- Renderiza la vista con datos de configuración

**Datos pasados a la vista:**
```php
$data = [
    'page_id'          => 15,
    'page_title'       => 'Clientes App',
    'page_description' => 'Gestiona los clientes para el acceso a la App.',
    'page_container'   => 'Customersuserapp',
    'page_view'        => 'customersuserapp',
    'page_js_css'      => 'customersuserapp',
    'page_vars'        => ['permission_data', 'login', 'login_info'],
];
```

**Log de navegación:**
Registra evento con información del usuario, IP, método HTTP, URL, user agent y timestamp.

---

#### 2. `getPeople()`

Obtiene la lista de personas para DataTables (AJAX).

**Permisos:** `permissionInterface(15)`

**Proceso:**
1. Consulta personas con usuarios: `select_people_with_users()`
2. Formatea datos para cada registro:
   - Contador secuencial
   - Nombre completo
   - Estado con badge HTML
   - Fecha de nacimiento formateada (d/m/Y)
   - Teléfono completo (prefijo + número)
   - Fechas formateadas con `dateFormat()`
   - Información del usuario (desencriptado)
3. Genera botones de acción con atributos `data-*`
4. Retorna JSON

**Estructura de respuesta JSON:**
```json
[
    {
        "cont": 1,
        "idPeople": 1,
        "names": "JUAN",
        "lastname": "PÉREZ",
        "email": "juan@example.com",
        "date_of_birth": "1990-01-15",
        "date_of_birth_formatted": "15/01/1990",
        "country": "PERÚ",
        "telephone_prefix": "+51",
        "phone_number": "987654321",
        "phone_full": "+51 987654321",
        "status": "Activo",
        "status": "<span class='badge badge-success'><i class='fa fa-check'></i> Activo</span>",
        "registration_date_formatted": "15/01/2025 10:30:00",
        "update_date_formatted": "15/01/2025 10:30:00",
        "user_app": "juan_perez",
        "has_user": true,
        "actions": "<div class='btn-group'>...</div>"
    }
]
```

**Atributos en botones:**
- `update-item`: `data-id`, `data-names`, `data-lastname`, `data-email`, `data-date-of-birth`, `data-country`, `data-telephone-prefix`, `data-phone-number`, `data-status`, `data-registration-date`, `data-update-date`, `data-user-app-id`, `data-user`, `data-user-password`, `data-user-status`, `data-has-user`
- `report-item`: Mismos atributos (sin `data-user-app-id`)
- `delete-item`: `data-id`, `data-fullname`, `data-user-app-id`

---

#### 3. `setPeople()`

Registra una nueva persona (POST).

**Permisos:** `permissionInterface(15)`

**Validaciones:**
1. ✅ Método POST
2. ✅ Token CSRF
3. ✅ Campos requeridos: `txtNames`, `txtLastname`, `txtEmail`, `txtDateOfBirth`, `txtCountry`, `txtTelephonePrefix`, `txtPhoneNumber`
4. ✅ Campos no vacíos
5. ✅ Formato de nombres (solo letras y espacios)
6. ✅ Formato de apellidos (solo letras y espacios)
7. ✅ Formato de email (regex)
8. ✅ Formato de fecha válida (`checkdate()`)
9. ✅ Teléfono solo números
10. ✅ Email único
11. ✅ Teléfono único
12. ✅ Si hay usuario:
    - Formato: 3-15 caracteres alfanuméricos, guiones bajos o guiones
    - Usuario único (encriptado antes de buscar)
    - Contraseña obligatoria
    - Contraseña mínimo 8 caracteres

**Campos opcionales:**
- `txtUser`: Nombre de usuario
- `txtPassword`: Contraseña

**Proceso:**
1. Limpia y valida datos
2. Convierte nombres, apellidos y país a mayúsculas
3. Inserta persona: `insert_people()` (sin status, usa default)
4. Si se proporcionó usuario y contraseña:
   - Encripta usuario y contraseña
   - Inserta usuario: `insert_user_app()` (sin status, usa default)
5. Registra log
6. Retorna JSON

**Respuesta exitosa:**
```json
{
    "title": "Registro exitoso",
    "message": "La persona fue registrada satisfactoriamente en el sistema.",
    "type": "success",
    "status": true
}
```

**Respuesta parcial (persona OK, usuario falló):**
```json
{
    "title": "Registro parcial",
    "message": "La persona fue registrada correctamente, pero no se pudo crear el usuario de la aplicación. Puede crearlo posteriormente.",
    "type": "warning",
    "status": true
}
```

---

#### 4. `updatePeople()`

Actualiza una persona existente (POST).

**Permisos:** `permissionInterface(15)`

**Validaciones:**
1. ✅ Método POST
2. ✅ Token CSRF
3. ✅ Campos requeridos: `update_txtId`, `update_txtNames`, `update_txtLastname`, `update_txtEmail`, `update_txtDateOfBirth`, `update_txtCountry`, `update_txtTelephonePrefix`, `update_txtPhoneNumber`, `update_slctStatus`
4. ✅ Campos no vacíos
5. ✅ ID numérico
6. ✅ Formatos (nombres, apellidos, email, fecha)
7. ✅ Persona existe
8. ✅ Email único (excepto el actual)
9. ✅ Teléfono único (excepto el actual)
10. ✅ Si hay usuario:
    - Formato válido
    - Usuario único (excepto el actual, encriptado antes de buscar)
    - Si hay contraseña: mínimo 8 caracteres

**Campos opcionales:**
- `update_txtUserAppId`: ID del usuario existente
- `update_txtUser`: Nombre de usuario
- `update_txtPassword`: Contraseña (vacío = mantener actual)
- `update_slctUserStatus`: Estado del usuario

**Proceso:**
1. Valida datos
2. Convierte a mayúsculas
3. Actualiza persona: `update_people()`
4. Si se proporcionó usuario:
   - Si existe `update_txtUserAppId`: Actualiza usuario existente
   - Si no existe: Crea nuevo usuario
   - Si no hay contraseña: Mantiene actual o genera aleatoria
5. Registra log
6. Retorna JSON

**Lógica de usuario:**
```php
if (!empty($strUser)) {
    $strUserEncrypted = encryption($strUser);
    
    if (!empty($intUserAppId) && is_numeric($intUserAppId)) {
        // Actualizar usuario existente
        $strPasswordEncrypted = !empty($strPassword) 
            ? encryption($strPassword) 
            : null;  // Mantener actual
        $this->model->update_user_app($intUserAppId, $strUserEncrypted, $strPasswordEncrypted, $slctUserStatus);
    } else {
        // Crear nuevo usuario
        $strPasswordEncrypted = !empty($strPassword) 
            ? encryption($strPassword) 
            : encryption(passGenerator(10));  // Generar aleatoria
        $this->model->insert_user_app($strUserEncrypted, $strPasswordEncrypted, $slctUserStatus, $intId);
    }
}
```

---

#### 5. `deletePeople()`

Elimina una persona (DELETE).

**Permisos:** `permissionInterface(15)`

**Validaciones:**
1. ✅ Método DELETE
2. ✅ Token CSRF (desde JSON)
3. ✅ ID no vacío
4. ✅ ID numérico
5. ✅ Persona existe

**Parámetros recibidos (JSON):**
```json
{
    "id": 1,
    "fullname": "Juan Pérez",
    "token": "csrf_token",
    "user_app_id": 1
}
```

**Proceso:**
1. Valida datos
2. Si existe `user_app_id`: Elimina usuario primero
3. Elimina persona: `delete_people()`
4. Registra log
5. Retorna JSON

**Respuesta exitosa:**
```json
{
    "title": "Eliminación exitosa",
    "message": "La persona con ID '1' y nombre 'Juan Pérez' ha sido eliminada correctamente del sistema.",
    "type": "success",
    "status": true
}
```

---

## Vista (customersuserapp.php)

### Estructura HTML

#### 1. Header y Breadcrumb

```php
<?= headerAdmin($data) ?>
<main class="app-content">
    <div class="app-title pt-5">
        <div>
            <h1 class="text-primary"><i class="fa fa-users"></i> <?= $data["page_title"] ?></h1>
            <p><?= $data["page_description"] ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-users fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/im/<?= $data['page_view'] ?>"><?= $data["page_title"] ?></a></li>
        </ul>
    </div>
```

#### 2. Botón "Nuevo"

```html
<div class="tile">
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalSave">
        <i class="fa fa-plus"></i> Nuevo
    </button>
</div>
```

#### 3. Tabla DataTables

```html
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm w-100" id="table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Fecha de Nacimiento</th>
                                <th>País</th>
                                <th>Teléfono</th>
                                <th>Usuario App</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Clases CSS:**
- `table`: Estilo base de Bootstrap
- `table-hover`: Efecto hover en filas
- `table-bordered`: Bordes en celdas
- `table-sm`: Tabla compacta
- `w-100`: Ancho completo
- `thead-light`: Encabezado claro

---

### Modales

#### Modal Save (`#modalSave`)

**Header:**
```html
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Registro de Cliente App</h5>
    <button type="button" class="close" data-dismiss="modal">×</button>
</div>
```

**Formulario (`#formSave`):**

**Sección: Datos Personales**
- `txtNames`: Nombres (required, pattern, uppercase oninput, maxlength 255)
- `txtLastname`: Apellidos (required, pattern, uppercase oninput, maxlength 255)
- `txtEmail`: Email (required, type="email", pattern, lowercase oninput)
- `txtDateOfBirth`: Fecha de nacimiento (required, type="date")
- `txtCountry`: País (required, uppercase oninput, maxlength 50)
- `txtTelephonePrefix`: Prefijo telefónico (required, maxlength 7)
- `txtPhoneNumber`: Número de teléfono (required, pattern="^\d+$", maxlength 11)

**Sección: Datos de Usuario de la App (Opcional)**
- `txtUser`: Usuario (pattern="^[a-zA-Z0-9_-]{3,15}$", minlength 3, maxlength 15)
- `txtPassword`: Contraseña (type="password", minlength 8)

**Características:**
- Token CSRF: `<?= csrf(); ?>`
- Autocompletado deshabilitado: `autocomplete="off"`
- Validación HTML5 con mensajes de ayuda

---

#### Modal Update (`#modalUpdate`)

**Header:**
```html
<div class="modal-header bg-success text-white">
    <h5 class="modal-title">Actualizar información del Cliente</h5>
</div>
```

**Formulario (`#formUpdate`):**

**Campos:**
- `update_txtId`: ID oculto
- `update_txtNames`, `update_txtLastname`, `update_txtEmail`, etc.: Campos de persona
- `update_slctStatus`: Estado (required)
- `update_txtUserAppId`: ID de usuario oculto
- `update_txtUser`: Usuario (opcional)
- `update_txtPassword`: Contraseña (opcional, placeholder: "Deje vacío para mantener la actual")
- `update_slctUserStatus`: Estado del usuario

**Diferencia con Save:**
- Campo `update_slctStatus` es requerido
- Campo `update_txtPassword` permite estar vacío

---

#### Modal Delete (`#confirmModalDelete`)

**Header:**
```html
<div class="modal-header bg-danger text-white">
    <h5 class="modal-title">Confirmación de Eliminación</h5>
</div>
```

**Body:**
```html
<div class="modal-body text-center">
    <i class="fa fa-exclamation-triangle fa-5x text-danger mb-3"></i>
    <p class="font-weight-bold">¿Estás seguro?</p>
    <p id="txtDelete"></p>
    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
</div>
```

**Botón de confirmación:**
```html
<button type="button" class="btn btn-danger" data-token="<?= csrf(false) ?>" id="confirmDelete">
    <i class="fa fa-check"></i> Eliminar
</button>
```

---

#### Modal Report (`#modalReport`)

**Header:**
```html
<div class="modal-header bg-info text-white">
    <h5 class="modal-title font-weight-bold">Reporte de Cliente</h5>
</div>
```

**Body:**
- Nombre completo destacado
- Tabla de datos personales
- Tabla de datos de usuario de la app
- Fechas de registro y actualización

**Elementos:**
- `#reportFullName`: Nombre completo
- `#reportNames`, `#reportLastname`, `#reportEmail`, etc.: Datos personales
- `#reportUser`: Usuario (o "Sin usuario")
- `#reportPassword`: Contraseña desencriptada
- `#reportUserStatus`: Estado del usuario con badge
- `#reportRegistrationDate`, `#reportUpdateDate`: Fechas formateadas

---

## JavaScript (functions_plans.js)

### Variables Globales

```javascript
let table;  // Instancia de DataTables
toastr.options = {
  closeButton: true,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "5000",
  progressBar: true,
};
```

### Event Listeners

#### DOMContentLoaded

```javascript
window.addEventListener("DOMContentLoaded", (e) => {
  e.preventDefault();
  loadTable();
  setTimeout(() => {
    saveData();
    confirmationDelete();
    deleteData();
    loadPeopleReport();
    loadDataUpdate();
    updateData();
  }, 1500);
});
```

**Propósito:** Inicializa la tabla y configura los event listeners después de 1.5 segundos (para asegurar que el DOM esté completamente cargado).

#### Click Global

```javascript
window.addEventListener("click", (e) => {
  loadPeopleReport();
  confirmationDelete();
  loadDataUpdate();
});
```

**Propósito:** Reconfigura los event listeners después de cada clic (para elementos dinámicos de DataTables).

---

### Funciones Principales

#### 1. `loadTable()`

Inicializa DataTables con configuración completa.

**Configuración:**
- `aProcessing: true`: Muestra indicador de carga
- `aServerSide: true`: Procesamiento en servidor
- `ajax.url`: `base_url + "/Customersuserapp/getPeople"`
- `ajax.dataSrc: ""`: Datos directos del JSON

**Columnas:**
```javascript
columns: [
  { data: "cont" },
  { data: "names" },
  { data: "lastname" },
  { data: "email" },
  { data: "date_of_birth_formatted" },
  { data: "country" },
  { data: "phone_full" },
  { data: "user_app" },
  { data: "status" },
  { data: "actions" },
]
```

**Botones de exportación:**
- Copiar (columnas 1-7)
- Excel (columnas 1-7)
- CSV (columnas 1-7)
- PDF (columnas 1-7, landscape, LEGAL)

**ColumnDefs:**
- Columna 0: Centrado
- Columnas 1-7: Alineado izquierda
- Columna 7 (Usuario App): Renderizado personalizado (badge si "Sin usuario")
- Columna 9 (Acciones): No ordenable, no buscable, centrado

**Idioma:**
- Archivo JSON: `base_url + "/Assets/js/libraries/Admin/Spanish-datatables.json"`

**Callback:**
```javascript
fnDrawCallback: function () {
  $(".dataTables_paginate > .pagination").addClass("pagination-sm");
  confirmationDelete();
  loadPeopleReport();
  loadDataUpdate();
}
```

---

#### 2. `saveData()`

Maneja el envío del formulario de registro.

**Proceso:**
1. Previene submit por defecto
2. Crea FormData del formulario
3. Muestra loader: `elementLoader.classList.remove("d-none")`
4. Envía POST a `/Customersuserapp/setPeople`
5. Si éxito:
   - Resetea formulario
   - Cierra modal
   - Recarga tabla
6. Muestra notificación Toastr
7. Oculta loader

**Manejo de errores:**
- Captura errores de red
- Muestra notificación de error
- Oculta loader

---

#### 3. `confirmationDelete()`

Configura el modal de confirmación de eliminación.

**Proceso:**
1. Selecciona todos los botones `.delete-item`
2. Para cada botón:
   - Captura `data-fullname`, `data-id`, `data-user-app-id`
   - Actualiza texto del modal
   - Guarda datos en botón de confirmación
   - Muestra modal

---

#### 4. `deleteData()`

Ejecuta la eliminación.

**Proceso:**
1. Captura datos del botón de confirmación
2. Crea objeto JSON con `id`, `fullname`, `token`, `user_app_id`
3. Envía DELETE a `/Customersuserapp/deletePeople`
4. Si éxito:
   - Cierra modal
   - Recarga tabla
5. Muestra notificación
6. Oculta loader

---

#### 5. `loadPeopleReport()`

Carga datos en el modal de reporte.

**Proceso:**
1. Selecciona todos los botones `.report-item`
2. Para cada botón:
   - Captura todos los atributos `data-*`
   - Llena elementos del modal
   - Muestra loader
   - Muestra modal
   - Oculta loader después de 500ms

**Datos del usuario:**
```javascript
if (hasUser === "1" && user && user !== "Sin usuario") {
  reportUser.innerHTML = user;
  reportPassword.innerHTML = userPassword || "-";
  reportUserStatus.innerHTML = userStatus === "Activo" 
    ? '<span class="badge badge-success">Activo</span>' 
    : '<span class="badge badge-danger">Inactivo</span>';
} else {
  reportUser.innerHTML = '<span class="badge badge-secondary">Sin usuario</span>';
  reportPassword.innerHTML = "-";
  reportUserStatus.innerHTML = "-";
}
```

---

#### 6. `loadDataUpdate()`

Carga datos en el formulario de actualización.

**Proceso:**
1. Selecciona todos los botones `.update-item`
2. Para cada botón:
   - Captura atributos `data-*`
   - Llena campos del formulario
   - Si tiene usuario: Llena campos de usuario
   - Si no tiene usuario: Limpia campos de usuario
   - Muestra loader
   - Muestra modal
   - Oculta loader después de 500ms

**Lógica de usuario:**
```javascript
update_txtUserAppId.value = userAppId;
if (hasUser === "1" && user && user !== "Sin usuario") {
  update_txtUser.value = user;
  update_txtPassword.value = userPassword;
  update_slctUserStatus.value = userStatus;
} else {
  update_txtUser.value = "";
  update_txtPassword.value = "";
  update_slctUserStatus.value = "Activo";
}
```

---

#### 7. `updateData()`

Maneja el envío del formulario de actualización.

**Proceso:**
Similar a `saveData()`, pero:
- URL: `/Customersuserapp/updatePeople`
- Modal: `#modalUpdate`
- Botón: `btn-success`

---

## Estilos CSS (style_customersuserapp.css)

### Responsive Design

```css
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
    }

    .btn-group .btn {
        margin: 2px 0;
        width: 100%;
    }
}
```

**Propósito:** En pantallas pequeñas, los botones de acción se apilan verticalmente y ocupan el ancho completo.

---

## Flujos de Trabajo

### 1. Registro de Persona con Usuario

```
Usuario llena formulario
    ↓
[Validación HTML5] → Nombres, email, teléfono, etc.
    ↓
[Submit] → saveData()
    ↓
[POST] /Customersuserapp/setPeople
    ↓
[Backend]
    ├─ Validación CSRF
    ├─ Validación de campos
    ├─ Validación de formatos
    ├─ Validación de duplicados (email, teléfono)
    ├─ Si hay usuario:
    │   ├─ Validación formato usuario
    │   ├─ Validación usuario único (encriptado)
    │   ├─ Validación contraseña (mínimo 8 caracteres)
    │   └─ Encriptación usuario y contraseña
    ├─ Conversión a mayúsculas (nombres, apellidos, país)
    ├─ [INSERT] people (sin status, usa default)
    └─ Si hay usuario: [INSERT] user_app (sin status, usa default)
    ↓
[Respuesta JSON]
    ├─ Éxito: { status: true, type: "success" }
    └─ Error: { status: false, type: "error" }
    ↓
[JavaScript]
    ├─ Si éxito: Reset formulario, cerrar modal, recargar tabla
    ├─ Mostrar notificación Toastr
    └─ Ocultar loader
```

---

### 2. Actualización de Persona y Usuario

```
Usuario hace clic en "Editar" (botón verde)
    ↓
[JavaScript] → loadDataUpdate()
    ├─ Captura atributos data-* del botón
    ├─ Llena formulario #formUpdate
    └─ Muestra modal #modalUpdate
    ↓
Usuario modifica datos
    ↓
[Submit] → updateData()
    ↓
[POST] /Customersuserapp/updatePeople
    ↓
[Backend]
    ├─ Validaciones (similar a setPeople)
    ├─ Validación que persona exista
    ├─ Validación duplicados (excepto actual)
    ├─ [UPDATE] people
    └─ Si hay usuario:
        ├─ Si existe update_txtUserAppId:
        │   └─ [UPDATE] user_app (contraseña opcional)
        └─ Si no existe:
            └─ [INSERT] user_app (genera contraseña si no se proporciona)
    ↓
[Respuesta JSON] → Similar a setPeople
    ↓
[JavaScript] → Similar a saveData
```

---

### 3. Eliminación de Persona

```
Usuario hace clic en "Eliminar" (botón rojo)
    ↓
[JavaScript] → confirmationDelete()
    ├─ Captura data-id, data-fullname, data-user-app-id
    ├─ Actualiza texto del modal
    └─ Muestra modal #confirmModalDelete
    ↓
Usuario confirma eliminación
    ↓
[JavaScript] → deleteData()
    ↓
[DELETE] /Customersuserapp/deletePeople
    Body: { id, fullname, token, user_app_id }
    ↓
[Backend]
    ├─ Validación método DELETE
    ├─ Validación CSRF
    ├─ Validación ID
    ├─ Validación que persona exista
    ├─ Si existe user_app_id: [DELETE] user_app
    └─ [DELETE] people
    ↓
[Respuesta JSON]
    ↓
[JavaScript]
    ├─ Cerrar modal
    ├─ Recargar tabla
    └─ Mostrar notificación
```

---

### 4. Visualización de Reporte

```
Usuario hace clic en "Ver" (botón azul)
    ↓
[JavaScript] → loadPeopleReport()
    ├─ Captura atributos data-* del botón
    ├─ Llena elementos del modal #modalReport
    └─ Muestra modal
    ↓
Modal muestra:
    ├─ Datos personales (tabla)
    ├─ Datos de usuario (tabla, si existe)
    └─ Fechas de registro y actualización
```

---

## Validaciones y Seguridad

### Validaciones Frontend (HTML5)

| Campo | Validación |
|-------|------------|
| Nombres | `pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"`, `maxlength="255"` |
| Apellidos | `pattern="^[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*$"`, `maxlength="255"` |
| Email | `type="email"`, `pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"` |
| Fecha de Nacimiento | `type="date"` |
| País | `maxlength="50"` |
| Prefijo Tel. | `maxlength="7"` |
| Número Tel. | `pattern="^\d+$"`, `maxlength="11"` |
| Usuario | `pattern="^[a-zA-Z0-9_-]{3,15}$"`, `minlength="3"`, `maxlength="15"` |
| Contraseña | `type="password"`, `minlength="8"` |

---

### Validaciones Backend (PHP)

#### Persona

1. **Nombres/Apellidos:**
   ```php
   verifyData("[A-ZÁÉÍÓÚÑa-záéíóúñ]+(\s[A-ZÁÉÍÓÚÑa-záéíóúñ]+)*", $strNames)
   ```

2. **Email:**
   ```php
   verifyData("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", $strEmail)
   // + Verificación de duplicados
   ```

3. **Fecha:**
   ```php
   $dateParts = explode('-', $strDateOfBirth);
   checkdate($dateParts[1], $dateParts[2], $dateParts[0])
   ```

4. **Teléfono:**
   ```php
   preg_match('/^\d+$/', $strPhoneNumber)
   // + Verificación de duplicados
   ```

#### Usuario

1. **Formato:**
   ```php
   verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)
   ```

2. **Duplicados:**
   ```php
   $strUserEncrypted = encryption($strUser);
   $this->model->select_user_app_by_user($strUserEncrypted);
   ```

3. **Contraseña:**
   ```php
   strlen($strPassword) >= 8
   ```

---

### Seguridad

#### 1. Encriptación

- **Usuario y contraseña** se encriptan antes de guardar en BD
- Funciones: `encryption()` y `decryption()` de `Helpers.php`
- Se desencriptan solo para mostrar en la interfaz

**Ejemplo:**
```php
// Al guardar
$strUserEncrypted = encryption($strUser);
$strPasswordEncrypted = encryption($strPassword);

// Al mostrar
$userName = decryption($userApp["user"]);
$userPassword = decryption($userApp["password"]);
```

#### 2. CSRF Protection

- Todos los métodos validan token CSRF
- `isCsrf()` para POST
- `isCsrf($request["token"])` para DELETE

#### 3. Sanitización

- Todos los inputs se limpian con `strClean()`
- Previene inyección XSS

#### 4. Validación de Permisos

- `permissionInterface(15)` en todos los métodos
- Controla acceso basado en roles

#### 5. Logging

- Todas las operaciones se registran
- Tipos: Error (1), Éxito (2), Advertencia (3)
- Incluye información del usuario, IP, método, URL, user agent

#### 6. Prepared Statements

- Todas las consultas usan PDO prepared statements
- Previene inyección SQL

---

## Integración con el Sistema

### Template System

El módulo se integra con el sistema de templates mediante:

1. **Header:** `<?= headerAdmin($data) ?>`
   - Carga CSS, JavaScript, meta tags
   - Incluye navbar, sidebar

2. **Footer:** `<?= footerAdmin($data) ?>`
   - Carga JavaScript específico del módulo
   - Cierra estructura HTML

3. **Configuración de página:**
   ```php
   $data = [
       'page_id' => 15,
       'page_title' => 'Clientes App',
       'page_container' => 'Customersuserapp',
       'page_view' => 'customersuserapp',
       'page_js_css' => 'customersuserapp',
   ];
   ```

### Carga de Assets

**CSS:**
- `Assets/css/app/Admin/customersuserapp/style_customersuserapp.css`
- Cargado automáticamente por el template

**JavaScript:**
- `Assets/js/app/Admin/customersuserapp/functions_plans.js`
- Cargado automáticamente por el template

### Dependencias

**Librerías JavaScript:**
- jQuery 3.7.1
- DataTables 1.10+
- Bootstrap 4
- Toastr
- Font Awesome 4.7.0

**Librerías CSS:**
- Bootstrap 4
- DataTables
- Toastr
- Font Awesome 4.7.0

### Helpers Utilizados

- `base_url()`: URL base del sistema
- `media()`: Ruta de assets
- `csrf()`: Genera token CSRF
- `strClean()`: Limpia strings
- `encryption()` / `decryption()`: Encriptación
- `dateFormat()`: Formatea fechas
- `registerLog()`: Registra logs
- `isCsrf()`: Valida CSRF
- `validateFields()`: Valida campos requeridos
- `validateFieldsEmpty()`: Valida campos no vacíos
- `verifyData()`: Valida formato con regex
- `toJson()`: Retorna JSON
- `passGenerator()`: Genera contraseña aleatoria

---

## Notas Importantes

1. **Status por defecto:** Al insertar persona o usuario, no se especifica `status` (usa default de BD: 'Activo')

2. **Encriptación obligatoria:** Siempre encriptar usuario y contraseña antes de guardar

3. **Validación de duplicados:** Comparar usuarios encriptados en la BD

4. **Contraseña opcional en actualización:** Si está vacía, se mantiene la actual

5. **Relación 1:1:** Una persona puede tener máximo un usuario

6. **Eliminación manual:** Al eliminar persona, se debe eliminar manualmente el usuario asociado

7. **Conversión a mayúsculas:** Nombres, apellidos y país se convierten automáticamente

8. **Event listeners dinámicos:** Se reconfiguran después de cada operación para elementos nuevos de DataTables

---

## Troubleshooting

### Problema: Usuario no se muestra en la tabla

**Causas posibles:**
- Error en desencriptación
- Usuario no existe en BD
- Error en LEFT JOIN

**Solución:**
- Verificar que `select_people_with_users()` retorne datos
- Verificar que `decryption()` funcione correctamente
- Revisar logs del sistema

---

### Problema: Error al validar usuario duplicado

**Causa:** Usuario no encriptado antes de buscar

**Solución:**
```php
// ❌ Incorrecto
$request = $this->model->select_user_app_by_user($strUser);

// ✅ Correcto
$strUserEncrypted = encryption($strUser);
$request = $this->model->select_user_app_by_user($strUserEncrypted);
```

---

### Problema: Contraseña no se actualiza

**Causa:** Se envía vacío y el modelo la mantiene

**Solución:**
- Verificar que `update_user_app()` reciba `null` si no se actualiza
- Si se quiere actualizar, enviar valor no vacío

---

### Problema: DataTables no carga datos

**Causas posibles:**
- Error en URL del AJAX
- Error en formato JSON
- Error de permisos

**Solución:**
- Verificar URL: `base_url + "/Customersuserapp/getPeople"`
- Verificar que retorne JSON válido
- Verificar permisos del usuario (page_id: 15)
- Revisar consola del navegador

---

## Conclusión

El módulo **Customersuserapp** proporciona una solución completa para gestionar clientes y sus credenciales de acceso, con validaciones robustas, seguridad mediante encriptación, y una interfaz moderna y responsive. La implementación sigue las mejores prácticas del framework MVC personalizado y mantiene consistencia con el resto del sistema.

---

**Última actualización:** 2025-01-XX  
**Versión del módulo:** 1.0  
**Sistema:** Capy Ventas
