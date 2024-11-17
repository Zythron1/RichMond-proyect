<?php
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ProductModel.php';

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

    public function getProductById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['productId'] && is_numeric($data['productId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del producto inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $productId = (int)$data['productId'];
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

    public function createProduct ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data)) !== 6) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del producto faltantes, se requiere el nombre, descripción, stock, precio, url, id de la categoría del producto.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newProductId = $this->productModel->createProduct($this->connection, $data);

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

    public function updateProduct ($data) {
        // paso 2: Verficar los datos recibidos
        if (!($data['productId'] && is_numeric($data['productId']) && count(array_filter($data)) > 1)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos del producto para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $productId = (int)$data['productId'];

        // paso 4: Verificar la respuesta del método
        if (!$this->productModel->updateProduct($this->connection, $productId, $data)) {
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

    public function deleteProduct ($data) {
        // paso 1: Varificar los datos recibidos
        if (!($data['productId'] && is_numeric($data['productId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere los datos del producto válidos para eliminarlo.'
            ]);
            return;
        }

        // paso 2: Convertir productId a entero y llamar al método necesario
        $productId = (int)$data['productId'];
        
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

    public function getProductsByCategoryWithLimitAndOffset($data) {
        if (count($data) !== 4) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo realizar la acción, intenta de nuevo.',
                'messageToDeveloper' => 'Hace falta datos para poder realizar el método.',
                'data' => $data
            ]);
            return;
        }

        $productsResponse = $this->productModel->getProductsByCategoryWithLimitAndOffset($this->connection, $data);

        if ($productsResponse['status'] === 'error') {
            http_response_code(404);
            echo json_encode($productsResponse);
        } else {
            http_response_code(200);
            echo json_encode($productsResponse);
        }
    }

}