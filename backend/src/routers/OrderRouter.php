<?php

class OrderRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'OrderController', 'action' => 'getAllOrder', 'route' => '/Order', 'method' => 'GET'],
        ['controller' => 'OrderController', 'action' => 'getOrderById', 'route' => '/Order/{orderId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'OrderController', 'action' => 'createOrder', 'route' => '/Order', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'OrderController', 'action' => 'updateOrder', 'route' => '/Order/{orderId}/update', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'OrderController', 'action' => 'deleteOrder', 'route' => '/Order/{orderId}/delete', 'method' => 'DELETE']
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