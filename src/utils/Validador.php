<?php

class Validador {
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validarContrasena($password) {
        // Mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password);
    }

    public static function sanitizarEntrada($entrada) {
        return htmlspecialchars(strip_tags(trim($entrada)));
    }

    public static function validarCamposObligatorios($campos, $datos) {
        $errores = [];
        foreach ($campos as $campo) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                $errores[] = "El campo $campo es obligatorio";
            }
        }
        return $errores;
    }
}