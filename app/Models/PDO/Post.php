<?php namespace App\Models\PDO;

/**
 */
class Post extends Model
{
    /** Model table */
    const TABLE_NAME = 'posts';

    /** Field name */
    const FIELD_TITLE = 'title';

    /** Field name */
    const FIELD_BODY = 'body';

    /** Field name */
    const FIELD_AUTHOR_ID = 'author_id';

    /** Field name */
    const FIELD_SITE_ID = 'site_id';

    /** Link name */
    const LINK_SITE = 'site';

    /** Link name */
    const LINK_AUTHOR = 'author';

    /** Link name */
    const LINK_COMMENTS = 'comments';

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getField(self::FIELD_TITLE);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->getField(self::FIELD_BODY);
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->getLink(self::FIELD_AUTHOR_ID, Author::class);
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->getLink(self::FIELD_SITE_ID, Site::class);
    }

    /**
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->setField(self::FIELD_TITLE, $value);
    }

    /**
     * @param string $value
     */
    public function setBody($value)
    {
        $this->setField(self::FIELD_BODY, $value);
    }

    /**
     * @param Author $value
     */
    public function setAuthor(Author $value)
    {
        $this->setLink(self::FIELD_AUTHOR_ID, $value);
    }

    /**
     * @param Site $value
     */
    public function setSite(Site $value)
    {
        $this->setLink(self::FIELD_SITE_ID, $value);
    }

    /**
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, Comment::FIELD_POST_ID);
    }
}
