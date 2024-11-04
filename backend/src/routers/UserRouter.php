<?php

class UserRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'UserController', 'action' => 'getAllUsers', 'route' => '/user', 'method' => 'GET'],
        ['controller' => 'UserController', 'action' => 'getUserById', 'route' => '/user/{userId}', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'UserController', 'action' => 'createUser', 'route' => '/user', 'method' => 'POST'],
        ['controller' => 'UserController', 'action' => 'login', 'route' => '/user/login', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'UserController', 'action' => 'updateUser', 'route' => '/user/{userId}/updateUser', 'method' => 'PUT'],
        ['controller' => 'UserController', 'action' => 'changePassword', 'route' => '/user/{userId}/changePassword', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'UserController', 'action' => '', 'route' => '/user/{userId}', 'method' => 'DELETE'],
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