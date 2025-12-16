<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades(true, [
    PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::class => 'JWTAuth',
    PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory::class => 'JWTFactory'
]);

$app->withEloquent();

// Register Container Bindings
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// Configure
$app->configure('app');
$app->configure('auth');
$app->configure('jwt');
$app->configure('database');

// Register Middleware
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

// Register Service Providers
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(PHPOpenSourceSaver\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(App\Providers\AppServiceProvider::class);

// Register Migration CLI
$app->register(Illuminate\Database\DatabaseServiceProvider::class);
$app->register(Illuminate\Database\MigrationServiceProvider::class);

// Load Routes
$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;