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

    .pricing-section {
      margin-top: 120px;
    }

    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 28px;
    }

    .pricing-card {
      position: relative;
      background: var(--color-card);
      border-radius: var(--border-radius);
      padding: 36px 32px;
      border: 1px solid rgba(35, 67, 106, 0.12);
      box-shadow: var(--shadow-soft);
      display: grid;
      gap: 18px;
    }

    .pricing-card.recommended {
      border: 2px solid var(--color-secondary);
      transform: translateY(-8px);
      box-shadow: 0 24px 48px rgba(27, 191, 157, 0.22);
    }

    .plan-badge {
      position: absolute;
      top: 24px;
      right: 24px;
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
      font-size: 1.35rem;
      font-weight: 600;
      color: var(--color-primary);
    }

    .plan-description {
      color: var(--color-muted);
      font-size: 0.95rem;
    }

    .plan-price {
      font-size: 2.4rem;
      font-weight: 700;
      color: var(--color-secondary);
    }

    .plan-price span {
      font-size: 0.9rem;
      color: var(--color-muted);
      font-weight: 500;
      margin-left: 6px;
    }

    .plan-features {
      list-style: none;
      display: grid;
      gap: 10px;
      color: var(--color-muted);
      font-size: 0.95rem;
      padding-left: 0;
    }

    .plan-features li {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .plan-features li::before {
      content: "✓";
      font-weight: 700;
      color: var(--color-secondary);
    }

    .plan-cta {
      margin-top: 12px;
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
      background: radial-gradient(circle, rgba(27, 191, 157, 0.35), transparent 60%);
      top: -220px;
      right: -120px;
      z-index: 0;
    }

    .cta-content {
      position: relative;
      z-index: 2;
      display: grid;
      gap: 16px;
      max-width: 600px;
    }

    .cta-content h3 {
      font-size: clamp(2.1rem, 3.2vw, 2.8rem);
      font-weight: 700;
      line-height: 1.2;
    }

    .cta-content p {
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.8);
    }

    footer {
      margin-top: 80px;
      padding: 40px 0 20px;
      text-align: center;
      color: var(--color-muted);
      font-size: 0.85rem;
    }

    footer a {
      color: var(--color-secondary);
      text-decoration: none;
      font-weight: 600;
    }

    @media (max-width: 860px) {
      header {
        flex-direction: column;
        gap: 18px;
        padding: 22px 6%;
      }

      nav {
        flex-wrap: wrap;
        justify-content: center;
      }

      .hero {
        gap: 40px;
        padding: 40px 0 20px;
      }

      .hero-card {
        order: -1;
      }
    }

    @media (max-width: 540px) {
      main {
        padding: 24px 5% 80px;
      }

      header {
        align-items: flex-start;
      }

      nav {
        width: 100%;
        justify-content: space-between;
        gap: 14px;
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
      <a href="#contacto">Contacto</a>
    </nav>
    <a class="btn btn-outline" href="#demo">Solicitar demo</a>
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
          <a class="btn btn-primary" href="#demo">Comenzar prueba gratuita</a>
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

    <section class="pricing-section" id="planes">
      <h2 class="section-title">Planes diseñados para tu crecimiento</h2>
      <p class="section-subtitle">
        Selecciona el plan que mejor se adapta a tu equipo y activa Capy Ventas desde S/. 189 al mes. Todos los
        planes incluyen implementación guiada y soporte en español.
      </p>
      <div class="pricing-grid">
        <article class="pricing-card">
          <h3 class="plan-name">Plan Esencial</h3>
          <p class="plan-description">Ideal para puntos de venta que necesitan digitalizar procesos rápidamente.</p>
          <p class="plan-price">S/. 189<span>/mes</span></p>
          <ul class="plan-features">
            <li>Hasta 5 usuarios activos</li>
            <li>Módulos de POS e inventario</li>
            <li>Reportes básicos de ventas</li>
            <li>Soporte vía chat en horario laboral</li>
          </ul>
          <div class="plan-cta">
            <a class="btn btn-outline" href="#demo">Probar Plan Esencial</a>
          </div>
        </article>
        <article class="pricing-card recommended">
          <span class="plan-badge">Más popular</span>
          <h3 class="plan-name">Plan Profesional</h3>
          <p class="plan-description">Combina analítica avanzada y CRM para acelerar la productividad comercial.</p>
          <p class="plan-price">S/. 389<span>/mes</span></p>
          <ul class="plan-features">
            <li>Usuarios ilimitados</li>
            <li>Módulos POS, inventario y CRM</li>
            <li>Campañas automatizadas y reportes BI</li>
            <li>Soporte prioritario 24/7</li>
          </ul>
          <div class="plan-cta">
            <a class="btn btn-primary" href="#demo">Elegir Plan Profesional</a>
          </div>
        </article>
        <article class="pricing-card">
          <h3 class="plan-name">Plan Enterprise</h3>
          <p class="plan-description">Integraciones personalizadas y acompañamiento estratégico para grandes redes.</p>
          <p class="plan-price">S/. 729<span>/mes</span></p>
          <ul class="plan-features">
            <li>Gestión de múltiples sedes y bodegas</li>
            <li>API abierta y conectores con ERP</li>
            <li>Asesoría dedicada en analítica e IA</li>
            <li>Capacitaciones trimestrales in-company</li>
          </ul>
          <div class="plan-cta">
            <a class="btn btn-outline" href="mailto:ventas@capyventas.com">Hablar con ventas</a>
          </div>
        </article>
      </div>
    </section>

    <section class="cta-panel" id="demo">
      <div class="cta-content">
        <h3>Agenda una demo personalizada y descubre Capy Ventas en acción</h3>
        <p>
          Un especialista analizará tu modelo de negocio, configurará los módulos ideales y te acompañará
          en la migración para que adoptes la plataforma sin fricción.
        </p>
        <div>
          <a class="btn btn-primary" href="mailto:hola@capyventas.com">Reservar demo</a>
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
    © <?php echo date('Y'); ?> Capy Ventas · Plataforma integral para la gestión y crecimiento de ventas.
  </footer>
</body>
</html>
