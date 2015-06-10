<?php namespace App\Schemas\PDO;

use \App\Models\PDO\Post;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Post schema.
 */
class PostSchema extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'posts';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/posts/';

    /**
     * @inheritdoc
     */
    public function getId($post)
    {
        /** @var Post $post */
        return $post->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($post)
    {
        /** @var Post $post */
        return [
            Post::FIELD_TITLE => $post->getTitle(),
            Post::FIELD_BODY  => $post->getBody(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($post)
    {
        /** @var Post $post */
        return [
            Post::LINK_AUTHOR   => [self::DATA => $post->getAuthor()],
            Post::LINK_COMMENTS => [self::DATA => $post->getComments()],
        ];
    }
}
