<?php

use \App\Http\Controllers\PDO\PostsController;

$app->get(
    'posts',
    ['uses' => PostsController::class.'@index']
);

$app->get(
    'posts/{id}',
    ['uses' => PostsController::class.'@show']
);

$app->post(
    '/posts',
    ['uses' => PostsController::class.'@store']
);

$app->delete(
    '/posts/{id}',
    ['uses' => PostsController::class.'@destroy']
);

$app->patch(
    '/posts/{id}',
    ['uses' => PostsController::class.'@update']
);
