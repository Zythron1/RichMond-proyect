<?php 

class ShoppingBagRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'ShoppingBagController', 'action' => 'getShoppingBagById', 'route' => '/shoppingBag/{userId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'ShoppingBagController', 'action' => 'createShoppingBag', 'route' => '/shoppingBag/{userId}', 'method' => 'POST'],
        ['controller' => 'ShoppingBagController', 'action' => 'addProduct', 'route' => '/shoppingBag/{userId}/addProduct', 'method' => 'POST'],
        ['controller' => 'ShoppingBagController', 'action' => 'checkOuts', 'route' => '/shoppingBag/{userId}/checkout', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'ShoppingBagController', 'action' => '', 'route' => '', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'ShoppingBagController', 'action' => 'deleteProduct', 'route' => '/shoppingBag/{userId}', 'method' => 'DELETE']
    ]; 

    // paso 2: hacer todas las funciones para obtener los datos 
    public function getRoutesGet () {
        return $this->routesGet;
    }

    public function getRoutesPost () {
        return $this->routesPost;
    }

    public function getRoutesPut () {
        return $this->routesPut;
    }

    public function getRoutesDelete () {
        return $this->routesDelete;
    }
}