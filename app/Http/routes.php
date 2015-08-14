<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use \App\Http\Controllers\Demo\UsersController;

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->get(
    'login/basic',
    ['middleware' => 'jsonapi.basicAuth', 'uses' => UsersController::class.'@getSignedInUserJwt']
);

$app->get(
    'login/refresh',
    ['middleware' => 'jsonapi.jwtAuth', 'uses' => UsersController::class.'@getSignedInUserJwt']
);

$app->group(
    [
        'prefix'     => 'api/v1',
        'middleware' => [
            'jsonapi.jwtAuth', // comment out this line if you want to disable authentication
        ]
    ],
    function () use ($app) {

        include __DIR__ . '/Routes/authors.php';
        include __DIR__ . '/Routes/comments.php';
        include __DIR__ . '/Routes/posts.php';
        include __DIR__ . '/Routes/sites.php';
        include __DIR__ . '/Routes/users.php';

    }
);
