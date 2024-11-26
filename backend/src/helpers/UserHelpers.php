<?php

class UserHelpers {
    /**
        * Valida la contraseña de un usuario.
        *
        * Este método verifica si la contraseña proporcionada cumple con los criterios
        * de seguridad requeridos: al menos 8 caracteres, una letra mayúscula, una letra
        * minúscula y un número.
        *
        * @param array $data Arreglo asociativo que debe incluir:
        *     - 'userPassword': Contraseña a validar (requerido, string).
        *
        * @return array Retorna un arreglo con el estado de la validación:
        *     - Si la contraseña no cumple con los criterios:
        *         - 'status' => 'error'.
        *         - 'message': Detalle del error indicando los requisitos faltantes.
        *     - Si la contraseña es válida:
        *         - 'status' => 'success'.
        *         - 'message': Mensaje indicando que la contraseña es válida.
    */
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


    /**
        * Valida los datos de un usuario, incluyendo nombre, correo electrónico y contraseña.
        *
        * Este método verifica que el nombre de usuario sea válido (solo letras y espacios),
        * que la dirección de correo electrónico tenga el formato correcto, y que la contraseña 
        * cumpla con los requisitos de seguridad establecidos. Utiliza una función auxiliar para 
        * validar la contraseña.
        *
        * @param array $data Arreglo asociativo que debe incluir:
        *     - 'userName': Nombre de usuario (requerido, string).
        *     - 'emailAddress': Dirección de correo electrónico (requerido, string).
        *     - 'userPassword': Contraseña del usuario (requerido, string).
        *
        * @return array Retorna un arreglo con el estado de la validación:
        *     - Si el nombre de usuario es inválido:
        *         - 'status' => 'error'.
        *         - 'message': Detalle del error (por ejemplo, "Nombre de usuario no válido").
        *     - Si el correo electrónico es inválido:
        *         - 'status' => 'error'.
        *         - 'message': Detalle del error (por ejemplo, "Email no válido").
        *     - Si la contraseña es inválida:
        *         - 'status' => 'error'.
        *         - 'message': Detalle del error (por ejemplo, "Contraseña no válida").
        *     - Si todos los datos son válidos:
        *         - 'status' => 'success'.
        *         - 'message': Mensaje indicando que la información fue verificada correctamente.
    */
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


    /**
        * Valida si un token de recuperación de contraseña existe y si ha expirado.
        *
        * Este método verifica si el usuario tiene un token de recuperación de contraseña en la base de datos.
        * Si el token existe y no ha expirado, se retorna un estado de error. Si el token no existe o ha expirado,
        * se retornan estados de éxito adecuados.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param int $data ID del usuario cuyo token de recuperación se va a verificar (requerido, entero).
        *
        * @return array Retorna un arreglo con el estado de la validación:
        *     - Si no se encuentra el token de recuperación:
        *         - 'status' => 'success1'.
        *     - Si el token no ha expirado:
        *         - 'status' => 'success2'.
        *     - Si el token ha expirado:
        *         - 'status' => 'error'.
        *         - 'message': Mensaje indicando que el token aún no ha caducado.
    */
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