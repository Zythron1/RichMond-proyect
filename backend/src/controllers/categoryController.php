<?php
require_once '../config/dbConnection.php';
require_once '../models/categoryModel.php';

class CategoryController {
    private $connection;
    private $categoryModel;

    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->categoryModel = new CategoryModel;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos'. $e->getMessage()
            ]);
        }
    }
    public function getAllCategories () {
        // paso 1: Llamar al método requerido
        $categories = $this->categoryModel->getAllCategories($this->connection);
        
        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($categories)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron categorías'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $categories
            ]);
        }
    }

    public function getCategorieById ($categoryIdEncoded) {
        // paso 1: Verificar datos recibidos
        if (!($categoryIdEncoded && is_numeric($categoryIdEncoded))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de categoria inválido o faltante.'
            ]); 
            return;
        }
        
        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $categoryId = (int)$categoryIdEncoded;
        $category = $this->categoryModel->getCategoryById($this->connection, $categoryId);
        
        // paso 3: Verificar datos devueltos del método
        if (empty($category)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Categoria no encontrada.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $category
            ]);
        }
    }

    public function createCategory ($categoryDataEncoded) {
        // paso 1: Decodificar los datos recibidos
        $categoryData = json_decode($categoryDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la categoría no válidos.'
            ]); 
            return;
        }

        // paso 2: Verificar los datos recibidos
        if (count(array_filter($categoryData)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la categoría faltantes, se requiere el nombre, descripción y status de la categoría.'
            ]); 
            return;
        }

        // paso 3: Llamar al método necesario
        $newCategoryId = $this->categoryModel->createCategory($this->connection, $categoryData);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newCategoryId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear la categoría en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Categoría creada exitosamente.',
                'data' => $newCategoryId
            ]);
        }
    }

    public function updateCategory ($categoryIdEncoded, $categoryDataEncoded) {
        // paso 1: Decodificar los datos enviados
        $categoryData = json_decode($categoryDataEncoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la categoría no válidos.'
            ]);
            return;
        }

        // paso 2: Verficar los datos recibidos
        if (!($categoryIdEncoded && is_numeric($categoryIdEncoded) && count(array_filter($categoryData)))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos de la categoría para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $categoryId = (int)$categoryIdEncoded;

        // paso 4: Verificar la respuesta del método
        if (!$this->categoryModel->updateCategory($this->connection, $categoryId, $categoryData)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo actualizar los datos en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 200 y mensaje
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Categoría actualizada exitosamente.'
            ]);
        }
    }

}