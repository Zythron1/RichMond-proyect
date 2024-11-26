<?php

/**
    * Carga de archivos necesarios.
    * 
    * Se incluye el archivo `dbConnection.php`, que contiene la configuración necesaria para establecer 
    * la conexión a la base de datos.
*/
require_once './backend/src/config/dbConnection.php';


/**
    * Clase PaymentMethodModel
    * 
    * Esta clase maneja la interacción con la base de datos relacionado con los métodos de pago.
*/
class PaymentMethodModel {
    /**
        * Obtiene los métodos de pago de un usuario por su ID.
        *
        * Este método recupera todos los métodos de pago asociados a un usuario específico 
        * utilizando el `userId`. Devuelve todos los métodos de pago registrados en la base de datos.
        *
        * @param PDO $connection Instancia de la conexión a la base de datos.
        * @param int $userId El ID del usuario cuyos métodos de pago se desean recuperar.
        *
        * @return array Un array con los métodos de pago del usuario, 
        *               cada uno representado por un registro con su información 
        *               (como detalles de la tarjeta, tipo de pago, etc.). Si no se encuentran métodos de pago, 
        *               se devolverá un array vacío.
    */
    public function getPaymentMethodById ($connection, $userId) {
        $stmt = $connection->prepare('SELECT * FROM payment_methods WHERE user_id = :userId;');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}