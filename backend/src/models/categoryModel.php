<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase CategoryModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con las categorías.
*/
class CategoryModel {
    /**
        * Obtiene todas las categorías de productos.
        *
        * Este método recupera todas las categorías de la base de datos desde la tabla `categories` y devuelve el resultado como un array asociativo.
        * Cada categoría contiene información relevante como el nombre y el ID.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        *
        * @return array Un array asociativo con todos los registros de categorías.
    */
    public function getAllCategories($connection){
        $stmt = $connection->query('SELECT * FROM categories;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
        * Obtiene una categoría por su ID.
        *
        * Este método realiza una consulta a la base de datos para obtener los detalles
        * de una categoría específica utilizando su ID. Utiliza una consulta preparada
        * para evitar inyecciones SQL.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param int $categoryId ID de la categoría que se desea obtener.
        *
        * @return array|null Retorna un arreglo asociativo con los datos de la categoría
        * si se encuentra, o null si no existe.
    */
    public function getCategoryById ($connection, $categoryId) {
        $stmt = $connection->prepare('SELECT * FROM categories WHERE category_id = :categoryId;');
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $category = $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
        * Crea una nueva categoría en la base de datos.
        *
        * Este método inserta los datos de una nueva categoría en la tabla `categories`.
        * Utiliza una consulta preparada para evitar inyecciones SQL. Si la inserción
        * es exitosa, retorna el ID de la nueva categoría creada.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param array $categoryData Arreglo asociativo con los datos de la categoría a crear. Debe incluir:
        *     - 'categoryName': Nombre de la categoría (requerido, string).
        *     - 'categoryDescription': Descripción de la categoría (requerido, string).
        *     - 'status': Estado de la categoría (requerido, string).
        *
        * @return int|bool Retorna el ID de la categoría creada si la operación es exitosa,
        * o false si ocurre algún error durante la inserción.
    */
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


    /**
        * Actualiza los datos de una categoría en la base de datos.
        *
        * Este método construye dinámicamente una consulta SQL para actualizar los campos proporcionados
        * de una categoría específica. Solo se actualizan los campos presentes en `$categoryData`.
        * Utiliza consultas preparadas para evitar inyecciones SQL.
        *
        * @param PDO $connection Objeto de conexión a la base de datos.
        * @param int $categoryId ID de la categoría que se desea actualizar.
        * @param array $categoryData Arreglo asociativo con los datos a actualizar. Puede incluir:
        *     - 'categoryName': Nuevo nombre de la categoría (opcional, string).
        *     - 'categoryDescription': Nueva descripción de la categoría (opcional, string).
        *     - 'categoryStatus': Nuevo estado de la categoría (opcional, string).
        *
        * @return bool Retorna true si la actualización fue exitosa, o false si ocurrió un error.
    */
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

        $query = rtrim($query, ', '). ' WHERE category_id = :categoryId;';
        $params[':categoryId'] = $categoryData['categoryId'];

        $stmt = $connection->prepare($query);

        if ($stmt->execute($params)) {
            return true;
        } else {
            return false;
        }
    }
}