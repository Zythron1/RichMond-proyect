<?php

require_once './backend/src/config/dbConnection.php';

class CategoryModel {
    public function getAllCategories ($connection) {
        $stmt = $connection->query('SELECT * FROM products;');
        return $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById ($connection, $categoryId) {
        $stmt = $connection->prepare('SELECT * FROM categories WHERE category_id = :categoryId;');
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $category = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory ($connection, $categoryData) {
        $stmt = $connection->prepare('INSERT INTO categories (category_name, category_description, category_status) VALUES (:categoryName, :categoryDescription, :status);');
        $stmt->bindParam(':categoryName', $categoryData['categoryName'], PDO::PARAM_STR);
        $stmt->bindParam(':categoryDescription', $categoryData['categoryDescription'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $categoryData['status'], PDO::PARAM_STR);
        if($stmt->execute()) {
            return $connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function updateCategory ($connection, $categoryId, $categoryData) {
        $query = $connection->prepare('UPDATE categories SET ');
        $params = [];

        if (isset($categoryData['categoryName'])) {
            $query .= 'category_name = :categoryName,';
            $params[':categoryName'] = $categoryData['categoryName'];
        }

        if (isset($categoryData['categoryDescription'])) {
            $query .= 'category_description = :categoryDescription,';
            $params[':categoryDescription'] = $categoryData['categoryDescription'];
        }

        if (isset($categoryData['categoryStatus'])) {
            $query .= 'category_status = :categoryStatus,';
            $params[':categoryStatus'] = $categoryData['categoryStatus'];
        }

        $query->rtrim($query, ', '). ' WHERE category_id = :categoryId;';
        $params[':categoryId'] = $categoryData['categoryId'];

        $stmt = $connection->prepare($query);

        if ($stmt->execute($params)) {
            return true;
        } else {
            return false;
        }
    }
}