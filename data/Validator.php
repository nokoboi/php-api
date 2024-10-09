<?php

class Validator
{
    public static function Sanitize($datos)
    {
        $saneado = [];
        foreach ($datos as $key => $value) {
            $saneado[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }

        return $saneado;
    }

    public static function Validate($datos)
    {
        $errors = [];

        // validar nombre
        if (!isset($datos['nombre']) || empty(trim($datos['nombre']))) {
            $errors['nombre'] = 'El nombre es necesario';
        } elseif (strlen($datos['nombre']) < 2 || strlen(trim($datos['nombre'])) > 50) {
            $errors['nombre'] = 'El nombre debe tener entre 2 y 50 caracteres';
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]+$/u", $datos['nombre'])) {
            $errors["nombre"] = "El nombre solo debe contener letras y espacios";
        }


        // Validar correo
        if (!isset($datos["email"]) || empty(trim($datos["email"]))) {
            $errors["email"] = "El email es necesario";
        } elseif (!filter_var($datos["email"], FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "El formato del email no es válido";
        } elseif (strlen($datos["email"]) > 255) {
            $errors["email"] = "El email es demasiado largo.";
        }

        return $errors;
    }


}