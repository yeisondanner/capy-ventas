<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Capy Ventas - Inicio</title>

  <style>
    /* =================== TIPOGRAFÍA Y TOKENS =================== */
    @import url("https://fonts.cdnfonts.com/css/pp-neue-montreal");

    @font-face {
      font-family: "PPSupplyMono";
      src: url("https://assets.codepen.io/7558/PPSupplyMono-Regular.ttf") format("truetype");
      font-weight: normal;
      font-style: normal;
      font-display: swap;
    }

    /* ===== THEME TOKENS (se alternan por data-theme en <body>) ===== */
    :root {
      --font-primary: "PP Neue Montreal", sans-serif;
      --font-secondary: "PPSupplyMono", monospace;
      --font-sans: "PP Neue Montreal", sans-serif;

      --spacing-small: .5rem;
      --spacing-medium: 1rem;
      --spacing-large: 2rem;

      --font-size-small: 10px;
      --font-size-regular: 1rem;
      --font-size-medium: 1.5rem;
      --font-size-large: 4rem;

      --cv-primary: #292d96;   /* CapyVentas */
      --cv-accent:  #00e5a8;
    }

    body[data-theme="dark"] {
      --bg: #0d1117;
      --bg-2: #121826;
      --text-primary: #ffffff;
      --text-secondary: #c7cbe0;
      --btn-bg: var(--cv-accent);
      --btn-bg-2: var(--cv-primary);
      --btn-fg: #0b1220;
      --btn-fg-2: #ffffff;
      --grain-opacity: .12;
    }
    body[data-theme="light"] {
      --bg: #f7faff;
      --bg-2: #eaf1ff;
      --text-primary: #0b1220;
      --text-secondary: #2a3352;
      --btn-bg: #0b1220;
      --btn-bg-2: #2a3352;
      --btn-fg: #ffffff;
      --btn-fg-2: #ffffff;
      --grain-opacity: .06;
    }

    *, *::before, *::after { box-sizing: border-box; }

    html { font-size: 16px; scroll-behavior: smooth; }
    @media (min-width: 768px){ html { font-size: 18px; } }
    @media (min-width: 1200px){ html { font-size: 20px; } }

    body {
      margin: 0;
      color: var(--text-primary);
      background: var(--bg);
      font-family: var(--font-secondary);
      font-size: var(--font-size-small);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
    }

    body::after {
      content: "";
      position: fixed; inset: 0;
      background-image: url("https://img.freepik.com/premium-photo/white-dust-scratches-black-background_279525-2.jpg?w=640");
      background-repeat: repeat;
      opacity: var(--grain-opacity);
      mix-blend-mode: multiply;
      pointer-events: none;
      z-index: 1;
      filter: invert(0);
    }

    .section { width: 100vw; height: 100vh; position: relative; transition: all .6s ease; }
    .hero-section { padding: var(--spacing-large); }
    .fin-section { display:flex; align-items:center; justify-content:center; background: var(--bg); z-index:20; position:relative; }
    .fin-text { font-family: var(--font-primary); font-size: 3rem; color: var(--text-secondary); letter-spacing:.05em; }

    /* ===== WebGL layer ===== */
    #container {
      position: fixed; inset: 0; width: 100vw; height: 100vh;
      z-index: 0; pointer-events: none;
    }
    #stats { position: fixed; top: 10px; left: 10px; z-index: 100; display:none; } /* oculto */

    /* ===== Header + Logo ===== */
    .header-area {
      position: fixed; top: var(--spacing-large); left: 0; width: 100%;
      padding: 0 var(--spacing-large); display: flex; justify-content: center; z-index: 10;
    }
    .logo-container {
      position: absolute; left: calc(var(--spacing-large) + 86px); /* deja espacio al toggle */
      top: 0; display: flex; align-items: center; height: 2rem; z-index: 10;
    }
    .logo-circles { position: relative; width: 100%; height: 100%; }
    .circle { position:absolute; border-radius: 50%; transition: transform .5s cubic-bezier(.445,.05,.55,.95);
      width: 1.4rem; height: 1.4rem; top: 50%; }
    body[data-theme="dark"] .circle-1 { background: var(--cv-primary); }
    body[data-theme="dark"] .circle-2 { background: var(--cv-accent); mix-blend-mode: exclusion; }
    body[data-theme="light"] .circle-1 { background: #2a3352; }
    body[data-theme="light"] .circle-2 { background: #86f7da; mix-blend-mode: normal; }
    .circle-1 { left: 0; transform: translate(0,-50%); }
    .circle-2 { left: .8rem; transform: translate(0,-50%); }
    .logo-container:hover .circle-1 { transform: translate(-.5rem,-50%); }
    .logo-container:hover .circle-2 { transform: translate(.5rem,-50%); }

    .center-logo { text-align:center; z-index:10; height:2rem; }
    #logo-text { font-family: var(--font-primary); font-weight: 700; font-size: var(--font-size-medium); line-height:1; margin:0; color: var(--text-primary); }

    /* ===== Hero ===== */
    .hero {
      position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%);
      text-align: center; z-index: 10; width: 90%; max-width: 860px;
    }
    .hero h1 {
      font-family: var(--font-primary); font-weight: 800; font-size: var(--font-size-large);
      line-height: .9; letter-spacing: -0.02em; margin: 0 0 1.2rem 0;
      background: linear-gradient(180deg, currentColor, rgba(167,179,255,.9) 60%, rgba(122,241,209,.95) 100%);
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .hero h2 {
      font-family: var(--font-secondary); font-size: var(--font-size-small); color: var(--text-secondary);
      text-transform: uppercase; letter-spacing:.05em; line-height:1.4; opacity:.85; margin:0 0 1.2rem 0;
    }

    /* ===== Botones ===== */
    .button-group { margin-top: 1.6rem; display:flex; justify-content:center; gap:16px; flex-wrap:wrap; }
    .btn {
      display:inline-block; padding: 14px 24px; font-size: 1rem; font-weight: 800; letter-spacing:.01em;
      text-decoration:none; border:none; border-radius: 12px; cursor:pointer;
      transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
      box-shadow: 0 8px 24px rgba(0,0,0,.18);
      font-family: var(--font-sans);
    }
    body[data-theme="dark"] .btn { background: var(--btn-bg); color: var(--btn-fg); }
    body[data-theme="light"] .btn { background: var(--btn-bg); color: var(--btn-fg); }

    .btn:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(0,0,0,.25); opacity:.97; }
    .btn--secondary {
      box-shadow: 0 8px 24px rgba(0,0,0,.18);
    }
    body[data-theme="dark"] .btn--secondary { background: var(--btn-bg-2); color: var(--btn-fg-2); }
    body[data-theme="light"] .btn--secondary { background: var(--btn-bg-2); color: var(--btn-fg-2); }

    /* ===== Contacto & footer ===== */
    .contact-info {
      position: fixed; top: 50%; left: var(--spacing-large); transform: translateY(-50%);
      z-index: 10; font-family: var(--font-secondary); letter-spacing:.05em; font-size: var(--font-size-small);
      color: var(--text-primary); text-transform: uppercase;
    }
    .contact-heading { color: var(--text-secondary); margin-bottom: var(--spacing-small); }
    .contact-email { display:block; color: var(--text-primary); text-decoration:none; cursor:pointer; }
    .contact-email:hover { opacity:.8; }

    .footer-links {
      position: fixed; bottom: var(--spacing-large); left: var(--spacing-large); z-index: 10;
      display:flex; flex-direction:column; gap:.25rem; font-family: var(--font-sans); font-weight:400; font-size: var(--font-size-regular);
    }
    .footer-link { color: var(--text-secondary); text-decoration:none; position:relative; padding-left:0; transition: all .3s; }
    .footer-link::before { content:""; position:absolute; left:0; top:50%; width:0; height:1px; background: currentColor; transform: translateY(-50%); opacity:0; transition: width .3s, opacity .3s; }
    .footer-link:hover { color: var(--text-primary); padding-left:1.2rem; }
    .footer-link:hover::before { width:.8rem; opacity:1; }

    .coordinates { position: fixed; bottom: var(--spacing-large); right: var(--spacing-large); text-align:right; z-index: 10; font-family: var(--font-secondary); font-size: var(--font-size-small); color: var(--text-secondary); }

    @media (max-width: 768px){
      .hero h1 { font-size: 3rem; }
      .coordinates { font-size: 10px; }
    }
    @media (max-width: 480px){
      .hero-section { padding: var(--spacing-medium); }
      .header-area, .contact-info, .footer-links, .coordinates { padding: 0 var(--spacing-medium); }
      .logo-container { left: calc(var(--spacing-medium) + 86px); }
      .coordinates { right: var(--spacing-medium); }
      .hero h1 { font-size: 2.2rem; }
    }

    /* =================================================================
       THEME TOGGLE (adaptado de tu snippet; renombrado a .theme-toggle)
       ================================================================= */
    .theme-toggle {
      --scale: 2;
      position: fixed;
      top: 14px;
      left: 14px;
      z-index: 20; /* por encima del canvas y header */
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: calc(var(--scale) * 60px);
      height: calc(var(--scale) * 34px);
      user-select: none;
    }
    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
      position: absolute; inset: 0; cursor: pointer; transition: .4s; z-index: 0; overflow: hidden;
      background: #2196f3; /* day */
      border-radius: calc(var(--scale) * 34px);
      box-shadow: 0 4px 14px rgba(0,0,0,.15) inset;
    }
    /* cuando está checked => modo oscuro */
    #theme-toggle-input:checked + .slider { background: #000; }

    .sun-moon {
      position: absolute;
      height: calc(var(--scale) * 26px);
      width:  calc(var(--scale) * 26px);
      left:   calc(var(--scale) * 4px);
      bottom: calc(var(--scale) * 4px);
      background: yellow;
      border-radius: 50%;
      transition: .4s;
    }
    #theme-toggle-input:checked + .slider .sun-moon {
      transform: translateX(calc(var(--scale) * 26px));
      background: white;
      animation: rotate-center .6s ease-in-out both;
    }
    @keyframes rotate-center { 0% { transform: translateX(calc(var(--scale) * 26px)) rotate(0) } 100% { transform: translateX(calc(var(--scale) * 26px)) rotate(360deg) } }

    .moon-dot { opacity: 0; transition:.4s; fill: gray; position: absolute; z-index: 4; }
    #moon-dot-1 { left: calc(var(--scale) * 10px); top: calc(var(--scale) * 3px);  width: calc(var(--scale) * 6px);  height: calc(var(--scale) * 6px); }
    #moon-dot-2 { left: calc(var(--scale) * 2px);  top: calc(var(--scale) * 10px); width: calc(var(--scale) * 10px); height: calc(var(--scale) * 10px); }
    #moon-dot-3 { left: calc(var(--scale) * 16px); top: calc(var(--scale) * 18px); width: calc(var(--scale) * 3px);  height: calc(var(--scale) * 3px); }
    #theme-toggle-input:checked + .slider .sun-moon .moon-dot { opacity: 1; }

    .light-ray { position:absolute; z-index:-1; fill:white; opacity:10%; }
    #light-ray-1 { left: calc(var(--scale) * -8px);  top: calc(var(--scale) * -8px);  width: calc(var(--scale) * 43px); height: calc(var(--scale) * 43px); }
    #light-ray-2 { left: -50%; top: -50%; width: calc(var(--scale) * 55px); height: calc(var(--scale) * 55px); }
    #light-ray-3 { left: calc(var(--scale) * -18px); top: calc(var(--scale) * -18px); width: calc(var(--scale) * 60px); height: calc(var(--scale) * 60px); }

    .cloud-light, .cloud-dark { position:absolute; animation: cloud-move 6s infinite; }
    .cloud-light { fill: #eee; }
    .cloud-dark { fill: #ccc; animation-delay: 1s; }
    #cloud-1 { left: calc(var(--scale) * 30px); top: calc(var(--scale) * 15px); width: calc(var(--scale) * 40px); }
    #cloud-2 { left: calc(var(--scale) * 44px); top: calc(var(--scale) * 10px); width: calc(var(--scale) * 20px); }
    #cloud-3 { left: calc(var(--scale) * 18px); top: calc(var(--scale) * 24px); width: calc(var(--scale) * 30px); }
    #cloud-4 { left: calc(var(--scale) * 36px); top: calc(var(--scale) * 18px); width: calc(var(--scale) * 40px); }
    #cloud-5 { left: calc(var(--scale) * 48px); top: calc(var(--scale) * 14px); width: calc(var(--scale) * 20px); }
    #cloud-6 { left: calc(var(--scale) * 22px); top: calc(var(--scale) * 26px); width: calc(var(--scale) * 30px); }
    @keyframes cloud-move {
      0% { transform: translateX(0) } 40% { transform: translateX(calc(var(--scale) * 4px)) }
      80% { transform: translateX(calc(var(--scale) * -4px)) } 100% { transform: translateX(0) }
    }

    .stars { transform: translateY(calc(var(--scale) * -32px)); opacity: 0; transition:.4s; }
    #theme-toggle-input:checked + .slider .stars { transform: translateY(0); opacity: 1; }
    .star { fill:white; position:absolute; animation: star-twinkle 2s infinite; }
    #star-1 { width: calc(var(--scale) * 20px); top: calc(var(--scale) * 2px);  left: calc(var(--scale) * 3px);  animation-delay: .3s; }
    #star-2 { width: calc(var(--scale) * 6px);  top: calc(var(--scale) * 16px); left: calc(var(--scale) * 3px); }
    #star-3 { width: calc(var(--scale) * 12px); top: calc(var(--scale) * 20px); left: calc(var(--scale) * 10px); animation-delay: .6s; }
    #star-4 { width: calc(var(--scale) * 18px); top: 0; left: calc(var(--scale) * 18px); animation-delay: 1.3s; }
    @keyframes star-twinkle {
      0% { transform: scale(1) } 40% { transform: scale(1.2) } 80% { transform: scale(.8) } 100% { transform: scale(1) }
    }
  </style>
</head>

<body data-theme="dark">
  <!-- ======= THEME TOGGLE (top-left) ======= -->
  <div class="theme-toggle" aria-label="Cambiar tema">
    <label class="switch">
      <input id="theme-toggle-input" type="checkbox" checked />
      <div class="slider round">
        <div class="sun-moon">
          <svg id="moon-dot-1" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="moon-dot-2" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="moon-dot-3" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="light-ray-1" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="light-ray-2" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="light-ray-3" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-1" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-2" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-3" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-4" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-5" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
          <svg id="cloud-6" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
        </div>
        <div class="stars">
          <svg id="star-1" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"/></svg>
          <svg id="star-2" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"/></svg>
          <svg id="star-3" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"/></svg>
          <svg id="star-4" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"/></svg>
        </div>
      </div>
    </label>
  </div>

  <!-- ======= LAYER WEBGL ======= -->
  <section class="section hero-section">
    <div id="container"></div>
    <div id="stats"></div>

    <!-- Header / Logo -->
    <div class="header-area">
      <div class="logo-container" title="Capy Ventas">
        <div class="logo-circles">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
        </div>
      </div>
      <div class="center-logo">
        <h1 id="logo-text">Capy Ventas</h1>
      </div>
    </div>

    <!-- Hero -->
    <div class="hero">
      <h1>El sistema simple para<br/>vender más y mejor</h1>
      <h2 id="story-text">
        nuestra nave está en (0.00, 0.00)<br/>
        el campo gravitacional se extiende 0.10 unidades<br/>
        fusionándose con 0 entidades
      </h2>
      <div class="button-group">
        <a href="./im/login" class="btn btn--secondary">Administración</a>
        <a href="./pos/login" class="btn">Capy ventas</a>
      </div>
    </div>

    <!-- Contacto -->
    <div class="contact-info">
      <p class="contact-heading">+Contacto</p>
      <span class="contact-email">soporte@capyventas.app</span>
    </div>

    <!-- Enlaces pie -->
    <div class="footer-links">
      <a href="#" class="footer-link">Producto</a>
      <a href="#" class="footer-link">Precios</a>
      <a href="#" class="footer-link">Documentación</a>
      <a href="#" class="footer-link">Soporte</a>
    </div>

    <div class="coordinates">
      <p>Capy Ventas • Activo</p>
      <p>donde las ventas fluyen</p>
    </div>
  </section>

  <section class="section fin-section">
    <div class="fin-text">{ Fin }</div>
  </section>

  <!-- ======= JS (Módulo) ======= -->
  <script type="module">
    import * as THREE from "https://esm.sh/three@0.178.0";

    let scene, camera, renderer, material;
    let clock;
    let cursorSphere3D = new THREE.Vector3(0, 0, 0);
    let activeMerges = 0;
    let targetMousePosition = new THREE.Vector2(0.5, 0.5);
    let mousePosition = new THREE.Vector2(0.5, 0.5);
    let lastTime = performance.now();
    let frameCount = 0;
    let fps = 0;

    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const isLowPowerDevice = isMobile || (navigator.hardwareConcurrency || 4) <= 4;
    const devicePixelRatioCap = Math.min(window.devicePixelRatio || 1, isMobile ? 1.5 : 2);

    /* ====== Presets para tema ====== */
    const shaderPresets = {
      dark: {
        ambientIntensity: 0.12,
        diffuseIntensity: 1.2,
        specularIntensity: 2.2,
        specularPower: 3,
        fresnelPower: 0.8,
        backgroundColor: new THREE.Color(0x0a0a15),
        sphereColor:     new THREE.Color(0x050510),
        lightColor:      new THREE.Color(0xccaaff),
        lightPosition:   new THREE.Vector3(0.9, 0.9, 1.2),
        smoothness: 0.8,
        contrast: 1.6,
        fogDensity: 0.06,
        cursorGlowIntensity: 1.2,
        cursorGlowRadius: 2.2,
        cursorGlowColor: new THREE.Color(0xaa77ff),
        sphereCount: isMobile ? 4 : 6
      },
      light: {
        ambientIntensity: 0.18,
        diffuseIntensity: 0.9,
        specularIntensity: 1.6,
        specularPower: 5,
        fresnelPower: 1.1,
        backgroundColor: new THREE.Color(0xf7faff),
        sphereColor:     new THREE.Color(0xeaf1ff),
        lightColor:      new THREE.Color(0x88aaff),
        lightPosition:   new THREE.Vector3(0.8, 1.0, 0.8),
        smoothness: 0.7,
        contrast: 1.25,
        fogDensity: 0.04,
        cursorGlowIntensity: 0.85,
        cursorGlowRadius: 1.8,
        cursorGlowColor: new THREE.Color(0x66ccff),
        sphereCount: isMobile ? 4 : 6
      }
    };

    const settings = {
      fixedTopLeftRadius: 0.8,
      fixedBottomRightRadius: 0.9,
      smallTopLeftRadius: 0.3,
      smallBottomRightRadius: 0.35,
      cursorRadiusMin: 0.08,
      cursorRadiusMax: 0.15,
      animationSpeed: 0.6,
      movementScale: 1.2,
      mouseSmoothness: 0.1,
      mergeDistance: 1.5,
      mouseProximityEffect: true,
      minMovementScale: 0.3,
      maxMovementScale: 1.0
    };

    function getStoryText(x, y, radius, merges, fps) {
      if (isMobile) {
        return `nave: (${x}, ${y})<br>campo: ${radius}u<br>fusiones: ${merges}<br>flujo: ${fps}hz`;
      }
      return `nuestra nave está en (${x}, ${y})<br>el campo gravitacional se extiende ${radius} unidades<br>fusionándose con ${merges} entidades<br>flujo temporal: ${fps} ciclos/seg`;
    }

    init();
    animate();

    function init() {
      const container = document.getElementById("container");
      scene = new THREE.Scene();
      camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 10);
      camera.position.z = 1;
      clock = new THREE.Clock();

      renderer = new THREE.WebGLRenderer({
        antialias: !isMobile && !isLowPowerDevice,
        alpha: true,
        powerPreference: isMobile ? "default" : "high-performance",
        preserveDrawingBuffer: false,
        premultipliedAlpha: false
      });

      const pixelRatio = devicePixelRatioCap;
      renderer.setPixelRatio(pixelRatio);
      const viewportWidth = window.innerWidth;
      const viewportHeight = window.innerHeight;
      renderer.setSize(viewportWidth, viewportHeight);
      renderer.setClearColor(0x000000, 0);
      renderer.outputColorSpace = THREE.SRGBColorSpace;

      const canvas = renderer.domElement;
      canvas.style.cssText = `
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 0 !important;
        display: block !important;
      `;
      container.appendChild(canvas);

      material = new THREE.ShaderMaterial({
        uniforms: {
          uTime: { value: 0 },
          uResolution: { value: new THREE.Vector2(viewportWidth, viewportHeight) },
          uActualResolution: { value: new THREE.Vector2(viewportWidth * pixelRatio, viewportHeight * pixelRatio) },
          uPixelRatio: { value: pixelRatio },
          uMousePosition: { value: new THREE.Vector2(0.5, 0.5) },
          uCursorSphere: { value: new THREE.Vector3(0, 0, 0) },
          uCursorRadius: { value: settings.cursorRadiusMin },
          uSphereCount: { value: shaderPresets.dark.sphereCount },
          uFixedTopLeftRadius: { value: settings.fixedTopLeftRadius },
          uFixedBottomRightRadius: { value: settings.fixedBottomRightRadius },
          uSmallTopLeftRadius: { value: settings.smallTopLeftRadius },
          uSmallBottomRightRadius: { value: settings.smallBottomRightRadius },
          uMergeDistance: { value: settings.mergeDistance },
          uSmoothness: { value: 0.8 },
          uAmbientIntensity: { value: 0.12 },
          uDiffuseIntensity: { value: 1.2 },
          uSpecularIntensity: { value: 2.2 },
          uSpecularPower: { value: 3 },
          uFresnelPower: { value: 0.8 },
          uBackgroundColor: { value: shaderPresets.dark.backgroundColor.clone() },
          uSphereColor:     { value: shaderPresets.dark.sphereColor.clone() },
          uLightColor:      { value: shaderPresets.dark.lightColor.clone() },
          uLightPosition:   { value: shaderPresets.dark.lightPosition.clone() },
          uContrast: { value: 1.6 },
          uFogDensity: { value: 0.06 },
          uAnimationSpeed: { value: settings.animationSpeed },
          uMovementScale: { value: settings.movementScale },
          uMouseProximityEffect: { value: true },
          uMinMovementScale: { value: settings.minMovementScale },
          uMaxMovementScale: { value: settings.maxMovementScale },
          uCursorGlowIntensity: { value: shaderPresets.dark.cursorGlowIntensity },
          uCursorGlowRadius: { value: shaderPresets.dark.cursorGlowRadius },
          uCursorGlowColor: { value: shaderPresets.dark.cursorGlowColor.clone() },
          uIsSafari: { value: isSafari ? 1.0 : 0.0 },
          uIsMobile: { value: isMobile ? 1.0 : 0.0 },
          uIsLowPower: { value: isLowPowerDevice ? 1.0 : 0.0 }
        },
        vertexShader: `
          varying vec2 vUv;
          void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }
        `,
        fragmentShader: `
          ${isMobile || isSafari || isLowPowerDevice ? "precision mediump float;" : "precision highp float;"}
          uniform float uTime;
          uniform vec2 uResolution;
          uniform vec2 uActualResolution;
          uniform float uPixelRatio;
          uniform vec2 uMousePosition;
          uniform vec3 uCursorSphere;
          uniform float uCursorRadius;
          uniform int uSphereCount;
          uniform float uFixedTopLeftRadius;
          uniform float uFixedBottomRightRadius;
          uniform float uSmallTopLeftRadius;
          uniform float uSmallBottomRightRadius;
          uniform float uMergeDistance;
          uniform float uSmoothness;
          uniform float uAmbientIntensity;
          uniform float uDiffuseIntensity;
          uniform float uSpecularIntensity;
          uniform float uSpecularPower;
          uniform float uFresnelPower;
          uniform vec3 uBackgroundColor;
          uniform vec3 uSphereColor;
          uniform vec3 uLightColor;
          uniform vec3 uLightPosition;
          uniform float uContrast;
          uniform float uFogDensity;
          uniform float uAnimationSpeed;
          uniform float uMovementScale;
          uniform bool uMouseProximityEffect;
          uniform float uMinMovementScale;
          uniform float uMaxMovementScale;
          uniform float uCursorGlowIntensity;
          uniform float uCursorGlowRadius;
          uniform vec3 uCursorGlowColor;
          uniform float uIsSafari;
          uniform float uIsMobile;
          uniform float uIsLowPower;

          varying vec2 vUv;
          const float PI = 3.14159265359;
          const float EPSILON = 0.001;
          const float MAX_DIST = 100.0;

          float smin(float a, float b, float k){ float h = max(k - abs(a - b), 0.0) / k; return min(a, b) - h*h*k*0.25; }
          float sdSphere(vec3 p, float r){ return length(p) - r; }

          vec3 screenToWorld(vec2 normalizedPos){
            vec2 uv = normalizedPos * 2.0 - 1.0;
            uv.x *= uResolution.x / uResolution.y;
            return vec3(uv * 2.0, 0.0);
          }

          float getDistanceToCenter(vec2 pos){
            float dist = length(pos - vec2(0.5, 0.5)) * 2.0;
            return smoothstep(0.0, 1.0, dist);
          }

          float sceneSDF(vec3 pos){
            float result = MAX_DIST;

            vec3 topLeftPos = screenToWorld(vec2(0.08, 0.92));
            float topLeft = sdSphere(pos - topLeftPos, uFixedTopLeftRadius);

            vec3 smallTopLeftPos = screenToWorld(vec2(0.25, 0.72));
            float smallTopLeft = sdSphere(pos - smallTopLeftPos, uSmallTopLeftRadius);

            vec3 bottomRightPos = screenToWorld(vec2(0.92, 0.08));
            float bottomRight = sdSphere(pos - bottomRightPos, uFixedBottomRightRadius);

            vec3 smallBottomRightPos = screenToWorld(vec2(0.72, 0.25));
            float smallBottomRight = sdSphere(pos - smallBottomRightPos, uSmallBottomRightRadius);

            float t = uTime * uAnimationSpeed;

            float dynamicMovementScale = uMovementScale;
            if (uMouseProximityEffect) {
              float distToCenter = getDistanceToCenter(uMousePosition);
              float mixFactor = smoothstep(0.0, 1.0, distToCenter);
              dynamicMovementScale = mix(uMinMovementScale, uMaxMovementScale, mixFactor);
            }

            int maxIter = uIsMobile > 0.5 ? 4 : (uIsLowPower > 0.5 ? 6 : min(uSphereCount, 10));
            for (int i=0; i<10; i++){
              if (i >= uSphereCount || i >= maxIter) break;
              float fi = float(i);
              float speed = 0.4 + fi * 0.12;
              float radius = 0.12 + mod(fi, 3.0) * 0.06;
              float orbitRadius = (0.3 + mod(fi, 3.0) * 0.15) * dynamicMovementScale;
              float phaseOffset = fi * PI * 0.35;

              float distToCursor = length(vec3(0.0) - uCursorSphere);
              float proximityScale = 1.0 + (1.0 - smoothstep(0.0, 1.0, distToCursor)) * 0.5;
              orbitRadius *= proximityScale;

              vec3 offset;
              if (i == 0) {
                offset = vec3(
                  sin(t * speed) * orbitRadius * 0.7,
                  sin(t * 0.5) * orbitRadius,
                  cos(t * speed * 0.7) * orbitRadius * 0.5
                );
              } else if (i == 1) {
                offset = vec3(
                  sin(t * speed + PI) * orbitRadius * 0.5,
                  -sin(t * 0.5) * orbitRadius,
                  cos(t * speed * 0.7 + PI) * orbitRadius * 0.5
                );
              } else {
                offset = vec3(
                  sin(t * speed + phaseOffset) * orbitRadius * 0.8,
                  cos(t * speed * 0.85 + phaseOffset * 1.3) * orbitRadius * 0.6,
                  sin(t * speed * 0.5 + phaseOffset) * 0.3
                );
              }

              vec3 toCursor = uCursorSphere - offset;
              float cursorDist = length(toCursor);
              if (cursorDist < uMergeDistance && cursorDist > 0.0) {
                float attraction = (1.0 - cursorDist / uMergeDistance) * 0.3;
                offset += normalize(toCursor) * attraction;
              }

              float movingSphere = sdSphere(pos - offset, radius);
              float blend = 0.05;
              if (cursorDist < uMergeDistance) {
                float influence = 1.0 - (cursorDist / uMergeDistance);
                blend = mix(0.05, uSmoothness, influence*influence*influence);
              }
              result = smin(result, movingSphere, blend);
            }

            float cursorBall = sdSphere(pos - uCursorSphere, uCursorRadius);

            float topLeftGroup = smin(topLeft, smallTopLeft, 0.4);
            float bottomRightGroup = smin(bottomRight, smallBottomRight, 0.4);

            result = smin(result, topLeftGroup, 0.3);
            result = smin(result, bottomRightGroup, 0.3);
            result = smin(result, cursorBall, uSmoothness);
            return result;
          }

          vec3 calcNormal(vec3 p){
            float eps = uIsLowPower > 0.5 ? 0.002 : 0.001;
            return normalize(vec3(
              sceneSDF(p + vec3(eps,0,0)) - sceneSDF(p - vec3(eps,0,0)),
              sceneSDF(p + vec3(0,eps,0)) - sceneSDF(p - vec3(0,eps,0)),
              sceneSDF(p + vec3(0,0,eps)) - sceneSDF(p - vec3(0,0,eps))
            ));
          }

          float ambientOcclusion(vec3 p, vec3 n){
            if (uIsLowPower > 0.5) {
              float h1 = sceneSDF(p + n * 0.03);
              float h2 = sceneSDF(p + n * 0.06);
              float occ = (0.03 - h1) + (0.06 - h2) * 0.5;
              return clamp(1.0 - occ * 2.0, 0.0, 1.0);
            } else {
              float occ = 0.0; float weight = 1.0;
              for (int i=0; i<6; i++){
                float dist = 0.01 + 0.015 * float(i*i);
                float h = sceneSDF(p + n * dist);
                occ += (dist - h) * weight; weight *= 0.85;
              }
              return clamp(1.0 - occ, 0.0, 1.0);
            }
          }

          float softShadow(vec3 ro, vec3 rd, float mint, float maxt, float k){
            if (uIsLowPower > 0.5) {
              float result = 1.0; float t = mint;
              for (int i=0; i<3; i++){
                t += 0.3; if (t >= maxt) break;
                float h = sceneSDF(ro + rd*t); if (h < EPSILON) return 0.0;
                result = min(result, k*h/t);
              } return result;
            } else {
              float result = 1.0; float t = mint;
              for (int i=0; i<20; i++){
                if (t >= maxt) break;
                float h = sceneSDF(ro + rd*t); if (h < EPSILON) return 0.0;
                result = min(result, k*h/t); t += h;
              } return result;
            }
          }

          float rayMarch(vec3 ro, vec3 rd){
            float t = 0.0;
            int maxSteps = uIsMobile > 0.5 ? 16 : (uIsSafari > 0.5 ? 16 : 48);
            for (int i=0; i<48; i++){
              if (i >= maxSteps) break;
              vec3 p = ro + rd*t;
              float d = sceneSDF(p);
              if (d < EPSILON) return t;
              if (t > 5.0) break;
              t += d * (uIsLowPower > 0.5 ? 1.2 : 0.9);
            }
            return -1.0;
          }

          vec3 lighting(vec3 p, vec3 rd, float t){
            if (t < 0.0) return vec3(0.0);
            vec3 n = calcNormal(p);
            vec3 viewDir = -rd;
            vec3 baseColor = uSphereColor;

            float ao = ambientOcclusion(p, n);
            vec3 ambient = uLightColor * uAmbientIntensity * ao;

            vec3 lightDir = normalize(uLightPosition);
            float diff = max(dot(n, lightDir), 0.0);
            float shadow = softShadow(p, lightDir, 0.01, 10.0, 20.0);
            vec3 diffuse = uLightColor * diff * uDiffuseIntensity * shadow;

            vec3 reflectDir = reflect(-lightDir, n);
            float spec = pow(max(dot(viewDir, reflectDir), 0.0), uSpecularPower);
            float fresnel = pow(1.0 - max(dot(viewDir, n), 0.0), uFresnelPower);
            vec3 specular = uLightColor * spec * uSpecularIntensity * fresnel;
            vec3 rim = uLightColor * fresnel * 0.4;

            float distToCursor = length(p - uCursorSphere);
            if (distToCursor < uCursorRadius + 0.4) {
              float highlight = 1.0 - smoothstep(0.0, uCursorRadius + 0.4, distToCursor);
              specular += uLightColor * highlight * 0.2;
              float glow = exp(-distToCursor * 3.0) * 0.15;
              ambient += uLightColor * glow * 0.5;
            }

            vec3 color = (baseColor + ambient + diffuse + specular + rim) * ao;
            color = pow(color, vec3(uContrast * 0.9));
            color = color / (color + vec3(0.8));
            return color;
          }

          float calculateCursorGlow(vec3 worldPos){
            float dist = length(worldPos.xy - uCursorSphere.xy);
            float glow = 1.0 - smoothstep(0.0, uCursorGlowRadius, dist);
            glow = pow(glow, 2.0);
            return glow * uCursorGlowIntensity;
          }

          void main(){
            vec2 uv = (gl_FragCoord.xy * 2.0 - uActualResolution.xy) / uActualResolution.xy;
            uv.x *= uResolution.x / uResolution.y;
            vec3 ro = vec3(uv * 2.0, -1.0);
            vec3 rd = vec3(0.0, 0.0, 1.0);

            float t = rayMarch(ro, rd);
            vec3 p = ro + rd * t;
            vec3 color = lighting(p, rd, t);

            float cursorGlow = calculateCursorGlow(ro);
            vec3 glowContribution = uCursorGlowColor * cursorGlow;

            if (t > 0.0) {
              float fogAmount = 1.0 - exp(-t * uFogDensity);
              color = mix(color, uBackgroundColor.rgb, fogAmount * 0.3);
              color += glowContribution * 0.3;
              gl_FragColor = vec4(color, 1.0);
            } else {
              if (cursorGlow > 0.01) gl_FragColor = vec4(glowContribution, cursorGlow * 0.8);
              else gl_FragColor = vec4(0.0, 0.0, 0.0, 0.0);
            }
          }
        `,
        transparent: true
      });

      const geometry = new THREE.PlaneGeometry(2, 2);
      scene.add(new THREE.Mesh(geometry, material));

      setupEventListeners();
      onPointerMove({ clientX: window.innerWidth / 2, clientY: window.innerHeight / 2 });

      // Tema inicial (oscuro)
      applyTheme('dark');
    }

    function setupEventListeners() {
      window.addEventListener("mousemove", onPointerMove, { passive: true });
      window.addEventListener("touchstart", onTouchStart, { passive: false });
      window.addEventListener("touchmove", onTouchMove, { passive: false });
      window.addEventListener("touchend", onTouchEnd, { passive: false });
      window.addEventListener("resize", onWindowResize, { passive: true });
      window.addEventListener("orientationchange", () => setTimeout(onWindowResize, 100), { passive: true });

      const input = document.getElementById('theme-toggle-input');
      input.addEventListener('change', () => {
        const theme = input.checked ? 'dark' : 'light';
        applyTheme(theme);
      });

      // Copiar correo
      const emailLink = document.querySelector(".contact-email");
      if (emailLink) {
        const originalText = emailLink.textContent;
        const email = "soporte@capyventas.app";
        emailLink.addEventListener("click", (e) => {
          e.preventDefault();
          navigator.clipboard.writeText(email).then(() => {
            emailLink.textContent = "copiado al portapapeles";
            setTimeout(() => { emailLink.textContent = originalText; }, 2000);
          }).catch(() => { window.location.href = "mailto:" + email; });
        });
      }
    }

    /* ====== Theme Switch: CSS + Shader uniforms ====== */
    function applyTheme(theme) {
      document.body.setAttribute('data-theme', theme);
      const preset = theme === 'light' ? shaderPresets.light : shaderPresets.dark;

      material.uniforms.uSphereCount.value      = preset.sphereCount;
      material.uniforms.uAmbientIntensity.value = preset.ambientIntensity;
      material.uniforms.uDiffuseIntensity.value = preset.diffuseIntensity;
      material.uniforms.uSpecularIntensity.value= preset.specularIntensity;
      material.uniforms.uSpecularPower.value    = preset.specularPower;
      material.uniforms.uFresnelPower.value     = preset.fresnelPower;
      material.uniforms.uBackgroundColor.value  = preset.backgroundColor;
      material.uniforms.uSphereColor.value      = preset.sphereColor;
      material.uniforms.uLightColor.value       = preset.lightColor;
      material.uniforms.uLightPosition.value    = preset.lightPosition;
      material.uniforms.uSmoothness.value       = preset.smoothness;
      material.uniforms.uContrast.value         = preset.contrast;
      material.uniforms.uFogDensity.value       = preset.fogDensity;
      material.uniforms.uCursorGlowIntensity.value = preset.cursorGlowIntensity;
      material.uniforms.uCursorGlowRadius.value = preset.cursorGlowRadius;
      material.uniforms.uCursorGlowColor.value  = preset.cursorGlowColor;
    }

    function onTouchStart(event) {
      event.preventDefault();
      if (event.touches.length > 0) {
        const t = event.touches[0];
        onPointerMove({ clientX: t.clientX, clientY: t.clientY });
      }
    }
    function onTouchMove(event) {
      event.preventDefault();
      if (event.touches.length > 0) {
        const t = event.touches[0];
        onPointerMove({ clientX: t.clientX, clientY: t.clientY });
      }
    }
    function onTouchEnd(event) { event.preventDefault(); }

    function screenToWorldJS(normalizedX, normalizedY) {
      const uv_x = normalizedX * 2.0 - 1.0;
      const uv_y = normalizedY * 2.0 - 1.0;
      const aspect = window.innerWidth / window.innerHeight;
      return new THREE.Vector3(uv_x * aspect * 2.0, uv_y * 2.0, 0.0);
    }

    function onPointerMove(event) {
      targetMousePosition.x = event.clientX / window.innerWidth;
      targetMousePosition.y = 1.0 - event.clientY / window.innerHeight;

      const worldPos = screenToWorldJS(targetMousePosition.x, targetMousePosition.y);
      cursorSphere3D.copy(worldPos);

      let closestDistance = 1000.0;
      activeMerges = 0;

      const fixedPositions = [
        screenToWorldJS(0.08, 0.92),
        screenToWorldJS(0.25, 0.72),
        screenToWorldJS(0.92, 0.08),
        screenToWorldJS(0.72, 0.25)
      ];

      fixedPositions.forEach((pos) => {
        const dist = cursorSphere3D.distanceTo(pos);
        closestDistance = Math.min(closestDistance, dist);
        if (dist < 1.5) activeMerges++;
      });

      const proximityFactor = Math.max(0, 1.0 - closestDistance / 1.5);
      const smoothFactor = proximityFactor * proximityFactor * (3.0 - 2.0 * proximityFactor);
      const dynamicRadius = 0.08 + (0.15 - 0.08) * smoothFactor;

      material.uniforms.uCursorSphere.value.copy(cursorSphere3D);
      material.uniforms.uCursorRadius.value = dynamicRadius;

      updateStory(cursorSphere3D.x, cursorSphere3D.y, dynamicRadius, activeMerges, fps);
    }

    function updateStory(x, y, radius, merges, fpsVal) {
      const storyText = document.getElementById("story-text");
      if (storyText) {
        storyText.innerHTML = getStoryText(x.toFixed(2), y.toFixed(2), radius.toFixed(2), merges, fpsVal || 0);
      }
    }

    function onWindowResize() {
      const width = window.innerWidth;
      const height = window.innerHeight;
      const currentPixelRatio = devicePixelRatioCap;

      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
      renderer.setPixelRatio(currentPixelRatio);

      material.uniforms.uResolution.value.set(width, height);
      material.uniforms.uActualResolution.value.set(width * currentPixelRatio, height * currentPixelRatio);
      material.uniforms.uPixelRatio.value = currentPixelRatio;

      const canvas = renderer.domElement;
      canvas.style.cssText = `
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 0 !important;
        display: block !important;
      `;
      if (renderer.info) renderer.info.autoReset = true;
    }

    function animate() { requestAnimationFrame(animate); render(); }

    function render() {
      const now = performance.now();
      frameCount++;
      if (now - lastTime >= 1000) {
        fps = Math.round((frameCount * 1000) / (now - lastTime));
        updateStory(cursorSphere3D.x, cursorSphere3D.y, material.uniforms.uCursorRadius.value, activeMerges, fps);
        frameCount = 0; lastTime = now;
      }
      mousePosition.x += (targetMousePosition.x - mousePosition.x) * 0.1;
      mousePosition.y += (targetMousePosition.y - mousePosition.y) * 0.1;

      material.uniforms.uTime.value = clock.getElapsedTime();
      material.uniforms.uMousePosition.value = mousePosition;

      if (performance.now() % 5000 < 16) renderer.renderLists.dispose();
      renderer.render(scene, camera);
    }
  </script>
</body>
</html>
