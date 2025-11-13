<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Capy Ventas | Plataforma Comercial Integral</title>

  <style>
    :root {
      --color-primary: #23436a;
      --color-secondary: #1bbf9d;
      --color-accent: #f7a325;
      --color-dark: #142235;
      --color-light: #f5f7fb;
      --color-muted: #6c7c8f;
      --color-card: #ffffff;
      --font-sans: "Poppins", "Segoe UI", sans-serif;
      --shadow-soft: 0 18px 40px rgba(19, 47, 76, 0.15);
      --border-radius: 18px;
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
      line-height: 1.6;
    }

    header {
      width: 100%;
      padding: 28px 6%;
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

    .logo {
      font-weight: 700;
      font-size: 1.35rem;
      color: var(--color-primary);
      letter-spacing: 0.05em;
    }

    nav {
      display: flex;
      gap: 28px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    nav a {
      text-decoration: none;
      font-size: 0.95rem;
      color: var(--color-dark);
      font-weight: 500;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: var(--color-secondary);
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
    }

    .btn-primary {
      background: linear-gradient(120deg, var(--color-secondary), #24d7b1);
      color: #fff;
      box-shadow: 0 14px 30px rgba(27, 191, 157, 0.35);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 40px rgba(27, 191, 157, 0.45);
    }

    .btn-outline {
      border: 2px solid var(--color-primary);
      color: var(--color-primary);
      background: transparent;
    }

    .btn-outline:hover {
      transform: translateY(-2px);
      color: var(--color-card);
      background: var(--color-primary);
      box-shadow: 0 16px 32px rgba(35, 67, 106, 0.32);
    }

    .btn-ghost {
      border: 2px solid rgba(35, 67, 106, 0.18);
      background: rgba(35, 67, 106, 0.08);
      color: var(--color-primary);
    }

    .btn-ghost:hover {
      transform: translateY(-2px);
      background: rgba(35, 67, 106, 0.14);
      box-shadow: 0 16px 32px rgba(35, 67, 106, 0.18);
    }

    main {
      width: 100%;
      padding: 40px 6% 120px;
    }

    .hero {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 60px;
      align-items: center;
      padding: 60px 0;
    }

    .hero-content h1 {
      font-size: clamp(2.6rem, 4vw, 3.6rem);
      font-weight: 700;
      line-height: 1.15;
      color: var(--color-primary);
      margin-bottom: 20px;
    }

    .hero-content p {
      font-size: 1.05rem;
      color: var(--color-muted);
      margin-bottom: 32px;
      max-width: 520px;
    }

    .hero-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 18px;
      align-items: center;
    }

    .hero-highlights {
      display: flex;
      flex-wrap: wrap;
      gap: 22px;
      margin-top: 30px;
    }

    .highlight {
      background: rgba(27, 191, 157, 0.12);
      color: var(--color-primary);
      padding: 12px 20px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 0.9rem;
    }

    .hero-card {
      background: var(--color-card);
      border-radius: var(--border-radius);
      padding: 38px;
      box-shadow: var(--shadow-soft);
      display: grid;
      gap: 22px;
      position: relative;
      overflow: hidden;
    }

    .hero-card::after {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top right, rgba(247, 163, 37, 0.3), transparent 55%);
      opacity: 0.8;
    }

    .hero-card > * {
      position: relative;
      z-index: 2;
    }

    .hero-card h2 {
      font-size: 2rem;
      color: var(--color-primary);
    }

    .hero-metric {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      border-radius: 16px;
      border: 1px solid rgba(35, 67, 106, 0.08);
      background: rgba(245, 247, 251, 0.9);
    }

    .hero-metric strong {
      font-size: 1.35rem;
      color: var(--color-secondary);
    }

    .section-title {
      font-size: clamp(2rem, 3vw, 2.6rem);
      font-weight: 700;
      color: var(--color-primary);
      text-align: center;
      margin-bottom: 18px;
    }

    .section-subtitle {
      text-align: center;
      max-width: 640px;
      margin: 0 auto 48px;
      color: var(--color-muted);
      font-size: 1rem;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 26px;
      margin-bottom: 80px;
    }

    .metric-card {
      background: var(--color-card);
      border-radius: var(--border-radius);
      padding: 28px;
      box-shadow: var(--shadow-soft);
      border: 1px solid rgba(35, 67, 106, 0.08);
    }

    .metric-card h3 {
      font-size: 2rem;
      color: var(--color-secondary);
      margin-bottom: 6px;
    }

    .metric-card span {
      font-size: 0.95rem;
      color: var(--color-muted);
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 26px;
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

    .feature-card h4 {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--color-primary);
    }

    .feature-card p {
      color: var(--color-muted);
      font-size: 0.95rem;
    }

    .clients-section {
      margin-top: 120px;
      background: rgba(35, 67, 106, 0.04);
      border-radius: 32px;
      padding: clamp(48px, 6vw, 72px);
      box-shadow: 0 18px 44px rgba(17, 40, 66, 0.12);
      border: 1px solid rgba(35, 67, 106, 0.08);
    }

    .clients-header {
      text-align: center;
      max-width: 660px;
      margin: 0 auto 42px;
      display: grid;
      gap: 12px;
    }

    .clients-eyebrow {
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: var(--color-secondary);
    }

    .clients-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 28px;
      align-items: stretch;
    }

    .client-card {
      background: var(--color-card);
      border-radius: 24px;
      padding: 26px 22px;
      border: 1px solid rgba(35, 67, 106, 0.08);
      box-shadow: 0 12px 28px rgba(17, 40, 66, 0.12);
      display: grid;
      gap: 14px;
      justify-items: center;
      text-align: center;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .client-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 44px rgba(17, 40, 66, 0.16);
    }

    .client-logo {
      width: 68px;
      height: 68px;
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(27, 191, 157, 0.18), rgba(35, 67, 106, 0.16));
      display: grid;
      place-items: center;
      color: var(--color-primary);
      font-weight: 700;
      font-size: 1.2rem;
    }

    .client-name {
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--color-primary);
    }

    .client-field {
      font-size: 0.85rem;
      color: var(--color-muted);
    }

    .client-quote {
      font-size: 0.95rem;
      color: var(--color-muted);
      line-height: 1.5;
    }

    .pricing-section {
      margin-top: 120px;
      position: relative;
      isolation: isolate;
    }

    .pricing-section::before,
    .pricing-section::after {
      content: "";
      position: absolute;
      border-radius: 50%;
      filter: blur(0);
      z-index: -1;
    }

    .pricing-section::before {
      width: 520px;
      height: 520px;
      background: radial-gradient(circle at center, rgba(27, 191, 157, 0.22), transparent 70%);
      top: -120px;
      left: -180px;
    }

    .pricing-section::after {
      width: 460px;
      height: 460px;
      background: radial-gradient(circle at center, rgba(35, 67, 106, 0.18), transparent 65%);
      bottom: -160px;
      right: -140px;
    }

    .pricing-wrapper {
      position: relative;
      padding: 72px clamp(32px, 6vw, 80px);
      border-radius: 36px;
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.92), rgba(235, 243, 250, 0.88));
      box-shadow: 0 28px 64px rgba(17, 40, 66, 0.18);
      border: 1px solid rgba(35, 67, 106, 0.08);
      overflow: hidden;
    }

    .pricing-wrapper::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(120deg, rgba(27, 191, 157, 0.06), rgba(35, 67, 106, 0));
      pointer-events: none;
    }

    .pricing-header {
      text-align: center;
      max-width: 720px;
      margin: 0 auto 48px;
      display: grid;
      gap: 14px;
    }

    .pricing-eyebrow {
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.22em;
      color: var(--color-secondary);
    }

    .pricing-header .section-title {
      margin-bottom: 0;
    }

    .billing-toggle {
      display: flex;
      justify-content: center;
      align-items: stretch;
      gap: 18px;
      margin-bottom: 48px;
      flex-wrap: wrap;
    }

    .toggle-button {
      border: 1px solid rgba(35, 67, 106, 0.14);
      background: rgba(245, 247, 251, 0.88);
      border-radius: 18px;
      padding: 16px 32px;
      min-width: 240px;
      display: grid;
      gap: 6px;
      text-align: left;
      color: var(--color-muted);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .toggle-button span {
      font-size: 1rem;
      color: var(--color-primary);
      font-weight: 600;
    }

    .toggle-button small {
      font-size: 0.8rem;
    }

    .toggle-button.active {
      border-color: transparent;
      background: linear-gradient(135deg, var(--color-secondary), #24d7b1);
      box-shadow: 0 16px 32px rgba(27, 191, 157, 0.25);
      color: rgba(255, 255, 255, 0.9);
    }

    .toggle-button.active span,
    .toggle-button.active small {
      color: #ffffff;
    }

    .pricing-grids {
      display: grid;
      gap: 36px;
    }

    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 28px;
    }

    .pricing-grid.is-hidden {
      display: none;
    }

    .pricing-card {
      position: relative;
      background: linear-gradient(160deg, rgba(255, 255, 255, 0.95), rgba(235, 243, 250, 0.88));
      border-radius: 26px;
      padding: 40px 34px 44px;
      border: 1px solid rgba(35, 67, 106, 0.08);
      box-shadow: 0 20px 48px rgba(17, 40, 66, 0.18);
      display: grid;
      gap: 20px;
      overflow: hidden;
    }

    .pricing-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(27, 191, 157, 0.12), rgba(35, 67, 106, 0.06));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 0;
    }

    .pricing-card::after {
      content: "";
      position: absolute;
      width: 220px;
      height: 220px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(27, 191, 157, 0.22), transparent 70%);
      top: -120px;
      right: -100px;
      opacity: 0.65;
      z-index: 0;
      transition: transform 0.35s ease;
    }

    .pricing-card:hover::after {
      transform: translate(-14px, 18px) scale(1.05);
    }

    .pricing-grid[data-plan-group="monthly"] .pricing-card:nth-child(2)::after {
      background: radial-gradient(circle, rgba(35, 67, 106, 0.2), transparent 70%);
    }

    .pricing-grid[data-plan-group="monthly"] .pricing-card:nth-child(3)::after,
    .pricing-grid[data-plan-group="yearly"] .pricing-card.recommended::after {
      background: radial-gradient(circle, rgba(27, 191, 157, 0.28), transparent 68%);
    }

    .pricing-grid[data-plan-group="monthly"] .pricing-card:nth-child(5)::after,
    .pricing-grid[data-plan-group="yearly"] .pricing-card:nth-child(3)::after {
      background: radial-gradient(circle, rgba(247, 163, 37, 0.26), transparent 70%);
    }

    .pricing-grid[data-plan-group="monthly"] .pricing-card:nth-child(6)::after,
    .pricing-grid[data-plan-group="yearly"] .pricing-card:nth-child(4)::after {
      background: radial-gradient(circle, rgba(35, 67, 106, 0.24), transparent 72%);
    }

    .pricing-card:hover::before {
      opacity: 1;
    }

    .pricing-card.recommended {
      border: 2px solid rgba(27, 191, 157, 0.55);
      transform: translateY(-10px);
      box-shadow: 0 32px 68px rgba(27, 191, 157, 0.28);
    }

    .plan-badge {
      position: absolute;
      top: 22px;
      right: 22px;
      background: linear-gradient(135deg, var(--color-secondary), #24d7b1);
      color: #fff;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .plan-name {
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--color-primary);
      position: relative;
      z-index: 1;
    }

    .plan-price {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--color-secondary);
      position: relative;
      z-index: 1;
    }

    .plan-price span {
      font-size: 0.9rem;
      color: var(--color-muted);
      font-weight: 500;
      margin-left: 6px;
    }

    .plan-cta {
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 12px;
      position: relative;
      z-index: 1;
    }

    .plan-cta .btn {
      width: 100%;
    }

    .cta-panel {
      margin-top: 100px;
      background: linear-gradient(135deg, var(--color-primary), #1a3a5a 60%, rgba(20, 34, 53, 0.95));
      border-radius: 26px;
      padding: 64px clamp(24px, 5vw, 86px);
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    .cta-panel::before {
      content: "";
      position: absolute;
      width: 480px;
      height: 480px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(27, 191, 157, 0.35), transparent 65%);
      top: -220px;
      right: -120px;
      opacity: 0.8;
    }

    .cta-panel::after {
      content: "";
      position: absolute;
      width: 340px;
      height: 340px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(247, 163, 37, 0.28), transparent 60%);
      bottom: -160px;
      left: -60px;
      opacity: 0.6;
    }

    .cta-panel > * {
      position: relative;
      z-index: 2;
    }

    .cta-panel h3 {
      font-size: clamp(2rem, 3vw, 2.6rem);
      margin-bottom: 18px;
    }

    .cta-panel p {
      max-width: 580px;
      color: rgba(255, 255, 255, 0.88);
      margin-bottom: 28px;
    }

    footer {
      padding: 40px 6% 60px;
      text-align: center;
      color: var(--color-muted);
      font-size: 0.9rem;
    }

    @media (max-width: 960px) {
      header {
        flex-wrap: wrap;
        gap: 18px;
      }

      nav {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }

      .header-actions {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }

      .hero {
        padding: 40px 0;
      }

      .hero-content h1 {
        font-size: clamp(2.2rem, 6vw, 3rem);
      }

      .hero-content p {
        font-size: 1rem;
      }

      .metrics-grid,
      .features-grid,
      .pricing-grid {
        gap: 20px;
      }

      .cta-panel {
        margin-top: 80px;
        padding: 48px 26px;
      }

      .pricing-section {
        margin-top: 80px;
      }

      .pricing-wrapper {
        padding: 52px 28px;
        border-radius: 28px;
      }

      .billing-toggle {
        margin-bottom: 36px;
      }

      .plan-price {
        font-size: 2.2rem;
      }

      .plan-cta {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <span class="logo">Capy Ventas</span>
    <nav>
      <a href="#solucion">Solución</a>
      <a href="#metricas">Impacto</a>
      <a href="#funcionalidades">Funcionalidades</a>
      <a href="#planes">Planes</a>
      <a href="#clientes">Clientes</a>
      <a href="#contacto">Contacto</a>
    </nav>
    <div class="header-actions">
      <a class="btn btn-ghost" href="/pos/login">Soy cliente</a>
      <a
        class="btn btn-outline"
        href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20Capy%20Ventas"
        target="_blank"
        rel="noopener noreferrer"
      >
        Inicia gratis
      </a>
    </div>
  </header>

  <main>
    <section class="hero" id="solucion">
      <div class="hero-content">
        <h1>La plataforma que impulsa tus ventas omnicanal</h1>
        <p>
          Capy Ventas integra punto de venta, inventario, CRM y analítica en un solo ecosistema
          para que cada oportunidad se convierta en ingresos recurrentes.
        </p>
        <div class="hero-buttons">
          <a
            class="btn btn-primary"
            href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20Capy%20Ventas"
            target="_blank"
            rel="noopener noreferrer"
          >
            Inicia gratis
          </a>
          <a class="btn btn-outline" href="#contacto">Hablar con un especialista</a>
        </div>
        <div class="hero-highlights">
          <span class="highlight">Gestión de sucursales ilimitada</span>
          <span class="highlight">Automatización de campañas</span>
          <span class="highlight">Reportes accionables en tiempo real</span>
        </div>
      </div>

      <aside class="hero-card">
        <h2>Tablero comercial en vivo</h2>
        <p>Visualiza el rendimiento de tu operación con indicadores que se actualizan cada minuto.</p>
        <div class="hero-metric">
          <div>
            <small>Ventas de hoy</small>
            <strong>S/. 128,400</strong>
          </div>
          <span>+18% vs ayer</span>
        </div>
        <div class="hero-metric">
          <div>
            <small>Nuevos clientes</small>
            <strong>64</strong>
          </div>
          <span>+32% objetivo</span>
        </div>
        <div class="hero-metric">
          <div>
            <small>Ticket promedio</small>
            <strong>S/. 850</strong>
          </div>
          <span>+12% mensual</span>
        </div>
      </aside>
    </section>

    <section id="metricas">
      <h2 class="section-title">Resultados tangibles desde el primer mes</h2>
      <p class="section-subtitle">
        Las organizaciones comerciales que migran a Capy Ventas aceleran su ciclo de venta, reducen rupturas
        de inventario y diseñan experiencias coherentes en todos sus canales.
      </p>
      <div class="metrics-grid">
        <article class="metric-card">
          <h3>+43%</h3>
          <span>Crecimiento de ingresos</span>
          <p>Dispara ventas cruzadas gracias a campañas segmentadas y catálogos dinámicos.</p>
        </article>
        <article class="metric-card">
          <h3>-28%</h3>
          <span>Menos quiebres de stock</span>
          <p>Sincroniza existencias entre tiendas físicas, e-commerce y marketplaces en un tablero único.</p>
        </article>
        <article class="metric-card">
          <h3>95%</h3>
          <span>Retención de clientes</span>
          <p>Automatiza recordatorios, programas de lealtad y seguimientos desde nuestro CRM integrado.</p>
        </article>
        <article class="metric-card">
          <h3>24/7</h3>
          <span>Soporte especializado</span>
          <p>Acompañamiento continuo con especialistas comerciales y soporte técnico multicanal.</p>
        </article>
      </div>
    </section>

    <section id="funcionalidades">
      <h2 class="section-title">Todo lo que tu fuerza de ventas necesita</h2>
      <p class="section-subtitle">
        Moderniza tu operación con módulos modulares y escalables que se adaptan al tamaño de tu negocio.
      </p>
      <div class="features-grid">
        <article class="feature-card">
          <div class="feature-icon">POS</div>
          <h4>Punto de venta inteligente</h4>
          <p>Procesa cobros rápidos, aplica promociones combinadas y genera facturas en segundos.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">CRM</div>
          <h4>Gestión de clientes</h4>
          <p>Segmenta prospectos, asigna seguimientos automáticos y mide la efectividad de tus campañas.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">BI</div>
          <h4>Analítica de ventas</h4>
          <p>Accede a dashboards de rentabilidad, proyecciones y pronósticos de demanda.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">Ops</div>
          <h4>Operación centralizada</h4>
          <p>Controla inventarios, transferencias, compras y logística desde una sola consola.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">API</div>
          <h4>Integraciones abiertas</h4>
          <p>Conecta tu ERP, tienda en línea o apps móviles con nuestra API segura y documentada.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">IA</div>
          <h4>Asistente predictivo</h4>
          <p>Recibe alertas y recomendaciones automáticas para optimizar precios y disponibilidad.</p>
        </article>
      </div>
    </section>

    <section class="clients-section" id="clientes">
      <div class="clients-header">
        <span class="clients-eyebrow">Nuestros clientes</span>
        <h2 class="section-title">Marcas que confían en Capy Ventas</h2>
        <p class="section-subtitle">
          Acompañamos a negocios de retail, gastronomía, moda y servicios a escalar sus operaciones con experiencias
          omnicanal memorables.
        </p>
      </div>
      <div class="clients-grid">
        <article class="client-card">
          <div class="client-logo">RV</div>
          <h3 class="client-name">Retail Vision</h3>
          <span class="client-field">Retail multiformato</span>
          <p class="client-quote">“Duplicamos nuestras ventas online integrando tiendas físicas y ecommerce en un solo flujo.”</p>
        </article>
        <article class="client-card">
          <div class="client-logo">GF</div>
          <h3 class="client-name">Gourmet Factory</h3>
          <span class="client-field">Restaurantes y dark kitchens</span>
          <p class="client-quote">“Las órdenes llegan centralizadas y el inventario se sincroniza en tiempo real en todas las sedes.”</p>
        </article>
        <article class="client-card">
          <div class="client-logo">LM</div>
          <h3 class="client-name">Luna Moda</h3>
          <span class="client-field">Moda omnicanal</span>
          <p class="client-quote">“La segmentación automática nos permitió personalizar campañas y aumentar la recompra.”</p>
        </article>
        <article class="client-card">
          <div class="client-logo">TC</div>
          <h3 class="client-name">TechCare</h3>
          <span class="client-field">Servicios técnicos</span>
          <p class="client-quote">“Integramos CRM y soporte para dar seguimiento a cada ticket y cerrar contratos recurrentes.”</p>
        </article>
      </div>
    </section>

    <section class="pricing-section" id="planes">
      <div class="pricing-wrapper">
        <div class="pricing-header">
          <span class="pricing-eyebrow">Planes Capy Ventas</span>
          <h2 class="section-title">Planes diseñados para tu crecimiento</h2>
          <p class="section-subtitle">
            Elige la modalidad de pago que se ajusta a tu presupuesto. Los planes mensuales ofrecen flexibilidad sin
            permanencia y los planes anuales premian tu compromiso con descuentos especiales.
          </p>
        </div>

        <div class="billing-toggle" role="group" aria-label="Selector de modalidad de facturación">
          <button class="toggle-button active" data-target="monthly" type="button">
            <span>Pagos mensuales</span>
            <small>Flexibilidad sin contratos</small>
          </button>
          <button class="toggle-button" data-target="yearly" type="button">
            <span>Pagos anuales</span>
            <small>Ahorra hasta 20%</small>
          </button>
        </div>

        <div class="pricing-grids">
          <div class="pricing-grid" data-plan-group="monthly">
            <article class="pricing-card">
              <h3 class="plan-name">Gratis</h3>
              <p class="plan-price">S/. 0<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Gratis%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Gratis%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Basic</h3>
              <p class="plan-price">S/. 10<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Basic%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Basic%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card recommended">
              <span class="plan-badge">Popular</span>
              <h3 class="plan-name">Pro</h3>
              <p class="plan-price">S/. 20<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Pro%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Pro%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Business</h3>
              <p class="plan-price">S/. 30<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Business%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Business%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Premium</h3>
              <p class="plan-price">S/. 40<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Premium%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Premium%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Full Max</h3>
              <p class="plan-price">S/. 50<span>/mes</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Full%20Max%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Full%20Max%20mensual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>
          </div>

          <div class="pricing-grid is-hidden" data-plan-group="yearly">
            <article class="pricing-card recommended">
              <span class="plan-badge">Recomendado</span>
              <h3 class="plan-name">Pro Anual</h3>
              <p class="plan-price">S/. 204<span>/año</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Pro%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Pro%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Business Anual</h3>
              <p class="plan-price">S/. 288<span>/año</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Business%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Business%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Premium Anual</h3>
              <p class="plan-price">S/. 384<span>/año</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Premium%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Premium%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Full Max Anual</h3>
              <p class="plan-price">S/. 480<span>/año</span></p>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20crear%20mi%20cuenta%20con%20el%20plan%20Full%20Max%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20el%20plan%20Full%20Max%20anual"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Inicia gratis
                </a>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-panel" id="demo">
      <div class="cta-content">
        <h3>Inicia gratis con acompañamiento personalizado</h3>
        <p>
          Un especialista analizará tu modelo de negocio, configurará los módulos ideales y te acompañará en cada paso para
          que adoptes la plataforma sin fricción.
        </p>
        <div>
          <a
            class="btn btn-primary"
            href="https://wa.me/5190367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20Capy%20Ventas%20y%20recibir%20acompa%C3%B1amiento"
            target="_blank"
            rel="noopener noreferrer"
          >
            Inicia gratis ahora
          </a>
        </div>
      </div>
    </section>

    <section id="contacto" style="margin-top: 80px;">
      <h2 class="section-title">Estamos listos para ayudarte</h2>
      <p class="section-subtitle">
        Escríbenos a <a href="mailto:soporte@capyventas.com">soporte@capyventas.com</a> o llámanos al
        <strong>+52 (55) 8000 1234</strong>. También puedes agendar una sesión estratégica con nuestro equipo comercial.
      </p>
    </section>
  </main>

  <footer>
    © <span id="current-year"></span> Capy Ventas · Plataforma integral para la gestión y crecimiento de ventas.
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const toggleButtons = document.querySelectorAll(".toggle-button");
      const planGroups = document.querySelectorAll("[data-plan-group]");
      const yearLabel = document.getElementById("current-year");

      toggleButtons.forEach((button) => {
        button.addEventListener("click", () => {
          const target = button.dataset.target;

          toggleButtons.forEach((btn) => {
            btn.classList.toggle("active", btn === button);
          });

          planGroups.forEach((group) => {
            const isTarget = group.dataset.planGroup === target;
            group.classList.toggle("is-hidden", !isTarget);
          });
        });
      });

      if (yearLabel) {
        yearLabel.textContent = new Date().getFullYear().toString();
      }
    });
  </script>
</body>
</html>
