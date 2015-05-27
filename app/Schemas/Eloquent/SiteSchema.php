<?php namespace App\Schemas\Eloquent;

use \App\Models\Eloquent\Site;
use \Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * Site schema.
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
    protected $baseSelfUrl = '/sites';

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
    public function getRelationships($site)
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
