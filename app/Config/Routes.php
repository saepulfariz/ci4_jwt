<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('/api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    $routes->post('login', 'AuthController::login');
    $routes->post('refresh-token', 'AuthController::refreshToken');

    $routes->get('me', 'UserController::me', ['filter' => 'App\Filters\AuthFilter']);

    $routes->group('/', ['namespace' => 'App\Controllers\Api', 'filter' => 'App\Filters\AuthFilter:administrator'], function ($routes) {
        $routes->get('roles', 'UserController::roles');
        $routes->resource('users', ['controller' => 'UserController']);
    });

    $routes->group('/', ['namespace' => 'App\Controllers\Api', 'filter' => 'App\Filters\AuthFilter:administrator,member'], function ($routes) {
        $routes->resource('products', ['controller' => 'ProductController']);
    });
});
