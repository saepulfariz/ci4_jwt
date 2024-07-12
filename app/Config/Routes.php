<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('/api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    $routes->post('login', 'AuthController::login');
    $routes->post('refresh-token', 'AuthController::refreshToken');
});
