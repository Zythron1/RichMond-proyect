<?php

require_once './backend/src/config/dbConnection.php';

class ProductModel {
    public function getAllProducts ($connection) {
        $stmt = $connection->query('SELECT * FROM products;');
        return $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById ($connection, $productId) {
        $stmt = $connection->prepare('SELECT * FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public function deleteProduct ($connection, $productId) {
        $stmt = $connection->prepare('DELETE FROM products WHERE product_id = :productId;');
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getProductsByCategoryWithLimitAndOffset ($connection, $data) {
        if ($data['categoryId'] != 0) {

            $stmt = $connection->prepare('SELECT product_name, price, image_url FROM products WHERE category_id = :categoryId LIMIT :limit OFFSET :offset');
            $stmt->bindParam(':categoryId', $data['categoryId'], PDO::PARAM_INT);
            $stmt->bindParam(':limit', $data['limit'], PDO::PARAM_INT);
            $stmt->bindParam(':offset', $data['offset'], PDO::PARAM_INT);

        } else {

            $stmt = $connection->prepare("SELECT product_name, price, image_url FROM products LIMIT :limit OFFSET :offset");
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