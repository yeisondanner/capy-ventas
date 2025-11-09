<?= headerAdmin($data) ?>

<main class="app-content">
  <div class="page-error tile space-404">

    <!-- LUNA -->
    <div class="moon"></div>
    <div class="moon__crater moon__crater1"></div>
    <div class="moon__crater moon__crater2"></div>
    <div class="moon__crater moon__crater3"></div>

    <!-- ESTRELLAS -->
    <div class="star star1"></div>
    <div class="star star2"></div>
    <div class="star star3"></div>
    <div class="star star4"></div>
    <div class="star star5"></div>

    <!-- MENSAJE DE ERROR -->
    <div class="error">
      <div class="error__title">404</div>
      <div class="error__subtitle">Controlador no encontrado</div>
      <div class="error__description">
        El controlador que estás intentando acceder no existe o fue eliminado del sistema.
      </div>
      <button class="error__button error__button--active"
        onclick="location.href='<?= base_url(); ?>';">
        IR AL INICIO
      </button>
      <button class="error__button"
        onclick="javascript:window.history.back();">
        REGRESAR
      </button>
    </div>

    <!-- ASTRONAUTA -->
    <div class="astronaut">
      <div class="astronaut__backpack"></div>
      <div class="astronaut__body"></div>
      <div class="astronaut__body__chest"></div>
      <div class="astronaut__arm-left1"></div>
      <div class="astronaut__arm-left2"></div>
      <div class="astronaut__arm-right1"></div>
      <div class="astronaut__arm-right2"></div>
      <div class="astronaut__arm-thumb-left"></div>
      <div class="astronaut__arm-thumb-right"></div>
      <div class="astronaut__leg-left"></div>
      <div class="astronaut__leg-right"></div>
      <div class="astronaut__foot-left"></div>
      <div class="astronaut__foot-right"></div>
      <div class="astronaut__wrist-left"></div>
      <div class="astronaut__wrist-right"></div>

      <div class="astronaut__cord">
        <canvas id="cord" height="500" width="500"></canvas>
      </div>

      <div class="astronaut__head">
        <img src="<?= base_url(); ?>Assets/head-capibara.png"
          alt="Capibara astronauta"
          class="astronaut__head-capibara">
      </div>
    </div>

  </div>
</main>

<link rel="stylesheet" href="<?= base_url(); ?>Assets/css/app/Admin/errors/style_404.css">
<script src="<?= base_url(); ?>Assets/js/app/Admin/errors/functions_404.js"></script>

<?= footerAdmin($data) ?>
