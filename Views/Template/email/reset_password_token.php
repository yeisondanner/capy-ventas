<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Recuperar cuenta</title>
</head>

<body style="background-color: #f4f4f4; margin: 0; padding: 20px; font-family: Arial, sans-serif; color: #333;">
	<table cellpadding="0" cellspacing="0"
		style="width: 100%; max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
		<!-- ENCABEZADO -->
		<tr>
			<td style="text-align: center; padding: 20px; background-color: #0a4661; color: #ffffff;">
				<!-- Si tienes un logo -->
				<!-- <img src="<?= $data['logo'] ?>" alt="<?= $data['compania'] ?>" style="max-width: 150px; margin-bottom: 10px;"> -->
				<h1 style="margin: 0; font-size: 24px;"><?= htmlspecialchars($data['compania']) ?></h1>
			</td>
		</tr>

		<!-- CONTENIDO -->
		<tr>
			<td style="padding: 20px;">
				<p style="font-size: 15px; margin: 0 0 10px 0;">Hola <strong
						style="color: #244180;"><?= htmlspecialchars($data['nombreUsuario']); ?></strong>,</p>
				<p style="font-size: 15px; margin: 0 0 10px 0;">
					Has solicitado recuperar los datos de tu usuario registrado con el correo
					<strong style="color: #244180;"><?= htmlspecialchars($data['email']); ?></strong>.
				</p>
				<p style="font-size: 15px; margin: 0 0 20px 0;">
					Para confirmar tu identidad y acceder nuevamente a tu cuenta, haz clic en el siguiente botón:
				</p>

				<!-- BOTÓN -->
				<div style="text-align: center; margin: 25px 0;">
					<a href="<?= htmlspecialchars($data['url_recovery']); ?>" target="_blank"
						style="background-color: #307cf4; color: #ffffff; padding: 12px 25px; text-transform: uppercase; font-weight: bold; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 15px;">
						Confirmar datos
					</a>
				</div>

				<!-- URL DE RESPALDO -->
				<p style="font-size: 14px; margin: 0 0 10px 0;">
					Si el botón no funciona, copia y pega la siguiente dirección en tu navegador:
				</p>
				<div
					style="word-break: break-all; background-color: #f9f9f9; padding: 10px; border-radius: 4px; font-size: 13px; color: #555;">
					<?= htmlspecialchars($data['url_recovery']); ?>
				</div>
			</td>
		</tr>

		<!-- PIE DE PÁGINA -->
		<tr>
			<td style="background-color: #f7f7f7; text-align: center; font-size: 13px; padding: 15px; color: #777;">
				<a href="<?= htmlspecialchars($data['url_compania']); ?>" target="_blank"
					style="color: #0a4661; text-decoration: none;">
					<?= htmlspecialchars($data['url_compania']); ?>
				</a>
			</td>
		</tr>
	</table>
</body>

</html>