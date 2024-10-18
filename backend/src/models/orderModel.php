<?php

require_once '../config/db_connection.php';

class OrderModel {
    public function getAllOrder($connection) {
        $stmt = $connection->query('SELECT * FROM orders;');
        $stmt->execute();
        return $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById ($connection, $orderId) {
        $stmt = $connection->prepare('SELECT * FROM orders WHERE order_id = :orderId;');
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }

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