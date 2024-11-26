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
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase BagProductModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con la tabla que maneja la relación entre la bolsa de compra y producto.
*/
class BagProductModel {
    /**
        * Crea una relación entre un producto y una bolsa de compras en la base de datos.
        *
        * Este método inserta un registro en la tabla `bag_product`, que asocia un producto
        * con una bolsa de compras, incluyendo la cantidad deseada. Utiliza una consulta
        * preparada para evitar inyecciones SQL.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param array $bagProductData Arreglo asociativo con los datos de la relación a crear. Debe incluir:
        *     - 'shoppingBagId': ID de la bolsa de compras (requerido, entero).
        *     - 'productId': ID del producto (requerido, entero).
        *     - 'quantity': Cantidad del producto en la bolsa (requerido, entero).
        *
        * @return int|bool Retorna el ID del registro creado si la operación es exitosa,
        * o false si ocurre algún error durante la inserción.
    */
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


    /**
        * Elimina un producto de la bolsa de compras.
        *
        * Este método elimina un producto específico de la tabla `bag_product`, 
        * después de verificar que el usuario tenga una sesión activa y que 
        * el ID de usuario coincida con el de la sesión. 
        * Utiliza una consulta preparada para evitar inyecciones SQL.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param array $data Arreglo asociativo con los datos necesarios para realizar la operación. Debe incluir:
        *     - 'userId': ID del usuario que realiza la solicitud (requerido, entero).
        *     - 'productId': ID del producto a eliminar de la bolsa de compras (requerido, entero).
        *
        * @return array Retorna un arreglo con el estado de la operación:
        *     - Si no hay sesión activa o los IDs no coinciden: 
        *         - 'status' => 'error'.
        *         - 'message': Mensaje para el usuario.
        *         - 'messageToDeveloper': Mensaje técnico para los desarrolladores.
        *     - Si la eliminación falla: 
        *         - 'status' => 'error'.
        *         - 'message': Mensaje indicando el fallo.
        *         - 'messageToDeveloper': Explicación técnica del error.
        *     - Si la eliminación es exitosa: 
        *         - 'status' => 'success'.
    */
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