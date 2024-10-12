<?php

require_once '../config/db_connection.php';

class ShoppingBagModel {

    public function getShoppingBagById ($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM shopping_bag WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $shoppingBagActive = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($shoppingBagActive) {
            return $shoppingBagActive;
        } else {
            return false;
        }
    }


    
    public function addProduct ($connection,  $userId, $productId, $quantity) {
        $stmt = $connection->prepare('SELECT stock FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock && $stock['stock'] >= $quantity) {
            $bagStmt = $connection->prepare('SELECT * FROM shopping_bag WHERE user_id = :userId AND product_id = :productId;');
            $bagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $bagStmt->bindParam(':productId',$productId, PDO::PARAM_INT);
            $bagStmt->execute();
            $bagProduct = $bagStmt->fetch(PDO::FETCH_ASSOC);

            if ($bagProduct) {
                $newQuantity = $bagProduct['quantity'] + $quantity;
                $updateStmt = $connection->prepare('UPDATE shopping_bag SET quantity = :newQuantity WHERE user_id = :userId AND product_id = :productId;');
                $updateStmt->bindParam(':newQuantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $updateStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                return $updateStmt->execute();
            } else {
                $insertStmt = $connection->prepare('INSERT INTO shopping_bag (user_id, product_id, quantity) VALUES (:userId, :productId, :newQuantity);');
                $insertStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $insertStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $insertStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                return $insertStmt->execute();
            }
        } else {
            return ['erorr' => 'NO hay suficiente stock para este producto'];
        }
    }

    
}
