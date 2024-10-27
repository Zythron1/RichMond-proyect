<?php

class PurchaseHistoryRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'PurchaseHistoryController', 'action' => 'getAllPurchaseHistory', 'route' => '/PurchaseHistory', 'method' => 'GET'],
        ['controller' => 'PurchaseHistoryController', 'action' => 'GetpurchaseHistoryById', 'route' => '/PurchaseHistory/{userId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'PurchaseHistoryController', 'action' => 'createPurchaseHistory', 'route' => '/PurchaseHistory', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'PurchaseHistoryController', 'action' => '', 'route' => '', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'PurchaseHistoryController', 'action' => '', 'route' => '', 'method' => 'DELETE']
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