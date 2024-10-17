<?php

require_once '../config/db_connection.php';

class BagProductModel {
    public function createBagProduct ($connection, $bagProductData) {
        $stmt = $connection->prepare('INSERT INTO bag_product (shopping_bag_id, product_id, quantity) VALUES (:shoppingBagId, :productId, :quantity);');
        $stmt->bindParam(':shoppingBagId', $bagProductData['shoppingBagId'], PDO::PARAM_INT);
        $stmt->bindParam(':productId', $bagProductData['productId'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $bagProductData['quantity'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }

    
}