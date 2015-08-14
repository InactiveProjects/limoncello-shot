<?php namespace App\Schemas;

use \App\Models\Site;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * @package Neomerx\LimoncelloShot
 */
class SiteSchema extends SchemaProvider
{
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
        return $site->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($site)
    {
        /** @var Site $site */
        return [
            'name' => $site->name,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($site, array $includeRelationships = [])
    {
        /** @var Site $site */
        return [
            'posts' => [self::DATA => $site->posts->all()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIncludePaths()
    {
        return [
            'posts',
            'posts.author',
            'posts.comments',
        ];
    }
}
