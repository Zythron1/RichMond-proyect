<?php

require_once './backend/src/config/dbConnection.php';

class PaymentMethodModel {
    public function getPaymentMethodById ($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM payment_methods WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}