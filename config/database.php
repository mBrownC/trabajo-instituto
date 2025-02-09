<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Database {
    private $host;
    private $usuario;
    private $password;
    private $database;
    private $conexion;

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $this->host = $_ENV['DB_HOST'];
        $this->usuario = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->database = $_ENV['DB_NAME'];
    }

    public function conectar() {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8", 
                $this->usuario, 
                $this->password
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch(PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    public function cerrar() {
        $this->conexion = null;
    }
}