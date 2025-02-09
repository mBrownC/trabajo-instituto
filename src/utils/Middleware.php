<?php
require_once __DIR__ . '/../services/AuthService.php';

class Middleware {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function validarToken($token) {
        if (empty($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token no proporcionado']);
            exit;
        }

        $tokenDecoded = $this->authService->validarTokenJWT($token);

        if (!$tokenDecoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido o expirado']);
            exit;
        }

        return $tokenDecoded->usuario_id;
    }
}