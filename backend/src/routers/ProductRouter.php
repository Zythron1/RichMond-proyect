<?php

class ProductRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'ProductController', 'action' => 'getAllProducts', 'route' => '/product', 'method' => 'GET'],
        ['controller' => 'ProductController', 'action' => 'getProductById', 'route' => '/product/{productId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'ProductController', 'action' => 'createProduct', 'route' => '/product', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'ProductController', 'action' => 'updateProduct', 'route' => '/product/{productId}/update', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'ProductController', 'action' => 'deleteProduct', 'route' => '/product/{productId}/delete', 'method' => 'DELETE']
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