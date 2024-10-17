<?php

require_once '../config/db_connection.php';
require_once '../models/order.php';
require_once '../models/purchase_history.php';
require_once '../models/bag_product.php';

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

    public function createShoppingBag ($connection, $shoppingBagData) {
        $stmt = $connection->prepare('INSERT INTO shopping_bag (user_id, product_id, quantity) VALUES (:userId, :productId, :quantity);');
        $stmt->bindParam(':userId', $shoppingBagData['userId'], PDO::PARAM_INT);
        $stmt->bindParam(':productId', $shoppingBagData['productId'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $shoppingBagData['quantity'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $shoppingBagId = $connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function addProduct($connection, $userId, $productId, $quantity) {
    // Paso 1: Verificar el stock
    $stmt = $connection->prepare('SELECT stock FROM products WHERE product_id = :productId;');
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stock && $stock['stock'] >= $quantity) {
        // Paso 2: Verificar si el usuario ya tiene una bolsa de compra
        $bagStmt = $connection->prepare('SELECT shopping_bag_id FROM shopping_bag WHERE user_id = :userId AND product_id = :productId;');
        $bagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $bagStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $bagStmt->execute();
        $shoppingBag = $bagStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($shoppingBag) {
            // Si la bolsa existe se actualiza el stock de la bolsa de compray el bag_product
            $updateBagStmt = $connection->prepare('UPDATE shopping_bag SET quantity = quantity + :quantity WHERE user_id = :userId AND product_id = :productId;');
            $updateBagStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $updateBagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $updateBagStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $updateBagStmt->execute();

            $updateBagProductStmt = $connection->prepare('UPDATE bag_product SET quantity = quantity + :quantity WHERE shopping_bag_id = :shoppingBagId AND product_id = :productId;');
            $updateBagProductStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $updateBagProductStmt->bindParam(':shoppingBagId', $shoppingBag['shopping_bag_id'], PDO::PARAM_INT);
            $updateBagProductStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $updateBagProductStmt->execute();

            return ['success' => true, 'message' => 'Producto actualizado.'];
        } else {
            // Si la bolsa no existe crear una bolsa de compra y crear el bag_product
            $shoppingBagData = [
                'userId' => $userId,
                'productId' => $productId,
                'quantity' => $quantity
            ];
            $shoppingBagId = $this->createShoppingBag(DatabaseConnection::getConnection(), $shoppingBagData);

            $bagProduct = new BagProductModel();
            $bagProductData = [
                'shoppingBagId' => $shoppingBagId,
                'productId' => $productId,
                'quantity' => $quantity
            ];
            $bagProductId = $bagProduct->createBagProduct(DatabaseConnection::getConnection(), $bagProductData);

            return ['success' => true, 'message' => 'Producto añadido a la bolsa de compra.'];
        }
    } else {
        return ['error' => 'No hay suficiente stock para este producto.'];
    }
}

    // finalizar la compra de una bolsa de compra. Lógica
    public function checkOuts ($connection, $userId) {
        try {
            // paso 1: Verificar si el carrito tiene productos
            $connection->beginTransaction();
            $bagStmt = $connection->prepare('SELECT * FROM bag_product WHERE shopping_bag_id = (SELECT shopping_bag_id FROM shopping_bag WHERE user_id = :userId;');
            $bagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $bagStmt->execute();
            $bagProduct = $bagStmt->fetchAll(PDO::FETCH_ASSOC);

            // paso 2: verificar el stock y calcular el total de la compra
            if (empty($bagProduct)) {
                return ['error' => 'El carrito está vacío'];
            }

            $productData = [];
            $total = 0;
            foreach ($bagProduct as $item) {
                $prodcutStmt = $connection->prepare('SELECT stock, price from products WHERE product_id = :productId;');
                $prodcutStmt->bindParam(':productId', $item['product_id'], PDO::PARAM_INT);
                $prodcutStmt->execute();
                $product = $prodcutStmt->fetch(PDO::FETCH_ASSOC);

                if ($product['stock'] >= $item['quantity']) {
                    $productData[$item['product_id']] = [
                        'price' => $product['price'],
                        'stock' => $product['stock']
                    ];

                    $total += $product['price'] * $item['quantity'];

                } else {
                    return ['error' => 'No hay suficiente stock para el producto'. $item['product_id']];
                }
            }

            // paso 3: crear una order utilizando el método ya hecho.
            $order = new OrderModel;
            $orderId = $order->createOrder(DatabaseConnection::getConnection(), ['userId' => $userId, 'total' => $total]);

            // paso 4: guardar el historial 
            // userId, productId, quantity, total
            $purchaseHistory = new PurchaseHistoryModel;
            foreach ($bagProduct as $item){
                $totalItem = $item['quantity'] * $productData[$item['product_id']]['price'];
                $purchaseData = [
                    'userId' => $userId,
                    'productId' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'total' => $totalItem
                ];
                $purchaseHistory->createPurchaseHistory(DatabaseConnection::getConnection(), $purchaseData);

                // paso 5: se reduce el stock
                $stockUpdateStmt = $connection->prepare('UPDATE products SET stock = stock - :quantity WHERE product_id = :productId;');
                $stockUpdateStmt->bindParam(':quantity', $item['quantity']);
                $stockUpdateStmt->bindParam(':productId', $item['product_id'], PDO::PARAM_INT);
                $stockUpdateStmt->execute();
            }
            // paso 6: Se elimina los productos del carrito.
                $deleteBagStmt = $connection->prepare('DELETE FROM shopping_bag WHERE userId = :userId;');
                $deleteBagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $deleteBagStmt->execute();

                $connection->commit();
                return ['succes' => 'Compra realizada con éxito', 'orderId' => $orderId];
                
        } catch (Exception $e) {
            $connection->rollBack();
            return ['Error' => 'Error al realizar la compra'. $e->getMessage()];
        }
    }

    public function deleteProduct($connection, $userId, $productId) {
        try {
            // Iniciar una transacción para que todo se cumpla o nada.
            $connection->beginTransaction();

            // Paso 1: Se verifica que el producto exista en la bolsa de compra
            $checkStmt = $connection->prepare('SELECT shopping_bag_id FROM shopping_bag WHERE user_id = :userId AND product_id = :productId;');
            $checkStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $checkStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $checkStmt->execute();
            $shoppingBag = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($shoppingBag) {
                // Paso 2: Se elimina el producto de la bolsa bag product
                $deleteBagProductStmt = $connection->prepare('DELETE FROM bag_product WHERE shopping_bag_id = :shoppingBagId AND product_id = :productId;');
                $deleteBagProductStmt->bindParam(':shoppingBagId', $shoppingBag['shopping_bag_id'], PDO::PARAM_INT);
                $deleteBagProductStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $deleteBagProductStmt->execute();

                // Paso 3: Se elimina el producto de la bolsa de compra
                $deleteBagStmt = $connection->prepare('DELETE FROM shopping_bag WHERE user_id = :userId AND product_id = :productId;');
                $deleteBagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $deleteBagStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $deleteBagStmt->execute();

                // Si se elimna se confirma la transacción 
                $connection->commit();
                return ['success' => true, 'message' => 'Producto eliminado del carrito de compra.'];
            } else {
                return ['error' => 'El producto no existe en el carrito de compra.'];
            }
        } catch (Exception $e) {
            // En caso de error hacer un rollBack para deshacer los cambios.
            $connection->rollBack();
            return ['error' => 'Ocurrió un error al eliminar el producto: ' . $e->getMessage()];
        }
    }
}
