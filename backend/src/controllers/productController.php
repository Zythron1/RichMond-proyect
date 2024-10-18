<?php
require_once '../config/dbConnection.php';
require_once '../models/productModel.php';

class ProductController {
    private $connection;
    private $productModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->productModel = new ProductModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos. '. $e->getMessage()
            ]);
            return;
        }
    }

    public function getAllProducts () {
        // paso 1: Llamar al método requerido
        $products = $this->productModel->getAllProducts($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($products)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron productos'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $products
            ]);
        }
    }

    public function getProductById ($productIdEncoded) {
        // paso 1: Verificar datos recibidos
        if (!($productIdEncoded && is_numeric($productIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del producto inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $productId = (int)$productIdEncoded;
        $product = $this->productModel->getProductById($this->connection, $productId);

        // paso 3: Verificar datos devueltos del método
        if (empty($product)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $product
            ]);
        }
    }

    public function createProduct ($producDataEncoded) {
        // paso 1: Decodificar los datos recibidos
        $producData = json_decode($producDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del producto no válidos.'
            ]);
            return;
        }

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($producData)) !== 6) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del producto faltantes, se requiere el nombre, descripción, stock, precio, url, id de la categoría del producto.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newProductId = $this->productModel->createProduct($this->connection, $producData);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newProductId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear el producto en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Producto creado exitosamente.',
                'data' => $newProductId
            ]);
        }
    }

    public function updateProduct ($productIdEncoded, $productDataEncoded) {
        // paso 1: Decodificar los datos enviados
        $productData = json_decode($productDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del producto no válidos.'
            ]);
            return;
        }

        // paso 2: Verficar los datos recibidos
        if (!($productIdEncoded && is_numeric($productIdEncoded) && count(array_filter($productData)))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos del producto para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $productId = (int)$productIdEncoded;

        // paso 4: Verificar la respuesta del método
        if (!$this->productModel->updateProduct($this->connection, $productId, $productData)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo actualizar los datos del producto en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 200 y mensaje
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Producto actualizado exitosamente.'
            ]);
        }
    }

    public function deleteProduct ($productIdEncoded) {
        // paso 1: Varificar los datos recibidos
        if (!($productIdEncoded && is_numeric($productIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere los datos del producto válidos para eliminarlo.'
            ]);
            return;
        }

        // paso 2: Convertir productId a entero y llamar al método necesario
        $productId = (int)$productIdEncoded;
        
        // paso 3: Verificar la respuesta del método
        if (!$this->productModel->deleteProduct($this->connection, $productId)) {
            // paso 4: Respuesta http 500 o 400 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo eliminar el producto en este momento.'
            ]);
        } else {
            // paso 5: Respuesta http 204 y mensaje
            http_response_code(204);
            echo json_encode([
                'status' => 'success',
                'message' => 'Eliminación del producto exitosa.'
            ]);
        }
    }
}