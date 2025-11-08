<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación</title>
</head>

<body style="background-color: #f4f4f4; margin: 0; padding: 20px; font-family: Arial, sans-serif; color: #333;">
    <table cellpadding="0" cellspacing="0"
        style="width: 100%; max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">

        <!-- ENCABEZADO -->
        <tr>
            <td style="text-align: center; padding: 20px; background-color: #0a4661; color: #ffffff;">
                <h1 style="margin: 0; font-size: 22px;"><?= htmlspecialchars($data['titulo']); ?></h1>
            </td>
        </tr>

        <!-- CONTENIDO -->
        <tr>
            <td style="padding: 20px;">
                <p style="font-size: 15px; margin: 0 0 10px 0;">
                    Hola <strong style="color: #244180;"><?= htmlspecialchars($data['nombres']); ?></strong>,
                </p>
                <p style="font-size: 15px; margin: 0 0 15px 0;">
                    <?= nl2br(htmlspecialchars($data['descripcion'])); ?>
                </p>
                <?php if(!empty($data['enlace'])) {  ?>
                <!-- BOTÓN DE ENLACE -->
                <div style="text-align: center; margin: 25px 0;">
                    <a href="<?= htmlspecialchars($data['enlace']); ?>" target="_blank"
                        style="background-color: #307cf4; color: #ffffff; padding: 12px 25px; text-transform: uppercase; font-weight: bold; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 15px;">
                        Ver más
                    </a>
                </div>

                <!-- URL DE RESPALDO -->
                <p style="font-size: 14px; margin: 0 0 10px 0;">
                    Si el botón no funciona, copia y pega la siguiente dirección en tu navegador:
                </p>
                <div
                    style="word-break: break-all; background-color: #f9f9f9; padding: 10px; border-radius: 4px; font-size: 13px; color: #555;">
                    <?= htmlspecialchars($data['enlace']); ?>
                </div>
                <?php } ?>
            </td>
        </tr>

        <!-- PIE DE PÁGINA -->
        <tr>
            <td style="background-color: #f7f7f7; text-align: center; font-size: 12px; padding: 15px; color: #777;">
                © <?= date('Y'); ?> - Todos los derechos reservados.
            </td>
        </tr>
    </table>
</body>

</html>