<?php

use \App\Models\Post;
use \App\Models\Site;
use \App\Models\Author;
use \Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
	/**
	 * Seeds the table.
	 *
	 * @return void
	 */
	public function run()
	{
        $post = new Post();
        $post->title = 'JSON API paints my bikeshed!';
        $post->body  = 'If you\'ve ever argued with your team about the way your JSON responses should be '.
            'formatted, JSON API is your anti-bikeshedding weapon.';

        /** @var Site $site */
        $site   = Site::firstOrFail();
        /** @var Author $author */
        $author = Author::firstOrFail();

        $post->site_id   = $site->id;
        $post->author_id = $author->id;

        $post->save();
	}
}
