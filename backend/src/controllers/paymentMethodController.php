<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para el funcionamiento del backend.
    * 
    * - `dbConnection.php`: Configuración de la conexión a la base de datos.
    * - `PaymentMethodModel.php`: Modelo para gestionar los métodos de pago.
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/PaymentMethodModel.php';


class PaymentMethodController {
    private $connection;
    private $paymentMethodModel;


    /**
        * Constructor de la clase.
        *
        * Este método establece la conexión a la base de datos y crea una instancia del modelo de métodos de pago.
        * Si ocurre un error durante la conexión a la base de datos, se lanza una excepción que detiene la ejecución 
        * y devuelve un mensaje de error con el código de estado HTTP 500.
        *
        * @return void No retorna ningún valor.
    */
    public function __construct () {
        try {
            $this->connection = DatabaseConnection::getConnection();
            $this->paymentMethodModel = new PaymentMethodModel;
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
        * Obtiene el método de pago asociado a un usuario mediante su ID.
        *
        * Este método recibe el ID de un usuario para obtener el método de pago asociado a dicho usuario.
        * Se valida que el ID de usuario sea un valor numérico y esté presente. Si los datos son incorrectos o faltan,
        * se devuelve un mensaje de error con código de estado HTTP 400. Si no se encuentra el método de pago para el usuario,
        * se devuelve un mensaje con código de estado HTTP 404. Si el método de pago es encontrado, se devuelve una respuesta
        * con código de estado HTTP 200 junto con los datos del método de pago.
        *
        * @param array $data Array asociativo que contiene:
        *     - userId (int): ID del usuario cuyo método de pago se desea obtener.
        *
        * @return void No retorna un valor directo, pero responde con un código de estado HTTP y un mensaje:
        *     - HTTP 200: Si se encuentra el método de pago del usuario.
        *     - HTTP 400: Si el ID de usuario es inválido o faltante.
        *     - HTTP 404: Si no se encuentra el método de pago para el usuario.
    */
    public function getPaymentMethodById ($data) {
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
        $paymentMethods = $this->paymentMethodModel->getPaymentMethodById($this->connection, $userId);

        // paso 3: Verificar datos devueltos del método
        if (empty($paymentMethods)) {
            // paso 4: Respuesta http 404 y mensaje
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Método de pago no encontrado.'
            ]);
        } else {
            // paso 5: Respuesta http 200 y datos
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $paymentMethods
            ]);
        }
    }
}