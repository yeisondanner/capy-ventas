<?php
/**
 * Clase sendMail
 * -------------------
 * Encapsula y administra el envío de correos electrónicos usando PHPMailer sin Composer.
 * Toda la configuración y parámetros de envío se manejan mediante arrays, lo que
 * permite alta flexibilidad en producción.
 *
 * Requisitos:
 *  - PHPMailer descargado y ubicado en ./Libraries/PHPMailer/src/
 *  - Servidor SMTP activo con credenciales válidas.
 *
 * Ejemplo de uso:
 *  $config = [
 *      'smtp' => [
 *          'host'       => 'mail.shaday-pe.com',
 *          'username'   => 'pureba@shaday-pe.com',
 *          'password'   => 'X5XFy46Qp?g_',
 *          'port'       => 465,
 *          'encryption' => 'ssl'
 *      ],
 *      'from' => [
 *          'email' => 'pureba@shaday-pe.com',
 *          'name'  => 'Nombre Remitente'
 *      ]
 *  ];
 *
 *  $correo = new sendMail($config);
 *  $result = $correo->send([
 *      'to'          => ['destino1@correo.com', 'destino2@correo.com'],
 *      'subject'     => 'Asunto de prueba',
 *      'body'        => '<h1>Hola</h1><p>Correo de prueba.</p>',
 *      'attachments' => ['./documentos/manual.pdf']
 *  ]);
 *
 *  if ($result === true) {
 *      echo "✅ Correo enviado correctamente";
 *  } else {
 *      echo $result; // Muestra el error
 *  }
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir PHPMailer sin Composer
require_once './Libraries/PHPMailer/src/PHPMailer.php';
require_once './Libraries/PHPMailer/src/SMTP.php';
require_once './Libraries/PHPMailer/src/Exception.php';

class sendMail
{
    /** @var PHPMailer */
    private $mail;

    /** @var array Configuración SMTP y remitente */
    private $config;

    /**
     * Constructor: recibe un array con la configuración SMTP y del remitente.
     *
     * @param array $config
     * Ejemplo:
     * [
     *   'smtp' => [
     *       'host'       => 'mail.shaday-pe.com',
     *       'username'   => 'pureba@shaday-pe.com',
     *       'password'   => 'X5XFy46Qp?g_',
     *       'port'       => 465,
     *       'encryption' => 'ssl' // ssl o tls
     *   ],
     *   'from' => [
     *       'email' => 'pureba@shaday-pe.com',
     *       'name'  => 'Nombre Remitente'
     *   ]
     * ]
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->mail = new PHPMailer(true);

        // Configuración general SMTP
        $this->mail->isSMTP();
        $this->mail->Host = $this->config['smtp']['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->config['smtp']['username'];
        $this->mail->Password = $this->config['smtp']['password'];
        $this->mail->Port = $this->config['smtp']['port'];

        // Seguridad
        if (isset($this->config['smtp']['encryption'])) {
            if (strtolower($this->config['smtp']['encryption']) === 'ssl') {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif (strtolower($this->config['smtp']['encryption']) === 'tls') {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
        }

        // Remitente
        $this->mail->setFrom(
            $this->config['from']['email'],
            $this->config['from']['name'] ?? ''
        );

        // Formato de correo
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    /**
     * Envía un correo electrónico usando un array de parámetros.
     *
     * @param array $params
     * Ejemplo:
     * [
     *   'to'          => ['correo1@dominio.com', 'correo2@dominio.com'], // o string
     *   'subject'     => 'Asunto del correo',
     *   'body'        => '<h1>Hola</h1><p>Este es un correo.</p>',
     *   'attachments' => ['./documentos/archivo.pdf'] // opcional
     * ]
     *
     * @return bool|string true si se envía correctamente, mensaje de error si falla.
     */
    public function send(array $params)
    {
        try {
            $this->mail->clearAddresses();

            // Destinatarios
            if (is_array($params['to'])) {
                foreach ($params['to'] as $recipient) {
                    $this->mail->addAddress($recipient);
                }
            } else {
                $this->mail->addAddress($params['to']);
            }

            // Asunto y cuerpo
            $this->mail->Subject = $params['subject'] ?? '(Sin asunto)';
            $this->mail->Body = $params['body'] ?? '';

            // Adjuntos
            if (!empty($params['attachments']) && is_array($params['attachments'])) {
                foreach ($params['attachments'] as $file) {
                    if (file_exists($file)) {
                        $this->mail->addAttachment($file);
                    }
                }
            }

            // Enviar
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return "❌ Error al enviar el correo: {$this->mail->ErrorInfo}";
        }
    }
}
