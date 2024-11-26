<?php

// Configuración de la sesión para mejorar la seguridad
/**
    * Se configuran los parámetros de la sesión para garantizar la seguridad.
    * 
    * - Se establece el tiempo de vida de la cookie de la sesión a 0, lo que significa que la cookie de sesión se eliminará
    *   cuando se cierre el navegador.
    * - Se habilita la opción `cookie_httponly`, lo que asegura que la cookie de sesión no sea accesible a través de JavaScript.
    * - Se inicia la sesión con `session_start()`.
    * - Se regenera el ID de sesión al comenzar, lo que ayuda a prevenir ataques de fijación de sesión.
*/
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', true);
session_start();
session_regenerate_id(true);


/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `ShoppingBagModel.php`: Modelo para gestionar la bolsa de compras.
    * - `UserHelpers.php`: Funciones auxiliares relacionadas con los usuarios.
    * - `autoload.php`: Carga automática de las dependencias gestionadas por Composer.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ShoppingBagModel.php';
require_once './backend/src/helpers/UserHelpers.php';
require_once './backend/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
    * Clase UserModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionada con los usuarios.
*/
class UserModel {
    /**
        * Obtiene todos los usuarios registrados en la base de datos.
        *
        * Este método ejecuta una consulta SQL para obtener todos los registros de la tabla `users`. 
        * La consulta devuelve los datos en un arreglo asociativo. Si no se encuentran registros, 
        * la respuesta será un arreglo vacío.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        *
        * @return array Un arreglo asociativo con todos los registros de la tabla `users`.
        *     Si no se encuentran usuarios, el arreglo será vacío.
    */
    public function getAllUsers ($connection) {
        // Se realiza la consulta con el método query del objeto que fue instaciado de PDO
        $stmt = $connection->query('SELECT * FROM users;');
        // Se recupera todos los datos, se dice cómo se devolverán y se retornan esos datos
        return $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }


    /**
        * Obtiene un usuario de la base de datos por su ID.
        *
        * Este método ejecuta una consulta SQL preparada para obtener un solo registro de la tabla `users`
        * basado en el `user_id` proporcionado. Se utiliza una consulta preparada para prevenir inyecciones SQL.
        * El resultado es un arreglo asociativo con los datos del usuario o `false` si no se encuentra el usuario.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario que se desea obtener de la base de datos.
        *
        * @return array|false Un arreglo asociativo con los datos del usuario si se encuentra, o `false` si no se encuentra el usuario.
    */
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


    /**
        * Crea un nuevo usuario en la base de datos.
        *
        * Este método inserta un nuevo usuario en la tabla `users` utilizando los datos proporcionados.
        * La contraseña del usuario se encripta antes de ser almacenada en la base de datos.
        * Se utiliza una consulta preparada para evitar inyecciones SQL.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $userData Un arreglo asociativo con los datos del usuario:
        *                        - 'userName' => Nombre del usuario.
        *                        - 'emailAddress' => Dirección de correo electrónico del usuario.
        *                        - 'userPassword' => Contraseña del usuario (será encriptada antes de almacenarse).
        *
        * @return int|false El ID del nuevo usuario insertado si la operación es exitosa, o `false` en caso de error.
    */
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


    /**
        * Inicia sesión de un usuario y valida sus credenciales.
        *
        * Este método verifica si el usuario ya tiene una sesión activa. Si no tiene, valida las credenciales proporcionadas
        * (email y contraseña). Si las credenciales son correctas, se inicia la sesión del usuario.
        * Además, se obtiene la bolsa de compras del usuario si existe, y se retorna el estado del inicio de sesión.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $data Un arreglo asociativo con los datos del usuario:
        *                    - 'emailAddress' => Dirección de correo electrónico del usuario.
        *                    - 'userPassword' => Contraseña del usuario (será verificada).
        *
        * @return array Un arreglo con el estado y mensaje del inicio de sesión, y los datos del usuario si la autenticación es exitosa:
        *               - 'status' => 'success' o 'error'.
        *               - 'message' => Mensaje correspondiente al estado del login.
        *               - 'userId' => ID del usuario en sesión si el login es exitoso.
        *               - 'shoppingBag' => Bolsa de compras del usuario si existe, o `false` si no tiene productos en la bolsa.
    */
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


    /**
        * Cierra la sesión de un usuario.
        *
        * Este método verifica si el usuario tiene una sesión activa. Si la sesión está activa, intenta destruir la sesión
        * y limpiar la variable `$_SESSION`. Si la sesión no está activa o hay un error en el proceso de cierre, 
        * retorna un mensaje de error. En caso de éxito, retorna un mensaje de confirmación.
        *
        * @return array Un arreglo con el estado y mensaje del cierre de sesión:
        *               - 'status' => 'success' o 'error'.
        *               - 'message' => Mensaje correspondiente al estado del cierre de sesión.
        *               - 'messageToDeveloper' => Información adicional para los desarrolladores, útil para la depuración.
    */
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


    /**
        * Actualiza los datos de un usuario en la base de datos.
        *
        * Este método permite actualizar los datos de un usuario en la tabla `users`. Los campos que pueden ser actualizados
        * son el nombre de usuario, dirección de correo electrónico, dirección y teléfono. Los valores que se pasan en el 
        * parámetro `$userData` son opcionales y solo se actualizarán los que se incluyan. La consulta se construye dinámicamente
        * según los datos proporcionados y se ejecuta sobre el `user_id` específico.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario a actualizar.
        * @param array $userData Un arreglo asociativo con los datos a actualizar. Las claves pueden ser:
        *                        - 'userName' => El nombre de usuario.
        *                        - 'emailAddress' => La dirección de correo electrónico del usuario.
        *                        - 'address' => La dirección física del usuario.
        *                        - 'phone' => El número de teléfono del usuario.
        * 
        * @return bool `true` si la actualización fue exitosa, `false` si ocurrió un error durante el proceso.
    */
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


    /**
        * Cambia la contraseña de un usuario.
        *
        * Este método permite cambiar la contraseña de un usuario verificando primero que la contraseña actual
        * coincida con la almacenada en la base de datos. Si la verificación es exitosa, la nueva contraseña se
        * encripta y se actualiza en la base de datos.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuya contraseña se desea cambiar.
        * @param string $currentPassword La contraseña actual del usuario.
        * @param string $newPassword La nueva contraseña que se desea establecer.
        * 
        * @return array|bool Retorna un arreglo con un mensaje de error si la contraseña actual no coincide, 
        *                    o `true` si la contraseña se actualiza correctamente, o `false` en caso de error.
    */
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


    /**
        * Envía un enlace de restablecimiento de contraseña por correo electrónico.
        *
        * Este método permite enviar un enlace de restablecimiento de contraseña a la dirección de correo electrónico
        * proporcionada. Si el correo existe en la base de datos, se genera un token único, se almacena en la tabla
        * `password_resets`, y se envía un correo con el enlace para restablecer la contraseña.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $data Datos del usuario que contiene el correo electrónico.
        * 
        * @return array Un arreglo con el estado de la operación:
        *               - 'status' puede ser 'success' o 'error'.
        *               - 'message' proporciona más detalles sobre el resultado.
    */
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
