<?php

class DecodeEncodeRequestData {

    public static function decodeJson ($jsonData) {
        try {
            // Verificar si hay datos antes de intentar decodificar
            if (empty($jsonData)) {
                return ['status' => 'succes'];  // Retorna un array vacío si no hay datos
            }

            // Paso 1: Decodificar el JSON en un array asociativo
            $data = json_decode($jsonData, true);

            // Paso 2: Verificar si hubo un error en la decodificación
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Lanza una excepción con el mensaje de error de decodificación
                throw new Exception('Error de decodificación JSON: ' . json_last_error_msg());
            }

            // Paso 3: Si la decodificación fue exitosa, devuelve los datos
            $data['status'] = 'succes';
            return $data;
        } catch (Exception $e) {
            // Paso 4: Si se lanza una excepción, devuelve un array con el estado y el mensaje
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

}