<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/POS/main.css?<?= versionSystem() ?>">
    <link rel="shortcut icon" href="<?= media() ?>/head-capibara.png?<?= versionSystem() ?>" type="image/x-icon">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="<?= media() ?>/css/libraries/POS/bootstrap-icons.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/Admin/toastr.min.css?<?= versionSystem() ?>">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/app/POS/account/style_account.css?<?= versionSystem() ?>">
    <title>Capy Login</title>
    <script>
        const base_url = "<?= base_url() ?>/pos";
        const currency = "<?= getCurrency() ?>";
    </script>
</head>

<body>
    <section class="material-half-bg">
        <div class="cover"></div>
    </section>

    <!-- PASO 1 -->
    <section id="setCode" class="d-flex justify-content-center align-items-center min-vh-100 p-3">
        <div class="card shadow-lg border-0 overflow-hidden w-100 rounded-3" style="max-width: 1000px;">
            <div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">4</div>
                                    <span>Tu negocio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-5">
                    <div class="text-center mb-4 mt-lg-5">
                        <div class="login-head">
                            <img src="<?= media() ?>/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Regístrate para comenzar</h2>
                    </div>
                    <form class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu correo electrónico</label>
                            <div class="text-muted small mb-2">Te enviaremos un código de verificación por <span class="fw-bold">correo electrónico</span></div>

                            <div class="input-group mb-3">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-envelope-at"></i>
                                </span>
                                <input type="email" class="form-control" placeholder="Escribe tu correo electrónico" id="email">
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="accept_terms">
                            <label class="form-check-label small text-muted" for="termsCheck">
                                He leído y acepto los <a href="#" class="text-dark fw-bold">Términos y Condiciones</a>, y autorizo expresamente el tratamiento de mis datos personales conforme a la <a href="#" class="text-dark fw-bold">Política de Privacidad</a>.
                            </label>
                        </div>

                        <button id="btnSendCode" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Enviar código</button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta en <strong>Capy Ventas</strong>?</span>
                        <a href="./login" class="text-primary fw-bold text-decoration-none">Iniciar sesión</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- PASO 2 -->
    <section id="verifyCode" class="d-flex justify-content-center align-items-center min-vh-100 p-3 d-none">
        <div class="card shadow-lg border-0 overflow-hidden w-100 rounded-3" style="max-width: 1000px;">
            <div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">4</div>
                                    <span>Tu negocio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-5">
                    <div class="text-center mb-4 mt-lg-5">
                        <div class="login-head">
                            <img src="<?= media() ?>/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Verificación del código</h2>
                    </div>
                    <form class="account-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ingresa tu código de verificación</label>
                            <div class="text-muted small mb-2">El código de verificación se envío a tu <span class="fw-bold">correo electrónico</span></div>

                            <div class="input-group mb-3">
                                <span class="input-group-text text-muted">
                                    <i class="bi bi-envelope-check-fill"></i>
                                </span>
                                <input id="code" type="input" class="form-control" placeholder="Escribe tu código de verificación">
                            </div>
                        </div>

                        <!-- <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsCheck">
                            <label class="form-check-label small text-muted" for="termsCheck">
                                He leído y acepto los <a href="#" class="text-dark fw-bold">Términos y Condiciones</a>, y autorizo expresamente el tratamiento de mis datos personales conforme a la <a href="#" class="text-dark fw-bold">Política de Privacidad</a>.
                            </label>
                        </div> -->

                        <button id="btnVerifyCode" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Verificar código</button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta en <strong>Capy Ventas</strong>?</span>
                        <a href="./login" class="text-primary fw-bold text-decoration-none">Iniciar sesión</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- PASO 3 -->
    <section id="createAccount" class="d-flex justify-content-center align-items-center min-vh-100 p-3 d-none">
        <div class="card shadow-lg border-0 overflow-hidden w-100 rounded-3" style="max-width: 1000px;">
            <div class="row g-0 account-content">
                <div class="d-none d-md-block col-md-5 bg-warning-subtle p-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-5 d-flex align-items-center gap-2">
                            <div class="bg-info text-white rounded py-1 px-2"><i class="bi bi-patch-check"></i></div>
                            <h3 class="fw-bold m-0 text-dark">Capy Ventas</h3>
                        </div>
                        <div class="stepper-container ms-2">
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">1</div>
                                    <span>Tu correo electrónico</span>
                                </div>
                                <!-- <div class="step-line"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">2</div>
                                    <span>Verificación</span>
                                </div>
                                <!-- <div class="step-line" style="top:-10px"></div> -->
                            </div>
                            <div class="d-flex align-items-center mb-2 position-relative">
                                <div class="step-active d-flex align-items-center gap-3">
                                    <div class="step-circle">3</div>
                                    <span>Tu cuenta</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center gap-3 text-muted">
                                    <div class="step-circle border-0">4</div>
                                    <span>Tu negocio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="fw-bold text-dark">Al registrarte en <span class="text-primary">Capy Ventas</span> podrás:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Administrar la contabilidad de tu negocio.</span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Cargar fácilmente todo tu inventario y llevar control de stock.</span>
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-dark fs-5"></i>
                                <span class="small">Gestionar tus clientes y proveedores.</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-7 bg-white p-4">
                    <!-- <div class="text-center mb-2 mt-lg-2">
                        <div class="login-head">
                            <img src="<?= media() ?>/carpincho.png" alt="">
                        </div>
                        <h2 class="fw-bold">Tu cuenta</h2>
                    </div> -->

                    <form class="account-form">
                        <h2 class="fw-bold text-center mb-3">Tu cuenta</h2>
                        <!-- nombres y apellidos -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombres: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input type="input" class="form-control" placeholder="Escriba sus nombres">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Apellidos: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input type="input" class="form-control" placeholder="Escriba sus apellidos">
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- email -->
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-envelope-fill"></i>
                                        </span>
                                        <input type="email" class="form-control" placeholder="Escriba su correo electrónico">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- fecha de nacimiento y pais -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Fecha de Nacimiento: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-calendar-event-fill"></i>
                                        </span>
                                        <input type="date" class="form-control" placeholder="Escriba sus apellidos">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">País: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-globe-americas-fill"></i>
                                        </span>
                                        <input type="input" class="form-control" placeholder="Escriba su país">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- prefijo y numero de telefono -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Prefijo Tel.: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-telephone-fill"></i>
                                        </span>
                                        <input type="input" class="form-control" placeholder="+51">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Número de Teléfono: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-phone-fill"></i>
                                        </span>
                                        <input type="number" class="form-control" placeholder="987654321">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr>

                        <!-- prefijo y numero de telefono -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Usuario: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </span>
                                        <input type="input" class="form-control" placeholder="Ingrese su usuario">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Contraseña: </label>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text text-muted">
                                            <i class="bi bi-file-lock2-fill"></i>
                                        </span>
                                        <input type="password" class="form-control" placeholder="Ingrese su contraseña">
                                    </div>
                                </div>

                            </div>
                        </div>


                        <button id="btnCreateAccount" type="button" class="btn btn-dark w-100 py-3 rounded-5 fw-bold">Registrarse</button>
                    </form>

                    

                </div>
            </div>
        </div>
    </section>

    <a target="_blank" href="https://wa.me/51910367611?text=Hola,%20tengo%20problemas%20con%20el%20registro%20en%20Capy%20Ventas" class="btn-whatsapp-float">
        <i class="bi bi-whatsapp me-2"></i> ¿Tienes problemas con el registro?
    </a>
    <!-- TODO: Essential javascripts for application to work-->
    <script src="<?= media() ?>/js/libraries/POS/jquery-3.7.0.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/popper.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/bootstrap.min.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/SweerAlert2.js?<?= versionSystem() ?>"></script>
    <script src="<?= media() ?>/js/libraries/POS/main.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/account/functions_account_api.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/account/functions_account.js?<?= versionSystem() ?>"></script>
    <script type="module" src="<?= media() ?>/js/app/POS/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js?<?= versionSystem() ?>"></script>
</body>

</html>