<?php

use \App\Http\Controllers\Demo\UsersController;

$app->get(
    'users',
    ['uses' => UsersController::class.'@index']
);
$app->get(
    'users/{id}',
    ['uses' => UsersController::class.'@show']
);
$app->post(
    '/users',
    ['uses' => UsersController::class.'@store']
);
$app->delete(
    '/users/{id}',
    ['uses' => UsersController::class.'@destroy']
);
$app->patch(
    '/users/{id}',
    ['uses' => UsersController::class.'@update']
);
