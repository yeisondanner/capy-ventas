<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Ups — Algo salió mal</title>

    <!-- Bootstrap 4.0 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" crossorigin="anonymous">

    <style>
        :root {
            --bg: #0d1117;
            --text: #e8ecf5;
            --primary: #4f46e5;
        }

        [data-theme="light"] {
            --bg: #f6f7fb;
            --text: #0b1220;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            flex-direction: column;
            text-align: center;
        }

        img.capi {
            width: 220px;
            height: auto;
            margin-bottom: 20px;
            user-select: none;
        }

        h1 {
            font-size: 3.8rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2rem;
            color: #9aa3b2;
            max-width: 480px;
            margin: 0 auto 30px;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), #6d28d9);
            border: none;
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, .35);
            color: var(--text);
        }

        /* ===== Custom Theme Switch ===== */
        .theme-toggle {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 3;
            user-select: none;
        }

        :root {
            --scale: 1.2;
            color-scheme: light;
        }

        :root:has(#input:checked) {
            color-scheme: dark;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: calc(var(--scale)*60px);
            height: calc(var(--scale)*34px);
        }

        .switch #input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #2196f3;
            transition: .4s;
            z-index: 0;
            overflow: hidden;
            border-radius: calc(var(--scale)*34px);
        }

        .sun-moon {
            position: absolute;
            height: calc(var(--scale)*26px);
            width: calc(var(--scale)*26px);
            left: calc(var(--scale)*4px);
            bottom: calc(var(--scale)*4px);
            background-color: yellow;
            transition: .4s;
            border-radius: 50%;
        }

        #input:checked+.slider {
            background-color: black;
        }

        #input:focus+.slider {
            box-shadow: 0 0 calc(var(--scale)*1px) #2196f3;
        }

        #input:checked+.slider .sun-moon {
            transform: translateX(calc(var(--scale)*26px));
            background-color: white;
            animation: rotate-center .6s ease-in-out both;
        }

        @keyframes rotate-center {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        .moon-dot {
            opacity: 0;
            transition: .4s;
            fill: gray;
        }

        #input:checked+.slider .sun-moon .moon-dot {
            opacity: 1;
        }

        .light-ray {
            position: absolute;
            z-index: -1;
            fill: #fff;
            opacity: .1;
        }

        #light-ray-1 {
            left: calc(var(--scale)*-8px);
            top: calc(var(--scale)*-8px);
            width: calc(var(--scale)*43px);
            height: calc(var(--scale)*43px);
        }

        #light-ray-2 {
            left: -50%;
            top: -50%;
            width: calc(var(--scale)*55px);
            height: calc(var(--scale)*55px);
        }

        #light-ray-3 {
            left: calc(var(--scale)*-18px);
            top: calc(var(--scale)*-18px);
            width: calc(var(--scale)*60px);
            height: calc(var(--scale)*60px);
        }

        .cloud-light,
        .cloud-dark {
            position: absolute;
            animation: cloud-move 6s infinite;
        }

        .cloud-light {
            fill: #eee;
        }

        .cloud-dark {
            fill: #ccc;
            animation-delay: 1s;
        }

        @keyframes cloud-move {
            0% {
                transform: translateX(0)
            }

            40% {
                transform: translateX(calc(var(--scale)*4px))
            }

            80% {
                transform: translateX(calc(var(--scale)*-4px))
            }

            100% {
                transform: translateX(0)
            }
        }

        .stars {
            transform: translateY(calc(var(--scale)*-32px));
            opacity: 0;
            transition: .4s;
        }

        .star {
            fill: #fff;
            position: absolute;
            transition: .4s;
            animation: star-twinkle 2s infinite;
        }

        #input:checked+.slider .stars {
            transform: translateY(0);
            opacity: 1;
        }

        #star-1 {
            width: calc(var(--scale)*20px);
            top: calc(var(--scale)*2px);
            left: calc(var(--scale)*3px);
            animation-delay: .3s;
        }

        #star-2 {
            width: calc(var(--scale)*6px);
            top: calc(var(--scale)*16px);
            left: calc(var(--scale)*3px);
        }

        #star-3 {
            width: calc(var(--scale)*12px);
            top: calc(var(--scale)*20px);
            left: calc(var(--scale)*10px);
            animation-delay: .6s;
        }

        #star-4 {
            width: calc(var(--scale)*18px);
            top: 0;
            left: calc(var(--scale)*18px);
            animation-delay: 1.3s;
        }

        @keyframes star-twinkle {
            0% {
                transform: scale(1)
            }

            40% {
                transform: scale(1.2)
            }

            80% {
                transform: scale(.8)
            }

            100% {
                transform: scale(1)
            }
        }
    </style>
</head>

<body>

    <div class="theme-toggle">
        <label class="switch">
            <input id="input" type="checkbox" checked="darkTheme" />
            <div class="slider round">
                <div class="sun-moon">
                    <svg id="moon-dot-1" class="moon-dot" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="moon-dot-2" class="moon-dot" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="moon-dot-3" class="moon-dot" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="light-ray-1" class="light-ray" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="light-ray-2" class="light-ray" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="light-ray-3" class="light-ray" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-1" class="cloud-dark" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-2" class="cloud-dark" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-3" class="cloud-dark" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-4" class="cloud-light" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-5" class="cloud-light" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                    <svg id="cloud-6" class="cloud-light" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50"></circle>
                    </svg>
                </div>
                <div class="stars">
                    <svg id="star-1" class="star" viewBox="0 0 20 20">
                        <path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path>
                    </svg>
                    <svg id="star-2" class="star" viewBox="0 0 20 20">
                        <path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path>
                    </svg>
                    <svg id="star-3" class="star" viewBox="0 0 20 20">
                        <path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path>
                    </svg>
                    <svg id="star-4" class="star" viewBox="0 0 20 20">
                        <path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path>
                    </svg>
                </div>
            </div>
        </label>
    </div>

    <!-- Imagen -->
    <img src="<?php echo base_url(); ?> /Assets/capi_mariado.png" alt="Capi mariado" class="capi">
    <h1>Ups, algo salió mal</h1>
    <p>No es tu culpa. Puede ser un problema temporal del servidor o de conexión.</p>

    <!-- Video -->
    <video class="capi" autoplay muted loop playsinline>
        <source src="<?php echo base_url(); ?> /Assets/capibara_pixel.mp4" type="video/mp4">
        Tu navegador no soporta videos HTML5.
    </video>

    <div class="d-flex flex-wrap justify-content-center">
        <button class="btn btn-primary mr-2 mb-2" id="btnRetry">Reintentar</button>
        <a href="#" class="btn btn-outline-light mr-2 mb-2" id="btnHome">Ir al inicio</a>
        <button class="btn btn-outline-light mb-2" data-toggle="modal" data-target="#reportModal">Reportar problema</button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Reportar problema</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <div class="form-group">
                            <label for="reportDesc">¿Qué pasó?</label>
                            <textarea class="form-control" id="reportDesc" rows="4" placeholder="Cuéntanos brevemente…"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="sendReport">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        (function() {
            var root = document.documentElement;
            var themeInput = document.getElementById('input');
            var saved = localStorage.getItem('theme');
            if (saved === 'light') {
                root.setAttribute('data-theme', 'light');
                themeInput.checked = false;
            } else {
                root.removeAttribute('data-theme');
                themeInput.checked = true;
            }
            themeInput.addEventListener('change', function() {
                if (this.checked) {
                    root.removeAttribute('data-theme');
                    localStorage.setItem('theme', 'dark');
                } else {
                    root.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                }
            });

            document.getElementById('btnHome').addEventListener('click', function(e) {
                e.preventDefault();
                location.href = '/';
            });
            document.getElementById('btnRetry').addEventListener('click', function() {
                location.reload();
            });

            document.getElementById('sendReport').addEventListener('click', function() {
                $('#reportModal').modal('hide');
                alert('Gracias, tu reporte ha sido enviado.');
            });
        })();
    </script>
</body>

</html>