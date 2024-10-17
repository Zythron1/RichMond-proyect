<?php

require_once '../config/db_connection.php';

class PasswordModel {
    private function generateToken()
    {
        // Se retorna un token random
        return bin2hex(random_bytes(16));
    }

    public function setResetToken($connection, $userId, $token)
    {
        // Se establece la fecha y hora actual más 30 minutos que va a duara el token
        $expiration = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        // Se verifica si ya existe un token para el usuario
        $stmt = $connection->prepare('SELECT * FROM password_resets WHERE user_id = :userId');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $updateStmt = $connection->prepare('UPDATE password_resets SET reset_token = :token, token_expiration = :expiration WHERE user_id = :userId');
        } else {
            $updateStmt = $connection->prepare('INSERT INTO password_resets (reset_token, token_expiration) VALUES (:token, :expiration);');
        }

        $updateStmt->bindParam(':token', $token, PDO::PARAM_STR);
        $updateStmt->bindParam(':expiration', $expiration, PDO::PARAM_STR);
        $updateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        return $updateStmt->execute();
    }

    public function verifyToken($connection, $userId, $token)
    {
        $stmt = $connection->prepare('SELECT * FROM password_resets WHERE user_id = :userId AND reset_token = :token AND token_expiration > NOW();');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword($connection, $userId, $newPassword)
    {
        // se encripta la nueva contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Se actualiza la nueva contraseña
        $updateStmt = $connection->prepare('UPDATE users SET user_password = :newPassword WHERE user_id = :userId;');
        $updateStmt->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            // Si la contraseña se actualiza correctamente, eliminamos el token
            $deleteStmt = $connection->prepare('DELETE FROM password_resets WHERE user_id = :userId;');
            $deleteStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $deleteStmt->execute();
            return true;
        } else {
            return false;
        }
    }
}