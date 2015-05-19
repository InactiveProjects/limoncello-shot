<?php namespace App\Schemas\PDO;

use \App\Models\PDO\Author;
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
    protected $baseSelfUrl = '/authors';

    /**
     * @inheritdoc
     */
    public function getId($author)
    {
        /** @var Author $author */
        return $author->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($author)
    {
        /** @var Author $author */
        return [
            Author::FIELD_FIRST_NAME => $author->getFirstName(),
            Author::FIELD_LAST_NAME  => $author->getLastName(),
            Author::FIELD_TWITTER    => $author->getTwitter(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks($author)
    {
        /** @var Author $author */
        return [
            Author::LINK_POSTS => [self::DATA => $author->getPosts()],
        ];
    }
}
