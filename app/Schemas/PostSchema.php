<?php namespace App\Schemas;

use \App\Models\Post;
use \App\Models\Author;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * @package Neomerx\LimoncelloShot
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
    public function getRelationships($post, array $includeRelationships = [])
    {
        /** @var Post $post */

        // that's an example how $includeRelationships could be used for reducing requests to database
        if (isset($includeRelationships['author']) === true) {
            // as author will be included as full resource we have to give full resource
            $author = $post->author;
        } else {
            // as author will be included as just id and type so it's not necessary to load it from database
            $author = new Author();
            $author->setAttribute($author->getKeyName(), $post->author_id);
        }

        return [
            'author'   => [self::DATA => $author],
            'comments' => [self::DATA => $post->comments->all()],
        ];
    }
}
