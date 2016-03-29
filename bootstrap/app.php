<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

if (env('APP_USE_LOG_SERVER', false) === true) {
    $app->configureMonologUsing(function (\Monolog\Logger $monolog) {
        $logServer = env('APP_LOG_SERVER', 'logs');
        $publisher = new \Gelf\Publisher(new \Gelf\Transport\UdpTransport($logServer));
        $handler   = new \Monolog\Handler\GelfHandler($publisher);
        $handler->pushProcessor(new \Monolog\Processor\WebProcessor());
        $handler->pushProcessor(new \Monolog\Processor\UidProcessor());
        $monolog->pushHandler($handler);

        return $monolog;
    });
}

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Neomerx\CorsIlluminate\CorsMiddleware::class,
]);

$app->routeMiddleware([
    'auth' => \Neomerx\Limoncello\Http\AuthMiddleware::class,
]);
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

//$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

$app->register(\Neomerx\Limoncello\Providers\LumenServiceProvider::class);
$app->register(\Neomerx\CorsIlluminate\Providers\LumenServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

require __DIR__ . '/../app/Http/routes.php';

return $app;
