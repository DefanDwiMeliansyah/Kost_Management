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
