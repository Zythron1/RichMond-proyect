<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `ProductModel.php`: Modelo para gestionar los productos.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ProductModel.php';


class ProductController {
    private $connection;
    private $productModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de productos.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Obtiene todos los productos disponibles en el sistema.
        *
        * Este método llama al modelo para obtener todos los productos registrados en la base de datos. Si no se encuentran productos,
        * se devuelve un mensaje de error con el código de estado HTTP 404. Si se encuentran productos, se devuelve un mensaje de éxito
        * junto con los datos de los productos en formato JSON con el código de estado HTTP 200.
        *
        * @param void
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentran productos y se devuelven correctamente.
        *     - HTTP 404: Si no se encuentran productos.
    */
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


    /**
        * Obtiene un producto específico a partir de su ID.
        *
        * Este método valida el ID del producto recibido y, si es válido, llama al modelo para obtener los detalles del producto con ese ID.
        * Si no se encuentra el producto, se devuelve un mensaje de error con el código de estado HTTP 404. Si el producto se encuentra,
        * se devuelve un mensaje de éxito junto con los detalles del producto en formato JSON con el código de estado HTTP 200.
        *
        * @param array $data Array asociativo que contiene:
        *     - productId (int): El ID del producto a buscar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el producto se encuentra correctamente y se devuelve.
        *     - HTTP 404: Si el producto no se encuentra en la base de datos.
    */
    public function getProductById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['productId'] && is_numeric($data['productId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo cargar el producto, intenta de nuevo.',
                'messageToDeveloper' => 'ID del producto inválido o faltante.'
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
                'message' => 'Producto no encontrado.',
                'messageToDeveloper' => 'No se encontró el producto o hubo un error al hacer la consulta.'

                
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            $product['product_id'] = $productId;
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'producto encontrado.',
                'messageToDeveloper' => 'Ningún problema.',
                'product' => $product
            ]);
        }
    }


    /**
        * Obtiene parcialmente los detalles de un producto específico a partir de su ID.
        *
        * Este método valida el ID del producto recibido y, si es válido, llama al modelo para obtener parcialmente los detalles del producto con ese ID.
        * Si no se encuentra el producto, se devuelve un mensaje de error con el código de estado HTTP 404. Si el producto se encuentra,
        * se devuelve un mensaje de éxito junto con los detalles del producto en formato JSON con el código de estado HTTP 200.
        *
        * @param array $data Array asociativo que contiene:
        *     - productId (int): El ID del producto a buscar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el producto se encuentra correctamente y se devuelve parcialmente.
        *     - HTTP 404: Si el producto no se encuentra en la base de datos.
    */
    public function getPartialProductById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['productId'] && is_numeric($data['productId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo cargar el producto, intenta de nuevo.',
                'messageToDeveloper' => 'ID del producto inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $productId = (int)$data['productId'];
        $product = $this->productModel->getPartialProductById($this->connection, $productId);

        // paso 3: Verificar datos devueltos del método
        if (empty($product)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Producto no encontrado.',
                'messageToDeveloper' => 'No se encontró el producto o hubo un error al hacer la consulta.',
                'productId' => $productId,
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'producto encontrado.',
                'messageToDeveloper' => 'Ningún problema.',
                'product' => $product,
                'productId' => $productId,
            ]);
        }
    }


    /**
        * Crea un nuevo producto en la base de datos.
        *
        * Este método valida los datos del producto recibidos, y si son válidos, llama al modelo para crear un nuevo producto con la información proporcionada.
        * Si falta algún dato requerido, se devuelve un mensaje de error con el código de estado HTTP 400. Si el producto se crea correctamente,
        * se devuelve un mensaje de éxito con el código de estado HTTP 201. Si hay un error al crear el producto, se devuelve un mensaje de error con el código HTTP 500.
        *
        * @param array $data Array asociativo que contiene:
        *     - name (string): Nombre del producto.
        *     - description (string): Descripción del producto.
        *     - stock (int): Cantidad disponible del producto.
        *     - price (float): Precio del producto.
        *     - image_url (string): URL de la imagen del producto.
        *     - category_id (int): ID de la categoría del producto.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si el producto se crea exitosamente.
        *     - HTTP 400: Si faltan datos necesarios para crear el producto.
        *     - HTTP 500: Si ocurre un error al intentar crear el producto.
    */
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


    /**
        * Actualiza los datos de un producto existente en la base de datos.
        *
        * Este método valida los datos recibidos, asegurándose de que el ID del producto sea numérico y que al menos uno de los demás datos esté presente.
        * Luego, llama al modelo para actualizar el producto con la información proporcionada. Si el producto no se actualiza correctamente,
        * se devuelve un mensaje de error con el código de estado HTTP 500. Si la actualización es exitosa, se devuelve un mensaje de éxito con el código HTTP 200.
        *
        * @param array $data Array asociativo que contiene:
        *     - productId (int): ID del producto que se va a actualizar.
        *     - name (string, opcional): Nuevo nombre del producto.
        *     - description (string, opcional): Nueva descripción del producto.
        *     - stock (int, opcional): Nueva cantidad disponible del producto.
        *     - price (float, opcional): Nuevo precio del producto.
        *     - image_url (string, opcional): Nueva URL de la imagen del producto.
        *     - category_id (int, opcional): Nuevo ID de la categoría del producto.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el producto se actualiza correctamente.
        *     - HTTP 400: Si faltan datos necesarios o los datos del producto son inválidos.
        *     - HTTP 500: Si ocurre un error al intentar actualizar el producto.
    */
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


    /**
        * Elimina un producto de la base de datos.
        *
        * Este método valida los datos recibidos, asegurándose de que el ID del producto sea numérico antes de intentar eliminarlo.
        * Luego, llama al modelo para eliminar el producto con el ID proporcionado. Si la eliminación no se puede completar correctamente,
        * se devuelve un mensaje de error con el código de estado HTTP 500. Si la eliminación es exitosa, se devuelve un mensaje de éxito
        * con el código HTTP 204.
        *
        * @param array $data Array asociativo que contiene:
        *     - productId (int): ID del producto que se va a eliminar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 204: Si el producto se elimina correctamente.
        *     - HTTP 400: Si el ID del producto es inválido o faltante.
        *     - HTTP 500: Si ocurre un error al intentar eliminar el producto.
    */
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


    /**
        * Obtiene los productos de una categoría con limitación y desplazamiento (offset).
        *
        * Este método recibe los datos necesarios para realizar una consulta que devuelve los productos de una categoría específica.
        * Se valida que los datos recibidos sean correctos, y luego se llama al modelo para obtener los productos. Si los datos son
        * incorrectos o faltan, se devuelve un mensaje de error. Si la consulta devuelve un error, se responde con un código de estado
        * HTTP 404. Si la consulta es exitosa, se devuelve una lista de productos con un código de estado HTTP 200.
        *
        * @param array $data Array asociativo que contiene:
        *     - categoryId (int): ID de la categoría de productos que se desean consultar.
        *     - limit (int): Número máximo de productos a devolver.
        *     - offset (int): Número de productos a omitir (paginación).
        *     - otros parámetros según se necesiten para la consulta (dependiendo de la implementación del modelo).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si los productos son obtenidos correctamente.
        *     - HTTP 400: Si faltan o son incorrectos los parámetros necesarios.
        *     - HTTP 404: Si no se encuentran productos para la categoría solicitada.
    */
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