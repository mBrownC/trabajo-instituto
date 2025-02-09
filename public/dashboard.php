<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Zapatos3000</title>
</head>
<body>
    <h1>Bienvenido al Dashboard</h1>
    <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
    
    <h2>Mis Tareas</h2>
    <!-- Aquí irá la lista de tareas -->
    
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>