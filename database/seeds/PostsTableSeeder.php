<?php

use \App\Models\Post;
use \App\Models\Site;
use \App\Models\Author;
use \Illuminate\Database\Seeder;

/**
 * Posts seeder
 */
class PostsTableSeeder extends Seeder
{
    /**
     * Seeds the table.
     *
     * @return void
     */
    public function run()
    {
        $post = Post::newInstance();
        $post->setTitle('JSON API paints my bikeshed!');
        $post->setBody('If you\'ve ever argued with your team about the way your JSON responses should be '.
            'formatted, JSON API is your anti-bikeshedding weapon.');

        /** @var Site $site */
        $site   = Site::findOrFail(1);
        /** @var Author $author */
        $author = Author::findOrFail(1);

        $post->setSite($site);
        $post->setAuthor($author);

        $post->save();
    }
}
