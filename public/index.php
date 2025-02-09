<?php
// Configuración de headers para API RESTful
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitudes OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar controladores y servicios
require_once __DIR__ . '/../src/controllers/UsuarioController.php';
require_once __DIR__ . '/../src/controllers/TareaController.php';
require_once __DIR__ . '/../src/services/RecuperacionService.php';
require_once __DIR__ . '/../src/utils/Middleware.php';
require_once __DIR__ . '/../src/utils/Validador.php';

// Inicializar controladores y servicios
$usuarioController = new UsuarioController();
$tareaController = new TareaController();
$recuperacionService = new RecuperacionService();
$middleware = new Middleware();

// Obtener la acción solicitada
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';

try {
    // Obtener el token de autorización si existe
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';

    // Enrutamiento
    switch ($accion) {
        // Rutas de Autenticación
        case 'registro':
            if ($metodo === 'POST') {
                $datos = json_decode(file_get_contents('php://input'), true);
                
                // Validaciones
                $errores = Validador::validarCamposObligatorios([
                    'nombre', 'apellido', 'email', 'password'
                ], $datos);

                if (!empty($errores)) {
                    http_response_code(400);
                    echo json_encode(['errores' => $errores]);
                    break;
                }

                if (!Validador::validarEmail($datos['email'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email inválido']);
                    break;
                }

                if (!Validador::validarContrasena($datos['password'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Contraseña no cumple requisitos de seguridad']);
                    break;
                }

                echo json_encode($usuarioController->registrar($datos));
            }
            break;

        case 'login':
            if ($metodo === 'POST') {
                $datos = json_decode(file_get_contents('php://input'), true);
                
                // Validaciones
                $errores = Validador::validarCamposObligatorios([
                    'email', 'password'
                ], $datos);

                if (!empty($errores)) {
                    http_response_code(400);
                    echo json_encode(['errores' => $errores]);
                    break;
                }

                echo json_encode($usuarioController->login($datos['email'], $datos['password']));
            }
            break;

        case 'solicitar_recuperacion':
            if ($metodo === 'POST') {
                $datos = json_decode(file_get_contents('php://input'), true);
                $email = $datos['email'] ?? '';
                
                if (!Validador::validarEmail($email)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email inválido']);
                    break;
                }

                $token = $recuperacionService->generarTokenRecuperacion($email);
                
                if ($token) {
                    // TODO: Implementar envío de correo electrónico
                    echo json_encode([
                        'mensaje' => 'Token de recuperación generado',
                        'token' => $token // Solo para pruebas
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'No se pudo generar el token']);
                }
            }
            break;

        case 'restablecer_contrasena':
            if ($metodo === 'POST') {
                $datos = json_decode(file_get_contents('php://input'), true);
                $token = $datos['token'] ?? '';
                $nuevaContrasena = $datos['nueva_contrasena'] ?? '';

                $errores = Validador::validarCamposObligatorios([
                    'token', 'nueva_contrasena'
                ], $datos);

                if (!empty($errores)) {
                    http_response_code(400);
                    echo json_encode(['errores' => $errores]);
                    break;
                }

                if (!Validador::validarContrasena($nuevaContrasena)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Contraseña no cumple requisitos de seguridad']);
                    break;
                }

                $resultado = $recuperacionService->restablecerContrasena($token, $nuevaContrasena);
                
                echo json_encode($resultado 
                    ? ['mensaje' => 'Contraseña restablecida exitosamente']
                    : ['error' => 'No se pudo restablecer la contraseña']
                );
            }
            break;

        // Rutas de Tareas (requieren autenticación)
        case 'crear_tarea':
            if ($metodo === 'POST') {
                $usuarioId = $middleware->validarToken($token);
                $datos = json_decode(file_get_contents('php://input'), true);
                
                $errores = Validador::validarCamposObligatorios([
                    'titulo', 'descripcion'
                ], $datos);

                if (!empty($errores)) {
                    http_response_code(400);
                    echo json_encode(['errores' => $errores]);
                    break;
                }

                echo json_encode($tareaController->crear($datos, $token));
            }
            break;

        case 'listar_tareas':
            if ($metodo === 'GET') {
                $usuarioId = $middleware->validarToken($token);
                echo json_encode($tareaController->listar($token));
            }
            break;

        case 'actualizar_tarea':
            if ($metodo === 'PUT') {
                $usuarioId = $middleware->validarToken($token);
                $datos = json_decode(file_get_contents('php://input'), true);
                
                $errores = Validador::validarCamposObligatorios([
                    'id', 'titulo', 'descripcion', 'estado'
                ], $datos);

                if (!empty($errores)) {
                    http_response_code(400);
                    echo json_encode(['errores' => $errores]);
                    break;
                }

                echo json_encode($tareaController->actualizar($datos, $token));
            }
            break;

        case 'eliminar_tarea':
            if ($metodo === 'DELETE') {
                $usuarioId = $middleware->validarToken($token);
                $tareaId = $_GET['id'] ?? null;
                
                if (!$tareaId) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de tarea no proporcionado']);
                    break;
                }

                echo json_encode($tareaController->eliminar($tareaId, $token));
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
            break;
    }

} catch (Exception $e) {
    // Manejo de errores inesperados
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}