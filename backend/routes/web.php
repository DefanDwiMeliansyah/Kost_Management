<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return response()->json([
        'app' => 'Kost Management API',
        'version' => $router->app->version()
    ]);
});

// Public routes
$router->group(['prefix' => 'api/auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

// Protected routes
$router->group(['prefix' => 'api/auth', 'middleware' => 'auth'], function () use ($router) {
    $router->post('logout', 'AuthController@logout');
    $router->get('me', 'AuthController@me');
    $router->post('refresh', 'AuthController@refresh');
});

// Rooms
$router->group(['prefix' => 'api/rooms'], function () use ($router) {
    $router->get('/', 'RoomController@index');
    $router->post('/', 'RoomController@store');
    $router->get('/statistics', 'RoomController@statistics');
    $router->get('/{id}', 'RoomController@show');
    $router->put('/{id}', 'RoomController@update');
    $router->delete('/{id}', 'RoomController@destroy');
});

// Tenants
$router->group(['prefix' => 'api/tenants'], function () use ($router) {
    $router->get('/', 'TenantController@index');
    $router->post('/', 'TenantController@store');
    $router->get('/statistics', 'TenantController@statistics');
    $router->get('/expiring-soon', 'TenantController@expiringSoon');
    $router->get('/{id}', 'TenantController@show');
    $router->put('/{id}', 'TenantController@update');
    $router->post('/{id}', 'TenantController@update');
    $router->delete('/{id}', 'TenantController@destroy');
});
