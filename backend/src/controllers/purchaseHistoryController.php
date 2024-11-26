<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `PurchaseHistoryModel.php`: Modelo para gestionar el historial de compras.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PurchaseHistoryModel.php';


class PurchaseHistoryController {
    private $connection;
    private $purchaseHistoryModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de historial de compras.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->purchaseHistoryModel = new PurchaseHistoryModel;
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
        * Obtiene el historial de compras de todos los usuarios.
        *
        * Este método llama al modelo para obtener el historial de compras de todos los usuarios. Si se encuentran registros,
        * devuelve la información correspondiente. Si no se encuentran registros, devuelve un mensaje de error.
        *
        * @param void No se requiere ningún parámetro.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si el historial de compras es encontrado y se devuelve correctamente.
        *     - HTTP 404: Si no se encuentra el historial de compras.
    */
    public function getAllPurchaseHistory () {
        // paso 1: Llamar al método requerido
        $purchasesHistory = $this->purchaseHistoryModel->getAllPurchaseHistory($this->connection);

        // paso 2: Verificar los datos devueltos del método llamado
        if (empty($purchasesHistory)) {
            // paso 3: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontró el historial de compras'
            ]);
        } else {
            // paso 4: Respuesta http 200 e informaicón de las categorías
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $purchasesHistory
            ]);
        }
    }


    /**
        * Obtiene el historial de compras de un usuario por su ID.
        *
        * Este método verifica que se haya recibido un `userId` válido. Si es válido, llama al modelo para obtener el historial de
        * compras asociado a ese usuario. Si no se encuentra ningún historial de compras, devuelve un mensaje de error.
        * En caso de éxito, devuelve los datos del historial de compras.
        *
        * @param array $data Un array asociativo con los siguientes elementos:
        *     - 'userId' (int): El ID del usuario para el cual se desea obtener el historial de compras.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentra el historial de compras del usuario y se devuelve correctamente.
        *     - HTTP 400: Si el `userId` no es válido o falta en los datos recibidos.
        *     - HTTP 404: Si no se encuentra el historial de compras para el usuario.
    */
    public function GetpurchaseHistoryById ($data) {
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
        $purchaseHistory = $this->purchaseHistoryModel->GetpurchaseHistoryById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($purchaseHistory)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Historial de compra no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $purchaseHistory
            ]);
        }
    }


    /**
        * Crea un nuevo historial de compra para un usuario.
        *
        * Este método verifica que se hayan recibido los datos necesarios para crear un historial de compra, incluyendo el ID del
        * usuario, el ID del producto, la cantidad y el total. Si los datos son válidos, se llama al modelo para guardar la información
        * y se devuelve un mensaje de éxito. Si los datos están incompletos o ocurre un error al crear el historial de compra, se devuelve
        * un mensaje de error con el código de estado correspondiente.
        *
        * @param array $data Un array asociativo con los siguientes elementos:
        *     - 'userId' (int): El ID del usuario que realizó la compra.
        *     - 'productId' (int): El ID del producto comprado.
        *     - 'quantity' (int): La cantidad de productos comprados.
        *     - 'total' (float): El total de la compra.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 201: Si el historial de compra se creó correctamente.
        *     - HTTP 400: Si faltan datos requeridos o son incorrectos.
        *     - HTTP 500: Si ocurre un error al crear el historial de compra.
    */
    public function createPurchaseHistory ($data) {
        // paso 2: Verificar los datos recibidos
        if (count(array_filter($data, 'is_numeric')) !== 4) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos del historial de compra faltantes, se requiere id de usuario, id de producto, cantidad y total.'
            ]);
            return;
        }

        // paso 3: Llamar al método necesario
        $newPurchaseHistoryId = $this->purchaseHistoryModel->createPurchaseHistory($this->connection, $data);

        // paso 4: Verificar los datos devueltos del método
        if (empty($newPurchaseHistoryId)) {
            // paso 5: Respuesta http 500 y mensaje
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear el historial de comrpa en este momento.'
            ]);
        } else {
            // paso 6: Respuesta http 201 e información del categoryId creado
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Historial de compra creado exitosamente.',
                'data' => $newPurchaseHistoryId
            ]);
        }
    }
}