<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>
    <?= isset($data['page_title']) ? $data['page_title'] : 'Generador de Códigos QR Gratis | Capy Ventas'?>
  </title>

  <!-- SEO Meta Tags -->
  <meta name="description"
    content="<?= isset($data['page_description']) ? $data['page_description'] : 'Crea códigos QR personalizados gratis para páginas web, textos, enlaces, contactos y más con el generador de QR de Capy Ventas. Fácil, rápido y descargable al instante.'?>">
  <meta name="keywords"
    content="generador de qr, crear codigo qr, qr gratis, qr online, descargar qr, qr para pagina web, qr de texto, qr capy ventas, generador qr gratis, qr personalizado">
  <meta name="author" content="Capy Ventas">
  <meta name="robots" content="index, follow">
  <link rel="shortcut icon"
    href="<?= function_exists('base_url') ? base_url() : 'https://capyventas.shaday-pe.com'?>/Assets/capysm.png"
    type="image/x-icon">

  <!-- Canonical URL -->
  <link rel="canonical"
    href="<?= function_exists('base_url') ? base_url() : 'https://capyventas.shaday-pe.com'?>/home/qr">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url"
    content="<?= function_exists('base_url') ? base_url() : 'https://capyventas.shaday-pe.com'?>/home/qr">
  <meta property="og:title"
    content="<?= isset($data['page_title']) ? $data['page_title'] : 'Generador de Códigos QR Gratis | Capy Ventas'?>">
  <meta property="og:description"
    content="<?= isset($data['page_description']) ? $data['page_description'] : 'Crea códigos QR personalizados gratis para páginas web, textos y más. Fácil, rápido y descargable al instante.'?>">
  <meta property="og:image"
    content="<?= function_exists('base_url') ? base_url() : 'https://capyventas.shaday-pe.com'?>/Assets/capylg.png">
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3076714141618004"
    crossorigin="anonymous"></script>
  <style>
    :root {
      --color-primary: #4369F0;
      --color-secondary: #FDC346;
      --color-accent: #FDC346;
      --color-dark: #142235;
      --color-light: #f5f7fb;
      --color-muted: #6c7c8f;
      --color-card: #ffffff;
      --font-sans: "Poppins", "Segoe UI", sans-serif;
      --shadow-soft: 0 18px 40px rgba(19, 47, 76, 0.15);
      --border-radius: 18px;
      --page-padding: 6%;
    }

    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: var(--font-sans);
      color: var(--color-dark);
      background: linear-gradient(135deg, rgba(35, 67, 106, 0.06), rgba(27, 191, 157, 0.04));
      min-height: 100vh;
      width: 100%;
      overflow-x: hidden;
    }

    html {
      scroll-behavior: smooth;
    }

    header {
      width: 100% !important;
      padding: 15px var(--page-padding);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      background: rgba(245, 247, 251, 0.92);
      backdrop-filter: blur(14px);
      z-index: 30;
      border-bottom: 1px solid rgba(35, 67, 106, 0.08);
    }

    .brand-container {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .logo-img {
      height: 50px;
      width: 50px;
      border-radius: 50%;
      object-fit: cover;
      display: block;
      transition: transform 0.3s ease, filter 0.3s ease;
    }

    .logo-img:hover {
      transform: scale(1.05);
    }

    .brand-name {
      font-weight: 700;
      font-size: 1.35rem;
      color: var(--color-primary);
      letter-spacing: 0.05em;
    }

    nav {
      display: flex;
      gap: 28px;
    }

    nav a {
      text-decoration: none;
      font-size: 0.95rem;
      color: var(--color-dark);
      font-weight: 500;
      transition: color 0.3s ease;
      position: relative;
    }

    nav a:hover,
    nav a.active {
      color: var(--color-secondary);
    }

    nav a.active::after {
      content: "";
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 100%;
      height: 2px;
      background: var(--color-secondary);
      border-radius: 99px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 24px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      border: none;
    }

    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-primary {
      background: linear-gradient(120deg, var(--color-secondary), #24d7b1);
      color: #fff;
      box-shadow: 0 14px 30px rgba(27, 191, 157, 0.35);
    }

    .btn-primary:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 18px 40px rgba(27, 191, 157, 0.45);
    }

    .btn-outline {
      border: 2px solid var(--color-primary);
      color: var(--color-primary);
      background: transparent;
    }

    .btn-outline:hover:not(:disabled) {
      transform: translateY(-2px);
      color: var(--color-card);
      background: var(--color-primary);
      box-shadow: 0 16px 32px rgba(35, 67, 106, 0.32);
    }

    /* Menu Toggle Mobile */
    .menu-toggle {
      display: none;
      align-items: center;
      gap: 10px;
      padding: 10px 18px;
      border-radius: 999px;
      border: 2px solid var(--color-primary);
      background: rgba(35, 67, 106, 0.06);
      color: var(--color-primary);
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
    }

    .menu-icon {
      display: grid;
      gap: 5px;
    }

    .menu-icon span {
      width: 18px;
      height: 2px;
      display: block;
      background: currentColor;
      border-radius: 999px;
      transition: transform 0.3s ease;
    }

    .menu-toggle.active .menu-icon span:nth-child(1) {
      transform: translateY(7px) rotate(45deg);
    }

    .menu-toggle.active .menu-icon span:nth-child(2) {
      opacity: 0;
    }

    .menu-toggle.active .menu-icon span:nth-child(3) {
      transform: translateY(-7px) rotate(-45deg);
    }

    main {
      width: 100%;
      padding: 40px var(--page-padding) 120px;
    }

    .section-title {
      font-size: clamp(2rem, 3vw, 2.6rem);
      font-weight: 700;
      color: var(--color-primary);
      margin-bottom: 18px;
    }

    .qr-hero {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: start;
      max-width: 1250px;
      margin: 0 auto;
    }

    .qr-hero>div {
      min-width: 0;
    }

    .qr-form {
      background: var(--color-card);
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--shadow-soft);
      border: 1px solid rgba(35, 67, 106, 0.08);
      margin-top: 24px;
    }

    /* Tabs */
    .qr-tabs {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 12px;
      margin-bottom: 24px;
      scrollbar-width: thin;
      -webkit-overflow-scrolling: touch;
    }

    .qr-tabs::-webkit-scrollbar {
      height: 6px;
    }

    .qr-tabs::-webkit-scrollbar-thumb {
      background-color: rgba(35, 67, 106, 0.15);
      border-radius: 10px;
    }

    .qr-tab {
      padding: 12px 20px;
      border-radius: 14px;
      background: rgba(35, 67, 106, 0.04);
      color: var(--color-muted);
      border: 1px solid transparent;
      cursor: pointer;
      white-space: nowrap;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: inherit;
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .qr-tab:hover {
      background: rgba(35, 67, 106, 0.08);
      color: var(--color-primary);
    }

    .qr-tab.active {
      background: var(--color-primary);
      color: #fff;
      box-shadow: 0 8px 20px rgba(67, 105, 240, 0.25);
    }

    .qr-tab-content {
      display: none;
      animation: fadeIn 0.3s ease;
    }

    .qr-tab-content.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-group {
      margin-bottom: 24px;
      text-align: left;
    }

    .form-label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: var(--color-primary);
      font-size: 0.95rem;
    }

    .form-input {
      width: 100%;
      padding: 14px 18px;
      border: 1px solid rgba(35, 67, 106, 0.1);
      border-radius: 14px;
      font-family: inherit;
      font-size: 1rem;
      color: var(--color-dark);
      transition: all 0.3s ease;
      background: var(--color-light);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--color-secondary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(27, 191, 157, 0.15);
      transform: translateY(-1px);
    }

    textarea.form-input {
      resize: vertical;
      min-height: 100px;
    }

    .qr-preview-card {
      background: linear-gradient(160deg, rgba(255, 255, 255, 0.95), rgba(235, 243, 250, 0.88));
      border-radius: var(--border-radius);
      padding: 40px;
      border: 1px solid rgba(35, 67, 106, 0.08);
      box-shadow: 0 20px 48px rgba(17, 40, 66, 0.18);
      display: flex;
      flex-direction: column;
      align-items: center;
      position: sticky;
      top: 100px;
    }

    #qrcode-container {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 300px;
      width: 100%;
      margin-bottom: 24px;
    }

    #qrcode-container img,
    #qrcode-container canvas {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }

    /* Checkbox list for wifi */
    .checkbox-container {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      user-select: none;
    }

    .checkbox-container input {
      width: 18px;
      height: 18px;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 26px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .feature-card {
      background: var(--color-card);
      padding: 28px;
      border-radius: var(--border-radius);
      border: 1px solid rgba(35, 67, 106, 0.08);
      box-shadow: var(--shadow-soft);
      display: grid;
      gap: 18px;
    }

    .feature-icon {
      width: 58px;
      height: 58px;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(35, 67, 106, 0.12), rgba(27, 191, 157, 0.22));
      display: grid;
      place-items: center;
      color: var(--color-primary);
      font-weight: 700;
      font-size: 1.2rem;
    }

    footer {
      text-align: center;
      padding: 24px;
      color: var(--color-muted);
      font-size: 0.9rem;
      border-top: 1px solid rgba(35, 67, 106, 0.08);
      background: rgba(245, 247, 251, 0.92);
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 900px) {
      header {
        width: 100%;
        flex-wrap: wrap;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 15px var(--page-padding);
      }

      .menu-toggle {
        display: inline-flex;
        margin-left: auto;
      }

      nav,
      .header-actions {
        width: 100%;
      }

      header:not(.is-menu-open) nav,
      header:not(.is-menu-open) .header-actions {
        display: none;
      }

      header.is-menu-open {
        align-items: stretch;
      }

      header.is-menu-open nav {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
        padding: 12px 0;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(19, 47, 76, 0.16);
      }

      header.is-menu-open nav a {
        width: 100%;
        padding: 10px 16px;
        text-align: center;
      }

      header.is-menu-open .header-actions {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(19, 47, 76, 0.16);
      }

      header.is-menu-open .header-actions .btn {
        width: 100%;
        justify-content: center;
      }

      .qr-hero {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .qr-preview-card {
        position: static;
        margin-top: 0;
      }
    }

    @media (max-width: 768px) {
      main {
        padding: 20px var(--page-padding) 80px;
      }

      .qr-form {
        padding: 24px;
      }

      .qr-preview-card {
        padding: 24px;
      }

      .section-title {
        text-align: center !important;
      }

      .qr-content>p {
        text-align: center;
      }

      .form-row {
        flex-direction: column !important;
        gap: 16px !important;
      }

      .form-row>div {
        width: 100% !important;
      }
    }

    @media (max-width: 520px) {
      header {
        padding: 12px var(--page-padding);
      }

      .qr-hero {
        gap: 32px;
      }

      .section-title {
        font-size: clamp(1.8rem, 8vw, 2.2rem);
      }

      .qr-form {
        padding: 20px;
      }

      .qr-preview-card {
        padding: 20px;
      }

      .features-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <header>
    <a href="<?= function_exists('base_url') ? base_url() : './'?>" class="brand-container">
      <img src="<?= function_exists('base_url') ? base_url() : 'https://capyventas.shaday-pe.com'?>/Assets/capysm.png"
        alt="Capy Ventas Logo" class="logo-img" />
      <span class="brand-name">Capy Ventas</span>
    </a>

    <!-- Menu Toggle para Móviles -->
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
      <span class="menu-icon" aria-hidden="true">
        <span></span><span></span><span></span>
      </span>
      <span class="menu-label" style="margin-left:5px;">Menú</span>
    </button>

    <nav id="primary-navigation">
      <a href="<?= function_exists('base_url') ? base_url() : './'?>">Inicio</a>
      <a href="<?= function_exists('base_url') ? base_url() : './'?>/home#funcionalidades">Funcionalidades</a>
      <a href="<?= function_exists('base_url') ? base_url() : './'?>/home#herramientas">Herramientas</a>
      <a href="<?= function_exists('base_url') ? base_url() : './'?>/home#planes">Planes</a>
      <a href="#" class="active">Generador QR</a>
    </nav>

    <div class="header-actions">
      <a href="<?= function_exists('base_url') ? base_url() : './'?>/pos/login" class="btn btn-outline"
        style="padding: 10px 20px; font-size: 0.9rem;">
        Soy cliente
      </a>
      <a href="<?= function_exists('base_url') ? base_url() : './'?>/pos/account" class="btn btn-primary"
        style="padding: 10px 20px; font-size: 0.9rem;">
        Inicia gratis <i class="bi bi-box-arrow-in-right" style="margin-left: 6px;"></i>
      </a>
    </div>
  </header>

  <main>
    <div class="qr-hero">
      <div class="qr-content" data-aos="fade-right">
        <h1 class="section-title" style="text-align: left;">Generador de Códigos QR <span
            style="color: var(--color-secondary);">Múltiple</span></h1>
        <p style="color: var(--color-muted); font-size: 1.1rem; margin-bottom: 24px; line-height: 1.6;">
          Crea todo tipo de códigos QR (enlaces, WiFi, VCard, WhatsApp y más). 100% responsivo, gratis y optimizado para
          tus campañas y negocios.
        </p>

        <div class="qr-form">

          <div class="qr-tabs">
            <button class="qr-tab active" data-tab="url"><i class="bi bi-link-45deg"></i> URL</button>
            <button class="qr-tab" data-tab="text"><i class="bi bi-fonts"></i> Texto</button>
            <button class="qr-tab" data-tab="whatsapp"><i class="bi bi-whatsapp"></i> WhatsApp</button>
            <button class="qr-tab" data-tab="wifi"><i class="bi bi-wifi"></i> WiFi</button>
            <button class="qr-tab" data-tab="vcard"><i class="bi bi-person-vcard"></i> vCard</button>
            <button class="qr-tab" data-tab="email"><i class="bi bi-envelope"></i> Email</button>
          </div>

          <!-- URL Tab -->
          <div class="qr-tab-content active" id="tab-url">
            <div class="form-group">
              <label class="form-label">URL de la página web</label>
              <input type="url" id="qr-input-url" class="form-input" placeholder="Ej. https://mitienda.com" required>
            </div>
          </div>

          <!-- Texto Tab -->
          <div class="qr-tab-content" id="tab-text">
            <div class="form-group">
              <label class="form-label">Texto libre</label>
              <textarea id="qr-input-text" class="form-input" placeholder="Escribe el mensaje o texto aquí..."
                rows="3"></textarea>
            </div>
          </div>

          <!-- WhatsApp Tab -->
          <div class="qr-tab-content" id="tab-whatsapp">
            <div class="form-row" style="display: flex; gap: 16px;">
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Número (con código país)</label>
                <input type="tel" id="qr-input-wa-num" class="form-input" placeholder="Ej. 51910367611">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Mensaje predeterminado</label>
              <textarea id="qr-input-wa-msg" class="form-input" placeholder="Hola, me gustaría más información..."
                rows="2"></textarea>
            </div>
          </div>

          <!-- WiFi Tab -->
          <div class="qr-tab-content" id="tab-wifi">
            <div class="form-group">
              <label class="form-label">Nombre de la red (SSID)</label>
              <input type="text" id="qr-input-wifi-ssid" class="form-input" placeholder="Nombre de tu WiFi">
            </div>
            <div class="form-row" style="display: flex; gap: 16px;">
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Contraseña</label>
                <input type="password" id="qr-input-wifi-pass" class="form-input" placeholder="Contraseña">
              </div>
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Seguridad</label>
                <select id="qr-input-wifi-type" class="form-input">
                  <option value="WPA">WPA/WPA2</option>
                  <option value="WEP">WEP</option>
                  <option value="nopass">Sin protección</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="checkbox-container text-muted">
                <input type="checkbox" id="qr-input-wifi-hidden"> Red oculta
              </label>
            </div>
          </div>

          <!-- vCard Tab -->
          <div class="qr-tab-content" id="tab-vcard">
            <div class="form-row" style="display: flex; gap: 16px;">
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Nombre(s)</label>
                <input type="text" id="qr-input-vc-name" class="form-input" placeholder="Juan">
              </div>
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Apellidos</label>
                <input type="text" id="qr-input-vc-last" class="form-input" placeholder="Pérez">
              </div>
            </div>
            <div class="form-row" style="display: flex; gap: 16px;">
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Teléfono</label>
                <input type="tel" id="qr-input-vc-phone" class="form-input" placeholder="+123456789">
              </div>
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Correo</label>
                <input type="email" id="qr-input-vc-email" class="form-input" placeholder="juan@correo.com">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Empresa</label>
              <input type="text" id="qr-input-vc-company" class="form-input" placeholder="Mi Empresa S.A">
            </div>
          </div>

          <!-- Email Tab -->
          <div class="qr-tab-content" id="tab-email">
            <div class="form-group">
              <label class="form-label">Destinatario</label>
              <input type="email" id="qr-input-em-address" class="form-input" placeholder="contacto@empresa.com">
            </div>
            <div class="form-group">
              <label class="form-label">Asunto</label>
              <input type="text" id="qr-input-em-sub" class="form-input" placeholder="Consulta sobre servicios">
            </div>
            <div class="form-group">
              <label class="form-label">Cuerpo del correo</label>
              <textarea id="qr-input-em-body" class="form-input" placeholder="Escribe el mensaje..."
                rows="3"></textarea>
            </div>
          </div>

          <hr style="border: 0; border-top: 1px solid rgba(35, 67, 106, 0.08); margin: 32px 0;">

          <h4 style="font-size: 1.1rem; color: var(--color-primary); margin-bottom: 16px;">Personalización y Estilo</h4>

          <div class="form-row" style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 120px;">
              <label class="form-label" for="qr-color">Color del QR</label>
              <input type="color" id="qr-color" class="form-input" value="#142235"
                style="padding: 4px; height: 50px; cursor: pointer;">
            </div>
            <div class="form-group" style="flex: 1; min-width: 120px;">
              <label class="form-label" for="qr-bg">Color de Fondo</label>
              <input type="color" id="qr-bg" class="form-input" value="#ffffff"
                style="padding: 4px; height: 50px; cursor: pointer;">
            </div>
            <div class="form-group" style="flex: 1.5; min-width: 150px;">
              <label class="form-label" for="qr-size">Calidad / Tamaño</label>
              <select id="qr-size" class="form-input" style="cursor: pointer; height: 50px;">
                <option value="300">Normal (300px)</option>
                <option value="600" selected>Media (600px)</option>
                <option value="900">Alta (900px)</option>
                <option value="1200">Print HD (1200px)</option>
              </select>
            </div>
          </div>

          <button id="btn-generate" class="btn btn-primary"
            style="width: 100%; justify-content: center; font-size: 1.1rem; padding: 16px; margin-top: 10px;">
            <i class="bi bi-qr-code" style="margin-right: 8px;"></i> Crear mi Código QR
          </button>
        </div>
      </div>

      <div class="qr-preview-card" data-aos="fade-left">
        <h3 style="color: var(--color-primary); margin-bottom: 16px; text-align: center; font-size: 1.5rem;">Visualizar
          QR</h3>
        <p style="color: var(--color-muted); text-align: center; margin-bottom: 24px; font-size: 0.95rem;">Aquí
          aparecerá tu código generado listo para su uso.</p>

        <div id="qrcode-container">
          <div id="qr-placeholder" style="color: var(--color-muted); text-align: center; max-width: 80%;">
            <i class="bi bi-qr-code-scan"
              style="font-size: 3.5rem; display: block; margin-bottom: 16px; opacity: 0.3;"></i>
            Rellena los campos y presiona <b>Crear</b>
          </div>
        </div>

        <button id="btn-download" class="btn btn-outline"
          style="width: 100%; justify-content: center; padding: 14px; font-size: 1.05rem;" disabled>
          <i class="bi bi-download" style="margin-right: 8px;"></i> Descargar QR (PNG)
        </button>
      </div>
    </div>

    <section class="features-section" style="margin-top: 80px;" data-aos="fade-up">
      <h2 class="section-title" style="text-align: center;">Generador QR Definitivo</h2>
      <p style="text-align: center; color: var(--color-muted); margin-bottom: 40px;">Disfruta de funciones premium sin
        costo ni límites de escaneos.</p>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-tablet"></i></div>
          <h4>Diseño Responsivo</h4>
          <p>La vista se adapta perfectamente a celulares, tablets y ordenadores. Genera y guarda códigos en donde sea
            que estés.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-grid-1x2"></i></div>
          <h4>Múltiples formatos</h4>
          <p>Soporte para enlaces, contraseñas de WiFi, tarjetas de contacto vCard, correos electrónicos o envíos de
            WhatsApp directo.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-images"></i></div>
          <h4>Descarga de Alta Resolución</h4>
          <p>Exporta tu QR con tamaño hasta de 1200px. Especialmente diseñado para carteles físicos, menús de
            restaurante e impresos HD.</p>
        </div>
      </div>
    </section>
  </main>

  <footer>
    © <span id="current-year"></span> Capy Ventas · Plataforma integral para la gestión y crecimiento de ventas.
  </footer>

  <!-- Librería pura para generar QR de alta calidad y fácil uso -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3076714141618004"
    crossorigin="anonymous"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Setup UI Basics
      const yearLabel = document.getElementById("current-year");
      if (yearLabel) yearLabel.textContent = new Date().getFullYear().toString();

      // Mobile Menu Toggle
      const header = document.querySelector("header");
      const menuToggle = document.querySelector(".menu-toggle");
      const navigation = document.getElementById("primary-navigation");

      if (header && menuToggle && navigation) {
        const alternarMenuPrincipal = () => {
          const abierto = header.classList.toggle("is-menu-open");
          menuToggle.classList.toggle("active", abierto);
          menuToggle.setAttribute("aria-expanded", abierto ? "true" : "false");
        };

        menuToggle.addEventListener("click", alternarMenuPrincipal);

        navigation.querySelectorAll("a").forEach((link) => {
          link.addEventListener("click", () => {
            if (header.classList.contains("is-menu-open")) alternarMenuPrincipal();
          });
        });
      }

      // Tab Management
      const tabs = document.querySelectorAll('.qr-tab');
      const tabContents = document.querySelectorAll('.qr-tab-content');

      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          // Remove active class from all
          tabs.forEach(t => t.classList.remove('active'));
          tabContents.forEach(c => c.classList.remove('active'));

          // Add active class to current
          tab.classList.add('active');
          const target = document.getElementById(`tab-${tab.dataset.tab}`);
          if (target) target.classList.add('active');
        });
      });

      // Generator Logic
      const btnGenerate = document.getElementById("btn-generate");
      const btnDownload = document.getElementById("btn-download");
      const qrcodeContainer = document.getElementById("qrcode-container");
      const qrColorInput = document.getElementById("qr-color");
      const qrBgInput = document.getElementById("qr-bg");
      const qrSizeInput = document.getElementById("qr-size");

      // Extract form data based on active tab
      function getQRData() {
        const activeTab = document.querySelector('.qr-tab.active').dataset.tab;

        switch (activeTab) {
          case 'url':
            return document.getElementById('qr-input-url').value.trim();
          case 'text':
            return document.getElementById('qr-input-text').value.trim();
          case 'whatsapp':
            const phone = document.getElementById('qr-input-wa-num').value.replace(/\D/g, '');
            const msg = encodeURIComponent(document.getElementById('qr-input-wa-msg').value.trim());
            if (!phone) return "";
            return `https://wa.me/${phone}${msg ? '?text=' + msg : ''}`;
          case 'wifi':
            const ssid = document.getElementById('qr-input-wifi-ssid').value.trim();
            const pass = document.getElementById('qr-input-wifi-pass').value.trim();
            const type = document.getElementById('qr-input-wifi-type').value;
            const hidden = document.getElementById('qr-input-wifi-hidden').checked ? "true" : "false";
            if (!ssid) return "";
            return `WIFI:T:${type};S:${ssid};P:${pass};H:${hidden};;`;
          case 'vcard':
            const vname = document.getElementById('qr-input-vc-name').value.trim();
            const vlast = document.getElementById('qr-input-vc-last').value.trim();
            const vphone = document.getElementById('qr-input-vc-phone').value.trim();
            const vemail = document.getElementById('qr-input-vc-email').value.trim();
            const vcomp = document.getElementById('qr-input-vc-company').value.trim();
            if (!vname && !vphone) return "";
            let vcard = `BEGIN:VCARD\nVERSION:3.0\nN:${vlast};${vname};;;\nFN:${vname} ${vlast}\n`;
            if (vcomp) vcard += `ORG:${vcomp}\n`;
            if (vphone) vcard += `TEL;TYPE=work,voice:${vphone}\n`;
            if (vemail) vcard += `EMAIL:${vemail}\n`;
            vcard += `END:VCARD`;
            return vcard;
          case 'email':
            const address = document.getElementById('qr-input-em-address').value.trim();
            const sub = document.getElementById('qr-input-em-sub').value.trim();
            const body = document.getElementById('qr-input-em-body').value.trim();
            if (!address) return "";
            return `mailto:${address}?subject=${encodeURIComponent(sub)}&body=${encodeURIComponent(body)}`;
          default:
            return "";
        }
      }

      function showError(message) {
        qrcodeContainer.innerHTML = `<p style='color: #d9534f; text-align: center; max-width: 80%;'><i class="bi bi-exclamation-triangle-fill" style="display:block; font-size:2rem; margin-bottom:10px;"></i> ${message}</p>`;
        btnDownload.disabled = true;
        btnDownload.classList.remove("btn-primary");
        btnDownload.classList.add("btn-outline");
      }

      btnGenerate.addEventListener("click", () => {
        const data = getQRData();

        if (!data) {
          showError("Por favor completa los campos requeridos para este tipo de QR.");
          return;
        }

        qrcodeContainer.innerHTML = "";
        const size = parseInt(qrSizeInput.value) || 400;
        const colorDark = qrColorInput.value || "#000000";
        const colorLight = qrBgInput.value || "#ffffff";

        try {
          new QRCode(qrcodeContainer, {
            text: data,
            width: size,
            height: size,
            colorDark: colorDark,
            colorLight: colorLight,
            correctLevel: QRCode.CorrectLevel.H
          });

          // Style canvas/img to fit container dynamically while keeping original resolution when downloading
          const childElements = qrcodeContainer.querySelectorAll('img, canvas');
          childElements.forEach(el => {
            el.style.maxWidth = '100%';
            el.style.height = 'auto';
            el.style.maxHeight = '300px';
            el.style.objectFit = 'contain';
          });

          btnDownload.disabled = false;
          btnDownload.classList.remove("btn-outline");
          btnDownload.classList.add("btn-primary");

          // Resalto visual suave
          qrcodeContainer.style.transform = "scale(1.05)";
          setTimeout(() => qrcodeContainer.style.transform = "scale(1)", 200);

        } catch (error) {
          showError("El contenido es demasiado extenso para el código QR. Intenta acortarlo.");
        }
      });

      btnDownload.addEventListener("click", () => {
        const canvas = qrcodeContainer.querySelector("canvas");
        const img = qrcodeContainer.querySelector("img");

        if (canvas) {
          const url = canvas.toDataURL("image/png");
          descargarImagen(url, "capy-ventas-qr.png");
        } else if (img && img.src) {
          descargarImagen(img.src, "capy-ventas-qr.png");
        }
      });

      function descargarImagen(url, nombreArchivo) {
        const a = document.createElement("a");
        a.href = url;
        a.download = nombreArchivo;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      }
    });
  </script>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 800,
      once: false,
      mirror: true,
      offset: 50
    });
  </script>
</body>

</html>