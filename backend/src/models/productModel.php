<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase ProductModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con los productos.
*/
class ProductModel {
    /**
        * Obtiene todos los productos de la base de datos.
        *
        * Este método consulta la tabla `products` para obtener todos los productos disponibles. 
        * Retorna un array asociativo con los detalles de cada producto, que incluye 
        * información como el ID, nombre, precio, stock y otros atributos del producto.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        *
        * @return array|bool Un array asociativo con todos los productos si la consulta fue exitosa, 
        *                    o `false` si ocurrió un error durante la ejecución de la consulta.
    */
    public function getAllProducts ($connection) {
        $stmt = $connection->query('SELECT * FROM products;');
        return $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene los detalles de un producto por su ID.
        *
        * Este método consulta la tabla `products` utilizando el `product_id` proporcionado 
        * para obtener detalles específicos del producto, como el nombre, descripción, stock, precio 
        * e imagen. Si el producto existe, se retorna un array asociativo con esta información.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $productId El ID del producto que se desea obtener.
        *
        * @return array|bool Un array asociativo con los detalles del producto si se encuentra, 
        *                    o `false` si no se encuentra el producto con el ID proporcionado.
    */
    public function getProductById ($connection, $productId) {
        $stmt = $connection->prepare('SELECT product_name, product_description, stock, price, image_url FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene una parte de los detalles de un producto por su ID.
        *
        * Este método consulta la tabla `products` utilizando el `product_id` proporcionado 
        * para obtener información parcial del producto, como su descripción y stock disponible. 
        * Si el producto existe, se retorna un array asociativo con esta información.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $productId El ID del producto que se desea obtener.
        *
        * @return array|bool Un array asociativo con la descripción y el stock del producto si se encuentra, 
        *                    o `false` si no se encuentra el producto con el ID proporcionado.
    */
    public function getPartialProductById ($connection, $productId) {
        $stmt = $connection->prepare('SELECT product_description, stock FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
        * Crea un nuevo producto en la base de datos.
        *
        * Este método inserta un nuevo producto en la tabla `products` utilizando los datos proporcionados. 
        * Si la inserción es exitosa, se retorna el `product_id` del nuevo producto.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $producData Un array asociativo que contiene los datos del producto a insertar:
        *                           - `productName` (string): El nombre del producto.
        *                           - `productDescription` (string): La descripción del producto.
        *                           - `stock` (int): La cantidad disponible en inventario.
        *                           - `price` (int): El precio del producto.
        *                           - `imageUrl` (string): La URL de la imagen del producto.
        *                           - `categoryId` (int): El ID de la categoría a la que pertenece el producto.
        *
        * @return int|bool El `product_id` del nuevo producto si la inserción es exitosa, 
        *                  o `false` si ocurre un error durante la inserción.
    */
    public function createProduct ($connection, $producData) {
        $stmt = $connection->prepare('INSERT INTO products (product_name, product_description, stock, price, image_url, category_id) VALUES (:productName, :productDescription, :stock, :price, :imageUrl, :categoryId);');
        $stmt->bindparam(':productName', $producData['productName'], PDO::PARAM_STR);
        $stmt->bindparam(':productDescription', $producData['productDescription'], PDO::PARAM_STR);
        $stmt->bindparam(':stock', $producData['stock'], PDO::PARAM_INT);
        $stmt->bindparam(':price', $producData['price'], PDO::PARAM_INT);
        $stmt->bindparam(':imageUrl', $producData['imageUrl'], PDO::PARAM_STR);
        $stmt->bindparam(':category_id', $producData['categoryId'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }


    /**
        * Actualiza los datos de un producto en la base de datos.
        *
        * Este método actualiza la información de un producto específico en la tabla `products`, basándose
        * en los datos proporcionados. Solo los campos que estén presentes en el array `$productData` 
        * serán actualizados.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $productId El ID del producto a actualizar.
        * @param array $productData Un array asociativo que contiene los datos del producto a actualizar:
        *                           - `productName` (string, opcional): El nuevo nombre del producto.
        *                           - `productDescription` (string, opcional): La nueva descripción del producto.
        *                           - `stock` (int, opcional): La nueva cantidad disponible en inventario.
        *                           - `price` (int, opcional): El nuevo precio del producto.
        *                           - `imageUrl` (string, opcional): La nueva URL de la imagen del producto.
        *
        * @return bool `true` si la actualización fue exitosa, o `false` si ocurrió un error.
    */
    public function updateProduct ($connection, $productId, $productData) {
        $query = 'UPDATE products SET ';
        $params = [];

        if (isset($producData['productName'])) {
            $query .= 'product_name = :productName, ';
            $params[':productName'] = $producData['productName'];
        }

        if (isset($producData['productDescription'])) {
            $query .= 'product_description = :productDescription,';
            $params[':productDescription'] = $producData['productDescription'];
        }

        if (isset($producData['stock'])) {
            $query .= 'stock = :stock, ';
            $params[':stock'] = $producData['stock'];
        }

        if (isset($producData['price'])) {
            $query .= 'price = :price, ';
            $params[':price'] = $producData['price'];
        }

        if (isset($producData['imageUrl'])) {
            $query .= 'image_url = :imageUrl, ';
            $params[':imageUrl'] = $producData['imageUrl'];
        }

        $query = rtrim($query, ', '). ' WHERE product_id = :productId;';
        $params[':productId'] = $productId;
        
        $stmt = $connection->prepare($query);

        if ($stmt->execute($params)) {
            return true;
        } else {
            return false;
        }
    }


    /**
        * Elimina un producto de la base de datos.
        *
        * Este método elimina un producto de la tabla `products` basándose en el `productId` proporcionado.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $productId El ID del producto que se desea eliminar.
        *
        * @return bool `true` si el producto fue eliminado exitosamente, o `false` si ocurrió un error.
    */
    public function deleteProduct ($connection, $productId) {
        $stmt = $connection->prepare('DELETE FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /**
        * Obtiene productos de una categoría con límite y desplazamiento.
        *
        * Este método recupera los productos de una categoría específica o de todas las categorías si el `categoryId` es 0.
        * Los resultados se limitan a una cantidad específica (`limit`) y se desplazan según el valor de `offset`.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param array $data Array asociativo con los siguientes parámetros:
        *   - `categoryId`: El ID de la categoría de los productos (0 para obtener productos de todas las categorías).
        *   - `limit`: La cantidad máxima de productos a devolver.
        *   - `offset`: El número de productos a omitir antes de comenzar a devolver resultados.
        *
        * @return array Un array con la clave `status` que indica el estado de la operación, 
        *               un mensaje general `message`, un mensaje para desarrolladores `messageToDeveloper`, 
        *               y una lista de productos bajo la clave `products`. 
        *               Si no hay productos disponibles, `status` será `error`, de lo contrario será `success`.
    */
    public function getProductsByCategoryWithLimitAndOffset ($connection, $data) {
        if ($data['categoryId'] != 0) {

            $stmt = $connection->prepare('SELECT product_name, price, image_url, product_id FROM products WHERE category_id = :categoryId LIMIT :limit OFFSET :offset');
            $stmt->bindParam(':categoryId', $data['categoryId'], PDO::PARAM_INT);
            $stmt->bindParam(':limit', $data['limit'], PDO::PARAM_INT);
            $stmt->bindParam(':offset', $data['offset'], PDO::PARAM_INT);

        } else {

            $stmt = $connection->prepare("SELECT product_name, price, image_url, product_id FROM products LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $data['limit'], PDO::PARAM_INT);
            $stmt->bindParam(':offset', $data['offset'], PDO::PARAM_INT);

        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            return [
                'status' => 'error',
                'message' => 'No hay productos disponibles en este momento.',
                'messageToDeveloper' => 'No hay productos o hubo un error en la consulta.',
                'products' => $products
            ];
        } else {
            return [
                'status' => 'success',
                'message' => 'Cargando productos.',
                'messageToDeveloper' => 'Ningún error.',
                'products' => $products
            ];
        }
    }
}