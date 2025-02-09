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
    $resultado = $usuarioController->registrar([
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ]);

    if (isset($resultado['mensaje'])) {
        // Redirigir a login con mensaje de éxito
        $_SESSION['registro_exitoso'] = $resultado['mensaje'];
        header('Location: login.php');
        exit();
    } else {
        $error = $resultado['error'] ?? 'Error al registrar';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Zapatos3000</title>
</head>
<body>
    <h1>Registrarse</h1>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>

    <p>
        <a href="login.php">Iniciar Sesión</a>
    </p>
</body>
</html>