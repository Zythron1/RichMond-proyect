<?php

class DatabaseConnection {
    private static $db = null;
    
    private static function connection() {
        if(!self::$db) {
            $config = require_once './backend/src/config/dbConfig.php';
            $dsn = 'mysql:host='. $config['host'] . ';dbname=' . $config['dbname']. ';'. $config['codificacion']; //data source name
            

            try {
                self::$db = new PDO($dsn, $config['username'], $config['password']);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8mb4'");
            } catch( PDOException $e) {
                echo 'Fallo de conexión con la base de datos RichMond_db, ERROR:'. $e->getMessage();
                die();
            }
        }
        return self::$db;
    }

    public static function getConnection() {
        return self::connection();
    }

    public static function conexion() {
        self::connection();
        try {   
            $stmt = self::$db->query('SELECT * FROM users;');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo 'Error al ejecutar la consulta'. $e->getMessage();
            return [];
        }
    }
}
