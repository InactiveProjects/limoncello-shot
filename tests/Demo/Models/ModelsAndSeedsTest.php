<?php namespace DemoTests\Models;

use \App\Models\PDO\Post;
use \App\Models\PDO\Site;
use \App\Models\PDO\Author;
use \App\Models\PDO\Comment;
use \DemoTests\BaseTestCase;

/**
 *
 */
class ModelsAndSeedsTest extends BaseTestCase
{
    /**
     * Test samples for all models have been seeded.
     */
    public function testModelSeed()
    {
        $message = 'Haven\'t you forgotten to run artisan migrate and db::seed?';

        $this->assertNotEmpty(Author::search(), $message);
        $this->assertNotEmpty(Comment::search(), $message);
        $this->assertNotEmpty(Post::search(), $message);
        $this->assertNotEmpty(Site::search(), $message);
    }

    /**
     * Check models have proper relations with each other.
     */
    public function testModelRelations()
    {
        /** @var Site $site */
        $this->assertNotNull($site = Site::findOrFail(1));

        $this->assertNotEmpty($posts = $site->getPosts());
        /** @var Post $post */
        $post = null;
        foreach ($posts as $curPost) {
            /** @var Post $curPost */
            $this->assertNotNull($curPost->getSite());
            if ($curPost->getSite()->getId() === $site->getId()) {
                $post = $curPost;
                break;
            }
        }
        $this->assertNotNull($post);
        $this->assertNotNull($post->getSite());
        $this->assertEquals($site->getId(), $post->getSite()->getId());

        $this->assertNotNull($author = $post->getAuthor());
        $this->assertNotEmpty($posts = $author->getPosts());
        foreach ($posts as $curPost) {
            /** @var Post $curPost */
            $this->assertNotNull($curPost->getAuthor());
        }

        $this->assertNotEmpty($comments = $post->getComments());
        foreach ($comments as $curComment) {
            /** @var Comment $curComment */
            $this->assertNotNull($curComment);
            $this->assertEquals($post->getId(), $curComment->getPost()->getId());
        }
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testUpdateNonExistingModel()
    {
        /** @var Author $author */
        $author = Author::existingInstance(999999);
        $author->setFirstName('First');
        $author->setLastName('Last');
        $author->setTwitter('@twitter');
        $author->save();
    }

    /**
     * @expectedException \PDOException
     */
    public function testSaveInvalidModel()
    {
        /** @var Author $author */
        $author = Author::newInstance();
        $author->save();
    }

    /**
     * Test update loaded model.
     */
    public function testUpdateLoadedModel()
    {
        /** @var Author $author */
        $author = Author::newInstance();
        $author->setFirstName('first');
        $author->setLastName('last');
        $author->setTwitter('twitter');
        $author->save();

        // re-read
        $author = Author::existingInstance($author->getId());
        $this->assertEquals('first', $author->getFirstName());
        $this->assertNotEmpty($author->getFirstName());
        $author->setFirstName('new name');
        $author->save();

        // re-read and check
        $author = Author::existingInstance($author->getId());
        $this->assertEquals('new name', $author->getFirstName());
    }

    /**
     * @expectedException \LogicException
     */
    public function testReadingExistingNotSavedModel()
    {
        /** @var Author $author */
        $author = Author::existingInstance(1);
        $author->setFirstName('first');

        /** @var Author $anotherAuthor */
        $anotherAuthor = Author::newInstance();
        $anotherAuthor->setFirstName('first');

        // this one works fine
        $this->assertEquals('first', $anotherAuthor->getFirstName());

        // This model has changes but no loaded data in it.
        // Before reading is should be saved (and loaded with data from db).
        $author->getFirstName(); // -> LogicException
    }
}
