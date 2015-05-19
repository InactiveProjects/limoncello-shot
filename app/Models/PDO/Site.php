<?php namespace App\Models\PDO;

/**
 */
class Site extends Model
{
    /** Model table */
    const TABLE_NAME = 'sites';

    /** Field name */
    const FIELD_NAME = 'name';

    /** Link name */
    const LINK_POSTS = 'posts';

    /**
     * @var string
     */
    protected static $tableName = self::TABLE_NAME;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getField(self::FIELD_NAME);
    }

    /**
     * @param string $value
     */
    public function setName($value)
    {
        $this->setField(self::FIELD_NAME, $value);
    }

    /**
     * @return Post[]
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, Post::FIELD_SITE_ID);
    }
}
