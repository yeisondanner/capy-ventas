<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Convertidor de Moneda</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      max-width: 400px;
      margin: auto;
    }

    input,
    select,
    button {
      padding: 8px;
      margin: 5px 0;
      width: 100%;
    }

    #resultado {
      margin-top: 15px;
      font-weight: bold;
      color: darkgreen;
    }
  </style>
</head>

<body>
  <h2>Convertidor de Moneda</h2>
  <input type="number" id="cantidad" placeholder="Cantidad" value="1" />
  <select id="de">
    <option value="USD">USD - Dólar</option>
    <option value="EUR">EUR - Euro</option>
    <option value="GBP">GBP - Libra</option>
    <option value="PEN">PEN - Sol Peruano</option>
  </select>
  <select id="a">
    <option value="PEN">PEN - Sol Peruano</option>
    <option value="USD">USD - Dólar</option>
    <option value="EUR">EUR - Euro</option>
    <option value="GBP">GBP - Libra</option>
  </select>
  <button id="convertir">Convertir</button>
  <div id="resultado"></div>

  <script>
    document.getElementById('convertir').addEventListener('click', async () => {
      const amount = document.getElementById('cantidad').value;
      const from = document.getElementById('de').value;
      const to = document.getElementById('a').value;

      const url = `https://api.exchangerate.host/convert?from=${from}&to=${to}&amount=${amount}`;

      try {
        const resp = await fetch(url);
        if (!resp.ok) throw new Error('Error en API');
        const data = await resp.json();

        document.getElementById('resultado').textContent =
          `${amount} ${from} = ${data.result.toFixed(2)} ${to}`;
      } catch (err) {
        document.getElementById('resultado').textContent =
          '⚠ Error: no se pudo conectar a la API.';
        console.error(err);
      }
    });
  </script>
</body>

</html>