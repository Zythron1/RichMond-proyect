<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase OrderModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con las órdenes.
*/
class OrderModel {
    /**
        * Obtiene todos los pedidos registrados en la base de datos.
        *
        * Este método consulta la tabla de pedidos y devuelve todos los registros disponibles.
        * La información recuperada incluye detalles de los pedidos de todos los usuarios.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        *
        * @return array Un array asociativo con los datos de todos los pedidos. Cada entrada del array 
        *               representa un pedido con sus respectivos detalles.
        *               Si no se encuentran pedidos, el array estará vacío.
    */
    public function getAllOrder($connection) {
        $stmt = $connection->query('SELECT * FROM orders;');
        $stmt->execute();
        return $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene los detalles de un pedido específico a partir de su ID.
        *
        * Este método consulta la tabla de pedidos para recuperar los detalles de un pedido 
        * específico basado en el ID proporcionado.
        * Si no se encuentra el pedido con el ID dado, se devolverá `null`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $orderId El ID del pedido que se desea obtener.
        *
        * @return array|null Un array asociativo con los detalles del pedido si se encuentra. 
        *                    Si no se encuentra el pedido, se devuelve `null`.
    */
    public function getOrderById ($connection, $orderId) {
        $stmt = $connection->prepare('SELECT * FROM orders WHERE order_id = :orderId;');
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
        * Crea un nuevo pedido en la base de datos.
        *
        * Este método inserta un nuevo registro en la tabla de pedidos con el ID del usuario 
        * y el total del pedido proporcionados. Si la inserción es exitosa, se retorna 
        * el ID del nuevo pedido.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $orderData Un array asociativo que contiene los datos del pedido:
        *                          - 'userId' (int): El ID del usuario que realiza el pedido.
        *                          - 'total' (float): El total del pedido.
        *
        * @return int|false El ID del pedido recién creado si la operación es exitosa, 
        *                   o `false` si ocurre un error durante la inserción.
    */
    public function createOrder ($connection, $orderData) {
        $stmt = $connection->prepare('INSERT INTO orders (user_id, total) VALUES (:userId, :total);');
        $stmt->bindParam(':userId', $orderData['userId'], PDO::PARAM_INT);
        $stmt->bindParam(':total', $orderData['total'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }


    /**
        * Actualiza los detalles de un pedido en la base de datos.
        *
        * Este método permite actualizar el total y/o el estado de un pedido existente.
        * El pedido se actualiza con los valores proporcionados en el array `$orderData`.
        * Si la actualización es exitosa, el método retorna `true`; si ocurre un error, retorna `false`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $orderId El ID del pedido que se desea actualizar.
        * @param array $orderData Un array asociativo que contiene los datos a actualizar del pedido:
        *                          - 'total' (float): El nuevo total del pedido (opcional).
        *                          - 'status' (string): El nuevo estado del pedido (opcional).
        *
        * @return bool `true` si la actualización es exitosa, o `false` si ocurre un error.
    */
    public function updateOrder ($connection, $orderId, $orderData) {
        $query = 'UPDATE orders SET ';
        $params = [];

        if (isset($orderData['total'])) {
            $query .= 'total = :total, ';
            $params[':total'] = $orderData['total'];
        }

        if (isset($orderData['status'])) {
            $query .= 'status = :status, ';
            $params[':status'] = $orderData['status'];
        }

        $query = rtrim($query, ', '). ' WHERE order_id = :orderId;';
        $params[':orderId'] = $orderData['orderId'];

        $stmt = $connection->prepare($query);

        if($stmt->execute($params)) {
            return true;
        } else {
            return false;
        }
    }


    /**
        * Elimina un pedido de la base de datos.
        *
        * Este método elimina un pedido específico de la tabla de pedidos, usando el ID del pedido proporcionado.
        * Si la eliminación es exitosa, el método retorna `true`; si ocurre un error, retorna `false`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $orderId El ID del pedido que se desea eliminar.
        *
        * @return bool `true` si la eliminación es exitosa, o `false` si ocurre un error.
    */
    public function deleteOrder ($connection, $orderId) {
        $stmt = $connection->prepare('DELETE FROM orders WHERE order_id = :orderId;');
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        if ($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }
}