<?php

use \Neomerx\Limoncello\Config\Config as C;

return [

    /*
    |--------------------------------------------------------------------------
    | Mapping between objects and their schemas
    |--------------------------------------------------------------------------
    |
    | Here you can specify what schemas should be used for object on encoding
    | to JSON API format.
    |
    | Supported schemas: as a class name, as closure.
    |
    */
    C::SCHEMAS => [
        \App\Models\PDO\Author::class       => \App\Schemas\PDO\AuthorSchema::class,
        \App\Models\PDO\Comment::class      => \App\Schemas\PDO\CommentSchema::class,
        \App\Models\PDO\Post::class         => \App\Schemas\PDO\PostSchema::class,
        \App\Models\PDO\Site::class         => \App\Schemas\PDO\SiteSchema::class,

        \App\Models\Eloquent\Author::class  => \App\Schemas\Eloquent\AuthorSchema::class,
        \App\Models\Eloquent\Comment::class => \App\Schemas\Eloquent\CommentSchema::class,
        \App\Models\Eloquent\Post::class    => \App\Schemas\Eloquent\PostSchema::class,
        \App\Models\Eloquent\Site::class    => \App\Schemas\Eloquent\SiteSchema::class,
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
    C::JSON => [
        C::JSON_OPTIONS => JSON_PRETTY_PRINT,
        C::JSON_DEPTH   => C::JSON_DEPTH_DEFAULT,
    ]

];
