<?php 

class Router {
    
    // Método que enruta dinámicamente
    public function disPatch1 ($routes, $urlParts, $data) {

        $routeMatches = false;
        // paso 1: Recorrer las rutas y dividol la ruta en partes
        foreach ($routes as $route) {
            $routeParts = explode('/', trim($route['route'], '/'));

            // paso 2: Verificar la longitud de las dos rutas
            if (count($urlParts) === count($routeParts)) {
                // paso 3: Crear 1 variable con valor boolean true si la longitud es igual. Crear una variable que almacenará los valores  que reemplazarán el lugar donde está la expresión regular y se fusioinará con la data llegada en el body
                $routeMatches = true;
                $params = [];
            } 

            // paso 4: Recorrer el array que contiene la ruta 
            foreach ($routeParts as $index => $routePart) {
                // paso 5: verificar si la expresión regualar existe en cada parte de la ruta, si existe agregarla a una variable el valor de la ruta, limpiarlo y utilizarlo como clave al fusionar los arrays que contienen la información para el método del controlador
                if (preg_match('/\{[a-zA-Z_]+\}/', $routePart)) {
                    $cleanKey = preg_replace('/^\{(.*)\}$/', '$1', $routePart);
                    // paso 6: Si existe almacenar el dato en la variable que guarda los parámetros
                    if (isset($urlParts[$index])) {
                        $params[$cleanKey] = $urlParts[$index];
                    } else {
                        $routeMatches = false;
                        break;
                    }

                    // paso 7: Sino existe un parámetro dinámico, verificar la ruta y la url parte por parte
                } else {
                    if ($urlParts[$index] !== $routePart) {
                        // paso 8: Si son diferentes cambiar el estado de la variable rutasCoincididas a false y romper para que no siga ejecutandose el método.
                        $routeMatches = false;
                        break;
                    }
                }
            }

            // paso 9: Verificar el estado de la variable boolean
            if ($routeMatches){
                // paso 10: Almacenar en variables el nombre del archivo del controlador y la acción, además, requerir el archivo e instaciar la clase
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                require_once "./backend/src/controllers/{$controllerName}.php";
                $controller = new $controllerName;

                // paso 11: Verificar que la variable data no tenga un error al haber decodificado los datos
                if ($data['status'] === 'error') {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => $data['message']
                    ]);
                    return;
                } 

                // paso 12: fusionar los arrays que contienen los parámetros, y verificar si hay contenido
                $newData = array_merge($data, $params);
                if (empty($newData)) {
                    // paso 13: Parte true. LLamar al método sin pasar los parámetros
                    $controller->$actionName();
                    return;
                } else {
                    // paso 14: Parte false. Llamar al método y pasar le los parámetros
                    $controller->$actionName($newData);
                    return;
                }
            }
        }
        // paso 15: Si en el foreach no se encuentra coincidencias entre la ruta y la url, retornar un código de respuesta http y la respuesta en formato Json.
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' =>'Problemas con la petición, intenta de nuevo.', 'messageToDeveloper' => 'Ruta no encontrada.']);
    }
}
