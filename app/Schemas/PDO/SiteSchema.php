<?php namespace App\Schemas\PDO;

use \App\Models\PDO\Post;
use \App\Models\PDO\Site;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Site schema.
 */
class SiteSchema extends SchemaProvider
{
    /**
     * @var int
     */
    protected $defaultParseDepth = 2;

    /**
     * @inheritdoc
     */
    protected $resourceType = 'sites';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/sites/';

    /**
     * @inheritdoc
     */
    public function getId($site)
    {
        /** @var Site $site */
        return $site->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($site)
    {
        /** @var Site $site */
        return [
            Site::FIELD_NAME => $site->getName(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($site)
    {
        /** @var Site $site */
        return [
            Site::LINK_POSTS => [self::DATA => $site->getPosts()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIncludePaths()
    {
        return [
            Site::LINK_POSTS,
            Site::LINK_POSTS . '.' . Post::LINK_AUTHOR,
            Site::LINK_POSTS . '.' . Post::LINK_COMMENTS,
        ];
    }
}
