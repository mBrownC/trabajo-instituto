<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $db;
    private $conexion;

    public function __construct() {
        $this->db = new Database();
        $this->conexion = $this->db->conectar();
    }

    public function registrar($nombre, $apellido, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $query = "INSERT INTO Usuario (nombre, apellido, correo_electronico, contrasena, rol_id, estado) 
                      VALUES (:nombre, :apellido, :email, :password, 
                              (SELECT id FROM Rol WHERE nombre = 'colaborador'), 'activo')";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $passwordHash);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error en registro: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $query = "SELECT * FROM Usuario WHERE correo_electronico = :email";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['contrasena'])) {
                return $usuario;
            }

            return false;
        } catch(PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarContrasena($usuarioId, $nuevaContrasena) {
        $passwordHash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
    
        try {
            $query = "UPDATE Usuario SET contrasena = :password WHERE id = :usuario_id";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
    
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error actualizando contraseña: " . $e->getMessage());
            return false;
        }
    }
    
    public function buscarPorEmail($email) {
        try {
            $query = "SELECT * FROM Usuario WHERE correo_electronico = :email";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
    
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error buscando usuario por email: " . $e->getMessage());
            return false;
        }
    }
}