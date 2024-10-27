<?php

require_once './backend/src/config/dbConnection.php';

class PurchaseHistoryModel {
    public function getAllPurchaseHistory($connection) {
        $stmt = $connection->query('SELECT * FROM purchase_history;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetpurchaseHistoryById($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM purchase_history WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPurchaseHistory ($connection, $purchaseData) {
        $stmt = $connection->prepare('INSERT INTO purchase_history (user_id, product_id, quantity, total) VALUES (:userId, :productId, :quantity, :total);');
        $stmt->bindPara(':userId', $purchaseData['userId'],PDO::PARAM_INT);
        $stmt->bindPara(':productId', $purchaseData['productId'],PDO::PARAM_INT);
        $stmt->bindPara(':quantity', $purchaseData['quantity'],PDO::PARAM_INT);
        $stmt->bindPara(':total', $purchaseData['total'],PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }
}