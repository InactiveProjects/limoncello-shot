<?php namespace App\Schemas\Eloquent;

use \App\Models\Eloquent\Post;
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
        return $post->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($post)
    {
        /** @var Post $post */
        return [
            'title' => $post->title,
            'body'  => $post->body,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($post)
    {
        /** @var Post $post */
        return [
            'author'   => [self::DATA => $post->author],
            'comments' => [self::DATA => $post->comments->all()],
        ];
    }
}
