<?php
require_once '../config/dbConnection.php';
require_once '../models/shoppingBagModel.php';

class ShoppingBagController {
    private $connection;
    private $shoppingBag;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->shoppingBag = new ShoppingBagModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function getShoppingBagById ($userIdEncoded) {
        // paso 1: Verificar datos recibidos
        if (!($userIdEncoded && is_numeric($userIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $userId = (int)$userIdEncoded;
        $activeShoppingBag = $this->shoppingBag->getShoppingBagById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($activeShoppingBag)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Bolsa de compra no encontrada.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $activeShoppingBag
            ]);
        }
    }

    public function createShoppingBag ($shoppingBagDataEncoded) {
        // paso 1: Decodificar los datos recibidos
        $shoppingBagData = json_decode($shoppingBagDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa de compra no válidos.'
            ]);
            return;
        }

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($shoppingBagData)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa de compra faltantes, se requiere el id de usuario, el id del producto y cantidad de la bolsa de compra.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newShoppingBagId = $this->shoppingBag->createShoppingBag($this->connection, $shoppingBagData);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newShoppingBagId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear la bolsa de compra en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Bolsa de compra creada exitosamente.',
                'data' => $newShoppingBagId
            ]);
        }
    }

    public function addProduct ($shoppingBagDataEncoded) {
        // paso 1: Decodificar los datos recibidos y validar la decodificación
        $shoppingBagData = json_decode($shoppingBagDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa de compra no válidos.'
            ]);
            return;
        }
        
        // paso 2: Validar los datos recibidos userId productId quantity
        if (count(array_filter($shoppingBagData)) !== 3 && count(array_filter($shoppingBagData, 'is_numeric')) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa de compra faltantes, se requiere el id de usuario, el id del producto y la cantidad.'
            ]);
            return;
        }

        // paso 3: convertir los datos a int y llamar al método requerido
        $userId = (int)$shoppingBagData['userId'];
        $productId = (int)$shoppingBagData['productId'];
        $quantity = (int)$shoppingBagData['quantity'];
        $shoppingBagResult = $this->shoppingBag->addProduct($this->connection, $userId, $productId, $quantity);

        // paso 4: Verificar la respuesta del método.
        if ($shoppingBagResult['status'] === 'error') {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $shoppingBagResult['message']
            ]);
        } else {
            // paso 5: Respuesta http 201 y mensaje
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $shoppingBagResult['message']
            ]);
        }
    }

    public function checkOuts ($userIdEncoded) {
        // paso 1: Verificar el dato recibido
        if (empty($userIdEncoded) && !is_numeric($userIdEncoded)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: pasar el número a entero y llamar al método necesario
        $userId = (int)$userIdEncoded;
        $shoppingBagResult = $this->shoppingBag->checkOuts($this->connection, $userId);

        // paso 3: Verificar el resultado del método
        if ($shoppingBagResult['status'] === 'error') {
            // paso 4: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => $shoppingBagResult['status'],
                'message' => $shoppingBagResult['message']
            ]);
        } else {
            // paso 4: Respuesta http 201 y mensaje
            http_response_code(201);
            echo json_encode([
                'status' => $shoppingBagResult['status'],
                'message' => $shoppingBagResult['message'],
                'message' => $shoppingBagResult['orderId']
            ]);
        }
    }

    public function deleteProduct ($shoppingBagDataEncoded) {
        // paso 1: decodificar los datos recibidos
        $shoppingBagData = json_decode($shoppingBagDataEncoded, true);

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($shoppingBagData, 'is_numeric')) !== 2) {
            http_response_code();
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos inválidos o faltantes.'
            ]);
            return;
        }

        // paso 3: pasar el número a entero y llamar al método necesario
        $userId = (int)$shoppingBagData['userId'];
        $productId = (int)$shoppingBagData['productId'];
        $shoppingBagResult = $this->shoppingBag->deleteProduct($this->connection, $userId, $productId);

        // paso 4: Verificar el resultado del método
        if ($shoppingBagResult['status'] === 'error') {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => $shoppingBagResult['status'],
                'message' => $shoppingBagResult['message']
            ]);
        } else {
            // paso 6: Respuesta http 204 y mensaje
            http_response_code(204);
            echo json_encode([
                'status' => $shoppingBagResult['status'],
                'message' => $shoppingBagResult['message']
            ]);
        }
    }
}