<?php
class Sesion {
    // Iniciar sesión
    public static function iniciar($usuarioId, $nombre, $email, $rol) {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Almacenar datos del usuario en la sesión
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        $_SESSION['rol'] = $rol;
        $_SESSION['inicio_sesion'] = time();
    }

    // Verificar si hay una sesión activa
    public static function estaActiva() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar si existe el usuario_id
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        // Opcional: Verificar tiempo de sesión (por ejemplo, 4 horas)
        $tiempoMaximo = 4 * 60 * 60; // 4 horas en segundos
        if (isset($_SESSION['inicio_sesion']) && 
            (time() - $_SESSION['inicio_sesion']) > $tiempoMaximo) {
            self::cerrar();
            return false;
        }

        return true;
    }

    // Obtener dato de la sesión
    public static function obtener($clave) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION[$clave] ?? null;
    }

    // Cerrar sesión
    public static function cerrar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Destruir la sesión
        session_destroy();

        // Eliminar cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    // Verificar permisos de rol
    public static function verificarPermiso($permisoRequerido) {
        if (!self::estaActiva()) {
            return false;
        }

        $rol = self::obtener('rol');

        // Definir permisos por rol
        $permisosRoles = [
            'administrador' => ['crear', 'editar', 'eliminar', 'ver_todo'],
            'supervisor' => ['crear', 'editar', 'ver_parcial'],
            'colaborador' => ['crear', 'ver_propio']
        ];

        // Verificar si el rol tiene el permiso
        return isset($permisosRoles[$rol]) && 
               in_array($permisoRequerido, $permisosRoles[$rol]);
    }

    // Renovar sesión
    public static function renovar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        // Actualizar tiempo de inicio
        $_SESSION['inicio_sesion'] = time();
    }
}