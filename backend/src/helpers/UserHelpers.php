<?php

class UserHelpers {
    static function validateUserData ($data) {
        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+)*$/', $data['userName'])) {
            return [
                'status' => 'error',
                'message' => 'Nombre de usuario no válido'
            ];
        }
        
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data['emailAddress'])) {
            return [
                'status' => 'error',
                'message' => 'Email no válido'
            ];
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $data['userPassword'])) {
            return [
                'status' => 'error',
                'message' => 'Contraseña no válida. Min 8 caracteres una letra mayúscula, minúscula y un número.'
            ];
        }
        return [
            'status' => 'success',
            'message' => 'Información de usuario varificada.'
        ];
    }
}