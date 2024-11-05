<?php

class UserHelpers {
    public static function validateUserPassword($data) {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $data['userPassword'])) {
            return [
                'status' => 'error',
                'message' => 'Contraseña no válida. Min 8 caracteres una letra mayúscula, minúscula y un número.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Información de usuario verificada.'
        ];
    }

    public static function validateUserData ($data) {
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

        $validatedPassword = UserHelpers::validateUserPassword(['userPassword' => $data['userPassword']]);
        if ($validatedPassword['status'] === 'error'){
            return $validatedPassword;
        }

        return [
            'status' => 'success',
            'message' => 'Información de usuario verificada.'
        ];
    }

    public function validateIsExistsRecoveryToken ($connection, $data) {
        $stmt = $connection->prepare('SELECT * FROM password_resets WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $data, PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return [
                'status' => 'success1'
            ];
        }
        if ($userData['token_expiration'] < time()) {
            return [
                'status' => 'error',
                'message' => 'Ya tienes la url en tu correo y aún no ha caducado.'
            ];
        } else {
            return [
                'status' => 'success2'
            ];
        }



    }
}