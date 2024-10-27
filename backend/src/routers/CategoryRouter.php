<?php

class CategoryRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'CategoryController', 'action' => 'getAllCategories', 'route' => '/category', 'method' => 'GET'],
        ['controller' => 'CategoryController', 'action' => 'getCategorieById', 'route' => '/category/{categoryId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'CategoryController', 'action' => 'createCategory', 'route' => '/category', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'CategoryController', 'action' => 'updateCategory', 'route' => '/category/{categoryId}/update', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'CategoryController', 'action' => '', 'route' => '', 'method' => 'DELETE']
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