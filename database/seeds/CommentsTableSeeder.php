<?php

use \App\Models\Post;
use \App\Models\Comment;
use \Illuminate\Database\Seeder;

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
        $post = Post::firstOrFail();

        $comment = new Comment();
        $comment->body   = 'First!';
        $comment->post_id = $post->id;
        $comment->save();

        $comment = new Comment();
        $comment->body   = 'I like XML better';
        $comment->post_id = $post->id;
        $comment->save();
	}
}
