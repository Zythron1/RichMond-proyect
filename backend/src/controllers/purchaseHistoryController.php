<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PurchaseHistoryModel.php';

class PurchaseHistoryController {
    private $connection;
    private $purchaseHistoryModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->purchaseHistoryModel = new PurchaseHistoryModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function getAllPurchaseHistory () {
        // paso 1: Llamar al método requerido
        $purchasesHistory = $this->purchaseHistoryModel->getAllPurchaseHistory($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($purchasesHistory)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontró el historial de compras'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $purchasesHistory
            ]);
        }
    }

    public function GetpurchaseHistoryById ($data) {
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
        $purchaseHistory = $this->purchaseHistoryModel->GetpurchaseHistoryById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($purchaseHistory)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Historial de compra no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $purchaseHistory
            ]);
        }
    }

    public function createPurchaseHistory ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data, 'is_numeric')) !== 4) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del historial de compra faltantes, se requiere id de usuario, id de producto, cantidad y total.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newPurchaseHistoryId = $this->purchaseHistoryModel->createPurchaseHistory($this->connection, $data);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newPurchaseHistoryId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear el historial de comrpa en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Historial de compra creado exitosamente.',
                'data' => $newPurchaseHistoryId
            ]);
        }
    }
}