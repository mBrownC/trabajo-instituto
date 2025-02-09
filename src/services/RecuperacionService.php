<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/EmailService.php';

class RecuperacionService
{
    private $db;
    private $conexion;

    public function __construct()
    {
        $this->db = new Database();
        $this->conexion = $this->db->conectar();
    }

    public function generarTokenRecuperacion($email)
    {
        try {
            // Log de inicio
            error_log("Iniciando generación de token para: $email");

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscarPorEmail($email);

            if (!$usuario) {
                error_log("Usuario no encontrado: $email");
                return false;
            }

            // Invalidar tokens anteriores
            $invalidarQuery = "UPDATE Token 
                               SET usado = 1 
                               WHERE usuario_id = :usuario_id 
                               AND tipo = 'recuperacion_pass' 
                               AND usado = 0";

            $stmt = $this->conexion->prepare($invalidarQuery);
            $stmt->bindParam(':usuario_id', $usuario['id'], PDO::PARAM_INT);
            $stmt->execute();

            $token = bin2hex(random_bytes(32));

            $query = "INSERT INTO Token (usuario_id, token, tipo, fecha_expiracion) 
                      VALUES (:usuario_id, :token, 'recuperacion_pass', DATE_ADD(NOW(), INTERVAL 1 HOUR))";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario['id'], PDO::PARAM_INT);
            $stmt->bindParam(':token', $token);

            $resultado = $stmt->execute();

            // Log de generación de token
            error_log("Token generado para $email: $token");

            // Enviar correo
            $emailService = new EmailService();
            $resultadoCorreo = $emailService->enviarCorreoRecuperacion($email, $token);

            error_log("Resultado de envío de correo: " . ($resultadoCorreo ? 'Exitoso' : 'Fallido'));

            return $token;
        } catch (PDOException $e) {
            error_log("Error generando token de recuperación: " . $e->getMessage());
            return false;
        }
    }

    // Resto del código permanece igual
    public function validarTokenRecuperacion($token)
    {
        try {
            $query = "SELECT * FROM Token 
                      WHERE token = :token 
                      AND tipo = 'recuperacion_pass' 
                      AND fecha_expiracion > NOW() 
                      AND usado = 0";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $tokenInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            return $tokenInfo ? $tokenInfo : false;
        } catch (PDOException $e) {
            error_log("Error validando token de recuperación: " . $e->getMessage());
            return false;
        }
    }

    public function restablecerContrasena($token, $nuevaContrasena)
    {
        try {
            $tokenInfo = $this->validarTokenRecuperacion($token);

            if (!$tokenInfo) {
                return false;
            }

            $usuarioModel = new Usuario();
            $resultado = $usuarioModel->actualizarContrasena(
                $tokenInfo['usuario_id'],
                $nuevaContrasena
            );

            if ($resultado) {
                $this->marcarTokenComoUsado($token);
            }

            return $resultado;
        } catch (Exception $e) {
            error_log("Error restableciendo contraseña: " . $e->getMessage());
            return false;
        }
    }

    private function marcarTokenComoUsado($token)
    {
        try {
            $query = "UPDATE Token SET usado = 1 WHERE token = :token";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error marcando token como usado: " . $e->getMessage());
        }
    }
}
