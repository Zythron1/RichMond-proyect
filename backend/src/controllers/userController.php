<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `UserModel.php`: Modelo para gestionar la información y operaciones relacionadas con los usuarios.
    * - `UserHelpers.php`: Funciones auxiliares relacionadas con los usuarios, como validaciones de datos.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/UserModel.php';
require_once './backend/src/helpers/UserHelpers.php';


class UserController {
    private $connection;
    private $userModel;

    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de usuario. 
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene 
        * la ejecución y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Obtiene todos los usuarios y devuelve una respuesta en formato JSON.
        *
        * Este método solicita a `userModel` que recupere todos los usuarios desde la base de datos.
        * Si no se encuentran usuarios, responde con un código HTTP 404 y un mensaje de error.
        * Si se encuentran usuarios, responde con un código HTTP 200 y los datos en formato JSON.
        *
        * @return void Este método no devuelve un valor, sino que envía una respuesta JSON al cliente.
    */
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


    /**
        * Obtiene la información de un usuario por su ID.
        *
        * Este método busca un usuario en la base de datos utilizando su ID. Primero verifica que el ID proporcionado
        * sea válido y numérico. Luego llama al modelo para recuperar los datos del usuario. La respuesta HTTP 
        * varía dependiendo de si el usuario es encontrado o no.
        *
        * @param array $data Datos necesarios para buscar al usuario. El arreglo debe contener:
        *     - 'userId': ID del usuario a buscar (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el usuario fue encontrado, incluye los datos del usuario en formato JSON.
        *     - HTTP 404: Si el usuario no fue encontrado.
        *     - HTTP 400: Si el ID es inválido o no fue proporcionado.
    */
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


    /**
        * Crea un nuevo usuario en la base de datos.
        *
        * Este método permite registrar un nuevo usuario verificando que los datos esenciales sean proporcionados.
        * Si los datos son válidos, llama al modelo para realizar la inserción en la base de datos y responde
        * con el resultado del proceso.
        *
        * @param array $data Datos necesarios para crear el usuario. El arreglo debe contener:
        *     - 'userName': Nombre de usuario (requerido, cadena de texto).
        *     - 'emailAddress': Dirección de correo electrónico (requerido, cadena de texto).
        *     - 'userPassword': Contraseña del usuario (requerido, cadena de texto).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si el usuario fue creado exitosamente, incluye el ID del nuevo usuario en el mensaje.
        *     - HTTP 400: Si faltan datos requeridos.
        *     - HTTP 500: Si hubo un error al intentar crear el usuario.
    */
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


    /**
        * Inicia sesión de un usuario en el sistema.
        *
        * Este método valida los datos proporcionados para el inicio de sesión. Si los datos son válidos,
        * se llama al modelo para verificar las credenciales del usuario. Según el resultado, responde con
        * los detalles del inicio de sesión o un error.
        *
        * @param array $data Datos necesarios para iniciar sesión. El arreglo debe contener:
        *     - 'emailAddress': Dirección de correo electrónico del usuario (requerido, cadena de texto).
        *     - 'userPassword': Contraseña del usuario (requerido, cadena de texto).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si el inicio de sesión fue exitoso, incluye detalles del usuario o token en la respuesta.
        *     - HTTP 400: Si los datos enviados no son válidos.
        *     - HTTP 404: Si las credenciales son incorrectas o el usuario no existe.
    */
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
            http_response_code(201);
            echo json_encode($loginResponse);
        }
    }


    /**
        * Cierra la sesión de un usuario en el sistema.
        *
        * Este método llama al modelo para realizar las operaciones necesarias de cierre de sesión,
        * como invalidar tokens o limpiar datos de sesión. Responde según el resultado de la operación.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si la sesión se cerró exitosamente.
        *     - HTTP 400: Si hubo un error al intentar cerrar la sesión.
    */
    public function logout () {
        $ResponseLogout = $this->userModel->logout();

        if ($ResponseLogout['status'] === 'error') {
            http_response_code(400);
            echo json_encode($ResponseLogout);
        } else {
            http_response_code(200);
            echo json_encode($ResponseLogout);
        }
    }


    /**
        * Actualiza la información de un usuario existente en la base de datos.
        *
        * Este método verifica que el ID del usuario sea válido y que se hayan proporcionado datos 
        * para actualizar. Llama al modelo para realizar la actualización en la base de datos y 
        * responde según el resultado.
        *
        * @param array $data Datos necesarios para actualizar el usuario. El arreglo debe contener:
        *     - 'userId': ID del usuario a actualizar (requerido, numérico).
        *     - Otros campos opcionales para actualizar, por ejemplo:
        *         - 'userName': Nuevo nombre de usuario (opcional, cadena de texto).
        *         - 'emailAddress': Nueva dirección de correo electrónico (opcional, cadena de texto).
        *         - 'address': Nueva dirección del usuario (opcional, cadena de texto).
        *         - 'phone': Nuevo número de teléfono (opcional, cadena de texto).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si la actualización fue exitosa.
        *     - HTTP 400: Si no se proporcionaron datos para actualizar.
        *     - HTTP 404: Si el ID del usuario es inválido o no se proporcionó.
        *     - HTTP 500: Si hubo un error al intentar actualizar los datos.
    */
    public function updateUser ($data) {
        // Paso 1: Verificar si userId tiene contenido y es numérico
        if ($data['userId'] && is_numeric($data['userId'])) {
            $userId = (int)$data['userId']; // Convertimos a entero
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de usuario faltante o inválido.'
            ]);
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


    /**
        * Cambia la contraseña de un usuario.
        *
        * Este método permite actualizar la contraseña de un usuario después de validar el ID del usuario,
        * la contraseña actual y la nueva contraseña. Llama al modelo para realizar la actualización en la base
        * de datos y responde según el resultado del proceso.
        *
        * @param array $data Datos necesarios para cambiar la contraseña. El arreglo debe contener:
        *     - 'userId': ID del usuario cuyo password se va a cambiar (requerido, numérico).
        *     - 'currentPassword': Contraseña actual del usuario (requerido, cadena de texto).
        *     - 'newPassword': Nueva contraseña del usuario (requerido, cadena de texto).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si la contraseña fue actualizada exitosamente.
        *     - HTTP 400: Si faltan datos requeridos o las credenciales son incorrectas.
        *     - HTTP 500: Si hubo un error al intentar actualizar la contraseña.
    */
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


    public function sendUrlToEmail ($data) {
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data['emailAddress'])) {
            http_response_code(400);
            return [
                'status' => 'error',
                'message' => 'Email no válido'
            ];
        }

        $response = $this->userModel->sendUrlToEmail($this->connection, $data);

        if ($response['status'] === 'error') {
            http_response_code(404);
            echo json_encode($response);
        } else {
            http_response_code(200);
            echo json_encode($response); 
        }
    }
}