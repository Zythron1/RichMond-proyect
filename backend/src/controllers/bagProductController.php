<?php
require_once '../config/dbConnection.php';
require_once '../models/bagProductModel.php';

class BagProductController {
    private $connection;
    private $bagProductModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->bagProductModel = new BagProductModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function createOrder ($bagProductDataEncoded) {
        // paso 1: Decodificar los datos recibidos
        $bagProductData = json_decode($bagProductDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa y producto no válidos.'
            ]);
            return;
        }

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($bagProductData)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa y el producto faltantes, se requiere id de bolsa de compra, id de producto y cantidad.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newBagProduct = $this->bagProductModel->createBagProduct($this->connection, $bagProductData);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newBagProduct)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear la bolsa producto en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Bolsa producto creada exitosamente.',
                'data' => $newBagProduct
            ]);
        }
    }

}