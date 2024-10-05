<?php

class DatabaseConnection {
    private static $db = null;
    
    private static function connect() {
        if(!self::$db) {
            $config = require('db_config.php');
            $dsn = 'mysql:host='. $config['host'] . ';dbname=' . $config['dbname']; //data source name
            

            try {
                self::$db = new PDO($dsn, $config['username'], $config['password']);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch( PDOException $e) {
                echo 'Fallo de conexión con la base de datos RichMond_db, ERROR:'. $e->getMessage();
                die();
            }
        }
        return self::$db;
    }

    
    public static function conexion() {
        self::connect();
        try {
            $stmt = self::$db->query('SELECT * FROM users;');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo 'Error al ejecutar la consulta'. $e->getMessage();
            return [];
        }
    }
    
}
$conxion = DatabaseConnection::conexion();
print_r($conxion);