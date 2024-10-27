<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PaymentMethodModel.php';

class PaymentMethodController {
    private $connection;
    private $paymentMethodModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->paymentMethodModel = new PaymentMethodModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function getPaymentMethodById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['userId'] && is_numeric($data['userId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $userId = (int)$data['userId'];
        $paymentMethods = $this->paymentMethodModel->getPaymentMethodById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($paymentMethods)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Método de pago no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $paymentMethods
            ]);
        }
    }
}