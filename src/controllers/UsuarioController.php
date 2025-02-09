<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../services/AuthService.php';

class UsuarioController {
    private $usuarioModel;
    private $authService;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->authService = new AuthService();
    }

    public function registrar($datos) {
        // Validaciones básicas
        if (empty($datos['nombre']) || empty($datos['apellido']) || 
            empty($datos['email']) || empty($datos['password'])) {
            return ['error' => 'Todos los campos son obligatorios'];
        }

        $resultado = $this->usuarioModel->registrar(
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['email'], 
            $datos['password']
        );

        return $resultado 
            ? ['mensaje' => 'Usuario registrado exitosamente'] 
            : ['error' => 'Error al registrar usuario'];
    }

    public function login($email, $password) {
        $usuario = $this->usuarioModel->login($email, $password);

        if ($usuario) {
            // Generar token de acceso
            $token = $this->authService->generarTokenJWT($usuario['id']);

            return [
                'token' => $token,
                'usuario' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'email' => $usuario['correo_electronico']
                ]
            ];
        }

        return ['error' => 'Credenciales inválidas'];
    }
}