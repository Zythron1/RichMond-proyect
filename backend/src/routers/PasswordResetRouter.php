<?php

class PasswordResetRouter {
    // paso 1: Crear 4 atributos que guardan la información necesaria para ejecutar el método requerido en la petición (controlador y la acción del controlador, ruta y método)
    private $routesGet = [
        ['controller' => 'PasswordResetController', 'action' => '', 'route' => '', 'method' => 'GET']
    ]; 
    private $routesPost = [
        ['controller' => 'PasswordResetController', 'action' => '', 'route' => '', 'method' => 'POST']
    ]; 
    private $routesPut = [
        ['controller' => 'PasswordResetController', 'action' => '', 'route' => '', 'method' => 'PUT']
    ]; 
    private $routesDelete = [
        ['controller' => 'PasswordResetController', 'action' => '', 'route' => '', 'method' => 'DELETE']
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