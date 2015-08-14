<?php namespace App\Schemas;

use \App\Models\Post;
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
    public function getRelationships($comment, array $includeRelationships = [])
    {
        /** @var Comment $comment */

        // that's an example how $includeRelationships could be used for reducing requests to database
        if (isset($includeRelationships['post']) === true) {
            // as post will be included as full resource we have to give full resource
            $post = $comment->post;
        } else {
            // as post will be included as just id and type so it's not necessary to load it from database
            $post = new Post();
            $post->setAttribute($post->getKeyName(), $comment->post_id);
        }

        return [
            'post' => [self::DATA => $post],
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
