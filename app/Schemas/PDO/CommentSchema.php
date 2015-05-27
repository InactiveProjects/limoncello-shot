<?php namespace App\Schemas\PDO;

use \App\Models\PDO\Comment;
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
        return $comment->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($comment)
    {
        /** @var Comment $comment */
        return [
            Comment::FIELD_BODY => $comment->getBody(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($comment)
    {
        /** @var Comment $comment */
        return [
            Comment::LINK_POST => [self::DATA => $comment->getPost()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIncludePaths()
    {
        return [
            Comment::LINK_POST,
        ];
    }
}
