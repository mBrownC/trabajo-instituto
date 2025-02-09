<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }

    private function configurarSMTP()
    {
        try {
            // Configuración detallada de depuración
            $this->mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $this->mail->Debugoutput = function ($str, $level) {
                error_log("SMTP DEBUG: $str");
            };

            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['EMAIL_USER'];
            $this->mail->Password   = $_ENV['EMAIL_PASS'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cambiar a STARTTLS
            $this->mail->Port       = 587; // Puerto para TLS

            // Configuraciones de seguridad
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $this->mail->setFrom($_ENV['EMAIL_USER'], 'Zapatos3000');
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
            throw $e;
        }
    }

    public function enviarCorreoRecuperacion($destinatario, $token)
    {
        try {
            // Limpiar configuraciones previas
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $this->mail->addAddress($destinatario);
            $this->mail->Subject = 'Recuperación de Contraseña - Zapatos3000';

            $enlaceRecuperacion = "http://localhost/proyecto_zapatos3000/frontend/recuperar-contrasena.html?token=" . $token;

            $cuerpo = "
            <html>
            <body>
                <h2>Recuperación de Contraseña</h2>
                <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                <p>Haz clic en el siguiente enlace para recuperar tu contraseña:</p>
                <a href='{$enlaceRecuperacion}'>Restablecer Contraseña</a>
                <p>Si no solicitaste este cambio, ignora este correo.</p>
                <p>El enlace expirará en 1 hora.</p>
            </body>
            </html>
            ";

            $this->mail->Body = $cuerpo;
            $this->mail->AltBody = strip_tags($cuerpo);

            // Intentar enviar
            $resultado = $this->mail->send();

            // Registro detallado
            error_log("Intento de envío de correo a: " . $destinatario);
            error_log("Token de recuperación: " . $token);
            error_log("Resultado del envío: " . ($resultado ? 'Exitoso' : 'Fallido'));

            return $resultado;
        } catch (Exception $e) {
            // Registro detallado de errores
            error_log("Excepción al enviar correo: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Método de prueba con más detalle
    public function probarConfiguracion()
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->addAddress($_ENV['EMAIL_USER']);
            $this->mail->Subject = 'Prueba de Configuración SMTP - Zapatos3000';
            $this->mail->Body = 'Este es un correo de prueba para verificar la configuración SMTP.';

            $resultado = $this->mail->send();
            error_log("Prueba de configuración: " . ($resultado ? 'Exitosa' : 'Fallida'));
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en prueba de configuración: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
}
