<?php
require_once './backend/src/helpers/UserHelpers.php';

class UserModel {
    public function getAllUsers ($connection) {
        // Se realiza la consulta con el método query del objeto que fue instaciado de PDO
        $stmt = $connection->query('SELECT * FROM users;');
        // Se recupera todos los datos, se dice cómo se devolverán y se retornan esos datos
        return $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function getUserById ($connection, $userId) {
        // Se prepara la consulta para evitar inyecciones SQL
        $stmt = $connection->prepare('SELECT  * FROM users WHERE user_id = :userId;');
        // Se asocia el parámetro $userId con el parámetro :userId
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        // Se ejecuta la consulta
        $stmt->execute();
        // Se recupera el usuario encontrado, se indica cómo saldrán los datos y se retorna
        return $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser ($connection, $userData) {
        // Se encripta la contraseña del usuario
        $hashedPassword = password_hash($userData['userPassword'], PASSWORD_BCRYPT);

        // Se prepara la consulta evitando inyecciones sql
        $stmt = $connection->prepare('INSERT INTO users (user_name, email_address, user_password) VALUES (:userName, :emailAddress, :userPassword);');
        
        // Se asocia los valores del array del parámetro $userData
        $stmt->bindParam(':userName', $userData['userName'], PDO::PARAM_STR);
        $stmt->bindParam(':emailAddress', $userData['emailAddress'], PDO::PARAM_STR);
        $stmt->bindParam(':userPassword', $hashedPassword, PDO::PARAM_STR);

        // Se ejecuta la consulta y se devuelve el id del nuevo usuario insetado
        if($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function login ($connection, $data) {
        $stmt = $connection->prepare('SELECT user_id, email_address, user_password FROM users WHERE email_address = :emailAddress');
        $stmt->bindParam(':emailAddress', $data['emailAddress'], PDO::PARAM_STR);
        $stmt->execute();
        $realUserData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$realUserData) {
            return [
                'status' => 'error',
                'message' => 'El email no existe.'
            ];
        }

        if (!password_verify($data['userPassword'], $realUserData['user_password'])) {
            return [
                'status' => 'error',
                'message' => 'La contraseña no coincide.'
            ];
        }

        $userId = $realUserData['user_id'];
        $tokenData = [
            'userId' => $userId,
            'exp' => time() + (60 * 60 * 168)
        ];

        $token = base64_encode(json_encode($tokenData));

        return [
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token
        ];
    }

    public function updateUser ($connection, $userId, $userData) {
        $query = 'UPDATE users SET ';
        $params = [];

        if (isset($userData['userName'])) {
            $query .= 'user_name = :userName, ';
            $params[':userName'] = $userData['userName'];
        }

        if (isset($userData['emailAddress'])) {
            $query .= 'email_address = :emailAddress, ';
            $params[':emailAddress'] = $userData['emailAddress'];
        }

        if (isset($userData['address'])) {
            $query .= 'address = :address, ';
            $params[':address'] = $userData['address'];
        }

        if (isset($userData['phone'])) {
            $query .= 'phone = :phone ';
            $params[':phone'] = $userData['phone'];
        }

        $query = rtrim($query, ', '). ' WHERE user_id = :userId;';
        $params[':userId'] = $userId;

        $stmt = $connection->prepare($query);

        if($stmt->execute($params)) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword ($connection, $userId, $currentPassword, $newPassword) { 
        // paso 1: Se obtiene la contraseña actual del usuario
        $currentPasswordstmt = $connection->prepare('SELECT user_password FROM users WHERE user_id = :userId;');
        $currentPasswordstmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        $currentPasswordstmt->execute();
        $password = $currentPasswordstmt->fetch(PDO::FETCH_ASSOC);
        
        // paso 2: Se verifica la contraseña
        if (!password_verify($currentPassword, $password['user_password'])) {
            return [
                'status' => 'error',
                'message' => 'La contraseña actual no coincide'
            ];
        } 

        // paso 3: Se encripta la contraseña nueva
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // paso 4: Se actualiza la contraseña en la base de datos.
        $updateStmt = $connection->prepare('UPDATE users SET user_password = :newPassword WHERE user_id = :userId;');
        $updateStmt->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($updateStmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function sendUrlToEmail($connection, $data){
        $stmt = $connection->prepare('SELECT user_id, email_address FROM users WHERE email_address = :emailAddress;');
        $stmt->bindParam(':emailAddress', $data['emailAddress'], PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return [
                'status' => 'error',
                'message' => 'El email no existe.'
            ];
        }

        $UserHelpersInstance = new UserHelpers;
        $response = $UserHelpersInstance->validateIsExistsRecoveryToken($connection, $userData['user_id']);

        if ($response['status'] === 'error') {
            return $response;
        }

        $token = bin2hex(random_bytes(16));
        $exp = time() + (60 * 15);
        $expiry = date('Y-m-d H:i:s', $exp);

        if ($response['status'] === 'success1') {
            $stmt = $connection->prepare('INSERT INTO password_resets (user_id, reset_token, token_expiration) VALUES (:userId, :resetToken, :tokenExpiration);');
            $stmt->bindParam(':userId', $userData['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':resetToken', $token, PDO::PARAM_STR);
            $stmt->bindParam(':tokenExpiration', $expiry, PDO::PARAM_STR);
            $stmt->execute();
        } elseif ($response['status'] === 'success2') {
            $stmt = $connection->prepare('UPDATE password_resets SET reset_token = :resetToken, token_expiration = :tokenExpiration WHERE user_id = :userId;');
            $stmt->bindParam(':userId', $userData['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':resetToken', $token, PDO::PARAM_STR);
            $stmt->bindParam(':tokenExpiration', $expiry, PDO::PARAM_STR);
            $stmt->execute();
        }

        $resetLink = "http://localhost:3000/frontend/src/html/reset.html?token=$token";

        if (!mail($data['emailAddress'], "Restablecer contraseña. El enlace expirará en 15 minutos.", "Haz clic en el siguiente enlace para restablecer la contraseña: $resetLink")) {
            return [
                'status' => 'error',
                'message' => 'Error al enviar el email para recuperar la contraseña.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Revisa tu email para restablecer la contraseña.'
        ];
    }


}
