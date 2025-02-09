<?php
require_once __DIR__ . '/../models/Tarea.php';
require_once __DIR__ . '/../services/AuthService.php';

class TareaController {
    private $tareaModel;
    private $authService;

    public function __construct() {
        $this->tareaModel = new Tarea();
        $this->authService = new AuthService();
    }

    public function crear($datos, $token) {
        $usuarioDecoded = $this->authService->validarTokenJWT($token);
        
        if (!$usuarioDecoded) {
            return ['error' => 'Token inválido'];
        }

        if (empty($datos['titulo']) || empty($datos['descripcion'])) {
            return ['error' => 'Título y descripción son obligatorios'];
        }

        $resultado = $this->tareaModel->crear(
            $usuarioDecoded->usuario_id, 
            $datos['titulo'], 
            $datos['descripcion']
        );

        return $resultado 
            ? ['mensaje' => 'Tarea creada exitosamente', 'id' => $resultado]
            : ['error' => 'Error al crear tarea'];
    }

    public function listar($token) {
        $usuarioDecoded = $this->authService->validarTokenJWT($token);
        
        if (!$usuarioDecoded) {
            return ['error' => 'Token inválido'];
        }

        $tareas = $this->tareaModel->listarPorUsuario($usuarioDecoded->usuario_id);

        return $tareas !== false 
            ? ['tareas' => $tareas]
            : ['error' => 'Error al listar tareas'];
    }

    public function actualizar($datos, $token) {
        $usuarioDecoded = $this->authService->validarTokenJWT($token);
        
        if (!$usuarioDecoded) {
            return ['error' => 'Token inválido'];
        }

        if (empty($datos['id']) || empty($datos['titulo']) || 
            empty($datos['descripcion']) || empty($datos['estado'])) {
            return ['error' => 'Todos los campos son obligatorios'];
        }

        $resultado = $this->tareaModel->actualizar(
            $datos['id'],
            $usuarioDecoded->usuario_id,
            $datos['titulo'],
            $datos['descripcion'],
            $datos['estado']
        );

        return $resultado 
            ? ['mensaje' => 'Tarea actualizada exitosamente']
            : ['error' => 'Error al actualizar tarea'];
    }

    public function eliminar($tareaId, $token) {
        $usuarioDecoded = $this->authService->validarTokenJWT($token);
        
        if (!$usuarioDecoded) {
            return ['error' => 'Token inválido'];
        }

        $resultado = $this->tareaModel->eliminar($tareaId, $usuarioDecoded->usuario_id);

        return $resultado 
            ? ['mensaje' => 'Tarea eliminada exitosamente']
            : ['error' => 'Error al eliminar tarea'];
    }
}