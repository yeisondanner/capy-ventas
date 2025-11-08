<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Main CSS-->
  <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/main.css">
  <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/loader.css">
  <!-- Font-icon css-->
  <link rel="stylesheet" type="text/css"
    href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- CSS de la las alertas -->
  <link rel="stylesheet" type="text/css" href="<?= media() ?>/css/libraries/toastr.min.css">
  <link rel="shortcut icon"
    href="<?= base_url() ?>/loadfile/icon?f=<?= (getSystemInfo()) ? getSystemInfo()["c_logo"] : null; ?>"
    type="image/x-icon">
  <link rel="stylesheet"
    href="<?= media() ?>/css/app/<?= strtolower($data["page_container"]) ?>/style_<?= $data["page_js_css"] ?>.css">
  <style>
    :root {
      --color-primary:
        <?= (getSystemInfo()) ? getSystemInfo()["c_color_primary"] : "#4da8da"; ?>;
      --color-secondary:
        <?= (getSystemInfo()) ?  getSystemInfo()["c_color_secondary"] : "#004e89"; ?>;
    }
  </style>
  <title><?= $data["page_title"] ?></title>
</head>

<body>
  <div id="loaderOverlay">
    <?= getSystemInfo()["c_contentLoader"] ?>
    <h5> <?= getSystemInfo()["c_textLoader"] ?></h5>
  </div>

  <section class="material-half-bg">
    <div class="cover"></div>
  </section>

  <section class="login-content">
    <div class="logo">
      <h1><?= (getSystemInfo()) ? getSystemInfo()["c_name"] : getSystemName(); ?></h1>
    </div>

    <div class="login-box">
      <!-- Formulario de login -->
      <form class="login-form" id="formLogin" autocomplete="off">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>Iniciar Sesión</h3>

        <div class="form-group">
          <label class="control-label" for="txtUser">Usuario o Email</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                <i class="fa fa-user"></i>
              </span>
            </div>
            <input class="form-control" type="text" id="txtUser" name="txtUser" placeholder="Ingrese su usuario o Email"
              autofocus required minlength="3">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label" for="txtPassword">Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" name="txtPassword" id="txtPassword"
              placeholder="Ingrese su contraseña" required minlength="8">
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="button" id="togglePassword">
                <i class="fa fa-eye" id="iconoPassword"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="utility">
            <div class="animated-checkbox">
              <label>
                <input type="checkbox" id="chbxRemember" name="chbxRemember">
                <span class="label-text">Recuérdame</span>
              </label>
            </div>
            <p class="semibold-text mb-2">
              <a href="#" data-toggle="flip">¿Olvidaste tu contraseña?</a>
            </p>
          </div>
        </div>

        <div class="form-group btn-container">
          <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>Ingresar</button>
        </div>
      </form>

      <!-- Formulario de recuperar contraseña -->
      <form class="forget-form" autocomplete="off" id="formReset">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>¿Olvidaste tu contraseña?</h3>
        <div class="form-group">
          <label class="control-label" for="txtEmail">EMAIL</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                <i class="fa fa-envelope-o"></i>
              </span>
            </div>
            <input class="form-control" type="text" placeholder="Correo electronico" id="txtEmail" name="txtEmail"
              autocomplete="off">
          </div>
        </div>
        <div class="form-group btn-container">
          <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>Reiniciar</button>
        </div>
        <div class="form-group mt-3">
          <p class="semibold-text mb-0">
            <a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Ir al Login</a>
          </p>
        </div>
      </form>
    </div>
  </section>

  <!-- Essential javascripts for application to work-->
  <script src="<?= media() ?>/js/libraries/jquery-3.7.1.min.js"></script>
  <script src="<?= media() ?>/js/libraries/popper.min.js"></script>
  <script src="<?= media() ?>/js/libraries/bootstrap.min.js"></script>
  <script src="<?= media() ?>/js/libraries/main.js"></script>
  <!-- The javascript plugin to display page loading on top-->
  <script src="<?= media() ?>/js/libraries/plugins/pace.min.js"></script>
  <!--Libreria de sweetalert-->
  <script type="text/javascript" src="<?= media() ?>/js/libraries/toastr.min.js"></script>
  <script type="text/javascript">
    const base_url = "<?= base_url(); ?>/im";
  </script>
  <script
    src="<?= media() ?>/js/app/<?= strtolower($data["page_container"]) ?>/functions_<?= $data["page_js_css"] ?>.js"></script>
</body>

</html>