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

use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\BoardsController;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\CommentsController;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\Controller;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\PostsController;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\RolesController;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\UsersController;
use Neomerx\LimoncelloIlluminate\Http\Controllers\Web\HomeController;
use Neomerx\LimoncelloIlluminate\Schemas\BoardSchema;
use Neomerx\LimoncelloIlluminate\Schemas\CommentSchema;
use Neomerx\LimoncelloIlluminate\Schemas\PostSchema;
use Neomerx\LimoncelloIlluminate\Schemas\RoleSchema;
use Neomerx\LimoncelloIlluminate\Schemas\UserSchema;

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/', HomeController::class . '@index');
$app->post('authenticate', HomeController::class . '@authenticate');

$app->group([
    'middleware' => ['auth'],
    'prefix'     => Controller::API_URL_PREFIX,
], function () use ($app) {

    $regResource = function ($type, $controllerClass) use ($app) {
        $app->get("$type", $controllerClass . '@index');
        $app->get("$type/{idx}", $controllerClass . '@show');
        $app->post("$type", $controllerClass . '@store');
        $app->patch("$type/{idx}", $controllerClass . '@update');
        $app->delete("$type/{idx}", $controllerClass . '@destroy');
    };

    $regGetRelationship = function ($type, $relationship, $handler) use ($app) {
        $app->get($type . '/{idx}/' . DocumentInterface::KEYWORD_RELATIONSHIPS . '/' . $relationship, $handler);
    };

    $regResource(BoardSchema::TYPE, BoardsController::class);
    $regGetRelationship(BoardSchema::TYPE, BoardSchema::REL_POSTS, BoardsController::class . '@indexPosts');

    $regResource(CommentSchema::TYPE, CommentsController::class);

    $regResource(PostSchema::TYPE, PostsController::class);
    $regGetRelationship(PostSchema::TYPE, PostSchema::REL_COMMENTS, PostsController::class . '@indexComments');

    $regResource(RoleSchema::TYPE, RolesController::class);

    $regResource(UserSchema::TYPE, UsersController::class);
    $regGetRelationship(UserSchema::TYPE, UserSchema::REL_POSTS, UsersController::class . '@indexPosts');
    $regGetRelationship(UserSchema::TYPE, UserSchema::REL_COMMENTS, UsersController::class . '@indexComments');

});
