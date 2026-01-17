<!DOCTYPE html>

<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Capy Ventas | Plataforma Comercial Integral</title>

  <!-- SEO Meta Tags -->
  <meta name="description" content="Descubre Capy Ventas, la plataforma comercial integral que unifica punto de venta (POS), inventario, CRM y analítica para potenciar tus ventas omnicanal. Administra múltiples negocios desde un solo lugar y convierte cada oportunidad en ingresos.">
  <meta name="keywords" content="punto de venta, POS, CRM, inventario, ventas omnicanal, gestión de negocios, plataforma comercial, Capy Ventas, analítica de ventas, software para retail">
  <meta name="author" content="Capy Ventas">
  <meta name="robots" content="index, follow">
  <link rel="shortcut icon" href="https://capyventas.shaday-pe.com/Assets/capysm.png" type="image/x-icon">

  <!-- Canonical URL -->
  <!-- Asegúrate de reemplazar 'https://www.capyventas.com' con tu dominio real -->
  <link rel="canonical" href="https://capyventas.shaday-pe.com">
  <!--<link rel="stylesheet" href="./../Assets/css/libraries/POS/plugins/feather.css" type="text/css">-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://capyventas.shaday-pe.com">
  <meta property="og:title" content="Capy Ventas | Plataforma Comercial Integral">
  <meta property="og:description" content="La plataforma todo en uno para gestionar y escalar tus ventas omnicanal. Integra POS, inventario y CRM fácilmente.">
  <!-- Asegúrate de reemplazar 'URL_DE_TU_IMAGEN_OG.png' con la URL de una imagen representativa (ej. 1200x630px) -->
  <meta property="og:image" content="https://capyventas.shaday-pe.com/Assets/capylg.png">

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
      width: 100%;
      overflow-x: hidden;
    }

    html {
      scroll-behavior: smooth;
    }

    header {
      width: 100% !important;
      padding: 15px 6%;
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
      box-shadow: 0 4px 12px rgba(35, 67, 106, 0.1);
    }

    .logo-img:hover {
      transform: scale(1.05);
      filter: drop-shadow(0 6px 12px rgba(35, 67, 106, 0.15));
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
      transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
    }

    .menu-toggle:hover {
      background: rgba(35, 67, 106, 0.12);
      transform: translateY(-1px);
    }

    .menu-toggle.active {
      background: var(--color-primary);
      color: #ffffff;
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

    .hero-card>* {
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
      overflow: hidden;
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

    /* Contact Form Styles */
    .contact-form-container {
      max-width: 600px;
      margin: 0 auto;
      background: var(--color-card);
      padding: 40px;
      border-radius: 24px;
      box-shadow: 0 20px 40px rgba(35, 67, 106, 0.06);
      border: 1px solid rgba(35, 67, 106, 0.08);
      position: relative;
      overflow: hidden;
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

    .form-input,
    .form-textarea {
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

    .form-input:focus,
    .form-textarea:focus {
      outline: none;
      border-color: var(--color-secondary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(27, 191, 157, 0.15);
      transform: translateY(-1px);
    }

    .form-textarea {
      resize: vertical;
      min-height: 140px;
    }

    /* Plans Compare Table */
    .plans-compare-container {
      margin-top: 60px;
      overflow-x: auto;
      background: var(--color-card);
      border-radius: 24px;
      box-shadow: var(--shadow-soft);
      border: 1px solid rgba(35, 67, 106, 0.08);
      padding: 30px;
    }

    .compare-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
    }

    .compare-table th,
    .compare-table td {
      padding: 16px 20px;
      text-align: center;
      border-bottom: 1px solid rgba(35, 67, 106, 0.08);
      font-size: 0.95rem;
    }

    .compare-table th:first-child,
    .compare-table td:first-child {
      text-align: left;
      font-weight: 600;
      color: var(--color-dark);
      width: 30%;
    }

    .compare-table tr:hover td {
      background-color: rgba(27, 191, 157, 0.04);
    }

    .compare-table th {
      font-weight: 700;
      color: var(--color-primary);
      font-size: 1.1rem;
      padding-bottom: 24px;
      position: sticky;
      top: 0;
      background: var(--color-card);
      z-index: 10;
    }

    .compare-table tr:last-child td {
      border-bottom: none;
    }

    .feature-category {
      background-color: rgba(235, 243, 250, 0.7);
      font-weight: 700;
      color: var(--color-secondary);
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 1.2px;
      padding: 16px 20px;
      text-align: left;
    }

    .bi-check-circle-fill {
      color: var(--color-secondary);
      font-size: 1.25rem;
      filter: drop-shadow(0 2px 4px rgba(27, 191, 157, 0.2));
    }

    .bi-x-lg {
      color: #cbd5e1;
      font-size: 1.1rem;
    }

    .text-muted-icon {
      color: #cbd5e1;
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
      display: flex;
      flex-direction: column;
      gap: 20px;
      overflow: hidden;
      height: 100%;
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

    .plan-features {
      list-style: none;
      margin: 24px 0 32px;
      display: grid;
      gap: 14px;
    }

    .plan-features li {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.95rem;
      color: var(--color-muted);
    }

    .plan-features li i {
      color: var(--color-secondary);
      font-size: 1.1rem;
    }

    .original-price {
      text-decoration: line-through;
      color: var(--color-muted);
      font-size: 1.1rem;
      display: block;
      margin-bottom: -8px;
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

    .cta-panel>* {
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
        width: 100%;
        flex-wrap: wrap;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 15px 5%;
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

      .hero {
        padding: 40px 0;
        margin-top: 0;
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

    @media (max-width: 768px) {
      main {
        width: 100%;
        padding: 20px 4% 80px;
      }

      .hero-content h1 {
        font-size: clamp(2rem, 8vw, 2.6rem);
      }

      .billing-toggle {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
      }

      .hero-buttons {
        flex-direction: column;
        align-items: stretch;
      }

      .hero-content p {
        max-width: none;
      }

      .hero-card {
        padding: 28px;
      }

      .metrics-grid,
      .features-grid,
      .pricing-grid {
        grid-template-columns: 1fr;
      }

      .pricing-wrapper {
        padding: 40px 22px;
      }

      .cta-panel {
        padding: 40px 22px;
      }
    }

    @media (max-width: 520px) {
      header {
        padding: 12px 5%;
      }

      .hero {
        gap: 40px;
      }

      .hero-content h1 {
        font-size: clamp(1.9rem, 9vw, 2.4rem);
      }

      .hero-card h2 {
        font-size: 1.6rem;
      }

      .hero-metric {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
      }

      .pricing-section::before,
      .pricing-section::after {
        display: none;
      }

      .cta-panel::before,
      .cta-panel::after {
        display: none;
      }

      footer {
        padding: 30px 6% 40px;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="brand-container">
      <img src="Assets/capymd.png" alt="Capy Ventas" class="logo-img">
      <span class="brand-name">Capy Ventas</span>
    </div>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
      <span class="menu-icon" aria-hidden="true">
        <span></span>
        <span></span>
        <span></span>
      </span>
      <span class="menu-label">Menú</span>
    </button>
    <nav id="primary-navigation">
      <a href="#solucion">Solución</a>
      <a href="#metricas">Impacto</a>
      <a href="#funcionalidades">Funcionalidades</a>
      <a href="#clientes">Clientes</a>
      <a href="#planes">Planes</a>
      <a href="#contacto">Contacto</a>
    </nav>
    <div class="header-actions">
      <a class="btn btn-ghost" href="/pos/login">Soy cliente</a>
      <a
        class="btn btn-outline"
        href="/pos/account"
        target="_self"
        rel="noopener noreferrer">
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
          para que cada oportunidad se convierta en ingresos recurrentes, incluyendo la creación
          y administración de diferentes negocios desde una misma plataforma.
        </p>
        <div class="hero-buttons">
          <a
            class="btn btn-primary"
            href="/pos/account"
            target="_self"
            rel="noopener noreferrer">
            Inicia gratis
          </a>
          <a class="btn btn-outline" href="#contacto">Hablar con un especialista</a>
        </div>
        <div class="hero-highlights">
          <span class="highlight">Creación ilimitada de negocios</span>
          <span class="highlight">Administración centralizada de diferentes negocios</span>
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
          <h4>Punto de Venta y Caja</h4>
          <p>Registra ventas en segundos, gestiona aperturas y cierres de caja detallados para un control total del efectivo.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">Stock</div>
          <h4>Inventario y Proveedores</h4>
          <p>Control total de tu stock, gestión eficiente de compras y administración centralizada de proveedores.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">CRM</div>
          <h4>Gestión de Clientes</h4>
          <p>Base de datos centralizada para conocer mejor a tus compradores y gestionar sus datos de contacto.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">Fin</div>
          <h4>Estadísticas y Gastos</h4>
          <p>Monitorea tus ingresos en tiempo real, registra tus gastos operativos y visualiza la rentabilidad del negocio.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">Multi</div>
          <h4>Multi-Negocio</h4>
          <p>Crea y administra diferentes negocios independientes desde una sola cuenta de usuario.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">User</div>
          <h4>Usuarios y Permisos</h4>
          <p>Crea cuentas para tus trabajadores y asigna permisos específicos para controlar el acceso a los módulos.</p>
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
          <div class="client-logo">RS</div>
          <h3 class="client-name">Raby Shaday</h3>
          <span class="client-field">Tienda de hilos y textilería</span>
          <p class="client-quote">“Controlar el stock de miles de referencias de hilos y accesorios ahora es posible de forma rápida y sencilla.”</p>
        </article>
        <article class="client-card">
          <div class="client-logo">SV</div>
          <h3 class="client-name">LEJIAS SUPER VALENTINA</h3>
          <span class="client-field">Productos de limpieza</span>
          <p class="client-quote">“Nuestros procesos de venta de productos de limpieza y cuidado personal son más ágiles y organizados.”</p>
        </article>
        <article class="client-card">
          <div class="client-logo">DA</div>
          <h3 class="client-name">DALUMY</h3>
          <span class="client-field">Perfumería y moda femenina</span>
          <p class="client-quote">“La gestión de fidelización para nuestra boutique de perfumería ha mejorado la recompra y satisfacción de las clientas.”</p>
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
              <p class="plan-description">Ideal para emprendedores que recién comienzan.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Ventas Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Inventario Ilimitado</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Negocios Ilimitados</li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card recommended">
              <span class="plan-badge">Popular</span>
              <h3 class="plan-name">Pro</h3>
              <p class="plan-price">S/. 60<span>/mes</span></p>
              <p class="plan-description">Para negocios en crecimiento que necesitan más control.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Fotos de Productos Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Catálogo Público Web Estándar</li>
                <li><i class="bi bi-check-circle-fill"></i> Registro de Ingresos, Egresos y Gastos</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Caja (Arqueo/Cierre)</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Créditos y Fiados</li>
                <li><i class="bi bi-check-circle-fill"></i> 1 sola Caja</li>
                <li><i class="bi bi-check-circle-fill"></i> Clientes, Proveedores y Roles</li>
                <li><i class="bi bi-check-circle-fill"></i> Atención Prioritaria (Soporte)</li>
                <li><i class="bi bi-check-circle-fill"></i> <strong>Todo lo del Plan Gratis</strong></li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Empresarial</h3>
              <p class="plan-price">S/. 90<span>/mes</span></p>
              <p class="plan-description">Solución completa para empresas consolidadas.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Catálogo Web Personalizable</li>
                <li><i class="bi bi-check-circle-fill"></i> Creación de Cajas Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Configuración de Colores/Widgets</li>
                <li><i class="bi bi-check-circle-fill"></i> Atención Prioritaria (Soporte)</li>
                <li><i class="bi bi-check-circle-fill"></i> Acceso a Funciones Beta</li>
                <li><i class="bi bi-check-circle-fill"></i> <strong>Todo lo del Plan Pro</strong></li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>
          </div>

          <div class="pricing-grid is-hidden" data-plan-group="yearly">
            <article class="pricing-card">
              <h3 class="plan-name">Gratis</h3>
              <p class="plan-price">S/. 0<span>/año</span></p>
              <p class="plan-description">Ideal para emprendedores que recién comienzan.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Ventas Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Inventario Ilimitado</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Negocios Ilimitados</li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card recommended">
              <span class="plan-badge">Recomendado</span>
              <h3 class="plan-name">Pro Anual</h3>
              <span class="original-price">S/. 576.00</span>
              <p class="plan-price">S/. 461.00<span>/año</span></p>
              <p class="plan-description">Ahorra más del 20% con facturación anual.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Fotos de Productos Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Catálogo Público Web Estándar</li>
                <li><i class="bi bi-check-circle-fill"></i> Registro de Ingresos, Egresos y Gastos</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Caja (Arqueo/Cierre)</li>
                <li><i class="bi bi-check-circle-fill"></i> Gestión de Créditos y Fiados</li>
                <li><i class="bi bi-check-circle-fill"></i> 1 sola Caja</li>
                <li><i class="bi bi-check-circle-fill"></i> Clientes, Proveedores y Roles</li>
                <li><i class="bi bi-check-circle-fill"></i> Atención Prioritaria (Soporte)</li>
                <li><i class="bi bi-check-circle-fill"></i> <strong>Todo lo del Plan Gratis</strong></li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>

            <article class="pricing-card">
              <h3 class="plan-name">Empresarial Anual</h3>
              <span class="original-price">S/. 864.00</span>
              <p class="plan-price">S/. 691.00<span>/año</span></p>
              <p class="plan-description">Ahorra más del 20% con facturación anual.</p>
              <ul class="plan-features">
                <li><i class="bi bi-check-circle-fill"></i> Catálogo Web Personalizable</li>
                <li><i class="bi bi-check-circle-fill"></i> Creación de Cajas Ilimitadas</li>
                <li><i class="bi bi-check-circle-fill"></i> Configuración de Colores/Widgets</li>
                <li><i class="bi bi-check-circle-fill"></i> Atención Prioritaria (Soporte)</li>
                <li><i class="bi bi-check-circle-fill"></i> Acceso a Funciones Beta</li>
                <li><i class="bi bi-check-circle-fill"></i> <strong>Todo lo del Plan Pro</strong></li>
              </ul>
              <div class="plan-cta">
                <a
                  class="btn btn-primary"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Crear cuenta
                </a>
                <a
                  class="btn btn-outline"
                  href="/pos/account"
                  target="_self"
                  rel="noopener noreferrer">
                  Inicia gratis
                </a>
              </div>
            </article>
          </div>
        </div>

        <!-- Comparative Table -->
        <div class="plans-compare-container">
          <h3 class="section-title" style="margin-bottom: 30px; font-size: 1.8rem;">Comparativa de Funciones</h3>
          <table class="compare-table">
            <thead>
              <tr>
                <th>Funciones</th>
                <th>Plan Gratis</th>
                <th>Plan Pro</th>
                <th>Plan Empresarial</th>
              </tr>
            </thead>
            <tbody>
              <!-- Ventas e Inventario -->
              <tr>
                <td colspan="4" class="feature-category">Ventas e Inventario</td>
              </tr>
              <tr>
                <td>Ventas Ilimitadas</td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Inventario de Productos</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitado</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitado</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitado</td>
              </tr>
              <tr>
                <td>Fotos de Productos</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitadas</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitadas</td>
              </tr>
              <tr>
                <td>Catálogo Público Web</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i> Estándar</td>
                <td><i class="bi bi-check-circle-fill"></i> Personalizable</td>
              </tr>

              <!-- Finanzas y Caja -->
              <tr>
                <td colspan="4" class="feature-category">Finanzas y Caja</td>
              </tr>
              <tr>
                <td>Registro de Ingresos</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Registro de Egresos y Gastos</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Gestión de Caja (Arqueo/Cierre)</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Gestión de Créditos y Fiados</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Creación de Cajas</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i> 1 sola</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitadas</td>
              </tr>

              <!-- Administración -->
              <tr>
                <td colspan="4" class="feature-category">Administración</td>
              </tr>
              <tr>
                <td>Gestión de Negocios</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitados</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitados</td>
                <td><i class="bi bi-check-circle-fill"></i> Ilimitados</td>
              </tr>
              <tr>
                <td>Gestión de Clientes y Proveedores</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Gestión de Empleados y Roles</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>

              <!-- Soporte y Exclusivos -->
              <tr>
                <td colspan="4" class="feature-category">Soporte y Exclusivos</td>
              </tr>
              <tr>
                <td>Configuración de Colores y Widgets</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Atención Prioritaria (Soporte)</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
              <tr>
                <td>Acceso a Funciones Beta</td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-x-lg"></i></td>
                <td><i class="bi bi-check-circle-fill"></i></td>
              </tr>
            </tbody>
          </table>
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
            href="https://wa.me/51910367611?text=Hola%2C%20quiero%20iniciar%20gratis%20con%20Capy%20Ventas%20y%20recibir%20acompa%C3%B1amiento"
            target="_blank"
            rel="noopener noreferrer">
            Inicia gratis ahora
          </a>
        </div>
      </div>
    </section>

    <section id="contacto" style="margin-top: 80px; padding-bottom: 80px;">
      <h2 class="section-title">Estamos listos para ayudarte</h2>
      <p class="section-subtitle">
        Envíanos un mensaje y te responderemos directamente por WhatsApp.
      </p>

      <div class="contact-form-container">
        <form id="whatsappForm">
          <div class="form-group">
            <label for="contactName" class="form-label">Nombre</label>
            <input type="text" id="contactName" class="form-input" placeholder="Tu nombre" required>
          </div>
          <div class="form-group">
            <label for="contactMessage" class="form-label">Mensaje</label>
            <textarea id="contactMessage" class="form-textarea" placeholder="¿En qué podemos ayudarte?" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <i class="bi bi-whatsapp"></i>&nbsp; Enviar a WhatsApp
          </button>
        </form>
      </div>
    </section>
  </main>

  <footer>
    © <span id="current-year"></span> Capy Ventas · Plataforma integral para la gestión y crecimiento de ventas.
  </footer>

  <footer>
    <link rel="stylesheet" href="Assets/css/app/POS/chatbot/style_chatbot.css">
    <?php include __DIR__ . '/../Views/App/POS/chatbot/capyChatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="Assets/js/app/POS/chatbot/capyChatbot.js"></script>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const toggleButtons = document.querySelectorAll(".toggle-button");
      const planGroups = document.querySelectorAll("[data-plan-group]");
      const yearLabel = document.getElementById("current-year");
      const header = document.querySelector("header");
      const menuToggle = document.querySelector(".menu-toggle");
      const navigation = document.getElementById("primary-navigation");

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

      if (header && menuToggle && navigation) {
        /**
         * Alterna la visibilidad del menú principal para optimizar la
         * experiencia en dispositivos móviles.
         *
         * @returns {void}
         */
        const alternarMenuPrincipal = () => {
          const abierto = header.classList.toggle("is-menu-open");
          menuToggle.classList.toggle("active", abierto);
          menuToggle.setAttribute("aria-expanded", abierto ? "true" : "false");
        };

        menuToggle.addEventListener("click", alternarMenuPrincipal);

        navigation.querySelectorAll("a").forEach((link) => {
          link.addEventListener("click", () => {
            if (header.classList.contains("is-menu-open")) {
              alternarMenuPrincipal();
            }
          });
        });
      }

      /* Active menu tracking */
      const observerOptions = {
        root: null,
        rootMargin: '-50% 0px -50% 0px', // Activate when section is in the middle
        threshold: 0
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const id = entry.target.getAttribute('id');
            if (id) {
              const activeLink = document.querySelector(`nav a[href="#${id}"]`);
              if (activeLink) {
                // Remove active class from all links
                document.querySelectorAll('nav a').forEach(link => link.classList.remove('active'));
                // Add active class to current link
                activeLink.classList.add('active');
              }
            }
          }
        });
      }, observerOptions);

      // Observe all sections that are linked in the menu
      document.querySelectorAll('section[id]').forEach(section => {
        observer.observe(section);
      });

      // Also handle click to set active immediately
      if (navigation) {
        navigation.querySelectorAll("a").forEach((link) => {
          link.addEventListener("click", () => {
            document.querySelectorAll('nav a').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
          });
        });
      }

      // WhatsApp Form Handler
      const whatsappForm = document.getElementById('whatsappForm');
      if (whatsappForm) {
        whatsappForm.addEventListener('submit', (e) => {
          e.preventDefault();

          const name = document.getElementById('contactName').value.trim();
          const message = document.getElementById('contactMessage').value.trim();

          if (!name || !message) return;

          const phoneNumber = "51910367611";
          const text = encodeURIComponent(`Hola, mi nombre es ${name}. ${message}`);
          const url = `https://wa.me/${phoneNumber}?text=${text}`;

          window.open(url, '_blank');
        });
      }

    });
  </script>
</body>

</html>