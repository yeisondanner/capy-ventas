<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $data['page_title'] ?? 'Ruta no encontrada' ?></title>

    <link rel="shortcut icon" href="https://capyventas.shaday-pe.com/Assets/capysm.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
            --border-radius: 24px;
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
            background: linear-gradient(135deg, rgba(35, 67, 106, 0.06), rgba(67, 105, 240, 0.04));
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: var(--color-card);
            border-radius: var(--border-radius);
            padding: clamp(40px, 8vw, 60px);
            box-shadow: var(--shadow-soft);
            text-align: center;
            width: min(100%, 600px);
            border: 1px solid rgba(35, 67, 106, 0.08);
            position: relative;
            overflow: hidden;
            animation: scaleIn 0.5s ease-out forwards;
        }

        .error-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(67, 105, 240, 0.1), transparent 60%);
            opacity: 0.8;
            pointer-events: none;
        }

        .error-icon {
            font-size: clamp(4rem, 10vw, 5rem);
            color: var(--color-primary);
            margin-bottom: 24px;
            display: inline-block;
            animation: bounce 3s infinite ease-in-out;
            filter: drop-shadow(0 10px 15px rgba(67, 105, 240, 0.2));
        }

        .content-box {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards 0.3s;
        }

        .error-title {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 16px;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .error-message {
            font-size: clamp(1rem, 3vw, 1.1rem);
            color: var(--color-muted);
            margin-bottom: 32px;
            line-height: 1.6;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background: linear-gradient(120deg, var(--color-primary), #24d7b1);
            color: #fff;
            box-shadow: 0 14px 30px rgba(67, 105, 240, 0.25);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(67, 105, 240, 0.35);
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }
    </style>
</head>

<body>

    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-signpost-split-fill"></i>
        </div>
        <div class="content-box">
            <h1 class="error-title">
                <?= $data['page_title'] ?? '¿Te has perdido?' ?>
            </h1>
            <p class="error-message">
                <?= $data['page_description'] ?? 'Parece que este camino no lleva a ninguna parte. No te preocupes, todos nos perdemos a veces. Vamos a buscar el rumbo correcto.' ?>
            </p>
            <a href="<?= base_url(); ?>" class="btn">
                <i class="bi bi-house-door-fill"></i>
                Regresar a casa
            </a>
        </div>
    </div>

</body>

</html>