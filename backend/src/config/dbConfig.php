<?php

/**
    * Configuración de conexión a la base de datos.
    *
    * Este archivo contiene las configuraciones necesarias para establecer la conexión con la base de datos MySQL.
    * Los parámetros de configuración incluyen el host, el nombre de la base de datos, las credenciales de usuario y el conjunto de caracteres.
    * Estos valores se utilizan para establecer la conexión en el método `connection` de la clase que maneja la conexión a la base de datos.
    *
    * @return array Devuelve un arreglo con la configuración de la base de datos.
    *               - 'host': Dirección del servidor de base de datos (localhost o dirección IP).
    *               - 'dbname': Nombre de la base de datos.
    *               - 'username': Nombre de usuario para la conexión.
    *               - 'password': Contraseña del usuario de la base de datos.
    *               - 'codificacion': Codificación utilizada en la conexión (utf8mb4 para compatibilidad con caracteres Unicode completos).
*/
return [
    'host' => 'localhost',  //127.0.0.1
    'dbname' => 'RichMond_db',
    'username' => 'root',
    'password' => '3105180466Df',
    'codificacion' => 'charset=utf8mb4',
    
];