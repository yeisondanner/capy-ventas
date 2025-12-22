<?php

class Business extends Controllers
{
    /**
     * Nombre de la variable de sesión que almacena el negocio activo.
     *
     * @var string
     */
    protected string $nameVarBusiness;

    /**
     * Nombre de la variable de sesión con los datos del usuario POS.
     *
     * @var string
     */
    protected string $nameVarLoginInfo;

    public function __construct()
    {
        isSession(1);
        parent::__construct('POS');
        $sessionName = config_sesion(1)['name'] ?? '';
        $this->nameVarBusiness  = $sessionName . 'business_active';
        $this->nameVarLoginInfo = $sessionName . 'login_info';
    }
    /**
     * Metodo que se encarga de entrar a las configuracion
     * del negocio
     */
    public function configuration(): void
    {
        validate_permission_app(8, "u");
        $idBusiness = $_SESSION[$this->nameVarBusiness]['idBusiness'] ?? null;
        $infoBusiness = $this->model->select_info_business($idBusiness);
        if (empty($infoBusiness['logo'])) {
            $logoBusiness = GENERAR_PERFIL . htmlspecialchars($infoBusiness['business'] ?? 'Negocio', ENT_QUOTES, 'UTF-8');
        } else {
            $logoBusiness = base_url() . '/Loadfile/iconbusiness?f=' . $infoBusiness['logo'];
        }
        $data = [
            'page_id'          => 9,
            'page_title'       => 'Configuración de negocio',
            'page_description' => 'Configuración de negocio.',
            'page_container'   => 'Business',
            'page_view'        => 'configuration',
            'page_js_css'      => 'configuration',
            'sesion_posbusiness_active' => $infoBusiness,
            'logoBusiness' => $logoBusiness,

        ];

        $this->views->getView($this, 'configuration', $data, 'POS');
    }

    /**
     * Devuelve los negocios registrados por el usuario autenticado.
     *
     * @return void
     */
    public function getBusinesses(): void
    {
        $userId = $this->getUserId();
        $activeBusiness = isset($_SESSION[$this->nameVarBusiness]['idBusiness'])
            ? (int) $_SESSION[$this->nameVarBusiness]['idBusiness']
            : null;
        $businessOwner = $this->model->selectBusinessesByUserOwner($userId);
        //adicionamos un atributo que indique es el dueño de este negocio
        foreach ($businessOwner as $index => $business) {
            $businessOwner[$index]['is_owner'] = true;
        }
        $businessEmploye = $this->model->selectBusinessesByUserEmployee($userId);
        //adicionamos un atributo que indique es el dueño de este negocio
        foreach ($businessEmploye as $index => $business) {
            $businessEmploye[$index]['is_owner'] = false;
        }
        $businesses = array_merge($businessOwner, $businessEmploye);
        foreach ($businesses as $index => $business) {
            $businesses[$index]['business'] = htmlspecialchars($business['business'] ?? '', ENT_QUOTES, 'UTF-8');
            $businesses[$index]['category'] = htmlspecialchars($business['category'] ?? '', ENT_QUOTES, 'UTF-8');
            $businesses[$index]['is_active'] = $activeBusiness === (int) ($business['idBusiness'] ?? 0);
        }

        toJson([
            'status' => true,
            'data'   => $businesses,
        ]);
    }

    /**
     * Obtiene los tipos de negocio activos para el formulario.
     *
     * @return void
     */
    public function getBusinessTypes(): void
    {
        $types = $this->model->selectBusinessTypes();

        foreach ($types as $index => $type) {
            $types[$index]['name'] = htmlspecialchars($type['name'] ?? '', ENT_QUOTES, 'UTF-8');
        }

        toJson([
            'status' => true,
            'data'   => $types,
        ]);
    }

    /**
     * Registra un nuevo negocio asociado al usuario actual.
     *
     * @return void
     */
    public function create(): void
    {
        //VALIDACION DE PERMISOS
        //(!validate_permission_app(8, "c", false)['status']) ? toJson(validate_permission_app(8, "c", false)) : ''; #se comento esto por motivo que si el usuario no tiene ningun negocio creado le permita crear uno
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        isCsrf("", 1);
        validateFields(['businessType', 'businessName', 'businessDocument', 'businessPhone', 'businessTelephonePrefix', 'businessEmail']);
        $userId = $this->getUserId();
        $typebusinessId  = (int) $_POST['businessType'];
        $name            = strClean($_POST['businessName']);
        $documentNumber  = strClean($_POST['businessDocument']);
        $phoneNumber     = strClean($_POST['businessPhone']);
        $telephonePrefix = strClean($_POST['businessTelephonePrefix']);
        $email           = strClean($_POST['businessEmail']);
        $direction       = strClean($_POST['businessDirection']);
        $city            = strClean($_POST['businessCity']);
        $country         = strClean($_POST['businessCountry']);
        validateFieldsEmpty([
            'TIPO DE NEGOCIO' => $typebusinessId,
            'NOMBRE' => $name,
            'DOCUMENTO' => $documentNumber,
            'TELEFONO' => $phoneNumber,
            'PREFIX' => $telephonePrefix,
            'CORREO' => $email,
        ]);

        if ($this->model->findBusinessByDocument($documentNumber, $userId)) {
            $this->responseError('Ya registraste un negocio con el mismo número de documento.');
        }
        //preparamos los datos para insertarlos en la base de datos
        $data = [
            'typebusiness_id'  => $typebusinessId,
            'name'             => $name,
            'direction'        => $direction,
            'city'             => $city,
            'document_number'  => $documentNumber,
            'phone_number'     => $phoneNumber,
            'country'          => $country,
            'telephone_prefix' => $telephonePrefix,
            'email'            => $email,
        ];

        $businessId = $this->model->insertBusiness($data, $userId);

        if (empty($businessId)) {
            $this->responseError('No se pudo registrar el negocio, intenta nuevamente.');
        }
        //insertamos los datos por defecto
        $this->model->insertDefaultData((int) $businessId);

        $newBusiness = $this->model->selectBusinessByIdForUser((int) $businessId, $userId);
        if ($newBusiness) {
            $_SESSION[$this->nameVarBusiness] = $newBusiness;
        }
        toJson([
            'status'  => true,
            'icon'    => 'success',
            'title'   => 'Negocio creado',
            'message' => 'El negocio se registró correctamente.',
            'data'    => $newBusiness,
        ]);
    }

    /**
     * Define un negocio como activo para la sesión actual.
     *
     * @return void
     */
    public function setActiveBusiness(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        validateFields(['businessId', 'owner']);
        $userId = $this->getUserId();
        $businessId = $_POST['businessId'];
        $owner = (bool) ($_POST['owner'] === 'true' ? true : false);
        if ($businessId <= 0) {
            $this->responseError('Identificador de negocio inválido.');
        }
        //validamos que si usuario es dueño o empleado
        if ($owner) {
            $business = $this->model->selectBusinessByIdForUser($businessId, $userId);
            $ownerText = 'Dueño';
        } else if (!$owner) {
            $business = $this->model->selectBusinessByIdUserEmploye($businessId, $userId);
            $ownerText = 'Empleado';
        }
        if (!$business) {
            $this->responseError('El negocio seleccionado no pertenece a tu cuenta.');
        }
        $_SESSION[$this->nameVarBusiness] = $business;
        toJson([
            'status'  => true,
            'title'   => 'Negocio seleccionado',
            'message' => $business['business'] . ' ha sido seleccionado como el negocio actual.',
            'html'    => <<<HTML
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>
                        ¡Negocio cambiado con éxito! Ahora estás gestionando como <strong class="text-danger">$ownerText</strong> el negocio <strong>{$business['business']}</strong>.                   
                        <span class="badge bg-danger text-white badge-pill badge-sm">                      
                            Cambiando, espere un momento...
                        </span>
                    </div>
                </div>
            HTML,
            'timer' => 2000,
            'data'    => $business,
            'reload' => true
        ]);
    }

    /**
     * Obtiene el identificador del usuario autenticado en POS.
     *
     * @return int
     */
    private function getUserId(): int
    {
        if (!isset($_SESSION[$this->nameVarLoginInfo]['idUser'])) {
            $this->responseError('No se encontró información del usuario en la sesión.');
        }

        return (int) $_SESSION[$this->nameVarLoginInfo]['idUser'];
    }
    /**
     * Envía una respuesta de error estándar en formato JSON y finaliza la ejecución.
     *
     * @param string $message Mensaje descriptivo del error.
     * @return void
     */
    private function responseError(string $message): void
    {
        toJson([
            'title'   => 'Ocurrió un error',
            'message' => $message,
            'type'    => 'error',
            'icon'    => 'error',
            'status'  => false,
        ]);
    }
    /**
     * Metodo que se encarga de actualizar la informacion del negocio
     */
    public function update()
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(8, "u", false)['status']) ? toJson(validate_permission_app(8, "u", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        $openBoxSwitch = 'No';
        if (isset($_POST['update_openBoxSwitch'])) {
            $openBoxSwitch = 'Si';
        }
        validateFields([
            'update_name',
            'update_slctTypeBusiness',
            'update_documentNumber',
            'update_email',
            'update_country',
            'update_telephone_prefix',
            'update_telephone',
            'update_city',
            'update_direction',
            'update_taxname',
            'update_tax',
        ]);
        $name = strClean($_POST['update_name']);
        $typebusinessId = strClean($_POST['update_slctTypeBusiness']);
        $documentNumber = strClean($_POST['update_documentNumber']);
        $email = strClean($_POST['update_email']);
        $country = strClean($_POST['update_country']);
        $telephonePrefix = strClean($_POST['update_telephone_prefix']);
        $phoneNumber = strClean($_POST['update_telephone']);
        $city = strClean($_POST['update_city']);
        $direction = strClean($_POST['update_direction']);
        $taxName = strClean($_POST['update_taxname']);
        $tax = strClean($_POST['update_tax']);
        $idUser = $this->getUserId();
        $logo = $_FILES['update_logoInput'];
        validateFieldsEmpty([
            'CORREO ELECTRONICO' => $email,
            'NOMBRE DEL NEGOCIO' => $name,
            'TIPO DE NEGOCIO' => $typebusinessId,
            'NÚMERO DE DOCUMENTO' => $documentNumber,
            'PAIS' => $country,
            'PREFIX DEL TELEFONO' => $telephonePrefix,
            'TELEFONO' => $phoneNumber,
            'NOMBRE DEL IMPUESTO' => $taxName,
            'IMPUESTO' => $tax,
        ]);
        //validamos que el nombre del negocio no sea mayor de 255 caracteres
        if (strlen($name) > 255) {
            $this->responseError('El nombre del negocio no puede tener más de 255 caracteres.');
        }
        //validamos que el nombre de la ciudad no sea mayor a 250 caracteres
        if (strlen($city) > 250) {
            $this->responseError('El nombre de la ciudad no puede tener más de 250 caracteres.');
        }
        //validamos que el nombre del pais no sea mayor a 100 caracteres
        if (strlen($country) > 100) {
            $this->responseError('El nombre del pais no puede tener más de 100 caracteres.');
        }
        //validamos que el numero del documento no sea mayor a 11 caracteres
        if (strlen($documentNumber) > 11) {
            $this->responseError('El numero del documento no puede tener más de 11 caracteres.');
        }
        //preparamos la ruta de almacenamiento del logo
        $urlFile = getRoute();
        verifyFolder($urlFile);
        $urlFile .= '/Business';
        verifyFolder($urlFile);
        $urlFile .= '/logo';
        verifyFolder($urlFile);
        //obtenemos el id del negocio
        $idBusiness = (int)$_SESSION[$this->nameVarBusiness]['idBusiness'];
        //consultamos a la base de datos la informacion del negocio
        $businessActually = $this->model->select_info_business($idBusiness);
        //validamos que exista la informacion del negocio
        if (empty($businessActually)) {
            $this->responseError('No se encontró la informacion del negocio.');
        }
        $logoname = '';
        //ahora validamos si tenemos un logo cargado
        if ($logo['name'] == '') {
            $logoname = $businessActually['logo'];
        } else {
            //eliminamos el logo anterior
            if ($businessActually['logo'] != '') {
                delFolder($urlFile, $businessActually['logo'], false);
            }
            //validamos si el archivo es un archivo valido
            if (isFile('image', $logo, ['png', 'jpg', 'jpeg'])) {
                $this->responseError('El archivo debe ser una imagen.');
            }
            //obtenemos la extension del archivo
            $extension = pathinfo($logo['name'], PATHINFO_EXTENSION);
            $logoname = $businessActually['idBusiness'] . '-' . time() . '.' . $extension;
            $sizefile = valConvert($logo['size'])['MB'];
            $urlFile .= '/' . $logoname;
            if ($sizefile > 2) {
                resizeAndCompressImage($logo['tmp_name'], $urlFile, 2);
            } else {
                move_uploaded_file($logo['tmp_name'], $urlFile);
            }
        }
        //preparamos el array para la actualizacion
        $data = [
            'idBusiness' => $idBusiness,
            'typebusiness_id' => $typebusinessId,
            'name' => $name,
            'direction' => $direction,
            'city' => $city,
            'document_number' => $documentNumber,
            'phone_number' => $phoneNumber,
            'country' => $country,
            'telephone_prefix' => $telephonePrefix,
            'email' => $email,
            'taxname' => $taxName,
            'tax' => $tax,
            'openBox' => $openBoxSwitch,
            'logo' => $logoname
        ];
        $responseUpdate = $this->model->updateBusiness($data);
        if ($responseUpdate) {
            toJson([
                'title'   => 'Negocio actualizado',
                'message' => 'El negocio ha sido actualizado con exito.',
                'type'    => 'success',
                'icon'    => 'success',
                'status'  => true,
            ]);
        } else {
            $this->responseError('No se pudo actualizar el negocio.');
        }
    }
    /**
     * Metodo que desactiva un negocio
     * @return void
     */
    public function delete_bussiness(): void
    {
        //VALIDACION DE PERMISOS
        (!validate_permission_app(8, "d", false)['status']) ? toJson(validate_permission_app(8, "d", false)) : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseError('Método de solicitud no permitido.');
        }
        isCsrf("", 1);
        validateFields(['id', 'name']);
        $idBusiness = (int)strClean($_POST['id']);
        $name = strClean($_POST['name']);
        validateFieldsEmpty([
            'ID DEL NEGOCIO' => $idBusiness,
            'NOMBRE DEL NEGOCIO' => $name,
        ]);
        //validamos que el id del negocio sea un numero
        if (!is_numeric($idBusiness)) {
            $this->responseError('El id del negocio debe ser un numero.');
        }
        //consultamos a la base de datos la informacion del negocio
        $businessActually = $this->model->select_info_business($idBusiness);
        //validamos que exista la informacion del negocio
        if (empty($businessActually)) {
            $this->responseError('No se encontró la informacion del negocio.');
        }
        //eliminamos el negocio
        $response = $this->model->disableBusiness($idBusiness);
        if ($response) {
            toJson([
                'title'   => 'Eliminación exitosa',
                'html' => <<<HTML
                        <p>El negocio <strong style="color: green;">$name</strong> ha sido eliminado con exito.</p>
                HTML,
                'type'    => 'success',
                'icon'    => 'success',
                'status'  => true,
                'url'     => base_url() . '/pos/LogOut',
                'timer'   => 1500,
            ]);
        } else {
            $this->responseError('No se pudo eliminar el negocio.');
        }
    }
}
