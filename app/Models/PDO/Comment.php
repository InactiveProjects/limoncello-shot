<?php namespace App\Models\PDO;

/**
 */
class Comment extends Model
{
    /** Model table */
    const TABLE_NAME = 'comments';

    /** Field name */
    const FIELD_BODY = 'body';

    /** Field name */
    const FIELD_POST_ID = 'post_id';

    /** Link name */
    const LINK_POST = 'post';

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->getField(self::FIELD_BODY);
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->getLink(self::FIELD_POST_ID, Post::class);
    }

    /**
     * @param string $value
     */
    public function setBody($value)
    {
        $this->setField(self::FIELD_BODY, $value);
    }

    /**
     * @param Post $value
     */
    public function setPost(Post $value)
    {
        $this->setLink(self::FIELD_POST_ID, $value);
    }
}
