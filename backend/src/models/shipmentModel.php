<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase ShipmentModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con los envíos.
*/
class ShipmentModel {
    /**
        * Obtiene todos los envíos registrados en la base de datos.
        *
        * Este método ejecuta una consulta para obtener todos los registros de la tabla `shipments`
        * y retorna el resultado en forma de un arreglo asociativo.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * 
        * @return array Un arreglo asociativo con todos los registros de envíos. 
        *         Si no hay envíos, el arreglo estará vacío.
    */
    public function getAllShipments ($connection) {
        $stmt = $connection->query('SELECT * FROM shipments;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene el envío asociado a un pedido para un usuario específico.
        *
        * Este método realiza dos consultas: primero obtiene el `order_id` asociado al `user_id`,
        * y luego, con ese `order_id`, busca el registro del envío correspondiente en la tabla `shipments`.
        * Si no se encuentra un pedido para el usuario o si no existe un envío relacionado, el método retorna `null`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuya información de envío se desea obtener.
        * 
        * @return array|null Un arreglo asociativo con los detalles del envío si se encuentra, 
        *         o `null` si no hay un pedido o envío asociado al usuario.
    */
    public function getShipmentById ($connection, $userId) {
        $getOrderStmt = $connection->prepare('SELECT order_id FROM orders WHERE user_id = :userId');
        $getOrderStmt->bindParam('userId', $userId, PDO::PARAM_INT);
        $getOrderStmt->execute();
        $orderId = $getOrderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$orderId) {
            return null;
        } else {
            $stmt = $connection->prepare('SELECT * FROM shipments WHERE order_id = :orderId;');
            $stmt->bindParam(':orderId', $orderId['order_id'], PDO::PARAM_INT);
            $stmt->execute();
            return $shipment = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
    }
}