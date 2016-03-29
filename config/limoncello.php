<?php

use Neomerx\LimoncelloIlluminate\Http\Controllers\Api\Controller;
use Neomerx\LimoncelloIlluminate\Schemas\BoardSchema;
use Neomerx\LimoncelloIlluminate\Schemas\CommentSchema;
use Neomerx\LimoncelloIlluminate\Schemas\PostSchema;
use Neomerx\LimoncelloIlluminate\Schemas\RoleSchema;
use Neomerx\LimoncelloIlluminate\Schemas\UserSchema;
use Neomerx\Limoncello\Settings\Settings as S;

return [

    /*
    |--------------------------------------------------------------------------
    | A list of schemas
    |--------------------------------------------------------------------------
    |
    | Here you can specify what schemas should be used for object on encoding
    | to JSON API format.
    |
    */
    S::SCHEMAS => [
        BoardSchema::class,
        PostSchema::class,
        CommentSchema::class,
        UserSchema::class,
        RoleSchema::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON encoding options
    |--------------------------------------------------------------------------
    |
    | Here you can specify options to be used while converting data to actual
    | JSON representation with json_encode function.
    |
    | For example if options are set to JSON_PRETTY_PRINT then returned data
    | will be nicely formatted with spaces.
    |
    | see http://php.net/manual/en/function.json-encode.php
    |
    | If this section is omitted default values will be used.
    |
    */
    S::JSON => [
        S::JSON_OPTIONS         => JSON_PRETTY_PRINT,
        S::JSON_DEPTH           => S::JSON_DEPTH_DEFAULT,
        S::JSON_IS_SHOW_VERSION => !S::JSON_IS_SHOW_VERSION_DEFAULT,
        S::JSON_URL_PREFIX      => Controller::API_URL_PREFIX,
        S::JSON_VERSION_META    => [
            'name'       => 'JSON API Neomerx Demo Application',
            'copyright'  => '2015-2016 info@neomerx.com',
            'powered-by' => 'Neomerx limoncello shot',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth settings
    |--------------------------------------------------------------------------
    |
    | Here you can specify options for authentication.
    |
    | Value for AUTH_CODEC must point to implementation of TokenCodecInterface
    | which transforms user/account into token and back.
    |
    */
    S::AUTH => [
        S::AUTH_CODEC => \Neomerx\LimoncelloIlluminate\Authentication\TokenCodec::class,
    ],

];
