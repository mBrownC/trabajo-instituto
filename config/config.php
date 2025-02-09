<?php
// Configuraciones generales de la aplicación

// Configuración de la aplicación
define('APP_NAME', 'Zapatos3000');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// Configuraciones de seguridad
define('PASSWORD_HASH_ALGORITHM', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// Configuraciones de base de datos
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'zapatos3000');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Configuraciones de JWT
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'tu_secreto_por_defecto');
define('JWT_EXPIRATION', 3600); // 1 hora en segundos

// Configuraciones de correo
define('EMAIL_FROM', $_ENV['EMAIL_FROM'] ?? 'noreply@zapatos3000.com');

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');

// Manejo de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Zona horaria
date_default_timezone_set('America/Santiago');