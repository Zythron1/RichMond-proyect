<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase PurchaseHistoryModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionada con el historial de compra.
*/
class PurchaseHistoryModel {
    /**
        * Obtiene todo el historial de compras de la base de datos.
        *
        * Este método consulta la tabla `purchase_history` para obtener todos los registros de compras.
        * Retorna un arreglo de todos los registros de compras almacenados en la base de datos.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * 
        * @return array Un arreglo asociativo con todos los registros de compra encontrados.
    */
    public function getAllPurchaseHistory($connection) {
        $stmt = $connection->query('SELECT * FROM purchase_history;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene el historial de compras de un usuario específico.
        *
        * Este método consulta la tabla `purchase_history` para obtener todos los registros de compras 
        * asociados a un usuario en particular, identificado por su `userId`.
        * Retorna un arreglo con los registros de compra del usuario solicitado.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario para el cual se desea obtener el historial de compras.
        * 
        * @return array Un arreglo asociativo con los registros de compra del usuario especificado.
    */
    public function GetpurchaseHistoryById($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM purchase_history WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Crea un registro en el historial de compras de un usuario.
        *
        * Este método inserta un nuevo registro en la tabla `purchase_history` que incluye información sobre 
        * un producto comprado por un usuario, incluyendo el ID del usuario, el ID del producto, la cantidad 
        * adquirida y el total de la compra. Retorna el ID del último registro insertado.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $purchaseData Datos de la compra, que incluye:
        *      - `userId`: El ID del usuario que realizó la compra.
        *      - `productId`: El ID del producto comprado.
        *      - `quantity`: La cantidad de unidades compradas.
        *      - `total`: El total de la compra (valor total).
        *
        * @return int|bool El ID del nuevo registro en `purchase_history` si la operación fue exitosa, 
        *                  o `false` si ocurrió un error durante la inserción.
    */
    public function createPurchaseHistory ($connection, $purchaseData) {
        $stmt = $connection->prepare('INSERT INTO purchase_history (user_id, product_id, quantity, total) VALUES (:userId, :productId, :quantity, :total);');
        $stmt->bindParam(':userId', $purchaseData['userId'],PDO::PARAM_INT);
        $stmt->bindParam(':productId', $purchaseData['productId'],PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $purchaseData['quantity'],PDO::PARAM_INT);
        $stmt->bindParam(':total', $purchaseData['total'],PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }
}