<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación - CapyVentas</title>
</head>

<body style="background-color: #f4f4f4; margin: 0; padding: 20px; font-family: Arial, sans-serif; color: #333;">
    <table cellpadding="0" cellspacing="0"
        style="width: 100%; max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">

        <tr>
            <td style="text-align: center; padding: 30px 20px; background-color: #4369F0; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px; font-weight: bold;"><?= htmlspecialchars($data['titulo']); ?></h1>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; margin: 0 0 15px 0; color: #555;">
                    Hola <strong style="color: #4369F0;"><?= htmlspecialchars($data['nombres']); ?></strong>,
                </p>
                
                <p style="font-size: 15px; line-height: 1.6; margin: 0 0 25px 0; color: #666;">
                    <?= nl2br(htmlspecialchars($data['descripcion'])); ?>
                </p>

                <div style="margin: 30px 0; text-align: center;">
                    <div style="background-color: #FFF9E6; border: 2px dashed #FDC346; color: #4369F0; font-size: 38px; font-weight: bold; letter-spacing: 12px; padding: 25px 10px; border-radius: 12px; display: inline-block; min-width: 200px;">
                        <?= htmlspecialchars($data['codigo']); ?>
                    </div>
                    <p style="font-size: 13px; color: #999; margin-top: 15px;">
                        Este código es válido por 10 minutos.
                    </p>
                </div>

                <p style="font-size: 14px; color: #888; margin: 0; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                    Si no has solicitado crear una cuenta en <strong>CapyVentas</strong>, por favor ignora este correo.
                </p>
            </td>
        </tr>

        <tr>
            <td style="background-color: #f8f9fa; text-align: center; font-size: 12px; padding: 20px; color: #999;">
                © <?= date('Y'); ?> CapyVentas. Todos los derechos reservados.
            </td>
        </tr>
    </table>
</body>

</html>