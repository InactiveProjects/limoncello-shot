<?php

use \App\Http\Controllers\Demo\CommentsController;

$app->get(
    'comments',
    ['uses' => CommentsController::class.'@index']
);
$app->get(
    'comments/{id}',
    ['uses' => CommentsController::class.'@show']
);
$app->post(
    '/comments',
    ['uses' => CommentsController::class.'@store']
);
$app->delete(
    '/comments/{id}',
    ['uses' => CommentsController::class.'@destroy']
);
$app->patch(
    '/comments/{id}',
    ['uses' => CommentsController::class.'@update']
);
