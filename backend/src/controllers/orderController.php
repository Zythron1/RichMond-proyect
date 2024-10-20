<?php
require_once '../config/dbConnection.php';
require_once '../models/orderModel.php';

class OrderController {
    private $connection;
    private $orderModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->orderModel = new OrderModel;
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
        $orders = $this->orderModel->getAllOrder($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($orders)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron ordenes'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $orders
            ]);
        }
    }

    public function getOrderById ($orderIdEncoded) {
        // paso 1: Verificar datos recibidos
        if (!($orderIdEncoded && is_numeric($orderIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de la orden inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $orderId = (int)$orderIdEncoded;
        $order = $this->orderModel->getOrderById($this->connection, $orderId);

        // paso 3: Verificar datos devueltos del método
        if (empty($order)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'orden no encontrada.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $order
            ]);
        }
    }

    public function createOrder ($orderDataEncoded) {
        // paso 1: Decodificar los datos recibidos
        $orderData = json_decode($orderDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la orden no válidos.'
            ]);
            return;
        }

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($orderData)) !== 2) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la orden faltantes, se requiere el id de usuario y total.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newOrderId = $this->orderModel->createOrder($this->connection, $orderData);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newOrderId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear la orden en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'orden creada exitosamente.',
                'data' => $newOrderId
            ]);
        }
    }

    public function updateOrder ($orderIdEncoded, $orderDataEncoded) {
        // paso 1: Decodificar los datos enviados
        $orderData = json_decode($orderDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la orden no válidos.'
            ]);
            return;
        }

        // paso 2: Verficar los datos recibidos
        if (!($orderIdEncoded && is_numeric($orderIdEncoded) && count(array_filter($orderData)))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos de la orden para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $orderId = (int)$orderIdEncoded;

        // paso 4: Verificar la respuesta del método
        if (!$this->orderModel->updateOrder($this->connection, $orderId, $orderData)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo actualizar los datos de la orden en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 200 y mensaje
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'orden actualizada exitosamente.'
            ]);
        }
    }

    public function deleteOrder ($orderIdEncoded) {
        // paso 1: Varificar los datos recibidos
        if (!($orderIdEncoded && is_numeric($orderIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere los datos de la orden válidos para eliminarla.'
            ]);
            return;
        }

        // paso 2: Convertir productId a entero y llamar al método necesario
        $orderId = (int)$orderIdEncoded;
        
        // paso 3: Verificar la respuesta del método
        if (!$this->orderModel->deleteOrder($this->connection, $orderId)) {
            // paso 4: Respuesta http 500 o 400 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo eliminar la orden en este momento.'
            ]);
        } else {
            // paso 5: Respuesta http 204 y mensaje
            http_response_code(204);
            echo json_encode([
                'status' => 'success',
                'message' => 'Eliminación de la orden exitosa.'
            ]);
        }
    }
}