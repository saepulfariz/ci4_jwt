<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('/api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    $routes->post('login', 'AuthController::login');
    $routes->post('refresh-token', 'AuthController::refreshToken');

    $routes->group('/', ['namespace' => 'App\Controllers\Api', 'filter' => 'App\Filters\AuthFilter:administrator'], function ($routes) {
        $routes->resource('users', ['controller' => 'UserController']);
    });

    $routes->group('/', ['namespace' => 'App\Controllers\Api', 'filter' => 'App\Filters\AuthFilter:administrator,member'], function ($routes) {
        $routes->resource('products', ['controller' => 'ProductController']);
    });
});
