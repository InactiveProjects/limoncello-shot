<?php

use \App\Models\Author;
use \Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{
	/**
	 * Seeds the table.
	 *
	 * @return void
	 */
	public function run()
	{
        $author = new Author();
        $author->first_name = 'Dan';
        $author->last_name  = 'Gebhardt';
        $author->twitter    = 'dgeb';
        $author->save();
	}
}
