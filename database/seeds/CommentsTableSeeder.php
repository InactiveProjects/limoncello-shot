<?php

use \App\Models\PDO\Post;
use \App\Models\PDO\Comment;
use \Illuminate\Database\Seeder;

/**
 * Comments seeder
 */
class CommentsTableSeeder extends Seeder
{
    /**
     * Seeds the table.
     *
     * @return void
     */
    public function run()
    {
        /** @var Post $post */
        $post = Post::findOrFail(1);

        $comment = Comment::newInstance();
        $comment->setBody('First!');
        $comment->setPost($post);
        $comment->save();

        $comment = Comment::newInstance();
        $comment->setBody('I like XML better');
        $comment->setPost($post);
        $comment->save();
    }
}
