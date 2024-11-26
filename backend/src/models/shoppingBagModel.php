<?php

// Verificación y inicio de sesión si no está iniciada
/**
    * Se verifica si la sesión no está iniciada. Si la sesión no está activa, se inicia.
    * 
    * Esto asegura que la sesión esté disponible para ser utilizada durante el flujo de ejecución del script.
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/**
    * Carga de archivos necesarios.
    * 
    * Se incluyen los archivos PHP necesarios para interactuar con la base de datos y los modelos relacionados con las compras y el historial de órdenes.
    * 
    * - `dbConnection.php`: Establece la conexión a la base de datos.
    * - `OrderModel.php`: Modelo que maneja las operaciones relacionadas con las órdenes de los usuarios.
    * - `PurchaseHistoryModel.php`: Modelo que gestiona el historial de compras del usuario.
    * - `BagProductModel.php`: Modelo para gestionar los productos en la bolsa de compras (carrito de compras).
*/
require_once './backend/src/config/dbConnection.php';
require_once './backend/src/models/OrderModel.php';
require_once './backend/src/models/PurchaseHistoryModel.php';
require_once './backend/src/models/BagProductModel.php';


/**
    * Clase ShoppingBagModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionada con la bolsa de compra.
*/
class ShoppingBagModel {
    /**
        * Obtiene el ID de la bolsa de compras de un usuario.
        *
        * Este método busca la bolsa de compras asociada a un usuario en la base de datos utilizando el ID del usuario.
        * Si se encuentra una bolsa de compras, se devuelve su ID.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuya bolsa de compras se desea obtener.
        * 
        * @return array|null Un arreglo con el ID de la bolsa de compras si se encuentra, o null si no existe.
    */
    public function getShoppingBagId ($connection, $userId) {
        $stmt = $connection->prepare('SELECT shopping_bag_id FROM shopping_bag WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene los productos dentro de una bolsa de compras.
        *
        * Este método realiza una consulta para obtener los detalles de los productos que están dentro de una bolsa de compras específica.
        * Devuelve una lista de productos que incluye información sobre el nombre, precio, imagen y la cantidad en la que están presentes en la bolsa de compras.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $shoppingBagId El ID de la bolsa de compras cuyos productos se desean obtener.
        * 
        * @return array Un arreglo con los detalles de los productos en la bolsa de compras.
    */
    public function getShoppingBagProducts ($connection, $shoppingBagId) {
        $stmt = $connection->prepare('
        SELECT 
            products.product_id,
            products.product_name,
            products.price,
            products.image_url,
            bag_product.quantity
        FROM 
            bag_product
        INNER JOIN 
            products ON bag_product.product_id = products.product_id
        WHERE 
            bag_product.shopping_bag_id = :shoppingBagId
        ');
        $stmt->bindParam(':shoppingBagId', $shoppingBagId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene la bolsa de compras activa de un usuario.
        *
        * Este método realiza una consulta para obtener los detalles de la bolsa de compras de un usuario específico,
        * identificada por el `userId`. Si el usuario tiene una bolsa de compras activa, se retorna un arreglo con los
        * detalles de dicha bolsa. Si no tiene una bolsa activa, se retorna `false`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuya bolsa de compras se desea obtener.
        * 
        * @return array|false Un arreglo con los detalles de la bolsa de compras activa si existe, o `false` si no existe.
    */
    public function getShoppingBagById ($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM shopping_bag WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $activeShoppingBag = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($activeShoppingBag) {
            return $activeShoppingBag;
        } else {
            return false;
        }
    }


    /**
        * Crea una nueva bolsa de compras para un usuario.
        *
        * Este método inserta una nueva bolsa de compras en la base de datos para el usuario especificado.
        * La bolsa de compras se crea utilizando el `userId` proporcionado en los datos de la solicitud.
        * Si la inserción es exitosa, retorna `true`, de lo contrario, retorna `false`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $shoppingBagData Un arreglo que contiene los datos necesarios para crear la bolsa de compras,
        *        con la clave `userId` que representa el ID del usuario.
        * 
        * @return bool `true` si la bolsa de compras se crea exitosamente, `false` si ocurre un error en la inserción.
    */
    public function createShoppingBag ($connection, $shoppingBagData) {
        $stmt = $connection->prepare('INSERT INTO shopping_bag (user_id) VALUES (:userId);');
        $stmt->bindParam(':userId', $shoppingBagData['userId'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /**
        * Añade un producto a la bolsa de compras del usuario.
        *
        * Este método realiza varias comprobaciones antes de agregar un producto a la bolsa de compras de un usuario:
        * 1. Verifica si el `userId` de la sesión coincide con el `userId` proporcionado.
        * 2. Verifica si el producto tiene stock disponible.
        * 3. Si el usuario no tiene una bolsa de compras, crea una nueva.
        * 4. Verifica si el producto ya está en la bolsa de compras. Si es así, actualiza la cantidad del producto.
        * 5. Si no, inserta el producto en la bolsa de compras.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario que está realizando la acción.
        * @param int $productId El ID del producto que se desea añadir a la bolsa de compras.
        * 
        * @return array Un arreglo asociativo con el estado de la operación:
        *         - `status`: Puede ser `'success'` o `'error'`.
        *         - `message`: Mensaje que describe el resultado de la operación.
        *         - `messageToDeveloper`: Mensaje adicional para el desarrollador con información detallada.
        *         - `products`: Lista de los productos actuales en la bolsa de compras si la operación fue exitosa.
    */
    public function addProduct ($connection, $userId, $productId) {
        if ($_SESSION['userId'] !== $userId) {
            return [
                'status' => 'error',
                'message' => 'User ID inválido.',
                'messageToDeveloper' => 'El ID del usuario es diferente del de la sesión iniciada.'
            ];
        }

        // Paso 1: Verificar el stock
        $stmt = $connection->prepare('SELECT stock FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock && $stock['stock'] >= 1) {
            // Paso 2: Verificar si el usuario ya tiene una bolsa de compra
            $bagStmt = $connection->prepare('SELECT shopping_bag_id FROM shopping_bag WHERE user_id = :userId;');
            $bagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $bagStmt->execute();
            $shoppingBag = $bagStmt->fetch(PDO::FETCH_ASSOC);

            if (!$shoppingBag) {
                // Si la bolsa no existe, crear una nueva
                $createBagStmt = $connection->prepare('INSERT INTO shopping_bag (user_id) VALUES (:userId);');
                $createBagStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                if (!$createBagStmt->execute()) {
                    return [
                        'status' => 'error',
                        'message' => 'No se pudo crear la bolsa de compra.',
                        'messageToDeveloper' => 'Error al crear la bolsa en la base de datos.'
                    ];
                }
                // Obtener el ID de la nueva bolsa
                $shoppingBagId = $connection->lastInsertId();
            } else {
                $shoppingBagId = $shoppingBag['shopping_bag_id'];
            }

            // Paso 3: Verificar si el producto ya está en la bolsa
            $productStmt = $connection->prepare('SELECT quantity FROM bag_product WHERE shopping_bag_id = :shoppingBagId AND product_id = :productId;');
            $productStmt->bindParam(':shoppingBagId', $shoppingBagId, PDO::PARAM_INT);
            $productStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $productStmt->execute();
            $bagProduct = $productStmt->fetch(PDO::FETCH_ASSOC);

            if ($bagProduct) {
                // Si ya existe, actualizar la cantidad
                $newQuantity = $bagProduct['quantity'] + 1;
                if ($newQuantity > $stock['stock']) {
                    return [
                        'status' => 'error',
                        'message' => 'No hay suficiente stock para añadir más de este producto.',
                        'messageToDeveloper' => 'Stock insuficiente al actualizar la cantidad en bag_product.'
                    ];
                }

                $updateStmt = $connection->prepare('UPDATE bag_product SET quantity = :quantity WHERE shopping_bag_id = :shoppingBagId AND product_id = :productId;');
                $updateStmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindParam(':shoppingBagId', $shoppingBagId, PDO::PARAM_INT);
                $updateStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $updateStmt->execute();
            } else {
                // Si no existe, insertar el producto en la bolsa
                $insertStmt = $connection->prepare('INSERT INTO bag_product (shopping_bag_id, product_id, quantity) VALUES (:shoppingBagId, :productId, 1);');
                $insertStmt->bindParam(':shoppingBagId', $shoppingBagId, PDO::PARAM_INT);
                $insertStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $insertStmt->execute();
            }

            $products = $this->getShoppingBagProducts(DatabaseConnection::getConnection(), $shoppingBagId);

            return [
                'status' => 'success',
                'message' => 'Producto añadido a la bolsa de compra.',
                'messageToDeveloper' => 'Operación realizada con éxito.',
                'products' => $products
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'No hay suficiente stock para este producto.',
                'messageToDeveloper' => 'El producto no tiene stock suficiente.'
            ];
        }
    }


    /**
        * Realiza el proceso de compra del usuario, verificando el stock, creando una orden y actualizando el historial de compras.
        *
        * Este método realiza las siguientes acciones:
        * 1. Verifica si el carrito de compras tiene productos.
        * 2. Verifica el stock de cada producto y calcula el total de la compra.
        * 3. Crea una nueva orden con el total calculado.
        * 4. Guarda el historial de compras, registrando cada producto comprado.
        * 5. Reduce el stock de los productos comprados.
        * 6. Elimina los productos del carrito de compras del usuario.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario que está realizando la compra.
        * 
        * @return array Un arreglo asociativo con el estado de la operación:
        *         - `status`: Puede ser `'success'` o `'error'`.
        *         - `message`: Mensaje que describe el resultado de la operación.
        *         - `orderId`: ID de la orden creada, si la operación fue exitosa.
    */
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
                return ['status' => 'error', 'message' => 'El carrito está vacío'];
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
                    return ['status' => 'error', 'message' => 'No hay suficiente stock para el producto '. $item['product_id']];
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
                return ['status' => 'success', 'message' => 'Compra realizada con éxito, orden '. $orderId, 'orderId' => $orderId];
                
        } catch (Exception $e) {
            $connection->rollBack();
            return ['status' => 'error', 'message' => 'Error al realizar la compra'. $e->getMessage()];
        }
    }


    /**
        * Elimina un producto del carrito de compras del usuario.
        *
        * Este método realiza las siguientes acciones:
        * 1. Verifica si el producto existe en la bolsa de compra del usuario.
        * 2. Si el producto está en el carrito, lo elimina de la tabla `bag_product` y de la tabla `shopping_bag`.
        * 3. Si la eliminación es exitosa, confirma la transacción.
        * 4. En caso de error, realiza un `rollback` para deshacer los cambios.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuya bolsa de compras se está modificando.
        * @param int $productId El ID del producto a eliminar del carrito.
        * 
        * @return array Un arreglo asociativo con el estado de la operación:
        *         - `status`: Puede ser `'success'` o `'error'`.
        *         - `message`: Mensaje que describe el resultado de la operación, incluyendo detalles de error si los hubiera.
    */
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
                return ['status' => 'success', 'message' => 'Producto eliminado del carrito de compra.'];
            } else {
                return ['status' => 'error', 'message' => 'El producto no existe en el carrito de compra.'];
            }
        } catch (Exception $e) {
            // En caso de error hacer un rollBack para deshacer los cambios.
            $connection->rollBack();
            return ['status' => 'error', 'message' => 'Error al eliminar el producto: ' . $e->getMessage()];
        }
    }
}
