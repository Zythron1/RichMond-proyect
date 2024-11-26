<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `ShipmentModel.php`: Modelo para gestionar los envíos y su lógica.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/ShipmentModel.php';


class ShipmentController {
    private $connection;
    private $shipmentModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de envíos.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->shipmentModel = new ShipmentModel;
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
        * Obtiene todos los envíos registrados en el sistema.
        *
        * Este método llama al modelo para obtener todos los envíos de la base de datos. Si se encuentran envíos, se devuelve
        * la información de los mismos. Si no se encuentran envíos, se devuelve un mensaje de error.
        *
        * @param void No recibe parámetros.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentran envíos y se devuelven correctamente.
        *     - HTTP 404: Si no se encuentran envíos.
    */
    public function getAllOrder () {
        // paso 1: Llamar al método requerido
        $shipments = $this->shipmentModel->getAllShipments($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($shipments)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron envíos'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $shipments
            ]);
        }
    }


    /**
        * Obtiene el envío asociado a un usuario específico mediante su ID.
        *
        * Este método verifica la validez del ID de usuario recibido, luego llama al modelo para obtener el envío asociado
        * a ese usuario. Si se encuentra el envío, devuelve la información correspondiente. Si no se encuentra, devuelve un mensaje de error.
        *
        * @param array $data Un array asociativo que debe contener:
        *     - 'userId' (int): El ID del usuario cuyo envío se desea obtener.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el envío asociado al usuario es encontrado y se devuelve correctamente.
        *     - HTTP 404: Si no se encuentra el envío asociado al usuario.
        *     - HTTP 400: Si el 'userId' proporcionado es inválido o falta.
    */
    public function getOrderById ($data) {
        // paso 1: Verificar datos recibidos
        if (!($data['userId'] && is_numeric($data['userId']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID de usuario inválido o faltante.'
            ]);
            return;
        }

        // paso 2: Convertir categoryId a entero y llamar al método correspondiente
        $userId = (int)$data['userId'];
        $shipment = $this->shipmentModel->getShipmentById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($shipment)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Envío no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $shipment
            ]);
        }
    }
}