<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `PasswordResetModel.php`: Modelo para gestionar los reseteos de contraseña.
    * - `UserHelpers.php`: Funciones auxiliares relacionadas con los usuarios.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PasswordResetModel.php';
require_once './backend/src/helpers/UserHelpers.php';


class PasswordResetController {
    private $connection;
    private $passwordResetModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de restablecimiento de contraseña.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->passwordResetModel = new PasswordResetModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }


    /**
        * Recupera y actualiza la contraseña de un usuario mediante un token de recuperación.
        *
        * Este método permite a un usuario recuperar su contraseña utilizando un token de recuperación y una nueva contraseña.
        * Primero, valida si el token y la nueva contraseña están presentes en los datos recibidos. Luego, valida que la
        * nueva contraseña cumpla con los requisitos establecidos mediante una función de validación. Si la validación es exitosa,
        * el método realiza la actualización de la contraseña. Si los datos son incorrectos o la validación falla, se devuelve un
        * mensaje de error con el código de estado HTTP correspondiente.
        *
        * @param array $data Array asociativo que contiene:
        *     - token (string): El token de recuperación de contraseña.
        *     - newPassword (string): La nueva contraseña del usuario.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si la contraseña se actualiza correctamente.
        *     - HTTP 400: Si falta el token o la nueva contraseña, o si la validación de la contraseña falla.
    */
    function passwordRecovery ($data) {
        if (empty($data['token']) || empty($data['newPassword']) ) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Falta el token o la nueva contraseña.'
            ]);
        }

        $validatedPassword = UserHelpers::validateUserPassword($data['newPassword']);
        if ($validatedPassword['status'] === 'error') {
            return $validatedPassword;
        }

        $PasswordResetModelInstance = new PasswordResetModel;
        $response = $PasswordResetModelInstance->passwordRecovery($this->connection, $data);

        if ($response['status'] === 'error') {
            http_response_code(400);
            echo json_encode($response);
        } else {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente.'
            ]);
        }
    }
}