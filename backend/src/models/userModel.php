<?php
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', true);
session_start();
session_regenerate_id(true);

require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ShoppingBagModel.php';
require_once './backend/src/helpers/UserHelpers.php';
require_once './backend/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        if (isset($_SESSION['userId'])) {
            return [
                'status' => 'error',
                'message' => 'Hay una sessión abierta en este momento.'
            ];
        }

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

        $_SESSION['userId'] = $realUserData['user_id'];

        $ShoppingBagModelInstance = new ShoppingBagModel;
        $ShoppingBagId = $ShoppingBagModelInstance->getShoppingBagId(DatabaseConnection::getConnection(), $realUserData['user_id']);

        if (!empty($ShoppingBagId)) {
            $products = $ShoppingBagModelInstance->getShoppingBagProducts(DatabaseConnection::getConnection(), $ShoppingBagId['shopping_bag_id']);

            if (!empty($products)) {
                return [
                    'status' => 'success',
                    'message' => 'Inicio de sesión exitoso.',
                    'userId' => $_SESSION['userId'],
                    'shoppingBag' => $products,
                ];
            }
        }

        
        return [
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'userId' => $_SESSION['userId'],
            'shoppingBag' => false,
        ];
    }

    public function logout () {
        if (empty($_SESSION['userId'])) {
            return [
                'status' => 'error',
                'message' => 'No hay una sesión activa.',
                'messageToDeveloper' => 'No está el userId en la variable $_SESSION.'
            ];
        }
        
        if (!session_unset()) {
            return [
                'status' => 'error',
                'message' => 'Hubo problemas al cerrar sesión, intenta de nuevo.',
                'messageToDeveloper' => 'Error al limpiar la variable $_SESSION.'
            ];
        }
        
        if (!session_destroy()) {
            return [
                'status' => 'error',
                'message' => 'Error al destruir la sesión.',
                'messageToDeveloper' => 'Error al destruir la variable $_SESSION.'
            ];
        }

        $_SESSION= [];
        
        return [
            'status' => 'succes',
            'message' => 'Sesión cerrada exitosamente.',
            'messageToDeveloper' => 'Ningún error.'
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

        $token = bin2hex(random_bytes(16));
        $exp = time() + (60 * 15);
        $expiry = date('Y-m-d H:i:s', $exp);

        $stmt = $connection->prepare('INSERT INTO password_resets (user_id, reset_token, token_expiration)
        VALUES (:userId, :resetToken, :tokenExpiration) ON DUPLICATE KEY UPDATE reset_token = :resetToken, token_expiration = :tokenExpiration;');
        $stmt->bindParam(':userId', $userData['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':resetToken', $token, PDO::PARAM_STR);
        $stmt->bindParam(':tokenExpiration', $expiry, PDO::PARAM_STR);
        $stmt->execute();

        $resetLink = "http://localhost:3000/frontend/src/html/reset.html?token=$token";

        // Instanciar phpmailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.sendinblue.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'felipedavid.310@gmail.com';  // Usa tu correo de Brevo
            $mail->Password = '4W8qAOhB0RwCVHby';  // Usa la clave de API generada
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;  // O intenta con 587 y PHPMailer::ENCRYPTION_STARTTLS

            // Configurar el email
            $mail->setFrom('felipedavid.310@gmail.com', 'RichMond Support');
            $mail->addAddress($data['emailAddress']);
            $mail->Subject = 'Restablecer contraseña';
            $mail->Body = "Haz click en el siguiente enlace para restablecer la contraseña: $resetLink\nEste enlace expirará en 15 minutos.";

            $mail->send();

            return [
                'status' => 'success',
                'message' => 'Revisa tu email para restablecer la contraseña.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al enviar el email: ' . $mail->ErrorInfo
            ];
        }

    }


}
