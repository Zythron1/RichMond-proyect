<?php

header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// paso 1: Recuperar la url particionada y el método.
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// paso 2: Obtener los datos envviados en el body y utilizar el método estático de la clase DecodeEncodeRequestData
$jsonData = file_get_contents('php://input');
require_once './backend/src/helpers/DecodeEncodeRequestData.php';
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
    case 'product':
        require_once './backend/src/routers/ProductRouter.php';
        $product = new ProductRouter();
        if ($method === 'GET') {
            $routes = $product->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $product->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $product->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $product->getRoutesDelete();
        }
        break;
    case 'category':
        require_once './backend/src/routers/CategoryRouter.php';
        $category = new CategoryRouter();
        if ($method === 'GET') {
            $routes = $category->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $category->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $category->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $category->getRoutesDelete();
        }
        break;
    case 'shoppingBag':
        require_once './backend/src/routers/ShoppingBagRouter.php';
        $shoppingBag = new ShoppingBagRouter();
        if ($method === 'GET') {
            $routes = $shoppingBag->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $shoppingBag->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $shoppingBag->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $shoppingBag->getRoutesDelete();
        }
        break;
    case 'bagProduct':
        require_once './backend/src/routers/BagProductRouter.php';
        $bagProduct = new BagProductRouter();
        if ($method === 'GET') {
            $routes = $bagProduct->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $bagProduct->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $bagProduct->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $bagProduct->getRoutesDelete();
        }
        break;
    case 'paymentMethod':
        require_once './backend/src/routers/PaymentMethodRouter.php';
        $paymentMethod = new PaymentMethodRouter();
        if ($method === 'GET') {
            $routes = $paymentMethod->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $paymentMethod->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $paymentMethod->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $paymentMethod->getRoutesDelete();
        }
        break;
    case 'order':
        require_once './backend/src/routers/OrderRouter.php';
        $order = new OrderRouter();
        if ($method === 'GET') {
            $routes = $order->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $order->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $order->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $order->getRoutesDelete();
        }
        break;
    case 'shipment':
        require_once './backend/src/routers/ShipmentRouter.php';
        $shipment = new ShipmentRouter();
        if ($method === 'GET') {
            $routes = $shipment->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $shipment->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $shipment->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $shipment->getRoutesDelete();
        }
        break;
    case 'purchaseHistory':
        require_once './backend/src/routers/PurchaseHistoryRouter.php';
        $purchaseHistory = new PurchaseHistoryRouter();
        if ($method === 'GET') {
            $routes = $purchaseHistory->getRoutesGet();
        } elseif ($method === 'POST') {
            $routes = $purchaseHistory->getRoutesPost();
        } elseif ($method === 'PUT') {
            $routes = $purchaseHistory->getRoutesPut();
        } elseif ($method === 'DELETE') {
            $routes = $purchaseHistory->getRoutesDelete();
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
