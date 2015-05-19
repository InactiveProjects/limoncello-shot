<?php

use \Illuminate\Database\Seeder;
use \Illuminate\Database\Eloquent\Model;

/**
 * @package Neomerx\Tests\JsonApi
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SitesTableSeeder::class);
        $this->call(AuthorsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
    }
}
