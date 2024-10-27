<?php

require_once './backend/src/config/dbConnection.php';

class ShipmentModel {
    public function getAllShipments ($connection) {
        $stmt = $connection->query('SELECT * FROM shipments;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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