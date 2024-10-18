<?php
require_once '../config/dbConnection.php';
require_once '../models/passwordResetsModel.php';

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

    // No será terminada porque aún me falta comprender la lógica de cómo se recuperará la contraseña, especialmente en cómo voy a hacer para que los métodos ya hechos manden el token al usuario, se resetee o cree uno nuevo, se verifique y que finalmente pueda recuperar la contraseña.
}