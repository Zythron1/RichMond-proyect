<?php

class DatabaseConnection {
    private static $db = null;


    /**
        * Establece y devuelve una conexión a la base de datos utilizando PDO.
        *
        * Este método es responsable de crear una conexión a la base de datos si no existe una conexión activa. Utiliza la configuración
        * definida en el archivo `dbConfig.php` para obtener los parámetros de conexión. Si la conexión es exitosa, se establece
        * la codificación y el modo de error. Si la conexión falla, se muestra un mensaje de error y el script se detiene.
        *
        * @return PDO La instancia de la conexión PDO a la base de datos.
    */
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


    /**
        * Devuelve la conexión a la base de datos.
        *
        * Este método es un simple envoltorio que llama al método `connection` para obtener la instancia de la conexión a la base de datos.
        * Se utiliza para acceder a la conexión a la base de datos en otros métodos o clases que necesiten interactuar con la base de datos.
        *
        * @return PDO La instancia de la conexión PDO a la base de datos.
    */
    public static function getConnection() {
        return self::connection();
    }
}
