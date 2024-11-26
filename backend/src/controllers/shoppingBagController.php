<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `ShoppingBagModel.php`: Modelo para gestionar la bolsa de compras y sus operaciones.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ShoppingBagModel.php';


class ShoppingBagController {
    private $connection;
    private $shoppingBag;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de la bolsa de compras.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Obtiene la bolsa de compras activa de un usuario.
        *
        * Este método valida el ID del usuario recibido, llama al modelo para obtener la bolsa de compras activa
        * asociada al usuario y responde según el resultado de la consulta.
        *
        * @param array $data Datos necesarios para obtener la bolsa de compras. El arreglo debe contener:
        *     - 'userId': ID del usuario cuya bolsa de compras se desea obtener (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encontró la bolsa de compras, incluye los datos en la respuesta.
        *     - HTTP 400: Si el ID del usuario es inválido o no se proporcionó.
        *     - HTTP 404: Si no se encontró una bolsa de compras activa para el usuario.
    */
    public function getShoppingBagById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['userId'] && is_numeric($data['userId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $userId = (int)$data['userId'];
        $activeShoppingBag = $this->shoppingBag->getShoppingBagById($this->connection, $data);

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


    /**
        * Crea una nueva bolsa de compras para un usuario.
        *
        * Este método valida los datos recibidos, como el ID de usuario, el ID de producto y la cantidad.
        * Luego, llama al modelo para crear la bolsa de compras en la base de datos y responde según el resultado.
        *
        * @param array $data Datos necesarios para crear la bolsa de compras. El arreglo debe contener:
        *     - 'userId': ID del usuario para quien se crea la bolsa de compras (requerido, numérico).
        *     - 'productId': ID del producto que se va a añadir a la bolsa de compras (requerido, numérico).
        *     - 'quantity': Cantidad del producto a añadir a la bolsa (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si la bolsa de compras se creó exitosamente.
        *     - HTTP 400: Si faltan datos necesarios para crear la bolsa de compras.
        *     - HTTP 500: Si hubo un error al intentar crear la bolsa de compras.
    */
    public function createShoppingBag ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa de compra faltantes, se requiere el id de usuario, el id del producto y cantidad de la bolsa de compra.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newShoppingBagId = $this->shoppingBag->createShoppingBag($this->connection, $data);

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


    /**
        * Añade un producto a la bolsa de compras de un usuario.
        *
        * Este método valida que los datos recibidos para el ID del usuario y el ID del producto sean correctos.
        * Luego, llama al modelo para añadir el producto a la bolsa de compras del usuario.
        *
        * @param array $data Datos necesarios para añadir un producto a la bolsa de compras. El arreglo debe contener:
        *     - 'userId': ID del usuario que tiene la bolsa de compras (requerido, numérico).
        *     - 'productId': ID del producto que se va a añadir a la bolsa de compras (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el producto fue añadido exitosamente a la bolsa de compras.
        *     - HTTP 400: Si los datos necesarios (ID de usuario o ID de producto) están faltantes o no son válidos.
        *     - HTTP 500: Si hubo un error al intentar añadir el producto a la bolsa de compras.
    */
    public function addProduct ($data) {
        // paso 2: Validar los datos recibidos userId productId 
        if (count(array_filter($data)) !== 2 && count(array_filter($data, 'is_numeric')) !== 2) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere el id.',
                'messageToDeveloper' => 'Datos de la bolsa de compra faltantes, se requiere el id de usuario y el id del producto.'
            ]);
            return;
        }

        // paso 3: convertir los datos a int y llamar al método requerido
        $userId = (int)$data['userId'];
        $productId = (int)$data['productId'];

        $shoppingBagResult = $this->shoppingBag->addProduct($this->connection, $userId, $productId);

        // paso 4: Verificar la respuesta del método.
        if ($shoppingBagResult['status'] === 'error') {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode($shoppingBagResult);
        } else {
            // paso 5: Respuesta http 201 y mensaje
            http_response_code(200);
            echo json_encode($shoppingBagResult);
        }
    }


    /**
        * Realiza el proceso de checkout para un usuario, gestionando la bolsa de compras y generando una orden.
        *
        * Este método verifica que el ID de usuario recibido sea válido, luego llama al modelo para procesar el checkout,
        * realizando el pago y actualizando la bolsa de compras del usuario.
        *
        * @param array $data Datos necesarios para realizar el checkout. El arreglo debe contener:
        *     - 'userId': ID del usuario que realiza el checkout (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si el checkout se realizó correctamente y se generó una orden.
        *     - HTTP 400: Si el ID del usuario es inválido o faltante.
        *     - HTTP 500: Si hubo un error al procesar el checkout.
    */
    public function checkOuts ($data) {
        // paso 1: Verificar el dato recibido
        if (empty($data) && !is_numeric($data['userId'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: pasar el número a entero y llamar al método necesario
        $userId = (int)$data;
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


    /**
        * Elimina un producto de la bolsa de compras de un usuario.
        *
        * Este método valida los datos recibidos, luego llama al modelo para eliminar un producto de la bolsa de compras
        * del usuario. Si el proceso se realiza correctamente, se responde con un mensaje de éxito. Si ocurre un error, 
        * se devuelve una respuesta de error.
        *
        * @param array $data Datos necesarios para eliminar el producto. El arreglo debe contener:
        *     - 'userId': ID del usuario (requerido, numérico).
        *     - 'productId': ID del producto a eliminar (requerido, numérico).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 204: Si el producto se eliminó exitosamente.
        *     - HTTP 400: Si los datos recibidos son inválidos o faltantes.
        *     - HTTP 500: Si hubo un error al intentar eliminar el producto.
    */
    public function deleteProduct ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data, 'is_numeric')) !== 2) {
            http_response_code();
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos inválidos o faltantes.'
            ]);
            return;
        }

        // paso 3: pasar el número a entero y llamar al método necesario
        $userId = (int)$data['userId'];
        $productId = (int)$data['productId'];
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