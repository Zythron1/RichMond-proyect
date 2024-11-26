<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `BagProductModel.php`: Modelo para gestionar los productos en la bolsa de compras.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/BagProductModel.php';


class BagProductController {
    private $connection;
    private $bagProductModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de productos en el carrito de compras.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Crea una nueva relación entre un producto y una bolsa de compras.
        *
        * Este método verifica que se hayan recibido los datos necesarios para crear la relación entre un producto y una bolsa de
        * compras (ID de la bolsa, ID del producto y cantidad). Si los datos son válidos, llama al modelo para crear la relación
        * y devuelve una respuesta con un código de estado HTTP 201 si la creación es exitosa. Si ocurre un error, responde con un 
        * código de estado HTTP 500 y un mensaje de error.
        *
        * @param array $data Datos de la solicitud, que debe incluir los siguientes parámetros:
        *     - 'bagId' (int): El ID de la bolsa de compras.
        *     - 'productId' (int): El ID del producto a agregar a la bolsa.
        *     - 'quantity' (int): La cantidad del producto a agregar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si se crea la relación entre el producto y la bolsa de compras correctamente.
        *     - HTTP 500: Si no se puede crear la relación, con un mensaje de error.
    */
    public function createBagProduct ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data)) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la bolsa y el producto faltantes, se requiere id de bolsa de compra, id de producto y cantidad.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newBagProduct = $this->bagProductModel->createBagProduct($this->connection, $data);

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


    /**
        * Elimina un producto de la bolsa de compras.
        *
        * Este método verifica que se hayan recibido los datos necesarios para eliminar un producto de la bolsa de compras. 
        * Si los datos son válidos, llama al modelo para eliminar el producto y devuelve una respuesta con un código de estado 
        * HTTP 204 si la eliminación es exitosa. Si ocurre un error, responde con un código de estado HTTP 404 y el mensaje 
        * de error correspondiente.
        *
        * @param array $data Datos de la solicitud, que debe incluir los siguientes parámetros:
        *     - 'bagId' (int): El ID de la bolsa de compras de la que se eliminará el producto.
        *     - 'productId' (int): El ID del producto a eliminar de la bolsa.
        *     - 'quantity' (int): La cantidad del producto a eliminar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP:
        *     - HTTP 204: Si el producto es eliminado exitosamente de la bolsa de compras.
        *     - HTTP 404: Si no se encuentra el producto en la bolsa de compras, con un mensaje de error.
    */
    public function deleteProductShoppingBag ($data) {
        if (count($data) !== 3) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo realizar la acción, intenta de nuevo.',
                'messageToDeveloper' => 'Hace falta datos para poder realizar el método.',
                'data' => $data
            ]);
            return;
        }

        $productDeleted = $this->bagProductModel->deleteProductShoppingBag($this->connection, $data);

        if ($productDeleted['status'] === 'error') {
            http_response_code(404);
            echo json_encode($productDeleted);
        } else {
            http_response_code(204);
        }
    }
}