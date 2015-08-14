<?php namespace App\Schemas;

use \App\Models\Author;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * @package Neomerx\LimoncelloShot
 */
class AuthorSchema extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'authors';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/authors/';

    /**
     * @inheritdoc
     */
    public function getId($author)
    {
        /** @var Author $author */
        return $author->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($author)
    {
        /** @var Author $author */
        return [
            'first'   => $author->first_name,
            'last'    => $author->last_name,
            'twitter' => $author->twitter,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($author)
    {
        /** @var Author $author */
        return [
            'posts' => [self::DATA => $author->posts->all()],
        ];
    }
}
