<?php
require_once __DIR__ . '/../src/controllers/UsuarioController.php';

session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarioController = new UsuarioController();
    $resultado = $usuarioController->login($_POST['email'], $_POST['password']);

    if (isset($resultado['token'])) {
        // Almacenar información en sesión
        $_SESSION['usuario_id'] = $resultado['usuario']['id'];
        $_SESSION['usuario_nombre'] = $resultado['usuario']['nombre'];
        $_SESSION['token'] = $resultado['token'];

        header('Location: dashboard.php');
        exit();
    } else {
        $error = $resultado['error'] ?? 'Credenciales inválidas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Zapatos3000</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <p>
        <a href="registro.php">Registrarse</a> | 
        <a href="recuperar-contrasena.php">Recuperar Contraseña</a>
    </p>
</body>
</html>