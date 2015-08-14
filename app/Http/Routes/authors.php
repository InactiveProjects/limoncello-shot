<?php

use \App\Http\Controllers\Demo\AuthorsController;

$app->get(
    'authors',
    ['uses' => AuthorsController::class.'@index']
);
$app->get(
    'authors/{id}',
    ['uses' => AuthorsController::class.'@show']
);
$app->post(
    '/authors',
    ['uses' => AuthorsController::class.'@store']
);
$app->delete(
    '/authors/{id}',
    ['uses' => AuthorsController::class.'@destroy']
);
$app->patch(
    '/authors/{id}',
    ['uses' => AuthorsController::class.'@update']
);
