<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `CategoryModel.php`: Modelo para gestionar las categorías.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/CategoryModel.php';


class CategoryController {
    private $connection;
    private $categoryModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de categorías.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Obtiene todas las categorías disponibles.
        *
        * Este método llama al modelo correspondiente para obtener una lista de todas las categorías. Si se encuentran categorías,
        * se devuelve una respuesta con un código de estado HTTP 200 y los datos de las categorías. Si no se encuentran categorías,
        * se devuelve una respuesta con un código de estado HTTP 404 y un mensaje de error.
        *
        * @param void
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentran categorías, con los datos de las categorías.
        *     - HTTP 404: Si no se encuentran categorías, con un mensaje de error.
    */
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


    /**
        * Obtiene los detalles de una categoría por su ID.
        *
        * Este método verifica que el `categoryId` recibido sea válido y numérico. Luego, llama al modelo correspondiente para
        * obtener la categoría con ese ID. Si se encuentra la categoría, se devuelve una respuesta con un código de estado HTTP 200
        * y los datos de la categoría. Si no se encuentra la categoría, se devuelve una respuesta con un código de estado HTTP 404
        * y un mensaje de error.
        *
        * @param array $data Datos de la solicitud, que debe incluir el parámetro 'categoryId' con el ID de la categoría.
        *     - 'categoryId' (int): El ID de la categoría a buscar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentra la categoría, con los datos de la categoría.
        *     - HTTP 404: Si no se encuentra la categoría, con un mensaje de error.
    */
    public function getCategorieById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['categoryId'] && is_numeric($data['categoryId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de categoria inválido o faltante.'
            ]); 
            return;
        }
        
        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $categoryId = (int)$data['categoryId'];
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


    /**
        * Crea una nueva categoría en el sistema.
        *
        * Este método verifica que los datos necesarios (nombre, descripción y status) estén presentes en la solicitud. Si los datos
        * son válidos, llama al modelo para crear la categoría y devuelve una respuesta con un código de estado HTTP 201 y el ID de
        * la nueva categoría. Si no se pueden crear los datos, devuelve una respuesta con un código de estado HTTP 500 y un mensaje
        * de error.
        *
        * @param array $data Datos de la solicitud, que debe incluir los siguientes parámetros:
        *     - 'name' (string): El nombre de la categoría.
        *     - 'description' (string): La descripción de la categoría.
        *     - 'status' (string): El estado de la categoría (activo/inactivo).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si se crea la categoría correctamente, con el ID de la nueva categoría.
        *     - HTTP 500: Si no se puede crear la categoría, con un mensaje de error.
    */
    public function createCategory ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la categoría faltantes, se requiere el nombre, descripción y status de la categoría.'
            ]); 
            return;
        }

        // paso 3: Llamar al método necesario
        $newCategoryId = $this->categoryModel->createCategory($this->connection, $data);

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


    /**
        * Actualiza una categoría existente en el sistema.
        *
        * Este método verifica que se hayan recibido los datos necesarios para actualizar una categoría (ID y al menos un campo
        * a actualizar). Si los datos son válidos, llama al modelo para realizar la actualización y devuelve una respuesta con un 
        * código de estado HTTP 200 si la actualización es exitosa. Si ocurre un error, responde con un código de estado HTTP 500 
        * y un mensaje de error.
        *
        * @param array $data Datos de la solicitud, que debe incluir los siguientes parámetros:
        *     - 'categoryId' (int): El ID de la categoría a actualizar.
        *     - 'name' (string, opcional): El nuevo nombre de la categoría.
        *     - 'description' (string, opcional): La nueva descripción de la categoría.
        *     - 'status' (string, opcional): El nuevo estado de la categoría (activo/inactivo).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se actualiza la categoría correctamente.
        *     - HTTP 500: Si no se puede actualizar la categoría, con un mensaje de error.
    */
    public function updateCategory ($data) {
        // paso 2: Verficar los datos recibidos
        if (!($data['categoryId'] && is_numeric($data['categoryId']) && count(array_filter($data)) > 1)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos de la categoría para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $categoryId = (int)$data['categoryId'];

        // paso 4: Verificar la respuesta del método
        if (!$this->categoryModel->updateCategory($this->connection, $categoryId, $data)) {
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