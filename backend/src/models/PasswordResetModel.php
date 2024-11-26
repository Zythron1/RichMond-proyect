<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/UserModel.php';


/**
    * Clase PasswordResetModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con resetear la contraseña.
*/
class PasswordResetModel {
    /**
        * Recupera y actualiza la contraseña de un usuario a través de un enlace de recuperación.
        *
        * Este método valida el token de recuperación proporcionado. Si el token es válido y no ha expirado, 
        * actualiza la contraseña del usuario en la base de datos. Además, se limpia el token de recuperación 
        * después de que la contraseña ha sido cambiada con éxito.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $data Un array asociativo que contiene: 
        *                    - 'token': El token de recuperación proporcionado.
        *                    - 'newPassword': La nueva contraseña que se quiere asignar al usuario.
        *
        * @return array Un array con el estado de la operación, que incluye:
        *               - 'status': 'success' si la contraseña fue actualizada correctamente, 'error' si ocurrió algún problema.
        *               - 'message': Descripción detallada del resultado de la operación.
        * 
        * @throws Exception Si ocurre algún error durante la ejecución, este se captura y se envía en el mensaje de error.
    */
    public function passwordRecovery ($connection, $data) {
        $stmt = $connection->prepare('SELECT * FROM password_resets WHERE reset_token = :resetToken;');
        $stmt->bindParam(':token', $data['token'], PDO::PARAM_STR);
        $stmt->execute();
        $passwordResetsData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$passwordResetsData || $passwordResetsData['token_expiration'] < time()) {
            return [
                'status' => 'error',
                'message' => 'El enlace ha expirado o no es válido.'
            ];
        }

        $hashedPassword = password_hash($data['newPassword'], PASSWORD_BCRYPT);

        $stmt = $connection->prepare('UPDATE users SET user_password = :userPassword WHERE user_id = :userId;');
        $stmt->bindParam(':userPassword', $hashedPassword,PDO::PARAM_STR);
        $stmt->bindParam(':userId', $passwordResetsData['user_id'], PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'No fue posible actualizar la contraseña.'
            ];
        }

        $stmt = $connection->prepare('UPDATE password_resets SET reset_token = NULL, token_expiration = NULL WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $passwordResetsData['user_id'], PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'No fue posible actualizar los tokens.'
            ];
        }
    }
}