# Documentación del Módulo de Usuarios de Aplicación (user_app)

## Índice
1. [Descripción General](#descripción-general)
2. [Estructura de Base de Datos](#estructura-de-base-de-datos)
3. [Modelo (CustomersuserappModel)](#modelo-customersuserappmodel)
4. [Controlador (Customersuserapp)](#controlador-customersuserapp)
5. [Vista (customersuserapp.php)](#vista-customersuserappphp)
6. [JavaScript (functions_customersuserapp.js)](#javascript-functions_customersuserappjs)
7. [Flujo de Trabajo](#flujo-de-trabajo)
8. [Validaciones y Seguridad](#validaciones-y-seguridad)
9. [Ejemplos de Uso](#ejemplos-de-uso)

---

## Descripción General

El módulo de **Usuarios de Aplicación** permite gestionar credenciales de acceso para los clientes registrados en el sistema. Cada persona (`people`) puede tener asociado un usuario de aplicación (`user_app`) que le permite acceder a la aplicación móvil o web.

### Características Principales

- **Registro opcional**: Al registrar una persona, se puede crear simultáneamente su usuario de aplicación
- **Gestión completa**: Crear, actualizar y eliminar usuarios asociados a personas
- **Seguridad**: Usuario y contraseña se almacenan encriptados en la base de datos
- **Validaciones**: Verificación de formato, duplicados y longitud mínima
- **Relación**: Cada usuario está vinculado a una persona mediante `people_id`

---

## Estructura de Base de Datos

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

### Campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `idUserApp` | INT(11) | Identificador único del usuario (AUTO_INCREMENT) |
| `user` | VARCHAR(255) | Nombre de usuario (almacenado encriptado) |
| `password` | TEXT | Contraseña del usuario (almacenada encriptada) |
| `status` | ENUM | Estado del usuario: 'Activo' o 'Inactivo' |
| `registration_date` | TIMESTAMP | Fecha y hora de registro (automático) |
| `update_date` | TIMESTAMP | Fecha y hora de última actualización (automático) |
| `people_id` | INT(11) | ID de la persona asociada (FK a `people.idPeople`) |

### Relación

- **Relación**: `user_app.people_id` → `people.idPeople` (Foreign Key)
- **Cardinalidad**: Uno a Uno (una persona puede tener un usuario de aplicación)
- **Cascada**: Al eliminar una persona, se debe eliminar manualmente su usuario asociado

---

## Modelo (CustomersuserappModel)

El modelo `CustomersuserappModel` extiende de `Mysql` y proporciona métodos para interactuar con la tabla `user_app`.

### Métodos Implementados

#### 1. `select_user_app_by_people_id(int $peopleId)`

Obtiene el usuario de la aplicación asociado a una persona específica.

**Parámetros:**
- `$peopleId` (int): ID de la persona

**Retorna:**
- `array|false`: Datos del usuario o `false` si no existe

**Ejemplo:**
```php
$userApp = $this->model->select_user_app_by_people_id(1);
if ($userApp) {
    $userName = decryption($userApp["user"]);
    $password = decryption($userApp["password"]);
}
```

---

#### 2. `select_user_app_by_user(string $user)`

Busca un usuario por su nombre de usuario (para validar duplicados).

**Parámetros:**
- `$user` (string): Nombre de usuario encriptado

**Retorna:**
- `array|false`: Datos del usuario o `false` si no existe

**Nota:** El parámetro `$user` debe estar encriptado antes de llamar a este método.

**Ejemplo:**
```php
$encryptedUser = encryption("usuario123");
$existingUser = $this->model->select_user_app_by_user($encryptedUser);
if ($existingUser) {
    // El usuario ya existe
}
```

---

#### 3. `insert_user_app($user, $password, $status, $peopleId)`

Inserta un nuevo usuario de aplicación en la base de datos.

**Parámetros:**
- `$user` (string): Nombre de usuario encriptado
- `$password` (string): Contraseña encriptada
- `$status` (string): Estado ('Activo' o 'Inactivo')
- `$peopleId` (int): ID de la persona asociada

**Retorna:**
- `int|false`: ID del registro insertado o `false` en caso de error

**Ejemplo:**
```php
$userEncrypted = encryption("usuario123");
$passwordEncrypted = encryption("password123");
$idUserApp = $this->model->insert_user_app(
    $userEncrypted,
    $passwordEncrypted,
    "Activo",
    1
);
```

---

#### 4. `update_user_app($idUserApp, $user, $password, $status)`

Actualiza un usuario de aplicación existente.

**Parámetros:**
- `$idUserApp` (int): ID del usuario a actualizar
- `$user` (string): Nombre de usuario encriptado
- `$password` (string|null): Contraseña encriptada o `null` para mantener la actual
- `$status` (string): Estado ('Activo' o 'Inactivo')

**Retorna:**
- `bool`: `true` si se actualizó correctamente, `false` en caso contrario

**Nota:** Si `$password` es `null` o vacío, no se actualiza la contraseña.

**Ejemplo:**
```php
// Actualizar usuario y contraseña
$userEncrypted = encryption("nuevo_usuario");
$passwordEncrypted = encryption("nueva_password");
$this->model->update_user_app(1, $userEncrypted, $passwordEncrypted, "Activo");

// Actualizar solo usuario (mantener contraseña)
$userEncrypted = encryption("usuario_actualizado");
$this->model->update_user_app(1, $userEncrypted, null, "Activo");
```

---

#### 5. `delete_user_app($idUserApp)`

Elimina un usuario de aplicación de la base de datos.

**Parámetros:**
- `$idUserApp` (int): ID del usuario a eliminar

**Retorna:**
- `bool`: `true` si se eliminó correctamente, `false` en caso contrario

**Ejemplo:**
```php
$deleted = $this->model->delete_user_app(1);
if ($deleted) {
    // Usuario eliminado correctamente
}
```

---

#### 6. `select_people_with_users()`

Obtiene todas las personas con sus usuarios asociados mediante un LEFT JOIN.

**Retorna:**
- `array`: Lista de personas con información de usuario (si existe)

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
        'user' => 'usuario123',   // null si no tiene usuario
        'user_status' => 'Activo' // null si no tiene usuario
    ],
    // ...
]
```

**Ejemplo:**
```php
$peopleWithUsers = $this->model->select_people_with_users();
foreach ($peopleWithUsers as $person) {
    if ($person['idUserApp']) {
        echo "Persona: {$person['names']} - Usuario: {$person['user']}";
    } else {
        echo "Persona: {$person['names']} - Sin usuario";
    }
}
```

---

## Controlador (Customersuserapp)

El controlador `Customersuserapp` maneja las peticiones HTTP y coordina las operaciones entre el modelo y la vista.

### Métodos Modificados/Implementados

#### 1. `getPeople()`

Obtiene la lista de todas las personas con información de sus usuarios asociados para mostrar en la tabla DataTables.

**Funcionalidad:**
- Consulta personas con usuarios mediante `select_people_with_users()`
- Desencripta usuario y contraseña para mostrar en la interfaz
- Formatea datos para DataTables
- Incluye información de usuario en los atributos `data-*` de los botones

**Datos adicionales en la respuesta:**
```php
$arrData[$key]["user_app"] = $userName;        // Usuario desencriptado
$arrData[$key]["has_user"] = true/false;       // Indica si tiene usuario
```

**Atributos en botones de acción:**
- `data-user-app-id`: ID del usuario de aplicación
- `data-user`: Nombre de usuario desencriptado
- `data-user-password`: Contraseña desencriptada
- `data-user-status`: Estado del usuario
- `data-has-user`: "1" si tiene usuario, "0" si no

---

#### 2. `setPeople()`

Registra una nueva persona y opcionalmente crea su usuario de aplicación.

**Campos adicionales recibidos (opcionales):**
- `txtUser`: Nombre de usuario
- `txtPassword`: Contraseña
- `slctUserStatus`: Estado del usuario (por defecto: "Activo")

**Validaciones:**
1. Si se proporciona usuario:
   - Formato: 3-15 caracteres alfanuméricos, guiones bajos o guiones
   - No debe existir en el sistema
   - Contraseña obligatoria (mínimo 8 caracteres)

**Flujo:**
1. Valida datos de la persona
2. Valida datos del usuario (si se proporcionan)
3. Inserta la persona en `people`
4. Si se proporcionó usuario y contraseña:
   - Encripta usuario y contraseña
   - Inserta en `user_app`
5. Retorna respuesta JSON

**Ejemplo de petición POST:**
```php
$_POST = [
    'txtNames' => 'Juan',
    'txtLastname' => 'Pérez',
    'txtEmail' => 'juan@example.com',
    // ... otros campos de persona
    'txtUser' => 'usuario123',        // Opcional
    'txtPassword' => 'password123',   // Opcional (requerido si hay usuario)
    'slctUserStatus' => 'Activo'      // Opcional
];
```

---

#### 3. `updatePeople()`

Actualiza los datos de una persona y gestiona su usuario de aplicación.

**Campos adicionales recibidos (opcionales):**
- `update_txtUserAppId`: ID del usuario existente (si existe)
- `update_txtUser`: Nombre de usuario
- `update_txtPassword`: Contraseña (vacío para mantener la actual)
- `update_slctUserStatus`: Estado del usuario

**Validaciones:**
1. Si se proporciona usuario:
   - Formato válido
   - No debe estar duplicado (excepto el actual)
   - Si se proporciona contraseña, mínimo 8 caracteres

**Flujo:**
1. Valida datos de la persona
2. Valida datos del usuario (si se proporcionan)
3. Actualiza la persona en `people`
4. Si se proporcionó usuario:
   - Si existe `update_txtUserAppId`: Actualiza usuario existente
   - Si no existe: Crea nuevo usuario
   - Si no se proporciona contraseña: Mantiene la actual o genera una aleatoria

**Casos de uso:**
- **Actualizar usuario existente**: Proporcionar `update_txtUserAppId` y `update_txtUser`
- **Crear usuario nuevo**: No proporcionar `update_txtUserAppId`, pero sí `update_txtUser`
- **Cambiar contraseña**: Proporcionar `update_txtPassword`
- **Mantener contraseña**: No proporcionar `update_txtPassword` o enviar vacío

---

#### 4. `deletePeople()`

Elimina una persona y su usuario de aplicación asociado (si existe).

**Parámetros adicionales recibidos:**
- `user_app_id`: ID del usuario de aplicación a eliminar

**Flujo:**
1. Valida que la persona exista
2. Si existe `user_app_id`, elimina el usuario de aplicación
3. Elimina la persona
4. Retorna respuesta JSON

**Ejemplo de petición DELETE:**
```json
{
    "id": 1,
    "fullname": "Juan Pérez",
    "token": "csrf_token",
    "user_app_id": 1
}
```

---

## Vista (customersuserapp.php)

La vista incluye campos adicionales para gestionar usuarios de aplicación en los modales de registro, actualización y reporte.

### Modal de Registro (`modalSave`)

**Sección: "Datos de Usuario de la App (Opcional)"**

```html
<h5>Datos de Usuario de la App (Opcional)</h5>
<hr>
<div class="bg-light p-2 rounded">
    <!-- Campo Usuario -->
    <input type="text" 
           id="txtUser" 
           name="txtUser"
           pattern="^[a-zA-Z0-9_-]{3,15}$"
           minlength="3" 
           maxlength="15"
           placeholder="Ingrese el usuario">
    
    <!-- Campo Contraseña -->
    <input type="password" 
           id="txtPassword" 
           name="txtPassword"
           minlength="8"
           placeholder="Ingrese la contraseña">
    
    <!-- Campo Estado del Usuario -->
    <select id="slctUserStatus" name="slctUserStatus">
        <option value="Activo" selected>Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select>
</div>
```

**Características:**
- Campos opcionales (no requeridos)
- Validación HTML5: Patrón para usuario, longitud mínima para contraseña
- Mensajes de ayuda: Indicaciones sobre formato y requisitos

---

### Modal de Actualización (`modalUpdate`)

**Sección: "Datos de Usuario de la App (Opcional)"**

```html
<input type="hidden" name="update_txtUserAppId" id="update_txtUserAppId">
<!-- Campos similares al modal de registro con prefijo "update_" -->
```

**Características:**
- Campo oculto `update_txtUserAppId` para identificar usuario existente
- Campo contraseña permite dejarlo vacío para mantener la actual
- Mensaje: "Deje vacío para mantener la contraseña actual"

---

### Modal de Reporte (`modalReport`)

**Sección: "Datos de Usuario de la App"**

```html
<h6 class="text-uppercase font-weight-bold text-danger mt-4">
    Datos de Usuario de la App
</h6>
<hr>
<table class="table table-bordered">
    <tbody>
        <tr>
            <td><strong>Usuario</strong></td>
            <td id="reportUser">Sin usuario</td>
        </tr>
        <tr>
            <td><strong>Contraseña</strong></td>
            <td id="reportPassword">-</td>
        </tr>
        <tr>
            <td><strong>Estado del Usuario</strong></td>
            <td id="reportUserStatus">-</td>
        </tr>
    </tbody>
</table>
```

**Elementos:**
- `#reportUser`: Muestra el nombre de usuario o "Sin usuario"
- `#reportPassword`: Muestra la contraseña desencriptada
- `#reportUserStatus`: Muestra el estado con badge (Activo/Inactivo)

---

### Tabla Principal

**Nueva columna: "Usuario App"**

```html
<thead>
    <tr>
        <th>#</th>
        <th>Nombres</th>
        <th>Apellidos</th>
        <!-- ... otras columnas ... -->
        <th>Usuario App</th>  <!-- Nueva columna -->
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
</thead>
```

**Renderizado:**
- Si tiene usuario: Muestra el nombre de usuario
- Si no tiene usuario: Muestra badge "Sin usuario"

---

## JavaScript (functions_customersuserapp.js)

El archivo JavaScript maneja la interacción del usuario con los formularios y modales.

### Funciones Modificadas/Implementadas

#### 1. `loadTable()`

**Modificaciones:**
- Agregada columna `{ data: "user_app" }` en la configuración de DataTables
- Renderizado personalizado para mostrar badge si no tiene usuario

```javascript
{
    targets: [7], // Columna de usuario
    render: function (data, type, row) {
        if (data === "Sin usuario" || !data) {
            return '<span class="badge badge-secondary">Sin usuario</span>';
        }
        return data;
    },
}
```

---

#### 2. `confirmationDelete()`

**Modificaciones:**
- Captura `data-user-app-id` del botón de eliminar
- Almacena el ID en el botón de confirmación

```javascript
const userAppId = item.getAttribute("data-user-app-id") || "";
confirmDelete.setAttribute("data-user-app-id", userAppId);
```

---

#### 3. `deleteData()`

**Modificaciones:**
- Incluye `user_app_id` en la petición DELETE

```javascript
const userAppId = confirmDelete.getAttribute("data-user-app-id") || "";
const arrValues = {
    id: id,
    fullname: fullname,
    token: token,
    user_app_id: userAppId,  // Nuevo campo
};
```

---

#### 4. `loadPeopleReport()`

**Modificaciones:**
- Captura datos del usuario desde atributos `data-*`
- Muestra información del usuario en el modal de reporte

```javascript
const user = item.getAttribute("data-user");
const userPassword = item.getAttribute("data-user-password");
const userStatus = item.getAttribute("data-user-status");
const hasUser = item.getAttribute("data-has-user");

// Mostrar en el modal
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

#### 5. `loadDataUpdate()`

**Modificaciones:**
- Captura datos del usuario desde atributos `data-*`
- Llena los campos del formulario de actualización

```javascript
const userAppId = item.getAttribute("data-user-app-id") || "";
const user = item.getAttribute("data-user") || "";
const userPassword = item.getAttribute("data-user-password") || "";
const userStatus = item.getAttribute("data-user-status") || "Activo";
const hasUser = item.getAttribute("data-has-user") || "0";

// Llenar campos
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

## Flujo de Trabajo

### 1. Registro de Persona con Usuario

```
Usuario llena formulario
    ↓
[Validación Frontend] → Campos opcionales de usuario
    ↓
[POST] /Customersuserapp/setPeople
    ↓
[Validación Backend]
    - Datos de persona
    - Si hay usuario: formato, duplicados, contraseña
    ↓
[Insertar] people
    ↓
[Si hay usuario] → [Insertar] user_app
    ↓
[Respuesta JSON] → Éxito/Error
    ↓
[Actualizar tabla] DataTables
```

---

### 2. Actualización de Persona y Usuario

```
Usuario hace clic en "Editar"
    ↓
[Modal] Se cargan datos de persona y usuario
    ↓
Usuario modifica datos
    ↓
[POST] /Customersuserapp/updatePeople
    ↓
[Validación Backend]
    ↓
[Actualizar] people
    ↓
[Si hay usuario]
    - Si existe ID: [Actualizar] user_app
    - Si no existe ID: [Insertar] user_app
    ↓
[Respuesta JSON] → Éxito/Error
    ↓
[Actualizar tabla] DataTables
```

---

### 3. Eliminación de Persona

```
Usuario hace clic en "Eliminar"
    ↓
[Modal de confirmación]
    ↓
[DELETE] /Customersuserapp/deletePeople
    ↓
[Validar] que persona exista
    ↓
[Si existe user_app_id] → [Eliminar] user_app
    ↓
[Eliminar] people
    ↓
[Respuesta JSON] → Éxito/Error
    ↓
[Actualizar tabla] DataTables
```

---

## Validaciones y Seguridad

### Validaciones Frontend (HTML5)

**Campo Usuario:**
- Patrón: `^[a-zA-Z0-9_-]{3,15}$`
- Longitud: 3-15 caracteres
- Caracteres permitidos: Letras, números, guiones bajos (_), guiones (-)

**Campo Contraseña:**
- Longitud mínima: 8 caracteres
- Tipo: `password` (oculto al escribir)

---

### Validaciones Backend (PHP)

**Usuario:**
```php
// Formato
if (verifyData("[a-zA-Z0-9_-]{3,15}", $strUser)) {
    // Error: Formato inválido
}

// Duplicados (al crear)
$encryptedUser = encryption($strUser);
$existing = $this->model->select_user_app_by_user($encryptedUser);
if ($existing) {
    // Error: Usuario ya existe
}

// Duplicados (al actualizar)
if ($existing['idUserApp'] != $intUserAppId) {
    // Error: Usuario ya existe (otro registro)
}
```

**Contraseña:**
```php
// Longitud mínima
if (strlen($strPassword) < 8) {
    // Error: Mínimo 8 caracteres
}

// Obligatoria si hay usuario
if (!empty($strUser) && empty($strPassword)) {
    // Error: Contraseña requerida
}
```

---

### Seguridad

**Encriptación:**
- Usuario y contraseña se encriptan antes de guardar en BD
- Se desencriptan solo para mostrar en la interfaz
- Funciones utilizadas: `encryption()` y `decryption()` de `Helpers.php`

**CSRF Protection:**
- Todos los métodos validan token CSRF mediante `isCsrf()`

**Sanitización:**
- Todos los inputs se limpian con `strClean()`

**Logging:**
- Todas las operaciones se registran en logs del sistema
- Tipos de log: Error (1), Éxito (2), Advertencia (3)

---

## Ejemplos de Uso

### Ejemplo 1: Registrar Persona con Usuario

**Petición POST:**
```javascript
const formData = new FormData();
formData.append('txtNames', 'Juan');
formData.append('txtLastname', 'Pérez');
formData.append('txtEmail', 'juan@example.com');
formData.append('txtDateOfBirth', '1990-01-15');
formData.append('txtCountry', 'Perú');
formData.append('txtTelephonePrefix', '+51');
formData.append('txtPhoneNumber', '987654321');
formData.append('slctStatus', 'Activo');
formData.append('txtUser', 'juan_perez');        // Opcional
formData.append('txtPassword', 'password123');     // Opcional
formData.append('slctUserStatus', 'Activo');     // Opcional
```

**Respuesta exitosa:**
```json
{
    "title": "Registro exitoso",
    "message": "La persona fue registrada satisfactoriamente en el sistema.",
    "type": "success",
    "status": true
}
```

---

### Ejemplo 2: Actualizar Usuario Existente

**Petición POST:**
```javascript
const formData = new FormData();
formData.append('update_txtId', '1');
formData.append('update_txtNames', 'Juan');
// ... otros campos de persona
formData.append('update_txtUserAppId', '1');       // ID del usuario existente
formData.append('update_txtUser', 'juan_updated');
formData.append('update_txtPassword', '');        // Vacío = mantener actual
formData.append('update_slctUserStatus', 'Activo');
```

**Resultado:**
- Se actualiza el nombre de usuario
- Se mantiene la contraseña actual
- Se actualiza el estado

---

### Ejemplo 3: Crear Usuario para Persona Existente

**Petición POST:**
```javascript
const formData = new FormData();
formData.append('update_txtId', '1');
// ... campos de persona
formData.append('update_txtUserAppId', '');       // Vacío = crear nuevo
formData.append('update_txtUser', 'nuevo_usuario');
formData.append('update_txtPassword', 'nueva_pass');
formData.append('update_slctUserStatus', 'Activo');
```

**Resultado:**
- Se crea un nuevo registro en `user_app`
- Se asocia a la persona mediante `people_id`

---

### Ejemplo 4: Eliminar Persona con Usuario

**Petición DELETE:**
```json
{
    "id": 1,
    "fullname": "Juan Pérez",
    "token": "csrf_token_here",
    "user_app_id": 1
}
```

**Resultado:**
- Se elimina el registro de `user_app` (ID: 1)
- Se elimina el registro de `people` (ID: 1)

---

## Notas Importantes

1. **Encriptación**: Siempre encriptar usuario y contraseña antes de guardar en BD
2. **Desencriptación**: Solo desencriptar para mostrar en la interfaz
3. **Validación de duplicados**: Comparar usuarios encriptados en la BD
4. **Contraseña opcional en actualización**: Si está vacía, se mantiene la actual
5. **Relación obligatoria**: Cada usuario debe tener un `people_id` válido
6. **Eliminación en cascada**: Eliminar usuario antes de eliminar persona (o usar transacciones)

---

## Troubleshooting

### Problema: Usuario no se muestra en la tabla

**Solución:**
- Verificar que `select_people_with_users()` retorne datos correctos
- Verificar que la desencriptación funcione correctamente
- Revisar la columna `user_app` en la configuración de DataTables

### Problema: Error al validar usuario duplicado

**Solución:**
- Asegurarse de encriptar el usuario antes de buscar en BD
- Verificar que `select_user_app_by_user()` reciba el usuario encriptado

### Problema: Contraseña no se actualiza

**Solución:**
- Verificar que `update_user_app()` reciba la contraseña encriptada
- Si se envía vacío, el método debe recibir `null` para mantener la actual

---

## Conclusión

El módulo de Usuarios de Aplicación proporciona una solución completa para gestionar credenciales de acceso de los clientes, con validaciones robustas, seguridad mediante encriptación y una interfaz de usuario intuitiva. La implementación sigue las mejores prácticas de desarrollo y mantiene la consistencia con el resto del sistema.

---

**Última actualización:** 2025-01-XX  
**Versión:** 1.0  
**Autor:** Sistema Capy Ventas
