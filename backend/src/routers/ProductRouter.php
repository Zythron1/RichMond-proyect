<?php

/**
    * Class ProductRouter
    * 
    * Esta clase gestiona las rutas relacionadas con los productos para la API.
    * Define las rutas GET, POST, PUT y DELETE para acceder a la información de los productos.
*/
class ProductRouter {
    /**
        * Rutas para solicitudes GET.
        * 
        * Estas rutas están configuradas para obtener datos.
        * @var array
    */
    private $routesGet = [
        ['controller' => 'ProductController', 'action' => 'getAllProducts', 'route' => '/product', 'method' => 'GET'],
        ['controller' => 'ProductController', 'action' => 'getProductById', 'route' => '/product/{productId}', 'method' => 'GET'],
        ['controller' => 'ProductController', 'action' => 'getProductsByCategoryWithLimitAndOffset', 'route' => '/product/{categoryId}/{limit}/{offset}', 'method' => 'GET'],
        ['controller' => 'ProductController', 'action' => 'getPartialProductById', 'route' => '/product/{productId}/partialData', 'method' => 'GET'],
    ]; 


    /**
        * Rutas para solicitudes POST.
        * 
        * Estas rutas están configuradas para enviar datos a la API.
        * @var array
    */
    private $routesPost = [
        ['controller' => 'ProductController', 'action' => 'createProduct', 'route' => '/product', 'method' => 'POST']
    ]; 


    /**
        * Rutas para solicitudes PUT.
        * 
        * Estas rutas están configuradas para actualizar datos existentes.
        * @var array
    */
    private $routesPut = [
        ['controller' => 'ProductController', 'action' => 'updateProduct', 'route' => '/product/{productId}/update', 'method' => 'PUT']
    ]; 


    /**
        * Rutas para solicitudes DELETE.
        * 
        * Estas rutas están configuradas para eliminar datos de la API.
        * @var array
    */
    private $routesDelete = [
        ['controller' => 'ProductController', 'action' => 'deleteProduct', 'route' => '/product/{productId}/delete', 'method' => 'DELETE']
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