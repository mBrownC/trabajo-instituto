<?php

class Validador
{
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validarContrasena($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d\W]{8,}$/', $password);
    }

    public static function sanitizarEntrada($entrada)
    {
        return htmlspecialchars(strip_tags(trim($entrada)));
    }

    public static function validarCamposObligatorios($campos, $datos)
    {
        $errores = [];
        foreach ($campos as $campo) {
            if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                $errores[] = "El campo $campo es obligatorio";
            }
        }
        return $errores;
    }
}
