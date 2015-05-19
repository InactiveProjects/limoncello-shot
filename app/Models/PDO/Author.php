<?php namespace App\Models\PDO;

/**
 */
class Author extends Model
{
    /** Model table */
    const TABLE_NAME = 'authors';

    /** Field name */
    const FIELD_FIRST_NAME = 'first_name';

    /** Field name */
    const FIELD_LAST_NAME = 'last_name';

    /** Field name */
    const FIELD_TWITTER = 'twitter';

    /** Link name */
    const LINK_POSTS = 'posts';

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getField(self::FIELD_FIRST_NAME);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getField(self::FIELD_LAST_NAME);
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->getField(self::FIELD_TWITTER);
    }

    /**
     * @param string $value
     */
    public function setFirstName($value)
    {
        $this->setField(self::FIELD_FIRST_NAME, $value);
    }

    /**
     * @param string $value
     */
    public function setLastName($value)
    {
        $this->setField(self::FIELD_LAST_NAME, $value);
    }

    /**
     * @param string $value
     */
    public function setTwitter($value)
    {
        $this->setField(self::FIELD_TWITTER, $value);
    }

    /**
     * @return Post[]
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, Post::FIELD_AUTHOR_ID);
    }
}
