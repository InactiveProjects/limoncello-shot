<?php namespace App\Schemas\Eloquent;

use \App\Models\Eloquent\Comment;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Comment schema.
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
    protected $baseSelfUrl = '/comments/';

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
