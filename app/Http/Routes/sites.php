<?php

use \App\Http\Controllers\PDO\SitesController;

$app->get(
    'sites',
    ['uses' => SitesController::class.'@index']
);

$app->get(
    'sites/{id}',
    ['uses' => SitesController::class.'@show']
);

$app->post(
    '/sites',
    ['uses' => SitesController::class.'@store']
);

$app->delete(
    '/sites/{id}',
    ['uses' => SitesController::class.'@destroy']
);

$app->patch(
    '/sites/{id}',
    ['uses' => SitesController::class.'@update']
);


// Handler for Eloquent-based models
$app->get(
    'sites-eloquent/{id}',
    ['uses' => \App\Http\Controllers\Eloquent\SitesController::class.'@show']
);
