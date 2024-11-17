<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './backend/src/config/dbConnection.php';

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

    public function deleteProductShoppingBag ($connection, $data) {
        if (empty($_SESSION['userId'])) {
            return [
                'status' => 'error',
                'message' => 'No tienes ninguna sessión abierta.',
                'messageToDeveloper' => 'No está el userId en la variable $_SESSION["userId"].',
            ];
        }

        if ($_SESSION['userId'] != $data['userId']) {
            return [
                'status' => 'error',
                'message' => 'Tu sesión abierta no coincide con tu id de usuario.',
                'messageToDeveloper' => 'El $_SESSION["userId"] no coincide con el id enviado en la url.',
            ];
        }

        $stmt = $connection->prepare('DELETE FROM bag_product WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $data['productId'], PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->rowCount() > 0) {
            return [
                'status' => 'error',
                'message' => 'No se pudo eliminar el producto, intenta de nuevo.',
                'messageToDeveloper' => 'Hubo un error al ejecutarse la consulta o no se afectó a ninguna fila.',
            ];
        } else {
            return [
                'status' => 'success',
            ];
        }
    }
}