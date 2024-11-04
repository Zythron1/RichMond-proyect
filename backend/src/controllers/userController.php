<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/UserModel.php';
require_once './backend/src/helpers/UserHelpers.php';

class UserController {
    private $connection;
    private $userModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->userModel = new UserModel();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos'. $e->getMessage()
            ]);
            exit();
        }
    }

    public function getAllUsers () {
        $users = $this->userModel->getAllUsers($this->connection);

        if (empty($users)) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron usuarios'
            ]);
        } else {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $users
            ]);
        }
    }

    public function getUserById ($data) {
        // Paso 1: verificar que ID existe y es un valor numérico
        if ($data['userId'] && is_numeric($data['userId'])) {
            // Paso 2: convertir numero a entero
            $userId = (int)$data['userId']; // Convertimos a entero

            // Paso 3: Llamar al método del modelo y pasar los parámetros
            $user = $this->userModel->getUserById($this->connection, $userId);

            // Paso 4: Verificar si se encontró un usuario
            if ($user) {
                // Paso 5: Respuesta HTTP 200 e información del usuario encontrado
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'data' => $user
                ]);
            } else {
                // Paso 6: Respuesta HTTP 404 si no se encuentra el usuario y respuesta de error
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } else {
            // Respuesta HTTP 400 si el ID no es válido o no fue enviado
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de usuario inválido o faltante'
            ]);
        }
    }

    public function createUser ($data) {
        // paso 2: Verificar que los datos recibidos no sean vacíos
        if ($data['userName'] && $data['emailAddress'] && $data['userPassword']) {
            // paso 3: Llamar el método correspondiente y pasar los parámetros
            $newUserId = $this->userModel->createUser($this->connection, $data);

            // paso 4: Verificar el userId creado
            if (empty($newUserId)) {
                // paso 5: Respuesta http 500 si no se crea el usuario 
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo crear el usuario en este momento'
                ]);
            } else {
                // paso 6: Respuesta htttp 201 y userId del nuevo usuario
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuario creado exitosamente. Ya puedes ingresar a tu cuenta',
                    'userId' => $newUserId
                ]);
            }
        } else {
            // Repuesta http 400 si los datos obligatorios no fueron enviados
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de usuario faltantes, se requiere nombre de usuario, email y contraseña'
            ]);
        }
    }

    public function login ($data) {
        $validationResponse = UserHelpers::validateUserData($data);

        if ($validationResponse['status'] === 'error') {
            http_response_code(400);
            echo json_encode($validationResponse);
        }

        $loginResponse = $this->userModel->login($this->connection, $data);

        if ($loginResponse['status'] === 'error') {
            http_response_code(404);
            echo json_encode($loginResponse);
        } else {
            http_response_code(200);
            echo json_encode($loginResponse);
        }
    }

    public function updateUser ($data) {
        // Paso 1: Verificar si userId tiene contenido y es numérico
        if ($data['userId'] && is_numeric($data['userId'])) {
            $userId = (int)$data['userId']; // Convertimos a entero
        } else {
            return;
        }

        // paso 2: Verificar que los datos recibidos no sean vacíos
        if (count(array_filter($data))) {
            // paso 3: Llamar al método correspondiente y pasar los parámetros
            // paso 4: Verificar la respuesta del método
            if (!$this->userModel->updateUser($this->connection, $data['userId'], $data)) {
                // paso 5: Respuesta http 500 y mensaje de error
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo actualizar los datos en este momento.'
                ]);
            } else {
                // paso 6: Respuesta http 200 y mensaje de actualización exitosa
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuario actualizado exitosamente.'
                ]);
                
            }
        } else {
            // Respuesta http 400 si los datos no fueron enviados.
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren datos del usuario para actualizar.'
            ]);
        }
    }

    public function changePassword ($data) {
        // Paso 1: Verificar si el userId tiene contenido y es numérico
        if ($data['userId'] && is_numeric($data['userId'])) {
            $userId = (int)$data['userId']; // Convertimos a entero
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere un userId válido o existente.'
            ]);
            return;
        }

        // Paso 3: Verificar que las contraseñas necesarias tengan contenido
        if (empty($data['currentPassword']) || empty($data['newPassword'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren la contraseña actual y la nueva contraseña.'
            ]);
            return;
        }
        // paso 4: llamar al método requerido y pasar los parámetros
        $passwordChangeResult = $this->userModel->changePassword($this->connection, $data['userId'], $data['currentPassword'], $data['newPassword']);

        // paso 5: Verificar la respuesta del método
        if ($passwordChangeResult['status'] === 'error') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $passwordChangeResult['message']
            ]);
            return;
        } else if (!$passwordChangeResult){
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo actualizar la contraseña en este momento'
            ]);
            return;
        } else {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contraseña actualizada'
            ]);
        }
    }

    public function passwordRecovery ($data) {
        // paso 1: Verificar contenido de los datos recibidos 
        if ($data['userId'] && is_numeric($data['userId'])) {
            $userId = (int)$data['userId']; // Convertimos a entero
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere un userId válido o existente.'
            ]);
            return;
        }

        if (empty($userId) || empty($data['newPassword'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se necesitan los datos completos'
            ]);
            return;
        }

        // paso 2: Llamar el método requerido para recuperar contraseña
        // paso 3: Verificar el resultado del método.
        if (!$this->userModel->passwordRecovery($this->connection, $data['userId'], $data['newPassword'])) {
            // paso 4: Respuesta http 500 y mensaje de error
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo Recuperar la contraseña en este momento.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y mensaje de actualizado con éxito
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contraseña recuperada'
            ]);
        }
    }
}

