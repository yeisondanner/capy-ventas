<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capy Ventas - Inicio</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        h1 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Capy Ventas</h1>
        <div class="button-group">
            <a href="./im/login" class="btn">Administracion</a>
            <a href="./pos/login" class="btn">Capy ventas</a>
        </div>
    </div>

</body>

</html>