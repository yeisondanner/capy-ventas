<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <title>Error 404 • Conos + Capibara</title>

  <!-- Bootstrap 4.0 -->
  <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous"/>

  <link rel="stylesheet" href="styles.css"/>
</head>
<body>

  <main class="vh-100 vw-100 d-flex align-items-center justify-content-center bg-404">
    <section class="text-center">
      <!-- Canvas -->
      <div class="stage position-relative mx-auto">
        <!-- SVG base -->
        <svg class="illustration" viewBox="0 0 400 400" aria-hidden="true">
          <!-- Carteles apilados -->
          <rect x="55" y="25" rx="8" ry="8" width="290" height="150" fill="#f3f3f3"/>
          <rect x="65" y="35" rx="8" ry="8" width="270" height="150" fill="#ffffff"/>
          <text x="200" y="80" text-anchor="middle" font-family="Poppins,Arial,sans-serif"
                font-size="26" fill="#bdbdbd" font-weight="600">ERROR</text>
          <text x="200" y="140" text-anchor="middle" font-family="Poppins,Arial,sans-serif"
                font-size="96" fill="#757575" font-weight="800">404</text>

          <!-- Grieta -->
          <path d="M200 180 l-6 20 12 14 -8 16 10 12 -14 14 15 20"
                fill="none" stroke="#d0d0d0" stroke-width="4" stroke-linejoin="round"/>

          <!-- Hoyo -->
          <ellipse cx="200" cy="285" rx="78" ry="22" fill="#2a2a2a" opacity=".95"/>
          <ellipse cx="200" cy="280" rx="58" ry="11" fill="#3a3a3a" opacity=".55"/>
        </svg>

        <!-- 📸 Conos reales (pon tus imágenes) -->
        <img class="cone cone-left"  src="img/cone-left.png"  alt="Cono de tráfico izquierdo"/>
        <img class="cone cone-right" src="img/cone-right.png" alt="Cono de tráfico derecho"/>

        <!-- 🦫 Capibara (elige 1 de 2):
             A) Imagen real (PNG/WebP) → coloca tu archivo y se verá.
             B) Deja el src vacío y se usará el SVG integrado como fallback. -->
        <img id="capyImg" class="capy" src="" alt="Capibara"/>

        <!-- Fallback SVG del capibara (se oculta si hay imagen) -->
        <svg id="capySvg" class="capy capy-svg" viewBox="0 0 320 200" aria-hidden="true">
          <!-- cuerpo -->
          <ellipse cx="160" cy="120" rx="120" ry="70" fill="#b78054"/>
          <!-- barriga -->
          <ellipse cx="180" cy="130" rx="70" ry="45" fill="#c69166"/>
          <!-- cabeza -->
          <ellipse cx="230" cy="95" rx="55" ry="45" fill="#b78054"/>
          <!-- oreja -->
          <ellipse cx="255" cy="70" rx="12" ry="10" fill="#895a35"/>
          <!-- hocico -->
          <ellipse cx="260" cy="105" rx="18" ry="14" fill="#a87146"/>
          <!-- ojo -->
          <circle cx="240" cy="95" r="5" fill="#2b2b2b"/>
          <!-- patitas -->
          <rect x="105" y="165" width="16" height="18" rx="4" fill="#895a35"/>
          <rect x="185" y="165" width="16" height="18" rx="4" fill="#895a35"/>
          <!-- sombra propia -->
          <ellipse cx="160" cy="188" rx="90" ry="12" fill="rgba(0,0,0,.18)"/>
        </svg>

        <!-- Sombra bajo cada cono (se mantiene aunque cambies imagen) -->
        <div class="shadow-oval s-left"></div>
        <div class="shadow-oval s-right"></div>
      </div>

      <h5 class="oops mt-4 mb-1">OOPS!</h5>
      <p class="text-muted mb-4">La página que solicitaste no pudo ser encontrada.</p>

      <button id="retryBtn" class="btn btn-warning btn-lg rounded-pill px-5">RETRY</button>
      <small class="text-muted d-block mt-3">Demo 404 • Bootstrap 4</small>
    </section>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
          integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
          crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
          integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
          crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
          integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
          crossorigin="anonymous"></script>

  <script src="app.js"></script>
</body>
</html>
