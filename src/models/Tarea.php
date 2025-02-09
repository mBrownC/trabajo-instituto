<?php
require_once __DIR__ . '/../../config/database.php';

class Tarea {
    private $db;
    private $conexion;

    public function __construct() {
        $this->db = new Database();
        $this->conexion = $this->db->conectar();
    }

    public function crear($usuarioId, $titulo, $descripcion) {
        try {
            $query = "INSERT INTO Tarea (usuario_id, titulo, descripcion, estado, fecha_creacion) 
                      VALUES (:usuario_id, :titulo, :descripcion, 'pendiente', NOW())";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);

            return $stmt->execute() ? $this->conexion->lastInsertId() : false;
        } catch(PDOException $e) {
            error_log("Error creando tarea: " . $e->getMessage());
            return false;
        }
    }

    public function listarPorUsuario($usuarioId) {
        try {
            $query = "SELECT * FROM Tarea WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error listando tareas: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($tareaId, $usuarioId, $titulo, $descripcion, $estado) {
        try {
            $query = "UPDATE Tarea 
                      SET titulo = :titulo, 
                          descripcion = :descripcion, 
                          estado = :estado, 
                          fecha_actualizacion = NOW() 
                      WHERE id = :tarea_id AND usuario_id = :usuario_id";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':tarea_id', $tareaId, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error actualizando tarea: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($tareaId, $usuarioId) {
        try {
            $query = "DELETE FROM Tarea WHERE id = :tarea_id AND usuario_id = :usuario_id";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':tarea_id', $tareaId, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error eliminando tarea: " . $e->getMessage());
            return false;
        }
    }
}