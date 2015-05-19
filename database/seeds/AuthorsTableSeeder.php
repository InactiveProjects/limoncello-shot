<?php

use \App\Models\Author;
use \Illuminate\Database\Seeder;

/**
 * Authors seeder
 */
class AuthorsTableSeeder extends Seeder
{
    /**
     * Seeds the table.
     *
     * @return void
     */
    public function run()
    {
        $author = Author::newInstance();
        $author->setFirstName('Dan');
        $author->setLastName('Gebhardt');
        $author->setTwitter('dgeb');
        $author->save();
    }
}
