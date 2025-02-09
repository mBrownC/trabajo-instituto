<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }

    private function configurarSMTP() {
        try {
            // Configuración del servidor SMTP
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['EMAIL_USER'];
            $this->mail->Password   = $_ENV['EMAIL_PASS'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 465;
            $this->mail->setFrom($_ENV['EMAIL_USER'], 'Zapatos3000');
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
        }
    }

    public function enviarCorreoRecuperacion($destinatario, $token) {
        try {
            // Limpiar cualquier destinatario previo
            $this->mail->clearAddresses();
            
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = 'Recuperación de Contraseña - Zapatos3000';
            
            // Construir el enlace de recuperación
            $enlaceRecuperacion = "http://localhost:8000/recuperar-contrasena?token=" . $token;
            
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

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo: " . $e->getMessage());
            return false;
        }
    }

    public function enviarCorreoConfirmacion($destinatario, $nombre) {
        try {
            $this->mail->clearAddresses();
            
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = 'Bienvenido a Zapatos3000';
            
            $cuerpo = "
            <html>
            <body>
                <h2>¡Bienvenido a Zapatos3000, {$nombre}!</h2>
                <p>Tu cuenta ha sido creada exitosamente.</p>
                <p>Esperamos que disfrutes de nuestros servicios.</p>
            </body>
            </html>
            ";
            
            $this->mail->Body = $cuerpo;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo de confirmación: " . $e->getMessage());
            return false;
        }
    }
}