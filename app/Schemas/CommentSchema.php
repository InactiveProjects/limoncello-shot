<?php namespace App\Schemas;

use \App\Models\Comment;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * @package Neomerx\LimoncelloShot
 */
class CommentSchema extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'comments';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/comments/';

    /**
     * @inheritdoc
     */
    public function getId($comment)
    {
        /** @var Comment $comment */
        return $comment->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($comment)
    {
        /** @var Comment $comment */
        return [
            'body' => $comment->body,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($comment)
    {
        /** @var Comment $comment */
        return [
            'post' => [self::DATA => $comment->post],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIncludePaths()
    {
        return [
            'post',
        ];
    }
}
