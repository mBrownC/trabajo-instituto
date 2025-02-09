<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class AuthService {
    private $jwtSecret;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }

    public function generarTokenJWT($usuarioId) {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // 1 hora
            'usuario_id' => $usuarioId
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function validarTokenJWT($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return $decoded;
        } catch(Exception $e) {
            return false;
        }
    }
}