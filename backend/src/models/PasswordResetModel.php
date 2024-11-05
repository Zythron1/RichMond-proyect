<?php

require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/UserModel.php';

class PasswordResetModel {

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