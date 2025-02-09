<?php
require_once __DIR__ . '/../../config/database.php';

class SistemaInicial
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Database())->conectar();
    }

    public function inicializarSistema($claveTextoPlano) {
        try {
            // Verificar si ya existe un admin
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT COUNT(*) FROM Usuario WHERE correo_electronico = 'admin@zapatos3000.com'"
                );
                $stmt->execute();
                $count = $stmt->fetchColumn();
                error_log("Número de usuarios admin existentes: " . $count);
            } catch (PDOException $e) {
                error_log("Error verificando usuario admin: " . $e->getMessage());
                return false;
            }
    
            if ($count == 0) {
                // Verificar si el rol "administrador" existe
                try {
                    $stmt = $this->conexion->prepare(
                        "SELECT id FROM Rol WHERE nombre = 'administrador'"
                    );
                    $stmt->execute();
                    $rolId = $stmt->fetchColumn();
    
                    if (!$rolId) {
                        error_log("Error: No se encontró el rol 'administrador'");
                        return false;
                    }
                } catch (PDOException $e) {
                    error_log("Error verificando rol administrador: " . $e->getMessage());
                    return false;
                }
    
                // Hashear la contraseña
                $passwordHash = password_hash($claveTextoPlano, PASSWORD_BCRYPT);
    
                // Insertar usuario administrador
                try {
                    $stmt = $this->conexion->prepare(
                        "INSERT INTO Usuario (nombre, apellido, correo_electronico, contrasena, rol_id, estado) 
                         VALUES (:nombre, :apellido, :email, :password, :rolId, 'activo')"
                    );
                    $resultado = $stmt->execute([
                        ':nombre' => 'Admin',
                        ':apellido' => 'Sistema',
                        ':email' => 'admin@zapatos3000.com',
                        ':password' => $passwordHash,
                        ':rolId' => $rolId
                    ]);
                    error_log("Resultado de inserción de usuario: " . ($resultado ? 'Éxito' : 'Fallo'));
                    return $resultado;
                } catch (PDOException $e) {
                    error_log("Error insertando usuario admin: " . $e->getMessage());
                    return false;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error inicializando sistema: " . $e->getMessage());
            return false;
        }
    }
}
