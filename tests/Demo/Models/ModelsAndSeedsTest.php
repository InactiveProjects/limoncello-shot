<?php namespace DemoTests\Models;

use \Auth;
use \App\Models\Post;
use \App\Models\Site;
use \UsersTableSeeder;
use \App\Models\Author;
use \App\Models\Comment;
use \DemoTests\BaseTestCase;

class ModelsAndSeedsTest extends BaseTestCase
{
    /**
     * Test samples for all models have been seeded.
     */
    public function testModelSeed()
    {
        $message = 'Haven\'t you forgotten to run artisan migrate and db::seed?';

        $this->assertNotEmpty(Author::all(), $message);
        $this->assertNotEmpty(Comment::all(), $message);
        $this->assertNotEmpty(Post::all(), $message);
        $this->assertNotEmpty(Site::all(), $message);

        $isAuthenticated = Auth::attempt([
            'email'    => UsersTableSeeder::SAMPLE_LOGIN,
            'password' => UsersTableSeeder::SAMPLE_PASSWORD,
        ]);
        $this->assertTrue($isAuthenticated);
    }

    /**
     * Check models have proper relations with each other.
     */
    public function testModelRelations()
    {
        /** @var Site $site */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertNotNull($site = Site::firstOrFail());

        $this->assertNotEmpty($site->posts);
        /** @var Post $post */
        $post = null;
        foreach ($site->posts as $curPost) {
            /** @var Post $curPost */
            $this->assertNotNull($curPost->site);
            if ($curPost->site_id === $site->id) {
                $post = $curPost;
                break;
            }
        }
        $this->assertNotNull($post);
        $this->assertNotNull($post->site);
        $this->assertEquals($site->id, $post->site_id);

        /** @var Author $author */
        $this->assertNotNull($author = $post->author);
        $this->assertNotEmpty($author->posts);
        foreach ($author->posts as $curPost) {
            /** @var Post $curPost */
            $this->assertNotNull($curPost->author);
        }

        $this->assertNotEmpty($post->comments);
        foreach ($post->comments as $curComment) {
            /** @var Comment $curComment */
            $this->assertNotNull($curComment);
            $this->assertEquals($post->id, $curComment->post_id);
        }
    }
}
