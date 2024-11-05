<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PasswordResetModel.php';
require_once './backend/src/helpers/UserHelpers.php';

class PasswordResetController {
    private $connection;
    private $passwordResetModel;

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