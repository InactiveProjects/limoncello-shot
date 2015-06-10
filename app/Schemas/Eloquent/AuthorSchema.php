<?php namespace App\Schemas\Eloquent;

use \App\Models\Eloquent\Author;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Author schema.
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
    protected function getBaseSelfUrl($resource)
    {
        // NOTE: You can dynamically set resource urls

        return \Request::getSchemeAndHttpHost() . $this->baseSelfUrl . '/';
    }

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
            'first_name' => $author->first_name,
            'last_name'  => $author->last_name,
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
