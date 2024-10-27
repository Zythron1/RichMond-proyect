<?php

header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// paso 1: Recuperar la url particionada y el método.
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// paso 2: Obtener los datos envviados en el body y utilizar el método estático de la clase DecodeEncodeRequestData
$jsonData = file_get_contents('php://input');
require_once './backend/src/services/DecodeEncodeRequestData.php';
$data = DecodeEncodeRequestData::decodeJson($jsonData);

//paso 2: partir la url por cada / que se encuentre y almacenar el nombre de la entidad para hacer la verificación de a quién
$urlParts = explode('/', trim($url, '/'));
$entity = $urlParts[0];

// paso 4: Crear la variable que almacenará los parámetros para el método del enrutador principal según la entidad llamada en la petición
$routes;

// paso 4: Verificar a qué entidad instaciar
switch ($entity) {
    case 'user':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'Product':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'Category':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'ShoppingBag':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'BagProduct':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'PaymentMethod':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'Order':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'Shipment':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    case 'PurchaseHistory':
        require_once './backend/src/routers/UserRouter.php';
        $userRouter = new UserRouter();
        if ($method === 'GET') {
            $routes = $userRouter->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $userRouter->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $userRouter->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $userRouter->getRoutesDelete();
        }
        break;
    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ruta no encontrada.'
        ]);
        return;
}

// paso 5: instaciar el enrutador dinámico
require_once './backend/src/routers/Router.php';
$router = new Router;

    // paso 6: Verificar la longitud de la ruta y Llamar el método que va a enrutar dinámicamente las peticiones a los controladores según la longitud de la ruta

    $router->disPatch1($routes, $urlParts, $data);
