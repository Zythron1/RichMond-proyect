<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ShipmentModel.php';

class ShipmentController {
    private $connection;
    private $shipmentModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->shipmentModel = new ShipmentModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function getAllOrder () {
        // paso 1: Llamar al método requerido
        $shipments = $this->shipmentModel->getAllShipments($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($shipments)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron envíos'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $shipments
            ]);
        }
    }

    public function getOrderById ($data) {
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
        $shipment = $this->shipmentModel->getShipmentById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($shipment)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Envío no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $shipment
            ]);
        }
    }
}