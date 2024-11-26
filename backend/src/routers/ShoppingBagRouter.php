<?php 

/**
    * Class ShoppingBagRouter
    * 
    * Esta clase gestiona las rutas relacionadas con la bolsa de compra para la API.
    * Define las rutas GET, POST, PUT y DELETE para acceder a la información de la bolsa de compra.
*/
class ShoppingBagRouter {
    /**
        * Rutas para solicitudes GET.
        * 
        * Estas rutas están configuradas para obtener datos.
        * @var array
    */
    private $routesGet = [
        ['controller' => 'ShoppingBagController', 'action' => 'getShoppingBagById', 'route' => '/shoppingBag/{userId}', 'method' => 'GET']
    ];


    /**
        * Rutas para solicitudes POST.
        * 
        * Estas rutas están configuradas para enviar datos a la API.
        * @var array
    */
    private $routesPost = [
        ['controller' => 'ShoppingBagController', 'action' => 'addProduct', 'route' => '/shoppingBag/addProductToShoppingBag', 'method' => 'POST'],
        ['controller' => 'ShoppingBagController', 'action' => 'createShoppingBag', 'route' => '/shoppingBag/{userId}', 'method' => 'POST'],
        ['controller' => 'ShoppingBagController', 'action' => 'checkOuts', 'route' => '/shoppingBag/{userId}/checkout', 'method' => 'POST'],
    ];


    /**
        * Rutas para solicitudes PUT.
        * 
        * Estas rutas están configuradas para actualizar datos existentes.
        * @var array
    */
    private $routesPut = [
        ['controller' => 'ShoppingBagController', 'action' => '', 'route' => '', 'method' => 'PUT']
    ]; 


    /**
        * Rutas para solicitudes DELETE.
        * 
        * Estas rutas están configuradas para eliminar datos de la API.
        * @var array
    */
    private $routesDelete = [
        ['controller' => 'ShoppingBagController', 'action' => 'deleteProduct', 'route' => '/shoppingBag/{userId}', 'method' => 'DELETE']
    ];



    /**
        * Obtiene las rutas GET definidas.
        *
        * Este método retorna el arreglo de rutas GET que están configuradas en la aplicación.
        * Las rutas GET son utilizadas para obtener datos de la API, como la lista de usuarios.
        *
        * @return array Arreglo con las rutas GET definidas.
    */
    public function getRoutesGet(){
        return $this->routesGet;
    }


    /**
        * Obtiene las rutas POST definidas.
        *
        * Este método retorna el arreglo de rutas POST que están configuradas en la aplicación.
        * Las rutas POST son utilizadas para enviar datos a la API, como la creación de un nuevo usuario.
        *
        * @return array Arreglo con las rutas POST definidas.
    */
    public function getRoutesPost(){
        return $this->routesPost;
    }


    /**
        * Obtiene las rutas PUT definidas.
        *
        * Este método retorna el arreglo de rutas PUT que están configuradas en la aplicación.
        * Las rutas PUT se utilizan para actualizar datos en la API, como la actualización de un usuario.
        *
        * @return array Arreglo con las rutas PUT definidas.
    */
    public function getRoutesPut(){
        return $this->routesPut;
    }


    /**
        * Obtiene las rutas DELETE definidas.
        *
        * Este método retorna el arreglo de rutas DELETE que están configuradas en la aplicación.
        * Las rutas DELETE se utilizan para eliminar recursos de la API, como la eliminación de un usuario.
        *
        * @return array Arreglo con las rutas DELETE definidas.
    */
    public function getRoutesDelete(){
        return $this->routesDelete;
    }
}