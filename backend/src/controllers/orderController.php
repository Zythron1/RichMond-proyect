<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `OrderModel.php`: Modelo para gestionar los pedidos.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/OrderModel.php';


class OrderController {
    private $connection;
    private $orderModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de órdenes.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
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


    /**
        * Obtiene todas las órdenes almacenadas en el sistema.
        *
        * Este método se encarga de llamar al modelo para obtener todas las órdenes de la base de datos. Si no se encuentran órdenes,
        * se devuelve un mensaje de error con código de estado HTTP 404. Si se encuentran órdenes, se devuelve una respuesta con
        * código HTTP 200 y los datos de las órdenes.
        *
        * @param void No requiere parámetros.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentran órdenes, con los datos de las órdenes.
        *     - HTTP 404: Si no se encuentran órdenes, con un mensaje de error.
    */
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


    /**
        * Obtiene una orden específica por su ID.
        *
        * Este método valida el ID de la orden proporcionado, lo convierte a un valor entero y llama al modelo correspondiente
        * para obtener los detalles de la orden. Si el ID es inválido o la orden no se encuentra, se devuelve un error con
        * código de estado HTTP 400 o 404. Si la orden se encuentra, se devuelve una respuesta con código HTTP 200 y los datos
        * de la orden solicitada.
        *
        * @param array $data Un array asociativo con el parámetro:
        *     - 'orderId' (int): El ID de la orden a buscar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si la orden es encontrada, con los datos de la orden.
        *     - HTTP 400: Si el ID de la orden es inválido o faltante, con un mensaje de error.
        *     - HTTP 404: Si la orden no se encuentra, con un mensaje de error.
    */
    public function getOrderById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['orderId'] && is_numeric($data['orderId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de la orden inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $orderId = (int)$data['orderId'];
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


    /**
        * Crea una nueva orden en el sistema.
        *
        * Este método verifica que los datos necesarios para crear una orden (ID de usuario y total) estén presentes.
        * Si los datos son válidos, se llama al modelo correspondiente para crear la orden. Si la creación es exitosa,
        * se devuelve un código de estado HTTP 201 con el ID de la nueva orden. Si ocurre un error, se devuelve un código
        * de estado HTTP 400 o 500 con el mensaje correspondiente.
        *
        * @param array $data Un array asociativo con los parámetros:
        *     - 'userId' (int): El ID del usuario que realiza la orden.
        *     - 'total' (float): El monto total de la orden.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si la orden es creada exitosamente, con el ID de la nueva orden.
        *     - HTTP 400: Si faltan datos requeridos, con un mensaje de error.
        *     - HTTP 500: Si ocurre un error al crear la orden, con un mensaje de error.
    */
    public function createOrder ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data)) !== 2) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos de la orden faltantes, se requiere el id de usuario y total.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newOrderId = $this->orderModel->createOrder($this->connection, $data);

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


    /**
        * Actualiza los datos de una orden existente.
        *
        * Este método verifica que los datos de la orden sean válidos, incluyendo un ID de orden numérico y datos adicionales
        * para la actualización. Si los datos son válidos, se llama al modelo correspondiente para realizar la actualización.
        * Si la actualización es exitosa, se devuelve un código de estado HTTP 200 con un mensaje de éxito. Si ocurre un error,
        * se devuelve un código de estado HTTP 400 o 500 con el mensaje correspondiente.
        *
        * @param array $data Un array asociativo con los parámetros:
        *     - 'orderId' (int): El ID de la orden a actualizar.
        *     - Otros datos relacionados con la orden que se desean actualizar (por ejemplo, el total o el estado de la orden).
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si la orden se actualiza exitosamente, con un mensaje de éxito.
        *     - HTTP 400: Si los datos recibidos no son válidos, con un mensaje de error.
        *     - HTTP 500: Si ocurre un error al actualizar la orden, con un mensaje de error.
    */
    public function updateOrder ($data) {
        // paso 2: Verficar los datos recibidos
        if (!($data['orderId'] && is_numeric($data['orderId']) && count(array_filter($data)) > 1)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requieren los datos válidos de la orden para actualizar.'
            ]);
            return;
        }

        // paso 3: Convertir el id a entero y llamar al método requerido
        $orderId = (int)$data['orderId'];

        // paso 4: Verificar la respuesta del método
        if (!$this->orderModel->updateOrder($this->connection, $orderId, $data)) {
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


    /**
        * Elimina una orden existente.
        *
        * Este método verifica que los datos de la orden sean válidos, incluyendo un ID de orden numérico. Si los datos son válidos,
        * se llama al modelo correspondiente para eliminar la orden. Si la eliminación es exitosa, se devuelve un código de estado HTTP 204
        * con un mensaje de éxito. Si ocurre un error, se devuelve un código de estado HTTP 400 o 500 con el mensaje correspondiente.
        *
        * @param array $data Un array asociativo con los parámetros:
        *     - 'orderId' (int): El ID de la orden a eliminar.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 204: Si la orden se elimina exitosamente, con un mensaje de éxito.
        *     - HTTP 400: Si los datos recibidos no son válidos, con un mensaje de error.
        *     - HTTP 500: Si ocurre un error al eliminar la orden, con un mensaje de error.
    */
    public function deleteOrder ($data) {
        // paso 1: Varificar los datos recibidos
        if (!($data['orderId'] && is_numeric($data['orderId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Se requiere los datos de la orden válidos para eliminarla.'
            ]);
            return;
        }

        // paso 2: Convertir productId a entero y llamar al método necesario
        $orderId = (int)$data['orderId'];
        
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